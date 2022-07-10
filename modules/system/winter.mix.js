/* eslint-disable */
const mix = require('laravel-mix');
const fs = require('fs');
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

    // Compile Snowboard for the Backend / System
    .js(
        [
            './assets/js/snowboard/snowboard.base.debug.js',
            './assets/js/snowboard/ajax/Request.js',
            './assets/js/snowboard/snowboard.backend.extras.js',
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

    // Copy Bootstrap 3 JS files into location (Storm UI asset)
    .copy('../../node_modules/bootstrap/js/dropdown.js', './assets/ui/vendor/bootstrap/js/dropdown.js')
    .copy('../../node_modules/bootstrap/js/modal.js', './assets/ui/vendor/bootstrap/js/modal.js')
    .copy('../../node_modules/bootstrap/js/tab.js', './assets/ui/vendor/bootstrap/js/tab.js')
    .copy('../../node_modules/bootstrap/js/tooltip.js', './assets/ui/vendor/bootstrap/js/tooltip.js')
    .copy('../../node_modules/bootstrap/js/transition.js', './assets/ui/vendor/bootstrap/js/transition.js')

    // Copy jQuery.Flot into location (Storm UI asset)
    .copy('../../node_modules/flot/source/jquery.canvaswrapper.js', './assets/ui/vendor/flot/jquery.canvaswrapper.js')
    .copy('../../node_modules/flot/source/jquery.colorhelpers.js', './assets/ui/vendor/flot/jquery.colorhelpers.js')
    .copy('../../node_modules/flot/source/jquery.flot.js', './assets/ui/vendor/flot/jquery.flot.js')
    .copy('../../node_modules/flot/source/jquery.flot.resize.js', './assets/ui/vendor/flot/jquery.flot.resize.js')
    .copy('../../node_modules/flot/source/jquery.flot.saturated.js', './assets/ui/vendor/flot/jquery.flot.saturated.js')
    .copy('../../node_modules/flot/source/jquery.flot.time.js', './assets/ui/vendor/flot/jquery.flot.time.js')
    .copy('../../node_modules/jquery.flot.tooltip/js/jquery.flot.tooltip.js', './assets/ui/vendor/flot/jquery.flot.tooltip.js')

    // Copy Luxon into location (Storm UI asset)
    .copy('../../node_modules/luxon/build/global/luxon.js', './assets/ui/vendor/luxon/luxon.js')

    // Copy Mustache into location (Storm UI asset)
    .copy('../../node_modules/mustache/mustache.js', './assets/ui/vendor/mustache/mustache.js')

    // Copy Pickaday into location (Storm UI asset)
    .copy('../../node_modules/pikaday/pikaday.js', './assets/ui/vendor/pikaday/js/pikaday.js')
    .copy('../../node_modules/pikaday/css/pikaday.css', './assets/ui/vendor/pikaday/css/pikaday.css')
    .copy('../../node_modules/pikaday/plugins/pikaday.jquery.js', './assets/ui/vendor/pikaday/js/pikaday.jquery.js')

    // Copy PrismJS into location (Exception pages)
    .combine([
        '../../node_modules/prismjs/prism.js',
        '../../node_modules/prismjs/components/prism-php.js',
        '../../node_modules/prismjs/components/prism-markup-templating.js',
        '../../node_modules/prismjs/plugins/line-numbers/prism-line-numbers.js',
        '../../node_modules/prismjs/plugins/line-highlight/prism-line-highlight.js',
    ], './assets/vendor/prism/prism.js')

    .combine([
        '../../node_modules/prismjs/themes/prism.css',
        '../../node_modules/prismjs/plugins/line-numbers/prism-line-numbers.css',
        '../../node_modules/prismjs/plugins/line-highlight/prism-line-highlight.css',
    ], './assets/vendor/prism/prism.css')

    // Copy Raphael JS into location (Storm UI asset)
    .copy('../../node_modules/raphael/raphael.js', './assets/ui/vendor/raphael/raphael.js')

    // Copy Select2 into location (Storm UI asset)
    .copy('../../node_modules/select2/dist/js/select2.full.js', './assets/ui/vendor/select2/js/select2.full.js')
    .copy('../../node_modules/select2/dist/js/i18n/*.js', './assets/ui/vendor/select2/js/i18n')

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    })

    // Callbacks after compilation
    .after(() => {
        // Fix Pikaday asset permissions (seems to be 0755 in the source)
        fs.chmodSync('./assets/ui/vendor/pikaday/js/pikaday.js', '644');
        fs.chmodSync('./assets/ui/vendor/pikaday/js/pikaday.jquery.js', '644');
        fs.chmodSync('./assets/ui/vendor/pikaday/css/pikaday.css', '644');
    });
