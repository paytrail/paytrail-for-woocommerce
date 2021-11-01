<?php
/**
 * Provider form view
 */

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}
$allowed_html = array(
    'a'      => array(
        'href'  => array(),
        'title' => array(),
    )
);

wp_enqueue_style( 'paytrail-woocommerce-payment-fields' );
wp_enqueue_script( 'paytrail-woocommerce-payment-fields' );

// Something went wrong loading the providers.
if ( ! empty( $data['error'] ) ) {
    printf(
        '<p class="paytrail-for-woocommerce-payment-fields__error">%s</p>',
        esc_html( $data['error'] )
    );
    return;
}

// Terms
$terms_link = $data['terms'];
echo '<div class="checkout-terms-link">' . wp_kses($terms_link,$allowed_html) . '</div>';

array_walk( $data['groups'], function( $group ) {
    echo '<div class="provider-group">';
    $providers_list = [];
    //var_dump($group['providers']);
    echo '<style type="text/css">';
    foreach( $group['providers'] as $key => $provider ) {
        // Create simple list of provider names only
        $providers_list[] = $provider->getName();
        // Styles for group icons
        $group_id =  $group['id'];
        $group_icon = $group['icon'];
        if ($key === 0) {

            echo <<<EOL
            .payment_method_paytrail .provider-group-title.$group_id i {
                background: url($group_icon) no-repeat;
                background-size: 28px 28px;
                background-position-y: center;
            }
            .payment_method_paytrail .provider-group.selected .provider-group-title.$group_id i {
                background: url($group_icon) no-repeat;
                background-size: 28px 28px;
                background-position-y: center;
            }
EOL;
        }
        
    }
    echo '</style>';
    echo '<div class="provider-group-title ' . esc_html($group['id'])  . '">';
    echo '<i></i>';
    echo esc_html( $group['name'] );
    echo '</div>';
    echo '<div class="provider-list">';
    echo esc_html(implode( ', ', $providers_list ));
    echo '</div>';
    echo '</div>';
    echo '<ul class="paytrail-woocommerce-payment-fields hidden">';
    if (!\Paytrail\WooCommercePaymentGateway\Helper::getIsSubscriptionsEnabled()) {
        array_walk( $group['providers'], function ($provider) {
            echo '<li class="paytrail-woocommerce-payment-fields--list-item">';
            echo '<label>';
            echo '<input class="paytrail-woocommerce-payment-fields--list-item--input" type="radio" name="payment_provider" value="' . esc_attr($provider->getId()) . '">';
            echo '<div class="paytrail-woocommerce-payment-fields--list-item--wrapper">';
            echo '<img class="paytrail-woocommerce-payment-fields--list-item--img" src="' . esc_url($provider->getSvg()) . '">';
            echo '</div>';
            echo '</label>';
            echo '</li>';
        });
    }
    if (is_user_logged_in() && $group['id'] == 'creditcard') {
        (new \Paytrail\WooCommercePaymentGateway\Gateway)->render_saved_payment_methods();
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