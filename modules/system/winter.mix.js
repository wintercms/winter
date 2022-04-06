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

    // Extract imported libraries
    .extract({
        libraries: ['js-cookie'],
        to: './assets/js/snowboard/build/snowboard.vendor.js',
    })
    .extract({
        libraries: [
            '@popperjs/core',
            'vue',
        ],
        to: './assets/js/build/vendor.js',
    })

    // Compile Snowboard for the Backend / System
    .js(
        [
            './assets/js/snowboard/snowboard.base.debug.js',
            './assets/js/snowboard/ajax/Request.js',
            './assets/js/snowboard/snowboard.extras.js',
        ],
        './assets/js/build/system.js',
    )

    // Compile Snowboard framework separately for the CMS module
    .js(
        './assets/js/snowboard/snowboard.base.js',
        './assets/js/snowboard/build/snowboard.base.js',
    )
    .js(
        './assets/js/snowboard/snowboard.base.debug.js',
        './assets/js/snowboard/build/snowboard.base.debug.js',
    )
    .js(
        './assets/js/snowboard/ajax/Request.js',
        './assets/js/snowboard/build/snowboard.request.js',
    )
    .js(
        './assets/js/snowboard/ajax/handlers/AttributeRequest.js',
        './assets/js/snowboard/build/snowboard.data-attr.js',
    )
    .js(
        './assets/js/snowboard/snowboard.extras.js',
        './assets/js/snowboard/build/snowboard.extras.js',
    )

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
