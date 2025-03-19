import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/admin.scss',
                'resources/js/app.js',
                'resources/js/tom-select.js',
                'resources/js/flatpickr.js',
                'resources/js/chartjs.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1', // Локальный хост для разработки
    },
    build: {

    },
    base: process.env.APP_URL || 'https://tumbashka-easy-tutor.loophole.site',
    resolve: {
        alias: {
            '$': 'jQuery'
        },
    },
    optimizeDeps: {
        include: ['clipboard'],
    },
});
