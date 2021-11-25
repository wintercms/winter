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
    .combine([
        './abstracts/Module.js',
        './abstracts/Singleton.js',
        './ModuleFactory.js',
        './Winter.js',
        './utilities/Debounce.js',
        './utilities/JsonParser.js',
        './utilities/Sanitizer.js',
    ], './build/framework.js', true)
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '>0.5%, not ie > 0'
    });
