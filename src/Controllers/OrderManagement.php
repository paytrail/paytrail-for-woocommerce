<?php
/**
 * Paytrail for Woocommerce order management controller class
 *
 * @package Paytrail\WooCommercePaymentGateway\Controllers
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\SDK\Response\InvoiceActivationResponse;
use Paytrail\SDK\Response\InvoiceCancellationResponse;
use Paytrail\WooCommercePaymentGateway\Model;
use Paytrail\WooCommercePaymentGateway\Plugin;

/**
 * Class OrderManagement
 *
 * @package Paytrail\WooCommercePaymentGateway\Controllers
 */
class OrderManagement extends AbstractController {

	/**
	 * OrderManagement constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( $this, 'maybe_handle_manual_invoice_request' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'maybe_cancel_pending_klarna_invoice' ) );
	}

	/**
	 * Handles manual invoice activation for supported providers.
	 *
	 * @param int $order_id The WC order ID.
	 * @return void
	 */
	public function maybe_handle_manual_invoice_request( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		if ( Plugin::GATEWAY_ID !== $order->get_payment_method() ) {
			return;
		}

		$gateway        = Plugin::instance()->gateway();
		$transaction_id = $order->get_transaction_id();
		if ( empty( $transaction_id ) ) {
			$gateway->log(
				"Cannot activate manual invoice for order $order_id: missing transaction ID",
				'error'
			);
			return;
		}

		try {
			$model            = new Model\MetaBox( $order );
			$payment_status   = $model->get_payment_status();
			$payment_provider = $payment_status ? strtolower( $payment_status->getProvider() ) : '';

			if ( empty( $payment_status ) || 'pending' !== $payment_status->getStatus() || ( false === strpos( $payment_provider, 'walley' ) && false === strpos( $payment_provider, 'klarna' ) ) ) {
				return;
			}

			$client = $gateway->get_client();

			$response = $client->activateInvoice( $transaction_id );

			$gateway->log(
				InvoiceActivationResponse::class . " Successfully activated invoice for order $order_id with transaction id $transaction_id: " . wp_json_encode( $response )
			);
		} catch ( \Exception $e ) {
			$sanitized_message = sanitize_text_field( $e->getMessage() );
			$gateway->log(
				"Failed to send manual invoice for order $order_id with transaction id $transaction_id: $sanitized_message",
				'error'
			);
			$order->set_status(
				'on-hold',
				sprintf(
					// Translators: %s is the error message.
					__( 'Failed to activate manual invoice: %s', 'paytrail-for-woocommerce' ),
					$sanitized_message
				)
			);
			$order->save();
		}
	}

	/**
	 * Handles cancellation of pending Klarna invoices when an order is cancelled.
	 *
	 * @param int $order_id The WC order ID.
	 * @return void
	 */
	public function maybe_cancel_pending_klarna_invoice( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		if ( Plugin::GATEWAY_ID !== $order->get_payment_method() ) {
			return;
		}

		$gateway        = Plugin::instance()->gateway();
		$transaction_id = $order->get_transaction_id();
		if ( empty( $transaction_id ) ) {
			$gateway->log(
				"Cannot cancel pending Klarna invoice for order $order_id: missing transaction ID",
				'error'
			);
			return;
		}

		try {

			$model = new Model\MetaBox( $order );

			$payment_status = $model->get_payment_status();
			if ( empty( $payment_status ) || 'pending' !== $payment_status->getStatus() ) {
				return;
			}

			$payment_provider = $payment_status ? strtolower( $payment_status->getProvider() ) : '';
			if ( false === strpos( $payment_provider, 'klarna' ) ) {
				return;
			}

			$client = $gateway->get_client();

			$response = $client->cancelInvoice( $transaction_id );

			$gateway->log(
				InvoiceCancellationResponse::class . " Successfully cancelled invoice for order $order_id with transaction id $transaction_id: " . wp_json_encode( $response )
			);
			$order->add_order_note( __( 'Cancelled Klarna invoice.', 'paytrail-for-woocommerce' ) );
		} catch ( \Exception $e ) {
			$sanitized_message = sanitize_text_field( $e->getMessage() );
			$gateway->log(
				"Failed to cancel invoice for order $order_id with transaction id $transaction_id: $sanitized_message",
				'error'
			);
			$order->add_order_note(
				sprintf(
					// Translators: %s is the error message.
					__( 'Failed to cancel Klarna invoice: %s', 'paytrail-for-woocommerce' ),
					$sanitized_message
				)
			);
		}
	}
}
