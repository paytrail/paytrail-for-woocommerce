<?php
/**
 * Paytrail for Woocommerce payment Helper class
 */

namespace Paytrail\WooCommercePaymentGateway;

use Exception;
use LogicException;

/**
 * Helper class for shared payment related utilities.
 */
class Helper {

	/**
	 * Check is subscriptions enabled
	 *
	 * @return bool
	 */
	public static function getIsSubscriptionsEnabled() {
		if ( ! class_exists( 'WC_Subscriptions_Cart' ) ) {
			return false;
		}
		if ( ! class_exists( 'WC_Subscriptions_Change_Payment_Gateway' ) ) {
			return false;
		}
		if ( ! function_exists( 'wcs_cart_contains_renewal' ) ) {
			return false;
		}
		if ( class_exists( '\WC_Subscriptions_Admin' ) ) {
			$accept_manual_renewals = ( 'no' !== get_option( \WC_Subscriptions_Admin::$option_prefix . '_accept_manual_renewals', 'no' ) );
			if ( true === $accept_manual_renewals ) {
				return false;
			}
		}

		return (
			\WC_Subscriptions_Cart::cart_contains_subscription() ||
			wcs_cart_contains_renewal() ||
			filter_input( INPUT_GET, 'change_payment_method' )
		);
	}

	/**
	 * Get the change_payment_method query arg, set when a subscription's payment method is being changed.
	 *
	 * @return string|null The query arg value, or null when it is not present.
	 */
	public static function getIsChangeSubscriptionPaymentMethod() {
		return filter_input( INPUT_GET, 'change_payment_method' );
	}

	/**
	 * Currency specific formattings
	 *
	 * @param int|double $sum The sum to format.
	 * @return integer
	 */
	public function handle_currency( $sum ) {
		return round( $sum * 100 );
	}

	/**
	 * Get current WooCommerce cart total.
	 *
	 * @return integer
	 */
	public function get_cart_total() {
		$sum = WC()->cart->total;

		return $this->handle_currency( $sum );
	}

	/**
	 * Map the WordPress locale to a locale supported by the Paytrail API.
	 *
	 * @return string
	 */
	public static function getLocale() {
		$full_locale = get_locale();

		$short_locale = substr( $full_locale, 0, 2 );

		// Get and assign the WordPress locale.
		switch ( $short_locale ) {
			case 'sv':
				$locale = 'SV';
				break;
			case 'fi':
				$locale = 'FI';
				break;
			default:
				$locale = 'EN';
				break;
		}
		return $locale;
	}

	/**
	 * Generate a unique id for use as payment item stamp
	 *
	 * @param int|string $order_id The order ID.
	 *
	 * @return string
	 */
	public function generate_item_stamp( $order_id ) {
		return uniqid( $order_id . '-', true );
	}
}
