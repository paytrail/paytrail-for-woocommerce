<?php

declare(strict_types=1);

namespace Tests\Harness\Reporting;

use Tests\Support\IntegrationTestCase;
use Tests\Support\Reporting\LogTail;

/**
 * Reading back just the log lines one test produced. Wrong, a failing E2E test is
 * reported with another test's log, or with a run's worth of noise.
 *
 * @covers \Tests\Support\Reporting\LogTail
 */
class LogTailTest extends IntegrationTestCase {

	private string $root;

	protected function setUp(): void {
		parent::setUp();

		$this->root = sys_get_temp_dir() . '/paytrail-logtail-' . bin2hex( random_bytes( 4 ) );
		mkdir( $this->root . '/wp-content/uploads/wc-logs', 0777, true );
	}

	protected function tearDown(): void {
		foreach ( (array) glob( $this->root . '/wp-content/uploads/wc-logs/*' ) as $file ) {
			unlink( (string) $file );
		}
		@unlink( $this->root . '/wp-content/debug.log' );

		parent::tearDown();
	}

	public function test_only_the_lines_written_after_the_mark_are_returned(): void {
		$this->haveDebugLog( "before the test\n" );

		$tail = new LogTail( $this->root );
		$tail->mark();

		$this->haveDebugLog( "before the test\nduring the test\n" );

		$this->assertSame( [ 'debug.log' => "during the test\n" ], $tail->delta() );
	}

	public function test_a_test_that_logged_nothing_has_nothing_to_attach(): void {
		$this->haveDebugLog( "before the test\n" );

		$tail = new LogTail( $this->root );
		$tail->mark();

		$this->assertSame( [], $tail->delta() );
	}

	/** WooCommerce writes one file per handle, and each one is its own attachment. */
	public function test_woocommerce_logs_are_returned_alongside_the_debug_log(): void {
		$tail = new LogTail( $this->root );
		$tail->mark();

		$this->haveDebugLog( "a notice\n" );
		$this->haveWooCommerceLog( 'paytrail-2026-01-01-' . str_repeat( 'a', 32 ) . '.log', "a request\n" );

		$this->assertSame(
			[
				'debug.log'    => "a notice\n",
				'paytrail.log' => "a request\n",
			],
			$tail->delta()
		);
	}

	/** A rotated file is smaller than the mark, so it is read whole rather than skipped. */
	public function test_a_rotated_log_is_read_from_the_start(): void {
		$this->haveDebugLog( str_repeat( "an old line\n", 20 ) );

		$tail = new LogTail( $this->root );
		$tail->mark();

		$this->haveDebugLog( "the only line now\n" );

		$this->assertSame( [ 'debug.log' => "the only line now\n" ], $tail->delta() );
	}

	/** A hook that notices once per request can otherwise produce megabytes of one line. */
	public function test_a_runaway_log_is_capped(): void {
		$tail = new LogTail( $this->root );
		$tail->mark();

		$this->haveDebugLog( str_repeat( 'x', LogTail::MAX_ATTACHMENT_BYTES + 1024 ) );

		$delta = $tail->delta();

		$this->assertLessThan( LogTail::MAX_ATTACHMENT_BYTES + 128, strlen( $delta['debug.log'] ) );
		$this->assertStringContainsString( 'truncated at', $delta['debug.log'] );
	}

	/** Clearing is what keeps one run's logs out of the next one's report. */
	public function test_clearing_empties_the_logs(): void {
		$this->haveDebugLog( "from an earlier run\n" );
		$this->haveWooCommerceLog( 'paytrail-2026-01-01-' . str_repeat( 'a', 32 ) . '.log', "also earlier\n" );

		$tail = new LogTail( $this->root );
		$tail->clear();
		$tail->mark();

		$this->assertSame( '', (string) file_get_contents( $this->root . '/wp-content/debug.log' ) );
		$this->assertSame( [], glob( $this->root . '/wp-content/uploads/wc-logs/*.log' ) );
	}

	private function haveDebugLog( string $contents ): void {
		file_put_contents( $this->root . '/wp-content/debug.log', $contents );
	}

	private function haveWooCommerceLog( string $name, string $contents ): void {
		file_put_contents( $this->root . '/wp-content/uploads/wc-logs/' . $name, $contents );
	}
}
