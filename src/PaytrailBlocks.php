<?php
/**
 * Paytrail WooCommerce Blocks Integration
 */

namespace Paytrail\WooCommercePaymentGateway;

use Paytrail\WooCommercePaymentGateway\Model\PaymentTokenMigration;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use Paytrail\WooCommercePaymentGateway\Providers\OPLasku;
use WC_Payment_Tokens;
use WC_Logger;

class Paytrail_Blocks_Support extends AbstractPaymentMethodType {

	protected $name = 'paytrail';
	protected $gateway;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', [ $this, 'add_payment_request_order_meta' ], 8, 2 );
	}

	/**
	 * Initialize the payment method settings.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_paytrail_settings', [] );
	}

	/**
	 * Check if provider selection is enabled in settings.
	 *
	 * @return bool True if provider selection is enabled, false otherwise.
	 */
	public function use_provider_selection() {
		return isset( $this->settings['provider_selection'] ) && 'yes' === $this->settings['provider_selection'];
	}

	/**
	 * Determine if provider selection is enabled.
	 *
	 * @return bool
	 */
	public function is_provider_selection_enabled() {
		return $this->use_provider_selection();
	}

	/**
	 * Lazy initialize and retrieve the Paytrail gateway.
	 *
	 * @return WC_Payment_Gateway|null The Paytrail gateway or null if not found.
	 */
	private function get_gateway() {
		if ( $this->gateway ) {
			return $this->gateway;
		}

		// Initialize the gateway if it hasn't been set yet.
		$gateways      = WC()->payment_gateways->payment_gateways();
		$this->gateway = isset( $gateways[ $this->name ] ) ? $gateways[ $this->name ] : null;

		return $this->gateway;
	}

	/**
	 * Return the handles of the scripts required by the payment method.
	 *
	 * @return array Script handles.
	 */
	public function get_payment_method_script_handles() {
		$script_handles = [];

		$script_path       = '/dist/assets/frontend/blocks.js';
		$script_asset_path = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_abspath() . 'dist/assets/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path ) ? require $script_asset_path : [
			'dependencies' => [],
			'version'      => \Paytrail\WooCommercePaymentGateway\Plugin::$version,
		];
		$script_url        = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_url() . $script_path;

		wp_register_script(
			'paytrail-block-payment',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		$script_handles[] = 'paytrail-block-payment';

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'paytrail-block-payment', \Paytrail\WooCommercePaymentGateway\Plugin::plugin_abspath() . 'languages/' );
		}

		// Register OP Lasku scripts on cart page
		if (is_cart()) { 
			$settings = get_option('woocommerce_paytrail_settings');
			if (isset($settings['op_lasku_calculator']) && 'yes' === $settings['op_lasku_calculator']) {
				OPLasku::register_blocks_cart_scripts();
				$script_handles[] = 'paytrail-op-lasku-helper-blocks';
			}
		}

		return $script_handles;
	}

	/**
	 * Add payment request order meta, including handling tokenized cards.
	 *
	 * @param PaymentContext $context The payment context.
	 * @param PaymentResult  $result  The payment result.
	 * @return PaymentResult
	 */
	public function add_payment_request_order_meta( PaymentContext $context, PaymentResult &$result ) {
		if ( $context->payment_method !== $this->name ) {
			return;
		}
		
		$payment_data = $context->payment_data;
		$gateway      = $this->get_gateway();

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
			!empty($payment_data['payment_provider']) 
				? $payment_data['payment_provider'] 
				: $payment_data['payment_method'],
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
	 *
	 * @return array Style handles.
	 */
	public function get_payment_method_style_handles() {
		$style_handle        = 'paytrail-woocommerce-payment-fields';
		$blocks_style_handle = 'paytrail-woocommerce-blocks-style';
		$blocks_css_url      = plugins_url( 'dist/assets/frontend/blocks.css', dirname( __FILE__ ) );
		$blocks_css_file     = plugin_dir_path( dirname( __FILE__ ) ) . 'dist/assets/frontend/blocks.css';

		if ( ! wp_style_is( $style_handle, 'enqueued' ) ) {
			wp_enqueue_style( $style_handle );
		}

		if ( ! wp_style_is( $blocks_style_handle, 'enqueued' ) ) {
			wp_enqueue_style( $blocks_style_handle, $blocks_css_url, [], filemtime( $blocks_css_file ) );
		}

		return [ $style_handle, $blocks_style_handle ];
	}

	/**
	 * Check if the payment method is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->get_setting( 'enabled' ) === 'yes';
	}

	/**
	 * Retrieve payment method data for the block-based checkout.
	 *
	 * @param PaymentContext|null $context The payment context provided during checkout.
	 * @return array The data required to render the payment method in blocks.
	 */
	public function get_payment_method_data( $context = null ) {
		$gateway = $this->get_gateway();
		if ( ! $this->is_provider_selection_enabled() ) {
				return [
					'title'        => $gateway->title,
					'description'  => $gateway->description,
					'supports'     => array_filter( $gateway->supports, [ $gateway, 'supports' ] ),
					'groups'       => [],
					'terms'        => '',
					'no_providers' => true,
				];
		}

		$grouped_providers = $gateway->get_grouped_payment_providers();
		$this->get_payment_method_style_handles();
		$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id() );

		return [
			'title'                 => $gateway->title,
			'description'           => $gateway->description,
			'supports'              => array_filter( $gateway->supports, [ $gateway, 'supports' ] ),
			'groups' => isset($grouped_providers['groups']) ? $grouped_providers['groups'] : [],
			'apple_pay_active' => $gateway->apple_pay_active,
			'terms'  => isset($grouped_providers['terms']) ? $grouped_providers['terms'] : '',
			'saved_payment_methods' => ! empty( $tokens ) ? array_map(
				function ( $token ) {
					return [
						'id'     => $token->get_id(),
						'last4'  => $token->get_last4(),
						'expiry' => $token->get_expiry_month() . '/' . $token->get_expiry_year(),
						'type'   => $token->get_card_type(),
					];
				},
				$tokens
			) : [],
		];
	}
}
