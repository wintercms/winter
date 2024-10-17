import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'assets/css/theme.css',
                'assets/javascript/theme.js',
            ],
            refresh: {
                paths: [
                    './**/*.htm',
                    'assets/**/*.css',
                    'assets/**/*.js',
                ]
            },
        })
    ],
});
