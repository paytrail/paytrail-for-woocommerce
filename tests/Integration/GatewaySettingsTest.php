<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\WooCommercePaymentGateway\Plugin;
use Tests\Support\IntegrationTestCase;

/**
 * How the settings a merchant saves change the gateway.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::__construct
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::admin_notices
 */
class GatewaySettingsTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/**
	 * Which merchant a request is signed as, which is the whole difference between a
	 * test store and a live one.
	 *
	 * @dataProvider provide_merchants
	 */
	public function test_the_request_is_signed_as_the_configured_merchant( string $profile, string $expected ): void {
		if ( 'live' === $profile ) {
			$this->haveLiveGatewaySettings();
		}

		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->willCreatePayment();
		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( $expected, $this->apiRequestTo( '/payments' )['headers']['checkout-account'] );
	}

	/** @return array<string, array{0: string, 1: string}> */
	public function provide_merchants(): array {
		return [
			// In test mode the plugin ignores the saved credentials and uses Paytrail's own.
			'test mode' => [ 'test', (string) Plugin::TEST_MERCHANT_ID ],
			'live mode' => [ 'live', '695874' ],
		];
	}

	/**
	 * Whether the checkout renders the provider list, which is what `has_fields`
	 * decides for WooCommerce.
	 *
	 * @dataProvider provide_provider_selection
	 */
	public function test_whether_the_checkout_renders_its_own_provider_list( string $setting, bool $expected ): void {
		$this->haveGatewaySettings( [ 'provider_selection' => $setting ] );

		$this->assertSame( $expected, $this->gateway()->has_fields );
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public function provide_provider_selection(): array {
		return [
			'selection in the store' => [ 'yes', true ],
			'selection at Paytrail'  => [ 'no', false ],
		];
	}

	/** The merchant's own wording wins over the plugin's default on the checkout. */
	public function test_the_checkout_wording_can_be_overridden(): void {
		$this->haveGatewaySettings(
			[
				'custom_provider_name'        => 'Maksa verkkopankilla',
				'custom_provider_description' => 'Valitse pankkisi',
			]
		);

		$gateway = $this->gateway();

		$this->assertSame( 'Maksa verkkopankilla', $gateway->title );
		$this->assertSame( 'Valitse pankkisi', $gateway->description );
	}

	/** With no override the checkout falls back to the plugin's own wording. */
	public function test_the_checkout_wording_falls_back_to_the_default(): void {
		$this->assertSame( 'Paytrail for WooCommerce', $this->gateway()->title );
	}

	/**
	 * Whether the gateway offers itself at checkout, which must not cost an API round
	 * trip: the checkout renders on every page load.
	 *
	 * @dataProvider provide_availability
	 */
	public function test_whether_the_gateway_offers_itself( string $enabled, bool $expected ): void {
		$this->haveGatewaySettings( [ 'enabled' => $enabled ] );

		$this->assertSame( $expected, $this->gateway()->is_available() );
		$this->assertNoApiRequests();
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public function provide_availability(): array {
		return [
			'enabled'  => [ 'yes', true ],
			'disabled' => [ 'no', false ],
		];
	}

	/** Order management and the metabox both match orders on this id. */
	public function test_the_gateway_is_registered_under_its_own_id(): void {
		$this->assertArrayHasKey( Plugin::GATEWAY_ID, WC()->payment_gateways()->payment_gateways() );
	}

	/**
	 * Paytrail settles in euro only, so a store on another currency is warned in
	 * wp-admin rather than left to have its payments rejected.
	 *
	 * @dataProvider provide_currencies
	 */
	public function test_an_unsupported_currency_is_flagged_in_wp_admin( string $currency, bool $expected ): void {
		$this->configureStore( [ 'currency' => $currency ] );

		$this->assertSame( $expected, false !== strpos( $this->renderAdminNotices(), 'Unsupported Currency Chosen' ) );
	}

	/** @return array<string, array{0: string, 1: bool}> */
	public function provide_currencies(): array {
		return [
			'euro'          => [ 'EUR', false ],
			'Swedish krona' => [ 'SEK', true ],
		];
	}

	/** Test mode is called out in wp-admin, so a live store cannot sit in it unnoticed. */
	public function test_test_mode_is_flagged_in_wp_admin(): void {
		$this->assertStringContainsString( 'test mode is enabled', $this->renderAdminNotices() );
	}

	private function renderAdminNotices(): string {
		ob_start();
		$this->gateway()->admin_notices();

		return (string) ob_get_clean();
	}
}
