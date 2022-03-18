const mix = require('laravel-mix');
require('laravel-mix-polyfill');

mix.setPublicPath(__dirname);

mix
    .options({
        terser: {
            extractComments: false,
        },
        runtimeChunkPath: './assets/js/vendor'
    })
    .vue({ version: 3 })

    // Extract imported libraries
    .extract({
        to: './assets/js/vendor/vendor.js',
    })

    // Compile UI and widgets
    .js(
        './assets/ui/widgets/base/base.js',
        './assets/ui/widgets/base/build/base.js'
    )
    .js(
        './assets/ui/widgets/inspector/inspector.js',
        './assets/ui/widgets/inspector/build/inspector.js'
    )

    // Compile Snowboard framework
    .js(
        './assets/js/snowboard/snowboard.base.js',
        './assets/js/snowboard/build/snowboard.base.js'
    )
    .js(
        './assets/js/snowboard/snowboard.base.debug.js',
        './assets/js/snowboard/build/snowboard.base.debug.js'
    )
    .js(
        './assets/js/snowboard/ajax/Request.js',
        './assets/js/snowboard/build/snowboard.request.js'
    )
    .js(
        './assets/js/snowboard/ajax/handlers/AttributeRequest.js',
        './assets/js/snowboard/build/snowboard.data-attr.js'
    )
    .js(
        './assets/js/snowboard/snowboard.extras.js',
        './assets/js/snowboard/build/snowboard.extras.js'
    )

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
