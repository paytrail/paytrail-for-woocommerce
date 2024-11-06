import {Component} from 'react';
import {PaytrailContextProvider} from './paytrail-context';

export const withPaytrail = (WrappedComponent) => {
    return class extends Component{render(){
        return (
            <PaytrailContextProvider>
			    <WrappedComponent {...this.props}/>
		    </PaytrailContextProvider>
        );   
    }};
}