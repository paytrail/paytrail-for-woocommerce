<?php
/**
 * Paytrail for Woocommerce payment Card success controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\SDK\Exception\HmacException;
use Paytrail\SDK\Exception\ValidationException;
use Paytrail\WooCommercePaymentGateway\Helper;
use Paytrail\WooCommercePaymentGateway\Plugin;

/**
 * Handles the redirect back from a successful card addition.
 */
class CardSuccess extends AbstractController {

	/**
	 * Store the card and return the customer to the checkout.
	 *
	 * @return void
	 */
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

	/**
	 * Store the card and return the customer to the payment methods page.
	 *
	 * @return void
	 */
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

	/**
	 * Store the card and return the customer to the subscriptions page.
	 *
	 * @return void
	 */
	protected function change_payment_method() {
		$gateway         = Plugin::instance()->gateway();
		$subscription_id = absint( Helper::getIsChangeSubscriptionPaymentMethod() );
		$nonce           = sanitize_text_field( (string) filter_input( INPUT_GET, '_paytrail_nonce' ) );

		if ( ! wp_verify_nonce( $nonce, 'paytrail_change_payment_method_' . $subscription_id ) ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
			wp_safe_redirect( wc_get_account_endpoint_url( 'subscriptions' ) );
			exit;
		}

		try {
			$token_id = $gateway->process_card_token();

			if ( $gateway->set_subscription_card( $subscription_id, $token_id ) ) {
				wc_add_notice( __( 'Card was added successfully', 'paytrail-for-woocommerce' ), 'success' );
			} else {
				wc_add_notice( __( 'The card was saved, but the subscription could not be updated to use it.', 'paytrail-for-woocommerce' ), 'error' );
			}
		} catch ( HmacException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		} catch ( ValidationException $e ) {
			wc_add_notice( __( 'Could not add card details', 'paytrail-for-woocommerce' ), 'error' );
		}
		wp_safe_redirect( wc_get_account_endpoint_url( 'subscriptions' ) );
		exit;
	}
}
