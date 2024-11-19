import React from 'react';
import { getSetting } from '@woocommerce/settings';
import { ProviderGroup } from './provider-group';

const settings = getSetting('paytrail_data', {});

export const PaymentMethods = () => {
    // Check if no providers should be displayed
    if (!settings.groups || settings.groups.length === 0) {
        return (
            <div className="payment_box payment_method_paytrail">
            </div>
        );
    }

    // Display provider groups if providers are available
    return (
        <div className="payment_box payment_method_paytrail">
            <div className="checkout-terms-link">
                {settings.groups.map((providerGroup) => (
                    <ProviderGroup group={providerGroup} key={providerGroup.id} />
                ))}
            </div>
        </div>
    );
};
