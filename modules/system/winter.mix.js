const mix = require('laravel-mix');
require('laravel-mix-polyfill');

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
        to: './assets/js/build/vendor.js',
    })

    // Compile Snowboard and UI assets for the Backend
    .js(
        [
            './assets/js/snowboard/snowboard.base.debug.js',
            './assets/js/snowboard/ajax/Request.js',
            './assets/js/snowboard/snowboard.extras.js',
            './assets/ui/widgets/base/base.js',
            './assets/ui/widgets/inspector/inspector.js',
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
