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

	const toggle = (e) => {
		if (e?.type === 'keydown') {
			// Only proceed if it's Enter or Space key
			if (e.key !== 'Enter' && e.key !== ' ') {
				return;
			}
			e.preventDefault();
			setActiveGroup(isOpen ? '' : group.id);
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
				aria-haspopup="true"
				aria-expanded={isOpen}
			>
				<img src={group.icon} className="provider-group-icon" height={28} width={28} alt="" aria-hidden="true" />
				<div className="paytrail-provider-group-title">{group.name}</div>
			</div>
			<div className="provider-list">
				<ul 
					className="paytrail-woocommerce-payment-fields"
					aria-hidden={!isOpen}
					aria-labelledby={group.id}
				>
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
