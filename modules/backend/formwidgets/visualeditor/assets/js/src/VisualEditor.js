import { Editor } from '@tiptap/core';
import StarterKitExtension from '@tiptap/starter-kit';
import TextAlignExtension from '@tiptap/extension-text-align';
import Bold from './actions/Bold';
import Italic from './actions/Italic';
import NodeType from './actions/NodeType';
import TextAlign from './actions/TextAlign';
import BulletList from './actions/BulletList';
import OrderedList from './actions/OrderedList';

((Snowboard) => {
    /**
     * Visual editor widget.
     *
     * @author Ben Thomson
     * @copyright Winter CMS
     */
    class VisualEditor extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.valueBag = element.querySelector('textarea');
            this.actions = [];
            this.toolbar = element.querySelector('.visualeditor-toolbar');
            this.editor = null;

            this.createEditor();
            this.registerEvents();
            this.registerActions();
            this.createToolbar();
        }

        createEditor() {
            this.editor = new Editor({
                element: this.element,
                extensions: [
                    StarterKitExtension,
                    TextAlignExtension.configure({
                        types: ['heading', 'paragraph'],
                    }),
                ],
                content: this.valueBag.value,
            });
        }

        registerEvents() {
            this.editor.on('selectionUpdate', () => {
                this.updateToolbarButtonStates();
            });
            this.editor.on('transaction', () => {
                this.updateToolbarButtonStates();
            });

            this.editor.on('update', () => {
                this.updateValueBag();
            });
        }

        registerActions() {
            this.actions.push({
                name: 'nodeType',
                action: new NodeType(this.editor),
            });
            this.actions.push({
                name: 'bold',
                action: new Bold(this.editor),
            });
            this.actions.push({
                name: 'italic',
                action: new Italic(this.editor),
            });
            this.actions.push({
                name: 'alignment',
                action: new TextAlign(this.editor),
            });
            this.actions.push({
                name: 'bulletList',
                action: new BulletList(this.editor),
            });
            this.actions.push({
                name: 'orderedList',
                action: new OrderedList(this.editor),
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
                        item.action();
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
         * Updates the value within the value bag (textarea).
         */
        updateValueBag() {
            this.valueBag.value = this.editor.getHTML();
        }
    }

    Snowboard.addPlugin('backend.formwidgets.visualeditor', VisualEditor);
    Snowboard['backend.ui.widgetHandler']().register('visualeditor', 'backend.formwidgets.visualeditor');
})(window.Snowboard);
