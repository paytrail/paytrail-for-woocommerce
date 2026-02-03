<?php
/**
 * Paytrail for Woocommerce payment Card success controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\SDK\Exception\HmacException;
use Paytrail\SDK\Exception\ValidationException;
use Paytrail\WooCommercePaymentGateway\Plugin;

class CardSuccess extends AbstractController {

	protected function checkout() {
		$gateway = Plugin::instance()->gateway();
		try {
			$gateway->process_card_token();
			wc_add_notice( __( 'Card was added successfully', 'paytrail-for-woocommerce' ), 'success' );
		} catch ( HmacException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		} catch ( ValidationException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		}
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	protected function my_account() {
		$gateway = Plugin::instance()->gateway();
		try {
			$gateway->process_card_token();
			wc_add_notice( __( 'Card was added successfully', 'paytrail-for-woocommerce' ), 'success' );
		} catch ( HmacException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		} catch ( ValidationException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		}
		wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
		exit;
	}

	protected function change_payment_method() {
		$gateway = Plugin::instance()->gateway();
		try {
			$gateway->process_card_token();
			wc_add_notice( __( 'Card was added successfully', 'paytrail-for-woocommerce' ), 'success' );
		} catch ( HmacException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		} catch ( ValidationException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		}
		wp_safe_redirect( wc_get_account_endpoint_url( 'subscriptions' ) );
		exit;
	}
}
