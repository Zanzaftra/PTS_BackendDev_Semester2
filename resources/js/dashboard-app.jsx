import React from 'react';
import { createRoot } from 'react-dom/client';
import Dashboard from './components/Dashboard.jsx';

const el = document.getElementById('dashboard-root');
if (el) {
    const initialData = window.__RINDU_DATA__ || null;
    createRoot(el).render(<Dashboard initialData={initialData} />);
}
