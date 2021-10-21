<?php
/**
 * Paytrail for Woocommerce payment Abstract controller class
 */

namespace Paytrail\WooCommercePaymentGateway\Controllers;

abstract class AbstractController
{
    public function execute($action = null)
    {
        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            echo 'Not found';
            return;
        }
    }
}