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

    // Extract imported libraries
    .extract({
        libraries: ['js-cookie'],
        to: './assets/js/snowboard/build/snowboard.vendor.js',
    })

    // Compile Storm UI and framework assets
    .less('./assets/less/styles.less', './assets/css/styles.css')
    .less('./assets/ui/storm.less', './assets/ui/storm.css')
    .less('./assets/less/framework.extras.less', './assets/css/framework.extras.css', {
        lessOptions: {
            strictMath: true,
        },
    })
    .less('./assets/less/snowboard.extras.less', './assets/css/snowboard.extras.css', {
        lessOptions: {
            strictMath: true,
        },
    })
    .less('./assets/ui/less/select.less', './assets/ui/vendor/select2/css/select2.css')

    // Compile Font Awesome icons and fonts
    .less('./assets/ui/icons.less', './assets/ui/icons.css')
    .copy('./../../node_modules/@fortawesome/fontawesome-free/webfonts/*', './assets/ui/font')

    // Compile Snowboard for the Backend / System
    .js(
        [
            './assets/js/snowboard/snowboard.base.js',
            './assets/js/snowboard/snowboard.request.js',
            './assets/js/snowboard/snowboard.backend.extras.js',
        ],
        './assets/js/build/system.js',
    )
    .js(
        [
            './assets/js/snowboard/snowboard.base.debug.js',
            './assets/js/snowboard/snowboard.request.js',
            './assets/js/snowboard/snowboard.backend.extras.js',
        ],
        './assets/js/build/system.debug.js',
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
        './assets/js/snowboard/snowboard.request.js',
        './assets/js/snowboard/build/snowboard.request.js',
    )
    .js(
        './assets/js/snowboard/snowboard.data-attr.js',
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
