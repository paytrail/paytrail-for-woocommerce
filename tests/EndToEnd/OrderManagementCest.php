<?php

namespace Tests\EndToEnd;

use Tests\Support\Data\{TestProducts, TestTaxRates};
use Tests\Support\EndToEndTester;

/**
 * What a finished purchase leaves on the order, and what the order screen does to it.
 *
 * Refunds are not here: Paytrail's published test merchant cannot receive refunds of
 * e-payments, so the Integration suite covers them against the API fake instead.
 */
class OrderManagementCest
{
	/** The notes a refused operation leaves, checked at the end of each lifecycle. */
	private const FAILURE_NOTES = [
		'Something went wrong with the refund',
		'does not support either regular or email refunds',
		'Failed to create token payment',
		'Failed to activate manual invoice',
		'Failed to cancel Klarna invoice',
		'Payment failed.',
	];

	/**
	 * The transaction and reference are what a later callback, and a merchant chasing a
	 * payment in Paytrail's merchant panel, look the order up by.
	 */
	public function a_purchase_records_what_paytrail_paid(EndToEndTester $I): void
	{
		$orderId = $this->purchase($I);

		$I->seeOrderMeta(
			$orderId,
			[
				'_payment_method'            => 'paytrail',
				'_transaction_id'            => null,
				'_checkout_reference'        => (string) $orderId,
				'_checkout_payment_provider' => EndToEndTester::DEFAULT_PROVIDER,
				'_order_total'               => '124.99',
			]
		);

		$I->seeOrderNotes($orderId, [ 'Payment completed with transaction ID' ]);
		$I->dontSeeOrderNotes($orderId, self::FAILURE_NOTES);
	}

	/**
	 * Completing an order that was not paid with a manually activated invoice must not
	 * reach Paytrail, and must not leave the order somewhere a merchant has to rescue.
	 */
	public function completing_a_bank_payment_leaves_it_completed(EndToEndTester $I): void
	{
		$orderId = $this->purchase($I);

		$I->amEditingOrder($orderId);
		$I->changeOrderStatusTo('completed');

		$I->seeOrderStatusIs($orderId, 'wc-completed');
		$I->dontSeeOrderNotes($orderId, self::FAILURE_NOTES);
	}

	/** Buys one 25% VAT product as a Finnish customer and returns the finished order's id. */
	private function purchase(EndToEndTester $I): int
	{
		$I->haveStoreOptionsInDatabase([ 'woocommerce_prices_include_tax' => 'no' ]);
		$I->haveTaxClassesInDatabase([ TestTaxRates::TAX_RATE_25 ]);
		$I->haveCartWith([ TestProducts::SIMPLE_25 ]);

		$I->amOnCheckoutPageWithGateway();
		$I->fillBillingAddressForm();
		$I->choosePaymentProvider();

		$I->placeOrder();
		$I->payAtProvider();
		$I->waitForThankYouPage();

		return $I->grabOrderIdFromThankYouPage();
	}
}
