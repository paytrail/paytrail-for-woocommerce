<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use PHPUnit\Framework\Assert;

/**
 * The browser half of a purchase: shape the store and the cart, then drive the checkout
 * out to Paytrail and back. Sibling of CanDriveCheckout, which cans the API's answers
 * for the Integration suite.
 */
trait CanDriveE2ECheckout {

	use \Tests\Support\_generated\EndToEndTesterActions;

	/** The billing address every checkout starts from. */
	public const BILLING_ADDRESS = [
		'first_name' => 'Matti',
		'last_name'  => 'Meikalainen',
		'address_1'  => 'Mannerheimintie 12',
		'city'       => 'Helsinki',
		'postcode'   => '00100',
		'country'    => 'FI',
		'phone'      => '0401234567',
		'email'      => 'matti@example.com',
	];

	/**
	 * The provider a purchase is paid with. Nordea's test page is a plain form with a
	 * confirm button, so it is the one a test can finish without an app.
	 */
	public const DEFAULT_PROVIDER = 'nordea';

	/** The plugin's provider list on the checkout page. */
	private const PROVIDER_GROUP = '.paytrail-provider-group';

	/** The form the order-pay page posts to the provider, which its own inline script submits. */
	private const REDIRECT_FORM = '#checkout-redirect-form';

	/** How long Paytrail's own pages may take, in seconds. */
	private const PAYTRAIL_TIMEOUT = 60;

	/** How many times a provider screen may be advanced before giving up. */
	private const PROVIDER_STEPS = 6;

	/** Sets the store options a scenario needs, keyed by option name. */
	public function haveStoreOptionsInDatabase( array $options ): void {
		foreach ( $options as $name => $value ) {
			$this->haveOptionInDatabase( $name, $value );
		}
	}

	/** Creates the tax classes and rates a scenario needs. */
	public function haveTaxClassesInDatabase( array $rates ): void {
		foreach ( $rates as $rate ) {
			$this->haveTaxClassInDatabase( $rate );
		}
	}

	/**
	 * Creates the products a scenario needs and adds them to the cart. An item is a SKU
	 * from TestProducts or `[ SKU, quantity ]`.
	 *
	 * @return array<string, int> The created product ids, keyed by SKU.
	 */
	public function haveCartWith( array $items ): array {
		$product_ids = [];

		foreach ( $items as $item ) {
			[ $sku, $quantity ] = is_array( $item ) ? [ $item[0], $item[1] ?? 1 ] : [ $item, 1 ];

			$product_ids[ $sku ] = $this->haveProductInDatabase( $sku );

			// WooCommerce's own add-to-cart, which leaves the cart in the session the
			// checkout reads. Doing it over wc-ajax builds a second cart instead.
			$this->amOnPage( "/?add-to-cart={$product_ids[ $sku ]}&quantity={$quantity}" );
		}

		return $product_ids;
	}

	/** Opens the WooCommerce checkout page. */
	public function amOnCheckoutPage(): void {
		$this->amOnPage( '/checkout/' );
	}

	/**
	 * Opens the checkout and waits for the plugin's provider list, which is the gateway
	 * availability assertion: the list is Paytrail's answer having arrived.
	 */
	public function amOnCheckoutPageWithGateway( int $timeout = 30 ): void {
		$this->amOnCheckoutPage();
		$this->waitForElement( '#payment_method_paytrail', $timeout );
		$this->waitForElement( self::PROVIDER_GROUP, $timeout );
	}

	/** Fills WooCommerce's own billing form. */
	public function fillBillingAddressForm( array $overrides = [] ): void {
		$address = array_replace( self::BILLING_ADDRESS, $overrides );

		$this->waitForCheckoutReady();

		// Written straight onto the fields: the country is a select2, whose visible
		// input is not the one WooCommerce posts.
		$this->executeJS(
			'const values = ' . json_encode( $address ) . ';'
			. ' for (const key in values) {'
			. '   const el = document.getElementById("billing_" + key);'
			. '   if (!el) { continue; }'
			. '   el.value = values[key];'
			. '   el.dispatchEvent(new Event("input", { bubbles: true }));'
			. '   el.dispatchEvent(new Event("change", { bubbles: true }));'
			. ' }'
			. ' jQuery(document.body).trigger("update_checkout");'
		);

		$this->waitForCheckoutReady();
	}

	/**
	 * Picks a payment provider from the plugin's list. The list is collapsed until its
	 * group is opened, so the radio is checked rather than clicked.
	 */
	public function choosePaymentProvider( string $provider = self::DEFAULT_PROVIDER ): void {
		$this->selectPaytrailGateway();

		$chosen = $this->executeJS(
			'const wanted = ' . json_encode( $provider ) . ';'
			. ' const radios = Array.from(document.querySelectorAll(\'input[name="payment_provider"]\'));'
			. ' const radio = radios.find(r => r.value === wanted) || radios[0];'
			. ' if (!radio) { return null; }'
			. ' radio.checked = true;'
			. ' radio.dispatchEvent(new Event("change", { bubbles: true }));'
			. ' return radio.value;'
		);

		Assert::assertNotEmpty(
			$chosen,
			"Paytrail offered no payment providers, so '{$provider}' could not be chosen."
		);
	}

	/** Selects Paytrail as the payment method and waits for the checkout to settle. */
	public function selectPaytrailGateway(): void {
		$this->executeJS(
			'const radio = document.getElementById("payment_method_paytrail");'
			. ' if (radio && !radio.checked) {'
			. '   radio.checked = true;'
			. '   jQuery(radio).trigger("click").trigger("change");'
			. ' }'
		);

		$this->waitForCheckoutReady();
	}

	/** Waits for the checkout to settle after any pending `update_checkout` call. */
	public function waitForCheckoutReady( int $timeout = 30 ): void {
		$this->waitForJS(
			"return typeof jQuery !== 'undefined'"
			. ' && jQuery.active === 0'
			. " && !document.querySelector('form.checkout .blockUI');",
			$timeout
		);
	}

	/**
	 * Places the order and follows it out of the store: the order-pay page posts itself
	 * to the provider, so the browser ends up on Paytrail's own page.
	 */
	public function placeOrder(): void {
		$this->waitForCheckoutReady();
		$this->acceptTheTerms();
		$this->executeJS( 'document.getElementById("place_order").click();' );

		$this->waitForJS(
			"return location.pathname.indexOf('/checkout/') === -1"
			. " || location.pathname.indexOf('order-pay') !== -1"
			. " || location.pathname.indexOf('order-received') !== -1;",
			self::PAYTRAIL_TIMEOUT
		);

		$this->leaveTheOrderPayPage();
	}

	/**
	 * Pays on whatever Paytrail put in front of the shopper. Not a scripted sequence:
	 * Paytrail decides how many screens its test provider shows, so each pass is the
	 * same move, confirm and look again.
	 */
	public function payAtProvider(): void {
		for ( $step = 0; $step < self::PROVIDER_STEPS; $step++ ) {
			if ( $this->isBackOnTheStore() ) {
				return;
			}

			if ( ! $this->confirmOnProviderScreen() ) {
				break;
			}

			$this->waitForPageToSettle();
		}

		if ( ! $this->isBackOnTheStore() ) {
			Assert::fail(
				'Paytrail did not send the shopper back to the store. Stopped on '
				. (string) $this->executeJS( 'return location.href;' )
			);
		}
	}

	/** Waits for the browser to land on the thank you page. */
	public function waitForThankYouPage( int $timeout = self::PAYTRAIL_TIMEOUT ): void {
		$this->waitForJS( "return location.pathname.indexOf('order-received') !== -1;", $timeout );
	}

	/** The order the thank you page belongs to, asserted to exist before it is returned. */
	public function grabOrderIdFromThankYouPage(): int {
		$order_id = $this->grabFromCurrentUrl( '/\/checkout\/order-received\/(\d+)\//' );

		if ( $order_id === null ) {
			Assert::fail( 'Could not extract the order id from ' . (string) $this->executeJS( 'return location.href;' ) );
		}

		$this->seeInDatabase(
			'wp_posts',
			[
				'ID'        => $order_id,
				'post_type' => 'shop_order',
			]
		);

		return (int) $order_id;
	}

	/** Verifies a WooCommerce order once we are on the thank you page. */
	public function verifyOrderOnThankYouPage( string $paymentMethod, string $orderTotal, array $expectedMeta = [] ): void {
		$order_id = $this->grabOrderIdFromThankYouPage();

		$expectedMeta = array_merge(
			[
				'_payment_method' => $paymentMethod,
				'_order_total'    => $orderTotal,
			],
			$expectedMeta
		);

		$this->seeOrderMeta( $order_id, $expectedMeta );
	}

	/** Ticks the terms checkbox, which the store shows because it has a terms page. */
	private function acceptTheTerms(): void {
		$this->executeJS(
			'const terms = document.getElementById("terms");'
			. ' if (terms && !terms.checked) {'
			. '   terms.checked = true;'
			. '   terms.dispatchEvent(new Event("change", { bubbles: true }));'
			. ' }'
		);
	}

	/** Waits out the order-pay page, whose inline script posts the provider form for us. */
	private function leaveTheOrderPayPage(): void {
		if ( ! $this->isOnTheOrderPayPage() ) {
			return;
		}

		// The form is rendered and submitted in the same response, so it is often gone
		// before the driver looks. Submitting again is harmless, not finding it is not.
		$this->waitForJS(
			"return !!document.querySelector('" . self::REDIRECT_FORM . "')"
			. " || location.pathname.indexOf('order-pay') === -1;",
			self::PAYTRAIL_TIMEOUT
		);

		$this->executeJS(
			'const form = document.querySelector("' . self::REDIRECT_FORM . '");'
			. ' if (form) { form.submit(); }'
		);

		$this->waitForJS( "return location.pathname.indexOf('order-pay') === -1;", self::PAYTRAIL_TIMEOUT );
	}

	private function isOnTheOrderPayPage(): bool {
		return (bool) $this->executeJS( "return location.pathname.indexOf('order-pay') !== -1;" );
	}

	/** Whether the browser is back on a page the store serves. */
	private function isBackOnTheStore(): bool {
		$local = rtrim( (string) ( $_ENV['WORDPRESS_URL'] ?? '' ), '/' );

		return (bool) $this->executeJS(
			'return location.href.indexOf(' . json_encode( $local ) . ') === 0;'
		);
	}

	/**
	 * Makes the one move that gets past Paytrail's current screen: the button that
	 * carries the payment onwards rather than back.
	 */
	private function confirmOnProviderScreen(): bool {
		return (bool) $this->executeJS(
			<<<'JS'
			const visible = el => {
				const rect = el.getBoundingClientRect();
				if (rect.width === 0 && rect.height === 0) { return false; }
				const style = getComputedStyle(el);
				return style.visibility !== 'hidden' && style.display !== 'none';
			};
			const label = el => ((el.innerText || el.value || el.getAttribute('aria-label') || '') + '')
				.replace(/\s+/g, ' ')
				.trim();
			const away = /cancel|back|peruuta|palaa|avbryt/i;
			const onward = /ok|continue|confirm|pay|submit|jatka|maksa|hyv[aä]ksy|vahvista|siirry|forts[aä]tt/i;
			const buttons = Array.from(
				document.querySelectorAll('button, input[type="submit"], input[type="button"], a.button, [role="button"]')
			).filter(visible).filter(el => !away.test(label(el)));

			const target = buttons.find(el => onward.test(label(el))) || buttons[0];
			if (!target) { return false; }
			target.click();
			return true;
			JS
		);
	}

	/** Waits for whatever the last click started to finish loading. */
	private function waitForPageToSettle(): void {
		try {
			$this->waitForJS( "return document.readyState === 'complete';", self::PAYTRAIL_TIMEOUT );
		} catch ( \Throwable $e ) {
			// A screen that never reports complete is still worth looking at.
			$this->comment( 'paytrail: the provider screen did not finish loading, reading it anyway' );
		}
	}
}
