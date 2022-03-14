const mix = require('laravel-mix');
require('laravel-mix-polyfill');

mix.setPublicPath(__dirname);

// Compile Inspector widget
mix
    .options({
        terser: {
            extractComments: false,
        },
    })
    .js('./base.js', './build/base.js');
