import React, {useEffect, useContext} from 'react';
import { getSetting } from '@woocommerce/settings';
import { PaymentMethods } from '../components/payment-methods';
import { PaytrailContext} from '../context/paytrail-context';
import { withPaytrail } from '../context/with-paytrail';
const settings = getSetting('paytrail_data', { mode: 'default', providers: [], contains_subscription: false });

/**
 * Content component
 */
export const PaytrailContainer = withPaytrail( (props) => {
	const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;
	const { activeProvider } = useContext(PaytrailContext);
	const cleanProvider = activeProvider.replace(/-\d+$/, '');

	if (settings.no_providers) {
        const defaultProvider = 'paytrail';
        setActiveProvider(defaultProvider);
    }

	const PaytrailStaticComponent = () => (
        <div className="paytrail-static-container">
            <div className="paytrail-description">
                {settings.description || "Your selected payment method is Paytrail."}
            </div>
        </div>
    );

	useEffect(() => {  
		const paymentSetup = onPaymentSetup(async () => {
		console.log(cleanProvider);
		return{
		  type: emitResponse.responseTypes.SUCCESS,
		  meta: { paymentMethodData: { payment_provider: cleanProvider || defaultProvider } }
		}
		});

		return () => { paymentSetup(); };
	}, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, activeProvider]);

	if (settings.no_providers || settings.providers?.length === 0 || settings.contains_subscription) {
        return <PaytrailStaticComponent />;
    } else {
        return <PaymentMethods/>;
    }
});