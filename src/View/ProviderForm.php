<?php
/**
 * Provider form view
 */

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
$allowed_html = array(
	'a' => array(
		'href'  => array(),
		'title' => array(),
	),
);

wp_enqueue_style( 'paytrail-woocommerce-payment-fields' );
wp_enqueue_script( 'paytrail-woocommerce-payment-fields' );

// Something went wrong loading the providers.
if ( ! empty( $data['error'] ) ) {
	printf(
		'<p class="paytrail-for-woocommerce-payment-fields__error" role="alert">%s</p>',
		esc_html( $data['error'] )
	);
	return;
}

// Terms
$terms_link = $data['terms'];
echo '<div class="checkout-terms-link" aria-label="' . esc_attr__( 'Payment terms', 'paytrail-for-woocommerce' ) . '">' . wp_kses( $terms_link, $allowed_html ) . '</div>';
array_walk(
	$data['groups'],
	function ( $group ) {
		if ( \Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled() && 'creditcard' == $group['id'] ) {
			echo '<div class="paytrail-provider-group" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">';
		} elseif ( \Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled() ) {
			echo '<div class="paytrail-provider-group" style="display:none;">';
		} else {
			echo '<div class="paytrail-provider-group" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">';
		}
		$providers_list = array();
		// var_dump($group['providers']);
		echo '<style type="text/css">';
		foreach ( $group['providers'] as $key => $provider ) {
			// Create simple list of provider names only
			$providers_list[] = $provider->getName();
			// Styles for group icons
			$group_id   = $group['id'];
			$group_icon = $group['icon'];
			if ( 0 === $key ) { ?>
			.payment_method_paytrail .paytrail-provider-group-title.<?php echo esc_html( $group_id ); ?> i {
				background: url(<?php echo esc_html( $group_icon ); ?>) no-repeat;
				background-size: 28px 28px;
				background-position-y: center;
			}
			.payment_method_paytrail .paytrail-provider-group.selected .paytrail-provider-group-title.<?php echo esc_html( $group_id ); ?> i {
				background: url(<?php echo esc_html( $group_icon ); ?>) no-repeat;
				background-size: 28px 28px;
				background-position-y: center;
			}
				<?php
			}
		}
		echo '</style>';
		echo '<div class="paytrail-provider-group-title ' . esc_attr( $group['id'] ) . '" id="paytrail-provider-group-title-' . esc_attr( $group['id'] ) . '">';
		echo '<i aria-hidden="true"></i>';
		echo esc_html( $group['name'] );
		echo '</div>';
		echo '<div class="provider-list">';
		echo esc_html( implode( ', ', $providers_list ) );
		echo '</div>';
		echo '</div>';
		echo '<ul class="paytrail-woocommerce-payment-fields hidden" aria-labelledby="paytrail-provider-group-title-' . esc_attr( $group['id'] ) . '">';
		if ( ! \Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled() ) {
			array_walk(
				$group['providers'],
				function ( $provider ) {
					$provider_name = $provider->getName();
					$provider_id   = $provider->getId();
					$provider_svg  = $provider->getSvg();

					// Create unique ID for credit cards
					$input_id = ( 'creditcard' === $provider_id ) ? 'creditcard-' . $provider_name : $provider_id;

					echo '<li class="paytrail-woocommerce-payment-fields--list-item">';
					echo '<label for="' . esc_attr( $input_id ) . '">';
					echo '<input id="' . esc_attr( $input_id ) . '" class="paytrail-woocommerce-payment-fields--list-item--input" type="radio" name="payment_provider" value="' . esc_attr( $provider_id ) . '" aria-label="' . esc_attr( $provider_name ) . '">';
					echo '<div class="paytrail-woocommerce-payment-fields--list-item--wrapper" aria-hidden="true">';
					echo '<img class="paytrail-woocommerce-payment-fields--list-item--img" src="' . esc_url( $provider_svg ) . '" alt="' . esc_attr( $provider_name ) . '">';
					echo '</div>';
					echo '</label>';
					echo '</li>';
				}
			);
		}
		if ( is_user_logged_in() && 'creditcard' == $group['id'] ) {
			\Paytrail\WooCommercePaymentGateway\Gateway::render_saved_payment_methods();
		} elseif ( get_option( 'users_can_register' ) == 1 && 'creditcard' == $group['id'] ) {
			$mypage_link = get_permalink( wc_get_page_id( 'myaccount' ) );
			echo '<p class="add-card-login-description" role="link">';
			printf(
				wp_kses(
					/* translators: %s - My account / login URL */
					__( 'You can save your card details for next time by <a href="%s">logging in to the store or by creating an account.</a>', 'paytrail-for-woocommerce' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url( $mypage_link )
			);
			echo '</p>';
		}
		echo '</ul>';
	}
);

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
