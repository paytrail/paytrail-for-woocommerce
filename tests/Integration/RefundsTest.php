<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * Refunding a Paytrail order. Paytrail settles a refund asynchronously, so the plugin
 * records a pending refund and waits for the callback to confirm the amount.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_refund
 */
class RefundsTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** The refund body, including the callbacks Paytrail confirms the refund on. */
	public function test_the_refund_body(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$this->haveGatewayRefund( $order, 25.50, 'Damaged in transit' );

		$request = $this->apiRequestTo( '/refund' );

		$this->assertPaymentMatchesSnapshot(
			$request,
			'refund-fi',
			$order,
			[ '<refund-unique-id>' => $this->refundUniqueIdOf( $request ) ]
		);
	}

	/** The refund reaches Paytrail against the transaction the order was paid on. */
	public function test_the_refund_is_sent_against_the_orders_transaction(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$this->haveGatewayRefund( $order, 25.50 );

		$this->assertSame( '/payments/paytrail-transaction-1/refund', $this->apiRequestTo( '/refund' )['uri'] );
		$this->assertSame( 2550, $this->apiRequestTo( '/refund' )['json']['amount'] );
	}

	/** With no amount given, the whole order is refunded. */
	public function test_a_refund_without_an_amount_refunds_the_whole_order(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$this->gateway()->process_refund( $order->get_id(), null, '' );
		$this->haveRefundForItems( $order );

		$this->assertSame( 12550, $this->apiRequestTo( '/refund' )['json']['amount'] );
	}

	/** An order worth nothing has nothing to refund, so Paytrail is never asked. */
	public function test_an_order_with_nothing_to_refund_is_refused(): void {
		$order = $this->havePaidPaytrailOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'price' => '0.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$this->assertWpErrorCode( 400, $this->gateway()->process_refund( $order->get_id(), null, '' ) );
		$this->assertNoApiRequests();
	}

	/** The merchant is told the refund is under way, and why it was made. */
	public function test_the_order_records_that_a_refund_started(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$this->haveGatewayRefund( $order, 25.50, 'Damaged in transit' );

		$this->assertOrderHasNote( $order, 'Refunding process started. Reason: Damaged in transit' );
	}

	/**
	 * The refund shows as zero until Paytrail confirms it, so the order total is not
	 * reduced by an amount that may still fail to settle.
	 */
	public function test_a_pending_refund_is_recorded_as_zero(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$refund = $this->reload( $this->haveGatewayRefund( $order, 25.50, 'Damaged in transit' ) );

		$this->assertSame( '0', $refund->get_amount() );
		$this->assertSame( '25.5', $refund->get_meta( '_checkout_refund_amount' ) );
		$this->assertSame( '1', $refund->get_meta( '_checkout_refund_processing' ) );
		$this->assertStringContainsString( 'Refund is still being processed', $refund->get_reason() );
	}

	/** The unique id is what ties Paytrail's refund callback back to this refund. */
	public function test_the_refund_carries_the_id_its_callback_will_report(): void {
		$order = $this->haveRefundableOrder();

		$this->willRefund();
		$refund = $this->reload( $this->haveGatewayRefund( $order, 25.50 ) );

		$this->assertSame(
			$this->refundUniqueIdOf( $this->apiRequestTo( '/refund' ) ),
			$refund->get_meta( '_checkout_refund_unique_id' )
		);
	}

	/**
	 * A refund Paytrail refuses is rolled back rather than left on the order as one that
	 * happened, and the order is failed so a merchant looks at it.
	 */
	public function test_a_rejected_refund_is_rolled_back(): void {
		$order = $this->haveRefundableOrder();

		$this->willRejectRefund( 'refund window closed' );
		$this->haveGatewayRefund( $order, 25.50 );

		$this->assertSame( [], $this->reload( $order )->get_refunds() );
		$this->assertSame( 'failed', $this->statusOf( $order ) );
		$this->assertOrderHasNote( $order, 'Something went wrong with the refund and it was cancelled.' );
	}

	/** An unreachable API is a refund that did not happen, so it is rolled back too. */
	public function test_a_refund_that_never_reached_paytrail_is_rolled_back(): void {
		$order = $this->haveRefundableOrder();

		$this->haveGatewayRefund( $order, 25.50 );

		$this->assertSame( [], $this->reload( $order )->get_refunds() );
		$this->assertSame( 'failed', $this->statusOf( $order ) );
	}

	/** The refund id the plugin generated, read back off the request it sent. */
	private function refundUniqueIdOf( array $request ): string {
		parse_str(
			(string) wp_parse_url( $request['json']['callbackUrls']['success'], PHP_URL_QUERY ),
			$query
		);

		return (string) ( $query['refund_unique_id'] ?? '' );
	}

	/** A paid Paytrail order of 125.50, with a transaction to refund against. */
	private function haveRefundableOrder(): \WC_Order {
		return $this->havePaidPaytrailOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);
	}
}
