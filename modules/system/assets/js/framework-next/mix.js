const mix = require('laravel-mix');
require('laravel-mix-polyfill');

mix.setPublicPath(__dirname);

// Compile Winter JS framework
mix
    .override((webpackConfig) => {
        if (mix.inProduction()) {
            // Strip out /* develblock */ code in production.
            webpackConfig.module.rules.push({
                test: /\.js$/,
                enforce: 'pre',
                exclude: /(node_modules|bower_components|\.spec\.js)/,
                use: [
                    {
                        loader: 'webpack-strip-block'
                    }
                ]
            })
        }
    })
    .js('./framework.js', './build/framework.js')
    .js('./ajax/Request.js', './build/framework-js-request.js')
    .js('./ajax/handlers/AttributeRequest.js', './build/framework-attr-request.js')
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '>0.5%, not ie > 0'
    });
