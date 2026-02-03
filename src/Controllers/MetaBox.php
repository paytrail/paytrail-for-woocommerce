<?php
/**
 * Paytrail for Woocommerce payment MetaBox controller class
 *
 * @package Paytrail\WooCommercePaymentGateway\Controllers
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\SDK\Response\InvoiceActivationResponse;
use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\View;
use Paytrail\WooCommercePaymentGateway\Model;

/**
 * Class MetaBox
 *
 * @package Paytrail\WooCommercePaymentGateway\Controllers
 */
class MetaBox extends AbstractController {

	/**
	 * MetaBox constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ), 10, 2 );

		add_action( 'woocommerce_order_status_completed', array( $this, 'maybe_handle_manual_invoice_request' ) );
	}

	/**
	 * Register meta box for order post type.
	 *
	 * @param string             $screen_id The screen ID (or post type).
	 * @param \WC_Order|\WP_Post $order The WC Order or WP Post.
	 * @return void
	 */
	public function register_meta_box( $screen_id, $order ) {
		if ( \in_array( $screen_id, array( 'woocommerce_page_wc-orders', 'shop_order' ), true ) ) {
			$order = $order instanceof \WP_Post ? wc_get_order( $order->ID ) : $order;
			if ( Plugin::GATEWAY_ID === $order->get_payment_method() ) {
				add_meta_box(
					'paytrail_meta_box',
					__( 'Paytrail', 'paytrail-for-woocommerce' ),
					function () use ( $order ) {
						$this->meta_box_content( $order );
					},
					$screen_id,
					'side',
					'core'
				);
			}
		}
	}

	/**
	 * Determines whether the success or error content should be printed.
	 *
	 * @param \WC_Order $order The WC order.
	 * @return void
	 */
	public function meta_box_content( $order ) {
		// Note: when this method is called, we've already registered the metabox. Thus, it will appear for the merchant. We should display something, even if it is something as simple as error message that explains why it is empty.

		if ( empty( $order->get_transaction_id() ) ) {
			$data = array( 'error' => __( 'The order is missing transaction ID.', 'paytrail-for-woocommerce' ) );
		} else {

			$model          = new Model\MetaBox( $order );
			$paytrail_order = $model->get_status();
			if ( empty( $paytrail_order ) ) {
				$data = array( 'error' => __( 'Failed to retrieve the order from Paytrail.', 'paytrail-for-woocommerce' ) );
			} else {
				$data = array(
					'status'         => $model->get_status(),
					'amount'         => $model->get_amount(),
					'currency'       => $model->get_currency(),
					'transaction_id' => $model->get_transaction_id(),
				);
			}
		}

		( new View( 'MetaBox' ) )->render( $data );
	}

	/**
	 * Handles manual invoice request if submitted.
	 *
	 * @param int $order_id The WC order ID.
	 * @return void
	 */
	public function maybe_handle_manual_invoice_request( $order_id ) {
		$order = wc_get_order( $order_id );
		try {
			if ( ! $order ) {
				return;
			}

			// If the WooCommerce order is not for Paytrail or it has already been paid.
			if ( Plugin::GATEWAY_ID !== $order->get_payment_method() ) {
				return;
			}

			// Only if the order is for a manual invoice order.
			$model            = new Model\MetaBox( $order );
			$payment_status   = $model->get_payment_status();
			$payment_provider = $payment_status ? strtolower( $payment_status->getProvider() ) : '';

			// Ensure the payment status was retrieved, is still pending, and the provider is one that supports manual invoices. Otherwise skip.
			if ( empty( $payment_status ) || 'pending' !== $payment_status->getStatus() || ( ! str_contains( $payment_provider, 'walley' ) && ! str_contains( $payment_provider, 'klarna' ) ) ) {
				return;
			}

			$gateway  = Plugin::instance()->gateway();
			$client   = $gateway->get_client();
			$response = $client->activateInvoice( $order->get_transaction_id() );
			Plugin::instance()->gateway()->log(  InvoiceActivationResponse::class . " Successfully activated invoice for order $order_id with transaction id {$order->get_transaction_id()}: " . json_encode( $response ) );
		} catch ( \Exception $e ) {
			$message = $e->getMessage();
			$gateway->log( "Failed to send manual invoice for order $order_id with transaction id {$order->get_transaction_id()}: $message", 'error' );
			$order->set_status( 'on-hold', __( 'Failed to activate manual invoice: ' . $message, 'paytrail-for-woocommerce' ) );
			$order->save();
			return;
		}
	}
}
