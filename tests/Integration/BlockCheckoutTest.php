<?php

declare(strict_types=1);

namespace Tests\Integration;

use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Paytrail\WooCommercePaymentGateway\Paytrail_Blocks_Support;
use Tests\Support\IntegrationTestCase;

/**
 * Paying with a saved card through the checkout block, which reaches the gateway from
 * the Store API rather than from the shortcode checkout's POST.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Paytrail_Blocks_Support::add_payment_request_order_meta
 */
class BlockCheckoutTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** The shopper's own card is charged and the block checkout told where to go next. */
	public function test_a_card_charge_completes_the_order(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->willChargeCard( 'paytrail-cit-1' );
		$result = $this->payWithBlockCheckout( $order, (string) $token->get_id() );

		$this->assertSame( 'success', $result->status );
		$this->assertSame( 'paytrail-cit-1', $this->reload( $order )->get_transaction_id() );
	}

	/**
	 * The Store API passes the token id through as the shopper sent it, so the block
	 * checkout has to refuse another shopper's card the same way the POST one does.
	 */
	public function test_a_card_the_shopper_does_not_own_is_refused(): void {
		$order    = $this->havePurchasableOrder();
		$token_id = $this->haveCardToken( [ 'user_id' => $this->haveCustomerAccount() ] )->get_id();

		$this->willChargeCard();

		$refusal = null;

		try {
			$this->payWithBlockCheckout( $order, (string) $token_id );
		} catch ( \Exception $exception ) {
			$refusal = $exception;
		}

		$this->assertNotNull( $refusal, 'A card the shopper does not own should stop the checkout.' );
		$this->assertStringContainsString( 'The chosen card is not available', $refusal->getMessage() );
		$this->assertNoApiRequests( 'A card the shopper does not own is never charged.' );
	}

	private function payWithBlockCheckout( \WC_Order $order, string $token_id ): PaymentResult {
		$context = new PaymentContext();
		$context->set_payment_method( 'paytrail' );
		$context->set_payment_data( [ 'wc-paytrail-payment-token' => $token_id ] );
		$context->set_order( $order );

		$result = new PaymentResult();

		$this->blockCheckout()->add_payment_request_order_meta( $context, $result );

		return $result;
	}

	/** The block checkout's payment handler, without the Store API hook its constructor adds. */
	private function blockCheckout(): Paytrail_Blocks_Support {
		$blocks = new Paytrail_Blocks_Support();

		remove_action(
			'woocommerce_rest_checkout_process_payment_with_context',
			[ $blocks, 'add_payment_request_order_meta' ],
			8
		);

		return $blocks;
	}

	/** A signed-in shopper with an order to pay, which is where a card charge starts. */
	private function havePurchasableOrder(): \WC_Order {
		$this->haveSignedInCustomer();

		return $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);
	}
}
