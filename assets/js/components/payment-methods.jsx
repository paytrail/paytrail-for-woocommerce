import React from 'react';
import { getSetting } from '@woocommerce/settings';
import { ProviderGroup } from './provider-group';
const settings = getSetting( 'paytrail_data', {} );

export const PaymentMethods = () => (
	<div className="payment_box payment_method_paytrail">
      <div className="checkout-terms-link">
		{settings.groups.map(providerGroup => (
		<ProviderGroup
			group={providerGroup}
			key={providerGroup.id}
		/>
		))}
      </div>
    </div>
)