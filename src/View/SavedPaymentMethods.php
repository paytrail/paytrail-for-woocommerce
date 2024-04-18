<?php
/**
 * Paytrail for Woocommerce payment Display View File
 */

use Paytrail\WooCommercePaymentGateway\Gateway;
use Paytrail\WooCommercePaymentGateway\Plugin;
use Paytrail\WooCommercePaymentGateway\Router;

// Ensure that the file is being run within the WordPress context.
if (!defined('ABSPATH')) {
	die;
}

$saved_methods = wc_get_customer_saved_methods_list(get_current_user_id());
$has_methods = (bool) $saved_methods;

if (\Paytrail\WooCommercePaymentGateway\Helper::getIsChangeSubscriptionPaymentMethod()) {
	$add_card_form_url = Router::get_url(Plugin::CARD_ENDPOINT, 'add') . '?change_payment_method=1';
} else {
	$add_card_form_url = Router::get_url(Plugin::CARD_ENDPOINT, 'add');
}

$delete_card_url = Router::get_url(Plugin::CARD_ENDPOINT, 'delete');
?>
<div class="paytrail-provider-group-title mobile paytrail-for-woocommerce-tokenized-payment-methods-saved-payment-methods-title">
	<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
		<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="add_card_pay" id="add_card_pay">
		<span class="woocommerce-terms-and-conditions-checkbox-text"><?php esc_html_e('Pay with new card and save your card details for next time', 'paytrail-for-woocommerce'); ?></span>
	</label>
	<input type="hidden" name="add_card_pay-field" value="1">
</div> 
<div class="paytrail-provider-group-title mobile paytrail-for-woocommerce-tokenized-payment-methods-saved-payment-methods-title">   
	<?php esc_html_e('Pay with saved card', 'paytrail-for-woocommerce'); ?>
</div>
<?php if ($has_methods) : ?>
	<?php ( new Gateway() )->saved_payment_methods(); ?>
	<a class="paytrail-for-woocommerce-tokenized-payment-method-links delete-card-button button"
		 href="#"><?php esc_html_e('Delete selected card', 'paytrail-for-woocommerce'); ?></a>
<?php endif; ?>
<!--<a class="paytrail-for-woocommerce-tokenized-payment-method-links add-card-button button"
	 href="<?php echo esc_url($add_card_form_url); ?>">
	<span class="paytrail-for-woocommerce-tokenized-payment-add-card-button dashicons dashicons-plus"></span>
	<?php esc_html_e('Add New Card', 'paytrail-for-woocommerce'); ?>
</a>-->

<script>
	jQuery(document).ready(function () {
		openTokenizedCardProviderGroupSelection();
	});
	function openTokenizedCardProviderGroupSelection() {
		jQuery("input.paytrail-for-woocommerce-tokenized-payment-method-input[type='radio']").each(function () {
			let creditCardProviderGroup = jQuery('.paytrail-provider-group-title.creditcard').parent();

			if (jQuery(this).prop('checked')) {
				jQuery(creditCardProviderGroup).closest(creditCardProviderGroup).addClass('selected');
				jQuery(this).closest('.paytrail-woocommerce-payment-fields').removeClass('hidden');
			}
		});
	}

	jQuery('.paytrail-woocommerce-payment-fields input[type=radio]').click(function () {
		jQuery('.paytrail-woocommerce-payment-fields input[type=radio]:checked').not(this).prop('checked', false);
	});

	jQuery(".paytrail-for-woocommerce-tokenized-payment-method-links.delete-card-button").click(function (evt) {
		evt.preventDefault();
		let cardTokenId = jQuery("input[name='wc-paytrail-payment-token']:checked").val();

		jQuery.ajax({
			type: 'POST',
			contentType: 'application/json',
			url: '<?php echo esc_url_raw($delete_card_url); ?>',
			data: JSON.stringify({token_id: cardTokenId}),
			success: function (response) {
				if (response.success) {
					jQuery('body').trigger('update_checkout')
				}
			}
		})
	});
</script>
