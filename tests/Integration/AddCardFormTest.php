<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * Opening Paytrail's hosted form for saving a card.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::add_card_form
 */
class AddCardFormTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** Paytrail documents a UUID for the nonce, and it has to be random rather than taken from the clock. */
	public function test_the_add_card_request_carries_a_uuid_nonce(): void {
		$this->haveSignedInCustomer();
		$this->willOpenAddCardForm();

		$this->gateway()->add_card_form();

		$this->assertMatchesRegularExpression(
			'/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
			$this->nonceOf( $this->apiRequestTo( '/tokenization/addcard-form' ) )
		);
	}

	public function test_each_add_card_request_gets_its_own_nonce(): void {
		$this->haveSignedInCustomer();
		$this->willOpenAddCardForm();
		$this->willOpenAddCardForm();

		$this->gateway()->add_card_form();
		$this->gateway()->add_card_form();

		$requests = $this->apiRequestsTo( '/tokenization/addcard-form' );

		$this->assertNotSame( $this->nonceOf( $requests[0] ), $this->nonceOf( $requests[1] ) );
	}

	/** Answered without a Location header, so add_card_form() returns instead of redirecting and exiting. */
	private function willOpenAddCardForm(): void {
		$this->willRespondWith( [], 200, '/tokenization/addcard-form' );
	}

	private function nonceOf( array $request ): string {
		return (string) ( $request['json']['checkout-nonce'] ?? '' );
	}
}
