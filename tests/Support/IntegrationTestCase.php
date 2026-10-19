<?php

declare(strict_types=1);

namespace Tests\Support;

use lucatume\WPBrowser\TestCase\WPTestCase;
use Qameta\Allure\Allure;
use Tests\Support\Reporting\Redactor;
use Tests\Support\Reporting\SecretRegistry;
use Tests\Support\Traits\CanBuildCartsAndOrders;
use Tests\Support\Traits\CanConfigureStore;
use Tests\Support\Traits\CanDriveCheckout;
use Tests\Support\Traits\CanDriveOrderManagement;
use Tests\Support\Traits\CanFakeSubscriptions;
use Tests\Support\Traits\CanInterceptPaytrailApi;
use Tests\Support\Traits\CanManageProducts;
use Tests\Support\Traits\CanSnapshotRequests;

/**
 * Base class for Integration tests. Resets the WooCommerce state a transaction rollback
 * does not cover before and after every test, and keeps the Paytrail SDK off the network.
 */
abstract class IntegrationTestCase extends WPTestCase {

	use CanConfigureStore;
	use CanManageProducts;
	use CanBuildCartsAndOrders;
	use CanInterceptPaytrailApi;
	use CanDriveCheckout;
	use CanDriveOrderManagement;
	use CanFakeSubscriptions;
	use CanSnapshotRequests;

	/** @var \Tests\Support\Reporting\Redactor|null */
	private static $reportRedactor = null;

	/**
	 * Store profile applied after the reset, before each test. One of `fi` (FI/EUR,
	 * 25.5% VAT), `fi-no-tax`, `fi-live` (a real merchant account rather than test mode),
	 * or null for WooCommerce defaults with the gateway unconfigured.
	 */
	protected ?string $storeProfile = null;

	protected function setUp(): void {
		parent::setUp();

		$this->resetStore();
		$this->applyStoreProfile();
		$this->resetApiInterception();
	}

	/** Applies $storeProfile. Fails loud on a typo rather than silently testing another store. */
	private function applyStoreProfile(): void {
		switch ( $this->storeProfile ) {
			case null:
				return;
			case 'fi':
				$this->configureFinnishStore();
				$this->haveGatewaySettings();
				$this->haveCustomerAddress( $this->finnishAddress(), $this->finnishAddress() );
				return;
			case 'fi-no-tax':
				$this->configureStore( [ 'calc_taxes' => false ] );
				$this->haveGatewaySettings();
				$this->haveCustomerAddress( $this->finnishAddress(), $this->finnishAddress() );
				return;
			case 'fi-live':
				$this->configureFinnishStore();
				$this->haveLiveGatewaySettings();
				$this->haveCustomerAddress( $this->finnishAddress(), $this->finnishAddress() );
				return;
			default:
				throw new \InvalidArgumentException( sprintf( 'Unknown store profile "%s".', $this->storeProfile ) );
		}
	}

	protected function tearDown(): void {
		// Before resetApiInterception(), which throws the recording away.
		$this->attachApiRequestsToReport();

		$this->resetStore();
		$this->resetApiInterception();

		parent::tearDown();
	}

	/**
	 * Puts the API traffic this test provoked into the test report, pass or fail, so a
	 * green report doubles as a record of what the plugin actually sends.
	 */
	private function attachApiRequestsToReport(): void {
		$json = $this->describeApiRequestsForReport();

		if ( null === $json ) {
			return;
		}

		Allure::attachment( 'paytrail-requests.json', $json, 'application/json' );
	}

	/** The intercepted requests as scrubbed, pretty-printed JSON. Null when there were none. */
	protected function describeApiRequestsForReport(): ?string {
		$requests = $this->apiRequests();

		if ( empty( $requests ) ) {
			return null;
		}

		$json = wp_json_encode( $requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		if ( ! is_string( $json ) ) {
			return null;
		}

		return $this->reportRedactor()->scrub( $json );
	}

	private function reportRedactor(): Redactor {
		if ( null === self::$reportRedactor ) {
			self::$reportRedactor = SecretRegistry::fromEnvironment();
		}

		return self::$reportRedactor;
	}

	/** Puts the store back to a blank slate. */
	protected function resetStore(): void {
		$this->deleteAllTaxRates();
		$this->haveStorePages();
		$this->emptyCart();
		$this->haveGuestCustomer();
		$this->haveCustomerAddress();
		$this->setGatewaySettings( [] );
		$this->resetGatewaySession();
		$this->resetFakeSubscriptions();
	}

	/**
	 * Reads an order or a refund back from the database.
	 *
	 * @return \WC_Order|\WC_Order_Refund
	 */
	protected function reload( \WC_Abstract_Order $order ) {
		return wc_get_order( $order->get_id() );
	}

	/** The order's status as persisted, not as the test's copy remembers it. */
	protected function statusOf( \WC_Abstract_Order $order ): string {
		return $this->reload( $order )->get_status();
	}

	/**
	 * The order's notes, newest first.
	 *
	 * @return array<int, string>
	 */
	protected function orderNotes( \WC_Abstract_Order $order ): array {
		return array_column( wc_get_order_notes( [ 'order_id' => $order->get_id() ] ), 'content' );
	}

	protected function assertOrderHasNote( \WC_Order $order, string $expected ): void {
		$this->assertNotEmpty(
			$this->orderNotesContaining( $order, $expected ),
			sprintf(
				'No order note containing "%s". Got: %s',
				$expected,
				implode( ' | ', $this->orderNotes( $order ) )
			)
		);
	}

	protected function assertOrderHasNoNote( \WC_Order $order, string $unexpected ): void {
		$this->assertSame(
			[],
			$this->orderNotesContaining( $order, $unexpected ),
			sprintf( 'Expected no order note containing "%s".', $unexpected )
		);
	}

	/** @return array<int, string> */
	private function orderNotesContaining( \WC_Order $order, string $text ): array {
		return array_values(
			array_filter(
				$this->orderNotes( $order ),
				static function ( $note ) use ( $text ) {
					return false !== strpos( $note, $text );
				}
			)
		);
	}

	/**
	 * A numeric code comes back as an int, because WP_Error keys its errors by it.
	 *
	 * @param string|int $expected_code The code to expect.
	 * @param mixed      $result        The value to check.
	 */
	protected function assertWpErrorCode( $expected_code, $result ): void {
		$this->assertInstanceOf(
			\WP_Error::class,
			$result,
			sprintf( 'Expected a WP_Error with code "%s".', $expected_code )
		);
		$this->assertSame( $expected_code, $result->get_error_code() );
	}

	/** The first item of the given product code in a request body's `items`. */
	protected function findItem( array $items, string $product_code ): ?array {
		foreach ( $items as $item ) {
			if ( ( $item['productCode'] ?? null ) === $product_code ) {
				return $item;
			}
		}

		return null;
	}

	/** Asserts an item with the given product code exists, and returns it. */
	protected function assertHasItem( array $items, string $product_code ): array {
		$item = $this->findItem( $items, $product_code );

		$this->assertNotNull(
			$item,
			sprintf(
				'Expected an item with product code "%s", got: %s',
				$product_code,
				implode( ', ', array_column( $items, 'productCode' ) )
			)
		);

		return $item;
	}

	/**
	 * Asserts a payment body's items add up to its amount, the rule Paytrail rejects a
	 * payment on and the plugin's rounding row exists to satisfy.
	 */
	protected function assertItemsAddUp( array $body ): void {
		$total = array_sum(
			array_map(
				static function ( $item ) {
					return $item['unitPrice'] * $item['units'];
				},
				$body['items'] ?? []
			)
		);

		$this->assertSame(
			$body['amount'],
			$total,
			sprintf( 'The items total %d, but the payment amount is %d.', $total, $body['amount'] )
		);
	}
}
