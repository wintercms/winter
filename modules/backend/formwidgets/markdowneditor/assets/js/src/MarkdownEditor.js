import Bold from './actions/Bold';
import Italic from './actions/Italic';

((Snowboard) => {
    /**
     * Markdown editor widget.
     *
     * This loads a Monaco editor instance set up specifically for editing Markdown files, with a
     * preview of the Markdown rendered to HTML on the right.
     *
     * @author Alexey Bobkov, Samuel Georges (initial implementation)
     * @author Ben Thomson (converting to Monaco/Snowboard)
     * @copyright Winter CMS
     */
    class MarkdownEditor extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.toolbar = element.querySelector('.markdowneditor-toolbar');
            this.editorElement = element.querySelector('.markdowneditor-editor');
            this.preview = element.querySelector('.markdowneditor-preview');
            this.valueBag = element.querySelector('[data-value-bag]');
            this.config = this.snowboard.dataConfig(this, this.element);
            this.previewTimer = null;
            this.editor = null;
            this.actions = [];
            this.events = {
                value: (value) => this.onValue(value),
            };

            this.createEditor();
            this.registerActions();
            this.createToolbar();
        }

        defaults() {
            return {
                refreshHandler: null,
                useMediaManager: false,
            };
        }

        destruct() {
            if (this.editor) {
                this.editor.events.off('input', this.events.value);
                this.editor.destruct();
            }
        }

        /**
         * Defines the dependencies.
         *
         * @returns {string[]}
         */
        dependencies() {
            return ['backend.formwidgets.codeeditor'];
        }

        createEditor() {
            this.editor = this.snowboard['backend.formwidgets.codeeditor'](this.editorElement);

            // Set config
            this.editor.loadTheme('vs');
            this.editor.setConfig('fontSize', 14);
            this.editor.setConfig('showGutter', false);
            this.editor.setConfig('showMinimap', false);
            this.editor.setConfig('showColors', false);
            this.editor.setConfig('showScrollbar', false);
            this.editor.setConfig('showSuggestions', false);
            this.editor.setConfig('displayIndentGuides', false);
            this.editor.setConfig('codeFolding', false);
            this.editor.setConfig('wordWrap', 'fluid');
            this.editor.setConfig('showOccurrences', false);
            this.editor.setConfig('showSelectionOccurrences', false);
            this.editor.setConfig('semanticHighlighting', false);
            this.editor.events.once('create', () => {
                this.editor.setLanguage('markdown');
                this.registerKeyBindings();
            });

            // Value listener
            this.editor.events.on('input', this.events.value);

            // Position / selection lister
            this.editor.events.on('position', () => {
                this.updateToolbarButtonStates();
            });
            this.editor.events.on('selection', () => {
                this.updateToolbarButtonStates();
            });
        }

        onValue() {
            this.valueBag.value = this.editor.getValue();

            if (this.previewTimer) {
                clearTimeout(this.previewTimer);
            }

            this.previewTimer = setTimeout(() => {
                this.updatePreview();
                this.previewTimer = null;
            }, 100);
        }

        updatePreview() {
            this.snowboard.request(this.element, this.config.get('refreshHandler'), {
                loading: false,
                success: (data) => {
                    this.preview.innerHTML = data.preview;
                },
            });
        }

        registerActions() {
            this.actions.push({
                name: 'bold',
                action: new Bold(this, this.editor),
            });
            this.actions.push({
                name: 'italic',
                action: new Italic(this, this.editor),
            });
        }

        findAction(name) {
            const action = this.actions.find((item) => item.name === name);
            return (!action) ? null : action.action;
        }

        createToolbar() {
            // Reset toolbar contents
            if (this.toolbar.children.length) {
                this.toolbar.children.forEach((child) => {
                    child.remove();
                });
            }

            // Create buttons for each action
            this.actions.forEach((action) => {
                this.toolbar.appendChild(this.createToolbarButton(action));
            });
        }

        createToolbarButton({ name, action }) {
            // Create button
            const button = document.createElement('button');
            button.dataset.action = name;
            // Make active if needed
            if (action.isActive()) {
                button.classList.add('active');
            }
            if (action.isDropdown()) {
                button.classList.add('dropdown');
            }

            // Disable if needed
            button.disabled = !action.isEnabled();

            // Create icon
            const icon = document.createElement('i');
            icon.classList.add(`icon-${action.icon()}`);
            button.appendChild(icon);

            // Add status if needed
            if (action.status()) {
                const status = document.createElement('span');
                status.classList.add('status');
                status.innerText = action.status();
                button.appendChild(status);
            }

            // Bind button
            action.bindButton(button);

            if (action.dropdownItems().length) {
                const container = document.createElement('div');
                container.classList.add('dropdown');

                const dropdown = document.createElement('ul');
                dropdown.classList.add('dropdown-menu');
                dropdown.setAttribute('role', 'menu');

                this.generateDropdownItems(action, dropdown);

                container.appendChild(button);
                container.appendChild(dropdown);

                // Add tooltip if needed
                if (action.tooltip()) {
                    container.classList.add('has-tooltip');
                    container.setAttribute('data-toggle', 'tooltip');
                    container.setAttribute('title', action.tooltip());

                    if (action.shortcutKey()) {
                        container.setAttribute('title', `${container.getAttribute('title')} (${this.getModifierKey()}${action.shortcutKey()})`);
                    }
                }

                button.setAttribute('data-toggle', 'dropdown');

                return container;
            }

            // Add tooltip if needed
            if (action.tooltip()) {
                button.classList.add('has-tooltip');
                button.setAttribute('data-toggle', 'tooltip');
                button.setAttribute('title', action.tooltip());

                if (action.shortcutKey()) {
                    button.setAttribute('title', `${button.getAttribute('title')} (${this.getModifierKey()}${action.shortcutKey()})`);
                }
            }

            // Add click action
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopImmediatePropagation();

                action.action();
                this.updateToolbarButtonStates();
            });

            return button;
        }

        /**
         * Generates dropdown items for a given action.
         *
         * @param {EditorAction} action
         * @param {HTMLElement} dropdown
         * @returns {void}
         */
        generateDropdownItems(action, dropdown) {
            if (!action.dropdownItems().length) {
                return;
            }

            // Generate list items
            action.dropdownItems().forEach((item) => {
                const listItem = document.createElement('li');
                listItem.setAttribute('role', 'presentation');

                if (item.active) {
                    listItem.classList.add('active');
                }
                if (!item.enabled) {
                    listItem.classList.add('disabled');
                }

                const listItemLink = document.createElement('a');
                listItemLink.setAttribute('role', 'menuitem');
                listItemLink.setAttribute('href', '#');
                if (item.icon) {
                    listItemLink.classList.add(`wn-icon-${item.icon}`);
                }
                if (item.label) {
                    listItemLink.innerText = item.label;
                }

                if (item.enabled) {
                    listItemLink.addEventListener('click', (event) => {
                        event.preventDefault();
                        if (item.isEnabled()) {
                            item.action();
                        }
                        this.updateToolbarButtonStates();
                    });
                }

                listItem.appendChild(listItemLink);
                dropdown.appendChild(listItem);
            });
        }

        /**
         * Updates the toolbar button states and regenerates dropdown menus.
         *
         * This action is less computationally expensive than creating the entire toolbar again and
         * is suitable for running when the selection or editor state changes.
         */
        updateToolbarButtonStates() {
            this.toolbar.querySelectorAll('button[data-action]').forEach((button) => {
                const action = this.findAction(button.dataset.action);
                if (!action) {
                    return;
                }

                // Update icon
                button.querySelector('i').className = `icon-${action.icon()}`;

                // Add status if needed
                if (action.status()) {
                    let status = button.querySelector('.status');

                    if (!status) {
                        status = document.createElement('span');
                        status.classList.add('status');
                        button.appendChild(status);
                    }

                    status.innerText = action.status();
                } else if (button.querySelector('.status')) {
                    button.querySelector('.status').remove();
                }

                // Regenerate dropdown navigation
                if (action.dropdownItems().length) {
                    let container = button.parentElement;
                    if (!container.classList.contains('dropdown')) {
                        container = document.createElement('div');
                        container.classList.add('dropdown');
                    }

                    if (container.querySelector('ul')) {
                        container.querySelector('ul').remove();
                    }

                    const dropdown = document.createElement('ul');
                    dropdown.classList.add('dropdown-menu');
                    dropdown.setAttribute('role', 'menu');

                    this.generateDropdownItems(action, dropdown);

                    container.appendChild(button);
                    container.appendChild(dropdown);

                    // Add tooltip if needed
                    if (action.tooltip() && container.classList.contains('has-tooltip')) {
                        container.setAttribute('title', action.tooltip());

                        if (action.shortcutKey()) {
                            container.setAttribute('title', `${container.getAttribute('title')} (${this.getModifierKey()}${action.shortcutKey()})`);
                        }
                    }

                    button.setAttribute('data-toggle', 'dropdown');
                } else if (action.tooltip() && button.classList.contains('has-tooltip')) {
                    button.setAttribute('title', action.tooltip());

                    if (action.shortcutKey()) {
                        button.setAttribute('title', `${button.getAttribute('title')} (${this.getModifierKey()}${action.shortcutKey()})`);
                    }
                }

                // Chane active and enabled states
                button.disabled = !action.isEnabled();
                if (action.isActive()) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }

        /**
         * Determines the applicable modifier key depending on the OS in use.
         *
         * Borrowed from https://developer.mozilla.org/en-US/docs/Web/API/Navigator/platform.
         *
         * @returns {string}
         */
        getModifierKey() {
            let modifierKeyPrefix = '^'; // control key
            if (navigator.platform.indexOf('Mac') === 0 || navigator.platform === 'iPhone') {
                modifierKeyPrefix = '⌘'; // command key
            }

            return modifierKeyPrefix;
        }

        /**
         * Registers key bindings with the code editor.
         */
        registerKeyBindings() {
            if (!this.actions.length) {
                return;
            }

            this.actions.forEach((item) => {
                if (!item.action.shortcutKey()) {
                    return;
                }

                this.editor.addKeyBinding({
                    key: item.action.shortcutKey(),
                    ctrl: true,
                }, () => {
                    if (item.action.isEnabled()) {
                        item.action.action();
                    }

                    this.updateToolbarButtonStates();
                });
            });
        }
    }

    Snowboard.addPlugin('backend.formwidgets.markdowneditor', MarkdownEditor);
    Snowboard['backend.ui.widgetHandler']().register('markdowneditor', 'backend.formwidgets.markdowneditor');
})(window.Snowboard);
