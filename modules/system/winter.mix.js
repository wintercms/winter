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

    // Compile Snowboard for the Backend / System
    .js(
        [
            './assets/js/snowboard/snowboard.base.js',
            './assets/js/snowboard/ajax/Request.js',
            './assets/js/snowboard/snowboard.backend.extras.js',
        ],
        './assets/js/build/system.js',
    )
    .js(
        [
            './assets/js/snowboard/snowboard.base.debug.js',
            './assets/js/snowboard/ajax/Request.js',
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

    // Compile original system assets
    .less('./assets/less/styles.less', './assets/css/styles.css')
    .less('./assets/ui/storm.less', './assets/ui/storm.css')
    .combine([
        'assets/ui/vendor/mustache/mustache.js',
        'assets/ui/vendor/modernizr/modernizr.js',
        'assets/ui/vendor/bootstrap/js/dropdown.js',
        'assets/ui/vendor/bootstrap/js/transition.js',
        'assets/ui/vendor/bootstrap/js/tab.js',
        'assets/ui/vendor/bootstrap/js/modal.js',
        'assets/ui/vendor/bootstrap/js/tooltip.js',
        'assets/ui/vendor/raphael/raphael.js',
        'assets/ui/vendor/flot/jquery.flot.js',
        'assets/ui/vendor/flot/jquery.flot.tooltip.js',
        'assets/ui/vendor/flot/jquery.flot.resize.js',
        'assets/ui/vendor/flot/jquery.flot.time.js',
        'assets/ui/vendor/select2/js/select2.full.js',
        'assets/ui/vendor/mousewheel/mousewheel.js',
        'assets/ui/vendor/sortable/jquery-sortable.js',
        'assets/ui/vendor/moment/moment.js',
        'assets/ui/vendor/moment/moment-timezone-with-data.js',
        'assets/ui/vendor/pikaday/js/pikaday.js',
        'assets/ui/vendor/pikaday/js/pikaday.jquery.js',
        'assets/ui/vendor/clockpicker/js/jquery-clockpicker.js',
        'assets/ui/js/foundation.baseclass.js',
        'assets/ui/js/foundation.element.js',
        'assets/ui/js/foundation.event.js',
        'assets/ui/js/foundation.controlutils.js',
        'assets/ui/js/flashmessage.js',
        'assets/ui/js/autocomplete.js',
        'assets/ui/js/checkbox.js',
        'assets/ui/js/checkbox.balloon.js',
        'assets/ui/js/dropdown.js',
        'assets/ui/js/callout.js',
        'assets/ui/js/datepicker.js',
        'assets/ui/js/tooltip.js',
        'assets/ui/js/toolbar.js',
        'assets/ui/js/filter.js',
        'assets/ui/js/filter.dates.js',
        'assets/ui/js/filter.numbers.js',
        'assets/ui/js/select.js',
        'assets/ui/js/loader.base.js',
        'assets/ui/js/loader.cursor.js',
        'assets/ui/js/loader.stripe.js',
        'assets/ui/js/popover.js',
        'assets/ui/js/popup.js',
        'assets/ui/js/chart.utils.js',
        'assets/ui/js/chart.line.js',
        'assets/ui/js/chart.bar.js',
        'assets/ui/js/chart.pie.js',
        'assets/ui/js/chart.meter.js',
        'assets/ui/js/list.rowlink.js',
        'assets/ui/js/input.monitor.js',
        'assets/ui/js/input.hotkey.js',
        'assets/ui/js/input.preset.js',
        'assets/ui/js/input.trigger.js',
        'assets/ui/js/drag.value.js',
        'assets/ui/js/drag.sort.js',
        'assets/ui/js/drag.scroll.js',
        'assets/ui/js/tab.js',
        'assets/ui/js/inspector.surface.js',
        'assets/ui/js/inspector.manager.js',
        'assets/ui/js/inspector.wrapper.base.js',
        'assets/ui/js/inspector.wrapper.popup.js',
        'assets/ui/js/inspector.wrapper.container.js',
        'assets/ui/js/inspector.groups.js',
        'assets/ui/js/inspector.engine.js',
        'assets/ui/js/inspector.editor.base.js',
        'assets/ui/js/inspector.editor.string.js',
        'assets/ui/js/inspector.editor.checkbox.js',
        'assets/ui/js/inspector.editor.dropdown.js',
        'assets/ui/js/inspector.editor.popupbase.js',
        'assets/ui/js/inspector.editor.text.js',
        'assets/ui/js/inspector.editor.set.js',
        'assets/ui/js/inspector.editor.objectlist.js',
        'assets/ui/js/inspector.editor.object.js',
        'assets/ui/js/inspector.editor.stringlist.js',
        'assets/ui/js/inspector.editor.stringlistautocomplete.js',
        'assets/ui/js/inspector.editor.dictionary.js',
        'assets/ui/js/inspector.editor.autocomplete.js',
        'assets/ui/js/inspector.helpers.js',
        'assets/ui/js/inspector.validationset.js',
        'assets/ui/js/inspector.validator.base.js',
        'assets/ui/js/inspector.validator.basenumber.js',
        'assets/ui/js/inspector.validator.required.js',
        'assets/ui/js/inspector.validator.regex.js',
        'assets/ui/js/inspector.validator.integer.js',
        'assets/ui/js/inspector.validator.float.js',
        'assets/ui/js/inspector.validator.length.js',
        'assets/ui/js/inspector.externalparametereditor.js',
        'assets/ui/js/list.sortable.js',
    ], './assets/ui/storm-min.js')
    .less('./assets/ui/icons.less', './assets/ui/icons.css')
    .minify('./assets/js/framework.js', './assets/js/framework-min.js')
    .combine([
        './assets/js/framework.js',
        './assets/js/framework.extras.js',
    ], './assets/js/framework.combined-min.js')
    .less('./assets/less/framework.extras.less', './assets/css/framework.extras.css')
    .less('./assets/less/snowboard.extras.less', './assets/css/snowboard.extras.css')

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
