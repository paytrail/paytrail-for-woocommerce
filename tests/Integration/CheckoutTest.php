<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * What a purchase does with Paytrail's answer: where the shopper is sent, what the
 * order records, and what happens when the call does not come back.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_paytrail_payment
 */
class CheckoutTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/**
	 * With in-store provider selection the shopper goes to the order-pay page, which
	 * posts them on to the provider the receipt page renders a form for.
	 */
	public function test_selecting_a_provider_in_the_store_sends_the_shopper_to_the_order_pay_page(): void {
		$order = $this->havePurchasableOrder();

		$this->willCreatePayment();
		$result = $this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( 'success', $result['result'] );
		$this->assertSame( $order->get_checkout_payment_url( true ), $result['redirect'] );
	}

	/** The chosen provider is carried to the receipt page in the session. */
	public function test_the_chosen_provider_is_kept_for_the_receipt_page(): void {
		$order = $this->havePurchasableOrder();

		$this->willCreatePayment();
		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( self::DEFAULT_PROVIDER, WC()->session->get( 'payment_provider' )->getId() );
	}

	/** Without in-store selection Paytrail hosts the provider list, so the shopper goes there. */
	public function test_without_in_store_selection_the_shopper_goes_to_paytrail(): void {
		$this->haveGatewaySettings( [ 'provider_selection' => 'no' ] );

		$order = $this->havePurchasableOrder();

		$this->willCreatePayment( [ 'href' => 'https://pay.paytrail.com/pay/transaction-1' ] );
		$result = $this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( 'https://pay.paytrail.com/pay/transaction-1', $result['redirect'] );
		$this->assertNull( WC()->session->get( 'payment_provider' ) );
	}

	/**
	 * The note is what a merchant reads to tell which provider a transaction went to.
	 *
	 * @dataProvider provide_order_notes
	 */
	public function test_the_order_note_records_the_transaction( string $provider_selection, string $expected ): void {
		$this->haveGatewaySettings( [ 'provider_selection' => $provider_selection ] );

		$order = $this->havePurchasableOrder();

		$this->willCreatePayment();
		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertOrderHasNote( $order, $expected );
	}

	/** @return array<string, array{0: string, 1: string}> */
	public function provide_order_notes(): array {
		return [
			'selected in the store' => [ 'yes', 'Transaction paytrail-transaction-1 created with payment provider OP.' ],
			'selected at Paytrail'  => [ 'no', 'Transaction paytrail-transaction-1 created and user redirected to the payment provider selection page.' ],
		];
	}

	/** The provider is stored on the order, so a retry can reuse it. */
	public function test_the_chosen_provider_is_stored_on_the_order(): void {
		$order = $this->havePurchasableOrder();

		$this->willCreatePayment();
		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( self::DEFAULT_PROVIDER, $this->reload( $order )->get_meta( '_checkout_payment_provider' ) );
	}

	/** A free order has nothing to charge, so it completes without touching Paytrail. */
	public function test_a_free_order_is_completed_without_a_payment(): void {
		$order = $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'price' => '0.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);

		$result = $this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );

		$this->assertSame( 'success', $result['result'] );
		$this->assertNoApiRequests( 'A free order has nothing to charge.' );
		$this->assertNotEmpty( $this->reload( $order )->get_date_paid() );
	}

	/** Nothing to pay with, so the shopper is stopped before a payment is created. */
	public function test_a_purchase_without_a_provider_or_a_card_is_refused(): void {
		$order = $this->havePurchasableOrder();

		$this->expectExceptionMessage( 'The payment provider was not chosen.' );

		try {
			$this->gateway()->process_paytrail_payment( $order, null, '', false );
		} finally {
			$this->assertNoApiRequests();
		}
	}

	/**
	 * The shopper sees a generic message rather than whatever Paytrail said, so an API
	 * error cannot leak into the storefront.
	 */
	public function test_a_rejected_payment_shows_a_generic_message(): void {
		$order = $this->havePurchasableOrder();

		$this->willRejectWith( '/payments', 'merchant not found', 401 );

		// The message is stored escaped, so the apostrophe is not matched on.
		$this->expectExceptionMessage( 'process your payment. You have not been charged.' );

		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );
	}

	/** An unreachable API is the same story as a rejected one from the shopper's side. */
	public function test_an_unreachable_api_shows_a_generic_message(): void {
		$order = $this->havePurchasableOrder();

		$this->expectExceptionMessage( 'process your payment. You have not been charged.' );

		$this->gateway()->process_paytrail_payment( $order, null, self::DEFAULT_PROVIDER, false );
	}

	/** An order Paytrail can be asked to charge: one taxed product and an address. */
	private function havePurchasableOrder(): \WC_Order {
		return $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);
	}
}
