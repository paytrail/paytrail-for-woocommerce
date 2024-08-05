<?php
/**
 * Paytrail for Woocommerce payment gateway class
 */

namespace Paytrail\WooCommercePaymentGateway;

use Automattic\WooCommerce\Utilities\NumberUtil;
use Paytrail\SDK\Exception\ValidationException;
use Paytrail\SDK\Request\AddCardFormRequest;
use Paytrail\SDK\Request\CitPaymentRequest;
use Paytrail\SDK\Request\GetTokenRequest;
use Paytrail\SDK\Request\MitPaymentRequest;
use Paytrail\SDK\Request\PaymentRequest;
use Paytrail\SDK\Model\Customer;
use Paytrail\SDK\Model\Address;
use Paytrail\SDK\Model\Item;
use Paytrail\SDK\Model\CallbackUrl;
use Paytrail\SDK\Exception\HmacException;
use Paytrail\SDK\Request\RefundRequest;
use Paytrail\SDK\Client;
use Paytrail\SDK\Request\EmailRefundRequest;
use Paytrail\SDK\Model\Provider;
use Paytrail\SDK\Response\GetTokenResponse;
use Paytrail\SDK\Response\InvoiceActivationResponse;
use Paytrail\WooCommercePaymentGateway\Model\PaymentSubscriptionMigration;
use Paytrail\WooCommercePaymentGateway\Model\PaymentTokenMigration;
use WC_Order;
use WC_Order_Item;
use WC_Order_Item_Product;
use WC_Order_Item_Fee;
use WC_Order_Item_Shipping;
use WC_Payment_Token_CC;

/**
 * Class Gateway
 * The Gateway class
 *
 * @package Paytrail\WooCommercePaymentGateway
 */
final class Gateway extends \WC_Payment_Gateway {

	/**
	 * Paytrail merchant ID.
	 *
	 * @var int
	 */
	protected $merchant_id;

	/**
	 * Paytrail secret key.
	 *
	 * @var string
	 */
	protected $secret_key;

	/**
	 * Whether test mode is enabled.
	 *
	 * @var boolean
	 */
	public $enable_test_mode = false;

	/**
	 * Whether the debug mode is enabled.
	 *
	 * @var boolean
	 */
	public $debug = false;

	public $callbackMode = false;

	/**
	 * Supported features.
	 *
	 * @var array
	 */
	public $supports = [
		'products',
		'refunds',
		'tokenization',
		'subscriptions',
		'subscription_cancellation',
		'subscription_suspension',
		'subscription_reactivation',
		'subscription_amount_changes',
		'subscription_date_changes',
		'subscription_payment_method_change',
		'subscription_payment_method_change_customer',
		'subscription_payment_method_change_admin',
		'multiple_subscriptions'
	];

	/**
	 * Dynamic method info that will be populated from an endpoint.
	 *
	 * @var array
	 */
	public $method_info = [];

	/**
	 * WooCommerce logger instance
	 *
	 * @var \WC_Logger
	 */
	protected $logger = null;

	/**
	 * Paytrail SDK Client instance
	 *
	 * @var \Paytrail\SDK\Client
	 */
	protected $client = null;

	/**
	 * Helper instance
	 *
	 * @var Helper
	 */
	protected $helper = null;
	const TAX_RATE_PRECISION = 1;

	/**
	 * Object constructor
	 */
	public function __construct( $params = []) {
		// Set payment gateway ID
		$this->id = Plugin::GATEWAY_ID;

		$this->has_fields = $this->use_provider_selection();

		// Get dynamic payment method info.
		$this->method_info = $this->get_method_info();

		// These strings will show in the backend.
		$this->method_title       = $this->method_info['title'];
		$this->method_description = $this->method_info['description'];

		// These strings may show in the frontend.
		$this->title       = !empty($this->get_option('custom_provider_name')) ? $this->get_option('custom_provider_name') : $this->method_info['title'];
		$this->description = !empty($this->get_option('custom_provider_description')) ? $this->get_option('custom_provider_description') : $this->method_info['description'];

		// Icon temporarily disabled for size issues
		// $this->icon = Plugin::ICON_URL;

		// Set gateway admin settings fields.
		$this->set_form_fields();

		// Initialize gateway settings.
		$this->init_settings();

		// Whether we are in test mode or not.
		$this->enable_test_mode = 'yes' === $this->get_option('enable_test_mode', 'no');

		// Set merchant ID and secret key either from the options or for test mode.
		if ($this->enable_test_mode) {
			$this->merchant_id = (int) Plugin::TEST_MERCHANT_ID;
			$this->secret_key  = Plugin::TEST_SECRET_KEY;
		} else {
			$this->merchant_id = (int) $this->get_option('merchant_id');
			$this->secret_key  = $this->get_option('secret_key');
		}

		$platformName = 'paytrail-for-woocommerce-' . \Paytrail\WooCommercePaymentGateway\Plugin::$version;

		// Create SDK client instance
		$this->client = new Client(
			$this->merchant_id,
			$this->secret_key,
			$platformName
		);

		// Create Helper instance
		$this->helper = new Helper();

		// Whether we are in debug mode or not.
		$this->debug = 'yes' === $this->get_option('debug', 'no');

		// Check if transaction settlement is enabled
		$this->transaction_settlement_enable = $this->get_option( 'settlement_enablement', 'no' ) === 'yes';

		if (!empty($params) && isset($params['callbackMode'])) {
			$this->callbackMode = true;
		}

		// Add actions and filters.
		$this->add_actions();

		// Register stylesheet for payment fields
		$this->register_styles();

		// Register scripts for payment fields
		$this->register_scripts();

		// Check if we are in response phase
		$this->check_paytrail_response();
	}

	/**
	 * Add all actions and filters.
	 *
	 * @return void
	 */
	protected function add_actions() {
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ]);
		add_action('woocommerce_scheduled_subscription_payment_' . Plugin::GATEWAY_ID, [ $this, 'scheduled_subscription_payment' ], 10, 2);
		add_action('woocommerce_receipt_' . $this->id, [ $this, 'receipt_page' ]);
		add_filter('woocommerce_admin_order_items_after_refunds', [ $this, 'refund_items' ], 10, 1);
		add_filter('woocommerce_order_data_store_cpt_get_orders_query', [ $this, 'handle_custom_searches' ], 10, 2);
		add_filter('woocommerce_payment_gateway_get_saved_payment_method_option_html', [ $this, 'get_token_payment_option_html' ], 10, 2);
		add_action('admin_footer', [$this, 'display_user_data_form']);
		add_action('admin_notices', [$this, 'admin_notices']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);

	}

	/**
	 * Display the user_data_form as an overlay
	 */
	public function display_user_data_form() {
		$merchant_id = $this->get_option('merchant_id');
		$test_mode_enabled = $this->get_option('enable_test_mode', 'no') === 'yes';
		$current_screen = get_current_screen();
		$is_paytrail_settings_page = (
			!$test_mode_enabled &&
			$current_screen && 'woocommerce_page_wc-settings' === $current_screen->id &&
			isset($_GET['tab']) && 'checkout' === $_GET['tab'] &&
			isset($_GET['section']) && 'paytrail' === $_GET['section']
		);

		// Check if merchant_id is already submitted
		if (!empty($merchant_id) || $this->get_option('enable_test_mode', 'no') === 'yes') {
			return;
		}

		if ($is_paytrail_settings_page) {
			$template_path = plugin_dir_path(__FILE__) . 'View/Intro-form.php';
			if (file_exists($template_path)) {
				include_once $template_path;
			}
		}
		$this->user_data_form(); // Output the user_data_form content
	}

	/**
	 * Function to display the form
	 */
	public function user_data_form() {
		$current_user = wp_get_current_user();
		$user_email = $current_user->user_email;
		$first_name = $current_user->first_name;
		$last_name = $current_user->last_name;

		if (class_exists('WooCommerce')) {
			$wc_billing_data = get_user_meta($current_user->ID, 'billing', true);
			$company_name = isset($wc_billing_data['company']) ? $wc_billing_data['company'] : '';
			$phone_number = isset($wc_billing_data['phone']) ? $wc_billing_data['phone'] : '';

			// Fetch shop's address using WC_Countries
			$wc_countries = WC()->countries;
			$shop_address = $wc_countries->get_base_address();
			$shop_city = $wc_countries->get_base_city();
			$shop_postcode = $wc_countries->get_base_postcode();
		} else {
			$company_name = '';
			$phone_number = '';
			$shop_address = '';
			$shop_city = '';
			$shop_postcode = '';
		}


		$site_url = esc_url(get_site_url());

		$template_path = plugin_dir_path(__FILE__) . 'View/User-data-form.php';
		if (file_exists($template_path)) {
			include_once $template_path;
		}
	}


	/**
	 * Returns the payment method description string.
	 *
	 * @return array
	 */
	protected function get_method_info() {
		$method_info = [
			'title' => __('Paytrail for WooCommerce', 'paytrail-for-woocommerce'),
			'description' => __('Paytrail for WooCommerce - the most comprehensive suite of payment methods in the market with a single contract', 'paytrail-for-woocommerce'),
			'save_card' => 1,
		];
		return $method_info;
	}

	/**
	 * Returns admin form fields.
	 *
	 * @return void
	 */
	protected function set_form_fields() {
		$test_mode_enabled = $this->get_option('enable_test_mode', 'no') === 'yes';
		$merchant_id_disabled = $this->get_option( 'enable_test_mode', 'no' ) === 'yes';
		$secret_key_disabled = $this->get_option( 'enable_test_mode', 'no' ) === 'yes';
		$enable_test_mode_disabled = !$test_mode_enabled && (
			!empty($this->get_option('merchant_id')) ||
			!empty($this->get_option('secret_key'))
		);
		$this->form_fields = [
			// Whether the payment gateway is enabled.
			'enabled'     => [
				'title'   => __('Payment gateway status', 'paytrail-for-woocommerce'),
				'type'    => 'checkbox',
				'label'   => __('Enable Paytrail for WooCommerce', 'paytrail-for-woocommerce'),
				'default' => 'yes',
			],
			// Credentials
			'credentials_title' => [
				'title' => __('Credentials', 'paytrail-for-woocommerce'),
				'type' => 'title',
			],
			// Paytrail credentials
			'merchant_id' => [
				'title'   => __('Paytrail Merchant ID', 'paytrail-for-woocommerce'),
				'type'    => 'text',
				'label'   => __('Merchant ID', 'paytrail-for-woocommerce'),
				'default' => '',
				'disabled' => $merchant_id_disabled, // Disable if enable_test_mode is checked
			],
			'secret_key'  => [
				'title'   => __('Paytrail Secret key', 'paytrail-for-woocommerce'),
				'type'    => 'password',
				'label'   => __('Secret key', 'paytrail-for-woocommerce'),
				'default' => '',
				'description' => __('Credentials can be found in <a target="_blank" href="https://merchant.paytrail.com/">test credentials</a>the merchant panel</a>.', 'paytrail-for-woocommerce'),
				'disabled' => $secret_key_disabled, // Disable if enable_test_mode is checked
			],
			// Whether test mode is enabled
			'enable_test_mode' => [
				'title'       => __('Test mode', 'paytrail-for-woocommerce'),
				'type'        => 'checkbox',
				'label'       => __('Enable test mode', 'paytrail-for-woocommerce'),
				'default'     => 'no',
				'description' => __('You can use test mode to simulate payments with Paytrail\'s <a target="_blank" href="https://docs.paytrail.com/#/?id=test-credentials">test credentials</a>. To enable test mode, please first clear the Credentials and save settings.', 'paytrail-for-woocommerce'),
				'disabled'    => $enable_test_mode_disabled, // Disable the checkbox if merchant_id or secret_key has a value
			],
			// General settings
			'general_settings_title' => [
				'title' => __('General settings', 'paytrail-for-woocommerce'),
				'type' => 'title',
			],
			// Whether to show the payment provider wall or choose the method in the store
			'provider_selection' => [
				'title'       => __('Payment provider selection', 'paytrail-for-woocommerce'),
				'type'        => 'checkbox',
				'label'       => __('Enable payment provider selection in the checkout page', 'paytrail-for-woocommerce'),
				'default'     => 'yes',
				'description' => __('Choose whether you want the payment provider selection to happen in the checkout page or in a separate page.', 'paytrail-for-woocommerce'),
			],
			// Alternative text + description to show on the Checkout page
			'custom_provider_name' => [
				'title'       => __('Payment provider title', 'paytrail-for-woocommerce'),
				'type'        => 'text',
				'label'       => __('Used on the Checkout page title', 'paytrail-for-woocommerce'),
				'default'     => 'Paytrail for WooCommerce',
				'description' => __('This title is displayed on the Checkout page before the payment provider images.', 'paytrail-for-woocommerce')
			],
			'custom_provider_description' => [
				'title'       => __('Payment provider description', 'paytrail-for-woocommerce'),
				'type'        => 'text',
				'label'       => __('Used on the Checkout page title', 'paytrail-for-woocommerce'),
				'default'     => 'Paytrail for WooCommerce',
				'description' => __('Depending on your theme, this description might be displayed on the Checkout page before the payment provider images.', 'paytrail-for-woocommerce')
			],
			// Advanced settings
			'advanced_settings_title' => [
				'title' => __('Advanced settings', 'paytrail-for-woocommerce'),
				'type' => 'title',
			],
			'settlement_enablement' => [
				'title' => __( 'Enable individual settlements', 'paytrail-for-woocommerce' ),
				'type' => 'checkbox',
				'default' => 'no',
				'description' => __( 'This setting is required only if you are using transaction-by-transaction settlements in Paytrail.', 'paytrail-for-woocommerce' ),
			],
			'settlement_prefix' => [
				'title' => __( 'Bank reference prefix', 'paytrail-for-woocommerce' ),
				'type' => 'text',
				'description' => __( 'Add Prefix', 'paytrail-for-woocommerce' ),
				'default' => '10'
			],
			'fallback_country'  => [
				'title'   => __('Default country', 'paytrail-for-woocommerce'),
				'type'    => 'select',
				'label'   => __('Fallback country', 'paytrail-for-woocommerce'),
				'default' => '',
				'description' => __('Select country to be used as fallback if no country specified in checkout.', 'paytrail-for-woocommerce'),
				'options' => array_merge(['' => 'Select country'], WC()->countries->get_countries())
			],
			// Whether debug mode is enabled
			'debug'       => [
				'title'       => __('Debug log', 'paytrail-for-woocommerce'),
				'type'        => 'checkbox',
				'label'       => __('Enable logging', 'paytrail-for-woocommerce'),
				'default'     => 'no',
				// translators: %s: URL
				'description' => sprintf(__('This enables logging all payment gateway events. The log will be written in %1$s. Recommended only for debugging purposes as this might save personal data. All logs can be viewed here: %2$s', 'paytrail-for-woocommerce'), '<code>' . \WC_Log_Handler_File::get_log_file_path(Plugin::GATEWAY_ID) . '</code>', '<a target="_blank" href="/wp-admin/admin.php?page=wc-status&tab=logs">Logs</a>'),
			],
			// Update tokens if enabled
			'tokenize'       => [
				'title'       => __('Update tokens', 'paytrail-for-woocommerce'),
				'type'        => 'checkbox',
				'label'       => __('Enable token update', 'paytrail-for-woocommerce'),
				'default'     => 'no',
				// translators: %s: URL
				'description' => __('Choose this to update card information (tokens) from the old Checkout Finland for WooCommerce -plugin. The update is done upon saving settings. This will also update tokens for the current WooCommerce Subscriptions orders, if that module is in use. </br> <b>CAUTION:</b> This action cannot be reverted.', 'paytrail-for-woocommerce'),
			],
		];
	}

	/**
	 * Save admin options.
	 *
	 * @return boolean
	 */
	public function process_admin_options() {
		$saved = parent::process_admin_options();

		// Clear logs if debugging was disabled.
		if ('yes' !== $this->get_option('debug', 'no')) {
			if (empty($this->logger)) {
				$this->logger = wc_get_logger();
			}

			$this->logger->clear(Plugin::GATEWAY_ID);
		}

		// Update tokens if checkbx was checked
		if ('yes' === $this->get_option('tokenize', 'yes')) {
			$token_migration = new PaymentTokenMigration();
			$token_migration->execute();
			$subscription_migration = new PaymentSubscriptionMigration();
			$subscription_migration->execute();
		}

		return $saved;
	}

	/**
	 * Display a notice when test mode is enabled.
	 */
	public function display_test_mode_notice() {
		// Check if test mode is enabled
		$test_mode_enabled = $this->get_option('enable_test_mode', 'no') === 'yes';

		// Check if the notice should be displayed
		if ($test_mode_enabled) {
			?>
			<div class="notice notice-warning">
				<p>
				<?php
					esc_html_e('Paytrail for WooCommerce test mode is enabled. Please disable it to insert your Merchant ID and secret key.', 'paytrail-for-woocommerce');
					echo ' ' . sprintf(
						/* translators: If you have not registered yet, you can do so on our website %s to get your credentials! */
						esc_html__('If you have not registered yet, you can do so on our website %s to get your credentials!', 'paytrail-for-woocommerce'),
						'<a href="https://www.paytrail.com/en/get-started" target="_blank">' . esc_html__('here', 'paytrail-for-woocommerce') . '</a>'
					);
				?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Callback to display the notice on the admin page.
	 */
	public function admin_notices() {
		$this->display_test_mode_notice();
	}

	/**
	 * Calculate bank reference
	 */
	private function calculate_reference( $base ) {
		$base = sprintf( '%s%s', $this->settlement_prefix, $base );
		$base = trim( str_replace( ' ', '', $base ) );
		$base = str_split( $base );
		$reversed_base = array_reverse( $base );

		$weights = array( 7, 3, 1, 7, 3, 1, 7, 3, 1, 7, 3, 1, 7, 3, 1, 7, 3, 1, 7 );

		$sum = 0;
		for ( $i = 0; $i < count( $reversed_base ); $i++ ) {
			$coefficient = array_shift( $weights );
			$sum += intval( $reversed_base[$i] ) * $coefficient;
		}

		$checksum = ( 0 == ( $sum % 10 ) ) ? 0 : ( 10 - ( $sum % 10 ) );

		$reference = sprintf( '%s%d', implode( '', $base ), $checksum );

		return $reference;
	}

	/**
	 * Receipt page to redirect user forward.
	 *
	 * @return void
	 */
	public function receipt_page() {
		$view = new View('CheckoutForm');

		$provider = WC()->session->get('payment_provider');

		$view->render($provider);
	}

	/**
	 * Renders SavedPaymentMethods view
	 *
	 * @return void
	 */
	public function render_saved_payment_methods() {
		$view = new View('SavedPaymentMethods');

		$view->render();
	}

	/**
	 * Add card form
	 *
	 * @param string $context
	 * @throws HmacException
	 * @throws ValidationException
	 */
	public function add_card_form( $context = Plugin::ADD_CARD_CONTEXT_CHECKOUT) {
		$datetime = new \DateTime();
		$checkout_nonce = sha1(uniqid(true));

		if (Plugin::ADD_CARD_CONTEXT_MY_ACCOUNT === $context) {
			$success_url = Router::get_url(Plugin::ADD_CARD_REDIRECT_SUCCESS_URL, Plugin::ADD_CARD_CONTEXT_MY_ACCOUNT);
			$cancel_url = Router::get_url(Plugin::ADD_CARD_REDIRECT_CANCEL_URL, Plugin::ADD_CARD_CONTEXT_MY_ACCOUNT);
		} elseif (Helper::getIsChangeSubscriptionPaymentMethod()) {
			$success_url = Router::get_url(
				Plugin::ADD_CARD_REDIRECT_SUCCESS_URL,
				Plugin::ADD_CARD_CONTEXT_CHANGE_PAYMENT_METHOD
			);
			$cancel_url = Router::get_url(
				Plugin::ADD_CARD_REDIRECT_CANCEL_URL,
				Plugin::ADD_CARD_CONTEXT_CHANGE_PAYMENT_METHOD
			);
		} else {
			$success_url = Router::get_url(Plugin::ADD_CARD_REDIRECT_SUCCESS_URL, Plugin::ADD_CARD_CONTEXT_CHECKOUT);
			$cancel_url = Router::get_url(Plugin::ADD_CARD_REDIRECT_CANCEL_URL, Plugin::ADD_CARD_CONTEXT_CHECKOUT);
		}
		$this->log('Paytrail: try to add new card', 'debug');

		$add_card_form_request = new AddCardFormRequest();
		$add_card_form_request->setCheckoutAccount($this->merchant_id);
		$add_card_form_request->setCheckoutAlgorithm('sha256');
		$add_card_form_request->setCheckoutMethod('POST');
		$add_card_form_request->setCheckoutTimestamp($datetime->format('Y-m-d\TH:i:s.u\Z'));
		$add_card_form_request->setCheckoutNonce($checkout_nonce);
		$add_card_form_request->setCheckoutRedirectSuccessUrl($success_url);
		$add_card_form_request->setCheckoutRedirectCancelUrl($cancel_url);
		$add_card_form_request->setLanguage(Helper::getLocale());

		// Create a addCardFormRequest via Paytrail SDK
		// @var \GuzzleHttp\Psr7\Response $response
		$response = $this->client->createAddCardFormRequest($add_card_form_request);

		if ($response->getHeader('Location')) {
			wp_redirect($response->getHeader('Location')[0]);
			exit;
		}
	}

	/**
	 * Process card token
	 *
	 * @return bool
	 * @throws HmacException
	 * @throws ValidationException
	 */
	public function process_card_token() {
		$getTokenRequest = new GetTokenRequest();
		$getTokenRequest->setCheckoutTokenizationId(filter_input(INPUT_GET, 'checkout-tokenization-id'));
		$this->log('Paytrail: process_card_token', 'debug');

		$response = $this->client->createGetTokenRequest($getTokenRequest);

		return (bool) $this->save_card_token($response);
	}

	/**
	 * Save card token
	 *
	 * @param GetTokenResponse $card_token
	 */
	private function save_card_token( GetTokenResponse $card_token) {
		$this->log('Paytrail: save_card_token', 'debug');

		$token = new WC_Payment_Token_CC();
		$token->set_card_type($card_token->getCard()->getType());
		$token->set_expiry_month($card_token->getCard()->getExpireMonth());
		$token->set_expiry_year($card_token->getCard()->getExpireYear());
		$token->set_last4($card_token->getCard()->getPartialPan());
		$token->set_token($card_token->getToken());
		$token->set_user_id(get_current_user_id());
		$token->set_gateway_id(Plugin::GATEWAY_ID);
		\WC_Payment_Tokens::set_users_default(get_current_user_id(), $token->get_id());

		return $token->save();
	}

	/**
	 * Add payment method
	 *
	 * @return array
	 * @throws HmacException
	 * @throws ValidationException
	 */
	public function add_payment_method() {
		$this->add_card_form(Plugin::ADD_CARD_CONTEXT_MY_ACCOUNT);

		return array(
			'result' => 'success',
			'redirect' => wc_get_endpoint_url('payment-methods'),
		);
	}

	/**
	 * Grab and display users saved card payment methods.
	 */
	public function saved_payment_methods() {
		$html = '<ul class="woocommerce-SavedPaymentMethods wc-saved-payment-methods" data-count="' . esc_attr(count($this->get_tokens())) . '">';
		foreach ($this->get_tokens() as $token) {
			$html .= $this->get_saved_payment_method_option_html($token);
		}

		$html .= '</ul>';

		$kses_arr = [
			'li' => ['class' => []],
			'label' => ['for' => []],
			'input' => [
				'id' => [],
				'type' => [],
				'name' => [],
				'value'=> [],
				'class' => []
			],
			'div' => ['class' => []],
			'ul' => ['class' => []]
		];

		/**
		 * Show payment methods
		 *
		 * @since 1.0
		 */
		echo wp_kses(apply_filters('wc_payment_gateway_form_saved_payment_methods_html', $html, $this), $kses_arr);
	}

	/**
	 * Get token payment option
	 *
	 * @param $html
	 * @param $token
	 * @return string
	 */
	public function get_token_payment_option_html( $html, $token) {
		if (Plugin::GATEWAY_ID !== $token->get_gateway_id()) {
			error_log('Not the expected gateway ID. Returning original HTML.');
			return $html;
		}
		$html = sprintf(
			'<li class="woocommerce-SavedPaymentMethods-token paytrail-for-woocommerce-tokenized-payment-method">
				<label for="wc-%1$s-payment-token-%2$s">
				<input id="wc-%1$s-payment-token-%2$s" type="radio" name="wc-%1$s-payment-token" value="%2$s" class="paytrail-for-woocommerce-tokenized-payment-method-input" %6$s />
				<div class="paytrail-for-woocommerce-tokenized-payment-method-title" title="%4$s">%5$s%3$s</div>
				</label>
			</li>',
			esc_attr($this->id),
			esc_attr($token->get_id()),
			$this->get_display_name($token),
			esc_html($token->get_display_name()),
			$this->get_card_image($token),
			checked($token->is_default(), true, false)
		);

		return $html;
	}

	/**
	 * Get card display info
	 *
	 * @param $token
	 * @return string
	 */
	private function get_display_name( $token) {
		$display = sprintf(
			/* translators: 1: last 4 digits 2: expiry month 3: expiry year */
			__('xxxx xxxx xxxx %1$s %2$s/%3$s', 'paytrail-for-woocommerce'),
			$token->get_last4(),
			'<span id="paytrail-for-woocommerce-tokenized-payment-method-card-expire-date">' . $token->get_expiry_month(),
			substr($token->get_expiry_year(), 2) . '</span>'
		);

		return $display;
	}

	/**
	 * Get card image
	 *
	 * @param $token
	 * @return string
	 */
	private function get_card_image( $token) {
		$token_card_type = strtolower($token->get_card_type());

		if ('amex' === $token_card_type) {
			$token_card_type = 'american-express';
		}

		$html = sprintf(
			'<img alt="' . $token->get_card_type() . '" src="' . Plugin::PAYMENT_METHOD_IMG_URL . '/%1$s.svg" class="paytrail-for-woocommerce-tokenized-payment-method-title-image" />',
			esc_html(preg_replace('/[[:space:]]+/', '-', $token_card_type))
		);

		return $html;
	}

	/**
	 * Thank you page
	 *
	 * @return void
	 */
	public function check_paytrail_response() {
		$status           = filter_input(INPUT_GET, 'checkout-status');
		$refund_callback  = filter_input(INPUT_GET, 'refund_callback');
		$refund_unique_id = filter_input(INPUT_GET, 'refund_unique_id');
		$order_id         = filter_input(INPUT_GET, 'order_id');
		$reference        = filter_input(INPUT_GET, 'checkout-reference');
		$cancel_order     = filter_input(INPUT_GET, 'cancel_order');
		$pay_for_order    = filter_input(INPUT_GET, 'pay_for_order');

		if (!$status && !$reference && !$refund_callback && !$refund_unique_id) {
			//no log to reduce number of log entries
			return;
		}

		if (!$reference && $status && !$refund_callback && !$refund_unique_id) {
			$this->log('Paytrail: check_paytrail_response, no reference found for status: ' . $status, 'debug');
			return;
		}

		if (!$status && $reference && !$refund_callback && !$refund_unique_id) {
			$this->log('Paytrail: check_paytrail_response, no status found for reference ' . $reference, 'debug');
			return;
		}

		if ($cancel_order) {
			//Do not attempt to process further. Woo Commerce will cancel the order
			$this->log('Paytrail: check_paytrail_response, cancel_order is true. Order will be cancelled. Reference: ' . $reference, 'debug');
			return;
		}

		if ($pay_for_order) {
			//Do not attempt to process further, the customer will be shown a page to choose payment methods
			wc_clear_notices();
			$message = __(
				'Payment failed or was cancelled. Please try again',
				'paytrail-for-woocommerce'
			);

			wc_add_notice( $message, 'notice');
			$this->log('Paytrail: check_paytrail_response, pay_for_order is true. Payment page will be shown. Reference: ' . $reference, 'debug');
			return;
		}

		$sleepTime = rand(0, 3);
		$sleepTimeCallback = rand(3, 6);

		if (true === $this->callbackMode) {
			$this->log('Paytrail: Callback check_paytrail_response for order ' . $reference, 'debug');
			$this->log('Paytrail: Wait for ' . $sleepTimeCallback . ' seconds until processing order ' . $reference, 'debug');
			sleep($sleepTimeCallback);
		} else {
			$this->log('Paytrail: Redirect check_paytrail_response for reference ' . $reference, 'debug');
			$this->log('Paytrail: Wait for ' . $sleepTime . ' seconds until processing reference ' . $reference, 'debug');
			sleep($sleepTime);
		}

		// Handle the response only if the status exists.
		if ($refund_callback) {
			$this->log('Paytrail: Start handle_refund_response for order_id ' . $order_id, 'debug');
			$this->handle_refund_response($refund_callback, $refund_unique_id, $order_id);

		} else {
			$this->log('Paytrail: Start handle_payment_response for reference ' . $reference, 'debug');
			$this->handle_payment_response($status);
		}
	}

	/**
	 * Handle payment response functionalities
	 *
	 * @param string $status The status of the response.
	 *
	 * @return void
	 */
	public function handle_payment_response( $status) {
		// Check the HMAC
		try {
			$this->client->validateHmac(filter_input_array(INPUT_GET), '', filter_input(INPUT_GET, 'signature'));
		} catch (HmacException $exception) {
			$this->signature_error($exception);
		}

		$reference = filter_input(INPUT_GET, 'checkout-reference');

		$orders = \wc_get_orders([ 'checkout_reference' => $reference ]);

		if (empty($orders)) {
			$this->log('Paytrail: handle_payment_response, orders collection empty for reference: ' . $reference, 'debug');
			return;
		}
		$order = $orders[0];

		// Store information that transaction-specific settlement was used
		if ( $this->transaction_settlement_enable ) {
			$order->update_meta_data( '_paytrail_ppa_transaction_settlement', true );
			$order->save();
		}

		switch ($status) {
			case 'ok':
				$this->log('Paytrail: handle_payment_response, case = ok for order ' . $order->get_id(), 'debug');
				if (!$this->validate_order_payment_processing($order)) {
					return;
				}
				$this->log('Paytrail: handle_payment_response payment_complete, order ' . $order->get_id() . ' needs processing ' . $order->needs_processing(), 'debug');

				$transaction_id = filter_input(INPUT_GET, 'checkout-transaction-id');

				// If this transaction has already been processed, don't process again
				if ($order->get_transaction_id() === $transaction_id) {
					$this->log('Paytrail: handle_payment_response, transaction id ' . $transaction_id . ' already processed for order ' . $order->get_id(), 'debug');
					return false;
				}

				// Save transient to avoid race condition between redirect and callback processing
				\set_transient('checkout_transaction_id_processing_' . $transaction_id, 'yes', 60);

				if (! $this->use_provider_selection()) {
					$this->log('Paytrail: handle_payment_response, use_provider_selection = false for order ' . $order->get_id(), 'debug');
					// Get the chosen payment provider and save it to the order
					$payment_provider = filter_input(INPUT_GET, 'checkout-provider');
					$payment_amount   = filter_input(INPUT_GET, 'checkout-amount');

					$order->update_meta_data('_checkout_payment_provider', $payment_provider);
					$order->save();
					$providers = $this->get_payment_providers($payment_amount);

					if (! empty($providers['error'])) {
						$provider_name = ucfirst($payment_provider);
					} else {
						// Get only the wanted payment provider object
						$wanted_provider = $this->get_wanted_provider($providers, $payment_provider);
						if (null !== $wanted_provider) {
							$provider_name = !empty($wanted_provider->getName()) ? $wanted_provider->getName() : ucfirst($wanted_provider->getId());
						} else {
							$provider_name = ucfirst($payment_provider);
						}
					}

					WC()->session->set('payment_provider', $wanted_provider);

					$message = sprintf(
						// translators: First parameter is transaction ID, the other is the name of the payment provider.
						__('Payment completed with transaction ID %1$s and payment provider %2$s.', 'paytrail-for-woocommerce'),
						$transaction_id,
						$provider_name
					);
					$this->log('Paytrail: handle_payment_response, use_provider_selection = false, add_order_note', 'debug');

					$order->add_order_note($message);
				} else {
					$order_note = sprintf(
						// Translators: The placeholder is a transaction ID.
						esc_html__(
							'Payment completed with transaction ID %s.',
							'paytrail-for-woocommerce'
						),
						$transaction_id
					);
					$this->log('Paytrail: handle_payment_response, use_provider_selection = true, add_order_note', 'debug');

					$order->add_order_note($order_note);
				}

				// Mark payment completed and store the transaction ID.
				$order->payment_complete($transaction_id);

				// Clear the cart.
				WC()->cart->empty_cart();

				// Delete transient
				\delete_transient('checkout_transaction_id_processing_' . $transaction_id);

				break;
			case 'pending':
				$this->log('Paytrail: handle_payment_response, case = pending', 'debug');
				if (!$this->validate_order_payment_process_status($order)) {
					break;
				}
				$order->update_status('on-hold');
				$order->add_order_note(__('Payment pending.', 'paytrail-for-woocommerce'));
				break;
			default:
				$this->log('Paytrail: handle_payment_response, case = failed', 'debug');
				if (!$this->validate_order_payment_process_status($order)) {
					break;
				}
				$order->update_status('failed');
				$order->add_order_note(__('Payment failed.', 'paytrail-for-woocommerce'));
				break;
		}
	}

	/**
	 * Validate payment processing
	 *
	 * @param WC_Order $order
	 * @param bool $retry Whether to try again after 15 seconds if order is being processed
	 * @return bool
	 */
	protected function validate_order_payment_processing( WC_Order $order, $retry = true) {
		$transaction_id = filter_input(INPUT_GET, 'checkout-transaction-id');

		if (!$transaction_id) {
			$this->log('Paytrail: validate_order_payment_processing, transaction id empty for order: ' . $order->get_id(), 'debug');
			return false;
		}

		$order_status = $order->get_status();

		if ('completed' === $order_status || 'processing' === $order_status) {
			$this->log('Paytrail: validate_order_payment_processing, order already processed ' . $order->get_id(), 'debug');
			// This order has already been processed.
			return false;
		}

		// If the transaction is currently being processed, wait for 15 seconds and check again
		if ('yes' === \get_transient('checkout_transaction_id_processing_' . $transaction_id)) {
			$this->log('Paytrail: validate_order_payment_processing, order is currently being processed ' . $order->get_id(), 'debug');

			if (true === $retry) {
				$this->log('Paytrail: validate_order_payment_processing, waiting for 15 seconds ' . $order->get_id(), 'debug');
				sleep(15);

				return $this->validate_order_payment_processing($order, false);
			}

			$this->log('Paytrail: validate_order_payment_processing, not trying again ' . $order->get_id(), 'debug');
			return false;
		}

		$this->log('Paytrail: validate_order_payment_processing, order is valid ' . $order->get_id(), 'debug');

		return true;
	}

	protected function validate_order_payment_process_status( WC_Order $order) {
		$order_status = $order->get_status();

		if ('completed' === $order_status || 'processing' === $order_status) {
			// This order has already been processed.
			return false;
		}
		return true;
	}

	/**
	 * Whether we want to use in-store provider selection or not.
	 *
	 * @return boolean
	 */
	protected function use_provider_selection() {
		return 'yes' === $this->get_option('provider_selection', 'yes');
	}

	/**
	 * Handle refund response functionalities
	 *
	 * @param string $refund_callback  Refund callback status.
	 * @param string $refund_unique_id Unique ID for the refund.
	 * @param string $order_id         Order ID.
	 * @return void
	 */
	public function handle_refund_response( $refund_callback, $refund_unique_id, $order_id) {
		// Remove the callback indicators from the GET array
		$get = filter_input_array(INPUT_GET);

		unset($get['refund_callback']);
		unset($get['refund_unique_id']);
		unset($get['order_id']);

		// Check the HMAC
		try {
			$this->client->validateHmac($get, '', filter_input(INPUT_GET, 'signature'));
		} catch (HmacException $exception) {
			$this->signature_error($exception);
		}

		$refunds = \wc_get_orders(
			[
				'type'                      => 'shop_order_refund',
				'checkout_refund_unique_id' => $refund_unique_id,
			]
		);

		if (empty($refunds)) {
			wp_die(esc_html__('Refund cannot be found.', 'paytrail-for-woocommerce'), '', 404);
		} else {
			$refund = $refunds[0];
		}

		switch ($refund_callback) {
			case 'success':
				$amount = $refund->get_meta('_checkout_refund_amount');
				$reason = $refund->get_meta('_checkout_refund_reason');

				$refund->set_amount($amount);
				$refund->set_reason($reason);

				$order = \wc_get_order($order_id);

				$order->add_order_note(
					__('Refund process completed.', 'paytrail-for-woocommerce')
				);

				$refund->update_meta_data('_checkout_refund_processing', false);
				$refund->save();
				break;
			case 'cancel':
				$refund->delete(true);

				$order_note = __(
					'Refund was cancelled by the payment provider.',
					'paytrail-for-woocommerce'
				);

				$order = \wc_get_order($order_id);
				$order->add_order_note($order_note);

				/**
				 * Delete refund action
				 *
				 * @since 1.0
				 */
				do_action('woocommerce_refund_delete', $refund->get_id(), $order_id);
				break;
		}

		die('ok');
	}

	/**
	 * Show the payment method form in the checkout.
	 *
	 * @return void
	 */
	public function payment_fields() {
		if (is_checkout() && $this->use_provider_selection()) {
			$this->provider_form();
		} elseif (is_checkout()) {
			$this->payment_description();
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 * @throws \Exception If the processing fails, this error is handled by WooCommerce.
	 */
	public function process_payment( $order_id) {
		$this->log('Paytrail: process_payment', 'debug');
		// @var WC_Order $order
		$order = wc_get_order($order_id);
		$token_id = filter_input(INPUT_POST, 'wc-paytrail-payment-token');

		// Define if the process should die if an error occurs.
		$die_on_error = filter_input(INPUT_POST, 'woocommerce_pay') ? true : false;

		// Get the wanted payment provider and check that it exists
		if ($this->use_provider_selection()) {
			$payment_provider = filter_input(INPUT_POST, 'payment_provider');
		} else {
			$payment_provider = filter_input(INPUT_POST, 'payment_method');
		}

		$is_token_payment = !empty($token_id);

		if (! $payment_provider && ! $is_token_payment) {
			wc_add_notice(__(
				'The payment provider was not chosen.',
				'paytrail-for-woocommerce'
			), 'error');
			return [
				'result' => 'failure'
			];
		} elseif ($is_token_payment) {
			$this->log('Paytrail: process_payment, is token payment', 'debug');
			$payment_provider = 'creditcard';
		}

		if ($is_token_payment) {
			$token = \WC_Payment_Tokens::get($token_id);

			$this->log('Paytrail: process_payment, add_payment_token', 'debug');
			$order->add_payment_token($token);

			if ($this->helper::getIsSubscriptionsEnabled()) {
				$subscriptions = wcs_get_subscriptions_for_order($order_id);
				$this->log('Paytrail: add_payment_token to subscriptions', 'debug');
				foreach ($subscriptions as $subscription) {
					$subscription->add_payment_token($token);
				}
			}

			$payment = new CitPaymentRequest();

			if ($token && method_exists($token, 'get_token')) {
				$payment->setToken($token->get_token());
			} else {
				$this->log('Paytrail: Token value: ' . print_r($token, true), 'debug');
			}

		} else {
			$this->log('Paytrail: init PaymentRequest', 'debug');
			$payment = new PaymentRequest();
		}

		if (0 == floatval($order->get_total())) {
			$this->log('Paytrail: process_payment, order total 0, payment complete, order needs processing' . $order->needs_processing(), 'debug');
			$order->payment_complete();

			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url($order)
			];
		}

		$this->set_base_payment_data($payment, $order);

		$this->log('Paytrail: process_payment, order update_meta_data', 'debug');
		// Save the reference for possible later use.
		$order->update_meta_data('_checkout_reference', $payment->getReference());
		// Save it also as a key for fast indexed searches.
		$order->update_meta_data('_checkout_reference_' . $payment->getReference(), true);
		// Save the wanted payment provider to the order
		$order->update_meta_data('_checkout_payment_provider', $payment_provider);
		$order->save();

		// Create a payment via Paytrail SDK
		try {
			if ($is_token_payment) {
				return $this->create_cit_payment($payment, $order);
			} else {
				return $this->create_normal_payment($payment, $order, $payment_provider);
			}
		} catch (ValidationException $exception) {
			$message = __(
				'An error occurred validating the payment.',
				'paytrail-for-woocommerce'
			);

			$this->error($exception, $message, $die_on_error);
		} catch (HmacException $exception) {
			$this->signature_error($exception, $die_on_error);
		} catch (\Exception $exception) {
			$message = __(
				'An error occurred performing the payment request.',
				'paytrail-for-woocommerce'
			);

			$this->error($exception, $message, $die_on_error);
		}

		return [
			'result'   => 'failure'
		];
	}

	/**
	 * Create payment
	 *
	 * @param PaymentRequest|CitPaymentRequest $payment
	 * @param WC_Order $order
	 * @param $payment_provider
	 * @return array
	 * @throws HmacException
	 * @throws ValidationException
	 * @throws \Exception
	 */
	private function create_normal_payment( $payment, $order, $payment_provider) {
		$this->log('Paytrail: create_normal_payment', 'debug');

		try {
			// Log the payment request if debug log is enabled.
			$this->log('Paytrail\SDK\Request\PaymentRequest: ' . json_encode($payment), 'info');
			$response = $this->client->createPayment($payment);
		} catch (\Exception $exception) {
			// Log the error message if debug log is enabled.
			$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');
			new \WP_Error($exception->getCode(), $exception->getMessage());

			//Add error messages to be displayed to the user by Woocommerce
			$exceptionError = $exception->getMessage();
			$jsonData = json_decode($exceptionError, true);

			//The API may return JSON data in the message rather than plain string
			if ($jsonData && isset($jsonData['message'])) {
				wc_add_notice ($jsonData['message'], 'error');
				//The API can return multiple error messages so add each of these messages
				if (isset($jsonData['meta']) && is_array($jsonData['meta'])) {
					foreach ($jsonData['meta'] as $meta) {
						wc_add_notice ($meta, 'error');
					}
				}
			} else {
				wc_add_notice (ucwords($exceptionError), 'error');
			}
		}

		if (!isset($response) || null === $response) {
			$this->log('FAILURE: Response is NULL or empty', 'error');
			return [
				'result'   => 'failure'
			];
		}

		if ($this->use_provider_selection()) {
			$this->log('Paytrail: create_normal_payment, use_provider_selection = true', 'debug');
			$providers = $response->getProviders();

			// Get only the wanted payment provider object
			$wanted_provider = $this->get_wanted_provider($providers, $payment_provider);

			WC()->session->set('payment_provider', $wanted_provider);

			$message = sprintf(
				// translators: First parameter is transaction ID, the other is the name of the payment provider.
				__(
					'Transaction %1$s created with payment provider %2$s.',
					'paytrail-for-woocommerce'
				),
				$response->getTransactionId(),
				!empty($wanted_provider->getName()) ? $wanted_provider->getName() : ucfirst($payment_provider)
			);
			$this->log('Paytrail: create_normal_payment, use_provider_selection = true, redirect', 'debug');
			$order->add_order_note($message);

			return [
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url(true),
			];
		} else {
			$this->log('Paytrail: create_normal_payment, use_provider_selection = false', 'debug');
			$message = sprintf(
				// translators: First parameter is transaction ID, the other is the name of the payment provider.
				__(
					'Transaction %1$s created and user redirected to the payment provider selection page.',
					'paytrail-for-woocommerce'
				),
				$response->getTransactionId()
			);

			$order->add_order_note($message);
			$this->log('Paytrail: create_normal_payment, use_provider_selection = false, redirect', 'debug');
			return [
				'result'   => 'success',
				'redirect' => $response->getHref(),
			];
		}
	}

	/**
	 * Create CIT payment
	 *
	 * @param CitPaymentRequest $payment
	 * @param WC_Order $order
	 * @throws HmacException
	 * @throws ValidationException
	 */
	private function create_cit_payment( $payment, $order) {
		$this->log('Paytrail: create_cit_payment', 'debug');

		try {
			$response = $this->client->createCitPaymentCharge($payment);

			// Log the payment request if debug log is enabled.
			$this->log('Paytrail\SDK\Request\CitPaymentRequest: ' . json_encode($payment), 'info');
		} catch (\Exception $exception) {
			$fail_message = __('Failed to create token payment using card.', 'paytrail-for-woocommerce');

			// Log the error message if debug log is enabled.
			$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');
			new \WP_Error($exception->getCode(), $exception->getMessage());

			wc_add_notice($fail_message, 'error');

			$order->add_order_note($fail_message);

			return [
				'result'   => 'fail'
			];
		}
		$requires_threeds = $response->getThreeDSecureUrl() !== null;

		if ($response->getTransactionId() === null && $requires_threeds) {
			throw new \Exception('Transcaction Id not found');
		}

		$message = sprintf(
			// translators: First parameter is transaction ID, and the other whether 3DS authentication was required.
			__(
				'Transaction %1$s created by token payment using card. Requires 3DS: %2$s',
				'paytrail-for-woocommerce'
			),
			$response->getTransactionId(),
			$requires_threeds ? __('yes', 'paytrail-for-woocommerce') : __('no', 'paytrail-for-woocommerce')
		);

		$order->add_order_note($message);

		if (!$requires_threeds) {
			$this->log('Paytrail: create_cit_payment, No 3DS required, payment_complete ', 'info');
			$order->payment_complete($response->getTransactionId());
		}

		$redirect_url = !empty($response->getThreeDSecureUrl()) ? $response->getThreeDSecureUrl() : $this->get_return_url($order);

		return [
			'result'   => 'success',
			'redirect' => $redirect_url
		];
	}

	/**
	 * Create MIT payment
	 *
	 * @param MitPaymentRequest $payment
	 * @param WC_Order $order
	 * @return bool
	 * @throws \Exception
	 */
	private function create_mit_payment( $payment, $order) {
		try {
			$response = $this->client->createMitPaymentCharge($payment);

			// Log the payment request if debug log is enabled.
			$this->log('Paytrail\SDK\Request\MitPaymentRequest: ' . json_encode($payment), 'info');
		} catch (\Exception $exception) {
			$fail_message = __('Failed to create token payment using card.', 'paytrail-for-woocommerce');

			// Log the error message if debug log is enabled.
			$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');
			new \WP_Error($exception->getCode(), $exception->getMessage());

			$order->add_order_note($fail_message);

			return false;
		}

		if ($response->getTransactionId() === null) {
			throw new \Exception('Transcaction Id not found');
		}

		$message = sprintf(
			// translators: First parameter is transaction ID.
			__(
				'Transaction %1$s created by token payment using card.',
				'paytrail-for-woocommerce'
			),
			$response->getTransactionId()
		);

		$order->add_order_note($message);
		$this->log('Paytrail: create_mit_payment payment_complete ', 'info');
		$order->payment_complete($response->getTransactionId());

		return true;
	}

	/**
	 * Set payment data
	 *
	 * @param PaymentRequest|CitPaymentRequest|MitPaymentRequest $payment
	 * @param WC_Order $order
	 * @return mixed
	 * @throws \Exception
	 */
	private function set_base_payment_data( $payment, $order) {
		// Set the order ID as the stamp to the payment request
		$payment->setStamp(get_current_blog_id() . '-' . $order->get_id() . '-' . time());

		// Use WooCom order number as reference
		$reference = $order->get_order_number();

		// Calculate bank reference for transaction-specific settlements
		if ( $this->transaction_settlement_enable ) {
			$reference = $this->calculate_reference( $reference );
		}

		// Set WooCommerce order number as the payment reference
		$payment->setReference($reference);

		// Fetch current currency and the cart total
		$currency    = get_woocommerce_currency();
		$order_total = $this->helper->handle_currency($order->get_total());

		// Set the aforementioned values to the payment request
		$payment->setCurrency($currency)
			->setAmount($order_total);

		// Create a customer object from the order
		$customer = $this->create_customer($order);

		// Set the customer object to the payment request
		$payment->setCustomer($customer);

		// Create a billing address and assign it to the payment request
		$billing_address = $this->create_address($order, 'invoicing');

		if ($billing_address) {
			$payment->setInvoicingAddress($billing_address);
		}

		// Create a shipping address and assign it to the payment request
		$shipping_address = $this->create_address($order, 'delivery');

		if ($shipping_address) {
			$payment->setDeliveryAddress($shipping_address);
		}

		$payment->setLanguage(Helper::getLocale());

		// Get the items from the order
		$items = $this->get_order_items($order);

		// Assign the items to the payment request.
		$payment->setItems(array_filter($items));

		// Create and assign the return urls
		$payment->setRedirectUrls($this->create_redirect_url($order));
		$payment->setCallbackUrls($this->create_callback_url());

		// Set callback delay to the payment request
		$payment->setCallbackDelay(3);

		return $payment;
	}

	/**
	 * Get order items
	 *
	 * @param WC_Order|\WC_Subscription $order
	 * @return array
	 * @throws \Exception
	 */
	private function get_order_items( $order) {
		/**
		 * Get the items from the order
		 *
		 * @since 1.0
		 */
		$order_items = apply_filters('woocommerce_paytrail_gateway_get_order_items', $order->get_items([ 'line_item', 'fee', 'shipping' ]), $order);
		$order_total = intval($this->helper->handle_currency($order->get_total()));

		// Convert items to SDK Item objects.
		$items = array_map(
			function ( $item) use ( $order) {
				return $this->create_item($item, $order);
			},
			$order_items
		);

		$sub_sum = intval(array_sum(array_map(function ( Item $item) {
			return ( $item->getUnitPrice() * $item->getUnits() );
		}, $items)));

		$diff = $order_total - $sub_sum;

		// If item total is negative, add positive amount for it.
		if ($diff > 0) {
			$rounding_item = new Item();
			$rounding_item->setDescription(__('Rounding', 'paytrail-for-woocommerce'));
			$rounding_item->setVatPercentage(0);
			$rounding_item->setUnits(1);
			$rounding_item->setUnitPrice(abs($diff));
			$rounding_item->setProductCode('rounding-row');

			$items[] = $rounding_item;
		} elseif ($diff < 0) {
			$items = $this->fix_rounding_error($items, $diff);
		}

		return $items;
	}

	private function fix_rounding_error( $items, $diff) {
		// Subtract rounding error from first not zero price item if sub sum is too high.
		$lastItemKey = $this->getLastNonZeroItemKey($items, $diff);
		$lastItem = $items[$lastItemKey];
		$lastItem->setUnitPrice($lastItem->getUnitPrice() + $diff);
		$items[$lastItemKey] = $lastItem;

		// If item quantity is not one, there's still negative difference to fix.
		if ($lastItem->getUnits() > 1) {
			$difference = ( $lastItem->getUnits() -1 )*$diff;

			$rounding_item = new Item();
			$rounding_item->setDescription(__('Rounding', 'paytrail-for-woocommerce'));
			$rounding_item->setVatPercentage(0);
			$rounding_item->setUnits(1);
			$rounding_item->setUnitPrice(abs($difference));
			$rounding_item->setProductCode('rounding-row');

			$items[] = $rounding_item;
		}

		return $items;
	}

	/**
	 * Loop items and find first non zero item to subtract difference.
	 */
	private function getLastNonZeroItemKey( $items, $diff ) {
		foreach ($items as $key => $item) {
			if (( $item->getUnitPrice() + $diff ) > 0) {
				// Return on first non zero item
				return $key;
			}
		}
	}

	/**
	 * Get payment providers
	 *
	 * @param $providers
	 * @param $payment_provider
	 * @return mixed|null
	 */
	private function get_wanted_provider( $providers, $payment_provider) {
		// Get only the wanted payment provider object
		return
			array_reduce(
				$providers,
				function ( $carry, $item = null) use ( $payment_provider) {
					if ($item && $item->getId() === $payment_provider) {
						return $item;
					}
					return $carry;
				}
			);
	}

	/**
	 * Process scheduled payment
	 *
	 * @param $amount
	 * @param WC_Order $order
	 * @throws \Exception
	 */
	public function scheduled_subscription_payment( $amount, $order) {
		$this->log('Paytrail: scheduled_subscription_payment', 'debug');
		$fail_message = __('Cannot schedule subscription payment. No valid tokens found for order.', 'paytrail-for-woocommerce');

		$tokens = \WC_Payment_Tokens::get_order_tokens($order->get_id());
		$validTokens = [];
		foreach ($tokens as $token) {
			if (!$token->validate()) {
				continue;
			}
			$validTokens[] = $token;
		}
		if (empty($validTokens)) {
			// Log the error message if debug log is enabled.
			$this->log($fail_message, 'error');
			$order->add_order_note($fail_message);
			return false;
		}
		try {
			$token = reset($validTokens);

			$payment = new MitPaymentRequest();
			$payment->setToken($token->get_token());

			$this->set_base_payment_data($payment, $order);

			// Save the reference for possible later use.
			$order->update_meta_data('_checkout_reference', $payment->getReference());

			// Save it also as a key for fast indexed searches.
			$order->update_meta_data('_checkout_reference_' . $payment->getReference(), true);
			$order->save();
			$this->create_mit_payment($payment, $order);
		} catch (\Exception $exception) {
			// Log the error message if debug log is enabled.
			$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');
			return false;
		}
		return true;
	}

	/**
	 * Process refunds.
	 *
	 * @param integer $order_id Order ID to refund from.
	 * @param integer $amount   Optionally the refund amount if not the whole sum.
	 * @param string  $reason   Optional reason for the refund.
	 * @return boolean|\WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '') {
		$this->log('Paytrail: process_refund', 'debug');

		try {
			$order = \wc_get_order($order_id);

			// Create a unique identifier for the refund
			$refund_unique_id = sha1(uniqid(true));

			$refund = new RefundRequest();

			if ($amount) {
				$refund->setAmount($this->helper->handle_currency($amount));
			} else {
				$refund->setAmount($this->helper->handle_currency($order->get_total()));
				$amount = $order->get_total();
			}

			if ($refund->getAmount() === 0) {
				return new \WP_Error(
					'400',
					__(
						'The refund amount must be larger than 0.',
						'paytrail-for-woocommerce'
					)
				);
			}

			$price = $amount;

			$url = new CallbackUrl();

			$callbacks = $this->create_redirect_url($order);

			$success_callback = add_query_arg(
				array(
					'refund_callback' => 'success',
					'refund_unique_id' => $refund_unique_id,
					'order_id' => $order_id
				),
				$callbacks->getSuccess()
			);
			$cancel_callback = add_query_arg(
				array(
					'refund_callback' => 'cancel',
					'refund_unique_id' => $refund_unique_id,
					'order_id' => $order_id
				),
				$callbacks->getSuccess()
			);

			$url->setSuccess($success_callback)
				->setCancel($cancel_callback);

			$refund->setCallbackUrls($url);

			$transaction_id = $order->get_transaction_id();

			$order->add_order_note(
				sprintf(
					// Translators: placeholder is the optional reason for the refund.
					__('Refunding process started.%s', 'paytrail-for-woocommerce'),
					$reason ? esc_html__(' Reason: ', 'paytrail-for-woocommerce') . esc_html($reason) : ''
				)
			);

			// Do some additional stuff after the refund object has been created
			add_action(
				'woocommerce_order_refunded',
				function ( $order_id, $refund_id) use ( $order, $refund, $reason, $transaction_id, $amount, $price, $refund_unique_id) {
					$refund_object = wc_get_order($refund_id);

					try {
						$this->client->refund($refund, $transaction_id);
					} catch (\Exception $e) {
						switch ($e->getCode()) {
							case 422:
								// An email refund request is needed
								$email = $order->get_billing_email();

								$email_refund_request = new EmailRefundRequest();

								$email_refund_request->setEmail($email);
								$email_refund_request->setAmount($refund->getAmount());
								$email_refund_request->setCallbackUrls($refund->getCallbackUrls());

								if (count($refund->getItems()) > 0) {
									$email_refund_request->setItems($refund->getItems());
								}

								try {
									$this->client->emailRefund($email_refund_request, $transaction_id);
								} catch (\Exception $e) {
									switch ($e->getCode()) {
										case 422:
											$refund_object->delete(true);
											$order->add_order_note(
												__(
													'The payment provider does not support either regular or email refunds. The refund was cancelled.',
													'paytrail-for-woocommerce'
												)
											);
											$order->update_status('failed');
											return false; // Return when an error occurred.
											// Default, should be 400.
										default:
											$refund_object->delete(true);
											$order->add_order_note(
												__(
													'Something went wrong with the email refund and it was cancelled.',
													'paytrail-for-woocommerce'
												)
											);
											$order->update_status('failed');
											return false; // Return when an error occurred.
									}
								}
								break; // Break the email refund processing.
								// Default, should be 400.
							default:
								$refund_object->delete(true);
								$order->add_order_note(
									__(
										'Something went wrong with the refund and it was cancelled.',
										'paytrail-for-woocommerce'
									)
								);
								$order->update_status('failed');
								return false; // Return when an error occurred.
						}
					}

					$reason = $refund_object->get_reason();

					$refund_object->update_meta_data( '_checkout_refund_amount', $amount);
					$refund_object->update_meta_data( '_checkout_refund_reason', $reason);
					$refund_object->update_meta_data( '_checkout_refund_unique_id', $refund_unique_id);
					$refund_object->update_meta_data( '_checkout_refund_processing', true);

					$refund_object->set_amount(0);
					$refund_object->set_reason($reason . ' Refund is still being processed. The status and the amount (' . $price . ') of the refund will update when the processing is completed.');

					$refund_object->save();

					return true;
				},
				10,
				2
			);

			return true;
		} catch (\Exception $exception) {
			$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');

			return new \WP_Error($exception->getCode(), $exception->getMessage());
		}
	}

	/**
	 * Make processing refund items amounts to show in italic
	 *
	 * @param int $order_id Order ID to handle.
	 * @return void
	 */
	public function refund_items( $order_id) {
		$order = new \WC_Order($order_id);
		$refunds = $order->get_refunds();

		if ($refunds) {
			array_walk(
				$refunds,
				function ( $refund) {
					$meta = $refund->get_meta('_checkout_refund_processing');
					if ($meta) {
						echo '<style>';
						echo '[data-order_refund_id=' . esc_html($refund->get_id()) . '] span.amount {';
						echo 'font-style: italic;';
						echo '}';
						echo '</style>';
					};
				}
			);
		}
	}

	/**
	 * Renders the provider selection form
	 *
	 * @return void
	 */
	protected function provider_form() {
		$cart_total = $this->helper->get_cart_total();
		$res = [];

		$providers = $this->get_grouped_payment_providers($cart_total, Helper::getLocale());

		// If there was an error getting the payment providers, show it
		if (! empty($providers['error'])) {
			echo '<p>' . esc_html($providers['error']) . '</p>';
			return;
		}
		$res['terms'] = !empty($providers['terms']) ? $providers['terms'] : '';
		$res['groups'] = $providers['groups'];

		$provider_form_view = new View('ProviderForm');

		$provider_form_view->render($res);
	}

	protected function payment_description() {
		$data['description'] = $this->description;

		$view = new View('PaymentDescription');
		$view->render($data);
	}

	/**
	 * Create SDK customer object.
	 *
	 * @param \WC_Order $order The order to create the customer object from.
	 *
	 * @return \CheckoutFinland\SDK\Model\Customer
	 */
	protected function create_customer( \WC_Order $order) {
		$customer = new Customer();

		if (!$order) {
			return $customer;
		}

		$customer->setEmail($order->get_billing_email())
			->setFirstName($order->get_billing_first_name())
			->setLastName($order->get_billing_last_name())
			->setPhone($order->get_billing_phone())
			->setCompanyName($order->get_billing_company());

		return $customer;
	}

	/**
	 * Get the list of payment providers
	 *
	 * @param integer $payment_amount Payment amount in currency minor unit, eg. cents.
	 * @return array
	 */
	protected function get_payment_providers( $payment_amount) {
		try {
			$providers = $this->client->getPaymentProviders($payment_amount);
		} catch (HmacException $exception) {
			$providers = $this->get_payment_providers_error_handler($exception);
		} catch (\Exception $exception) {
			$providers = $this->get_payment_providers_error_handler($exception);
		}

		return $providers;
	}

	/**
	 * Get the grouped list of payment providers
	 *
	 * @param integer $payment_amount Payment amount in currency minor unit, eg. cents.
	 * @param string $locale
	 * @return array
	 */
	protected function get_grouped_payment_providers( $payment_amount, $locale) {
		$groups = [];

		if ($this->helper::getIsSubscriptionsEnabled()) {
			$groups = ['creditcard'];
		}

		try {
			$providers = $this->client->getGroupedPaymentProviders($payment_amount, $locale, $groups);
		} catch (HmacException $exception) {
			$providers = $this->get_payment_providers_error_handler($exception);
		} catch (\Exception $exception) {
			$providers = $this->get_payment_providers_error_handler($exception);
		}

		return $providers;
	}

	/**
	 * Error handler for get_payment_providers method.
	 *
	 * @param \Exception $exception Exception to handle.
	 * @return array
	 */
	protected function get_payment_providers_error_handler( \Exception $exception) {

		// Log the error message.
		$this->log($exception->getMessage() . $exception->getTraceAsString(), 'error');

		$error = __(
			'An error occurred loading the payment providers.',
			'paytrail-for-woocommerce'
		);

		/**
		 * You can use this filter to modify the error message.
		 *
		 * @since 1.0
		 */
		$error = apply_filters('paytrail_provider_form_error', $error);
		return [
			'error' => $error,
		];
	}

	/**
	 * Create SDK address object.
	 *
	 * @param \WC_Order $order The order to create the address object from.
	 * @param string    $type  Whether we are creating an invoicing or a delivery address.
	 * @return Address|null
	 */
	protected function create_address( \WC_Order $order, $type = 'invoicing') {
		$address = new Address();

		if (!$order) {
			return;
		}

		switch ($type) {
			case 'delivery':
				$prefix = 'shipping_';
				break;
			default:
				$prefix = 'billing_';
				break;
		}

		$address_suffix = empty($order->{ 'get_' . $prefix . 'address_2' }())
			? null : ' ' . $order->{ 'get_' . $prefix . 'address_2' }();

		// Append 2nd address line to the address field if present
		$address->setStreetAddress(( $order->{ 'get_' . $prefix . 'address_1' }() . $address_suffix ))
			->setPostalCode($order->{ 'get_' . $prefix . 'postcode' }())
			->setCity($order->{ 'get_' . $prefix . 'city' }())
			->setCounty($order->{ 'get_' . $prefix . 'state' }())
			->setCountry($order->{ 'get_' . $prefix . 'country' }());

		if (empty($address->getCountry())) {
			$address->setCountry($this->get_option('fallback_country', ''));
		}

		// If we have any of the listed properties, we are good to go
		$has_values = array_filter(
			[ 'StreetAddress', 'PostalCode', 'City', 'County' ],
			function ( $key) use ( $address) {
				return ! empty($address->{ 'get' . $key }());
			}
		);

		return $has_values ? $address : null;
	}

	/**
	 * Create a SDK item object.
	 *
	 * @param WC_Order_Item $order_item The order item object to create the item object from.
	 * @param WC_Order      $order      The current order object.
	 *
	 * @return Item|null
	 */
	protected function create_item( WC_Order_Item $order_item, WC_Order $order) {
		$item = new Item();

		// Get the item total with taxes and without rounding.
		// Then convert it into the integer format required by Paytrail.
		$sub_total = $this->helper->handle_currency($order->get_item_total($order_item, true, false));
		$item->setUnitPrice($sub_total)
			->setUnits((int) $order_item->get_quantity());

		$tax_rate = $this->get_item_tax_rate($order_item);

		$item->setVatPercentage($tax_rate)
			->setProductCode($this->get_item_product_code($order_item))
			->setDescription($this->get_item_description($order_item))
			->setStamp((string) $order_item->get_id());

		return $item;
	}

	/**
	 * Get an order item product code text.
	 *
	 * @param WC_Order_Item $item An order item.
	 *
	 * @return string
	 */
	protected function get_item_product_code( WC_Order_Item $item) {
		$product_code = '';
		switch (get_class($item)) {
			case WC_Order_Item_Product::class:
				$product_code = !empty($item->get_product()->get_sku()) ? $item->get_product()->get_sku() : $item->get_product()->get_id();
				break;
			case WC_Order_Item_Fee::class:
				$product_code = __('fee', 'paytrail-for-woocommerce');
				$item->get_type();
				break;
			case WC_Order_Item_Shipping::class:
				$product_code = __('shipping', 'paytrail-for-woocommerce');
				break;
		}

		/**
		 * Return item product code
		 *
		 * @since 1.0
		 */
		return apply_filters('paytrail_item_product_code', $product_code, $item);
	}

	/**
	 * Get an order item description text.
	 *
	 * @param WC_Order_Item $item An order item.
	 *
	 * @return string
	 */
	protected function get_item_description( WC_Order_Item $item) {
		switch (get_class($item)) {
			case WC_Order_Item_Product::class:
				$description = !empty($item->get_product()->get_name()) ? $item->get_product()->get_name() : $item->get_product()->get_id();
				break;
			default:
				$description = $item->get_name();
				break;
		}

		// Ensure the description is maximum of 1000 characters long.
		$description = mb_substr($description, 0, 1000);

		/**
		 * Return item description
		 *
		 * @since 1.0
		 */
		return apply_filters('paytrail_item_description', $description, $item);
	}

	/**
	 * Get the tax rate of an order line item.
	 *
	 * @param WC_Order_Item $item  The order line item.
	 *
	 * @return float The tax percentage.
	 */
	protected function get_item_tax_rate( WC_Order_Item $item) {
		$taxes       = $item->get_taxes();
		$tax_total   = 0;
		$total_price = $item->get_total();

		foreach ( $taxes['total'] as $tax_rate_id => $tax ) {
			$tax_total += (float) $tax;
		}

		if ( $total_price > 0 && $tax_total > 0) {
			$tax_rate = NumberUtil::round( ( $tax_total / $total_price ) * 100, self::TAX_RATE_PRECISION );
		} else {
			$tax_rate = 0;
		}

		return $tax_rate;
	}

	/**
	 * Create SDK callback URL object.
	 *
	 * @param \WC_Order $order The order object.
	 * @return CallbackUrl
	 */
	protected function create_redirect_url( \WC_Order $order) {
		$callback = new CallbackUrl();

		$callback->setSuccess($this->get_return_url($order));
		//Customers choosing cancel option will be shown a payment page to allow re-attempt to pay
		$callback->setCancel($order->get_checkout_payment_url());
		return $callback;
	}

	/**
	 * Create SDK callback URL object for Callback urls.
	 *
	 * @return CallbackUrl
	 */
	protected function create_callback_url() {
		$callback = new CallbackUrl();

		$callback->setSuccess(Router::get_url(Plugin::CALLBACK_URL, 'index'));
		$callback->setCancel(Router::get_url(Plugin::CALLBACK_URL, 'index'));

		return $callback;
	}

	/**
	 * Handle custom search query vars to get orders by certain reference or refund identifier.
	 *
	 * @param array $query      Args for WP_Query.
	 * @param array $query_vars Query vars from WC_Order_Query.
	 * @return array
	 */
	public function handle_custom_searches( $query, $query_vars) {
		if (! empty($query_vars['checkout_reference'])) {
			$query['meta_query'][] = [
				'key'     => '_checkout_reference_' . esc_attr($query_vars['checkout_reference']),
				'compare' => 'EXISTS',
			];
		}

		if (! empty($query_vars['checkout_refund_unique_id'])) {
			$query['meta_query'][] = [
				'key'     => '_checkout_refund_unique_id',
				'compare' => '=',
				esc_attr($query_vars['checkout_refund_unique_id']),
			];
		}

		return $query;
	}

	/**
	 * Register scripts
	 *
	 * @return void
	 */
	protected function register_scripts() {
		$plugin_instance = Plugin::instance();

		$plugin_dir_url = $plugin_instance->get_plugin_dir_url();

		$plugin_version = $plugin_instance->get_plugin_info()['Version'];

		wp_register_script(
			'paytrail-woocommerce-payment-fields',
			$plugin_dir_url . 'assets/dist/main.js',
			[],
			$plugin_version
		);
	}

	public function enqueue_admin_scripts() {
		$screen = get_current_screen();

		// Check if the current screen is the WooCommerce settings page
		if ($screen && 'woocommerce_page_wc-settings' === $screen->id) {
			// Enqueue the introScripts only on the WooCommerce settings page
			wp_enqueue_script('introScripts');
		}
	}

	/**
	 * Register styles
	 *
	 * @return void
	 */
	protected function register_styles() {
		$plugin_instance = Plugin::instance();

		$plugin_dir_url = $plugin_instance->get_plugin_dir_url();

		$plugin_version = $plugin_instance->get_plugin_info()['Version'];

		wp_register_style(
			'paytrail-woocommerce-payment-fields',
			$plugin_dir_url . 'assets/dist/main.css',
			[],
			$plugin_version
		);

		wp_register_style(
			'introStyles',
			$plugin_dir_url . 'assets/dist/main.css',
			[],
			$plugin_version
		);
	}

	public function enqueue_admin_styles() {
		$screen = get_current_screen();

		// Check if the current screen is the WooCommerce settings page
		if ($screen && 'woocommerce_page_wc-settings' === $screen->id) {
			// Enqueue the style 'paytrail-woocommerce-payment-fields'
			wp_enqueue_style('introStyles');
		}
	}

	/**
	 * Insert new message to the log.
	 *
	 * @param string $message Message to log.
	 * @param string $level   Log level. Defaults to 'info'. Possible values:
	 *                        emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public function log( $message, $level = 'info') {
		if ($this->debug) {
			if (empty($this->logger)) {
				$this->logger = \wc_get_logger();
			}

			$context = [ 'source' => Plugin::GATEWAY_ID ];

			$this->logger->log($level, $message, $context);
		}
	}

	/**
	 * A wrapper for killing the process, logging and displaying error messages.
	 *
	 * @param \Exception $exception An exception instance.
	 * @param string     $message   A message to print out for the end user.
	 * @param bool       $die       Defines if the process should be terminated.
	 * @throws \Exception If the process is not killed, the error is passed on.
	 */
	protected function error( \Exception $exception, $message, $die = true) {
		$glue = PHP_EOL . '- ';

		$log_message = $message . $glue;

		$this->log($log_message . PHP_EOL . $exception->getTraceAsString(), 'error');

		/**
		 * You can use this filter to modify the error message.
		 *
		 * @since 1.0
		 */
		$error = apply_filters('paytrail_error_message', $message, $exception);

		if (true === $die) {
			wp_die(esc_html($error), '', esc_html($exception->getCode()));
		} else {
			throw $exception;
		}
	}

	/**
	 * Kills the process and prints out a signature error message.
	 *
	 * @param HmacException $exception The exception instance.
	 * @param bool          $die       Defines if the process should be terminated.
	 */
	protected function signature_error( HmacException $exception, $die = true) {
		$message = __(
			'An error occurred validating the signature.',
			'paytrail-for-woocommerce'
		);

		/**
		 * You can use this filter to modify the error message.
		 *
		 * @since 1.0
		 */
		$message = apply_filters('paytrail_signature_error', $message, $exception);

		$this->error($exception, $message, $die);
	}
}
