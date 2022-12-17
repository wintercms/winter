import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import { parse as parseXml } from 'fast-plist';

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
            this.alias = this.config.get('alias');
            this.model = null;
            this.valueListener = null;
            this.editor = null;
            this.valueBag = this.element.querySelector('[data-value-bag]');
            this.cachedThemes = {};

            this.observeElement();
        }

        /**
         * Data configuration defaults.
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
                showColors: true,
                showGutter: true,
                showInvisibles: false,
                showMinimap: true,
                showPrintMargin: false,
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
        }

        /**
         * Disposes the editor.
         *
         * This allows the editor to be disposed of when the editor is not visible, freeing up memory
         * and preventing slowdown of the browser.
         */
        dispose() {
            if (this.valueListener) {
                this.valueListener.dispose();
                this.valueListener = null;
            }
            if (this.editor) {
                this.editor.dispose();
                this.editor = null;
            }
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
            // This is needed to ensure the language chunks are loaded from the correct location
            // eslint-disable-next-line
            __webpack_public_path__ = this.snowboard.url().to('/modules/backend/formwidgets/codeeditor/assets/');

            this.editor = monaco.editor.create(this.element.querySelector('.editor-container'), this.getConfigOptions());

            this.attachValueListener();
            this.loadTheme();
        }

        /**
         * Completely disposes and re-creates the editor.
         */
        refresh() {
            this.dispose();
            this.createEditor();
        }

        /**
         * Gets the configuration object to provide to the editor.
         *
         * @returns {Object}
         */
        getConfigOptions() {
            const options = {
                automaticLayout: true,
                colorDecorators: this.config.get('showColors'),
                detectIndentation: false,
                folding: this.config.get('codeFolding'),
                fontSize: this.config.get('fontSize'),
                guides: {
                    bracketPairs: this.config.get('bracketColors'),
                    bracketPairsHorizontal: this.config.get('bracketColors') ? 'active' : false,
                    indentation: this.config.get('displayIndentGuides'),
                },
                insertSpaces: this.config.get('useSoftTabs'),
                language: this.config.get('language'),
                lineNumbers: this.config.get('showGutter') ? 'on' : 'off',
                minimap: {
                    enabled: this.config.get('showMinimap'),
                },
                renderLineHighlight: this.config.get('highlightActiveLine') ? 'all' : 'none',
                renderWhitespace: this.config.get('showInvisibles') ? 'all' : 'selection',
                scrollBeyondLastLine: false,
                tabSize: this.config.get('tabSize'),
                theme: this.config.get('theme'),
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

        /**
         * Attaches a value listener to the editor.
         *
         * This will update the value of the field when the editor has a change made to it.
         */
        attachValueListener() {
            this.model = this.editor.getModel();
            this.valueListener = this.model.onDidChangeContent(() => {
                this.valueBag.value = this.model.getValue();
            });
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
         * Sets the code editor value.
         *
         * @param {String} value
         */
        setValue(value) {
            this.model.setValue(value);
        }

        /**
         * Sets the language of the editor.
         *
         * @param {String} language
         */
        setLanguage(language) {
            monaco.editor.setModelLanguage(this.model, language);
            this.setConfig('language', language);
        }

        /**
         * Loads and applies a theme for the editor.
         *
         * @param {String} theme
         */
        loadTheme(theme) {
            const newTheme = theme || this.config.get('theme');
            const newThemeVS = newTheme.replace(/[^a-z0-9]+/g, '');

            if (newTheme === 'vs-dark') {
                return;
            }

            if (!this.cachedThemes[newTheme]) {
                this.snowboard.request(
                    this.element,
                    (this.alias) ? `${this.alias}::onLoadTheme` : 'onLoadTheme',
                    {
                        data: {
                            theme: theme || this.config.get('theme'),
                        },
                        success: (data) => {
                            if (data.result) {
                                const themeData = this.convertTmTheme(data.result);
                                this.cachedThemes[newTheme] = themeData;

                                monaco.editor.defineTheme(newThemeVS, themeData);
                                monaco.editor.setTheme(newThemeVS);
                                this.setConfig('theme', newThemeVS);
                            }
                        },
                        error: () => {
                            console.log(`Unable to load theme "${newTheme}"`);
                        },
                    },
                );
                return;
            }

            monaco.editor.setTheme(newThemeVS);
            this.setConfig('theme', newThemeVS);
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
                console.error("can't parse color", color);
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
    }

    Snowboard.addPlugin('backend.formwidgets.codeeditor', CodeEditor);
    Snowboard['backend.ui.widgetHandler']().register('codeeditor', 'backend.formwidgets.codeeditor');
})(window.Snowboard);
