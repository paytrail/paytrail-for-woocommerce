import React from 'react';
import { getSetting } from '@woocommerce/settings';
import { ProviderGroup } from './provider-group';

const settings = getSetting('paytrail_data', {});
const applePayActive = settings.apple_pay_active;
//Hide manually created mobile group if Apple Pay is not supported and there are no other mobile providers
const hasOtherProviders = settings.groups.some(group => group.id === 'mobile' && group.providers.some(provider => provider.id !== 'apple-pay'));
const hideMobileGroup = (providerGroup) => (applePayActive && providerGroup.id === 'mobile' && !hasOtherProviders && !paytrail?.applePayButton?.canMakePayment())

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
            {settings.groups.map(providerGroup => (
		    hideMobileGroup(providerGroup) ? null : <ProviderGroup group={providerGroup} key={providerGroup.id}/>
		))}
        </div>
    );
};
