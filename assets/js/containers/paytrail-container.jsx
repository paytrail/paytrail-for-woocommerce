import React, {useEffect, useContext} from 'react';
import { getSetting } from '@woocommerce/settings';
import { PaymentMethods } from '../components/payment-methods';
import { PaytrailContext} from '../context/paytrail-context';
import { withPaytrail } from '../context/with-paytrail';
import { __ } from '@wordpress/i18n';
const settings = getSetting('paytrail_data', {});

/**
 * Content component
 */
export const PaytrailContainer = withPaytrail( (props) => {
	const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;
	const { activeProvider } = useContext(PaytrailContext);
	let cleanProvider = activeProvider.replace(/-\d+$/, '');

	if (!settings.groups || settings.groups.length === 0) {
        const defaultProvider = 'paytrail';
        if (!cleanProvider) {
            cleanProvider = defaultProvider;
        }
    }

	const PaytrailStaticComponent = () => (
		<div className="paytrail-static-container">
			<div className="paytrail-description">
				{settings.description || __("payment providers will open in a new window", "paytrail-for-woocommerce")}
			</div>
		</div>
	);

	useEffect(() => {  
		const paymentSetup = onPaymentSetup(async () => {
		return{
		  type: emitResponse.responseTypes.SUCCESS,
		  meta: { paymentMethodData: { payment_provider: cleanProvider || defaultProvider } }
		}
		});

		return () => { paymentSetup(); };
	}, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, activeProvider]);

	if (!settings.groups || settings.groups.length === 0) {
        return <PaytrailStaticComponent />;
    } else {
        return <PaymentMethods/>;
    }
});