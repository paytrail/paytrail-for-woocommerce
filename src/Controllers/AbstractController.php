<?php
/**
 * Paytrail for Woocommerce payment Abstract controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

/**
 * Base controller dispatching a route action to a method of the same name.
 */
abstract class AbstractController {

	/**
	 * Run the requested action.
	 *
	 * @param string|null $action The action to run.
	 * @return void
	 */
	public function execute( $action = null ) {
		if ( method_exists( $this, $action ) ) {
			$this->$action();
		} else {
			echo esc_html( 'Not found' );
			return;
		}
	}
}
