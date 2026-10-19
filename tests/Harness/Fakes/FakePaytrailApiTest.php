<?php

declare(strict_types=1);

namespace Tests\Harness\Fakes;

use Paytrail\SDK\Exception\ClientException;
use Paytrail\SDK\Exception\RequestException;
use Paytrail\SDK\Request\PaymentStatusRequest;
use Tests\Support\IntegrationTestCase;

/**
 * The stand-in for the Paytrail SDK's HTTP client. If this drifts, every Integration
 * test built on it is testing the wrong thing.
 *
 * @covers \Tests\Support\Fakes\FakePaytrailApi
 */
class FakePaytrailApiTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/**
	 * The SDK validates the HMAC of every response before it reads the body, so a fake
	 * that cannot sign one would fail every read path for the wrong reason.
	 */
	public function test_a_canned_response_passes_the_sdk_signature_check(): void {
		$this->willReportPaymentStatus( 'paytrail-transaction-1', [ 'status' => 'pending' ] );

		$response = $this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );

		$this->assertSame( 'pending', $response->getStatus() );
	}

	/** Nothing queued means nothing to answer with, which the SDK sees as a dead API. */
	public function test_an_unqueued_call_is_refused(): void {
		$this->expectException( RequestException::class );

		$this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );
	}

	/**
	 * A rejection is shaped the way the SDK's own `RequestClient` shapes one: the status
	 * lands on `getResponseCode()`, and the exception code stays 0.
	 */
	public function test_a_rejection_reports_its_status_the_way_the_sdk_does(): void {
		$this->willRejectWith( '/payments/', 'Refund not supported', 422 );

		try {
			$this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );
			$this->fail( 'A queued rejection should reach the caller.' );
		} catch ( ClientException $exception ) {
			$this->assertSame( 422, $exception->getResponseCode() );
			$this->assertSame( 0, $exception->getCode() );
		}
	}

	/**
	 * Queued fragments are matched longest first, so a response for a nested endpoint
	 * is not eaten by one queued for the endpoint it sits under.
	 */
	public function test_the_most_specific_queued_fragment_answers_a_request(): void {
		$this->willRespondWith( [ 'transactionId' => 'paytrail-transaction-1', 'status' => 'from-the-general-one' ], 200, '/payments/' );
		$this->willRespondWith( [ 'transactionId' => 'paytrail-transaction-1', 'status' => 'from-the-specific-one' ], 200, '/payments/paytrail-transaction-1' );

		$response = $this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );

		$this->assertSame( 'from-the-specific-one', $response->getStatus() );
	}

	/** Equally specific responses are handed out in the order they were queued. */
	public function test_equally_specific_responses_are_used_in_order(): void {
		$this->willRespondWith( [ 'transactionId' => 'paytrail-transaction-1', 'status' => 'first' ], 200, '/payments/' );
		$this->willRespondWith( [ 'transactionId' => 'paytrail-transaction-1', 'status' => 'second' ], 200, '/payments/' );

		$client = $this->gateway()->get_client();

		$this->assertSame( 'first', $client->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) )->getStatus() );
		$this->assertSame( 'second', $client->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) )->getStatus() );
	}

	/** The recording is what every Integration assertion about a request body reads. */
	public function test_a_request_is_recorded_with_its_method_endpoint_and_body(): void {
		$this->willReportPaymentStatus( 'paytrail-transaction-1' );

		$this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );

		$request = $this->apiRequestTo( '/payments/paytrail-transaction-1' );

		$this->assertSame( 'GET', $request['method'] );
		$this->assertSame( '/payments/paytrail-transaction-1', $request['uri'] );
		$this->assertSame( 'https://services.paytrail.com/payments/paytrail-transaction-1', $request['url'] );
	}

	/** A test that queued more than it used has not exercised what it thinks it has. */
	public function test_unused_queued_responses_are_reported(): void {
		$this->willReportPaymentStatus( 'paytrail-transaction-1' );

		$this->assertSame( 1, $this->api()->pendingResponses() );

		$this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );

		$this->assertAllQueuedResponsesUsed();
	}

	/**
	 * Any settings change rebuilds the gateway, and a test that queued a response before
	 * one has to still get it.
	 */
	public function test_the_recording_survives_a_gateway_rebuild(): void {
		$this->willReportPaymentStatus( 'paytrail-transaction-1' );

		$this->haveGatewaySettings( [ 'debug' => 'yes' ] );

		$this->assertSame( 1, $this->api()->pendingResponses() );
		$this->assertSame(
			'paytrail-transaction-1',
			$this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) )->getTransactionId()
		);
	}

	/** A rebuild signs later responses as whichever merchant the new gateway reads. */
	public function test_a_rebuilt_gateway_signs_as_its_own_merchant(): void {
		$this->haveLiveGatewaySettings();
		$this->willReportPaymentStatus( 'paytrail-transaction-1' );

		$response = $this->gateway()->get_client()->getPaymentStatus( $this->statusRequestFor( 'paytrail-transaction-1' ) );

		$this->assertSame( 'paytrail-transaction-1', $response->getTransactionId() );
	}

	private function statusRequestFor( string $transaction_id ): PaymentStatusRequest {
		$request = new PaymentStatusRequest();
		$request->setTransactionId( $transaction_id );

		return $request;
	}
}
