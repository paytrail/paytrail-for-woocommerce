<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use PHPUnit\Framework\Assert;

/**
 * The block checkout half of a purchase, composed alongside CanDriveE2ECheckout, whose
 * cart and post-checkout steps it reuses. The fields are React controlled, and the
 * provider radios are not in the DOM at all until their group is opened.
 */
trait CanDriveE2EBlockCheckout {

	use \Tests\Support\_generated\EndToEndTesterActions;

	/** Paytrail's radio in the block checkout's payment method list. */
	private const BLOCK_GATEWAY_RADIO = '#radio-control-wc-payment-method-options-paytrail';

	/** The block checkout's own place order button. */
	private const BLOCK_PLACE_ORDER = '.wc-block-components-checkout-place-order-button';

	/** The modifier the button carries while the checkout is busy. */
	private const BLOCK_PLACE_ORDER_LOADING = 'wc-block-components-checkout-place-order-button--loading';

	/** How long the block checkout may take to settle a Store API round trip, in seconds. */
	private const BLOCK_TIMEOUT = 30;

	/**
	 * Puts the checkout block on the checkout page, in place of the shortcode the test
	 * store is installed with. WPDb reloads the dump before every test, so this is
	 * undone again without a teardown.
	 */
	public function haveBlockCheckoutPage(): void {
		$page_id = (int) $this->grabFromDatabase(
			'wp_options',
			'option_value',
			[ 'option_name' => 'woocommerce_checkout_page_id' ]
		);

		Assert::assertGreaterThan( 0, $page_id, 'The test store has no checkout page to put the block on.' );

		$this->updateInDatabase(
			'wp_posts',
			[ 'post_content' => $this->checkoutBlockContent() ],
			[ 'ID' => $page_id ]
		);
	}

	/**
	 * Opens the block checkout and waits for Paytrail to offer itself. The gateway radio
	 * is the availability assertion, and the provider group is Paytrail's answer having
	 * arrived: between them they are what a broken block build takes away.
	 */
	public function amOnBlockCheckoutPageWithGateway( int $timeout = self::BLOCK_TIMEOUT ): void {
		$this->amOnCheckoutPage();
		$this->waitForElement( self::BLOCK_GATEWAY_RADIO, $timeout );
		$this->selectBlockPaytrailGateway();
		$this->waitForElement( self::PROVIDER_GROUP, $timeout );
	}

	/**
	 * Fills the block checkout's contact and address fields. The country is asserted
	 * rather than set: the store's base country prefills it, and a store that stops
	 * doing that changes what every row here is buying.
	 */
	public function fillBlockBillingAddressForm( array $overrides = [] ): void {
		$address = array_replace( self::BILLING_ADDRESS, $overrides );

		// Left out of the write on purpose: WooCommerce clears the postcode whenever the
		// country field fires a change, so writing it back would empty a field we filled.
		$written = $address;
		unset( $written['country'] );

		$this->waitForBlockCheckoutReady();

		// Through the native setter, which is the one React's own onChange listens to.
		// A plain el.value write is reverted the next time the field renders.
		$this->executeJS(
			'const values = ' . json_encode( $written ) . ';'
			. ' const write = (el, value) => {'
			. '   const proto = el instanceof HTMLSelectElement ? HTMLSelectElement.prototype'
			. '     : el instanceof HTMLTextAreaElement ? HTMLTextAreaElement.prototype'
			. '     : HTMLInputElement.prototype;'
			. '   Object.getOwnPropertyDescriptor(proto, "value").set.call(el, value);'
			. '   el.dispatchEvent(new Event("input", { bubbles: true }));'
			. '   el.dispatchEvent(new Event("change", { bubbles: true }));'
			. '   el.dispatchEvent(new Event("blur", { bubbles: true }));'
			. ' };'
			. ' for (const key in values) {'
			. '   const el = document.getElementById(key === "email" ? "email" : "billing-" + key);'
			. '   if (el) { write(el, values[key]); }'
			. ' }'
		);

		$this->waitForBlockCheckoutReady();

		Assert::assertSame(
			$address['country'],
			(string) $this->executeJS(
				'const el = document.getElementById("billing-country"); return el ? el.value : "";'
			),
			'The block checkout did not prefill the billing country from the store base country.'
		);
	}

	/**
	 * Picks a provider from the plugin's list. Only one group is open at a time and a
	 * closed group renders no radios at all, so the groups are opened in turn until the
	 * wanted provider is among them.
	 */
	public function chooseBlockPaymentProvider( string $provider = self::DEFAULT_PROVIDER ): void {
		$this->selectBlockPaytrailGateway();

		$groups = (int) $this->executeJS(
			'return document.querySelectorAll("' . self::PROVIDER_GROUP . '").length;'
		);

		Assert::assertGreaterThan( 0, $groups, 'Paytrail offered no provider groups in the block checkout.' );

		for ( $index = 0; $index < $groups; $index++ ) {
			$this->executeJS(
				'const groups = document.querySelectorAll("' . self::PROVIDER_GROUP . '");'
				. ' if (groups[' . $index . ']) { groups[' . $index . '].click(); }'
			);

			if ( $this->clickProviderRadio( $provider ) ) {
				$this->waitForBlockCheckoutReady();
				return;
			}
		}

		Assert::fail( "None of Paytrail's provider groups offered '{$provider}' in the block checkout." );
	}

	/**
	 * Places the order and follows it out of the store. The block checkout posts to the
	 * Store API and then redirects the browser itself, so from the order-pay page on it
	 * is the same trip the shortcode checkout makes.
	 */
	public function placeBlockOrder(): void {
		$this->waitForBlockCheckoutReady();
		$this->acceptBlockTerms();

		$this->executeJS(
			'const button = document.querySelector("' . self::BLOCK_PLACE_ORDER . '");'
			. ' if (button) { button.click(); }'
		);

		$this->waitForJS(
			"return location.pathname.indexOf('/checkout/') === -1"
			. " || location.pathname.indexOf('order-pay') !== -1"
			. " || location.pathname.indexOf('order-received') !== -1;",
			self::PAYTRAIL_TIMEOUT
		);

		$this->leaveTheOrderPayPage();
	}

	/**
	 * Waits for the block checkout to settle. The place order button is the signal:
	 * WooCommerce disables it for the whole of a Store API round trip, so it says what
	 * the checkout store would without reaching into wp.data for it.
	 */
	public function waitForBlockCheckoutReady( int $timeout = self::BLOCK_TIMEOUT ): void {
		$this->waitForJS(
			'const button = document.querySelector("' . self::BLOCK_PLACE_ORDER . '");'
			. ' return !!button'
			. ' && !button.disabled'
			. ' && !button.classList.contains("' . self::BLOCK_PLACE_ORDER_LOADING . '")'
			. ' && !document.querySelector(".wc-block-checkout .wc-block-components-spinner");',
			$timeout
		);
	}

	/** Selects Paytrail as the payment method, which is what renders the provider list. */
	private function selectBlockPaytrailGateway(): void {
		// A real click rather than a checked write, so React sees the change.
		$this->executeJS(
			'const radio = document.querySelector("' . self::BLOCK_GATEWAY_RADIO . '");'
			. ' if (radio && !radio.checked) { radio.click(); }'
		);
	}

	/** Clicks the wanted provider's radio if the open group has one. */
	private function clickProviderRadio( string $provider ): bool {
		try {
			$this->waitForJS(
				'return !!document.querySelector('
				. json_encode( 'input[name="payment_provider"][value="' . $provider . '"]' )
				. ');',
				5
			);
		} catch ( \Throwable $e ) {
			return false;
		}

		return (bool) $this->executeJS(
			'const radio = document.querySelector('
			. json_encode( 'input[name="payment_provider"][value="' . $provider . '"]' )
			. ');'
			. ' if (!radio) { return false; }'
			. ' radio.click();'
			. ' return true;'
		);
	}

	/**
	 * Ticks the terms checkbox if the store shows one. The checkout block's terms block
	 * renders plain text by default, so usually there is nothing to tick.
	 */
	private function acceptBlockTerms(): void {
		$this->executeJS(
			'const terms = document.querySelector(".wc-block-checkout__terms input[type=\"checkbox\"]");'
			. ' if (terms && !terms.checked) { terms.click(); }'
		);
	}

	/**
	 * WooCommerce's own checkout block markup, read off WC_Install rather than pinned
	 * here, so the page under test cannot drift from what a real store is given.
	 */
	private function checkoutBlockContent(): string {
		Assert::assertTrue(
			class_exists( \WC_Install::class ),
			'WooCommerce is not loaded, so the checkout block markup cannot be read from it.'
		);

		$method = new \ReflectionMethod( \WC_Install::class, 'get_checkout_block_content' );
		$method->setAccessible( true );

		return (string) $method->invoke( null );
	}
}
