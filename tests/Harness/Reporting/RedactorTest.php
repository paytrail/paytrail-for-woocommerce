<?php

declare(strict_types=1);

namespace Tests\Harness\Reporting;

use Tests\Support\IntegrationTestCase;
use Tests\Support\Reporting\Redactor;
use Tests\Support\Reporting\SecretRegistry;

/**
 * Scrubbing credentials out of the artifacts a test run publishes. A gap here is a
 * secret in a downloadable CI artifact, so these run before the other suites.
 *
 * @covers \Tests\Support\Reporting\Redactor
 * @covers \Tests\Support\Reporting\SecretRegistry
 */
class RedactorTest extends IntegrationTestCase {

	private const SECRET = 'a-merchant-secret-key';

	public function test_a_registered_secret_is_masked(): void {
		$redactor = Redactor::withDefaultPatterns()->withSecret( self::SECRET, 'PAYTRAIL_SECRET_KEY' );

		$scrubbed = $redactor->scrub( 'signing with ' . self::SECRET . ' now' );

		$this->assertStringNotContainsString( self::SECRET, $scrubbed );
		$this->assertStringContainsString( Redactor::MASK, $scrubbed );
	}

	/**
	 * A secret reaches an artifact re-encoded as often as it does verbatim, in a
	 * base64 header or a URL-encoded query string.
	 *
	 * @dataProvider provide_encodings
	 */
	public function test_a_secret_is_masked_in_every_encoding_it_can_reach_an_artifact_in( string $encoded ): void {
		$redactor = Redactor::withDefaultPatterns()->withSecret( self::SECRET, 'PAYTRAIL_SECRET_KEY' );

		$this->assertStringNotContainsString( $encoded, $redactor->scrub( 'value: ' . $encoded ) );
	}

	/** @return array<string, array{0: string}> */
	public function provide_encodings(): array {
		return [
			'verbatim'    => [ self::SECRET ],
			'base64'      => [ base64_encode( self::SECRET ) ],
			'url encoded' => [ rawurlencode( self::SECRET ) ],
		];
	}

	/**
	 * An unset credential registers as the empty string, which would otherwise match
	 * everywhere and mask the whole artifact.
	 */
	public function test_a_short_or_empty_secret_is_ignored(): void {
		$redactor = Redactor::withDefaultPatterns()->withSecret( '', 'PAYTRAIL_SECRET_KEY' )->withSecret( 'abc', 'NGROK_AUTHTOKEN' );

		$this->assertSame( 'abc is fine', $redactor->scrub( 'abc is fine' ) );
	}

	/**
	 * Credential-shaped text is masked even when the value itself was never registered,
	 * which is what covers a secret the harness never knew about.
	 *
	 * @dataProvider provide_credential_shapes
	 */
	public function test_credential_shaped_text_is_masked( string $text ): void {
		$this->assertStringContainsString( Redactor::MASK, Redactor::withDefaultPatterns()->scrub( $text ) );
	}

	/** @return array<string, array{0: string}> */
	public function provide_credential_shapes(): array {
		return [
			'a basic auth header'   => [ 'Authorization: Basic ZGVtbzpzZWNyZXQ=' ],
			'a bearer token'        => [ '"Authorization":"Bearer abc123def456"' ],
			'a secret_key in JSON'  => [ '{"secret_key":"SAIPPUAKAUPPIAS"}' ],
			'an env assignment'     => [ 'PAYTRAIL_SECRET_KEY=some-real-secret' ],
		];
	}

	/** An all-digit credential registers as an int array key, which must still scrub. */
	public function test_an_all_digit_secret_is_masked(): void {
		$redactor = Redactor::withDefaultPatterns()->withSecret( '3759170000', 'PAYTRAIL_MERCHANT_ID' );

		$this->assertSame( Redactor::MASK, $redactor->scrub( '3759170000' ) );
	}

	/** A longer secret containing a shorter one is masked whole, not left in pieces. */
	public function test_a_secret_containing_another_is_masked_whole(): void {
		$redactor = Redactor::withDefaultPatterns()
			->withSecret( 'short-secret', 'NGROK_AUTHTOKEN' )
			->withSecret( 'short-secret-and-more', 'PAYTRAIL_SECRET_KEY' );

		$this->assertSame( Redactor::MASK, $redactor->scrub( 'short-secret-and-more' ) );
	}

	/** The registry is what decides which env values are secret in the first place. */
	public function test_the_registry_reads_secrets_out_of_the_environment(): void {
		$redactor = SecretRegistry::fromEnvironment(
			'/does/not/exist',
			[ 'PAYTRAIL_SECRET_KEY' => self::SECRET ]
		);

		$this->assertStringNotContainsString( self::SECRET, $redactor->scrub( self::SECRET ) );
	}

	/** A .env value stands in when the real environment does not carry the credential. */
	public function test_the_registry_falls_back_to_the_env_file(): void {
		$path = tempnam( sys_get_temp_dir(), 'paytrail-env' );
		file_put_contents( $path, "# a comment\nPAYTRAIL_SECRET_KEY=\"" . self::SECRET . "\"\n" );

		$redactor = SecretRegistry::fromEnvironment( $path, [] );
		unlink( $path );

		$this->assertStringNotContainsString( self::SECRET, $redactor->scrub( self::SECRET ) );
	}
}
