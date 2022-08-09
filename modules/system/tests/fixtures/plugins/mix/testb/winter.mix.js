const mix = require('laravel-mix');

mix.setPublicPath(__dirname);

mix.js('assets/src/app.js', 'assets/dist/app.js');
