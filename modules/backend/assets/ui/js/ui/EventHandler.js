/**
 * Widget event handler.
 *
 * Extends a widget with event handling functionality, allowing for the quick definition of events
 * and listening for events on a specific instance of a widget.
 *
 * This is a complement to Snowboard's global events - these events will still fire in order to
 * allow external code to listen and handle events. Local events can cancel the global event (and
 * further local events) by returning `false` from the callback.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class EventHandler extends Snowboard.PluginBase {
    /**
     * Constructor.
     *
     * @param {PluginBase} instance
     * @param {String} eventPrefix
     */
    construct(instance, eventPrefix) {
        if (instance instanceof Snowboard.PluginBase === false) {
            throw new Error('Event handling can only be applied to Snowboard classes.');
        }
        if (!eventPrefix) {
            throw new Error('Event prefix is required.');
        }
        this.instance = instance;
        this.eventPrefix = eventPrefix;
        this.events = [];
    }

    /**
     * Registers a listener for a widget's event.
     *
     * @param {String} event
     * @param {Function} callback
     */
    on(event, callback) {
        this.events.push({
            event,
            callback,
        });
    }

    /**
     * Deregisters a listener for a widget's event.
     *
     * @param {String} event
     * @param {Function} callback
     */
    off(event, callback) {
        this.events = this.events.filter((registeredEvent) => registeredEvent.event !== event || registeredEvent.callback !== callback);
    }

    /**
     * Registers a listener for a widget's event that will only fire once.
     *
     * @param {String} event
     * @param {Function} callback
     */
    once(event, callback) {
        const length = this.events.push({
            event,
            callback: (...parameters) => {
                callback(...parameters);
                this.events.splice(length - 1, 1);
            },
        });
    }

    /**
     * Fires an event on the widget.
     *
     * Local events are fired first, then a global event is fired afterwards.
     *
     * @param {String} eventName
     * @param  {...any} parameters
     */
    fire(eventName, ...parameters) {
        // Fire local events first
        const events = this.events.filter((registeredEvent) => registeredEvent.event === eventName);
        let cancelled = false;
        events.forEach((event) => {
            if (cancelled) {
                return;
            }
            if (event.callback(...parameters) === false) {
                cancelled = true;
            }
        });

        if (!cancelled) {
            this.snowboard.globalEvent(`${this.eventPrefix}.${eventName}`, ...parameters);
        }
    }

    /**
     * Fires a promise event on the widget.
     *
     * Local events are fired first, then a global event is fired afterwards.
     *
     * @param {String} eventName
     * @param  {...any} parameters
     */
    firePromise(eventName, ...parameters) {
        const events = this.events.filter((registeredEvent) => registeredEvent.event === eventName);
        const promises = events.filter((event) => event !== null, events.map((event) => event.callback(...parameters)));

        Promise.all(promises).then(
            () => {
                this.snowboard.globalPromiseEvent(`${this.eventPrefix}.${eventName}`, ...parameters);
            },
        );
    }
}
