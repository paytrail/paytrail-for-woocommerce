<?php
/**
 * Paytrail for Woocommerce payment Callback controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\WooCommercePaymentGateway\Plugin;

class Callback extends AbstractController {

	/**
	 * Index method for the Callback controller
	 */
	protected function index() {
		Plugin::instance()->gateway()->set_callback_mode( true );
		Plugin::instance()->gateway()->check_paytrail_response(); // Trigger the response check to process any potential payment response.
	}
}
