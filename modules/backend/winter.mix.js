/* eslint-disable */
const mix = require('laravel-mix');
require('laravel-mix-polyfill');
/* eslint-enable */

mix.setPublicPath(__dirname);

mix
    .options({
        terser: {
            extractComments: false,
        },
        runtimeChunkPath: './assets/js/build',
    })
    .vue({ version: 3 })

    // Compile Snowboard assets for the Backend
    .js(
        './assets/ui/js/index.js',
        './assets/ui/js/build/backend.js',
    )

    // Compile widgets for Backend
    .js(
        './formwidgets/iconpicker/assets/src/iconpicker.js',
        './formwidgets/iconpicker/assets/dist/iconpicker.js',
    )

    // Compile pages
    .js(
        './assets/ui/js/pages/Preferences.js',
        './assets/js/preferences/preferences.js',
    )

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
