/**
 * Backend widget handler.
 *
 * Handles the creation and disposal of widgets in the Backend. Widgets should include this as
 * a dependency in order to be loaded and initialised after the handler, in order to correctly
 * register.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class WidgetHandler extends Snowboard.Singleton {
    /**
     * Constructor.
     */
    construct() {
        this.registeredWidgets = [];
        this.elements = [];
        this.events = {
            mutate: (mutations) => this.onMutation(mutations),
        };
        this.observer = null;
    }

    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'onReady',
            render: 'onRender',
            ajaxUpdate: 'onAjaxUpdate',
        };
    }

    /**
     * Registers a widget as a given data control.
     *
     * Registering a widget will allow any element that contains a "data-control" attribute matching
     * the control name to be initialized with the given widget.
     *
     * You may optionally provide a callback that will be fired when an instance of the widget is
     * initialized - the callback will be provided the element and the widget instance as parameters.
     *
     * @param {String} control
     * @param {Snowboard.PluginBase} widget
     * @param {Function} callback
     */
    register(control, widget, callback) {
        this.registeredWidgets.push({
            control,
            widget,
            callback,
        });
    }

    /**
     * Unregisters a data control.
     *
     * @param {String} control
     */
    unregister(control) {
        this.registeredWidgets = this.registeredWidgets.filter((widget) => widget.control !== control);
    }

    /**
     * Ready handler.
     *
     * Initializes widgets within the entire document.
     */
    onReady() {
        this.initializeWidgets(document.body);

        // Register a DOM observer and watch for any removed nodes
        if (!this.observer) {
            this.observer = new MutationObserver(this.events.mutate);
            this.observer.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }
    }

    /**
     * Render handler.
     *
     * Initializes widgets within the entire document.
     */
    onRender() {
        this.initializeWidgets(document.body);
    }

    /**
     * AJAX update handler.
     *
     * Initializes widgets inside an update element from an AJAX response.
     *
     * @param {HTMLElement} element
     */
    onAjaxUpdate(element) {
        this.initializeWidgets(element);
    }

    /**
     * Initializes all widgets within an element.
     *
     * If an element contains a "data-control" attribute matching a registered widget, the widget
     * is initialized and attached to the element as a "widget" property.
     *
     * Only one widget may be initialized to a particular element.
     *
     * @param {HTMLElement} element
     */
    initializeWidgets(element) {
        this.registeredWidgets.forEach((widget) => {
            const instances = element.querySelectorAll(`[data-control="${widget.control}"]:not([data-widget-initialized])`);

            if (instances.length) {
                instances.forEach((instance) => {
                    // Prevent double-widget initialization
                    if (instance.dataset.widgetInitialized) {
                        return;
                    }

                    const widgetInstance = this.snowboard[widget.widget](instance);
                    this.elements.push({
                        element: instance,
                        instance: widgetInstance,
                    });
                    instance.dataset.widgetInitialized = true;
                    this.snowboard.globalEvent('backend.widget.initialized', instance, widgetInstance);

                    if (typeof widget.callback === 'function') {
                        widget.callback(widgetInstance, instance);
                    }
                });
            }
        });
    }

    /**
     * Returns a widget that is attached to the given element, if any.
     *
     * @param {HTMLElement} element
     * @returns {Snowboard.PluginBase|null}
     */
    getWidget(element) {
        const found = this.elements.find((widget) => widget.element === element);

        if (found) {
            return found.instance;
        }

        return null;
    }

    /**
     * Callback for mutation events.
     *
     * We're only tracking removed nodes, to ensure that those widgets are disposed of.
     *
     * @param {MutationRecord[]} mutations
     */
    onMutation(mutations) {
        const removedNodes = mutations.filter((mutation) => mutation.removedNodes.length).map((mutation) => Array.from(mutation.removedNodes)).flat();
        if (!removedNodes.length) {
            return;
        }

        removedNodes.forEach((node) => {
            const widgets = this.elements.filter((widget) => node.contains(widget.element));
            if (widgets.length) {
                widgets.forEach((widget) => {
                    widget.instance.destruct();
                    this.elements = this.elements.filter((element) => element !== widget);
                });
            }
        });
    }
}
