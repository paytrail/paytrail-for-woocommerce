<?php

namespace Paytrail\WooCommercePaymentGateway\Providers;

use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\Helper;

class OPLasku {

	private $product_price = 0;
	private $cart_total    = 0;

	public function __construct() {
		// Hooks for product and classic cart page
		add_action('woocommerce_before_add_to_cart_form', array($this, 'product_page'));
		add_action('woocommerce_proceed_to_checkout', array($this, 'cart_page'));
		// Register scripts and styles
		add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
		//Enqueue blocks cart scripts and styles
		add_action('woocommerce_blocks_cart_enqueue_data', array($this, 'enqueue_blocks_cart_assets'));
	}

	/**
	 * Enqueue assets for blocks cart
	 */
	public function enqueue_blocks_cart_assets() {
		wp_enqueue_script('paytrail-op-lasku');
		wp_enqueue_style('paytrail-op-lasku');
		wp_localize_script('paytrail-op-lasku', 'op_lasku_data', array(
			'language' => esc_js($this->get_language()),
		));
	}

	/**
	 * Register scripts for blocks cart
	 */
	public static function register_blocks_cart_scripts() {
		$op_lasku_script_path       = '/dist/assets/frontend/op-lasku-helper-blocks.js';
		$op_lasku_script_asset_path = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_abspath() . 'dist/assets/frontend/op-lasku-helper-blocks.asset.php';
		$op_lasku_script_asset      = file_exists($op_lasku_script_asset_path) ? require $op_lasku_script_asset_path : [
			'dependencies' => [],
			'version'      => \Paytrail\WooCommercePaymentGateway\Plugin::$version,
		];
		$op_lasku_script_url        = \Paytrail\WooCommercePaymentGateway\Plugin::plugin_url() . $op_lasku_script_path;
		wp_register_script(
			'paytrail-op-lasku-helper-blocks',
			$op_lasku_script_url,
			$op_lasku_script_asset['dependencies'],
			$op_lasku_script_asset['version'],
			true
		);
	}

	/**
	 * Register scripts for product and classic cart page
	 */
	public function register_scripts() {
		wp_register_script('paytrail-op-lasku', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-widget.js', [], Plugin::$version, true);
		wp_register_script('paytrail-op-lasku-helper', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-helper.js', array('jquery'), Plugin::$version, true);
		wp_register_style('paytrail-op-lasku', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-widget.css', [], Plugin::$version);
	}

	/**
	 * Display OP Lasku calculator in the product page
	 */
	public function product_page() {
		global $product;    
		if (!$product) {
			return;
		}

		$this->product_price = $product->get_price() ? $product->get_price() : 0;

		wp_enqueue_script('paytrail-op-lasku');
		wp_enqueue_script('paytrail-op-lasku-helper');
		wp_enqueue_style('paytrail-op-lasku');

		wp_localize_script('paytrail-op-lasku', 'op_lasku_data', array(
			'product_price' => esc_js($this->product_price),
			'language' => esc_js($this->get_language()),
		));

		echo wp_kses_post('<div id="op-lasku--init"></div>');
	}

	/**
	 * Display OP Lasku calculator in the cart page
	 */
	public function cart_page() {
		$this->cart_total = WC()->cart ? WC()->cart->get_total('raw') : 0;

		wp_enqueue_script('paytrail-op-lasku');
		wp_enqueue_script('paytrail-op-lasku-helper');
		wp_enqueue_style('paytrail-op-lasku');

		wp_localize_script('paytrail-op-lasku', 'op_lasku_data', array(
			'cart_total' => esc_js($this->cart_total),
			'language' => esc_js($this->get_language()),
		));
		echo wp_kses_post('<div id="op-lasku--init"></div>');
	}

	/**
	 * Title for settings page
	 *
	 * @return string
	 */
	public static function settings_title() {
		$icon_url = sprintf('%s/assets/img/icon_oplasku_admin.svg', Plugin::plugin_url());
		return sprintf(
			__('OP Lasku', 'paytrail-for-woocommerce') . '<br><img src="%s" alt="OP Lasku calculator" style="width: 70px; height: auto; margin-top: 5px;">',
			esc_url($icon_url)
		);
	}

	/**
	 * Get language for the calculator
	 *
	 * @return string
	 */
	protected function get_language() {
		$locale = strtolower(Helper::getLocale());
		if ('sv' === $locale) {
			$locale = 'se';
		}
		return $locale;
	}
}
