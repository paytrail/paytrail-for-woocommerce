<?php

declare(strict_types=1);

namespace Tests\Harness\Fakes;

use Paytrail\WooCommercePaymentGateway\Helper;
use Tests\Support\Fakes\SubscriptionOrder;
use Tests\Support\IntegrationTestCase;

/**
 * The WooCommerce Subscriptions stand-ins. The plugin believes Subscriptions is
 * installed for the whole run, so these have to answer as an uninstalled one until a
 * test says otherwise.
 *
 * @covers \Tests\Support\Fakes\SubscriptionsRegistry
 * @covers \Tests\Support\Traits\CanFakeSubscriptions
 */
class FakeSubscriptionsTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	/** A test that asks for nothing sees the store it would see without the fakes. */
	public function test_an_unprepared_test_sees_no_subscriptions(): void {
		$this->assertFalse( Helper::getIsSubscriptionsEnabled() );
		$this->assertFalse( wcs_is_subscription( $this->haveOrder() ) );
		$this->assertSame( [], wcs_get_subscriptions_for_order( $this->haveOrder() ) );
	}

	/** A registered subscription loads as the WC_Subscription stand-in. */
	public function test_a_registered_subscription_answers_as_one(): void {
		$subscription = $this->haveSubscription();

		$this->assertTrue( wcs_is_subscription( $subscription ) );
		$this->assertInstanceOf( SubscriptionOrder::class, wc_get_order( $subscription->get_id() ) );
		$this->assertSame( 'shop_subscription', $subscription->get_type() );
	}

	/** An ordinary order alongside a subscription is still an ordinary order. */
	public function test_an_ordinary_order_is_unaffected(): void {
		$this->haveSubscription();

		$order = $this->haveOrder();

		$this->assertFalse( wcs_is_subscription( $order ) );
		$this->assertSame( 'shop_order', wc_get_order( $order->get_id() )->get_type() );
	}

	/** The parent order is how the plugin gets from a subscription back to the purchase. */
	public function test_a_subscription_is_linked_to_the_order_it_was_bought_on(): void {
		$parent       = $this->haveOrder();
		$subscription = $this->haveSubscriptionFor( $parent );

		$this->assertSame( [ $subscription->get_id() ], array_keys( wcs_get_subscriptions_for_order( $parent ) ) );
		$this->assertSame( $parent->get_id(), $subscription->get_parent()->get_id() );
	}

	/** A renewal order resolves back to the subscription it renews. */
	public function test_a_renewal_order_resolves_to_its_subscription(): void {
		$subscription = $this->haveSubscription();
		$renewal      = $this->haveRenewalOrderFor( $subscription );

		$this->assertSame( [ $subscription->get_id() ], array_keys( wcs_get_subscriptions_for_renewal_order( $renewal ) ) );
		$this->assertSame( [], wcs_get_subscriptions_for_order( $renewal ) );
	}

	/** Registrations are static, so one test's subscriptions must not reach the next. */
	public function test_registrations_do_not_survive_the_reset(): void {
		$subscription = $this->haveSubscription();

		$this->resetFakeSubscriptions();

		$this->assertFalse( wcs_is_subscription( $subscription ) );
		$this->assertFalse( wcs_get_subscription( $subscription ) );
	}

	/** A cart state the fakes do not know about is a typo, not a false answer. */
	public function test_an_unknown_cart_state_fails_loudly(): void {
		$this->expectException( \PHPUnit\Framework\AssertionFailedError::class );
		$this->expectExceptionMessage( 'No cart state "switches" exists to fake.' );

		$this->haveCartContaining( 'switches' );
	}
}
