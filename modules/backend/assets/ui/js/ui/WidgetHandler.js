/**
 * Backend widget handler.
 *
 * Handles the creation and disposal of widgets in the Backend. Widgets should include this as
 * a dependency in order to be loaded and initialised after the handler, in order to correctly
 * register.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class WidgetHandler extends Snowboard.Singleton {
    construct() {
        this.registeredWidgets = [];
    }

    listens() {
        return {
            ready: 'onReady',
            ajaxUpdate: 'onAjaxUpdate',
        };
    }

    register(control, widget, callback) {
        this.registeredWidgets.push({
            control,
            widget,
            callback,
        });
    }

    unregister(control) {
        this.registeredWidgets = this.registeredWidgets.filter((widget) => widget.control !== control);
    }

    onReady() {
        this.initialiseWidgets(document.body);
    }

    onAjaxUpdate(element) {
        this.initialiseWidgets(element);
    }

    initialiseWidgets(element) {
        this.registeredWidgets.forEach((widget) => {
            const instances = element.querySelectorAll(`[data-control="${widget.control}"]`);

            if (instances.length) {
                instances.forEach((instance) => {
                    const widgetInstance = this.snowboard[widget.widget](instance);
                    if (typeof widget.callback === 'function') {
                        widget.callback(widgetInstance);
                    }
                });
            }
        });
    }
}
