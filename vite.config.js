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
    build: {

    },
    resolve: {
        alias: {
            '$': 'jQuery'
        },
    },
    optimizeDeps: {
        include: ['clipboard'],
    },
});
