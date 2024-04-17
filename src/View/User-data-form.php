<div class="wrap" style="display:none">
	<div class="paytrail-registration-container">
		<form id="user-data-form" method="get" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<p><?php esc_html_e('To begin the Paytrail registration process, please review the following information', 'paytrail-for-woocommerce'); ?></p>
			<div class="form-columns">
				<div class="form-column">
					<div class="form-group">
						<label for="firstname"><?php esc_html_e('First Name:', 'paytrail-for-woocommerce'); ?>:</label>
						<input type="text" name="firstname" id="firstname" value="<?php echo esc_attr($first_name); ?>" placeholder="First Name">
					</div>
					<div class="form-group">
						<label for="lastname"><?php esc_html_e('Last Name:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="lastname" id="lastname" value="<?php echo esc_attr($last_name); ?>" placeholder="Last Name">
					</div>
					<div class="form-group">
						<label for="email"><?php esc_html_e('Email:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="email" id="email" value="<?php echo esc_attr($user_email); ?>" placeholder="Email">
					</div>
					<div class="form-group">
						<label for="puhelinnumero"><?php esc_html_e('Phone:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="puhelinnumero" id="puhelinnumero" value="<?php echo esc_attr($phone_number); ?>" placeholder="Phone number">
					</div>
				</div>
				<div class="form-column">
					<div class="form-group">
						<label for="verkkokaupan_www_osoite"><?php esc_html_e('Site URL:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="verkkokaupan_www_osoite" id="verkkokaupan_www_osoite" value="<?php echo esc_attr($site_url); ?>" placeholder="Site URL">
					</div>
					<div class="form-group">
						<label for="company"><?php esc_html_e('Company Name:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="company" id="company" value="<?php echo esc_attr($company_name); ?>" placeholder="Company Name">
					</div>
					<div class="form-group">
						<label for="address"><?php esc_html_e('Shop Address:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="address" id="address" value="<?php echo esc_attr($shop_address); ?>" placeholder="Shop Address">
					</div>
					<div class="form-group">
						<label for="city"><?php esc_html_e('Shop City:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="city" id="city" value="<?php echo esc_attr($shop_city); ?>" placeholder="Shop City">
					</div>
					<div class="form-group">
						<label for="postinumero"><?php esc_html_e('Shop Postcode:', 'paytrail-for-woocommerce'); ?></label>
						<input type="text" name="postinumero" id="postinumero" value="<?php echo esc_attr($shop_postcode); ?>" placeholder="Shop Postcode">
					</div>
				</div>
			</div>
			<input type="submit" style=" background-color: #D92D83;color: #fff;border:none;padding:5px 15px;border-radius:10px;font-size:14px;;margin-right: 8px;text-transform:uppercase;font-weight: bold;" class="button-primary" value="Submit">
		</form>
		<button id="close-lightbox"><?php esc_html_e('Close', 'paytrail-for-woocommerce'); ?></button>
	</div>
</div>
