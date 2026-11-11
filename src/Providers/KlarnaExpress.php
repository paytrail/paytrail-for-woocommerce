<?php

namespace Paytrail\WooCommercePaymentGateway\Providers;

use Krokedil\Klarna\ExpressCheckout\Interfaces\AcquiringPartnerIntegration;
use Paytrail\WooCommercePaymentGateway\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Lets Klarna Payments for WooCommerce hand Klarna Express Checkout orders to Paytrail.
 *
 * Only loaded from the kec_acquiring_partner_integrations filter, which Klarna applies, so the interface exists.
 */
class KlarnaExpress implements AcquiringPartnerIntegration {
	/**
	 * Get the acquiring partner key Klarna reports for Paytrail.
	 *
	 * @return string
	 */
	public function get_key() {
		return 'c8f4a56a-25b5-5660-aabd-f4d940a13f44';
	}

	/**
	 * Process the state change for a Klarna Express order.
	 *
	 * @param \WC_Order $order                  The order to process.
	 * @param string    $interoperability_token The Klarna network session token.
	 * @param array     $interoperability_data  The interoperability data.
	 * @param string    $state                  The state of the Express order.
	 * @param array     $payload                The full payload from Klarna.
	 *
	 * @return string The URL to redirect the customer to.
	 */
	public function process_order_state( \WC_Order $order, string $interoperability_token, array $interoperability_data, string $state, array $payload ) {
		if ( 'COMPLETED' !== $state ) {
			return $order->get_checkout_order_received_url();
		}

		return Plugin::instance()->gateway()->process_klarna_express_payment( $order, $interoperability_token );
	}
}
