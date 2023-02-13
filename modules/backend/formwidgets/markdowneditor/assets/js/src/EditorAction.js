/**
 * Base editor action class.
 *
 * Defines the core functionality of all editor actions, including defining button attributes and states.
 *
 * @author Ben Thomson <git@alfreido.com>
 * @copyright 2023 Winter CMS.
 */
export default class EditorAction {
    /**
     * Constructor.
     *
     * @param {MarkdownEditor} editor
     * @param {CodeEditor} monaco
     */
    constructor(editor, monaco) {
        this.editor = editor;
        this.monaco = monaco;
        this.button = null;
    }

    /**
     * Binds a toolbar button element to this action.
     *
     * @param {HTMLElement} button
     */
    bindButton(button) {
        this.button = button;
    }

    /**
     * Defines the icon to use for the toolbar button.
     *
     * @returns {string}
     */
    icon() {
        return 'location-crosshairs';
    }

    /**
     * Defines a tooltip for the toolbar button.
     *
     * If `null` or an empty string is returned, no tooltip will be displayed.
     *
     * @returns {string|null}
     */
    tooltip() {
        return null;
    }

    /**
     * Defines the shortcut key that this action is bound to.
     *
     * Note that this assumes the use of the "Control" key on Windows and Linux, and the "Command"
     * key on Mac. You only need to define the actual key - for example, "B" for bold.
     *
     * This shortcut key will only be displayed if a tooltip is also defined.
     *
     * If `null` or an empty string is returned, no shortcut key will be displayed.
     *
     * @returns {string|null}
     */
    shortcutKey() {
        return null;
    }

    /**
     * Defines a status bubble to show next to the toolbar button icon.
     *
     * Can be used to show context for a given toolbar action.
     *
     * If `null` or an empty string is returned, no status will be displayed.
     *
     * @returns {string|null}
     */
    status() {
        return null;
    }

    /**
     * Defines if this action's button is a dropdown.
     *
     * This cosmetically shows that the button is a dropdown. It is up to the action to handle
     * the dropdown functionality through the `dropdownItems()` or `action()` methods.
     *
     * @returns {boolean}
     */
    isDropdown() {
        return false;
    }

    /**
     * Defines the list of dropdown items.
     *
     * This creates simple dropdown lists for toolbar buttons. Each dropdown item should be an
     * object containing the following:
     *
     *  - `label` - The label to show for the dropdown item. (required if no icon)
     *  - `icon` - The icon to show for the dropdown item. (required if no label)
     *  - `active` - Defines if this dropdown item is active.
     *  - `enabled` - Defines if this dropdown item is available.
     *  - `action` - The action to perform when the dropdown item is clicked. (required)
     *
     * @returns {Object[]}
     */
    dropdownItems() {
        return [];
    }

    /**
     * Defines if the action's button is active.
     *
     * This can show an active state for the button in certain circumstances - for instance, if
     * the current selected text is using this action.
     *
     * @returns {boolean}
     */
    isActive() {
        return false;
    }

    /**
     * Defines if the action's button is enabled and used.
     *
     * If this is `false`, the button will be disabled and greyed out.
     *
     * @returns {boolean}
     */
    isEnabled() {
        return false;
    }

    /**
     * Defines the action to perform when the button is clicked.
     */
    action() {
    }
}
