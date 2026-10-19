<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * The body the plugin sends to create a payment.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_paytrail_payment
 */
class PaymentRequestTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** A taxed Finnish order with a product, shipping and a fee. */
	public function test_the_payment_body_for_a_finnish_order(): void {
		$order = $this->haveOrder(
			[
				'items'    => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 2 ] ],
				'shipping' => [ 'total' => '5.90' ],
				'fees'     => [ [ 'name' => 'Handling fee', 'total' => '2.00' ] ],
				'billing'  => $this->finnishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$this->assertPaymentMatchesSnapshot( $this->apiRequestTo( '/payments' ), 'payment-fi', $order );
	}

	/** A store that does not charge tax sends every line at 0%. */
	public function test_the_payment_body_for_an_untaxed_order(): void {
		$this->configureStore( [ 'calc_taxes' => false ] );

		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$this->assertPaymentMatchesSnapshot( $this->apiRequestTo( '/payments' ), 'payment-fi-no-tax', $order );
	}

	/** A shipping address different from the billing one is sent alongside it. */
	public function test_a_separate_delivery_address_is_sent(): void {
		$order = $this->haveOrder(
			[
				'items'            => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing'          => $this->finnishAddress(),
				'shipping_address' => $this->swedishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$this->assertPaymentMatchesSnapshot( $this->apiRequestTo( '/payments' ), 'payment-separate-delivery', $order );
	}

	/**
	 * Paytrail rejects a payment whose items do not add up to its amount, which is what
	 * the plugin's rounding row is for. Per-unit prices are whole cents, so a discounted
	 * line total that its quantity does not divide evenly leaves a remainder.
	 *
	 * @dataProvider provide_uneven_totals
	 */
	public function test_the_items_always_add_up_to_the_amount( int $quantity, ?string $line_total ): void {
		$this->createPaymentFor( $this->haveDiscountedOrder( $quantity, $line_total ) );

		$this->assertItemsAddUp( $this->apiRequestTo( '/payments' )['json'] );
	}

	/** @return array<string, array{0: int, 1: string|null}> */
	public function provide_uneven_totals(): array {
		return [
			'an even line total'            => [ 3, null ],
			// 22.00 over three units is 733.33 cents, so the items fall a cent short.
			'the items fall short'          => [ 3, '22.00' ],
			// 20.00 over three units is 666.67 cents, so the items overshoot.
			'the items overshoot'           => [ 3, '20.00' ],
			'a single unit is never uneven' => [ 1, '19.99' ],
		];
	}

	/** The rounding row is what carries the difference when the items fall short. */
	public function test_a_shortfall_is_carried_by_a_rounding_row(): void {
		$this->createPaymentFor( $this->haveDiscountedOrder( 3, '22.00' ) );

		$rounding = $this->assertHasItem( $this->apiRequestTo( '/payments' )['json']['items'], 'rounding-row' );

		$this->assertSame( 1, $rounding['unitPrice'] );
		$this->assertSame( 1, $rounding['units'] );
	}

	/**
	 * An overshoot comes off the line itself, and the units it was over-charged on are
	 * handed back as a rounding row.
	 */
	public function test_an_overshoot_is_taken_off_the_line(): void {
		$this->createPaymentFor( $this->haveDiscountedOrder( 3, '20.00' ) );

		$items = $this->apiRequestTo( '/payments' )['json']['items'];

		$this->assertSame( 666, $this->assertHasItem( $items, 'sauna-bucket' )['unitPrice'] );
		$this->assertSame( 2, $this->assertHasItem( $items, 'rounding-row' )['unitPrice'] );
	}

	/**
	 * The product code is what a merchant reconciles a Paytrail line against.
	 *
	 * @dataProvider provide_product_codes
	 */
	public function test_the_product_code_of_a_line( string $kind, string $expected ): void {
		$product = 'no sku' === $kind
			? $this->haveProductWithoutSku( [ 'name' => 'Sauna bucket' ] )
			: $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket' ] );

		$order = $this->haveOrder(
			[
				'items'    => [ [ $product, 1 ] ],
				'shipping' => [ 'total' => '5.90' ],
				'fees'     => [ [ 'name' => 'Handling fee', 'total' => '2.00' ] ],
				'billing'  => $this->finnishAddress(),
			]
		);

		if ( 'no sku' === $kind ) {
			$expected = (string) $product->get_id();
		}

		$this->createPaymentFor( $order );

		$this->assertHasItem( $this->apiRequestTo( '/payments' )['json']['items'], $expected );
	}

	/** @return array<string, array{0: string, 1: string}> */
	public function provide_product_codes(): array {
		return [
			'a product with a SKU' => [ 'sku', 'sauna-bucket' ],
			'a product without'    => [ 'no sku', '' ],
			'shipping'             => [ 'shipping', 'shipping' ],
			'a fee'                => [ 'fee', 'fee' ],
		];
	}

	/** Paytrail caps an item description at 1000 characters. */
	public function test_a_long_product_name_is_truncated(): void {
		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => str_repeat( 'a', 1200 ), 'sku' => 'long-name' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$item = $this->assertHasItem( $this->apiRequestTo( '/payments' )['json']['items'], 'long-name' );

		$this->assertSame( 1000, mb_strlen( $item['description'] ) );
	}

	/** An order with no country at all falls back to the configured one. */
	public function test_a_missing_country_falls_back_to_the_setting(): void {
		$this->haveGatewaySettings( [ 'fallback_country' => 'FI' ] );

		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => $this->finnishAddress( [ 'country' => '' ] ),
			]
		);

		$this->createPaymentFor( $order );

		$this->assertSame( 'FI', $this->apiRequestTo( '/payments' )['json']['invoicingAddress']['country'] );
	}

	/** An order with no address at all sends none, rather than an empty one. */
	public function test_an_empty_address_is_left_out(): void {
		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => [ 'email' => 'matti@example.com' ],
			]
		);

		$this->createPaymentFor( $order );

		$body = $this->apiRequestTo( '/payments' )['json'];

		$this->assertArrayNotHasKey( 'invoicingAddress', $body );
		$this->assertArrayNotHasKey( 'deliveryAddress', $body );
	}

	/**
	 * With transaction-specific settlements the reference is a bank reference built
	 * from the order number, not the order number itself.
	 */
	public function test_transaction_settlements_send_a_bank_reference(): void {
		$this->haveGatewaySettings(
			[
				'settlement_enablement' => 'yes',
				'settlement_prefix'     => '10',
			]
		);

		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$reference = $this->apiRequestTo( '/payments' )['json']['reference'];

		$this->assertSame( '10' . $order->get_order_number(), substr( $reference, 0, -1 ) );
		$this->assertSame( strlen( (string) $order->get_order_number() ) + 3, strlen( $reference ) );
	}

	/** The reference the callback looks the order up by is stored on it. */
	public function test_the_reference_is_stored_on_the_order(): void {
		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->createPaymentFor( $order );

		$reference = $this->apiRequestTo( '/payments' )['json']['reference'];
		$reloaded  = $this->reload( $order );

		$this->assertSame( $reference, $reloaded->get_meta( '_checkout_reference' ) );
		$this->assertSame( '1', $reloaded->get_meta( '_checkout_reference_' . $reference ) );
	}

	/** An untaxed order of 10.00 units, optionally discounted to an uneven line total. */
	private function haveDiscountedOrder( int $quantity, ?string $line_total ): \WC_Order {
		$this->configureStore( [ 'calc_taxes' => false ] );

		$product = $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '10.00' ] );

		return $this->haveOrder(
			[
				'items'   => [ [ $product, $quantity, $line_total ] ],
				'billing' => $this->finnishAddress(),
			]
		);
	}

	/** Runs a purchase against a queued create-payment response. */
	private function createPaymentFor( \WC_Order $order, string $provider = self::DEFAULT_PROVIDER ): void {
		$this->willCreatePayment( [], $provider );

		$this->gateway()->process_paytrail_payment( $order, null, $provider, false );
	}
}
