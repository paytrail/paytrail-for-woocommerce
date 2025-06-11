import React, {useContext} from 'react';
import { Provider } from './provider';
import {PaytrailContext} from '../context/paytrail-context';
import { getSetting } from '@woocommerce/settings';
const settings = getSetting('paytrail_data', {});

export const ProviderGroup = ({group}) => {

    if ( !settings.groups || settings.groups === 0) {
        return null;
    }

	const {activeGroup, setActiveGroup} = useContext(PaytrailContext);
	const isOpen = activeGroup === group.id;

	const toggle = (event) => {
		if (event?.type === 'keydown') {
			// Only proceed if it's Enter or Space key
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				setActiveGroup(isOpen ? '' : group.id);
			}
			return;
		}
		
		// Handle non-keyboard events
		setActiveGroup(isOpen ? '' : group.id);
	};

	return (
		<>
			<div 
				className={"paytrail-provider-group"} 
				tabIndex={0} 
				role="button" 
				onClick={toggle}
				onKeyDown={toggle}
			>
				<img src={group.icon} className="provider-group-icon" height={28} width={28} alt={group.name} aria-hidden="true" />
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
