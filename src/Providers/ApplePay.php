<?php

namespace Paytrail\WooCommercePaymentGateway\Providers;

use Paytrail\SDK\Model\Provider;
use Paytrail\WooCommercePaymentGateway\Plugin;
use WP_Error;

class ApplePay {

	
	/**
	 * Create Apple Pay provider for ProviderForm view
	 * 
	 * @return Provider
	 */
	public static function create_provider() {
		$apple_pay = new Provider();
		$icon_url  = sprintf('%s/assets/img/icon_apple_pay.svg', Plugin::plugin_url());
		$apple_pay->setUrl(null);
		$apple_pay->setIcon($icon_url);
		$apple_pay->setSvg($icon_url);
		$apple_pay->setName('Apple Pay');
		$apple_pay->setGroup('mobile');
		$apple_pay->setId('apple-pay');
		$apple_pay->setParameters(null);
		return $apple_pay;
	}

	/**
	 * Create Apple Developer Merchant ID Domain Association file
	 * 
	 * @return bool|WP_Error
	 */
	public static function verification_file() {
		$plugin_dir_url       = Plugin::instance()->get_plugin_dir_url();
		$source_file          = sprintf('%s/apple-developer-merchantid-domain-association', $plugin_dir_url);
		$dest_file            = sprintf('%s.well-known/apple-developer-merchantid-domain-association', ABSPATH);
		$apple_well_known_dir = trailingslashit(sprintf('%s.well-known', ABSPATH)); 

		if (!is_dir($apple_well_known_dir)) {
			if (!mkdir($apple_well_known_dir, 0755, false)) {
				return new WP_Error('apple_pay_ver_file_not_created', 'Apple Pay verification file directory could not be created');
			}
		}
		if (!file_exists($dest_file)) {
			if (!copy($source_file, $dest_file)) {
				return new WP_Error('apple_pay_ver_file_not_created', 'Apple Pay verification file could not be created');
			}
		}
		//Check if the file is accessible
		$url      = sprintf('%s.well-known/apple-developer-merchantid-domain-association', home_url('/', 'https'));
		$response = wp_remote_get($url);
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new WP_Error('apple_pay_ver_file_not_accessible', 'Apple Pay verification file is not accessible');
		} else {
			return true;
		}
	}

	/**
	 * Title for settings page
	 * 
	 * @return string
	 */
	public static function settings_title() {
		$icon_url = sprintf('%s/assets/img/icon_apple_pay_admin.svg', Plugin::plugin_url());

		return sprintf(
			__('Apple Pay', 'paytrail-for-woocommerce') . '<br><img src="%s" alt="Apple Pay" style="width: 70px; height: auto; margin-top: 5px;">',
			esc_url($icon_url)
		);
	}

	/**
	 * Description for settings page
	 * 
	 * @param  bool $apple_pay_active Whether Apple Pay is activated in settings
	 * @return string
	 */
	public static function settings_description( $apple_pay_active) {
		$description = __('Requires activation and domain registration in the Paytrail Merchant panel, see <a target="_blank" href="https://support.paytrail.com/hc/fi/articles/4412478752017-Apple-Pay#h_01HQB1N8X624HCDX1TZ6MNHGYH">instructions</a>. Verification file is added by the plugin.', 'paytrail-for-woocommerce');
		$success     = __('Verification file found', 'paytrail-for-woocommerce');
		$error       = __('Verification file not found, please save the settings again', 'paytrail-for-woocommerce');

		if (is_admin() && $apple_pay_active) {
			if (!is_wp_error(self::verification_file())) {
				return $description . '<p class="description" style="color: green">' . $success . '</p>';
			}
			return $description . '<p class="description" style="color: red">' . $error . '</p>';
		}
		return $description;
	}

	/**
	 * Add Apple Pay provider to ProviderForm view mobile group and create the group if it doesn't exist
	 * 
	 * @param  array $providers Providers array
	 * @return array $providers
	 */
	public static function add_provider( $providers) {
		$mobile_group_added = false;

		// Check if the mobile group already exists and add the Apple Pay provider
		foreach ($providers['groups'] as $key => $group) {
			if (isset($group['id']) && 'mobile' === $group['id']) {
				$providers['groups'][$key]['providers'][] = self::create_provider();
				$mobile_group_added                       = true;
				break;
			}
		}

		// Only add the mobile group if it doesn't exist
		if (!$mobile_group_added) {
			$mobile_icon_url = esc_url('https://resources.paytrail.com/images/payment-group-icons/mobile.svg');
			$mobile_group    = [
				'id' => 'mobile',
				'name' => __('Mobile payment methods', 'paytrail-for-woocommerce'),
				'icon' => $mobile_icon_url,
				'svg' => $mobile_icon_url,
				'providers' => [self::create_provider()],
			];
			$insert_position = !empty($providers['groups']) ? 1 : 0;
			array_splice($providers['groups'], $insert_position, 0, [$mobile_group]);
		}
		return $providers;
	}
}
