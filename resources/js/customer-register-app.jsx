import React from 'react';
import ReactDOM from 'react-dom/client';
import CustomerRegister from './components/CustomerRegister';

if (document.getElementById('customer-register-root')) {
    ReactDOM.createRoot(document.getElementById('customer-register-root')).render(
        <React.StrictMode>
            <CustomerRegister />
        </React.StrictMode>
    );
}
