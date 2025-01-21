
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { getSetting } from '@woocommerce/settings';

import { PaytrailContainer } from './containers/paytrail-container';
import {label, PaytrailLabel} from './components/paytrail-label';

import '../scss/payment_blocks.scss';
import './paytrail'

const settings = getSetting( 'paytrail_data', {} );

/**
 * Paytrail payment method config object.
 */

registerPaymentMethod( {
	paymentMethodId: 'paytrail',
	name: "paytrail",
	label: <PaytrailLabel />,
	content: <PaytrailContainer />,
	edit: <PaytrailContainer />,
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: settings.supports,
	},
} );
