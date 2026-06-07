import React from 'react';
import { createRoot } from 'react-dom/client';
import Login from './components/Login.jsx';

const el = document.getElementById('login-root');
if (el) {
    createRoot(el).render(<Login />);
}
