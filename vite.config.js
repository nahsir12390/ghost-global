import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const hmrHost = process.env.VITE_HMR_HOST || '127.0.0.1';
const hmrPort = Number(process.env.VITE_HMR_PORT || 5173);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: hmrHost,
            port: hmrPort,
            clientPort: hmrPort,
        },
    },
});
