import React, {useState, useContext} from 'react';
import { Provider } from './provider';
import {PaytrailContext} from '../context/paytrail-context';
import { getSetting } from '@woocommerce/settings';
const settings = getSetting('paytrail_data', {});

export const ProviderGroup = ({group}) => {

    if ( !settings.groups || settings.groups === 0) {
        return null;
    }

	const {activeProvider} = useContext(PaytrailContext);
	const getProviderUniqueId = (provider, index) => `${provider.id}-${index}`;
	const [isOpen, setIsOpen] = useState(
		group.providers.some((provider, index) => activeProvider === getProviderUniqueId(provider, index))
	);

	const toggle = () => setIsOpen(!isOpen);

	return (
		<>
			<div className={"paytrail-provider-group"} onClick={toggle} >
				<img src={group.icon} className="provider-group-icon" height={28} width={28} />
				<div className="paytrail-provider-group-title">{group.name}</div>
			</div>
			<div className="provider-list">
				<ul className="paytrail-woocommerce-payment-fields">
				{isOpen && group.providers.map((provider, index) => (
					<Provider
						provider={provider}
						key={provider.id}
						index={index}
					/>
				))}
				</ul>
			</div>
		</>
	);
};
