const mix = require('laravel-mix');
require('laravel-mix-polyfill');

mix.setPublicPath(__dirname);

// Compile Snowboard framework
mix
    .options({
        terser: {
            extractComments: false,
        },
    })
    .js('./snowboard.base.js', './build/snowboard.base.js')
    .js('./snowboard.base.debug.js', './build/snowboard.base.debug.js')
    .js('./ajax/Request.js', './build/snowboard.request.js')
    .js('./ajax/handlers/AttributeRequest.js', './build/snowboard.data-attr.js')
    .js('./snowboard.extras.js', './build/snowboard.extras.js')
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
