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

    // Compile Snowboard assets for the Backend
    .js(
        './assets/ui/js/index.js',
        './assets/ui/js/build/backend.js',
    )

    // Compile original backend assets
    .less('./assets/less/winter.less', './assets/css/winter.css')
    .combine([
        './assets/js/vendor/jquery.touchwipe.js',
        './assets/js/vendor/jquery.autoellipsis.js',
        './assets/js/vendor/jquery.waterfall.js',
        './assets/js/vendor/jquery.cookie.js',
        './assets/vendor/dropzone/dropzone.js',
        './assets/vendor/sweet-alert/sweet-alert.js',
        './assets/vendor/jcrop/js/jquery.Jcrop.js',
        './../system/assets/vendor/prettify/prettify.js',
        './widgets/mediamanager/assets/js/mediamanager-global.js',
        './assets/js/winter.lang.js',
        './assets/js/winter.alert.js',
        './assets/js/winter.scrollpad.js',
        './assets/js/winter.verticalmenu.js',
        './assets/js/winter.navbar.js',
        './assets/js/winter.sidenav.js',
        './assets/js/winter.scrollbar.js',
        './assets/js/winter.filelist.js',
        './assets/js/winter.layout.js',
        './assets/js/winter.sidepaneltab.js',
        './assets/js/winter.simplelist.js',
        './assets/js/winter.treelist.js',
        './assets/js/winter.sidenav-tree.js',
        './assets/js/winter.datetime.js',
        './assets/js/backend.js',
    ], './assets/js/winter-min.js')

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
