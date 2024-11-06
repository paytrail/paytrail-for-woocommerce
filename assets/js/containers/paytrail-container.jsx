import React, {useEffect, useContext} from 'react';

import { PaymentMethods } from '../components/payment-methods';
import { PaytrailContext} from '../context/paytrail-context';
import { withPaytrail } from '../context/with-paytrail';

/**
 * Content component
 */
export const PaytrailContainer = withPaytrail( (props) => {
	const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;
	const { activeProvider } = useContext(PaytrailContext);
	const cleanProvider = activeProvider.replace(/-\d+$/, '');

	useEffect(() => {  
		const paymentSetup = onPaymentSetup(async () => {
		console.log(cleanProvider);
		return{
		  type: emitResponse.responseTypes.SUCCESS,
		  meta: { paymentMethodData: { payment_provider: cleanProvider} }
		}
		});

		return () => { paymentSetup(); };
	}, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, activeProvider]);

	return(
		<PaymentMethods/>
	);
});