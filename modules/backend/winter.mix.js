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
        runtimeChunkPath: './assets/ui/js/build',
    })
    .vue({ version: 3 })

    // Extract shared vendor libraries
    .extract({
        libraries: [
            '@vue',
            'babel-loader',
            'css-loader',
            'jquery-events-to-dom-events',
            'style-loader',
            'vue',
            'vue-loader',
        ],
        to: './assets/ui/js/build/vendor.js',
    })
    .vue({ version: 3 })

    // Compile Snowboard assets for the Backend
    .js(
        './assets/ui/js/index.js',
        './assets/ui/js/build/backend.js',
    )

    // Compile widgets for Backend
    .js(
        './formwidgets/colorpicker/assets/js/src/ColorPicker.js',
        './formwidgets/colorpicker/assets/js/dist/colorpicker.js',
    )
    .js(
        './formwidgets/iconpicker/assets/js/src/iconpicker.js',
        './formwidgets/iconpicker/assets/js/dist/iconpicker.js',
    )
    .js(
        './formwidgets/sensitive/assets/js/src/Sensitive.js',
        './formwidgets/sensitive/assets/js/dist/sensitive.js',
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
