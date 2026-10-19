<?php
/*
 * WooCommerce Subscriptions stand-ins, required by the Integration and Harness suites.
 *
 * Declaring these makes the plugin believe Subscriptions is installed for the whole run.
 * The per-test switch lives in SubscriptionsRegistry instead, which answers false until
 * a test registers something through `CanFakeSubscriptions`.
 */

use Tests\Support\Fakes\SubscriptionOrder;
use Tests\Support\Fakes\SubscriptionsRegistry;

if ( ! class_exists( 'WC_Subscription' ) ) {
	class WC_Subscription extends SubscriptionOrder {}
}

if ( ! class_exists( 'WC_Subscriptions_Cart' ) ) {
	class WC_Subscriptions_Cart {

		public static function cart_contains_subscription() {
			return SubscriptionsRegistry::cartContains( 'subscription' );
		}
	}
}

// Only its existence is checked, by `Helper::getIsSubscriptionsEnabled()`.
if ( ! class_exists( 'WC_Subscriptions_Change_Payment_Gateway' ) ) {
	class WC_Subscriptions_Change_Payment_Gateway {}
}

// The prefix is the real one, so a test can set the manual renewals option
// `Helper::getIsSubscriptionsEnabled()` bails out on.
if ( ! class_exists( 'WC_Subscriptions_Admin' ) ) {
	class WC_Subscriptions_Admin {

		public static $option_prefix = 'woocommerce_subscriptions';
	}
}

if ( ! function_exists( 'wcs_is_subscription' ) ) {
	function wcs_is_subscription( $order ) {
		return SubscriptionsRegistry::isSubscription( $order );
	}
}

if ( ! function_exists( 'wcs_get_subscription' ) ) {
	function wcs_get_subscription( $subscription ) {
		return SubscriptionsRegistry::get( $subscription );
	}
}

if ( ! function_exists( 'wcs_get_subscriptions' ) ) {
	function wcs_get_subscriptions( $args = [] ) {
		return SubscriptionsRegistry::all();
	}
}

if ( ! function_exists( 'wcs_get_subscriptions_for_order' ) ) {
	function wcs_get_subscriptions_for_order( $order, $args = [] ) {
		return SubscriptionsRegistry::forOrder( $order, (array) $args );
	}
}

if ( ! function_exists( 'wcs_get_subscriptions_for_renewal_order' ) ) {
	function wcs_get_subscriptions_for_renewal_order( $order ) {
		return SubscriptionsRegistry::forRenewalOrder( $order );
	}
}

if ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
	function wcs_order_contains_subscription( $order, $order_type = [ 'parent', 'resubscribe', 'switch' ] ) {
		return SubscriptionsRegistry::orderContains( $order, $order_type );
	}
}

if ( ! function_exists( 'wcs_cart_contains_renewal' ) ) {
	function wcs_cart_contains_renewal() {
		return SubscriptionsRegistry::cartContains( 'renewal' );
	}
}
