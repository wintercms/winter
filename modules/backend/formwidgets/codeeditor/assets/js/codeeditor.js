// Set webpack's public path dynamically to respect Winter's assetURL configuration
// This ensures code-split chunks (lazy-loaded language modules) load from the correct domain
// Without this, chunks load from APP_URL instead of assetURL (breaking CDN/subdirectory setups)
__webpack_public_path__ = window.Snowboard.url().asset('/modules/backend/formwidgets/codeeditor/assets/');

// Configure Monaco Environment BEFORE imports
// Supports both same-origin (dev) and cross-origin (CDN/Lambda) scenarios
// Fixes "Cannot read properties of null (reading 'webpackChunk')" in production builds
window.MonacoEnvironment = {
    getWorkerUrl: function (moduleId, label) {
        const basePath = window.Snowboard.url().asset('/modules/backend/formwidgets/codeeditor/assets/');
        const workerPaths = {
            'editorWorkerService': 'js/build/editor.worker.js',
            'typescript': 'js/build/ts.worker.js',
            'javascript': 'js/build/ts.worker.js',
            'css': 'js/build/css.worker.js',
            'less': 'js/build/css.worker.js',
            'scss': 'js/build/css.worker.js',
            'html': 'js/build/html.worker.js',
            'handlebars': 'js/build/html.worker.js',
            'razor': 'js/build/html.worker.js',
            'json': 'js/build/json.worker.js'
        };

        const workerPath = workerPaths[label] || workerPaths['editorWorkerService'];
        const workerUrl = basePath + workerPath;

        // Cross-origin detection and blob URL wrapping for CDN/Lambda scenarios
        const urlObj = new URL(workerUrl, window.location.href);
        if (urlObj.origin !== window.location.origin) {
            // Cross-origin: create blob URL wrapper
            const script = `importScripts('${workerUrl}');`;
            const blob = new Blob([script], { type: 'application/javascript' });
            return URL.createObjectURL(blob);
        }

        // Same-origin: direct load
        return workerUrl;
    }
};

import constrainedEditor from 'constrained-editor-plugin';
import { parse as parseXml } from 'fast-plist';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';

((Snowboard) => {
    /**
     * Code editor widget.
     *
     * This loads a Monaco editor instance with modifications for Winter CMS usage.
     *
     * @author Ben Thomson
     * @copyright Winter CMS
     */
    class CodeEditor extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.elementObserver = null;
            this.config = this.snowboard.dataConfig(this, element);
            this.events = this.snowboard['backend.ui.eventHandler'](this, 'backend.formwidget.codeeditor');
            this.alias = this.config.get('alias');
            this.monaco = monaco;  // Expose Monaco for accessing constants like MarkerSeverity
            this.model = null;
            this.resizeListener = false;
            this.visibilityListener = false;
            this.clickListener = false;
            this.clickStartedInEditor = false;
            this.editor = null;
            this.container = this.element.querySelector('.editor-container');
            this.valueBag = this.element.querySelector('[data-value-bag]');
            this.statusBar = this.element.querySelector('[data-status-bar]');
            if (this.statusBar) {
                this.language = this.statusBar.querySelector('.language');
                this.position = this.statusBar.querySelector('.position');
            }
            this.fullscreen = false;
            this.cachedThemes = {};
            this.resizeThrottle = null;
            this.callbacks = {
                fullScreenChange: () => this.onFullScreenChange(),
                resize: () => {
                    // Throttled event
                    clearTimeout(this.resizeThrottle);
                    this.resizeThrottle = setTimeout(() => this.onResize(), 80);
                },
                click: (event) => this.checkEditorClick(event),
                visibilityChange: () => this.onVisibilityChange(),
            };
            this.keybindings = [];
            this.customLineNumbering = null;
            this.disposables = [];

            this.observeElement();
        }

        /**
         * Data configuration defaults.
         *
         * Options available:
         *
         * - `alias`: Defines the alias of the field. Used for linking the code editor to the
         *      backend.
         * - `autoCloseTags`: Defines whether tags should be automatically closed.
         * - `bracketColors`: Defines whether brackets should be highlighted with matching colors
         *      for matching bracket pairs.
         * - `codeFolding`: Defines whether code blocks can be folded from the left gutter.
         * - `displayIndentGuides`: Defines whether indentation guides should be shown.
         * - `fontSize`: Defines the font size of the editor.
         * - `highlightActiveLine`: Defines whether the active line should be highlighted.
         * - `language`: Defines the language of the editor. This should not be changed via the
         *      config. Use the `setLanguage()` method instead.
         * - `margin`: Defines the margin size surrounded the horizontal sides of the editor.
         * - `readOnly`: Defines whether the editor should be read only.
         * - `semanticHighlighting`: Defines if the selected word, variable or method should be
         *      highlighted across the document.
         * - `selectionHighlighting`: Defines whether occurrences of the selected content should be
         *      highlighted.
         * - `showColors`: Defines whether colors should be shown (and modified) in the editor.
         * - `showGutter`: Defines whether the gutter should be shown.
         * - `showInvisibles`: Defines whether invisible characters should be shown.
         * - `showMinimap`: Defines whether the minimap should be shown.
         * - `showOccurrences`: Defines whether occurrences of the selected content should be
         *      highlighted.
         * - `showPrintMargin`: Defines whether the print margin should be shown.
         * - `showScrollbar`: Defines whether the scrollbars should be shown.
         * - `showSelectionOccurrences`: Defines whether occurrences of the selected content should
         *      be highlighted.
         * - `showSuggestions`: Defines whether suggestions should be shown.
         * - `tabSize`: Defines the size of a tab.
         * - `theme`: Defines the theme of the editor.
         * - `useSoftTabs`: Defines whether soft tabs (spaces) should be used.
         * - `wordWrap`: Defines whether the editor should wrap long lines.
         *
         * @returns {Object}
         */
        defaults() {
            return {
                alias: null,
                autoCloseTags: true,
                bracketColors: false,
                codeFolding: true,
                displayIndentGuides: true,
                fontSize: 12,
                highlightActiveLine: true,
                language: 'html',
                margin: 0,
                readOnly: false,
                semanticHighlighting: true,
                selectionHighlighting: true,
                showColors: true,
                showGutter: true,
                showInvisibles: false,
                showMinimap: true,
                showOccurrences: true,
                showPrintMargin: false,
                showScrollbar: true,
                showSelectionOccurrences: true,
                showSuggestions: true,
                tabSize: 4,
                theme: 'vs-dark',
                useSoftTabs: true,
                wordWrap: true,
            };
        }

        /**
         * Destructor.
         *
         * Disposes the editor and stops observation of the element.
         */
        destruct() {
            this.dispose();
            if (this.elementObserver) {
                this.elementObserver.disconnect();
            }
            if (this.visibilityListener) {
                document.removeEventListener('visibilitychange', this.callbacks.visibilityChange);
                this.visibilityListener = false;
            }
            if (this.clickListener) {
                document.removeEventListener('click', this.callbacks.click);
            }
        }

        /**
         * Disposes the editor.
         *
         * This allows the editor to be disposed of when the editor is not visible, freeing up memory
         * and preventing slowdown of the browser.
         */
        dispose() {
            if (this.disposables.length > 0) {
                this.disposables.forEach((disposable) => {
                    disposable.dispose();
                });
                this.disposables = [];
            }
            if (this.resizeListener) {
                window.removeEventListener('resize', this.callbacks.resize);
                this.resizeListener = false;
            }
            if (this.editor) {
                this.editor.dispose();
                this.editor = null;
            }
            this.events.fire('dispose', this, this.editor);
        }

        /**
         * Creates an intersection observer on the element.
         *
         * This will track whether the element is visible or not, disposing the editor when out of
         * view and re-enabling the editor when in view.
         */
        observeElement() {
            this.elementObserver = new IntersectionObserver((entries) => this.onObserve(entries));
            this.elementObserver.observe(this.element);
        }

        /**
         * Observation callback.
         *
         * @param {Array} entries
         */
        onObserve(entries) {
            if (entries[0].isIntersecting) {
                this.createEditor();
            } else {
                this.dispose();
            }
        }

        /**
         * Creates a Monaco editor instance in the given element.
         */
        createEditor() {

            // Force a specific height on the container - stops Monaco from indefinitely trying
            // to resize if the container has a fluid height
            this.container.style.height = null;
            this.container.style.height = Math.round(Number(getComputedStyle(this.container).height.replace('px', ''))) + 'px';

            this.editor = monaco.editor.create(this.element.querySelector('.editor-container'), this.getConfigOptions());

            // Backward compatibility: Set jQuery data for legacy Builder plugin code
            // Builder's JavaScript expects `.data('oc.codeEditor').editor` to access the editor
            if (window.jQuery) {
                window.jQuery(this.element).data('oc.codeEditor', this);
            }

            this.attachListeners();
            this.loadTheme();
            this.updateLanguage();
            this.enableStatusBarActions();
            this.registerKeyBindings();

            this.events.fire('create', this, this.editor);
        }

        /**
         * Completely disposes and re-creates the editor.
         */
        refresh() {
            this.dispose();
            window.requestAnimationFrame(() => this.createEditor());
        }

        /**
         * Gets the configuration object to provide to the editor.
         *
         * @returns {Object}
         */
        getConfigOptions() {
            const options = {
                automaticLayout: true,
                'bracketPairColorization.enabled': this.config.get('bracketColors'),
                colorDecorators: this.config.get('showColors'),
                detectIndentation: false,
                folding: this.config.get('codeFolding'),
                fontSize: this.config.get('fontSize'),
                guides: {
                    bracketPairs: this.config.get('bracketColors') ? 'active' : false,
                    bracketPairsHorizontal: this.config.get('bracketColors') ? 'active' : false,
                    indentation: this.config.get('displayIndentGuides'),
                },
                insertSpaces: this.config.get('useSoftTabs'),
                language: this.config.get('language'),
                lineNumbers: (this.customLineNumbering)
                    ? this.customLineNumbering
                    : this.config.get('showGutter') ? 'on' : 'off',
                minimap: {
                    enabled: this.config.get('showMinimap'),
                },
                occurrencesHighlight: this.config.get('showOccurrences'),
                quickSuggestions: this.config.get('showSuggestions'),
                renderLineHighlight: this.getLineHighlightOption(),
                renderWhitespace: this.config.get('showInvisibles') ? 'all' : 'selection',
                scrollbar: {
                    horizontalHasArrows: this.config.get('showScrollbar'),
                    horizontalScrollbarSize: this.config.get('showScrollbar') ? 10 : 0,
                    horizontalSliderSize: this.config.get('showScrollbar') ? 10 : 6,
                    verticalHasArrows: this.config.get('showScrollbar'),
                    verticalScrollbarSize: this.config.get('showScrollbar') ? 10 : 0,
                    verticalSliderSize: this.config.get('showScrollbar') ? 10 : 6,
                    useShadows: this.config.get('showScrollbar'),
                },
                scrollBeyondLastLine: false,
                selectionHighlight: this.config.get('showSelectionOccurrences'),
                'semanticHighlighting.enabled': this.config.get('semanticHighlighting')
                    ? 'configuredByTheme'
                    : false,
                tabSize: this.config.get('tabSize'),
                theme: this.config.get('themeVs'),
                value: this.valueBag.value,
            };

            if (this.config.get('wordWrap') === 'fluid') {
                options.wordWrap = 'on';
            } else if (typeof this.config.get('wordWrap') === 'number') {
                options.wordWrap = 'bounded';
                options.wordWrapColumn = this.config.get('wordWrap');
            } else {
                options.wordWrap = 'off';
            }

            if (this.config.get('showPrintMargin')) {
                options.rulers = [80];
            }

            return options;
        }

        getLineHighlightOption() {
            if (!this.config.get('highlightActiveLine')) {
                return 'none';
            }

            if (!this.config.get('showGutter')) {
                return 'line';
            }

            return 'all';
        }

        /**
         * Returns the editor instance.
         *
         * @returns {monaco.editor.IStandaloneCodeEditor}
         */
        getEditor() {
            return this.editor;
        }

        /**
         * Returns the editor model.
         *
         * @returns {monaco.editor.IModel}
         */
        getModel() {
            return this.model;
        }

        /**
         * Attaches listeners to the editor.
         *
         * The listeners in this widget include:
         *  - A value listener for when the editor value changes
         *  - A position listener when the editor caret position changes
         *  - A window resize listener
         *  - A visibility change listener
         */
        attachListeners() {
            this.model = this.editor.getModel();

            this.disposables.push(this.model.onDidChangeContent(() => {
                this.valueBag.value = this.model.getValue();
                this.events.fire('input', this.valueBag.value, this, this.editor);
            }));

            this.disposables.push(this.editor.onDidChangeCursorPosition((event) => {
                this.updatePosition(event.position);
                this.events.fire('position', event);
            }));

            this.disposables.push(this.editor.onDidChangeCursorSelection((event) => {
                this.events.fire('selection', event);
            }));
            this.disposables.push(this.editor.onMouseDown(() => {
                this.clickStartedInEditor = true;
            }));
            this.disposables.push(this.editor.onMouseUp(() => {
                setTimeout(() => {
                    this.clickStartedInEditor = false;
                }, 20);
            }));
            document.addEventListener('click', this.callbacks.click, {
                capture: true,
            });

            window.addEventListener('resize', this.callbacks.resize);
            this.resizeListener = true;

            if (!this.visibilityListener) {
                document.addEventListener('visibilitychange', this.callbacks.visibilityChange);
                this.visibilityListener = true;
            }
        }

        /**
         * Sets an editor configuration value.
         *
         * @param {*} key
         * @param {*} value
         */
        setConfig(key, value) {
            this.config.set(key, value);
            if (this.editor) {
                // Some keys need a full refresh to take effect
                if (['showPrintMargin'].includes(key)) {
                    this.refresh();
                } else {
                    this.editor.updateOptions(this.getConfigOptions());
                }
            }
        }

        /**
         * Gets the code editor value.
         */
        getValue() {
            return this.model.getValue();
        }

        /**
         * Sets the code editor value.
         *
         * @param {String} value
         */
        setValue(value) {
            this.model.setValue(value);
        }

        /**
         * Sets focus on the editor.
         */
        focus() {
            this.editor.focus();
        }

        /**
         * Sets markers (error/warning/info indicators) in the editor.
         *
         * Markers appear as icons in the gutter, squiggly underlines, and hover tooltips.
         * Multiple sources can add markers to the same editor without conflicts.
         *
         * @param {String} sourceId Unique identifier for the marker source (e.g., 'typescript', 'eslint')
         * @param {Array} markers Array of marker objects with position, message, severity
         *
         * Example:
         * wrapper.setMarkers('myPlugin', [{
         *     startLineNumber: 5,
         *     startColumn: 1,
         *     endLineNumber: 5,
         *     endColumn: Number.MAX_VALUE,
         *     message: 'Warning message',
         *     severity: monaco.MarkerSeverity.Warning
         * }]);
         *
         * Clear markers: wrapper.setMarkers('myPlugin', []);
         */
        setMarkers(sourceId, markers) {
            monaco.editor.setModelMarkers(this.editor.getModel(), sourceId, markers);
        }

        /**
         * Sets decorations (visual highlights without error semantics) in the editor.
         *
         * Unlike markers, decorations don't show as errors/warnings - just visual highlights.
         * Perfect for highlighting new content, diff views, or informational indicators.
         * Decorations can have background colors, custom CSS classes, and gutter indicators
         * without the squiggly underlines that markers have.
         *
         * @param {String} sourceId Unique identifier for this decoration source
         * @param {Array} decorations Array of decoration objects
         *
         * Example:
         * wrapper.setDecorations('myHighlight', [{
         *     range: new monaco.Range(5, 1, 5, Number.MAX_VALUE),
         *     options: {
         *         isWholeLine: true,
         *         className: 'myLineHighlight',              // CSS class for text
         *         linesDecorationsClassName: 'myGutterIcon', // CSS class for gutter
         *         inlineClassName: 'myInlineStyle',          // Inline text decoration
         *         glyphMarginClassName: 'myGlyphIcon'        // Glyph margin icon (far left)
         *     }
         * }]);
         *
         * Clear decorations: wrapper.setDecorations('myHighlight', []);
         */
        setDecorations(sourceId, decorations) {
            // Initialize decoration tracking if not already done
            if (!this._decorationIds) {
                this._decorationIds = {};
            }

            // Get previous decoration IDs for this source (to replace them)
            const oldIds = this._decorationIds[sourceId] || [];

            // Apply new decorations and get their IDs
            const newIds = this.editor.deltaDecorations(oldIds, decorations);

            // Store new IDs for future updates/clearing
            this._decorationIds[sourceId] = newIds;
        }

        /**
         * Gets the current editor position.
         *
         * Note that when selecting a range, this will be the end of the main selection.
         */
        getPosition() {
            return this.editor.getPosition();
        }

        /**
         * Gets the current editor selection.
         *
         * This will only include the main selection, which is generally the first selection range made.
         */
        getSelection() {
            return this.editor.getSelection();
        }

        /**
         * Gets the current editor selection.
         *
         * This is the same as `getSelection`, except that the selection range is copied to a new object to prevent any changes to the selection range from modifying positioning properties.
         */
        getSafeSelection() {
            const selection = this.editor.getSelection();
            return new monaco.Selection(selection.startLineNumber, selection.startColumn, selection.endLineNumber, selection.endColumn);
        }

        /**
         * Gets the current editor selections.
         *
         * This may include multiple selections.
         */
        getSelections() {
            return this.editor.getSelections();
        }

        /**
         * Inserts a value into the editor at the current position.
         *
         * @param {String} value
         * @returns {monaco.Selection[]}
         */
        insert(value) {
            return this.model.pushEditOperations(this.editor.getSelections(), [
                {
                    forceMoveMarkers: true,
                    range: this.editor.getSelection(),
                    text: value,
                },
            ]);
        }

        /**
         * Wraps the currently selected text with the given values.
         *
         * If no selection range is established, this acts like an "insert".
         *
         * @param {*} before
         * @param {*} after
         * @returns {monaco.Selection[]}
         */
        wrap(before, after) {
            if (this.editor.getSelection().isEmpty()) {
                return this.insert(`${before}${after}`);
            }

            return this.model.pushEditOperations(this.editor.getSelections(), [
                {
                    forceMoveMarkers: true,
                    range: this.editor.getSelection(),
                    text: `${before}${this.editor.getModel().getValueInRange(this.editor.getSelection())}${after}`,
                },
            ], () => [this.editor.getSelection()]);
        }

        /**
         * Unwraps the currently selected text from the given values.
         *
         * @param {*} before
         * @param {*} after
         * @returns {monaco.Selection[]}
         */
        unwrap(before, after) {
            if (this.editor.getSelection().isEmpty()) {
                return this.editor.getSelection();
            }

            let modification = this.editor.getModel().getValueInRange(this.editor.getSelection());
            if (modification.startsWith(before)) {
                modification = modification.substring(before.length);
            }
            if (modification.endsWith(after)) {
                modification = modification.substring(0, modification.length - after.length);
            }

            return this.model.pushEditOperations(this.editor.getSelections(), [
                {
                    forceMoveMarkers: true,
                    range: this.editor.getSelection(),
                    text: modification,
                },
            ], () => [this.editor.getSelection()]);
        }

        /**
         * Finds a single match in the editor content.
         *
         * @param {String|RegExp} search
         * @param {Boolean} matchCase
         * @returns {monaco.editor.FindMatch|null}
         */
        find(search, matchCase) {
            return this.findAll(search, matchCase)[0] || null;
        }

        /**
         * Finds all matches in the editor content.
         *
         * @param {String|RegExp} search
         * @param {Boolean} matchCase
         * @returns {monaco.editor.FindMatch[]}
         */
        findAll(search, matchCase) {
            const searchString = (search instanceof RegExp) ? search.source : search;
            const matches = this.model.findMatches(searchString, true, (search instanceof RegExp), matchCase || false, null, true, 1);

            return matches;
        }

        /**
         * Finds and replaces a single match in the editor content.
         *
         * @param {String|RegExp|monaco.Range} search
         * @param {String} replace
         * @param {Boolean} matchCase
         * @returns {monaco.editor.FindMatch|null}
         */
        replace(search, replace, matchCase, align = true) {
            if (typeof search !== 'string' && !(search instanceof RegExp)) {
                return this.model.pushEditOperations(
                    [monaco.Selection.fromRange(search)],
                    [
                        {
                            forceMoveMarkers: false,
                            range: new monaco.Range(search.startLineNumber, search.startColumn, search.endLineNumber, search.endColumn),
                            text: (align) ? this.alignText(replace, search.startColumn) : replace,
                        },
                    ],
                );
            }

            const found = this.find(search, matchCase);

            if (!found) {
                return;
            }

            return this.model.pushEditOperations(
                this.editor.getSelections(),
                [
                    {
                        forceMoveMarkers: false,
                        range: new monaco.Range(found.range.startLineNumber, found.range.startColumn, found.range.endLineNumber, found.range.endColumn),
                        text: (align) ? this.alignText(replace, search.startColumn) : replace,
                    },
                ],
            );
        }

        /**
         * Aligns the text to the given column.
         *
         * Any line breaks in the text will have additional spaces added to the start of the line
         * to ensure that it aligns with the original column.
         *
         * @param {String} text
         * @param {Number} column
         */
        alignText(text, column) {
            const lines = text.split('\n');
            return lines.map((line, index) => {
                if (index === 0) {
                    return line;
                }
                return `${' '.repeat(column - 1)}${line}`;
            }).join('\n');
        }

        /**
         * Sets the language of the editor.
         *
         * @param {String} language
         */
        setLanguage(language) {
            monaco.editor.setModelLanguage(this.model, language);
            this.setConfig('language', language);
            this.updateLanguage();
        }

        /**
         * Updates language status.
         * @param {String} language
         */
        updateLanguage() {
            if (!this.language) {
                return;
            }

            this.language.innerText = this.getConfigOptions().language.toUpperCase();
        }

        /**
         * Loads and applies a theme for the editor.
         *
         * @param {String} theme
         */
        loadTheme(theme) {
            const newTheme = theme || this.config.get('theme');
            const newThemeVS = newTheme.replace(/[^a-z0-9]+/g, '');

            if (newTheme === 'vs') {
                monaco.editor.setTheme('vs');
                this.setConfig('theme', 'vs');
                this.setConfig('themeVs', 'vs');
                this.updateStatusBarColor({
                    colors: {
                        'editor.foreground': '#000000',
                        'editor.background': '#ffffff',
                    }
                });
                return;
            }

            if (newTheme === 'vs-dark') {
                monaco.editor.setTheme('vs-dark');
                this.setConfig('theme', 'vs-dark');
                this.setConfig('themeVs', 'vs-dark');
                this.updateStatusBarColor({
                    colors: {
                        'editor.foreground': '#d4d4d4',
                        'editor.background': '#1E1E1E',
                    }
                });
                return;
            }

            if (!this.cachedThemes[newTheme]) {
                this.fetchTheme(newTheme).then(({ format, data }) => {
                    let themeData;

                    // Handle both JSON and tmTheme formats
                    if (format === 'json') {
                        // JSON theme - parse and use directly with Monaco
                        themeData = this.convertJsonTheme(data);
                    } else {
                        // tmTheme - convert from TextMate XML format
                        themeData = this.convertTmTheme(data);
                    }

                    this.cachedThemes[newTheme] = themeData;

                    monaco.editor.defineTheme(newThemeVS, themeData);
                    monaco.editor.setTheme(newThemeVS);
                    this.setConfig('theme', newTheme);
                    this.setConfig('themeVs', newThemeVS);
                    this.updateStatusBarColor(themeData);
                }).catch(() => {
                    // Theme loading failed - silently fall back to default theme
                });
                return;
            }

            monaco.editor.setTheme(newThemeVS);
            this.setConfig('theme', newTheme);
            this.setConfig('themeVs', newThemeVS);
            this.updateStatusBarColor(this.cachedThemes[newTheme]);
        }

        /**
         * Fetches a theme file directly from the themes directory.
         *
         * @param {String} themeName Theme filename (e.g., "twilight.tmTheme" or "one-dark-pro.json")
         * @returns {Promise<{format: string, data: string}>}
         */
        async fetchTheme(themeName) {
            // Handle legacy values without extension - default to .tmTheme
            let themeFile = themeName;
            if (!themeName.includes('.')) {
                themeFile = `${themeName}.tmTheme`;
            }

            // Determine format from file extension
            const isJson = themeFile.endsWith('.json');
            const format = isJson ? 'json' : 'tmTheme';

            // Construct URL using the same pattern as worker URLs
            const basePath = window.Snowboard.url().asset('/modules/backend/formwidgets/codeeditor/assets/themes/');
            const url = `${basePath}${themeFile}`;

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Theme "${themeName}" not found`);
            }

            const data = await response.text();
            return { format, data };
        }

        /**
         * Converts a JSON theme (VS Code/Monaco format) into a theme config for Monaco.
         *
         * @param {String} jsonContent
         * @returns {Object}
         */
        convertJsonTheme(jsonContent) {
            const theme = JSON.parse(jsonContent);

            // Monaco theme format
            const monacoTheme = {
                base: theme.type === 'light' ? 'vs' : 'vs-dark',
                inherit: true,
                rules: [],
                colors: {},
            };

            // Map token colors from the JSON theme
            if (theme.tokenColors && Array.isArray(theme.tokenColors)) {
                theme.tokenColors.forEach((tokenColor) => {
                    if (!tokenColor.scope) {
                        return;
                    }

                    const scopes = Array.isArray(tokenColor.scope)
                        ? tokenColor.scope
                        : tokenColor.scope.split(',').map((s) => s.trim());

                    scopes.forEach((scope) => {
                        const rule = { token: scope };

                        if (tokenColor.settings) {
                            if (tokenColor.settings.foreground) {
                                rule.foreground = tokenColor.settings.foreground.replace('#', '');
                            }
                            if (tokenColor.settings.background) {
                                rule.background = tokenColor.settings.background.replace('#', '');
                            }
                            if (tokenColor.settings.fontStyle) {
                                rule.fontStyle = tokenColor.settings.fontStyle;
                            }
                        }

                        monacoTheme.rules.push(rule);
                    });
                });
            }

            // Map UI colors
            if (theme.colors) {
                Object.keys(theme.colors).forEach((key) => {
                    monacoTheme.colors[key] = theme.colors[key];
                });
            }

            return monacoTheme;
        }

        /**
         * Converts a Textmate theme plist (XML) document into a theme config that Monaco supports.
         *
         * Based off the implementation at https://github.com/brijeshb42/monaco-themes.
         *
         * @param {String} content
         * @returns {Object}
         */
        convertTmTheme(content) {
            const themeData = parseXml(content);
            const globalColors = this.mapGlobalColors(themeData.settings.shift().settings);
            const scopes = [
                {
                    token: '',
                    foreground: globalColors['editor.foreground'].replace(/^#/, ''),
                    background: globalColors['editor.background'].replace(/^#/, ''),
                },
            ];

            themeData.settings.forEach((setting) => {
                if (!setting.scope) {
                    return;
                }

                const scope = {
                    token: setting.scope,
                };

                if (setting.settings.foreground) {
                    scope.foreground = this.parseColor(setting.settings.foreground).replace(/^#/, '');
                }
                if (setting.settings.background) {
                    scope.background = this.parseColor(setting.settings.background).replace(/^#/, '');
                }
                if (setting.settings.fontStyle) {
                    scope.fontStyle = setting.settings.fontStyle;
                }

                scopes.push(scope);
            });

            return {
                base: (this.isDarkTheme(globalColors['editor.background'])) ? 'vs-dark' : 'vs',
                inherit: false,
                rules: this.populateMissingScopes(scopes),
                colors: globalColors,
            };
        }

        /**
         * Maps global colors needed for the theme.
         *
         * @param {Object} settings
         * @returns {Object}
         */
        mapGlobalColors(settings) {
            const colors = {};
            const colorMap = [
                {
                    tm: 'foreground',
                    mn: 'editor.foreground',
                },
                {
                    tm: 'background',
                    mn: 'editor.background',
                },
                {
                    tm: 'selection',
                    mn: 'editor.selectionBackground',
                },
                {
                    tm: 'inactiveSelection',
                    mn: 'editor.inactiveSelectionBackground',
                },
                {
                    tm: 'selectionHighlightColor',
                    mn: 'editor.selectionHighlightBackground',
                },
                {
                    tm: 'findMatchHighlight',
                    mn: 'editor.findMatchHighlightBackground',
                },
                {
                    tm: 'currentFindMatchHighlight',
                    mn: 'editor.findMatchBackground',
                },
                {
                    tm: 'hoverHighlight',
                    mn: 'editor.hoverHighlightBackground',
                },
                {
                    tm: 'wordHighlight',
                    mn: 'editor.wordHighlightBackground',
                },
                {
                    tm: 'wordHighlightStrong',
                    mn: 'editor.wordHighlightStrongBackground',
                },
                {
                    tm: 'findRangeHighlight',
                    mn: 'editor.findRangeHighlightBackground',
                },
                {
                    tm: 'findMatchHighlight',
                    mn: 'peekViewResult.matchHighlightBackground',
                },
                {
                    tm: 'referenceHighlight',
                    mn: 'peekViewEditor.matchHighlightBackground',
                },
                {
                    tm: 'lineHighlight',
                    mn: 'editor.lineHighlightBackground',
                },
                {
                    tm: 'rangeHighlight',
                    mn: 'editor.rangeHighlightBackground',
                },
                {
                    tm: 'guide',
                    mn: 'editorIndentGuide.background',
                },
                {
                    tm: 'activeGuide',
                    mn: 'editorIndentGuide.activeBackground',
                },
                {
                    tm: 'selectionBorder',
                    mn: 'editor.selectionHighlightBorder',
                },
            ];

            colorMap.forEach((color) => {
                if (settings[color.tm]) {
                    colors[color.mn] = this.parseColor(settings[color.tm]);
                }
            });

            return colors;
        }

        /**
         * Parses a colour and returns it in a readable format.
         *
         * @param {String} color
         * @returns {String}
         */
        parseColor(color) {
            let currentColor = color;

            if (!currentColor.length) {
                return null;
            }
            if (currentColor.length === 4) {
                currentColor = color.replace(/[a-fA-F\d]/g, '$&$&');
            }
            if (currentColor.length === 7) {
                return currentColor;
            }
            if (color.length === 9) {
                return color;
            }
            if (!color.match(/^#(..)(..)(..)(..)$/)) {
                // Invalid color format - return original color
                return color;
            }
            const rgba = color.match(/^#(..)(..)(..)(..)$/).slice(1).map((c) => parseInt(c, 16));
            rgba[3] = (rgba[3] / 0xFF).toPrecision(2);
            return `rgba(${rgba.join(', ')})`;
        }

        /**
         * Converts a hexidecimal color to an RGB array.
         *
         * @param {String} color
         * @returns {Array}
         */
        rgbColor(color) {
            if (typeof color === 'object') {
                return color;
            }
            if (color[0] === '#') {
                return color.match(/^#(..)(..)(..)/).slice(1).map((c) => parseInt(c, 16));
            }

            return color.match(/\(([^,]+),([^,]+),([^,]+)/).slice(1).map((c) => parseInt(c, 10));
        }

        /**
         * Determines if a theme is dark by the color of the background.
         *
         * @param {String} background
         * @returns {Boolean}
         */
        isDarkTheme(background) {
            const rgb = this.rgbColor(background);
            return (((0.21 * rgb[0] + 0.72 * rgb[1] + 0.07 * rgb[2]) / 255) < 0.5);
        }

        /**
         * Goes through the scopes retrieved from the theme and adds any scopes that are needed for
         * the Monarch tokenizer.
         *
         * It appears that the tokenizer in Monaco is different to the one in VScode, which means
         * that tokens are interpreted a little differently. We'll try a whole lot of alternate
         * scope names and find the best fit.
         *
         * @param {Array} scopes
         * @returns {Array}
         */
        populateMissingScopes(scopes) {
            const mapScopes = {
                comment: ['comment.block', 'comment.line'],
                number: ['constant.numeric', 'constant.number', 'string.number'],
                regexp: ['string.regexp'],
                tag: ['meta.tag', 'entity.name.tag'],
                metatag: ['meta.tag', 'declaration.tag', 'constant.language', 'entity.name.tag'],
                annotation: ['meta.embedded', 'meta.annotation', 'string.annotation', 'comment.block', 'comment.line'],
                attribute: ['entity.other.attribute-name', 'support.type.property-name'],
                identifier: ['entity.name.function', 'meta.tag', 'declaration.tag', 'constant.language', 'entity.name.tag', 'support.type'],
                type: ['support.type', 'support.function'],
                operator: ['support.constant', 'constant.numeric', 'constant.number', 'string.number', 'support'],
                'attribute.name': ['support.type', 'support.constant', 'entity.other.attribute-name', 'support.type.property-name'],
                'attribute.value.html': ['string.quoted.double.html', 'string.quoted.single.html', 'string.quoted.double', 'string.quoted.single', 'string'],
                'attribute.value.unit': ['keyword.unit', 'support.unit', 'keyword', 'support', 'number', 'string.number', 'constant.numeric', 'constant.number'],
                'attribute.value.number': ['number', 'string.number', 'constant.numeric', 'constant.number'],
            };
            const processedScopes = {};
            Object.entries(mapScopes).forEach(([scope, map]) => {
                processedScopes[scope] = {
                    scope,
                    map,
                    currentSettings: null,
                    currentRank: null,
                };
            });

            scopes.forEach((scope) => {
                if (!scope.token) {
                    return;
                }

                const tokens = scope.token.split(/, +/);
                tokens.forEach((token, index) => {
                    const nested = token.split(/ +/);

                    nested.forEach((nestToken, nestIndex) => {
                        const matches = Object.values(processedScopes).filter((item) => item.map.filter((mapItem) => nestToken.startsWith(mapItem)).length > 0);

                        if (!matches.length) {
                            return;
                        }

                        // Determine the rank of this scope
                        matches.forEach((match) => {
                            const rank = 10 - index - (nestIndex * 2) - (!match.map.includes(nestToken) ? 5 : (match.map.indexOf(nestToken)));
                            if (match.currentRank !== null && match.currentRank >= rank) {
                                return;
                            }

                            processedScopes[match.scope].currentSettings = {};
                            processedScopes[match.scope].currentRank = rank;

                            if (scope.foreground) {
                                processedScopes[match.scope].currentSettings.foreground = scope.foreground;
                            }
                            if (scope.background) {
                                processedScopes[match.scope].currentSettings.background = scope.background;
                            }
                            if (scope.fontStyle) {
                                processedScopes[match.scope].currentSettings.fontStyle = scope.fontStyle;
                            }
                        });
                    });
                });
            });

            // Re-apply scopes only if needed
            Object.values(processedScopes).forEach((scope) => {
                if (!scope.currentSettings) {
                    return;
                }
                if (scopes.some((item) => item.token === scope.scope)) {
                    return;
                }

                const newScope = {
                    token: scope.scope,
                };
                if (scope.currentSettings.foreground) {
                    newScope.foreground = scope.currentSettings.foreground;
                }
                if (scope.currentSettings.background) {
                    newScope.background = scope.currentSettings.background;
                }
                if (scope.currentSettings.fontStyle) {
                    newScope.fontStyle = scope.currentSettings.fontStyle;
                }
                scopes.push(newScope);
            });

            // Split scopes up
            const splitScopes = [];
            scopes.forEach((scope) => {
                if (scope.token.indexOf(',') === -1) {
                    splitScopes.push(scope);
                    return;
                }

                const tokens = scope.token.split(/, +/);
                tokens.forEach((token) => {
                    splitScopes.push({
                        token,
                        foreground: scope.foreground,
                        background: scope.background,
                        fontStyle: scope.fontStyle,
                    });
                });
            });

            return splitScopes;
        }

        /**
         * Updates the position indicator in the status bar.
         *
         * @param {Object} position
         */
        updatePosition(position) {
            if (!this.position) {
                return;
            }

            this.position.innerText = `Line ${position.lineNumber}, Column ${position.column}`;
        }

        /**
         * Updates the styling of the status bar based on the theme.
         *
         * @param {Object} colors
         */
        updateStatusBarColor(colors) {
            if (!this.statusBar) {
                return;
            }

            const foreground = colors.colors['editor.foreground'];
            const background = colors.colors['editor.background'];
            if (this.isDarkTheme(background)) {
                this.statusBar.classList.add('is-dark');
            } else {
                this.statusBar.classList.remove('is-dark');
            }
            this.statusBar.style.color = foreground;
            this.statusBar.style.backgroundColor = background;
        }

        /**
         * Enables status bar action functionality.
         */
        enableStatusBarActions() {
            if (!this.statusBar) {
                return;
            }

            const fullscreen = this.statusBar.querySelector('[data-full-screen]');
            fullscreen.addEventListener('click', () => {
                if (!this.fullscreen) {
                    this.element.requestFullscreen({
                        navigationUI: 'hide',
                    }).then(() => {
                        this.fullscreen = true;
                        fullscreen.classList.add('active');
                        this.element.addEventListener('fullscreenchange', this.callbacks.fullScreenChange);
                        // Use Monaco's layout() instead of refresh() to avoid disposing the editor
                        if (this.editor) {
                            window.requestAnimationFrame(() => {
                                this.editor.layout();
                            });
                        }
                    });
                } else {
                    document.exitFullscreen();
                }
            });
        }

        /**
         * Tracks if the element exists fullscreen mode and resets the fullscreen state.
         */
        onFullScreenChange() {
            if (!document.fullscreenElement) {
                this.fullscreen = false;
                if (this.statusBar) {
                    this.statusBar.querySelector('[data-full-screen]').classList.remove('active');
                }
                this.element.removeEventListener('fullscreenchange', this.callbacks.fullScreenChange);
                // Use Monaco's layout() instead of refresh() to avoid disposing the editor
                // Monaco has automaticLayout: true, but we need to trigger it for fullscreen transitions
                if (this.editor) {
                    window.requestAnimationFrame(() => {
                        this.editor.layout();
                    });
                }
            }
        }

        /**
         * Tracks a resize event from the browser window.
         */
        onResize() {
            // Use Monaco's layout() instead of refresh() for better performance
            // Monaco has automaticLayout: true, but manual triggers help in some edge cases
            if (this.editor) {
                this.editor.layout();
            }
        }

        /**
         * Tracks a visibility change event from the browser window (ie. the user changes tab or minimizes the window)
         */
        onVisibilityChange() {
            if (document.hidden) {
                this.dispose();
            } else {
                this.createEditor();
            }
        }

        /**
         * Allows the editor to start from a specific line.
         *
         * This acts as a way to define prepended content for the editor that cannot be seen or
         * edited. For example, the "Code" view in the CMS editor has a line to define the opening
         * "<?php" tag, but this is hidden from the user.
         *
         * @param {Number} line
         */
        fromLine(line) {
            if (line <= 1) {
                this.customLineNumbering = null;
                return;
            }

            const modelLines = this.getModel().getFullModelRange().endLineNumber;
            const modelEndColumn = this.getModel().getFullModelRange().endColumn;

            if (line > modelLines) {
                this.customLineNumbering = null;
                throw new Error('The line number is greater than the number of lines in the editor.');
            }

            const hiddenRange = new monaco.Range(1, 1, line - 1, modelEndColumn);
            const editableRange = new monaco.Range(line, 1, modelLines, modelEndColumn);

            const constrainedInstance = constrainedEditor(monaco);
            constrainedInstance.initializeIn(this.editor);
            constrainedInstance.addRestrictionsTo(this.getModel(), [{
                range: [editableRange.startLineNumber, editableRange.startColumn, editableRange.endLineNumber, editableRange.endColumn],
                allowMultiline: true,
            }]);

            this.editor.setHiddenAreas([hiddenRange]);

            if (this.config.get('showGutter')) {
                this.customLineNumbering = (number) => {
                    return (number >= line) ? String((number - line) + 1) : '';
                };
                this.editor.updateOptions(this.getConfigOptions());
            }
        }

        /**
         * Adds a keybinding to the editor.
         *
         * The `keyCode` parameter can be either a string or an object, if you need to specify modifiers.
         *
         * For example:
         *
         * ```js
         * this.addKeyBinding('Ctrl+B', () => {});
         * // - or -
         * this.addKeyBinding({ key: 'B', ctrl: true }, () => {});
         * ```
         *
         * Note that the Command key is considered Ctrl on Macs.
         *
         * @param {Object|string} keyCode
         * @param {*} callback
         */
        addKeyBinding(keyCode, callback) {
            const keybinding = this.normalizeKeyBinding(keyCode);

            this.keybindings.push({
                keybinding,
                callback,
            });

            if (this.editor) {
                const keyName = monaco.KeyCode[`Key${keybinding.key.toUpperCase()}`];
                let binding = 0;

                if (keybinding.shift) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.Shift;
                }
                if (keybinding.ctrl) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.CtrlCmd;
                }
                if (keybinding.alt) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.Alt;
                }

                /* eslint-disable-next-line no-bitwise */
                binding |= keyName;

                this.editor.addCommand(binding, callback);
            }
        }

        /**
         * Removes a keybinding from the editor.
         *
         * Note that Monaco doesn't allow us to remove keybindings, so we'll just stub out the
         * callback if this is called.
         *
         * @param {Object|string} keyCode
         * @param {*} callback
         */
        removeKeyBinding(keyCode) {
            const keybinding = this.normalizeKeyBinding(keyCode);

            const index = this.keybindings.findIndex((item) => item.keybinding === keybinding);

            if (index === -1) {
                return;
            }

            this.keybindings[index].callback = () => {};
        }

        /**
         * Normalizes a keycode into an object.
         *
         * @param {Object|string} keyCode
         * @returns Object
         */
        normalizeKeyBinding(keyCode) {
            let keyBinding = {
                key: null,
                ctrl: false,
                alt: false,
                shift: false,
            };

            if (typeof keyCode === 'string') {
                if (keyCode.startsWith('Shift+Ctrl+')) {
                    keyBinding.key = keyCode.replace('Shift+Ctrl+', '');
                    keyBinding.shift = true;
                    keyBinding.ctrl = true;
                } else if (keyCode.startsWith('Shift+Alt+')) {
                    keyBinding.key = keyCode.replace('Shift+Alt+', '');
                    keyBinding.shift = true;
                    keyBinding.alt = true;
                } else if (keyCode.startsWith('Ctrl+')) {
                    keyBinding.key = keyCode.replace('Ctrl+', '');
                    keyBinding.ctrl = true;
                } else if (keyCode.startsWith('Alt+')) {
                    keyBinding.key = keyCode.replace('Alt+', '');
                    keyBinding.alt = true;
                }
            } else {
                keyBinding = { ...keyBinding, ...keyCode };
            }

            return keyBinding;
        }

        /**
         * Registers keybindings in the editor.
         */
        registerKeyBindings() {
            if (this.keybindings.length === 0) {
                return;
            }

            this.keybindings.forEach((item) => {
                const keyName = monaco.KeyCode[`Key${item.keybinding.key.toUpperCase()}`];
                let binding = 0;

                if (item.keybinding.shift) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.Shift;
                }
                if (item.keybinding.ctrl) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.CtrlCmd;
                }
                if (item.keybinding.alt) {
                    /* eslint-disable-next-line no-bitwise */
                    binding |= monaco.KeyMod.Alt;
                }

                /* eslint-disable-next-line no-bitwise */
                binding |= keyName;

                this.editor.addCommand(binding, item.callback);
            });
        }

        checkEditorClick(event) {
            if (this.clickStartedInEditor && !this.element.contains(event.target)) {
                event.stopImmediatePropagation();
                event.preventDefault();
            }

            this.clickStartedInEditor = false;
        }

        /**
         * Wrapper method to add a code lens provider.
         *
         * @param {(model: monaco.editor.ITextModel, token: monaco.CancellationToken) => Promise<monaco.languages.CodeLens[]>} provider
         * @param {(model: monaco.editor.ITextModel, codeLens: monaco.languages.CodeLens, token: monaco.CancellationToken) => Promise<monaco.languages.CodeLens>} resolver
         */
        addCodeLens(language, provider, resolver = null) {
            this.disposables.push(
                monaco.languages.registerCodeLensProvider(language, {
                    provideCodeLenses: (model, token) => {
                        return provider(model, token);
                    },
                    resolveCodeLens: (model, codeLens, token) => {
                        return resolver(model, codeLens, token) ?? codeLens;
                    },
                })
            );
        }
    }

    Snowboard.addPlugin('backend.formwidgets.codeeditor', CodeEditor);
    Snowboard['backend.ui.widgetHandler']().register('codeeditor', 'backend.formwidgets.codeeditor');
})(window.Snowboard);
