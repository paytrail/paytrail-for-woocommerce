import React, {createContext} from 'react';
import { useLocalStorage } from 'usehooks-ts';

export const PaytrailContext = createContext({
    activeProvider:'', 
    setActiveProvider:() => {
        throw new Error('No set active provider defined');
    },
    activeGroup: '',
    setActiveGroup: () => {
        throw new Error('No set active group defined');
    }
});

export const PaytrailContextProvider = ({children}) => {
	const [activeProvider, setActiveProvider] = useLocalStorage('activePaytrailProvider', '');
    const [activeGroup, setActiveGroup] = useLocalStorage('activePaytrailGroup', '');
    
    return (
        <PaytrailContext.Provider 
            value={{
                activeProvider, 
                setActiveProvider,
                activeGroup,
                setActiveGroup
            }}
        >
            {children}
        </PaytrailContext.Provider>
    );
};