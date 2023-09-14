<?php
/**
 * Paytrail for Woocommerce payment router class
 */

namespace Paytrail\WooCommercePaymentGateway;

use Paytrail\WooCommercePaymentGateway\Controllers\Callback;
use Paytrail\WooCommercePaymentGateway\Controllers\Card;
use Paytrail\WooCommercePaymentGateway\Controllers\CardCancel;
use Paytrail\WooCommercePaymentGateway\Controllers\CardSuccess;

class Router {

	const ACTION_BASE_URL = 'paytrail-action';

	const ROUTE_BASE_URL = 'paytrail-route';

	/**
	 * Router constructor.
	 */
	public function __construct() {
		add_filter('init', [$this, 'register_rewrites']);
		add_filter('template_include', [$this, 'routes']);
	}

	/**
	 * Get route action url
	 *
	 * @param $route
	 * @param $action
	 * @return string
	 */
	public static function get_url( $route, $action) {
		$home_url = esc_url(home_url('/'));
		$base_url = Plugin::BASE_URL;
		$route_base_url = self::ROUTE_BASE_URL;
		$action_base_url = self::ACTION_BASE_URL;

		if (!get_option('permalink_structure')) {
			return "{$home_url}index.php?{$route_base_url}={$route}&{$action_base_url}={$action}";
		}

		return "{$home_url}{$base_url}{$route}/{$action}";
	}

	/**
	 * Register router rewrites
	 */
	public function register_rewrites() {
		$base_url = Plugin::BASE_URL;
		$action_base_url = self::ACTION_BASE_URL;
		$route_base_url = self::ROUTE_BASE_URL;
		add_rewrite_rule('paytrail/([^/]*)/([^/]*)/?$', 'index.php?' . $route_base_url . '=$matches[1]&' . $action_base_url . '=$matches[2]', 'top');
		add_rewrite_tag("%$route_base_url%", '([^&]+)');
		add_rewrite_tag("%$action_base_url%", '([^&]+)');
	}

	/**
	 * Get routes
	 *
	 * @param $template
	 * @return mixed
	 */
	public function routes( $template) {
		$route = get_query_var(self::ROUTE_BASE_URL);

		if (!$route) {
			return $template;
		}
		$action = !empty(get_query_var(self::ACTION_BASE_URL)) ? get_query_var(self::ACTION_BASE_URL) : 'index';
		switch ($route) {
			case Plugin::ADD_CARD_REDIRECT_SUCCESS_URL:
				$controller = new CardSuccess();
				break;
			case Plugin::ADD_CARD_REDIRECT_CANCEL_URL:
				$controller = new CardCancel();
				break;
			case Plugin::CARD_ENDPOINT:
				$controller = new Card();
				break;
			case Plugin::CALLBACK_URL:
				$controller = new Callback();
				break;
			default:
				echo esc_html('Route did not match');
		}

		$controller->execute($action);

		return $template;
	}
}
