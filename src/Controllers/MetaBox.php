<?php
/**
 * Paytrail for Woocommerce payment MetaBox controller class
 *
 * @package Paytrail\WooCommercePaymentGateway\Controllers
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

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
}
