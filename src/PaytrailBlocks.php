<?php
/**
 * Paytrail WooCommerce Blocks Integration
 */

namespace Paytrail\WooCommercePaymentGateway;

use Paytrail\WooCommercePaymentGateway\Model\PaymentTokenMigration;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use WC_Payment_Tokens;
use WC_Logger;

class Paytrail_Blocks_Support extends AbstractPaymentMethodType {

	protected $name = 'paytrail';
	protected $gateway;

	public function __construct() {
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', [ $this, 'add_payment_request_order_meta' ], 8, 2 );
	}

	public function initialize() {
		$this->settings = get_option( 'woocommerce_paytrail_settings', [] );
	}

	public function use_provider_selection() {
		return isset($this->settings['provider_selection']) && 'yes' === $this->settings['provider_selection'];
	}

	public function is_provider_selection_enabled() {
		return $this->use_provider_selection();
	}

	/**
	 * Lazy initialize and retrieve the Paytrail gateway.
	 */
	private function get_gateway() {
		if ( $this->gateway ) {
			return $this->gateway;
		}

		// Initialize the gateway if it hasn't been set yet.
		$gateways       = WC()->payment_gateways->payment_gateways();
		$this->gateway  = isset( $gateways[ $this->name ] ) ? $gateways[ $this->name ] : null;

		return $this->gateway;
	}

	/**
	 * Return the handles of the scripts required by the payment method.
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/dist/assets/frontend/blocks.js';
		$script_asset_path = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_abspath() . 'dist/assets/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path ) ? require $script_asset_path : array(
			'dependencies' => array(),
			'version'      => \Paytrail\WooCommercePaymentGateway\Plugin::$version,
		);
		$script_url        = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_url() . $script_path;

		wp_register_script(
			'paytrail-block-payment',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'paytrail-block-payment', \Paytrail\WooCommercePaymentGateway\Plugin::plugin_abspath() . 'languages/' );
		}

		return [ 'paytrail-block-payment' ];
	}

	/**
	 * Add payment request order meta, including handling tokenized cards.
	 */
	public function add_payment_request_order_meta( PaymentContext $context, PaymentResult &$result ) {
		$payment_data = $context->payment_data;
		$gateway      = $this->get_gateway(); // Use the gateway via the new get_gateway method.

		// Check if tokenized card is used.
		if ( ! empty( $payment_data['wc-paytrail-payment-token'] ) ) {
			$token_id = $payment_data['wc-paytrail-payment-token'];
			$token    = WC_Payment_Tokens::get( $token_id );

			if ( $token && $token->validate() ) {
				$payment_result = $gateway->process_paytrail_payment( $context->order, $token_id, null, false );

				if ( 'success' === $payment_result['result'] ) {
					$result->set_status( 'success' );
					$result->set_redirect_url( $payment_result['redirect'] );
				} else {
					$result->set_status( 'failure' );
					$result->set_payment_details(
						[
							'error_message' => __( 'Payment failed, please try again.', 'paytrail-for-woocommerce' ),
						]
					);
				}

				return $result;
			}
		}

		// Process payment normally if no tokenized card is used.
		$payment_result = $gateway->process_paytrail_payment(
			$context->order,
			null,
			$payment_data['payment_provider'],
			false
		);

		if ( 'success' === $payment_result['result'] ) {
			$result->set_status( 'success' );
			$result->set_redirect_url( $payment_result['redirect'] );
		} else {
			$result->set_status( 'failure' );
			$result->set_payment_details(
				[
					'error_message' => __( 'Payment failed, please try again.', 'paytrail-for-woocommerce' ),
				]
			);
		}

		return $result;
	}

	/**
	 * Return the handles of the styles required by the payment method.
	 */
	public function get_payment_method_style_handles() {
		$style_handle       = 'paytrail-woocommerce-payment-fields';
		$blocks_style_handle = 'paytrail-woocommerce-blocks-style';

		// Use dirname(__FILE__) to move out of the src directory, then use plugins_url to get the URL for blocks.css.
		$blocks_css_url  = plugins_url( 'dist/assets/frontend/blocks.css', dirname( __FILE__ ) );

		// Use plugin_dir_path to get the server file path for filemtime(), correcting the directory.
		$blocks_css_file = plugin_dir_path( dirname( __FILE__ ) ) . 'dist/assets/frontend/blocks.css';

		// Enqueue the original style if not already enqueued.
		if ( ! wp_style_is( $style_handle, 'enqueued' ) ) {
			wp_enqueue_style( $style_handle );
		}

		// Enqueue the blocks.css file.
		if ( ! wp_style_is( $blocks_style_handle, 'enqueued' ) ) {
			wp_enqueue_style( $blocks_style_handle, $blocks_css_url, [], filemtime( $blocks_css_file ) );
		}

		return [ $style_handle, $blocks_style_handle ];
	}

	/**
	 * Check if the payment method is active.
	 */
	public function is_active() {
		return $this->get_setting( 'enabled' ) === 'yes';
	}

	/**
	 * Return data required for the block-based checkout.
	 */
	public function get_payment_method_data() {
		$gateway = $this->get_gateway();
	
		// Check if provider selection is disabled
		if ( ! $this->is_provider_selection_enabled() ) {
			// Redirect to Paytrail if provider selection is disabled
			$payment_result = $gateway->process_paytrail_payment(null, null, null, false);
	
			if ( 'success' === $payment_result['result'] ) {
				return [
					'redirect_url' => $payment_result['redirect'],
				];
			}
	
			return [
				'error_message' => __( 'Unable to process payment. Please try again.', 'paytrail-for-woocommerce' ),
			];
		}
	
		// Provider selection is enabled, so proceed with showing grouped providers and saved tokens
		$grouped_providers = $gateway->get_grouped_payment_providers();
	
		// Enqueue payment method styles
		$this->get_payment_method_style_handles();
	
		// Get saved tokens for the current logged-in user
		$tokens = WC_Payment_Tokens::get_customer_tokens(get_current_user_id());
	
		// Return the grouped providers and saved payment methods for display in the checkout block
		return [
			'title'       => $gateway->title,
			'description' => $gateway->description,
			'supports'    => array_filter($gateway->supports, [$gateway, 'supports']),
			'groups'      => $grouped_providers['groups'],
			'terms'       => $grouped_providers['terms'],
			'saved_payment_methods' => array_map(function ($token) {
				return [
					'id'     => $token->get_id(),
					'last4'  => $token->get_last4(),
					'expiry' => $token->get_expiry_month() . '/' . $token->get_expiry_year(),
					'type'   => $token->get_card_type(),
				];
			}, $tokens),
		];
	}
	
}
