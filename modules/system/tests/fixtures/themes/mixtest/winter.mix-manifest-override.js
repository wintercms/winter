const mix = require('laravel-mix');

mix.setPublicPath(__dirname)
    .options({
        manifest: 'assets/dist/mix-manifest.json',
    })
    .version()

    // Render Tailwind style
    .postCss('assets/src/css/theme.css', 'assets/dist/css/theme.css')

    // Compile JS
    .js('assets/src/js/theme.js', 'assets/dist/js/theme.js');
