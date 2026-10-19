<?php

declare(strict_types=1);

namespace Tests\Integration;

use Paytrail\WooCommercePaymentGateway\Model\PaymentTokenMigration;
use Paytrail\WooCommercePaymentGateway\Plugin;
use Tests\Support\IntegrationTestCase;

/**
 * Adopting the cards a store saved under the old Checkout Finland plugin, so shoppers
 * keep their stored payment methods across the rename.
 *
 * @covers \Paytrail\WooCommercePaymentGateway\Model\PaymentTokenMigration
 */
class MigrationsTest extends IntegrationTestCase {

	protected ?string $storeProfile = 'fi';

	public function test_a_card_saved_under_the_old_plugin_is_adopted(): void {
		$token = $this->haveCardToken( [ 'gateway_id' => 'checkout_finland' ] );

		( new PaymentTokenMigration() )->execute();

		$this->assertSame( Plugin::GATEWAY_ID, \WC_Payment_Tokens::get( $token->get_id() )->get_gateway_id() );
	}

	/** Another gateway's cards are not ours to take. */
	public function test_another_gateways_card_is_left_alone(): void {
		$token = $this->haveCardToken( [ 'gateway_id' => 'stripe' ] );

		( new PaymentTokenMigration() )->execute();

		$this->assertSame( 'stripe', \WC_Payment_Tokens::get( $token->get_id() )->get_gateway_id() );
	}

	/** Running it again on a migrated store is a no-op rather than an error. */
	public function test_running_the_migration_twice_changes_nothing(): void {
		$token = $this->haveCardToken( [ 'gateway_id' => 'checkout_finland' ] );

		( new PaymentTokenMigration() )->execute();
		( new PaymentTokenMigration() )->execute();

		$this->assertSame( Plugin::GATEWAY_ID, \WC_Payment_Tokens::get( $token->get_id() )->get_gateway_id() );
	}
}
