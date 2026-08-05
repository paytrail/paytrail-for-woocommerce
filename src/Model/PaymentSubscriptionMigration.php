<?php

namespace Paytrail\WooCommercePaymentGateway\Model;

use Paytrail\WooCommercePaymentGateway\Api\MigrationInterface;

/**
 * Migrates subscriptions from the legacy checkout_finland gateway.
 */
class PaymentSubscriptionMigration implements MigrationInterface {

	/**
	 * Subscriptions
	 *
	 * @var array
	 */
	protected $subscriptions;

	/**
	 * Collect the subscriptions still bound to the legacy gateway.
	 */
	public function __construct() {
		if ( function_exists( 'wcs_get_subscriptions' ) ) {
			$this->subscriptions = wcs_get_subscriptions(
				array(
					'subscriptions_per_page' => -1,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- One-off migration lookup, not a request path query.
					'meta_query'             => array(
						array(
							'key'     => '_payment_method',
							'value'   => 'checkout_finland',
							'compare' => '=',
						),
					),
				)
			);
		}
	}

	/**
	 * Executes migration for Subscriptions
	 */
	public function execute() {
		if ( empty( $this->subscriptions ) ) {
			return;
		}
		foreach ( $this->subscriptions as $subscription ) {
			$subscription->set_payment_method( 'paytrail' );
			$subscription->set_payment_method_title( 'Paytrail for Woocommerce' );
			$subscription->save();
		}
	}
}
