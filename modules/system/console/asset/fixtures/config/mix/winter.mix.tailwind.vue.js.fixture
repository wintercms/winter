const mix = require('laravel-mix');
mix.setPublicPath(__dirname);

mix.postCss('assets/src/css/{{packageName}}.css', 'assets/dist/css/{{packageName}}.css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('autoprefixer'),
]);

mix.js('assets/src/js/{{packageName}}.js', 'assets/dist/js/{{packageName}}.js').vue({ version: 3 });
