<?php
/**
 * Metabox view.
 *
 * @package Paytrail\WooCommercePaymentGateway\View
 */

$allowed_html = array(
	'strong' => array(),
);

if ( isset( $data['error'] ) ) {
	$error_message = $data['error'];
} else {

	$amount   = $data['amount'] / 100;
	$currency = array(
		'currency' => $data['currency'],
		'decimals' => 2,
	);

	// translators: Paytrail order status.
	$order_status = sprintf( __( '<strong>Status:</strong> %s', 'paytrail-for-woocommerce' ), wc_strtoupper( $data['status'] ) );
	// translators: Paytrail order amount.
	$amount = sprintf( __( '<strong>Amount:</strong> %s', 'paytrail-for-woocommerce' ), wc_price( $amount, $currency ) );
	// translators: Paytrail transaction ID.
	$transaction_id = sprintf( __( '<strong>Paytrail transaction ID:</strong> %s', 'paytrail-for-woocommerce' ), $data['transaction_id'] );
}
?>
<div class='paytrail-meta-box-content'>
	<?php if ( isset( $error_message ) ) : ?>
		<p><?php echo wp_kses( $error_message, $allowed_html ); ?></p>
	<?php else : ?>
		<?php echo wp_kses( $order_status, $allowed_html ); ?><br/>
		<?php echo wp_kses( $amount, $allowed_html ); ?><br/>
		<?php echo wp_kses( $transaction_id, $allowed_html ); ?><br/>
	<?php endif; ?>
</div>
