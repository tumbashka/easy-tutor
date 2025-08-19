import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from "vite-plugin-vuetify";

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
        vue(),
        vuetify({ autoImport: true }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5174,
        strictPort: true,
        hmr: {
            host: 'localhost'
        }
    },
    build: {},
    base: process.env.APP_URL || 'http://localhost:81',
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '$': 'jQuery'
        },
    },
    optimizeDeps: {
        include: ['clipboard'],
    },
});
