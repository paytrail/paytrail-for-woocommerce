<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * Klarna Express Checkout orders, which Klarna hands to the plugin with a session token to pay through Paytrail.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_klarna_express_payment
 * @covers \Paytrail\WooCommercePaymentGateway\Controllers\OrderManagement
 */
class KlarnaExpressTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	private const SESSION_TOKEN = 'krn:network:eu1:test:session-token:abc';

	public function test_the_payment_body_for_a_klarna_express_order(): void {
		$order = $this->haveExpressOrder();

		$this->willPayWithKlarna();
		$this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$this->assertPaymentMatchesSnapshot( $this->apiRequestTo( '/payments/klarna/charge' ), 'klarna-express-fi', $order );
	}

	/**
	 * Manual invoice activation decides whether the order is charged now or held for
	 * capture when it is completed.
	 *
	 * @dataProvider provide_capture_settings
	 */
	public function test_a_klarna_express_order_is_paid_through_the_endpoint_the_settings_ask_for( string $manual_invoice_activation, string $endpoint, string $hold_flag ): void {
		$this->haveGatewaySettings( [ 'manual_invoice_activation' => $manual_invoice_activation ] );
		$order = $this->haveExpressOrder();

		$this->willPayWithKlarna( $endpoint );
		$redirect = $this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$reloaded = $this->reload( $order );

		$this->assertApiRequestCount( 1, $endpoint );
		$this->assertApiRequestCount( 1, '', 'A Klarna Express payment is a single call.' );
		$this->assertSame( $order->get_checkout_order_received_url(), $redirect );
		$this->assertSame( 'paytrail-klarna-1', $reloaded->get_transaction_id() );
		$this->assertNotEmpty( $reloaded->get_date_paid() );
		$this->assertSame( $hold_flag, $reloaded->get_meta( '_paytrail_klarna_authorization_hold' ) );
	}

	/** @return array<string, array{0: string, 1: string, 2: string}> */
	public function provide_capture_settings(): array {
		return [
			'charged right away'        => [ 'no', '/payments/klarna/charge', '' ],
			'manual invoice activation' => [ 'yes', '/payments/klarna/authorization-hold', 'yes' ],
		];
	}

	/** The transaction is kept while the shopper is away, so a cancelled order can still release the hold. */
	public function test_a_step_up_sends_the_shopper_to_klarna_unpaid(): void {
		$this->haveGatewaySettings( [ 'manual_invoice_activation' => 'yes' ] );
		$order = $this->haveExpressOrder();

		$this->willAskForKlarnaStepUp( 'https://pay.test.klarna.com/eu/requests/1/start' );
		$redirect = $this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$reloaded = $this->reload( $order );

		$this->assertSame( 'https://pay.test.klarna.com/eu/requests/1/start', $redirect );
		$this->assertEmpty( $reloaded->get_date_paid() );
		$this->assertSame( 'paytrail-klarna-1', $reloaded->get_transaction_id() );
		$this->assertOrderHasNote( $order, 'Requires step-up: yes' );
	}

	/**
	 * Klarna resends its notification after an error or a timeout, which must never pay
	 * for the order twice.
	 *
	 * @dataProvider provide_first_answers
	 */
	public function test_a_resent_notification_does_not_create_a_second_payment( string $first_answer ): void {
		$order = $this->haveExpressOrder();

		'step-up' === $first_answer ? $this->willAskForKlarnaStepUp() : $this->willPayWithKlarna();
		$first  = $this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );
		$second = $this->gateway()->process_klarna_express_payment( $this->reload( $order ), self::SESSION_TOKEN );

		$this->assertSame( $first, $second );
		$this->assertApiRequestCount( 1, '', 'The resent notification should reuse the first payment.' );
	}

	/** @return array<string, array{0: string}> */
	public function provide_first_answers(): array {
		return [
			'paid'                 => [ 'paid' ],
			'waiting on a step-up'     => [ 'step-up' ],
		];
	}

	/** The shopper is sent to the pay page, where they can try again. */
	public function test_a_rejected_payment_fails_the_order(): void {
		$order = $this->haveExpressOrder();

		$this->willRejectWith( '/payments/klarna/charge', 'Invalid session token', 400 );
		$redirect = $this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$this->assertSame( $order->get_checkout_payment_url(), $redirect );
		$this->assertSame( 'failed', $this->statusOf( $order ) );
	}

	/** Nothing was created after a rejection, so Klarna's resend may try again. */
	public function test_a_resent_notification_after_a_rejection_tries_again(): void {
		$order = $this->haveExpressOrder();

		$this->willRejectWith( '/payments/klarna/charge', 'Invalid session token', 400 );
		$this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$this->willPayWithKlarna();
		$this->gateway()->process_klarna_express_payment( $this->reload( $order ), self::SESSION_TOKEN );

		$this->assertApiRequestCount( 2, '/payments/klarna/charge' );
		$this->assertNotEmpty( $this->reload( $order )->get_date_paid() );
	}

	/** Without an answer the payment may exist at Paytrail, so it must never be sent twice. */
	public function test_an_unanswered_payment_is_never_sent_again(): void {
		$order = $this->haveExpressOrder();

		$this->willFailToConnect( '/payments/klarna/charge', 'Operation timed out' );
		$redirect = $this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );

		$this->assertSame( $order->get_checkout_order_received_url(), $redirect );
		$this->assertSame( 'on-hold', $this->statusOf( $order ) );
		$this->assertOrderHasNote( $order, 'may have been created with Paytrail' );

		$this->willPayWithKlarna();
		try {
			$this->gateway()->process_klarna_express_payment( $this->reload( $order ), self::SESSION_TOKEN );
			$this->fail( 'A resent notification should not be handled while the first payment is unresolved.' );
		} catch ( \Exception $exception ) {
			$this->assertStringContainsString( 'already in progress', $exception->getMessage() );
		}

		$this->assertApiRequestCount( 1, '/payments/klarna/charge' );
	}

	/** Completing the order captures the hold, which needs no request body. */
	public function test_completing_the_order_captures_the_hold(): void {
		$order = $this->haveHeldExpressOrder();

		$this->willCommitKlarnaHold();
		$this->reload( $order )->update_status( 'completed' );

		$this->assertNull( $this->apiRequestTo( '/payments/paytrail-klarna-1/klarna/commit' )['json'] );
		$this->assertApiRequestCount( 0, '/activate-invoice' );
		$this->assertOrderHasNote( $order, 'Captured Klarna authorization hold.' );
		$this->assertSame( 'completed', $this->statusOf( $order ) );
	}

	/** A failed capture is a merchant problem, so the order waits on hold. */
	public function test_a_failed_capture_puts_the_order_on_hold(): void {
		$order = $this->haveHeldExpressOrder();

		$this->willRejectWith( '/klarna/commit', 'Transaction is not in a capturable state', 400 );
		$this->reload( $order )->update_status( 'completed' );

		$this->assertSame( 'on-hold', $this->statusOf( $order ) );
		$this->assertOrderHasNote( $order, 'Failed to capture Klarna authorization hold: Transaction is not in a capturable state' );
	}

	/** Cancelling the order releases the hold, so the shopper is not charged. */
	public function test_cancelling_the_order_releases_the_hold(): void {
		$order = $this->haveHeldExpressOrder();

		$this->willRevertKlarnaHold();
		$this->reload( $order )->update_status( 'cancelled' );

		$this->assertApiRequestCount( 1, '/payments/paytrail-klarna-1/klarna/revert' );
		$this->assertApiRequestCount( 0, '/cancel-order' );
		$this->assertOrderHasNote( $order, 'Voided Klarna authorization hold.' );
	}

	private function haveExpressOrder(): \WC_Order {
		return $this->haveOrder(
			[
				'items'       => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing'     => $this->finnishAddress(),
				'created_via' => 'klarna_express_checkout',
			]
		);
	}

	/** An express order paid with an authorization hold, with the request that made it forgotten. */
	private function haveHeldExpressOrder(): \WC_Order {
		$this->haveGatewaySettings( [ 'manual_invoice_activation' => 'yes' ] );
		$order = $this->haveExpressOrder();

		$this->willPayWithKlarna( '/payments/klarna/authorization-hold' );
		$this->gateway()->process_klarna_express_payment( $order, self::SESSION_TOKEN );
		$this->resetApiInterception();

		return $order;
	}
}
