<?php
/**
 * Paytrail for Woocommerce payment Card controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

use Paytrail\WooCommercePaymentGateway\Plugin;
use WC_Payment_Tokens;
use WP_Error;
use WP_HTTP_Response;

/**
 * Handles the card endpoints used for adding and deleting stored cards.
 */
class Card extends AbstractController {

	/**
	 * Render the add card form.
	 *
	 * @return void
	 */
	protected function add() {
		$gateway = Plugin::instance()->gateway();

		try {
			$gateway->add_card_form();
		} catch ( \Exception $e ) {
			return;
		}
	}

	/**
	 * Delete card token.
	 *
	 * @return WP_Error|WP_HTTP_Response
	 * @throws \Exception If the request body is not a decodable JSON object.
	 */
	protected function delete() {
		try {
			$this->validate_request();
		} catch ( \Exception $e ) {
			wc_add_notice( __( 'Card could not be deleted', 'paytrail-for-woocommerce' ), 'error' );
			wp_send_json_error();
			return new WP_Error( 'invalid-request', $e->getMessage(), array( 'status' => 400 ) );
		}

		$body = file_get_contents( 'php://input' );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			throw new \Exception( 'Failed to decode JSON object' );
		}

		$current_user = wp_get_current_user();

		$token_id = $data['token_id'];

		if ( ! $current_user->ID || ! $token_id ) {
			return new WP_Error( 'cant-delete', __( 'Card could not be deleted', 'paytrail-for-woocommerce' ), array( 'status' => 500 ) );
		}

		$customer_tokens    = WC_Payment_Tokens::get_customer_tokens( $current_user->ID, Plugin::GATEWAY_ID );
		$customer_token_ids = array_keys( $customer_tokens );

		if ( ! in_array( absint( $token_id ), array_map( 'absint', $customer_token_ids ), true ) ) {
			return new WP_Error( 'cant-delete', __( 'Card could not be deleted', 'paytrail-for-woocommerce' ), array( 'status' => 500 ) );
		}

		try {
			WC_Payment_Tokens::delete( $token_id );
			wc_add_notice( __( 'Card was deleted successfully', 'paytrail-for-woocommerce' ), 'success' );
			wp_send_json_success( $data );
			return new WP_HTTP_Response( array( 'type' => 'success' ), 200 );
		} catch ( \Exception $e ) {
			wc_add_notice( __( 'Card could not be deleted', 'paytrail-for-woocommerce' ), 'error' );
			wp_send_json_error();
			return new WP_Error( 'cant-delete', __( 'Card could not be deleted', 'paytrail-for-woocommerce' ), array( 'status' => 500 ) );
		}
	}

	/**
	 * Ensure the incoming request is a JSON POST.
	 *
	 * @return void
	 * @throws \Exception If the method or content type is not accepted.
	 */
	private function validate_request() {
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';

		if ( empty( $request_method ) ) {
			return;
		}

		if ( 'POST' !== $request_method ) {
			throw new \Exception( 'Only POST requests are allowed' );
		}

		$content_type = isset( $_SERVER['CONTENT_TYPE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['CONTENT_TYPE'] ) ) : '';

		if ( stripos( $content_type, 'application/json' ) === false ) {
			throw new \Exception( 'Content-Type must be application/json' );
		}
	}
}
