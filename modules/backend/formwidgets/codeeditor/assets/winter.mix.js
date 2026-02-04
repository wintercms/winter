/* eslint-disable */
const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');
require('laravel-mix-polyfill');
/* eslint-enable */

// Clean js/build directory before compiling
const buildDir = path.join(__dirname, 'js/build');
if (fs.existsSync(buildDir)) {
    fs.readdirSync(buildDir).forEach((file) => {
        const filePath = path.join(buildDir, file);
        if (fs.statSync(filePath).isDirectory()) {
            fs.rmSync(filePath, { recursive: true });
        } else {
            fs.unlinkSync(filePath);
        }
    });
}

mix.setPublicPath(__dirname);

mix
    .options({
        terser: {
            extractComments: false,
        },
    })

    // Compile editor
    .js(
        'js/codeeditor.js',
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
                    'codelens',
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
        let bundle = fs.readFileSync('js/build/codeeditor.bundle.js', 'utf8');

        // Remove inline CSS calls to the codicon font
        bundle = bundle.replace(/@font-face[^{]*\{(?:[^{}]|{[^}]*})*?codicon[^}]*?\}/g, '');

        // Remove Monaco plugin's MonacoEnvironment assignment to prevent timing issues
        // This allows our runtime window.MonacoEnvironment from codeeditor.js to be used exclusively
        // The plugin's version uses hardcoded webpack publicPath which breaks CDN/subdirectory support
        // Pattern: ...}),self.MonacoEnvironment=(...});var
        // Replace: ,self.MonacoEnvironment=(...}); with just ;
        // Result: ...});var (valid JavaScript with proper statement terminator)
        const monacoEnvStart = bundle.indexOf(',self.MonacoEnvironment=');
        if (monacoEnvStart !== -1) {
            const afterStart = bundle.substring(monacoEnvStart);
            const monacoEnvEnd = afterStart.indexOf('});var');
            if (monacoEnvEnd !== -1) {
                // Replace the pattern with semicolon to maintain statement terminator
                // +3 to skip past '});' (3 characters)
                bundle = bundle.substring(0, monacoEnvStart) + ';' + bundle.substring(monacoEnvStart + monacoEnvEnd + 3);
            }
        }

        fs.writeFileSync('js/build/codeeditor.bundle.js', bundle);
    });
