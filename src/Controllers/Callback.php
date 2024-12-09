<?php
/**
 * Paytrail for WooCommerce payment Callback controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\WooCommercePaymentGateway\Gateway;

class Callback extends AbstractController {

	protected function index() {
		new Gateway(['callbackMode' => true]);
	}
}
