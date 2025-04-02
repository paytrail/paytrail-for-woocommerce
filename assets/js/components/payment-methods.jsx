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

    //Renders terms and conditions HTML.
    const PaytrailTerms = () => {
        return (
            <div
                className="checkout-terms-link"
                dangerouslySetInnerHTML={{ __html: settings.terms }}
            ></div>
        );
    };

    // Display provider groups if providers are available
    return (
        <div className="payment_box payment_method_paytrail">
            <PaytrailTerms />
            {settings.groups.map((providerGroup) => (
		    <ProviderGroup group={providerGroup} key={providerGroup.id}/>
		))}
        </div>
    );
};
