<?php
/**
 * Paytrail for Woocommerce payment Card cancel controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

class CardCancel extends AbstractController {

	protected function checkout() {
		wc_add_notice(__('Could not add card details', 'paytrail-for-woocommerce'), 'error');
		wp_safe_redirect(wc_get_checkout_url());
		exit;
	}

	protected function my_account() {
		wc_add_notice(__('Could not add card details', 'paytrail-for-woocommerce'), 'error');
		wp_safe_redirect(wc_get_account_endpoint_url('payment-methods'));
		exit;
	}

	protected function change_payment_method() {
		wc_add_notice(__('Could not add card details', 'paytrail-for-woocommerce'), 'error');
		wp_safe_redirect(wc_get_account_endpoint_url('subscriptions'));
		exit;
	}
}
