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
    .js('./framework.js', './framework.build.js')
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '>0.5%, not ie > 0'
    });
