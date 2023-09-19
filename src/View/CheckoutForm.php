<?php
/**
 * Checkout form view
 */

// Ensure that the file is being run within the WordPress context.
if (! defined('ABSPATH')) {
	die;
}
?>

<form action="<?php echo esc_html($data->getUrl()); ?>" method="POST" id="checkout-redirect-form">
	<?php
		$parameters = $data->getParameters();

array_walk($parameters, function ( $parameter) {
		printf(
		'<input type="hidden" name="%s" value="%s" />',
		esc_html($parameter->name),
		esc_html($parameter->value)
		);
	});

esc_html_e('Redirecting... If nothing happens in a few seconds, click the button below.', 'paytrail-for-woocommerce');
	?>

	<p><input type="submit" value="<?php esc_html_e('Submit', 'paytrail-for-woocommerce'); ?>" /></p>
</form>

<script>
	document.getElementById( 'checkout-redirect-form' ).submit();
</script>
