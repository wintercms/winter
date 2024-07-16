import PluginBase from '../abstracts/PluginBase';

/**
 * @typedef {Object} TriggerEntity
 * @property {string} trigger The selector for the trigger target element(s).
 * @property {string} condition The condition that must be met for the trigger to fire.
 * @property {string} action The action to perform when the trigger fires.
 * @property {string|undefined} parent The parent element with which to limit the trigger scope.
 * @property {string|number} priority The priority of the trigger event.
 * @property {HTMLElement[]} elements The target elements that this trigger applies to.
 * @property {Function[]} conditionCallbacks The condition callbacks for this trigger.
 * @property {Map<HTMLElement, Set<string>>} elementEvents The events registered on the target elements.
 */
/**
 * @typedef {Object} TriggerElement
 * @property {HTMLElement} element The target element.
 * @property {string} eventName The trigger event name.
 * @property {int} priority The trigger event priority.
 * @property {Function} event The trigger event function.
 */

/**
 * Trigger handler for HTML elements.
 *
 * This is a re-imagining of the Input.Trigger functionality in the original Winter CMS framework,
 * initialised through the `data-trigger` attributes.
 *
 * In addition to remaining backwards-compatible with the original Input.Trigger functionality, this
 * handler adds additional conditions and configuration for more flexible trigger usage.
 *
 * @see https://wintercms.com/docs/v1.2/ui/script/input-trigger
 *
 * @copyright 2024 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Trigger extends PluginBase {
    /**
     * Constructor.
     *
     * @param {HTMLElement} element
     */
    construct(element) {
        /**
         * The element this instance is attached to.
         */
        this.element = element;

        /**
         * @type {Map<string, Map<TriggerEntity>>} The triggers for this element.
         */
        this.triggers = new Map();

        /**
         * @type {Map<Element, Set<TriggerElement>>} A map of elements that trigger events.
         */
        this.events = new Map();

        /**
         * @type {Map<Element, Map<string, Function>>} A map of elements and their event connectors.
         */
        this.connectors = new Map();

        this.parseTriggers();

        if (this.triggers.size > 0) {
            this.resetEvents();
            this.createTriggerEvents();
            this.runEvents();

            this.snowboard.globalEvent('triggers.ready', this.element);
        }
    }

    /**
     * Destructor.
     */
    destruct() {
        this.resetEvents();
        super.destruct();
    }

    /**
     * Parses the element's data attributes and determines applicable triggers.
     *
     * Trigger data attributes must be in the format `data-trigger-[name]-[parameter]` for multiple
     * triggers, or `data-trigger-[parameter]` for single triggers.
     *
     * Supported parameters are:
     *  - `condition` or `where`: The condition that must be met for the trigger to fire.
     *  - `action` or `do`: The action to perform when the trigger fires.
     *  - `closest-parent` or `parent`: The parent element with which to limit the trigger scope.
     *  - `priority`: The priority in which to consider the trigger.
     *
     * Internally, the trigger map uses the `trigger` parameter to store the trigger selector.
     */
    parseTriggers() {
        const { dataset } = this.element;
        this.triggers.clear();

        Object.keys(dataset).forEach((key) => {
            if (/-[A-Z]/.test(key)) {
                throw new Error(`Unable to convert camelCase to dash-style for data attribute: ${key}`);
            }

            const dashStyle = key.replace(/([A-Z])/g, (match) => `-${match.toLowerCase()}`);

            if (dashStyle !== 'trigger' && !dashStyle.startsWith('trigger-')) {
                return;
            }

            const triggerParts = /([a-z0-9\-.:_]+?)(?:(?:-)(closest-parent|condition|when|action|parent|priority|do))?$/i.exec(
                dashStyle.replace('trigger-', '').toLowerCase(),
            );

            let triggerName = null;
            let triggerType = null;

            if (
                ['trigger', 'condition', 'action', 'parent', 'when', 'closest'].indexOf(triggerParts[1]) !== -1
                && (triggerParts[1] !== 'closest' || (triggerParts[1] === 'closest' && triggerParts[2] === 'parent'))
            ) {
                // Support original trigger format
                triggerName = '__original';
                triggerType = (triggerParts[1] === 'closest') ? 'parent' : triggerParts[1];
            } else if (
                triggerParts[2] === undefined
                || ['closest-parent', 'condition', 'when', 'action', 'parent', 'priority', 'do'].indexOf(triggerParts[2]) !== -1
            ) {
                // Parse multi-trigger format
                [, triggerName] = triggerParts;
                switch (triggerParts[2]) {
                    case 'closest-parent':
                    case 'parent':
                        triggerType = 'parent';
                        break;
                    case 'condition':
                    case 'when':
                        triggerType = 'condition';
                        break;
                    case 'action':
                    case 'do':
                        triggerType = 'action';
                        break;
                    case 'priority':
                        triggerType = 'priority';
                        break;
                    default:
                        triggerType = 'trigger';
                        break;
                }
            }

            if (!this.triggers.has(triggerName)) {
                this.triggers.set(triggerName, new Map());
            }
            this.triggers.get(triggerName).set(triggerType, dataset[key]);

            // Remove trigger data attribute after parsing
            delete dataset[key];
        });

        // Validate triggers, and remove those that do not have at least a trigger selector, a
        // condition and an action, or are using invalid conditions or actions
        this.triggers.forEach((trigger, name) => {
            const elements = this.getSelectableElements(trigger);

            if (
                !trigger.has('trigger')
                || !trigger.has('condition')
                || !trigger.has('action')
                || elements.length === 0
                || !this.hasValidConditions(trigger)
                || !this.hasValidActions(trigger)
            ) {
                this.triggers.delete(name);
            } else {
                trigger.set('elements', elements);
                if (!trigger.has('priority')) {
                    trigger.set('priority', 100);
                }
            }
        });
    }

    /**
     * Parses a command given as either a condition or an action.
     *
     * Commands are formatted as: name:parameter1,parameter2,parameter3, although we also support
     * the old format of value[parameter1,parameter2,parameter3] for the `value` command only.
     *
     * If a parameter requires a comma within, the parameter should be wrapped in quotes.
     *
     * Multiple commands can be separated by a pipe character `|`.
     *
     * @param {string} command
     * @param {string} allowMultiple
     * @returns {{name: string, parameters: string[]}[]}
     */
    parseCommand(command, allowMultiple = true) {
        // Support old-format value command (value[foo,bar])
        if (command.startsWith('value') && command.includes('[')) {
            const match = command.match(/[^[\]]+(?=])/g);
            const values = [];

            // Split values with commas
            match.forEach((value) => {
                if (!value.includes(',')) {
                    values.push(value.replace(/^['"]|['"]$/g, '').trim());
                    return;
                }

                const splitValues = value.replace(/("[^"]*")|('[^']*')/g, (quoted) => quoted.replace(/,/g, '|||'))
                    .split(',')
                    .map((splitValue) => splitValue.replace(/\|\|\|/g, ',').replace(/^['"]|['"]$/g, '').trim());

                values.push(...splitValues);
            });

            return [{
                name: 'value',
                parameters: values,
            }];
        }

        // Handle multiple commands
        if (command.includes('|') && allowMultiple) {
            const splitCommands = command.replace(/("[^"]*")|('[^']*')/g, (quoted) => quoted.replace(/\|/g, '|||'))
                .split('|')
                .map((splitValue) => splitValue.replace(/\|\|\|/g, '|'));

            const commands = [];
            splitCommands.forEach((splitCommand) => {
                commands.push(...this.parseCommand(splitCommand, false));
            });

            return commands;
        }

        if (!command.includes(':')) {
            return [{
                name: command,
                parameters: [],
            }];
        }

        const [name, parameters] = command.split(':', 2);

        if (!parameters.includes(',')) {
            return [{
                name,
                parameters: [parameters],
            }];
        }

        const splitValues = parameters.replace(/("[^"]*")|('[^']*')/g, (quoted) => quoted.replace(/,/g, '|||'))
            .split(',')
            .map((splitValue) => splitValue.replace(/\|\|\|/g, ',').replace(/^['"]|['"]$/g, '').trim());

        return [{
            name,
            parameters: splitValues,
        }];
    }

    /**
     * Checks if any elements are accessible by the provided trigger.
     *
     * @param {Map<TriggerEntity>} trigger
     * @returns {HTMLElement[]}
     */
    getSelectableElements(trigger) {
        if (trigger.has('parent')) {
            return Array.from(this.element.closest(trigger.get('parent')).querySelectorAll(trigger.get('trigger')));
        }

        return Array.from(document.querySelectorAll(trigger.get('trigger')));
    }

    /**
     * Determines if the provided trigger condition(s) are valid.
     *
     * @param {TriggerEntity} trigger
     * @returns {boolean}
     */
    hasValidConditions(trigger) {
        return this.parseCommand(trigger.get('condition')).every((condition) => [
            'checked',
            'unchecked',
            'empty',
            'value',
            'oneof',
            'allof',
            'focus',
            'blur',
        ].includes(condition.name.toLowerCase()));
    }

    /**
     * Determines if the provided trigger action(s) are valid.
     *
     * @param {TriggerEntity} trigger
     * @returns {boolean}
     */
    hasValidActions(trigger) {
        return this.parseCommand(trigger.get('action')).every((action) => [
            'show',
            'hide',
            'enable',
            'disable',
            'empty',
            'value',
            'check',
            'uncheck',
            'class',
            'attr',
            'style',
        ].includes(action.name.toLowerCase()));
    }

    /**
     * Create trigger events on trigger and target elements.
     */
    createTriggerEvents() {
        this.triggers.forEach((trigger) => {
            // Collect conditions and check them as a group.
            trigger.set('conditionCallbacks', []);
            trigger.set('elementEvents', new Map());

            this.parseCommand(trigger.get('condition')).forEach((condition) => {
                switch (condition.name.toLowerCase()) {
                    case 'value':
                    case 'oneof':
                        trigger.get('conditionCallbacks').push(
                            this.createValueCondition(trigger, false, ...condition.parameters),
                        );
                        break;
                    case 'allof':
                        trigger.get('conditionCallbacks').push(
                            this.createValueCondition(trigger, true, ...condition.parameters),
                        );
                        break;
                    case 'empty':
                        trigger.get('conditionCallbacks').push(
                            this.createEmptyCondition(trigger),
                        );
                        break;
                    case 'checked':
                    case 'unchecked':
                        trigger.get('conditionCallbacks').push(
                            this.createCheckedCondition(trigger, (condition.name === 'checked'), ...condition.parameters),
                        );
                        break;
                    default:
                }
            });
        });

        this.registerEventListeners();
    }

    /**
     * Adds an event to an element.
     *
     * This registers the event in the `elementEvents` map for later usage and removal.
     *
     * @param {HTMLElement} element
     * @param {TriggerEntity} trigger
     * @param {string} eventName
     */
    addEvent(element, trigger, eventName) {
        if (!trigger.get('elementEvents').has(element)) {
            trigger.get('elementEvents').set(element, new Set());
        }
        if (!trigger.get('elementEvents').get(element).add(eventName)) {
            trigger.get('elementEvents').get(element).add(eventName);
        }
    }

    /**
     * Registers DOM event listeners for targeted elements of all triggers.
     *
     * Adds a connector to the element for the events, so that we may enable prioritisation and
     * control over the firing of the events, and then registers DOM event listeners for the
     * elements.
     */
    registerEventListeners() {
        const connectors = new Set();

        this.triggers.forEach((trigger) => {
            trigger.get('elementEvents').forEach((events, element) => {
                if (!this.events.has(element)) {
                    this.events.set(element, new Set());
                }

                events.forEach((eventName) => {
                    if (!connectors.has({ element, eventName })) {
                        connectors.add({ element, eventName });
                    }

                    const event = {
                        element,
                        eventName,
                        priority: Number(trigger.get('priority')),
                        event: () => {
                            this.executeActions(
                                trigger,
                                trigger.get('conditionCallbacks').every((condition) => condition()),
                            );
                        },
                    };

                    this.events.get(element).add(event);
                });
            });
        });

        connectors.forEach(({ element, eventName }) => {
            if (!this.connectors.has(element)) {
                this.connectors.set(element, new Map());
            }

            if (!this.connectors.get(element).has(eventName)) {
                this.connectors.get(element).set(eventName, () => {
                    const events = [];

                    this.events.get(element).forEach((elementEvent) => {
                        if (elementEvent.eventName === eventName) {
                            events.push(elementEvent);
                        }
                    });

                    events
                        .sort((a, b) => a.priority - b.priority)
                        .forEach((elementEvent) => {
                            elementEvent.event();
                        });
                });

                element.addEventListener(eventName, this.connectors.get(element).get(eventName));
            }
        });
    }

    /**
     * Creates a trigger that fires when the value of the target element(s) matches one of the
     * provided values.
     *
     * @param {TriggerEntity} trigger
     * @param  {...string} values
     */
    createValueCondition(trigger, all, ...values) {
        const supportedElements = new Set();

        trigger.get('elements').forEach((element) => {
            if (element.matches('input[type=button], input[type=file], input[type=image], input[type=reset], input[type=submit]')) {
                // Buttons and file inputs are unsupported
                return;
            }

            if (element.matches('input, select, textarea')) {
                supportedElements.add(element);
            }
        });

        supportedElements.forEach((element) => {
            if (element.matches('input[type=checkbox], input[type=radio]')) {
                this.addEvent(element, trigger, 'click');
            }

            this.addEvent(element, trigger, 'input');
        });

        return () => {
            const elementValues = new Set();

            supportedElements.forEach((element) => {
                if (element.matches('input[type=checkbox], input[type=radio]')) {
                    if (element.checked) {
                        elementValues.add(element.value);
                    }
                    return;
                }

                elementValues.add(element.value);
            });

            if (all) {
                return values.every((value) => elementValues.has(value));
            }

            return values.some((value) => elementValues.has(value));
        };
    }

    /**
     * Creates a trigger that fires when there is no value within the target element(s).
     *
     * @param {TriggerEntity} trigger
     */
    createEmptyCondition(trigger) {
        const supportedElements = new Set();

        trigger.get('elements').forEach((element) => {
            if (element.matches('input[type=button], input[type=image], input[type=reset], input[type=submit]')) {
                // Buttons and file inputs are unsupported
                return;
            }

            if (element.matches('input, select, textarea')) {
                supportedElements.add(element);
            }
        });

        supportedElements.forEach((element) => {
            if (element.matches('input[type=checkbox], input[type=radio]')) {
                this.addEvent(element, trigger, 'click');
                return;
            }

            this.addEvent(element, trigger, 'input');
        });

        return () => {
            const elementValues = new Set();

            supportedElements.forEach((element) => {
                if (element.matches('input[type=checkbox], input[type=radio]')) {
                    if (element.checked) {
                        elementValues.add(element);
                    }
                    return;
                }

                if (element.value.trim() !== '') {
                    elementValues.add(element);
                }
            });

            return elementValues.size === 0;
        };
    }

    /**
     * Creates a trigger that fires when a target element(s) is checked/unchecked.
     *
     * @param {TriggerEntity} trigger
     * @param {boolean} checked If the element should be checked or unchecked.
     * @param {string|number|undefined} atLeast The minimum number of elements that must be checked.
     *  Defaults to 1 if undefined. If specified as `all`, all elements must be checked.
     */
    createCheckedCondition(trigger, checked, atLeast = undefined, atMost = undefined) {
        const supportedElements = new Set();

        trigger.get('elements').forEach((element) => {
            // Only supports checkboxes and radio buttons
            if (element.matches('input[type=radio], input[type=checkbox]')) {
                supportedElements.add(element);
            }
        });

        supportedElements.forEach((element) => {
            this.addEvent(element, trigger, 'click');
        });

        return () => {
            const elementValues = new Set();

            supportedElements.forEach((element) => {
                if (checked === element.checked) {
                    elementValues.add(element);
                }
            });

            if (atLeast === 'all') {
                return elementValues.size === supportedElements.size;
            }

            const atLeastCount = (atLeast !== undefined && Math.floor(atLeast) > 0)
                ? Math.floor(atLeast)
                : 1;
            const atMostCount = (atLeast !== undefined && Math.floor(atMost) > 1)
                ? Math.floor(atMost)
                : supportedElements.size;

            return elementValues.size >= atLeastCount && elementValues.size <= atMostCount;
        };
    }

    runEvents() {
        this.connectors.forEach((elementConnectors) => {
            elementConnectors.forEach((connector) => {
                connector();
            });
        });
    }

    resetEvents() {
        this.connectors.forEach((elementConnectors, element) => {
            elementConnectors.forEach((connector, event) => {
                element.removeEventListener(event, connector);
            });
        });

        this.connectors.clear();
        this.events.clear();
    }

    executeActions(trigger, conditionMet) {
        this.parseCommand(trigger.get('action')).forEach((action) => {
            switch (action.name) {
                case 'show':
                case 'hide':
                    this.actionShow(trigger, (action.name === 'show') ? conditionMet : !conditionMet);
                    break;
                default:
            }
        });
    }

    actionShow(trigger, show) {
        if (show && getComputedStyle(this.element).display === 'none') {
            this.element.classList.remove('hide');

            if (!this.element.dataset.originalDisplay) {
                this.element.style.display = 'block';
            } else {
                this.element.style.display = this.element.dataset.originalDisplay;
            }

            delete this.element.dataset.originalDisplay;

            this.afterAction(trigger);
        } else if (!show && getComputedStyle(this.element).display !== 'none') {
            this.element.classList.add('hide');

            this.element.dataset.originalDisplay = getComputedStyle(this.element).display;
            this.element.style.display = 'none';

            this.afterAction(trigger);
        }
    }

    afterAction(trigger) {
        this.snowboard.debug('Trigger fired', this.element, trigger);
        this.snowboard.globalEvent('trigger.fired', this.element, trigger);
    }
}
