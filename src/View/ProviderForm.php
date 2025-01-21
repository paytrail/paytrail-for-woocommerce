<?php
/**
 * Provider form view
 */

// Ensure that the file is being run within the WordPress context.
if (! defined('ABSPATH')) {
	die;
}
$allowed_html = array(
	'a'      => array(
		'href'  => array(),
		'title' => array(),
	)
);

wp_enqueue_style('paytrail-woocommerce-payment-fields');
wp_enqueue_script('paytrail-woocommerce-payment-fields');
wp_enqueue_script('paytrail-woocommerce-paytrail-library');

// Something went wrong loading the providers.
if (! empty($data['error'])) {
	printf(
		'<p class="paytrail-for-woocommerce-payment-fields__error">%s</p>',
		esc_html($data['error'])
	);
	return;
}

// Terms
$terms_link = $data['terms'];
echo '<div class="checkout-terms-link">' . wp_kses($terms_link, $allowed_html) . '</div>';
array_walk($data['groups'], function ( $group) {
	if (\Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled() && 'creditcard' == $group['id']) {
		echo '<div class="paytrail-provider-group">';
	} elseif (\Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled()) {
		echo '<div class="paytrail-provider-group" style="display:none;">';
	} else {
		echo '<div class="paytrail-provider-group">';
	}
	$providers_list = [];
	//var_dump($group['providers']);
	echo '<style type="text/css">';
	foreach ($group['providers'] as $key => $provider) {
		// Create simple list of provider names only
		$providers_list[] = $provider->getName();
		// Styles for group icons
		$group_id =  $group['id'];
		$group_icon = $group['icon'];
		if (0 === $key) { ?>
			.payment_method_paytrail .paytrail-provider-group-title.<?php echo esc_html($group_id); ?> i {
				background: url(<?php echo esc_html($group_icon); ?>) no-repeat;
				background-size: 28px 28px;
				background-position-y: center;
			}
			.payment_method_paytrail .paytrail-provider-group.selected .paytrail-provider-group-title.<?php echo esc_html($group_id); ?> i {
				background: url(<?php echo esc_html($group_icon); ?>) no-repeat;
				background-size: 28px 28px;
				background-position-y: center;
			}
			<?php
		}

	}
	echo '</style>';
	echo '<div class="paytrail-provider-group-title ' . esc_attr($group['id']) . '">';
	echo '<i></i>';
	echo esc_html($group['name']);
	echo '</div>';
	echo '<div class="provider-list">';
	echo esc_html(implode(', ', $providers_list));
	echo '</div>';
	echo '</div>';
	echo '<ul class="paytrail-woocommerce-payment-fields hidden">';
	if (!\Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled()) {
		array_walk($group['providers'], function ( $provider) {
			echo '<li class="paytrail-woocommerce-payment-fields--list-item' . ( $provider->getId() === 'apple-pay' ? ' apple-pay' : '' ) . '">';
			echo '<label>';
			echo '<input class="paytrail-woocommerce-payment-fields--list-item--input" type="radio" name="payment_provider" value="' . esc_attr($provider->getId()) . '">';
			echo '<div class="paytrail-woocommerce-payment-fields--list-item--wrapper">';
			echo '<img class="paytrail-woocommerce-payment-fields--list-item--img" src="' . esc_url($provider->getSvg()) . '">';
			echo '</div>';
			echo '</label>';
			echo '</li>';
		});
	}
	if (is_user_logged_in() && 'creditcard' == $group['id']) {
		( new \Paytrail\WooCommercePaymentGateway\Gateway() )->render_saved_payment_methods();
	} elseif (get_option('users_can_register') == 1 && 'creditcard' == $group['id']) {
		$mypage_link = get_permalink(wc_get_page_id('myaccount'));
		echo '<p class="add-card-login-description">';
		/* translators: %s - Logging link */
		echo sprintf(__('You can save your card details for next time by <a href="%s">logging in to the store or by creating an account.</a>'), esc_html($mypage_link));
		echo '</p>';
	}
	echo '</ul>';
});

// @todo move this where it is more suitable
// toggle payment method group sections' visibility
// add class to handle different theme layouts 2 or 5 items per row
echo "
<script>
	if (typeof initPaytrail === 'function'){
		initPaytrail();
	}
</script>
";
