<?php

// Ensure that the file is being run within the WordPress context.
if (! defined('ABSPATH')) {
	die;
}

wp_enqueue_script('paytrail-woocommerce-paytrail-library');

if (isset($data)) { ?>
	<div id="apple-pay-container" role="region" aria-labelledby="apple-pay-text">
		<p id="apple-pay-text" tabindex="0"><?php esc_html_e('Click the button below to pay with Apple Pay', 'paytrail-for-woocommerce'); ?></p>
		<div id="apple-pay-button" role="button" aria-label="<?php esc_attr_e('Apple Pay Button', 'paytrail-for-woocommerce'); ?>">
			<?php
			$parameters = $data->getParameters();
				array_walk(
					$parameters, function ( $parameter) {
						printf(
							'<input type="hidden" name="%s" value="%s" />',
							esc_attr($parameter->name),
							esc_attr($parameter->value)
						);
					}
				);
			?>
		</div>
	</div>
<?php } else { ?>
	<p tabindex="0"><?php esc_html_e('Something went wrong, please go back to the checkout', 'paytrail-for-woocommerce'); ?></p>
<?php } ?>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const applePayButton = paytrail.applePayButton;
		const wooNotice = document.getElementsByClassName('woocommerce-info');

		// Remove WooCommerce notice after cancelled payment if it exists
		if (wooNotice.length > 0) {
			wooNotice[0].remove();
		}

		// canMakePayment() checks that the user is on a browser which supports Apple Pay.
		if (applePayButton !== undefined && applePayButton.canMakePayment()) {
			applePayButton.mount('#apple-pay-button', (redirectUrl) => {
				setTimeout(() => {
					window.location.replace(redirectUrl);
				}, 1500);
			});
		} else {
			document.getElementById('apple-pay-text').textContent = <?php echo wp_json_encode(__('Apple Pay is not available, please go back to the checkout and use another payment method.', 'paytrail-for-woocommerce')); ?>;
		}
	});
</script>

<style>
	#apple-pay-button {
		display: none;
		-webkit-appearance: -apple-pay-button;
		-apple-pay-button-type: plain;
		-apple-pay-button-style: black;
		height: 50px;
		width: 100%;
		margin-bottom: 10px;
	}
	body.woocommerce-order-pay div[data-block-name="woocommerce/classic-shortcode"],
	body.woocommerce-order-pay div.woocommerce {
		display: flex;
		flex-flow: column;
	}
	body.woocommerce-order-pay div.woocommerce>* {
		order: 1;
	}
	body.woocommerce-order-pay div.woocommerce ul.order_details {
		order: 3;
	}
	body.woocommerce-order-pay div.woocommerce #apple-pay-container {
		order: 1;
	}
</style>
