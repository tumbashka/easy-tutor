import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // assetsInlineLimit: 0, // Отключает встраивание шрифтов в base64
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
