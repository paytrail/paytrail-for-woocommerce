<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;

/**
 * Paying with a card the shopper has already saved, which Paytrail calls a
 * customer-initiated transaction.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Gateway::process_paytrail_payment
 */
class CardPaymentTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** A charge the issuer waves through is paid there and then. */
	public function test_a_card_charge_without_3ds_completes_the_order(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->willChargeCard( 'paytrail-cit-1' );
		$result = $this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$reloaded = $this->reload( $order );

		$this->assertSame( $this->gateway()->get_return_url( $order ), $result['redirect'] );
		$this->assertSame( 'paytrail-cit-1', $reloaded->get_transaction_id() );
		$this->assertNotEmpty( $reloaded->get_date_paid() );
	}

	/**
	 * A charge the issuer wants authenticated sends the shopper to the 3DS page unpaid.
	 * Paytrail signals that as a 403 whose body carries the URL, which the SDK reads
	 * back as an ordinary response, so both shapes end up here.
	 *
	 * @dataProvider provide_3ds_challenges
	 */
	public function test_a_card_charge_needing_3ds_sends_the_shopper_on( string $shape ): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->queue3dsChallenge( $shape );
		$result = $this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$this->assertSame( 'https://3ds.example.com/authenticate/1', $result['redirect'] );
		$this->assertEmpty( $this->reload( $order )->get_date_paid() );
		$this->assertOrderHasNote( $order, 'Requires 3DS: yes' );
	}

	/** @return array<string, array{0: string}> */
	public function provide_3ds_challenges(): array {
		return [
			'answered as a 403' => [ '403' ],
			'answered as a 201' => [ '201' ],
		];
	}

	/** The note tells a merchant the shopper was never asked to authenticate. */
	public function test_the_order_note_records_that_no_authentication_was_needed(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->willChargeCard( 'paytrail-cit-1' );
		$this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$this->assertOrderHasNote( $order, 'Transaction paytrail-cit-1 created by token payment using card. Requires 3DS: no' );
	}

	/** The card is attached to the order, which is what a later renewal charges. */
	public function test_the_card_is_attached_to_the_order(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->willChargeCard();
		$this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$this->assertSame( [ $token->get_id() ], $this->reload( $order )->get_payment_tokens() );
	}

	/** The card token is what Paytrail charges, so it has to reach the request body. */
	public function test_the_charge_carries_the_card_token(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken( [ 'token' => 'card-token-abc' ] );

		$this->willChargeCard();
		$this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$this->assertSame( 'card-token-abc', $this->apiRequestTo( '/payments/token/cit/charge' )['json']['token'] );
	}

	/** A card charge never goes through the redirect endpoint. */
	public function test_a_card_charge_does_not_create_a_redirect_payment(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		$this->willChargeCard();
		$this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );

		$this->assertApiRequestCount( 1, '/payments/token/cit/charge' );
		$this->assertApiRequestCount( 1, '', 'A card charge is a single call.' );
	}

	/** A declined card leaves a note a merchant can act on, and stops the checkout. */
	public function test_a_declined_card_is_noted_on_the_order(): void {
		$order = $this->havePurchasableOrder();
		$token = $this->haveCardToken();

		// Not a 403: the SDK reads that back as a 3DS challenge rather than a refusal.
		$this->willRejectWith( '/payments/token/cit/charge', 'card declined', 400 );

		try {
			$this->gateway()->process_paytrail_payment( $order, $token->get_id(), '', false );
			$this->fail( 'A declined card should stop the checkout.' );
		} catch ( \Exception $exception ) {
			$this->assertStringContainsString( 'Failed to create token payment using card.', $exception->getMessage() );
		}

		$this->assertOrderHasNote( $order, 'Failed to create token payment using card.' );
		$this->assertEmpty( $this->reload( $order )->get_date_paid() );
	}

	/**
	 * The token id arrives in the checkout POST and WooCommerce does not check whose card
	 * it is, so a card that is not the signed-in shopper's own is refused here.
	 *
	 * @dataProvider provide_cards_the_shopper_does_not_own
	 */
	public function test_a_card_the_shopper_does_not_own_is_refused( string $owner ): void {
		$order    = $this->havePurchasableOrder();
		$token_id = $this->haveCardTokenOwnedBy( $owner );

		$this->willChargeCard();

		$refusal = null;

		try {
			$this->gateway()->process_paytrail_payment( $order, $token_id, '', false );
		} catch ( \Exception $exception ) {
			$refusal = $exception;
		}

		$this->assertNotNull( $refusal, 'A card the shopper does not own should stop the checkout.' );
		$this->assertStringContainsString( 'The chosen card is not available', $refusal->getMessage() );

		$this->assertNoApiRequests( 'A card the shopper does not own is never charged.' );
		$this->assertSame( [], $this->reload( $order )->get_payment_tokens() );
	}

	/** @return array<string, array{0: string}> */
	public function provide_cards_the_shopper_does_not_own(): array {
		return [
			'saved by another customer' => [ 'another customer' ],
			'saved for another gateway' => [ 'another gateway' ],
			'saved while signed out'    => [ 'a guest' ],
		];
	}

	/** A token that exists but belongs to someone, or something, other than the shopper. */
	private function haveCardTokenOwnedBy( string $owner ): int {
		switch ( $owner ) {
			case 'another customer':
				return $this->haveCardToken( [ 'user_id' => $this->haveCustomerAccount() ] )->get_id();
			case 'another gateway':
				return $this->haveCardToken( [ 'gateway_id' => 'another-gateway' ] )->get_id();
			case 'a guest':
				$token = $this->haveCardToken( [ 'user_id' => 0 ] );
				$this->haveGuestCustomer();

				return $token->get_id();
			default:
				throw new \InvalidArgumentException( sprintf( 'Unknown card owner "%s".', $owner ) );
		}
	}

	private function queue3dsChallenge( string $shape ): void {
		if ( '403' === $shape ) {
			$this->willChallengeCardWith3ds();
			return;
		}

		$this->willChargeCard( 'paytrail-cit-1', 'https://3ds.example.com/authenticate/1' );
	}

	/** A signed-in shopper with an order to pay, which is where a card charge starts. */
	private function havePurchasableOrder(): \WC_Order {
		$this->haveSignedInCustomer();

		return $this->haveOrder(
			[
				'items'   => [ [ $this->haveSimpleProduct( [ 'name' => 'Sauna bucket', 'sku' => 'sauna-bucket', 'price' => '100.00' ] ), 1 ] ],
				'billing' => $this->finnishAddress(),
			]
		);
	}
}
