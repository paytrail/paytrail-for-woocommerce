import React, {useContext} from 'react';
import {PaytrailContext} from '../context/paytrail-context';
// Provider component to render individual providers
export const Provider = ({provider, index}) => {

    const {activeProvider, setActiveProvider} = useContext(PaytrailContext);
    const providerUniqueId = `${provider.id}-${index}`;
    return (
        <li 
            className={`paytrail-woocommerce-payment-fields--list-item ${activeProvider === providerUniqueId ? "selected" : ""}`}
            onClick={() => setActiveProvider(providerUniqueId)}
        >
            <label htmlFor={providerUniqueId}>
                <input
                    id={providerUniqueId}
                    type="radio"
                    className="paytrail-woocommerce-payment-fields--list-item--input"
                    name="payment_provider"
                    value={provider.id}
                    checked={activeProvider === providerUniqueId}  
                    onChange={() => setActiveProvider(providerUniqueId)}
                    aria-label={provider.name}
                />
                <div className="paytrail-woocommerce-payment-fields--list-item--wrapper" aria-hidden="true">
                    <img src={provider.svg} alt={provider.name} className="paytrail-woocommerce-payment-fields--list-item--img" />
                </div>
            </label>
        </li>
    );
};

