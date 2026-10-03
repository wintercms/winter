const mix = require('laravel-mix');

mix.setPublicPath(__dirname)
    .options({
        manifest: true,
    })
    .version()

    // Render Tailwind style
    .postCss('assets/src/css/theme.css', 'assets/dist/css/theme.css')
