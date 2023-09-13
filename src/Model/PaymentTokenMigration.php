<?php

namespace Paytrail\WooCommercePaymentGateway\Model;

use Paytrail\WooCommercePaymentGateway\Api\MigrationInterface;

class PaymentTokenMigration implements MigrationInterface {

	/**
	 * Array of payment tokens.
	 *
	 * @var \WC_Payment_Token[]
	 */
	protected $tokens;

	public function __construct() {
		$this->tokens = \WC_Payment_Tokens::get_tokens(['gateway_id' => 'checkout_finland']);
	}

	/**
	 * Executes migration for subscriptions
	 */
	public function execute() {
		if (empty($this->tokens)) {
			return;
		}
		foreach ($this->tokens as $token) {
			$token->set_gateway_id('paytrail');
			$token->save();
		}
	}
}
