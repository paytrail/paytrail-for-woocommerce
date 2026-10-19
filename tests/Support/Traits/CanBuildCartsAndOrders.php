<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use Paytrail\WooCommercePaymentGateway\Plugin;

/** Cart, customer and order fixtures for the Integration suite. */
trait CanBuildCartsAndOrders {

	/** Address fields WC_Customer / WC_Order expose setters for, per context. */
	private const BILLING_FIELDS = [
		'first_name',
		'last_name',
		'company',
		'address_1',
		'address_2',
		'city',
		'state',
		'postcode',
		'country',
		'email',
		'phone',
	];

	private const SHIPPING_FIELDS = [
		'first_name',
		'last_name',
		'company',
		'address_1',
		'address_2',
		'city',
		'state',
		'postcode',
		'country',
		'phone',
	];

	/** A complete Finnish billing address. */
	protected function finnishAddress( array $overrides = [] ): array {
		return array_merge(
			[
				'first_name' => 'Matti',
				'last_name'  => 'Meikalainen',
				'company'    => 'Krokedil Oy',
				'address_1'  => 'Mannerheimintie 12',
				'address_2'  => 'A 4',
				'city'       => 'Helsinki',
				'state'      => '',
				'postcode'   => '00100',
				'country'    => 'FI',
				'email'      => 'matti@example.com',
				'phone'      => '+358401234567',
			],
			$overrides
		);
	}

	/** A complete Swedish billing address, for a store selling across the border. */
	protected function swedishAddress( array $overrides = [] ): array {
		return array_merge(
			[
				'first_name' => 'Karl',
				'last_name'  => 'Karlsson',
				'company'    => 'Krokedil AB',
				'address_1'  => 'Storgatan 1',
				'address_2'  => 'Lgh 1102',
				'city'       => 'Goteborg',
				'state'      => '',
				'postcode'   => '411 06',
				'country'    => 'SE',
				'email'      => 'karl@example.com',
				'phone'      => '+46701234567',
			],
			$overrides
		);
	}

	/** A customer account, signed in so the store sees the shopper as that customer. */
	protected function haveSignedInCustomer(): int {
		$customer_id = (int) static::factory()->user->create( [ 'role' => 'customer' ] );
		wp_set_current_user( $customer_id );

		return $customer_id;
	}

	/** A customer account nobody is signed in as. */
	protected function haveCustomerAccount(): int {
		return (int) static::factory()->user->create( [ 'role' => 'customer' ] );
	}

	/** Signs the shopper out, leaving the store with a guest. */
	protected function haveGuestCustomer(): void {
		wp_set_current_user( 0 );
	}

	/** Blanks every address field on WC()->customer and then applies the given ones. */
	protected function haveCustomerAddress( array $billing = [], array $shipping = [] ): void {
		$customer = WC()->customer;

		$this->applyAddress( $customer, 'billing', array_fill_keys( self::BILLING_FIELDS, '' ) );
		$this->applyAddress( $customer, 'shipping', array_fill_keys( self::SHIPPING_FIELDS, '' ) );

		$this->applyAddress( $customer, 'billing', $billing );
		$this->applyAddress( $customer, 'shipping', $shipping );
	}

	/** Empties the cart and the shipping selection. */
	protected function emptyCart(): void {
		WC()->cart->empty_cart();
		WC()->session->set( 'chosen_shipping_methods', [] );

		$this->resetShippingCaches();
	}

	/** Clears the shipping state WooCommerce caches outside the database. */
	protected function resetShippingCaches(): void {
		\WC_Cache_Helper::get_transient_version( 'shipping', true );
		\WC_Cache_Helper::invalidate_cache_group( 'shipping_zones' );
		delete_transient( 'wc_shipping_method_count' );

		foreach ( array_keys( WC()->session->get_session_data() ) as $key ) {
			if ( 0 === strpos( (string) $key, 'shipping_for_package_' ) ) {
				WC()->session->__unset( $key );
			}
		}
	}

	/** Adds products to the cart and recalculates totals. */
	protected function haveCartWith( array $items ): void {
		foreach ( $items as $item ) {
			$product  = is_array( $item ) ? $item[0] : $item;
			$quantity = is_array( $item ) ? ( $item[1] ?? 1 ) : 1;

			WC()->cart->add_to_cart( $product->get_id(), $quantity );
		}

		$this->recalculateCart();
	}

	/** Registers a cart fee. */
	protected function haveCartFee( string $name, float $amount, bool $taxable = false, string $tax_class = '' ): void {
		add_action(
			'woocommerce_cart_calculate_fees',
			static function ( $cart ) use ( $name, $amount, $taxable, $tax_class ) {
				$cart->add_fee( $name, $amount, $taxable, $tax_class );
			}
		);

		$this->recalculateCart();
	}

	/** Creates a shipping zone with a single flat rate method and selects it. */
	protected function haveChosenFlatRateShipping( string $country = 'FI', string $cost = '5.90', string $tax_status = 'taxable' ): string {
		$zone = new \WC_Shipping_Zone();
		$zone->set_zone_name( "Test zone {$country}" );
		$zone->add_location( $country, 'country' );
		$instance_id = $zone->add_shipping_method( 'flat_rate' );
		$zone->save();

		update_option(
			"woocommerce_flat_rate_{$instance_id}_settings",
			[
				'title'      => 'Flat rate',
				'tax_status' => $tax_status,
				'cost'       => $cost,
			]
		);

		$rate_id = "flat_rate:{$instance_id}";

		$this->resetShippingCaches();
		WC()->shipping()->load_shipping_methods();
		WC()->session->set( 'chosen_shipping_methods', [ $rate_id ] );

		$this->recalculateCart();

		return $rate_id;
	}

	/** Makes WooCommerce believe the current request is the checkout page. */
	protected function simulateCheckoutPage(): void {
		add_filter( 'woocommerce_is_checkout', '__return_true' );
	}

	/** Recalculates shipping and totals for the current cart. */
	protected function recalculateCart(): void {
		WC()->cart->calculate_shipping();
		WC()->cart->calculate_totals();
	}

	/** Creates a saved WC_Order with items, shipping, fees and addresses. */
	protected function haveOrder( array $args = [] ): \WC_Order {
		$args = array_merge(
			[
				'items'            => [],
				'shipping'         => null,
				'fees'             => [],
				'billing'          => [],
				'shipping_address' => [],
				'currency'         => 'EUR',
				'status'           => null,
				'paid'             => false,
				'paytrail'         => false,
				'created_via'      => null,
			],
			$args
		);

		$order = new \WC_Order();

		if ( $args['currency'] ) {
			$order->set_currency( $args['currency'] );
		}

		if ( $args['created_via'] ) {
			$order->set_created_via( $args['created_via'] );
		}

		$this->applyAddress( $order, 'billing', $args['billing'] );
		$this->applyAddress( $order, 'shipping', $args['shipping_address'] );

		/*
		 * `[ $product, $quantity, $line_total ]`. The third entry overrides the line
		 * total while leaving the subtotal alone, which is what a discount looks like.
		 */
		foreach ( $args['items'] as $item ) {
			$product    = is_array( $item ) ? $item[0] : $item;
			$quantity   = is_array( $item ) ? ( $item[1] ?? 1 ) : 1;
			$line_total = is_array( $item ) ? ( $item[2] ?? null ) : null;

			$item_id = $order->add_product( $product, $quantity );

			if ( null !== $line_total ) {
				// Not from the database: the in-memory item is the one save() will write.
				$order->get_item( $item_id, false )->set_total( $line_total );
			}
		}

		if ( $args['shipping'] ) {
			$shipping = array_merge(
				[
					'method_title' => 'Flat rate',
					'method_id'    => 'flat_rate',
					'instance_id'  => 1,
					'total'        => '5.90',
				],
				$args['shipping']
			);

			$shipping_item = new \WC_Order_Item_Shipping();
			$shipping_item->set_method_title( $shipping['method_title'] );
			$shipping_item->set_method_id( $shipping['method_id'] );
			$shipping_item->set_instance_id( (string) $shipping['instance_id'] );
			$shipping_item->set_total( $shipping['total'] );
			$order->add_item( $shipping_item );
		}

		foreach ( $args['fees'] as $fee ) {
			$fee = array_merge(
				[
					'name'       => 'Handling fee',
					'total'      => '2.00',
					'tax_status' => 'taxable',
					'tax_class'  => '',
				],
				$fee
			);

			$fee_item = new \WC_Order_Item_Fee();
			$fee_item->set_name( $fee['name'] );
			$fee_item->set_amount( (string) $fee['total'] );
			$fee_item->set_total( (string) $fee['total'] );
			$fee_item->set_tax_status( $fee['tax_status'] );
			$fee_item->set_tax_class( $fee['tax_class'] );
			$order->add_item( $fee_item );
		}

		if ( false !== $args['paytrail'] ) {
			$gateway = is_array( $args['paytrail'] ) ? $args['paytrail'] : [];

			$this->applyGatewayMeta(
				$order,
				$gateway['transaction_id'] ?? 'paytrail-transaction-1',
				$gateway['reference'] ?? null,
				$gateway['provider'] ?? 'osuuspankki'
			);
		}

		if ( $args['paid'] ) {
			$order->set_date_paid( time() );
		}

		if ( $args['status'] ) {
			$order->set_status( $args['status'] );
		}

		$order->calculate_totals( true );
		$order->save();

		return $order;
	}

	/**
	 * A saved order that went through the gateway: one 100.00 product, a Finnish billing
	 * address and the plugin's own order meta.
	 */
	protected function havePaytrailOrder( array $args = [] ): \WC_Order {
		return $this->haveOrder(
			array_merge(
				[
					'items'    => [ [ $this->haveSimpleProduct( [ 'price' => '100.00' ] ), 1 ] ],
					'billing'  => $this->finnishAddress(),
					'paytrail' => true,
				],
				$args
			)
		);
	}

	/** A paid Paytrail order in processing, ready for an order management call. */
	protected function havePaidPaytrailOrder( array $args = [] ): \WC_Order {
		return $this->havePaytrailOrder(
			array_merge(
				[
					'paid'   => true,
					'status' => 'processing',
				],
				$args
			)
		);
	}

	/** Stamps an order with the meta a completed Paytrail purchase leaves behind. */
	protected function markAsPaytrailOrder( \WC_Order $order, string $transaction_id = 'paytrail-transaction-1', ?string $reference = null, string $provider = 'osuuspankki' ): \WC_Order {
		$this->applyGatewayMeta( $order, $transaction_id, $reference, $provider );
		$order->save();

		return $order;
	}

	/** Creates a saved card token for the gateway. */
	protected function haveCardToken( array $args = [] ): \WC_Payment_Token_CC {
		static $sequence = 0;
		++$sequence;

		$args = array_merge(
			[
				'token'        => "card-token-{$sequence}",
				'gateway_id'   => Plugin::GATEWAY_ID,
				'card_type'    => 'visa',
				'last4'        => '0024',
				'expiry_month' => '11',
				'expiry_year'  => '2032',
				'user_id'      => get_current_user_id(),
			],
			$args
		);

		$token = new \WC_Payment_Token_CC();
		$token->set_token( $args['token'] );
		$token->set_gateway_id( $args['gateway_id'] );
		$token->set_card_type( $args['card_type'] );
		$token->set_last4( $args['last4'] );
		$token->set_expiry_month( $args['expiry_month'] );
		$token->set_expiry_year( $args['expiry_year'] );
		$token->set_user_id( $args['user_id'] );
		$token->save();

		return $token;
	}

	/** The reference the callback looks an order up by. */
	protected function referenceOf( \WC_Order $order ): string {
		return (string) $order->get_meta( '_checkout_reference' );
	}

	/**
	 * Writes the plugin's order meta onto an order, without saving. The keys are the
	 * plugin's own storage keys, kept verbatim so the fixture matches what it reads.
	 */
	private function applyGatewayMeta( \WC_Order $order, string $transaction_id, ?string $reference, string $provider ): void {
		$reference = $reference ?? (string) $order->get_order_number();

		$order->set_payment_method( Plugin::GATEWAY_ID );
		$order->set_transaction_id( $transaction_id );
		$order->update_meta_data( '_checkout_reference', $reference );
		$order->update_meta_data( '_checkout_reference_' . $reference, true );
		$order->update_meta_data( '_checkout_payment_provider', $provider );
	}

	/**
	 * Refunds an amount through the gateway, the way "Refund via Paytrail" in wp-admin
	 * does: `process_refund()` first, then the `woocommerce_order_refunded` action the
	 * plugin does the actual work on.
	 *
	 * @return \WC_Order_Refund|\WP_Error
	 */
	protected function haveGatewayRefund( \WC_Order $order, float $amount, string $reason = '' ) {
		return wc_create_refund(
			[
				'order_id'       => $order->get_id(),
				'amount'         => $amount,
				'reason'         => $reason,
				'refund_payment' => true,
			]
		);
	}

	/** Refunds order items, the way "Refund" in wp-admin does. */
	protected function haveRefundForItems( \WC_Order $order, array $item_ids = [], string $reason = '', array $quantities = [] ): \WC_Order_Refund {
		if ( empty( $item_ids ) ) {
			$item_ids = array_keys( $order->get_items( [ 'line_item', 'shipping', 'fee' ] ) );
		}

		$line_items = [];
		$amount     = 0.0;

		foreach ( $item_ids as $item_id ) {
			$item       = $order->get_item( $item_id );
			$ordered    = max( 1, (int) $item->get_quantity() );
			$quantity   = $quantities[ $item_id ] ?? $ordered;
			$proportion = $quantity / $ordered;

			$tax = array_map(
				static function ( $value ) use ( $proportion ) {
					return round( (float) $value * $proportion, 2 );
				},
				$item->get_taxes()['total'] ?? []
			);

			$total = round( (float) $item->get_total() * $proportion, 2 );

			$line_items[ $item_id ] = [
				'qty'          => $quantity,
				'refund_total' => $total,
				'refund_tax'   => $tax,
			];

			$amount += $total + array_sum( $tax );
		}

		$refund = wc_create_refund(
			[
				'order_id'   => $order->get_id(),
				'amount'     => $amount,
				'reason'     => $reason,
				'line_items' => $line_items,
			]
		);

		if ( is_wp_error( $refund ) ) {
			throw new \RuntimeException( 'Could not create refund: ' . $refund->get_error_message() );
		}

		return $refund;
	}

	/**
	 * Applies 'billing' or 'shipping' address fields, keyed without the context prefix,
	 * to a WC_Customer or WC_Order via its setters.
	 */
	private function applyAddress( $target, string $context, array $fields ): void {
		foreach ( $fields as $key => $value ) {
			$setter = "set_{$context}_{$key}";

			if ( is_callable( [ $target, $setter ] ) ) {
				$target->{$setter}( $value );
			}
		}
	}
}
