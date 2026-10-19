<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * Finding the order a Paytrail callback belongs to. Paytrail identifies it by the
 * reference the payment was created with, not by the WooCommerce order id.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::handle_custom_searches
 */
class OrderLookupTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	public function test_an_order_is_found_by_its_checkout_reference(): void {
		$wanted = $this->markAsPaytrailOrder( $this->haveOrder(), 'paytrail-transaction-1', 'reference-wanted' );
		$this->markAsPaytrailOrder( $this->haveOrder(), 'paytrail-transaction-2', 'reference-other' );

		$found = wc_get_orders( [ 'checkout_reference' => 'reference-wanted' ] );

		$this->assertSame( [ $wanted->get_id() ], $this->idsOf( $found ) );
	}

	public function test_a_reference_no_order_carries_finds_nothing(): void {
		$this->markAsPaytrailOrder( $this->haveOrder(), 'paytrail-transaction-1', 'reference-wanted' );

		$this->assertSame( [], wc_get_orders( [ 'checkout_reference' => 'reference-missing' ] ) );
	}

	/** A refund callback names the refund, not the order, so it is looked up by that. */
	public function test_a_refund_is_found_by_the_id_its_callback_reports(): void {
		$order = $this->havePaidPaytrailOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->willRefund();
		$refund = $this->reload( $this->haveGatewayRefund( $order, 25.50 ) );

		$found = wc_get_orders(
			[
				'type'                      => 'shop_order_refund',
				'checkout_refund_unique_id' => $refund->get_meta( '_checkout_refund_unique_id' ),
			]
		);

		$this->assertSame( [ $refund->get_id() ], $this->idsOf( $found ) );
	}

	/** @return array<int, int> */
	private function idsOf( array $orders ): array {
		return array_map(
			static function ( $order ) {
				return $order->get_id();
			},
			$orders
		);
	}
}
