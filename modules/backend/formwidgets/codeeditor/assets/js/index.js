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
            this.model = null;
            this.valueListener = null;
            this.editor = null;
            this.valueBag = this.element.querySelector('[data-value-bag]');

            this.observeElement();
        }

        /**
         * Data configuration defaults.
         *
         * @returns {Object}
         */
        defaults() {
            return {
                autoCloseTags: true,
                bracketColors: false,
                codeFolding: true,
                displayIndentGuides: true,
                fontSize: 12,
                highlightActiveLine: true,
                language: 'html',
                margin: 0,
                readOnly: false,
                showGutter: true,
                showInvisibles: false,
                showPrintMargin: false,
                tabSize: 4,
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
            this.setShortcuts();
        }

        getConfigOptions() {
            return {
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
                renderLineHighlight: this.config.get('highlightActiveLine') ? 'all' : 'none',
                renderWhitespace: this.config.get('showInvisibles') ? 'all' : 'selection',
                scrollBeyondLastLine: false,
                theme: 'vs-dark',
                value: this.valueBag.value,
                wordWrap: this.config.get('wordWrap') ? 'on' : 'off',
            };
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

        setShortcuts() {

        }
    }

    Snowboard.addPlugin('backend.formwidgets.codeeditor', CodeEditor);
    Snowboard['backend.ui.widgetHandler']().register('codeeditor', 'backend.formwidgets.codeeditor');
})(window.Snowboard);
