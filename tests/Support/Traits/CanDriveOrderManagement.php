<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

/** Canned Paytrail responses for the calls an admin's actions make. */
trait CanDriveOrderManagement {

	/** Queues the payment lookup the metabox and the invoice handling both start with. */
	protected function willReportPaymentStatus( string $transaction_id = 'paytrail-transaction-1', array $overrides = [] ): void {
		$this->willRespondWith(
			array_merge(
				[
					'transactionId' => $transaction_id,
					'status'        => 'ok',
					'amount'        => 10000,
					'currency'      => 'EUR',
					'stamp'         => 'stamp-1',
					'reference'     => '1',
					'createdAt'     => '2026-01-01T10:00:00.000Z',
					'provider'      => 'osuuspankki',
				],
				$overrides
			),
			200,
			'/payments/' . $transaction_id
		);
	}

	/** Queues a successful refund. */
	protected function willRefund( array $overrides = [] ): void {
		$this->willRespondWith(
			array_merge(
				[
					'transactionId' => 'paytrail-refund-1',
					'status'        => 'ok',
					'provider'      => 'osuuspankki',
				],
				$overrides
			),
			201,
			'/refund'
		);
	}

	/** Queues a refund rejection. */
	protected function willRejectRefund( string $message = 'Refund not supported', int $status = 400 ): void {
		$this->willRejectWith( '/refund', $message, $status );
	}

	/** Queues a successful manual invoice activation. */
	protected function willActivateInvoice( string $status = 'ok' ): void {
		$this->willRespondWith( [ 'status' => $status ], 200, '/activate-invoice' );
	}

	/** Queues a successful invoice cancellation. */
	protected function willCancelInvoice( string $status = 'ok', string $message = 'Invoice cancelled' ): void {
		$this->willRespondWith(
			[
				'status'  => $status,
				'message' => $message,
			],
			200,
			'/cancel-order'
		);
	}
}
