import React from 'react';
import ReactDOM from 'react-dom/client';
import Register from './components/Register';

if (document.getElementById('register-root')) {
    ReactDOM.createRoot(document.getElementById('register-root')).render(
        <React.StrictMode>
            <Register />
        </React.StrictMode>
    );
}
