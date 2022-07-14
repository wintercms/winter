/**
 * UI Handler.
 *
 * The UI handler is a single instance that handles connecting elements to their corresponding JS
 * functionalities and property disposing and destructing plugins as required.
 *
 * It also houses any global functionality that might be used throughout the Backend UI.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class UIHandler extends Snowboard.Singleton {
    /**
     * Dependencies.
     *
     * @returns {String[]}
     */
    dependencies() {
        return [
            'disposableObserver',
            'disposable',
            'backend.controls.balloonSelector',
            'backend.controls.dateTime',
        ];
    }

    /**
     * Event listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxUpdate: 'ajaxUpdate',
        };
    }

    /**
     * Element matchers.
     *
     * Defines the elements that should be handled by each plugin. The plugin name should be the
     * key, and the CSS selector(s) should be the value.
     *
     * @returns {Object}
     */
    elementMatchers() {
        return {
            'backend.controls.balloonSelector': 'div[data-control="balloon-selector"]',
            'backend.controls.dateTime': 'time[data-datetime-control]',
        };
    }

    /**
     * Ready event handler.
     *
     * This will enable all UI elements in the entire document.
     */
    ready() {
        this.initializePlugins(document.body);
    }

    /**
     * AJAX update handler.
     *
     * This will enable all UI elements in each updated element from the AJAX response.
     *
     * @param {HTMLElement} updatedElement
     */
    ajaxUpdate(updatedElement) {
        this.initializePlugins(updatedElement);
    }

    /**
     * Initializes plugins within a given root element.
     *
     * Each plugin will receive one or more elements that match the element matches provided in the
     * `elementMatchers()` method.
     *
     * @param {HTMLElement} rootElement
     */
    initializePlugins(rootElement) {
        Object.entries(this.elementMatchers()).forEach(([plugin, selector]) => {
            rootElement.querySelectorAll(selector).forEach((element) => {
                this.snowboard[plugin](element);
            });
        });
    }
}
