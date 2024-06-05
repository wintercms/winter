import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'assets/css/{{packageName}}.css',
            ],
            refresh: {
                paths: [
                    'assets/**/*.htm',
                    'assets/**/*.css',
                    'assets/**/*.js',
                ]
            },
        }),
    ],
});
