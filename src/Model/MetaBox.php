<?php
/**
 * Paytrail for Woocommerce Meta box model class
 *
 * @package Paytrail\WooCommercePaymentGateway\Model
 */

namespace Paytrail\WooCommercePaymentGateway\Model;

use Paytrail\SDK\Request\PaymentStatusRequest;
use Paytrail\SDK\Response\PaymentStatusResponse;
use Paytrail\WooCommercePaymentGateway\Plugin;

/**
 * Paytrail for Woocommerce Meta box model class
 */
class MetaBox {

	/**
	 * The order the metabox retrieves its data from.
	 *
	 * @var \WC_Order
	 */
	private $order;

	/**
	 * The Paytrail order status.
	 *
	 * @var array
	 */
	private $status;

	/**
	 * The Paytrail order amount.
	 *
	 * @var string
	 */
	private $amount;

	/**
	 * The WC order currency.
	 *
	 * @var string
	 */
	private $currency;

	/**
	 * The Paytrail transaction ID.
	 *
	 * @var string
	 */
	private $transaction_id;

	/**
	 * The Paytrail payment status.
	 *
	 * @var PaymentStatusResponse|null
	 */
	private $payment_status;

	/**
	 * Constructor.
	 *
	 * @param \WC_Order $order The WC order.
	 */
	public function __construct( $order ) {
		$this->order = $order;
	}

	/**
	 * Retrieves the Paytrail order status.
	 *
	 * @return array|null The Paytrail order status.
	 */
	public function get_status() {
		if ( empty( $this->status ) ) {

			if ( empty( $this->payment_status ) ) {
				$this->payment_status = $this->get_payment_status();
			}
			$this->status = empty( $this->payment_status ) ? null : $this->payment_status->getStatus();
		}

		return $this->status;
	}

	/**
	 * Retrieves the Paytrail order amount.
	 *
	 * @return string The Paytrail order amount.
	 */
	public function get_amount() {
		if ( null === $this->amount ) {

			if ( empty( $this->payment_status ) ) {
				$this->payment_status = $this->get_payment_status();
			}
			$this->amount = empty( $this->payment_status ) ? null : $this->payment_status->getAmount();
		}

		return $this->amount;
	}

	/**
	 * Retrieves the WC order currency.
	 *
	 * @return string The WC order currency.
	 */
	public function get_currency() {
		if ( empty( $this->currency ) ) {
			$this->currency = $this->order->get_currency();
		}

		return $this->currency;
	}

	/**
	 * Retrieves the Paytrail transaction ID.
	 *
	 * @return string The Paytrail transaction ID.
	 */
	public function get_transaction_id() {
		// The Paytrail transaction ID is stored as the order's transaction ID. We don't need to fetch it from Paytrail.
		if ( empty( $this->transaction_id ) ) {
			$this->transaction_id = $this->order->get_transaction_id();
		}

		return $this->transaction_id;
	}

	/**
	 * Get the Payment status from Paytrail.
	 *
	 * @return PaymentStatusResponse|null
	 */
	public function get_payment_status() {
		$gateway = Plugin::instance()->gateway();

		$request = new PaymentStatusRequest();
		$request->setTransactionId( $this->order->get_transaction_id() );

		$client = $gateway->get_client();
		try {
			$response = $client->getPaymentStatus( $request );
			// Log the retrieved response for debugging purposes.
			Plugin::instance()->gateway()->log(  PaymentStatusRequest::class . ' retrieved: ' . $response->getTransactionId() . ', status: ' . $response->getStatus() . ', amount: ' . $response->getAmount() );
			if ( $response->getTransactionId() === $this->order->get_transaction_id() ) {
				return $response;
			}
		} catch ( \Exception $e ) {
			Plugin::instance()->gateway()->log( 'Error retrieving ' . PaymentStatusRequest::class . ': ' . $e->getMessage(), 'error' );
			return null;
		}

		return null;
	}
}
