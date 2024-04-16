<div id="paytrail-overlay-container">
	<div class="paytrail-intro-container">
		<div class="paytrail-intro-header">
			<div style="padding:25px;">
				<div class="paytrail-intro-logo-container">
					<img style="max-width:100px" src="https://www.paytrail.com/hubfs/images/Banners_and_logos/logo-white.svg"/>
				</div>
				<div class="paytrail-intro-title-container">
					<h2><?php /* translators: Get started with Paytrail */ esc_html_e('Get started with Paytrail', 'paytrail-for-woocommerce'); ?></h2>
					<p><?php /* translators: To start accepting payments with Paytrail, you need an account. */ esc_html_e('To start accepting payments with Paytrail, you need an account.', 'paytrail-for-woocommerce'); ?></p>
				</div>
			</div>
		</div>
		<div style="width:calc(50% - 2px);background-color:#fff;;border:1px solid #D92D83;">
			<div style="padding:25px 90px;text-align:center;">
				<h3 style="font-size:20px;"><?php /* translators: I need an account */ esc_html_e('I need an account', 'paytrail-for-woocommerce'); ?></h3>
				<p style="font-size:15px;line-height:1.2;"><?php esc_html_e('You can start our fast on-boarding process by clicking the register button', 'paytrail-for-woocommerce'); ?></p>
				<button class="modern-button" id="open-lightbox"><?php esc_html_e('Register now', 'paytrail-for-woocommerce'); ?></button>
				<?php
				$sales_link = 'https://www.paytrail.com/en/contact-us';
				?>
				<p style="font-size:15px;">
					<?php /* translators: Or <a href="%s" target="_blank">contact sales</a> */ echo sprintf(__('Or <a href="%s" target="_blank">contact sales</a>'), esc_html($sales_link)); ?>
				</p>
			</div>
		</div>
		<div style="width:calc(50% - 2px);background-color:#F3C0D9;border:1px solid #D92D83;align-items:center;display:flex;">
			<div style="padding:25px 90px;text-align:center;">
				<h3 style="font-size:20px;"><?php /* translators: I already have an account */ esc_html_e('I already have an account', 'paytrail-for-woocommerce'); ?></h3>
				<button class="modern-button" id="credentials"><?php esc_html_e('Add credentials', 'paytrail-for-woocommerce'); ?></button>
				<?php
				$merchant_link = 'https://merchant.paytrail.com/';
				?>
				<p style="font-size:15px;">
					<?php /* translators: Credentials can be found in the <a href="%s" target="_blank">merchant panel</a> */ echo sprintf(__('Credentials can be found in the <a href="%s" target="_blank">merchant panel</a>'), esc_html($merchant_link)); ?>
				</p>
			</div>
		</div>
		<div classs="paytrail-intro-support" style="display:flex;align-items: center;justify-content:center;width:100%;background-color:#D92D83;">
			<?php
			$customer_service_link = 'https://www.paytrail.com/en/customer-service#merchants/';
			?>
			<h2 style="font-weight:300;color:#fff;">
				<?php /* translators: <b>Need help?</b> Dont hesitate to <a href="%s" target="_blank" style="color:#fff;">contact support!</a> */ echo sprintf(__('<b>Need help?</b> Dont hesitate to <a href="%s" target="_blank" style="color:#fff;">contact support!</a>'), esc_html($customer_service_link)); ?>
			</h2>
		</div>
		<div classs="paytrail-intro-footer" style="display:flex;align-items: center;justify-content:center;padding-top:25px;padding-bottom:25px;width:calc(100% - 2px);background-color:#fff;;border:1px solid #D92D83;">
			<div style="display:inline-block;width:24%;text-align:center;font-size:0px;">
				<p style="font-weight:bold;font-size:15px;margin-bottom:0px;margin-top:0px;"><?php esc_html_e('I just want to try it', 'paytrail-for-woocommerce'); ?></p>
			</div>
			<div style="display:inline-block;width:50%;font-size:0px;">
				<p style="font-size:14px;"><?php esc_html_e('Activate the test mode and try out the test credentials for various payment methods from our API documentation', 'paytrail-for-woocommerce'); ?></p>
			</div>
			<div style="display:inline-block;width:25%;font-size:0px;text-align:center">
				<button class="modern-button gray" id="test-mode-button"><?php esc_html_e('Test mode', 'paytrail-for-woocommerce'); ?></button>
			</div>
		</div>
	</div>
</div>
