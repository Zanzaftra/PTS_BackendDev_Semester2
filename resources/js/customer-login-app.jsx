import React from 'react';
import ReactDOM from 'react-dom/client';
import CustomerLogin from './components/CustomerLogin';

if (document.getElementById('customer-login-root')) {
    ReactDOM.createRoot(document.getElementById('customer-login-root')).render(
        <React.StrictMode>
            <CustomerLogin />
        </React.StrictMode>
    );
}
