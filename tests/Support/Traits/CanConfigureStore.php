<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use Paytrail\WooCommercePaymentGateway\Plugin;

/**
 * Store-level fixtures for the Integration suite: base location, currency, tax
 * options, tax rates and the gateway settings option.
 */
trait CanConfigureStore {

	/** The gateway settings option name. */
	public const GATEWAY_SETTINGS_OPTION = 'woocommerce_paytrail_settings';

	/** Applies a store configuration. */
	protected function configureStore( array $args = [] ): void {
		$args = array_merge(
			[
				'country'            => 'FI',
				'currency'           => 'EUR',
				'calc_taxes'         => true,
				'prices_include_tax' => false,
				'tax_based_on'       => 'billing',
				'ship_to_countries'  => '',
			],
			$args
		);

		update_option( 'woocommerce_default_country', $args['country'] );
		update_option( 'woocommerce_currency', $args['currency'] );
		update_option( 'woocommerce_calc_taxes', $args['calc_taxes'] ? 'yes' : 'no' );
		update_option( 'woocommerce_prices_include_tax', $args['prices_include_tax'] ? 'yes' : 'no' );
		update_option( 'woocommerce_tax_based_on', $args['tax_based_on'] );
		update_option( 'woocommerce_ship_to_countries', $args['ship_to_countries'] );

		$this->flushStoreCaches();
	}

	/** A FI / EUR store with a single 25.5% VAT rate, which is Finland's standard rate. */
	protected function configureFinnishStore(): int {
		$this->configureStore();

		return $this->haveTaxRate(
			[
				'tax_rate_country' => 'FI',
				'tax_rate'         => '25.5000',
				'tax_rate_name'    => 'ALV',
			]
		);
	}

	/**
	 * Ensures the WooCommerce pages a real store has exist and are pointed at. WPLoader's
	 * install leaves the page ids in the options but no pages behind them, and the plugin
	 * builds every redirect URL it sends Paytrail off these pages.
	 */
	protected function haveStorePages(): void {
		// Pretty permalinks, so a redirect URL is the page's slug rather than a post id
		// that changes every test. WP_Rewrite reads the structure once, so it is told again.
		if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
		}

		$GLOBALS['wp_rewrite']->init();

		$pages = [
			'cart'     => [ 'Cart', '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->' ],
			'checkout' => [ 'Checkout', '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->' ],
			'terms'    => [ 'Terms and conditions', 'The terms a shopper agrees to.' ],
		];

		foreach ( $pages as $slug => list( $title, $content ) ) {
			$option  = "woocommerce_{$slug}_page_id";
			$page_id = (int) get_option( $option );

			if ( $page_id > 0 && get_post( $page_id ) instanceof \WP_Post ) {
				continue;
			}

			// KSES strips block delimiters, which are HTML comments, from anyone who may not
			// post unfiltered HTML. That is nobody in CLI.
			kses_remove_filters();

			$page_id = wp_insert_post(
				[
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_content' => $content,
				]
			);

			kses_init_filters();

			update_option( $option, $page_id );
		}
	}

	/** Inserts a tax rate. */
	protected function haveTaxRate( array $rate ): int {
		$rate_id = \WC_Tax::_insert_tax_rate(
			array_merge(
				[
					'tax_rate_country'  => '',
					'tax_rate_state'    => '',
					'tax_rate'          => '0.0000',
					'tax_rate_name'     => 'Tax',
					'tax_rate_priority' => 1,
					'tax_rate_compound' => 0,
					'tax_rate_shipping' => 1,
					'tax_rate_order'    => 0,
					'tax_rate_class'    => '',
				],
				$rate
			)
		);

		$this->flushStoreCaches();

		return (int) $rate_id;
	}

	/** Creates a tax class and a rate for it, and returns the class slug. */
	protected function haveTaxClass( string $name, string $rate, string $country = 'FI' ): string {
		$existing = \WC_Tax::get_tax_class_slugs();
		$slug     = sanitize_title( $name );

		if ( ! in_array( $slug, $existing, true ) ) {
			\WC_Tax::create_tax_class( $name, $slug );
		}

		$this->haveTaxRate(
			[
				'tax_rate_country' => $country,
				'tax_rate'         => $rate,
				'tax_rate_name'    => $name,
				'tax_rate_class'   => $slug,
			]
		);

		return $slug;
	}

	/** Removes every tax rate in the store. */
	protected function deleteAllTaxRates(): void {
		global $wpdb;

		$rate_ids = $wpdb->get_col( "SELECT tax_rate_id FROM {$wpdb->prefix}woocommerce_tax_rates" ); // phpcs:ignore

		foreach ( $rate_ids as $rate_id ) {
			\WC_Tax::_delete_tax_rate( (int) $rate_id );
		}

		$this->flushStoreCaches();
	}

	/** Overwrites the gateway settings option and rebuilds the gateway from it. */
	protected function setGatewaySettings( array $settings ): void {
		update_option( self::GATEWAY_SETTINGS_OPTION, $settings );

		// WPLoader rolls each test back, leaving the options cache and the table
		// disagreeing, so the next read would be a previous test's settings.
		wp_cache_delete( self::GATEWAY_SETTINGS_OPTION, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );

		$this->reloadGateway();
	}

	/**
	 * The settings a configured store carries. In test mode the plugin ignores
	 * `merchant_id` / `secret_key` and signs with Paytrail's published test merchant,
	 * so a test needs no credentials of its own.
	 */
	protected function haveGatewaySettings( array $overrides = [] ): void {
		$this->setGatewaySettings(
			array_merge(
				[
					'enabled'          => 'yes',
					'enable_test_mode' => 'yes',
				],
				$overrides
			)
		);
	}

	/** The gateway settings for a store signing with its own merchant account. */
	protected function haveLiveGatewaySettings( array $overrides = [] ): void {
		$this->haveGatewaySettings(
			array_merge(
				[
					'enable_test_mode' => 'no',
					'merchant_id'      => '695874',
					'secret_key'       => 'MONIKAUPPIAS',
				],
				$overrides
			)
		);
	}

	/**
	 * Rebuilds the gateway so it picks up changed settings, and re-points its new SDK
	 * client at the API fake. The gateway reads its options once in its constructor and
	 * `Plugin` caches the instance for the whole process.
	 */
	protected function reloadGateway(): void {
		$gateway = new \ReflectionProperty( Plugin::class, 'gateway' );
		$gateway->setAccessible( true );
		$gateway->setValue( Plugin::instance(), null );

		$this->interceptPaytrailApi();

		// WooCommerce holds its own copies of the gateway objects.
		WC()->payment_gateways()->init();
	}

	/** Forgets the gateway state the WooCommerce session carries between requests. */
	protected function resetGatewaySession(): void {
		foreach ( [ 'payment_provider' ] as $key ) {
			WC()->session->__unset( $key );
		}
	}

	/** Invalidates the WooCommerce caches that outlive a transaction rollback. */
	protected function flushStoreCaches(): void {
		\WC_Cache_Helper::invalidate_cache_group( 'taxes' );
		\WC_Cache_Helper::get_transient_version( 'shipping', true );
	}
}
