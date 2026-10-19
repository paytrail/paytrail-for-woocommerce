<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use Facebook\WebDriver\Exception\WebDriverException;
use PHPUnit\Framework\Assert;

/**
 * The admin half of an order's life: drive the WooCommerce order screen, then read back
 * what the plugin and WooCommerce made of it.
 *
 * Sibling of CanDriveE2ECheckout, which buys the order these steps then manage.
 */
trait CanDriveE2EOrderManagement {

	use \Tests\Support\_generated\EndToEndTesterActions;

	/** How long a save that talks to Paytrail may take, in seconds. */
	private const ADMIN_SAVE_TIMEOUT = 90;

	/** Whether this test has already moved the browser to wp-admin and logged in. */
	private bool $inOrderAdmin = false;

	/** Opens the order edit screen, logging in once per test. */
	public function amEditingOrder( int $orderId ): void {
		if ( ! $this->inOrderAdmin ) {
			$this->loginAsAdmin();

			$this->inOrderAdmin = true;
		}

		$this->waitOutOrderScreen( fn () => $this->amOnAdminPage( "post.php?post={$orderId}&action=edit" ) );
	}

	/** Moves the order to a status and waits out whatever it makes the plugin send. */
	public function changeOrderStatusTo( string $status ): void {
		$this->selectOption( '#order_status', "wc-{$status}" );

		// Out from under the admin bar, which otherwise swallows the click.
		$this->scrollTo( 'button.save_order', 0, -120 );
		$this->waitOutOrderScreen( fn () => $this->click( 'button.save_order' ) );
	}

	/** The order's status as WooCommerce stored it. */
	public function seeOrderStatusIs( int $orderId, string $status ): void {
		$this->seeInDatabase(
			'wp_posts',
			[
				'ID'          => $orderId,
				'post_status' => $status,
			]
		);
	}

	/** Asserts every needle appears somewhere in the order's notes. */
	public function seeOrderNotes( int $orderId, array $needles ): void {
		$notes = $this->grabOrderNotes( $orderId );

		foreach ( $needles as $needle ) {
			Assert::assertStringContainsString( $needle, $notes, "Order {$orderId} notes:\n{$notes}" );
		}
	}

	/** Asserts no needle appears in the order's notes, ignoring case. */
	public function dontSeeOrderNotes( int $orderId, array $needles ): void {
		$notes = $this->grabOrderNotes( $orderId );

		foreach ( $needles as $needle ) {
			Assert::assertStringNotContainsStringIgnoringCase( $needle, $notes, "Order {$orderId} notes:\n{$notes}" );
		}
	}

	/** Asserts post meta, where a null expected value means "any non-empty value". */
	public function seeOrderMeta( int $orderId, array $expected ): void {
		foreach ( $expected as $meta_key => $meta_value ) {
			// Read back rather than asserted, so a failure reports what was written.
			$actual = $this->grabFromDatabase(
				'wp_postmeta',
				'meta_value',
				[
					'post_id'  => $orderId,
					'meta_key' => $meta_key,
				]
			);

			if ( $meta_value === null ) {
				Assert::assertNotEmpty( $actual, "Order {$orderId} has no {$meta_key}." );
				continue;
			}

			Assert::assertSame(
				$meta_value,
				$actual,
				"Order {$orderId} has {$meta_key} = " . var_export( $actual, true )
					. ', expected ' . var_export( $meta_value, true )
			);
		}
	}

	/**
	 * Loads an order screen and waits for it, tolerating a page load that outruns the
	 * driver's budget: saving one can be an API round trip before the response starts.
	 */
	private function waitOutOrderScreen( callable $navigate ): void {
		try {
			$navigate();
		} catch ( WebDriverException $e ) {
			$this->comment( 'paytrail: the order screen outran the page load timeout, reading it anyway' );
		}

		$this->waitForElement( '#woocommerce-order-items', self::ADMIN_SAVE_TIMEOUT );
	}

	/** Every note on the order as one string, so a failure prints all of them. */
	private function grabOrderNotes( int $orderId ): string {
		return implode(
			"\n",
			$this->grabColumnFromDatabase(
				'wp_comments',
				'comment_content',
				[
					'comment_post_ID' => $orderId,
					'comment_type'    => 'order_note',
				]
			)
		);
	}
}
