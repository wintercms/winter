import { delegate } from 'jquery-events-to-dom-events';

/**
 * Backend trigger handler.
 *
 * Registers the usage of the new Trigger functionality, whilst mapping the old jQuery-style events
 * to the new functionality.
 *
 * @copyright 2024 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class TriggerHandler extends Snowboard.Singleton {
    construct() {
        this.changedElements = new Set();
    }

    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'onReady',
            render: 'setUpTriggers',
            'trigger.action': 'onTriggerAction',
        };
    }

    onReady() {
        delegate('oc.triggerOn.update');

        /**
         * A number of widgets trigger a change event on a hidden element when they are updated,
         * however, this change event is a jQuery "change" event, not the native DOM "change" event.
         *
         * The following intercepts the jQuery event and dispatches a native event as well.
         */
        delegate('change');
        document.addEventListener('$change', (event) => {
            if (event.throughTrigger) {
                return;
            }

            const newEvent = new InputEvent('change');
            newEvent.throughTrigger = true;
            event.target.dispatchEvent(newEvent);
        });

        this.setUpTriggers();
    }

    setUpTriggers() {
        // Scan for triggers
        Array
            .from(document.querySelectorAll('*'))
            .filter((element) => [...element.attributes].filter(
                ({ name }) => name.startsWith("data-trigger-")).length > 0
            ).forEach((element) => {
                const trigger = snowboard.trigger(element);
                element.addEventListener('$oc.triggerOn.update', () => {
                    trigger.runEvents(element);
                });
            });
    }

    onTriggerAction(element, trigger, action, conditionMet) {
        if (action.name === 'show' || action.name === 'hide') {
            this.actionShow(
                (action.parameters[0])
                    ? Array.from(element.querySelectorAll(action.parameters[0]))
                    : [element],
                (action.name === 'show') ? conditionMet : !conditionMet,
            );
        }
    }

    actionShow(elements, show) {
        elements.forEach((element) => {
            if (show && element.classList.contains('hide')) {
                element.classList.remove('hide');
            } else if (!show && !element.classList.contains('hide')) {
                element.classList.add('hide');
            }
        });
    }
}
