<?php
/**
 * Paytrail for Woocommerce payment Card controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\WooCommercePaymentGateway\Gateway;
use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\Exception;
use WC_Payment_Tokens;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;

class Card extends AbstractController {

	protected function add() {
		$gateway = new Gateway();
		try {
			$gateway->add_card_form();
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Delete card token.
	 *
	 * @return WP_Error|WP_HTTP_Response
	 * @throws Exception
	 */
	protected function delete() {
		$this->validate_request();

		$body = file_get_contents('php://input');
		$data = json_decode($body, true);

		if (!is_array($data)) {
			throw new Exception('Failed to decode JSON object');
		}

		// @var \WP_User $current_user
		$current_user = wp_get_current_user();

		$token_id = $data['token_id'];

		if (!$current_user->ID || !$token_id) {
			return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
		}

		$customer_tokens = WC_Payment_Tokens::get_customer_tokens($current_user->ID, Plugin::GATEWAY_ID);
		$customer_token_ids = array_keys($customer_tokens);

		if (!in_array($token_id, $customer_token_ids)) {
			return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
		}

		try {
			WC_Payment_Tokens::delete($token_id);
			wc_add_notice(__('Card was deleted successfully', 'paytrail-for-woocommerce'), 'success');
			wp_send_json_success($data);
			return new WP_HTTP_Response(['type' => 'success'], 200);
		} catch (\Exception $e) {
			wc_add_notice(__('Card could not be deleted', 'paytrail-for-woocommerce'), 'error');
			wp_send_json_error();
			return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
		}
	}

	private function validate_request() {
		if (empty($_SERVER['REQUEST_METHOD'])) {
			throw new Exception('No request method specified');
		}
		
		$request_method = filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING);
		if ('POST' !== strtoupper($request_method)) {
			throw new Exception('Only POST requests are allowed');
		}
		
		$content_type = filter_var(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', FILTER_SANITIZE_STRING);
		
		if (false === stripos($content_type, 'application/json')) {
			throw new Exception('Content-Type must be application/json');
		}
	}
	
	
}
