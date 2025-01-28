<?php

namespace Paytrail\WooCommercePaymentGateway\Providers;

use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\Helper;

class OPLasku
{
    private $product_price;
    private $cart_total;
    private $language;

    public function __construct()
    {
        $this->language = strtolower(Helper::getLocale());
        // Hooks for product and cart page
        add_action('woocommerce_before_add_to_cart_form', array($this, 'product_page'));
        add_action('woocommerce_proceed_to_checkout', array($this, 'cart_page'));
        // Register scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
    }

    public function register_scripts()
    {
        wp_register_script('paytrail-op-lasku', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-widget.js', array(), '1.0.0', true);
        wp_register_script('paytrail-op-lasku-helper', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-helper.js', array('jquery'), '1.0.0', true);
        wp_register_style('paytrail-op-lasku', Plugin::plugin_url() . '/assets/op-lasku-assets/op-lasku-widget.css', array(), '1.0.0');
    }

    /**
     * Display OP Lasku calculator in the product page
     */
    public function product_page()
    {
        global $product;
        if (!$product) return;

        $this->product_price = $product->get_price();
        
        wp_enqueue_script('paytrail-op-lasku');
        wp_enqueue_script('paytrail-op-lasku-helper');
        wp_enqueue_style('paytrail-op-lasku');
        
        wp_localize_script('paytrail-op-lasku', 'op_lasku_data', array(
            'product_price' => esc_js($this->product_price),
            'language' => esc_js($this->language),
        ));

        echo wp_kses_post('<div id="op-lasku--init"></div>');
    }

    /**
     * Display OP Lasku calculator in the cart page
     */
    public function cart_page()
    {
        $this->cart_total = WC()->cart ? WC()->cart->get_total('raw') : 0;
        
        wp_enqueue_script('paytrail-op-lasku');
        wp_enqueue_script('paytrail-op-lasku-helper');
        wp_enqueue_style('paytrail-op-lasku');
        
        wp_localize_script('paytrail-op-lasku', 'op_lasku_data', array(
            'cart_total' => esc_js($this->cart_total),
            'language' => esc_js($this->language),
        ));

        echo wp_kses_post('<div id="op-lasku--init"></div>');
    }

    /**
     * Title for settings page
     */
    public static function settings_title()
    {
        $icon_url = sprintf('%s/assets/img/icon_oplasku_admin.svg', Plugin::plugin_url());
        return sprintf(
            __('OP Invoice', 'paytrail-for-woocommerce') . '<br><img src="%s" alt="OP Invoice calculator" style="width: 70px; height: auto; margin-top: 5px;">',
            esc_url($icon_url)
        );
    }
}
