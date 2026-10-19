<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\Router;
use Tests\Support\IntegrationTestCase;

/**
 * The URLs Paytrail calls the store back on. Getting one wrong means a payment that
 * never reaches the order.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Router::get_url
 */
class RouterTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/**
	 * @dataProvider provide_routes
	 */
	public function test_the_url_of_a_route( string $route, string $action, string $expected ): void {
		$this->assertSame( home_url( $expected ), Router::get_url( $route, $action ) );
	}

	/** @return array<string, array{0: string, 1: string, 2: string}> */
	public function provide_routes(): array {
		return [
			'the payment callback'   => [ Plugin::CALLBACK_URL, 'index', '/paytrail/callback/index' ],
			'the add-card return'    => [ Plugin::ADD_CARD_REDIRECT_SUCCESS_URL, Plugin::ADD_CARD_CONTEXT_MY_ACCOUNT, '/paytrail/card-success/my_account' ],
			'the add-card cancel'    => [ Plugin::ADD_CARD_REDIRECT_CANCEL_URL, Plugin::ADD_CARD_CONTEXT_CHECKOUT, '/paytrail/card-cancel/checkout' ],
		];
	}

	/** A store on plain permalinks has no rewrite to match, so the route goes in the query. */
	public function test_a_store_without_pretty_permalinks_gets_a_query_string_url(): void {
		update_option( 'permalink_structure', '' );

		$this->assertSame(
			home_url( '/' ) . 'index.php?paytrail-route=callback&paytrail-action=index',
			Router::get_url( Plugin::CALLBACK_URL, 'index' )
		);
	}

	/** The callback URL a payment is created with is the one the router answers on. */
	public function test_a_payment_is_created_with_the_routers_callback_url(): void {
		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->willCreatePayment();
		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$callbacks = $this->apiRequestTo( '/payments' )['json']['callbackUrls'];
		$expected  = Router::get_url( Plugin::CALLBACK_URL, 'index' );

		$this->assertSame( $expected, $callbacks['success'] );
		$this->assertSame( $expected, $callbacks['cancel'] );
	}
}
