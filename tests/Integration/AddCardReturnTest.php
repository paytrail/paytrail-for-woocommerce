<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\SDK\Exception\ValidationException;
use Paytrail\WooCommercePaymentGateway\Gateway;
use Tests\Support\IntegrationTestCase;

/**
 * Saving a card when the customer comes back from Paytrail's add-card form, which only
 * counts for the signed-in customer who started that form.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::add_card_form
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_card_token
 */
class AddCardReturnTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	public function test_starting_the_form_remembers_who_started_it(): void {
		$customer_id = $this->haveSignedInCustomer();

		$state = $this->startAddCardForm();

		$this->assertSame( $customer_id, get_transient( Gateway::ADD_CARD_STATE_PREFIX . $state ) );
		$this->assertSame( $state, $this->apiRequestTo( '/tokenization/addcard-form' )['json']['checkout-nonce'] );
	}

	public function test_a_guest_cannot_start_the_form(): void {
		$this->haveGuestCustomer();

		$this->expectException( ValidationException::class );
		$this->gateway()->add_card_form();
	}

	public function test_the_card_is_saved_for_the_customer_who_started_the_form(): void {
		$customer_id = $this->haveSignedInCustomer();
		$state       = $this->startAddCardForm();

		$this->willIssueCardToken();
		$token_id = $this->gateway()->process_card_token( $this->addCardReturn( $state ) );

		$this->assertSame( $customer_id, \WC_Payment_Tokens::get( $token_id )->get_user_id() );
	}

	public function test_a_return_started_by_another_customer_is_refused(): void {
		$this->haveSignedInCustomer();
		$state = $this->startAddCardForm();

		$victim_id = $this->haveSignedInCustomer();

		$this->assertReturnIsRefused( $this->addCardReturn( $state ) );
		$this->assertSame( [], \WC_Payment_Tokens::get_customer_tokens( $victim_id ) );
	}

	public function test_a_return_without_its_state_is_refused(): void {
		$this->haveSignedInCustomer();
		$this->startAddCardForm();

		$this->assertReturnIsRefused( [ 'checkout-tokenization-id' => 'paytrail-tokenization-1' ] );
	}

	public function test_a_return_after_signing_out_is_refused(): void {
		$this->haveSignedInCustomer();
		$state = $this->startAddCardForm();

		$this->haveGuestCustomer();

		$this->assertReturnIsRefused( $this->addCardReturn( $state ) );
	}

	public function test_a_return_can_only_be_used_once(): void {
		$this->haveSignedInCustomer();
		$state = $this->startAddCardForm();

		$this->willIssueCardToken();
		$this->gateway()->process_card_token( $this->addCardReturn( $state ) );

		$this->assertReturnIsRefused( $this->addCardReturn( $state ) );
	}

	/** Opens the add-card form and returns the state its success URL carries. */
	private function startAddCardForm(): string {
		// Answered without a Location header, so add_card_form() returns instead of redirecting and exiting.
		$this->willRespondWith( [], 200, '/tokenization/addcard-form' );

		$this->gateway()->add_card_form();

		$requests = $this->apiRequestsTo( '/tokenization/addcard-form' );
		parse_str( (string) wp_parse_url( end( $requests )['json']['checkout-redirect-success-url'], PHP_URL_QUERY ), $query );

		return (string) ( $query['paytrail_state'] ?? '' );
	}

	private function addCardReturn( string $state ): array {
		return [
			'checkout-tokenization-id' => 'paytrail-tokenization-1',
			'paytrail_state'           => $state,
		];
	}

	/** The return is refused before the tokenization id is ever exchanged with Paytrail. */
	private function assertReturnIsRefused( array $params ): void {
		$exchanges_before = count( $this->apiRequestsTo( '/tokenization/paytrail-tokenization-1' ) );

		try {
			$this->gateway()->process_card_token( $params );
			$this->fail( 'The add-card return should have been refused' );
		} catch ( ValidationException $exception ) {
			$this->assertSame( 'The card could not be tied to the customer who added it.', $exception->getMessage() );
		}

		$this->assertApiRequestCount( $exchanges_before, '/tokenization/paytrail-tokenization-1', 'The refused return should not reach Paytrail' );
	}
}
