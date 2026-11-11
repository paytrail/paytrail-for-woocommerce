<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\WooCommercePaymentGateway\Controllers\Card;
use Tests\Support\IntegrationTestCase;

/**
 * Deleting a saved card from the store's own "Delete selected card" button.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Controllers\Card::delete
 */
class DeleteCardTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	public function test_a_customer_can_delete_their_card(): void {
		$this->haveSignedInCustomer();
		$token = $this->haveCardToken();

		$response = $this->deleteCard( [ 'token_id' => $token->get_id(), 'nonce' => wp_create_nonce( 'paytrail_delete_card' ) ] );

		$this->assertTrue( $response['success'] );
		$this->assertNull( \WC_Payment_Tokens::get( $token->get_id() ) );
	}

	/**
	 * @dataProvider forgedDeletes
	 */
	public function test_a_delete_from_outside_the_store_is_refused( string $nonce ): void {
		$this->haveSignedInCustomer();
		$token = $this->haveCardToken();

		$response = $this->deleteCard( [ 'token_id' => $token->get_id(), 'nonce' => $nonce ] );

		$this->assertFalse( $response['success'] );
		$this->assertNotNull( \WC_Payment_Tokens::get( $token->get_id() ), 'The card should still be saved' );
	}

	/** @return array<string, array{string}> */
	public function forgedDeletes(): array {
		return [
			'without a nonce'    => [ '' ],
			'with a forged one'  => [ 'not-a-nonce' ],
		];
	}

	public function test_a_nonce_issued_to_another_customer_is_refused(): void {
		$this->haveSignedInCustomer();
		$nonce = wp_create_nonce( 'paytrail_delete_card' );

		$this->haveSignedInCustomer();
		$token = $this->haveCardToken();

		$response = $this->deleteCard( [ 'token_id' => $token->get_id(), 'nonce' => $nonce ] );

		$this->assertFalse( $response['success'] );
		$this->assertNotNull( \WC_Payment_Tokens::get( $token->get_id() ) );
	}

	public function test_a_request_without_a_method_is_refused(): void {
		$this->haveSignedInCustomer();
		$token = $this->haveCardToken();

		$response = $this->deleteCard( [ 'token_id' => $token->get_id(), 'nonce' => wp_create_nonce( 'paytrail_delete_card' ) ], null );

		$this->assertFalse( $response['success'] );
		$this->assertNotNull( \WC_Payment_Tokens::get( $token->get_id() ) );
	}

	/**
	 * Sends a delete the way the store's JS does and returns the decoded JSON reply.
	 * The body is fed through get_request_body(), since php://input cannot be written to.
	 */
	private function deleteCard( array $body, ?string $method = 'POST' ): array {
		$controller = new class( (string) wp_json_encode( $body ) ) extends Card {
			private string $body;

			public function __construct( string $body ) {
				$this->body = $body;
			}

			protected function get_request_body() {
				return $this->body;
			}
		};

		$server = $_SERVER;
		if ( null === $method ) {
			unset( $_SERVER['REQUEST_METHOD'] );
		} else {
			$_SERVER['REQUEST_METHOD'] = $method;
		}
		$_SERVER['CONTENT_TYPE'] = 'application/json';

		// wp_send_json() ends the request. Here it throws an \Error instead, which the controller's catch ( \Exception ) cannot swallow.
		$doing_ajax = static fn() => true;
		$die        = static fn() => static function () {
			throw new \Error( 'wp_send_json' );
		};
		add_filter( 'wp_doing_ajax', $doing_ajax );
		add_filter( 'wp_die_ajax_handler', $die );

		ob_start();
		try {
			$controller->execute( 'delete' );
		} catch ( \Error $error ) {
			if ( 'wp_send_json' !== $error->getMessage() ) {
				throw $error;
			}
		} finally {
			$output  = (string) ob_get_clean();
			$_SERVER = $server;
			remove_filter( 'wp_doing_ajax', $doing_ajax );
			remove_filter( 'wp_die_ajax_handler', $die );
		}

		return (array) json_decode( $output, true );
	}
}
