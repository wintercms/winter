import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    build: {
        outDir: 'assets/dist',
    },
    resolve: {
        alias: {
            '~system': __dirname + '/../../modules/system',
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    plugins: [
        laravel({
            publicDirectory: 'assets/dist',
            input: [
                'controllers/updates/assets/src/updates.js',
                'controllers/updates/assets/src/updates.css'
            ],
            refresh: {
                paths: [
                    './**/*.htm',
                    './**/*.vue',
                    'controllers/updates/assets/src/**/*.css',
                    'controllers/updates/assets/src/**/*.js',
                    'controllers/updates/assets/src/**/*.vue',
                ]
            },
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
