<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\WooCommercePaymentGateway\Helper;
use Tests\Support\IntegrationTestCase;

/**
 * The shared conversions every request body is built out of.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Helper
 */
class HelperTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/**
	 * Paytrail takes amounts in cents, so a half cent decides whether the payment
	 * matches the order total or is rejected for not adding up.
	 *
	 * @dataProvider provide_amounts
	 *
	 * @param int|float $sum The WooCommerce total.
	 */
	public function test_an_amount_becomes_whole_cents( $sum, int $expected ): void {
		$this->assertSame( $expected, (int) ( new Helper() )->handle_currency( $sum ) );
	}

	/** @return array<string, array{0: int|float, 1: int}> */
	public function provide_amounts(): array {
		return [
			'a whole euro'        => [ 100, 10000 ],
			'two decimals'        => [ 12.34, 1234 ],
			'a third of a cent'   => [ 12.341, 1234 ],
			'rounds half up'      => [ 12.345, 1235 ],
			'rounds up'           => [ 12.346, 1235 ],
			'zero'                => [ 0, 0 ],
			'a negative discount' => [ -5.5, -550 ],
		];
	}

	/**
	 * Paytrail only accepts FI, SV and EN, and rejects the payment on anything else.
	 *
	 * @dataProvider provide_locales
	 */
	public function test_the_site_locale_maps_to_a_language_paytrail_accepts( string $locale, string $expected ): void {
		add_filter(
			'locale',
			static function () use ( $locale ) {
				return $locale;
			}
		);

		$this->assertSame( $expected, Helper::getLocale() );
	}

	/** @return array<string, array{0: string, 1: string}> */
	public function provide_locales(): array {
		return [
			'Finnish'            => [ 'fi', 'FI' ],
			'Finnish, regional'  => [ 'fi_FI', 'FI' ],
			'Swedish'            => [ 'sv_SE', 'SV' ],
			'US English'         => [ 'en_US', 'EN' ],
			'an unsupported one' => [ 'de_DE', 'EN' ],
		];
	}

	public function test_the_cart_total_is_read_in_cents(): void {
		$this->haveCartWith( [ [ $this->haveSimpleProduct( [ 'price' => '10.00' ] ), 3 ] ] );

		$this->assertSame( 3765, (int) ( new Helper() )->get_cart_total() );
	}

	/** The stamp has to be unique per item, or Paytrail rejects the payment. */
	public function test_an_item_stamp_is_unique_per_call(): void {
		$helper = new Helper();

		$this->assertNotSame( $helper->generate_item_stamp( 42 ), $helper->generate_item_stamp( 42 ) );
	}

	public function test_an_item_stamp_carries_the_order_id(): void {
		$this->assertStringStartsWith( '42-', ( new Helper() )->generate_item_stamp( 42 ) );
	}

	/**
	 * Whether the plugin treats the request as a subscription one, which decides both
	 * the tokenisation surface and the provider groups it asks Paytrail for.
	 *
	 * @dataProvider provide_subscription_states
	 */
	public function test_whether_subscriptions_are_in_play( array $cart_contains, bool $manual_renewals, bool $expected ): void {
		if ( $manual_renewals ) {
			update_option( \WC_Subscriptions_Admin::$option_prefix . '_accept_manual_renewals', 'yes' );
		}

		if ( [] !== $cart_contains ) {
			$this->haveCartContaining( ...$cart_contains );
		}

		$this->assertSame( $expected, Helper::getIsSubscriptionsEnabled() );
	}

	/** @return array<string, array{0: array<int, string>, 1: bool, 2: bool}> */
	public function provide_subscription_states(): array {
		return [
			'an ordinary cart'                 => [ [], false, false ],
			'a cart with a subscription'       => [ [ 'subscription' ], false, true ],
			'a renewal in the cart'            => [ [ 'renewal' ], false, true ],
			// Manual renewals mean the shopper pays each renewal themselves, so the
			// plugin has no reason to mint a token.
			'a subscription, manual renewals'  => [ [ 'subscription' ], true, false ],
		];
	}
}
