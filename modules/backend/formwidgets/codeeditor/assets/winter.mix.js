/* eslint-disable */
const mix = require('laravel-mix');
const fs = require('fs');
const IgnorePlugin = require('webpack/lib/IgnorePlugin');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');
require('laravel-mix-polyfill');
/* eslint-enable */

mix.setPublicPath(__dirname);

mix
    .options({
        terser: {
            extractComments: false,
        },
    })

    // Compile editor
    .js(
        'js/index.js',
        'js/build/codeeditor.bundle.js',
    )
    .less(
        'less/codeeditor.less',
        'css/codeeditor.css',
    )
    .webpackConfig({
        plugins: [
            new MonacoWebpackPlugin({
                filename: 'js/build/[name].worker.js',
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
    })

    .after(() => {
        // Remove inline CSS calls to the codicon font
        const bundle = fs.readFileSync('js/build/codeeditor.bundle.js', 'utf8');
        newBundle = bundle.replace(/\\n\\n@font-face \{[^\}]+codicon[^\}]+}\\n\\n/g, '\\n\\n');
        fs.writeFileSync('js/build/codeeditor.bundle.js', newBundle);
    });
