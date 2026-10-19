<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

/** The answers the WooCommerce Subscriptions stand-ins in tests/_subscriptions-fakes.php give. */
final class SubscriptionsRegistry {

	/**
	 * The cart states the plugin asks about. `subscription` backs
	 * `WC_Subscriptions_Cart::cart_contains_subscription()`, `renewal` backs
	 * `wcs_cart_contains_renewal()`.
	 */
	public const CART_FLAGS = [
		'subscription',
		'renewal',
	];

	/**
	 * Registered subscriptions and what they are linked to.
	 *
	 * @var array<int, array{parent: int|null, renewals: array<int, int>}>
	 */
	private static $subscriptions = [];

	/**
	 * Which cart-state calls answer true.
	 *
	 * @var array<string, bool>
	 */
	private static $cart_flags = [];

	/** Static only. */
	private function __construct() {}

	/** Forgets every registered subscription and cart flag. */
	public static function reset(): void {
		self::$subscriptions = [];
		self::$cart_flags    = [];
	}

	/** Registers an order id as a subscription. */
	public static function register( int $subscription_id, ?int $parent_id = null ): void {
		$renewals = self::$subscriptions[ $subscription_id ]['renewals'] ?? [];

		self::$subscriptions[ $subscription_id ] = [
			'parent'   => $parent_id,
			'renewals' => $renewals,
		];
	}

	/** Links a renewal order to a subscription. */
	public static function addRenewalOrder( int $subscription_id, int $renewal_order_id ): void {
		if ( ! isset( self::$subscriptions[ $subscription_id ] ) ) {
			self::register( $subscription_id );
		}

		self::$subscriptions[ $subscription_id ]['renewals'][] = $renewal_order_id;
	}

	/** Sets one of the cart-state answers. */
	public static function setCartFlag( string $flag, bool $value = true ): void {
		self::$cart_flags[ $flag ] = $value;
	}

	/** What the named cart-state check should answer. */
	public static function cartContains( string $flag ): bool {
		return self::$cart_flags[ $flag ] ?? false;
	}

	/** Backs `wcs_is_subscription()`. */
	public static function isSubscription( $order ): bool {
		return isset( self::$subscriptions[ self::idOf( $order ) ] );
	}

	/** Backs `wcs_get_subscription()`. */
	public static function get( $subscription ) {
		$id = self::idOf( $subscription );

		if ( ! isset( self::$subscriptions[ $id ] ) ) {
			return false;
		}

		return wc_get_order( $id );
	}

	/** Every registered subscription, which is what `wcs_get_subscriptions()` returns. */
	public static function all(): array {
		return self::resolve( array_keys( self::$subscriptions ) );
	}

	/** Backs `wcs_get_subscriptions_for_order()`. */
	public static function forOrder( $order, array $args = [] ): array {
		$types = (array) ( $args['order_type'] ?? [ 'parent' ] );
		$id    = self::idOf( $order );

		if ( 0 === $id ) {
			return [];
		}

		$any     = in_array( 'any', $types, true );
		$matched = [];

		foreach ( self::$subscriptions as $subscription_id => $links ) {
			$as_parent  = ( $any || in_array( 'parent', $types, true ) ) && $links['parent'] === $id;
			$as_renewal = ( $any || in_array( 'renewal', $types, true ) ) && in_array( $id, $links['renewals'], true );

			if ( $as_parent || $as_renewal ) {
				$matched[] = $subscription_id;
			}
		}

		return self::resolve( $matched );
	}

	/** Backs `wcs_get_subscriptions_for_renewal_order()`. */
	public static function forRenewalOrder( $order ): array {
		return self::forOrder( $order, [ 'order_type' => 'renewal' ] );
	}

	/** Backs `wcs_order_contains_subscription()`. */
	public static function orderContains( $order, $order_type = [ 'parent', 'resubscribe', 'switch' ] ): bool {
		return [] !== self::forOrder( $order, [ 'order_type' => (array) $order_type ] );
	}

	/** The order id behind an order, an id, or something else entirely. */
	private static function idOf( $order ): int {
		if ( $order instanceof \WC_Order ) {
			return $order->get_id();
		}

		return is_numeric( $order ) ? (int) $order : 0;
	}

	/** Loads the given subscription ids, keyed by id as Subscriptions returns them. */
	private static function resolve( array $ids ): array {
		$subscriptions = [];

		foreach ( $ids as $id ) {
			$subscription = wc_get_order( $id );

			if ( $subscription ) {
				$subscriptions[ $id ] = $subscription;
			}
		}

		return $subscriptions;
	}
}
