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
        // Keep CSS url() values untouched so the codicon @font-face URL stays relative,
        // pointing at the font file shipped next to the compiled CSS (the same way the
        // pre-monaco-0.57 artifacts shipped it). Rewriting URLs with the default
        // `processCssUrls: true` produces a root-absolute URL (/fonts/...) that
        // breaks on CDN/subdirectory installs.
        processCssUrls: false,
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
                // monaco-editor >= 0.56 restricts subpath resolution via its `exports`
                // map (entry point reorganization), so point the plugin at the
                // module's absolute path explicitly.
                monacoEditorPath: path.resolve(path.dirname(require.resolve('monaco-editor/editor/editor.api')), '..', '..', '..'),
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
                    'dropOrPasteInto',
                    'find',
                    'folding',
                    'gotoSymbol',
                    'hover',
                    'inPlaceReplace',
                    'indentation',
                    'inlayHints',
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
        // monaco 0.57 note: the previous regex surgery cut into minified JS code and
        // produced an invalid bundle ("Unexpected string"). The codicon CSS module push
        // is now replaced via an exact literal; the runtime generates the codicon CSS
        // itself (standaloneThemeService injects _codiconCSS), so the static push is
        // redundant. If the literal stops matching (future builds), the replace becomes
        // a no-op and the bundle keeps the inline CSS (fail-open, never fails the build).
        const codiconPush = `l.push([e.id,"@font-face{font-display:block;font-family:codicon;src:url("+c+') format("truetype")}.codicon[class*=codicon-]{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;display:inline-block;font:normal normal normal 16px/1 codicon;text-align:center;text-decoration:none;text-rendering:auto;text-transform:none;-moz-user-select:none;user-select:none;-webkit-user-select:none}',""])`;
        if (bundle.includes(codiconPush)) {
            bundle = bundle.replace(codiconPush, 'l.push([e.id,"",""])');
        }

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
