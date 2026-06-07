import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/login-app.jsx',
                'resources/js/register-app.jsx',
                'resources/js/dashboard-app.jsx',
                'resources/js/customer-login-app.jsx',
                'resources/js/customer-register-app.jsx',
                'resources/js/customer-dashboard-app.jsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
