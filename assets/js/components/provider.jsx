import React, {useContext} from 'react';
import {PaytrailContext} from '../context/paytrail-context';
import { getSetting } from '@woocommerce/settings';
const settings = getSetting('paytrail_data', {});
// Provider component to render individual providers
export const Provider = ({provider, index}) => {

    if (settings.no_providers) {
        return null;
    }

    const {activeProvider, setActiveProvider} = useContext(PaytrailContext);
    const providerUniqueId = `${provider.id}-${index}`;
    return (
        <li 
            className={`paytrail-woocommerce-payment-fields--list-item ${activeProvider === providerUniqueId ? "selected" : ""}`} 
            onClick={() => setActiveProvider(providerUniqueId)}
        >
            <label htmlFor={`provider-${provider.id}-${index}`}>
                <input
                    type="radio"
                    className="paytrail-woocommerce-payment-fields--list-item--input"
                    name="payment_provider"
                    value={provider.id}
                    checked={activeProvider === providerUniqueId}  
                    onChange={() => setActiveProvider(providerUniqueId)}
                />
                <div className="paytrail-woocommerce-payment-fields--list-item--wrapper">
                    <img src={provider.svg} alt={provider.name} className="paytrail-woocommerce-payment-fields--list-item--img" />
                </div>
            </label>
        </li>
    );
};

