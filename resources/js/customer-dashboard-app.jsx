import React from 'react';
import ReactDOM from 'react-dom/client';
import CustomerDashboard from './components/CustomerDashboard';

if (document.getElementById('customer-dashboard-root')) {
    // Read the passed data from Laravel
    const initialData = window.__CUSTOMER_DATA__ || {};
    
    ReactDOM.createRoot(document.getElementById('customer-dashboard-root')).render(
        <React.StrictMode>
            <CustomerDashboard initialData={initialData} />
        </React.StrictMode>
    );
}
