<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use Tests\Support\Reporting\Redactor;
use Tests\Support\Reporting\SecretRegistry;

/**
 * Pins an outgoing Paytrail request against a committed JSON fixture. Run with
 * `UPDATE_SNAPSHOTS=1` to rewrite the fixtures, then review the diff.
 */
trait CanSnapshotRequests {

	/**
	 * Asserts the request's method, endpoint and body match `Data/snapshots/<name>.json`.
	 *
	 * @param array                 $request      A request from apiRequestTo().
	 * @param string                $name         Fixture name, without the extension.
	 * @param array<string, scalar> $placeholders Placeholder token => volatile value.
	 */
	protected function assertRequestMatchesSnapshot( array $request, string $name, array $placeholders = [] ): void {
		$this->assertMatchesSnapshot(
			[
				'method' => $request['method'],
				'path'   => $request['uri'],
				'query'  => $request['query'],
				'body'   => $request['json'],
			],
			$name,
			$placeholders
		);
	}

	/**
	 * Asserts a payment request matches its snapshot, with the ids that change on every
	 * run masked out: the stamps structurally, the order id and key by value.
	 *
	 * @param array<string, scalar> $placeholders Extra placeholder token => volatile value.
	 */
	protected function assertPaymentMatchesSnapshot( array $request, string $name, \WC_Order $order, array $placeholders = [] ): void {
		$request['json'] = $this->maskStamps( $request['json'] );

		$this->assertRequestMatchesSnapshot(
			$request,
			$name,
			array_merge(
				[
					'<order-id>'  => $order->get_id(),
					'<order-key>' => $order->get_order_key(),
				],
				$placeholders
			)
		);
	}

	/**
	 * Blanks the stamps, which carry a timestamp and the order item ids. Done by key
	 * rather than by value, because an item stamp is a small integer that would
	 * otherwise be substituted wherever else it happened to appear.
	 */
	private function maskStamps( ?array $body ): ?array {
		if ( null === $body ) {
			return null;
		}

		if ( isset( $body['stamp'] ) ) {
			$body['stamp'] = '<stamp>';
		}

		foreach ( $body['items'] ?? [] as $index => $item ) {
			if ( isset( $item['stamp'] ) ) {
				$body['items'][ $index ]['stamp'] = '<item-stamp>';
			}
		}

		return $body;
	}

	/**
	 * Asserts an array matches `Data/snapshots/<name>.json`, with environment-specific
	 * values masked out first.
	 *
	 * @param array<string, scalar> $placeholders Placeholder token => volatile value.
	 */
	protected function assertMatchesSnapshot( array $actual, string $name, array $placeholders = [] ): void {
		$json   = (string) wp_json_encode( $actual, JSON_UNESCAPED_SLASHES );
		$actual = json_decode( $this->maskSnapshotValues( $json, $placeholders ), true );
		$path   = $this->snapshotPath( $name );

		if ( getenv( 'UPDATE_SNAPSHOTS' ) ) {
			$this->writeSnapshot( $path, $actual );
			$this->addToAssertionCount( 1 );
			return;
		}

		$this->assertFileExists( $path, sprintf( 'Missing snapshot "%s". Re-run with UPDATE_SNAPSHOTS=1 to create it.', $name ) );
		$this->assertSame(
			json_decode( (string) file_get_contents( $path ), true ),
			$actual,
			sprintf( 'Does not match snapshot "%s". Re-run with UPDATE_SNAPSHOTS=1 if the change is intended.', $name )
		);
	}

	/** Swaps out the site URL, the caller's volatile ids and any known secret. */
	private function maskSnapshotValues( string $text, array $placeholders ): string {
		foreach ( $placeholders as $token => $value ) {
			$value = (string) $value;

			if ( '' === $value || '0' === $value ) {
				continue;
			}

			// Digit-bounded, so an id like 42 cannot be replaced inside an amount like 4200.
			$text = (string) preg_replace(
				'/(?<![0-9])' . preg_quote( $value, '/' ) . '(?![0-9])/',
				$token,
				$text
			);
		}

		$text = str_replace( [ home_url(), untrailingslashit( home_url() ) ], '<site>', $text );

		return $this->snapshotRedactor()->scrub( $text );
	}

	private function snapshotRedactor(): Redactor {
		static $redactor = null;

		if ( null === $redactor ) {
			$redactor = SecretRegistry::fromEnvironment();
		}

		return $redactor;
	}

	private function snapshotPath( string $name ): string {
		return __DIR__ . '/../Data/snapshots/' . $name . '.json';
	}

	private function writeSnapshot( string $path, array $contents ): void {
		if ( ! is_dir( dirname( $path ) ) ) {
			mkdir( dirname( $path ), 0777, true );
		}

		file_put_contents( $path, wp_json_encode( $contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
	}
}
