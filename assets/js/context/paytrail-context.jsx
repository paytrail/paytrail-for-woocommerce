import React, {createContext} from 'react';
import { useLocalStorage } from 'usehooks-ts';

export const PaytrailContext = createContext({
    activeProvider:'', 
    setActiveProvider:() => {
        throw new Error('No set active provider defined');
    }
});

export const PaytrailContextProvider = ({children}) => {
	const [activeProvider, setActiveProvider] = useLocalStorage('activePaytrailProvider', '')
    return (<PaytrailContext.Provider value={{activeProvider, setActiveProvider}}>{children}</PaytrailContext.Provider>)
}