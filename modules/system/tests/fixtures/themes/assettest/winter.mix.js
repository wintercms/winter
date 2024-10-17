const mix = require('laravel-mix');
mix.setPublicPath(__dirname);

mix.js('assets/javascript/theme.js', 'dist/javascript/theme.js');
