<?php

namespace Tests\EndToEnd;

use Tests\Support\Data\{TestProducts, TestTaxRates};
use Tests\Support\EndToEndTester;

/**
 * A purchase through the WooCommerce checkout block, which registers the gateway from
 * the plugin's own built block script rather than from PHP.
 */
class BlockCheckoutCest
{
	/**
	 * The same purchase CheckoutCest makes through the shortcode checkout. What is
	 * under test is the block integration around it: a broken block build takes the
	 * gateway off the checkout entirely, which no other test would notice.
	 */
	public function can_purchase_through_the_block_checkout(EndToEndTester $I): void
	{
		$I->haveBlockCheckoutPage();
		$I->haveStoreOptionsInDatabase([ 'woocommerce_prices_include_tax' => 'no' ]);
		$I->haveTaxClassesInDatabase([ TestTaxRates::TAX_RATE_25 ]);
		$I->haveCartWith([ TestProducts::SIMPLE_25 ]);

		$I->amOnBlockCheckoutPageWithGateway();
		$I->fillBlockBillingAddressForm();
		$I->chooseBlockPaymentProvider();

		$I->placeBlockOrder();
		$I->payAtProvider();
		$I->waitForThankYouPage();

		$orderId = $I->grabOrderIdFromThankYouPage();

		// The provider reaches the order through paymentMethodData on the Store API
		// request, not through a POSTed field, so it is the block half worth pinning.
		$I->seeOrderMeta(
			$orderId,
			[
				'_payment_method'            => 'paytrail',
				'_order_total'               => '124.99',
				'_checkout_reference'        => (string) $orderId,
				'_checkout_payment_provider' => EndToEndTester::DEFAULT_PROVIDER,
			]
		);

		$I->seeOrderNotes($orderId, [ 'Payment completed with transaction ID' ]);
		$I->dontSeeOrderNotes($orderId, [ 'Payment failed.' ]);
	}
}
