<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use Paytrail\SDK\Client;
use Paytrail\SDK\PaytrailClient;
use Paytrail\WooCommercePaymentGateway\Gateway;
use Paytrail\WooCommercePaymentGateway\Plugin;
use Tests\Support\Fakes\FakePaytrailApi;

/**
 * Blocks and records Paytrail API traffic, and hands out the gateway wired to the fake.
 * `CanConfigureStore::reloadGateway()` re-installs it on every gateway rebuild, so the
 * two traits are composed together.
 */
trait CanInterceptPaytrailApi {

	/** @var \Tests\Support\Fakes\FakePaytrailApi|null */
	private $paytrailApi = null;

	/**
	 * The gateway under test: the one `Plugin::instance()` hands the plugin's own
	 * callers, so a controller resolving the singleton gets the same object.
	 */
	protected function gateway(): Gateway {
		return Plugin::instance()->gateway();
	}

	/** The fake standing in for Paytrail. */
	protected function api(): FakePaytrailApi {
		if ( null === $this->paytrailApi ) {
			$this->interceptPaytrailApi();
		}

		return $this->paytrailApi;
	}

	/**
	 * Replaces the SDK client's HTTP layer with the fake. The same fake is carried over
	 * to a rebuilt gateway, so a mid-test settings change does not throw the recording
	 * and the queued responses away.
	 */
	protected function interceptPaytrailApi(): FakePaytrailApi {
		$client      = $this->gateway()->get_client();
		$merchant_id = (int) $this->readClientProperty( $client, 'merchantId' );
		$secret_key  = (string) $this->readClientProperty( $client, 'secretKey' );

		if ( null === $this->paytrailApi ) {
			$this->paytrailApi = new FakePaytrailApi( $merchant_id, $secret_key );
		} else {
			$this->paytrailApi->useCredentials( $merchant_id, $secret_key );
		}

		$http_client = new \ReflectionProperty( PaytrailClient::class, 'http_client' );
		$http_client->setAccessible( true );
		$http_client->setValue( $client, $this->paytrailApi );

		return $this->paytrailApi;
	}

	/** Queues a successful API response. */
	protected function willRespondWith( array $body, int $status = 200, ?string $uri_contains = null, array $headers = [] ): void {
		$this->api()->willRespondWith( $body, $status, $uri_contains, $headers );
	}

	/** Queues an API rejection, optionally with the body Paytrail answers it with. */
	protected function willRejectWith( string $uri_contains, string $message, int $status = 400, array $body = [] ): void {
		$this->api()->willRejectWith( $uri_contains, $message, $status, $body );
	}

	/** Queues a connection failure. */
	protected function willFailToConnect( string $uri_contains, string $message = 'Connection refused' ): void {
		$this->api()->willFailToConnect( $uri_contains, $message );
	}

	/** Every API request the test provoked, in order. */
	protected function apiRequests(): array {
		return $this->api()->requests();
	}

	/** The API requests whose URI contains the given fragment. */
	protected function apiRequestsTo( string $uri_contains ): array {
		return $this->api()->requestsTo( $uri_contains );
	}

	/** The one API request aimed at the given endpoint. */
	protected function apiRequestTo( string $uri_contains ): array {
		$matching = $this->apiRequestsTo( $uri_contains );

		if ( 1 !== count( $matching ) ) {
			$this->fail(
				sprintf(
					'Expected exactly one API request to "%s", got %d. Requests made: %s',
					$uri_contains,
					count( $matching ),
					$this->api()->describe()
				)
			);
		}

		return $matching[0];
	}

	/** Asserts how many API requests were made, optionally to one endpoint. */
	protected function assertApiRequestCount( int $expected, string $uri_contains = '', ?string $message = null ): void {
		$matching = '' === $uri_contains ? $this->apiRequests() : $this->apiRequestsTo( $uri_contains );

		$this->assertCount(
			$expected,
			$matching,
			trim( ( $message ?? '' ) . ' Requests made: ' . $this->api()->describe() )
		);
	}

	/** Asserts that nothing called Paytrail. */
	protected function assertNoApiRequests( string $message = '' ): void {
		$this->assertSame(
			[],
			array_map(
				static function ( $request ) {
					return $request['method'] . ' ' . $request['uri'];
				},
				$this->apiRequests()
			),
			'' !== $message ? $message : 'Expected no requests to the Paytrail API.'
		);
	}

	/** Asserts every queued response was consumed, so a test cannot pass on a stale queue. */
	protected function assertAllQueuedResponsesUsed(): void {
		$this->assertSame(
			0,
			$this->api()->pendingResponses(),
			'Queued API responses were left unused. Requests made: ' . $this->api()->describe()
		);
	}

	/** Forgets recorded requests and queued responses. */
	protected function resetApiInterception(): void {
		if ( null !== $this->paytrailApi ) {
			$this->paytrailApi->reset();
		}
	}

	/**
	 * Reads a credential off the SDK client, so the fake signs its responses with the
	 * secret the client verifies them against.
	 *
	 * @return mixed
	 */
	private function readClientProperty( Client $client, string $name ) {
		$property = new \ReflectionProperty( PaytrailClient::class, $name );
		$property->setAccessible( true );

		return $property->getValue( $client );
	}
}
