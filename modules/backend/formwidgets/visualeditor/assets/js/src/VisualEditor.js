import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Bold from './actions/Bold';
import Italic from './actions/Italic';

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
                    StarterKit,
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
        }

        registerActions() {
            this.actions.push({
                name: 'bold',
                action: new Bold(this.editor),
            });
            this.actions.push({
                name: 'italic',
                action: new Italic(this.editor),
            });
        }

        findAction(name) {
            const action = this.actions.find((item) => item.name === name);
            return (!action) ? null : action.action;
        }

        createToolbar() {
            this.actions.forEach((action) => {
                this.toolbar.appendChild(this.createToolbarButton(action));
            });
        }

        createToolbarButton({ name, action }) {
            const button = document.createElement('button');
            button.dataset.action = name;
            if (action.isActive()) {
                button.classList.add('active');
            }
            button.disabled = !action.isEnabled();

            const icon = document.createElement('i');
            icon.classList.add(`icon-${action.icon()}`);
            button.appendChild(icon);

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopImmediatePropagation();

                action.action();
            });

            return button;
        }

        updateToolbarButtonStates() {
            this.toolbar.querySelectorAll('button[data-action]').forEach((button) => {
                const action = this.findAction(button.dataset.action);
                if (!action) {
                    return;
                }

                button.disabled = !action.isEnabled();
                if (action.isActive()) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }
    }

    Snowboard.addPlugin('backend.formwidgets.visualeditor', VisualEditor);
    Snowboard['backend.ui.widgetHandler']().register('visualeditor', 'backend.formwidgets.visualeditor');
})(window.Snowboard);
