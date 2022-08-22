let mix = require('laravel-mix');
mix.setPublicPath(__dirname);

mix
    // Froala Core Assets
    .combine([
        'node_modules/froala-editor/js/froala_editor.min.js',

        // Buttons
        'node_modules/froala-editor/js/plugins/paragraph_style.min.js',
        'node_modules/froala-editor/js/plugins/fullscreen.min.js',
        'node_modules/froala-editor/js/plugins/code_view.min.js',
        'node_modules/froala-editor/js/plugins/paragraph_format.min.js',
        'node_modules/froala-editor/js/plugins/align.min.js',
        'node_modules/froala-editor/js/plugins/lists.min.js',
        // 'node_modules/froala-editor/js/plugins/file_extended.js',
        'node_modules/froala-editor/js/plugins/image.min.js',
        'node_modules/froala-editor/js/plugins/inline_style.min.js',
        'node_modules/froala-editor/js/plugins/inline_class.min.js',

        // Cannot be minified twice
        'node_modules/froala-editor/js/plugins/link.min.js',

        'node_modules/froala-editor/js/plugins/table.min.js',
        'node_modules/froala-editor/js/plugins/video.min.js',
        // 'node_modules/froala-editor/js/plugins/audio.js',
        'node_modules/froala-editor/js/plugins/quote.min.js',
        'node_modules/froala-editor/js/plugins/font_size.min.js',
        'node_modules/froala-editor/js/plugins/font_family.min.js',
        'node_modules/froala-editor/js/plugins/emoticons.min.js',
        'node_modules/froala-editor/js/plugins/colors.min.js',

        // Functional
        'node_modules/froala-editor/js/plugins/url.min.js',
        'node_modules/froala-editor/js/plugins/line_breaker.min.js',
        'node_modules/froala-editor/js/plugins/entities.min.js',
        'node_modules/froala-editor/js/plugins/draggable.min.js',
        'node_modules/froala-editor/js/plugins/code_beautifier.min.js',

        // More testing needed
        //require ../vendor/froala_drm/js/plugins/quick_insert.js
    ], 'assets/js/froala.js')

    // Froala Winter Customizations
    .combine([
        'assets/js/plugins/figures.js',
        'assets/js/plugins/mediamanager.js',
        'assets/js/plugins/pagelinks.js',
        'assets/js/richeditor.js'
    ], 'assets/js/froala-winter.js')
;
