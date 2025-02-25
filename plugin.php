<?php
/**
 * Plugin Name: Paytrail for WooCommerce
 * Plugin URI: https://github.com/paytrail/paytrail-for-woocommerce
 * Description: Paytrail is a payment gateway that offers 20+ payment methods for Finnish customers.
 * Version: 2.4.1
 * Requires at least: 4.9
 * Requires Plugins: woocommerce
 * Tested up to: 6.7
 * Requires PHP: 7.3
 * WC requires at least: 3.5
 * WC tested up to: 9.6
 * Author: Paytrail
 * Author URI: https://www.paytrail.com/
 * Text Domain: paytrail-for-woocommerce
 * Domain Path: /languages
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Copyright: Paytrail
 */

namespace Paytrail\WooCommercePaymentGateway;

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * The main plugin class
 */
final class Plugin {

    /**
     * WooCommerce payment gateway ID.
     */
    public const GATEWAY_ID = 'paytrail';

    /**
     * Merchant ID for the test mode.
     */
    public const TEST_MERCHANT_ID = 375917;

    /**
     * Secret key for the test mode.
     */
    public const TEST_SECRET_KEY = 'SAIPPUAKAUPPIAS';

    public const PAYMENT_METHOD_IMG_URL = 'https://static.paytrail.com/static/img/payment-methods';

    public const BASE_URL = 'paytrail/';

    public const ADD_CARD_REDIRECT_SUCCESS_URL = 'card-success';

    public const ADD_CARD_REDIRECT_CANCEL_URL = 'card-cancel';

    public const ADD_CARD_CONTEXT_MY_ACCOUNT = 'my_account';

    public const ADD_CARD_CONTEXT_CHECKOUT= 'checkout';

    public const ADD_CARD_CONTEXT_CHANGE_PAYMENT_METHOD = 'change_payment_method';

    public const CARD_ENDPOINT = 'card';

    public const CALLBACK_URL = 'callback';

    /**
     * Singleton instance.
     *
     * @var Plugin
     */
    private static $instance;

    /**
     * Plugin version.
     *
     * @var string
     */
    public static $version;

    /**
     * Plugin directory.
     *
     * @var string
     */
    protected $plugin_dir;

    /**
     * Plugin directory URL.
     *
     * @var string
     */
    protected $plugin_dir_url;

    /**
     * Container array for possible initialization errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Plugin info
     *
     * @var array
     */
    protected $plugin_info = [
        'Plugin Name',
        'Plugin URI',
        'Description',
        'Version',
        'Author',
        'Author URI',
        'Text Domain',
        'Domain Path',
    ];
    public function enqueue_jquery() {
        // Enqueue jQuery script
        wp_enqueue_script('jquery');
    }

    /**
     * Constructor function
     */
    protected function __construct() {
        $this->plugin_dir     = __DIR__;
        $this->plugin_dir_url = plugin_dir_url( __FILE__ );
        $this->plugin_info    = array_combine( $this->plugin_info, get_file_data( __FILE__, $this->plugin_info ) );

        self::$version = $this->plugin_info['Version'];

        // Load the plugin textdomain.
        load_plugin_textdomain( 'paytrail-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Register customizations
        add_action( 'customize_register', [ $this, 'checkout_customizations' ] );
        // Add custom styles
        add_action( 'wp_head', [ $this, 'paytrail_checkout_customize_css' ] );
        // Enable WP Dashicons on frontend
        add_action( 'wp_enqueue_scripts', function() {
            wp_enqueue_style( 'dashicons' );
        } );

        add_action( 'before_woocommerce_init', function() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks',__FILE__, true );
            }
        } );

        // Blocks compatibility
		add_action( 'woocommerce_blocks_loaded', [__CLASS__,'register_blocks_support'] );
        
        // Enqueue jQuery
        add_action('admin_enqueue_scripts', array($this, 'enqueue_jquery'));
        add_action('admin_enqueue_scripts', array($this, 'enque_jquery_scripts'));

        //Add OP Lasku calculator to the product and cart page
        add_action('woocommerce_init', array($this, 'op_lasku_init'));
    }

    /**
     * Register intro scripts
     */
    public static function register_intro_scripts() {
        // Get plugin directory URL
        $plugin_instance = Plugin::instance();
		$plugin_dir_url = $plugin_instance->get_plugin_dir_url();
		$plugin_version = $plugin_instance->get_plugin_info()['Version'];

        // Register the custom script
        wp_register_script(
            'introScripts',
            $plugin_dir_url . 'dist/assets/frontend/intro-scripts.js',
            ['jquery'], // Dependency on jQuery
            $plugin_version,
            true // Enqueue in the footer
        );

        // Enqueue the custom script
        wp_enqueue_script('introScripts');
    }

    /**
     * Enqueue jQuery UI from WordPress core
     */
    public function enque_jquery_scripts($hook) {
        if ($hook == 'woocommerce_page_wc-settings' && isset($_GET['tab']) && $_GET['tab'] == 'checkout' && isset($_GET['section']) && $_GET['section'] == 'paytrail') {

        wp_enqueue_script('jquery');
        // Enqueue jQuery UI Core
        wp_enqueue_script('jquery-ui-core');
        // Enqueue jQuery UI Dialog
        wp_enqueue_script('jquery-ui-dialog');
        // Add jQuery UI styles
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        // Enqueue intro scripts
        self::register_intro_scripts();

        }
    }

    /**
     * Print custom styles
     */
    public function paytrail_checkout_customize_css() {
        ?>
            <style type="text/css">
                .paytrail-provider-group {
                    background-color: <?php echo get_theme_mod('paytrail_group_background', '#ebebeb'); ?> !important;
                    color: <?php echo get_theme_mod('paytrail_group_text', '#515151'); ?> !important;
                }
                .paytrail-provider-group.selected {
                    background-color: <?php echo get_theme_mod('paytrail_group_highlighted_background', '#33798d'); ?> !important;
                    color: <?php echo get_theme_mod('paytrail_group_highlighted_text', '#ffffff'); ?> !important;
                }
                .paytrail-provider-group.selected div {
                    color: <?php echo get_theme_mod('paytrail_group_highlighted_text', '#ffffff'); ?> !important;
                }
                .paytrail-provider-group:hover {
                    background-color: <?php echo get_theme_mod('paytrail_group_hover_background', '#d0d0d0'); ?> !important;
                    color: <?php echo get_theme_mod('paytrail_group_hover_text', '#515151'); ?> !important;
                }
                .paytrail-provider-group.selected:hover {
                    background-color: <?php echo get_theme_mod('paytrail_group_highlighted_background', '#33798d'); ?> !important;
                    color: <?php echo get_theme_mod('paytrail_group_highlighted_text', '#ffffff'); ?> !important;
                }
                .woocommerce-checkout #payment .paytrail-woocommerce-payment-fields--list-item--input:checked+.paytrail-woocommerce-payment-fields--list-item--wrapper, .woocommerce-checkout #payment .paytrail-woocommerce-payment-fields--list-item:hover .paytrail-woocommerce-payment-fields--list-item--wrapper {
                    border: 2px solid <?php esc_html_e( get_theme_mod('paytrail_method_highlighted', '#33798d')); ?> !important;
                }
                .woocommerce-checkout #payment ul.payment_methods li.paytrail-woocommerce-payment-fields--list-item .paytrail-woocommerce-payment-fields--list-item--wrapper:hover {
                    border: 2px solid <?php esc_html_e( get_theme_mod('paytrail_method_hover', '#5399ad')); ?> !important;
                }
            </style>
        <?php
    }

    /**
     * Customizer options
     */
    public function checkout_customizations( $wp_customize ) {
        // Settings
        $wp_customize->add_setting( 'paytrail_group_background' , array(
            'default'   => '#ebebeb',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_group_text' , array(
            'default'   => '#515151',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_group_highlighted_background' , array(
            'default'   => '#33798d',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_group_highlighted_text' , array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_group_hover_background' , array(
            'default'   => '#d0d0d0',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_group_hover_text' , array(
            'default'   => '#313131',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_method_highlighted' , array(
            'default'   => '#33798d',
            'transport' => 'refresh',
        ) );
        $wp_customize->add_setting( 'paytrail_method_hover' , array(
            'default'   => '#5399ad',
            'transport' => 'refresh',
        ) );
        // Section
        $wp_customize->add_section( 'paytrail_checkout_customize_section' , array(
            'title'      => __( 'Payment page personalization', 'paytrail-for-woocommerce' ),
            'priority'   => 30,
        ) );
        // Controls
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_bgcolor', array(
            'label'      => __( 'Payment method group background', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_background',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_fgcolor', array(
            'label'      => __( 'Payment method group text', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_text',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_bgcolor_selected', array(
            'label'      => __( 'Selected payment method group background', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_highlighted_background',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_fgcolor_selected', array(
            'label'      => __( 'Selected payment method group text', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_highlighted_text',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_bgcolor_hover', array(
            'label'      => __( 'Payment method group background hover', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_hover_background',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_fgcolor_hover', array(
            'label'      => __( 'Payment method group text hover', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_group_hover_text',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_bordercolor_selected', array(
            'label'      => __( 'Selected payment method', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_method_highlighted',
        ) ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'paytrail_bordercolor_hover', array(
            'label'      => __( 'Payment method hover', 'paytrail-for-woocommerce' ),
            'section'    => 'paytrail_checkout_customize_section',
            'settings'   => 'paytrail_method_hover',
        ) ) );
    }

    /**
     * Singleton instance getter function
     *
     * @return Plugin
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            // Construct the object.
            self::$instance = new self();

            // Run initialization checks. If any of the checks
            // fails, interrupt the execution.
            if ( ! self::$instance->initialization_checks() ) {
                return;
            }

            // Check if Composer has been initialized in this directory.
            // Otherwise we just use global composer autoloading.
            if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
                require_once __DIR__ . '/vendor/autoload.php';
            }

            // Create new instance of Router class
            new Router();

            // Add the gateway class to WooCommerce.
            add_filter( 'woocommerce_payment_gateways', function( $gateways ) {
                $gateways[] = Gateway::CLASS;

                return $gateways;
            });

        }

        return self::$instance;
    }

    /**
     * Run checks for plugin requirements.
     *
     * Returns false if checks failed.
     *
     * @return bool
     */
    protected function initialization_checks() {
        $errors = [];

        $errors[] = self::check_php_version();
        $errors[] = self::check_woocommerce_active_status();
        $errors[] = self::check_woocommerce_version();

        $errors = array_filter( $errors );

        if ( ! empty( $errors ) ) {
            add_action( 'admin_notices', function() use ( $errors ) {
                echo '<div class="notice notice-error">';
                array_walk( $errors, 'esc_html_e' );
                echo '</div>';
            });

            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Checks to run on plugin activation
     *
     * @return void
     */
    public static function activation_check() {
        $checks = [
            'check_php_version',
            'check_woocommerce_active_status',
            'check_woocommerce_version',
        ];

        array_walk( $checks, function( $check ) {
            $error = call_user_func( __CLASS__ . '::' . $check );

            if ( $error ) {
                wp_die( esc_html( $error ) );
            }
        });
    }

    /**
     * Ensure that the PHP version is at least 7.3.
     *
     * @return string|null
     */
    public static function check_php_version() : ?string {
        if ( ! version_compare( PHP_VERSION, '7.3.0', '>=' ) ) {
            return sprintf(
                // translators: The placeholder contains the current PHP version.
                esc_html__( 'Paytrail payment gateway plugin requires a PHP version of at least 7.3. You are currently running version %1$s.', 'paytrail-for-woocommerce' ),
                esc_html( PHP_VERSION )
            );
        }

        return null;
    }

    /**
     * Ensure that the WooCommerce plugin is active.
     *
     * @return string|null
     */
    public static function check_woocommerce_active_status() : ?string {
        if ( ! class_exists( '\WC_Payment_Gateway' ) ) {
            return esc_html__( 'Paytrail payment gateway plugin requires WooCommerce to be activated.', 'paytrail-for-woocommerce' );
        }

        return null;
    }

    /**
	 * Register blocks support
	 */
	public static function register_blocks_support() {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) && class_exists( 'Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry' ) ) {
			require_once 'src/PaytrailBlocks.php';

			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function( \Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new Paytrail_Blocks_Support() );
				}
			);
		}
	}

    /**
     * Ensure that we have at least version 3.5 of the WooCommerce plugin.
     *
     * @return string|null
     */
    public static function check_woocommerce_version() : ?string {
        if (
            defined( 'WOO_COMMERCE_VERSION' ) &&
            version_compare( WOO_COMMERCE_VERSION, '3.5' ) === -1
        ) {
            return esc_html__( 'Paytrail gateway plugin requires WooCommerce version of 3.5 or greater.', 'paytrail-for-woocommerce' );
        }

        return null;
    }

    /**
     * Get plugin directory.
     *
     * @return string
     */
    public function get_plugin_dir() : string {
        return $this->plugin_dir;
    }

    /**
     * Get plugin directory URL.
     *
     * @return string
     */
    public function get_plugin_dir_url() : string {
        return $this->plugin_dir_url;
    }

    /**
     * Get plugin info.
     *
     * @return array
     */
    public function get_plugin_info() : array {
        return $this->plugin_info;
    }

    /**
	 * Plugin url.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Plugin url.
	 *
	 * @return string
	 */
	public static function plugin_abspath() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}

    /**
     * Initialize OP Lasku calculator for product and cart page
     */
    public function op_lasku_init() {
        $settings = get_option('woocommerce_paytrail_settings');
        
        if(isset($settings['op_lasku_calculator']) && 'yes' === $settings['op_lasku_calculator']) {
           new \Paytrail\WooCommercePaymentGateway\Providers\OPLasku();
        }
    }
}

add_action( 'plugins_loaded', function() {
    Plugin::instance();
});


register_activation_hook( __FILE__, __NAMESPACE__ . '\\Plugin::activation_check' );
