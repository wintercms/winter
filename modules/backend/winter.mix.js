/* eslint-disable */
const mix = require('laravel-mix');
const path = require('path');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');
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
    .js(
        './formwidgets/iconpicker/assets/src/iconpicker.js',
        './formwidgets/iconpicker/assets/dist/iconpicker.js',
    )
    .vue()

    // Compile widgets for Backend
    .js(
        './formwidgets/codeeditor/assets/js/codeeditor.js',
        './formwidgets/codeeditor/assets/js/build/codeeditor.bundle.js',
    )
    .webpackConfig({
        plugins: [
            new MonacoWebpackPlugin({
                filename: './formwidgets/codeeditor/assets/js/build/[name].worker.js',
                publicPath: path.resolve(__dirname, './formwidgets/codeeditor/assets/js/build/'),
                languages: [
                    'typescript',
                    'javascript',
                    'css',
                    'json',
                    'html',
                    'ini',
                    'less',
                    'markdown',
                    'mysql',
                    'php',
                    'scss',
                    'twig',
                    'xml',
                    'yaml',
                ],
                features: [
                    'anchorSelect',
                    'bracketMatching',
                    'caretOperations',
                    'clipboard',
                    'colorPicker',
                    'comment',
                    'contextmenu',
                    'cursorUndo',
                    'find',
                    'folding',
                    'gotoSymbol',
                    'hover',
                    'inPlaceReplace',
                    'indentation',
                    'inlineHints',
                    'links',
                    'multicursor',
                    'parameterHints',
                    'rename',
                    'smartSelect',
                    'snippet',
                    'suggest',
                    'wordHighlighter',
                    'wordOperations',
                ],
            }),
        ],
    })

    // Polyfill for all targeted browsers
    .polyfill({
        enabled: mix.inProduction(),
        useBuiltIns: 'usage',
        targets: '> 0.5%, last 2 versions, not dead, Firefox ESR, not ie > 0',
    });
