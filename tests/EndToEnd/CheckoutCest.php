<?php

namespace Tests\EndToEnd;

use Codeception\Example;
use Tests\Support\Data\{TestProducts, TestTaxRates};
use Tests\Support\EndToEndTester;

/**
 * A purchase through the shortcode checkout, the plugin's provider list and Paytrail's
 * hosted provider page.
 */
class CheckoutCest
{
	/**
	 * @dataProvider provide_purchases
	 */
	public function can_purchase(EndToEndTester $I, Example $case): void
	{
		$I->haveStoreOptionsInDatabase($case['store']);
		$I->haveTaxClassesInDatabase($case['tax_rates']);
		$I->haveCartWith($case['cart']);

		$I->amOnCheckoutPageWithGateway();
		$I->fillBillingAddressForm($case['billing']);
		$I->choosePaymentProvider($case['provider']);

		$I->placeOrder();
		$I->payAtProvider();
		$I->waitForThankYouPage();

		$I->verifyOrderOnThankYouPage($case['gateway'], $case['total'], $case['meta']);
	}

	/**
	 * A row carries only what differs from the defaults in scenario(). Paytrail rejects a
	 * payment whose amount does not match its items to the cent, so reaching the thank
	 * you page at all is half the assertion. Every total below came from WooCommerce.
	 *
	 * @return array<string, array{store: array<string, string>, tax_rates: list<string>, cart: list<string|array{0: string, 1: int}>, billing: array<string, ?string>, provider: string, gateway: string, total: string, meta: array<string, string>}>
	 */
	protected function provide_purchases(): array
	{
		return self::only(
			[
				'prices include VAT' => self::scenario(
					[
						'store' => [ 'woocommerce_prices_include_tax' => 'yes' ],
						'total' => '99.99',
					]
				),
				'VAT added at checkout' => self::scenario(
					[
						'store' => [ 'woocommerce_prices_include_tax' => 'no' ],
						'total' => '124.99',
					]
				),

				// Three lines whose VAT each lands on a half cent, so rounding them one by
				// one and rounding their sum differ by a cent in the order total.
				'rounding per line, VAT added at checkout' => self::scenario(
					[
						'store' => self::pricing( 'no', 'no' ),
						'cart'  => TestProducts::ROUNDING_25_CART,
						'total' => '74.97',
						'meta'  => [ '_order_tax' => '15' ],
					]
				),
				'rounding at subtotal, VAT added at checkout' => self::scenario(
					[
						'store' => self::pricing( 'no', 'yes' ),
						'cart'  => TestProducts::ROUNDING_25_CART,
						'total' => '74.96',
						// Unrounded on purpose: with rounding deferred to the subtotal,
						// WooCommerce stores the raw sum and only rounds on display.
						'meta'  => [ '_order_tax' => '14.9925' ],
					]
				),

				// The same three lines with tax already in the price. The total cannot
				// move, so the setting shows up in the tax alone.
				'rounding per line, prices include VAT' => self::scenario(
					[
						'store' => self::pricing( 'yes', 'no' ),
						'cart'  => TestProducts::ROUNDING_25_CART,
						'total' => '59.97',
						'meta'  => [ '_order_tax' => '12' ],
					]
				),
				'rounding at subtotal, prices include VAT' => self::scenario(
					[
						'store' => self::pricing( 'yes', 'yes' ),
						'cart'  => TestProducts::ROUNDING_25_CART,
						'total' => '59.97',
						'meta'  => [ '_order_tax' => '11.994' ],
					]
				),

				// One line of six, whose per-unit price cannot divide evenly, so the
				// plugin's rounding row is what makes the payment add up.
				'six of one product' => self::scenario(
					[
						'store' => self::pricing( 'no', 'no' ),
						'cart'  => [ [ TestProducts::ROUNDING_25_A, 6 ] ],
						'total' => '74.93',
					]
				),

				// Four rates in one cart, which is four VAT percentages for the plugin to
				// put on its items, including a zero-rated one.
				'four VAT rates in one cart' => self::scenario(
					[
						'store'     => self::pricing( 'no', 'no' ),
						'tax_rates' => [
							TestTaxRates::TAX_RATE_25,
							TestTaxRates::TAX_RATE_12,
							TestTaxRates::TAX_RATE_6,
							TestTaxRates::TAX_RATE_0,
						],
						'cart'      => TestProducts::ALL_RATES_CART,
						'total'     => '401.94',
						'meta'      => [ '_order_tax' => '49.08' ],
					]
				),

				// Virtual, downloadable, and downloadable-but-shippable in one cart:
				// three product shapes whose items the plugin builds differently.
				'virtual and downloadable products' => self::scenario(
					[
						'store' => self::pricing( 'no', 'no' ),
						'cart'  => TestProducts::VIRTUAL_AND_DOWNLOADABLE_CART,
						'total' => '523.59',
					]
				),
			]
		);
	}

	/**
	 * The rows to run, narrowed by PAYTRAIL_ONLY when it is set:
	 * `PAYTRAIL_ONLY=rounding composer test:e2e` runs the rounding rows, and a comma
	 * separated list matches more than one.
	 */
	private static function only( array $rows ): array
	{
		$only = (string) getenv( 'PAYTRAIL_ONLY' );
		if ( $only === '' ) {
			return $rows;
		}

		$needles = array_filter( array_map( 'trim', explode( ',', $only ) ) );

		$matching = array_filter(
			$rows,
			static function ( string $name ) use ( $needles ): bool {
				foreach ( $needles as $needle ) {
					if ( stripos( $name, $needle ) !== false ) {
						return true;
					}
				}

				return false;
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( $matching === [] ) {
			throw new \InvalidArgumentException(
				"PAYTRAIL_ONLY=\"{$only}\" matches none of: " . implode( ' | ', array_keys( $rows ) )
			);
		}

		return $matching;
	}

	/** The two WooCommerce settings that decide what a cart adds up to. */
	private static function pricing( string $pricesIncludeTax, string $roundAtSubtotal ): array
	{
		return [
			'woocommerce_prices_include_tax'    => $pricesIncludeTax,
			'woocommerce_tax_round_at_subtotal' => $roundAtSubtotal,
		];
	}

	/**
	 * One provider row: a Finnish customer buying a single 25% VAT product with a bank
	 * payment, unless the row says otherwise.
	 */
	private static function scenario(array $overrides): array
	{
		return array_replace(
			[
				// Arrange: the store, the tax rates and the cart, whose SKUs are
				// TestProducts entries, each optionally [ SKU, quantity ].
				'store'     => [],
				'tax_rates' => [ TestTaxRates::TAX_RATE_25 ],
				'cart'      => [ TestProducts::SIMPLE_25 ],
				'billing'   => [],
				'provider'  => EndToEndTester::DEFAULT_PROVIDER,
				// Assert: the finished order's _payment_method, _order_total and any
				// further meta.
				'gateway'   => 'paytrail',
				'total'     => null,
				'meta'      => [],
			],
			$overrides
		);
	}
}
