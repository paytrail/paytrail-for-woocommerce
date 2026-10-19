<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

/**
 * Canned Paytrail responses for the purchase flow. The sibling of
 * CanDriveOrderManagement, which covers the calls an admin makes afterwards.
 */
trait CanDriveCheckout {

	/** The provider id the fixtures select unless a test says otherwise. */
	public const DEFAULT_PROVIDER = 'osuuspankki';

	/**
	 * Queues a successful create-payment response, the one a redirect or a provider
	 * selection comes from.
	 */
	protected function willCreatePayment( array $overrides = [], string $provider = self::DEFAULT_PROVIDER ): void {
		$this->willRespondWith(
			array_merge(
				[
					'transactionId' => 'paytrail-transaction-1',
					'href'          => 'https://pay.paytrail.com/pay/paytrail-transaction-1',
					'reference'     => '1',
					'terms'         => 'By paying you accept the terms.',
					'groups'        => [],
					'providers'     => [ $this->paytrailProvider( [ 'id' => $provider ] ) ],
				],
				$overrides
			),
			201,
			'/payments'
		);
	}

	/** Queues the flat provider list the redirect flow reads the chosen provider's name from. */
	protected function willListProviders( ?array $providers = null ): void {
		$this->willRespondWith(
			$providers ?? [ $this->paytrailProvider() ],
			200,
			'/merchants/payment-providers'
		);
	}

	/** Queues the grouped provider list the in-store selection form renders. */
	protected function willListGroupedProviders( array $overrides = [] ): void {
		$this->willRespondWith(
			array_merge(
				[
					'terms'  => 'By paying you accept the terms.',
					'groups' => [
						[
							'id'        => 'bank',
							'name'      => 'Banks',
							'icon'      => 'https://static.paytrail.com/bank.png',
							'svg'       => 'https://static.paytrail.com/bank.svg',
							'providers' => [ $this->paytrailProvider() ],
						],
					],
				],
				$overrides
			),
			200,
			'/merchants/grouped-payment-providers'
		);
	}

	/**
	 * Queues a successful card charge on a stored token. A `$three_ds_url` is what
	 * Paytrail answers with when the card issuer wants the shopper to authenticate.
	 */
	protected function willChargeCard( string $transaction_id = 'paytrail-cit-1', ?string $three_ds_url = null ): void {
		$this->willRespondWith(
			[
				'transactionId'   => $transaction_id,
				'threeDSecureUrl' => $three_ds_url,
			],
			201,
			'/payments/token/cit/charge'
		);
	}

	/**
	 * Queues the 403 Paytrail answers a card charge with when the issuer wants the
	 * shopper to authenticate. The SDK reads the body back off it as a normal response.
	 */
	protected function willChallengeCardWith3ds( string $three_ds_url = 'https://3ds.example.com/authenticate/1', string $transaction_id = 'paytrail-cit-1' ): void {
		$this->willRejectWith(
			'/payments/token/cit/charge',
			'Authentication required',
			403,
			[
				'transactionId'   => $transaction_id,
				'threeDSecureUrl' => $three_ds_url,
			]
		);
	}

	/** Queues a successful merchant-initiated charge, which is what a renewal is. */
	protected function willChargeStoredCard( string $transaction_id = 'paytrail-mit-1' ): void {
		$this->willRespondWith(
			[ 'transactionId' => $transaction_id ],
			201,
			'/payments/token/mit/charge'
		);
	}

	/** Queues the tokenisation read-back the add-card return leg makes. */
	protected function willIssueCardToken( array $card = [], string $token = 'card-token-1' ): void {
		$this->willRespondWith(
			[
				'token'    => $token,
				'card'     => array_merge(
					[
						'type'             => 'Visa',
						'bin'              => '415301',
						'partial_pan'      => '0024',
						'expire_year'      => '2032',
						'expire_month'     => '11',
						'cvc_required'     => 'no',
						'funding'          => 'debit',
						'category'         => 'personal',
						'country_code'     => 'FI',
						'pan_fingerprint'  => 'pan-fingerprint-1',
						'card_fingerprint' => 'card-fingerprint-1',
					],
					$card
				),
				'customer' => [ 'country_code' => 'FI' ],
			],
			200,
			'/tokenization/'
		);
	}

	/** Queues the add-card form response, whose Location header the plugin redirects to. */
	protected function willReturnAddCardForm( string $url = 'https://services.paytrail.com/tokenization/addcard-form/1' ): void {
		$this->willRespondWith( [], 200, '/tokenization/addcard-form', [ 'Location' => $url ] );
	}

	/**
	 * One entry of a Paytrail provider list.
	 *
	 * @return array<string, mixed>
	 */
	protected function paytrailProvider( array $overrides = [] ): array {
		return array_merge(
			[
				'id'         => self::DEFAULT_PROVIDER,
				'name'       => 'OP',
				'group'      => 'bank',
				'icon'       => 'https://static.paytrail.com/op.png',
				'svg'        => 'https://static.paytrail.com/op.svg',
				'url'        => 'https://kassa.op.fi',
				'parameters' => [],
			],
			$overrides
		);
	}
}
