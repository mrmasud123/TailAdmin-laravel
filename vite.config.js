import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { glob } from 'glob';
import * as path from "node:path";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...glob.sync('resources/assets/js/**/*.js'),
            ],
            refresh: false,
        }),
    ],
    resolve: {
        alias: {
            jquery: 'jquery/dist/jquery.js',
        },
    },
    define: {
        global: 'window',
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch:{
            usePolling:true
        }
    },
});
