const mix = require('laravel-mix');
require('laravel-mix-polyfill');

mix.setPublicPath(__dirname);

// Compile Snowcart framework
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
    .js('./snowcart.base.js', './build/snowcart.base.js')
    .js('./ajax/Request.js', './build/snowcart.request.js')
    .js('./ajax/handlers/AttributeRequest.js', './build/snowcart.data-attr.js')
    .js('./snowcart.extras.js', './build/snowcart.extras.js')
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0'
    });
