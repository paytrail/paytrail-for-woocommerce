<?php
/**
 * Paytrail for Woocommerce payment Callback controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\WooCommercePaymentGateway\Gateway;
use Paytrail\SDK\Response\GetTokenResponse;

class Callback extends AbstractController {

	protected function index() {
		new Gateway(['callbackMode' => true]);
	}
  
	protected function payAddCard() {
	  
		$gateway = new Gateway();
	  
		$gateway->log('Recieved Callback for pay and add card method', 'debug');
	  
		$reference = filter_input(INPUT_GET, 'checkout-reference');
	  
		$token = filter_input(INPUT_GET, 'checkout-card-token');
		
		//Extract Order ID from POST data		
		$order = wc_get_order($reference);
		   
		$allDetails = filter_input_array(INPUT_GET);
		
		if (isset($order)) {
		 
		   $user_id = $order->get_customer_id();
		  
		   $billing_email = $order->get_billing_email();
			  
		   $gateway->log('Start save token process for token :' . print_r($token, true), 'debug');
		  
		   $gateway->save_pay_and_add_card_method_token($allDetails, $user_id);

		   //Log the order details
		   $gateway->log('Token Saved successfully', 'debug');
		  
		} else {
			// Handle the case where the order ID is not present in the POST data
			$gateway->log('Order ID not present in the POST data', 'debug');
		}
	}
}
