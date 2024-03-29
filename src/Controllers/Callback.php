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
  
    protected function payAddCard(){
      
      $gateway = new Gateway();
      
      $gateway->log('Recieved Callback for pay and add card method', 'debug');
      
      	$reference = filter_input(INPUT_GET, 'checkout-reference');
        
        $decoded = filter_input(INPUT_GET, 'checkout-card-token');
      
        $gateway->log("Received Token with ID: $decoded", 'debug');

      
        // Step 2: Extract Order ID from POST data
        if (isset($reference)) {
          
            $order_id = $reference;

            // Step 3: Retrieve Order Details using WooCommerce API
            $order = wc_get_order($order_id);
          
            // Step 4: Process Order Details
            if ($order) {
              
                $user_id = $order->get_customer_id();
              
                $gateway->process_pay_add_card_token($decoded,$user_id);

                // Example: Log the order details
                $gateway->log("Received order with ID: $order_id, Total: $order_total, Email: $customer_email", 'debug');
            } else {
                // Handle the case where the order ID is not valid
                $gateway->log("Invalid order ID received: $order_id", 'debug');
            }
        } else {
            // Handle the case where the order ID is not present in the POST data
            $gateway->log("Order ID not present in the POST data", 'debug');
        }
    }
}
