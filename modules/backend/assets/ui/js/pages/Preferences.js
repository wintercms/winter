import { delegate } from 'jquery-events-to-dom-events';

((Snowboard) => {
    class Preferences extends Snowboard.Singleton {
        construct() {
            this.widget = null;
        }

        listens() {
            return {
                'backend.widget.initialized': 'onWidgetInitialized',
            };
        }

        onWidgetInitialized(element, widget) {
            if (element === document.getElementById('CodeEditor-formEditorPreview-_editor_preview')) {
                this.widget = widget;
                this.enablePreferences();
            }
        }

        enablePreferences() {
            delegate('change');

            const checkboxes = {
                show_gutter: 'showGutter',
                highlight_active_line: 'highlightActiveLine',
                use_hard_tabs: '!useSoftTabs',
                display_indent_guides: 'displayIndentGuides',
                show_invisibles: 'showInvisibles',
                show_print_margin: 'showPrintMargin',
                show_minimap: 'showMinimap',
                enable_folding: 'codeFolding',
                bracket_colors: 'bracketColors',
                show_colors: 'showColors',
            };

            Object.entries(checkboxes).forEach(([key, value]) => {
                this.element(key).addEventListener('change', (event) => {
                    this.widget.setConfig(
                        value.replace(/^!/, ''),
                        /^!/.test(value) ? !event.target.checked : event.target.checked,
                    );
                });
            });

            this.element('theme').addEventListener('$change', (event) => {
                this.widget.loadTheme(event.target.value);
            });

            this.element('font_size').addEventListener('$change', (event) => {
                this.widget.setConfig('fontSize', event.target.value);
            });

            this.element('tab_size').addEventListener('$change', (event) => {
                this.widget.setConfig('tabSize', event.target.value);
            });

            this.element('word_wrap').addEventListener('$change', (event) => {
                const { value } = event.target;
                switch (value) {
                    case 'off':
                        this.widget.setConfig('wordWrap', false);
                        break;
                    case 'fluid':
                        this.widget.setConfig('wordWrap', 'fluid');
                        break;
                    default:
                        this.widget.setConfig('wordWrap', parseInt(value, 10));
                }
            });

            document.querySelectorAll('[data-switch-lang]').forEach((element) => {
                element.addEventListener('click', (event) => {
                    event.preventDefault();
                    const language = element.dataset.switchLang;
                    const template = document.querySelector(`[data-lang-snippet="${language}"]`);

                    if (!template) {
                        return;
                    }

                    this.widget.setValue(template.textContent.trim());
                    this.widget.setLanguage(language);
                });
            });

            this.widget.events.once('create', () => {
                const event = new MouseEvent('click');
                document.querySelector('[data-switch-lang="css"]').dispatchEvent(event);
            });
        }

        element(key) {
            return document.getElementById(`Form-field-Preference-editor_${key}`);
        }
    }

    Snowboard.addPlugin('backend.preferences', Preferences);
})(window.Snowboard);
