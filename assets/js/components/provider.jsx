import React, {useContext} from 'react';
import {PaytrailContext} from '../context/paytrail-context';
// Provider component to render individual providers
export const Provider = ({provider, index}) => {

    const {activeProvider, setActiveProvider} = useContext(PaytrailContext);
    const providerUniqueId = `${provider.id}-${index}`;
    const hideApplePay = (provider.id === 'apple-pay' && !paytrail?.applePayButton?.canMakePayment());
    return (
        <li 
            className={`paytrail-woocommerce-payment-fields--list-item ${hideApplePay ? 'apple-pay' : ''} ${activeProvider === providerUniqueId ? "selected" : ""}`} 
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

