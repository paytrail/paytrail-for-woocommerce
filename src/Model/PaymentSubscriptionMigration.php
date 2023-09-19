<?php

namespace Paytrail\WooCommercePaymentGateway\Model;

use Paytrail\WooCommercePaymentGateway\Api\MigrationInterface;

class PaymentSubscriptionMigration implements MigrationInterface {

	/**
	 * Subscriptions
	 *
	 * @var array
	 */
	protected $subscriptions;

	public function __construct() {
		if (function_exists('wcs_get_subscriptions')) {
			$this->subscriptions = wcs_get_subscriptions(
				['subscriptions_per_page' => -1,
					'meta_query' => [
						[
							'key'     => '_payment_method',
							'value'   => 'checkout_finland',
							'compare' => '='
						]
					]
				]
			);
		}
	}

	/**
	 * Executes migration for Subscriptions
	 */
	public function execute() {
		if (empty($this->subscriptions)) {
			return;
		}
		foreach ($this->subscriptions as $subscription) {
			$subscription->set_payment_method('paytrail');
			$subscription->set_payment_method_title('Paytrail for Woocommerce');
			$subscription->save();
		}
	}
}
