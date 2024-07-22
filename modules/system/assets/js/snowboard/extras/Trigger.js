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
         * @type {Map<string, TriggerEntity>} The triggers for this element.
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

            this.snowboard.globalEvent('triggers.ready', this.element, this.triggers);
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

            const triggerParts = /([a-z0-9\-.:_]+?)(?:(?:-)(closest-parent|condition|when|action|parent|priority|do))?(?:(?<=(?:action|do)(\.oneway)?)(\.oneway))?$/i.exec(
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
     * @returns {{name: string, parameters: string[], oneWay: boolean}[]}
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

                const splitValues = value.replace(/('.*?(?<!\\)')|(".*?(?<!\\)")/g, (quoted) => quoted.replace(/,/g, '|||'))
                    .split(',')
                    .map((splitValue) => splitValue.replace(/\|\|\|/g, ',').replace(/^['"]|['"]$/g, '').trim());

                values.push(...splitValues);
            });

            return [{
                name: 'value',
                parameters: values,
                oneWay: false,
            }];
        }

        // Handle multiple commands
        if (command.includes('|') && allowMultiple) {
            const splitCommands = command.replace(/('.*?(?<!\\)')|(".*?(?<!\\)")/g, (quoted) => quoted.replace(/\|/g, '|||'))
                .split('|')
                .map((splitValue) => splitValue.replace(/\|\|\|/g, '|'));

            const commands = [];
            splitCommands.forEach((splitCommand) => {
                commands.push(...this.parseCommand(splitCommand, false));
            });

            return commands;
        }

        if (!command.includes(':')) {
            if (
                command.includes('.oneway')
                && (command.startsWith('do') || command.startsWith('action'))
            ) {
                return [{
                    name: command.replace('.oneway', ''),
                    parameters: [],
                    oneWay: true,
                }];
            }

            return [{
                name: command,
                parameters: [],
                oneWay: false,
            }];
        }

        let [name] = command.split(':', 2);
        const [, parameters] = command.split(':', 2);
        let oneWay = false;

        if (
            name.includes('.oneway')
            && (name.startsWith('do') || name.startsWith('action'))
        ) {
            name = command.replace('.oneway', '');
            oneWay = true;
        }

        if (!parameters.includes(',')) {
            return [{
                name,
                parameters: [parameters],
                oneWay,
            }];
        }

        const splitValues = parameters.replace(/('.*?(?<!\\)')|(".*?(?<!\\)")/g, (quoted) => quoted.replace(/,/g, '|||'))
            .split(',')
            .map((splitValue) => splitValue.replace(/\|\|\|/g, ',').replace(/^['"]|['"]$/g, '').replace(/\\(['"])/, '$1').trim());

        return [{
            name,
            parameters: splitValues,
            oneWay,
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
            if (!this.element.closest(trigger.get('parent'))) {
                return [];
            }
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
            'within',
            'notWithin',
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
            'valueOf',
            'check',
            'uncheck',
            'class',
            'classOf',
            'attr',
            'attrOf',
            'style',
            'styleOf',
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
                    case 'focus':
                        trigger.get('conditionCallbacks').push(
                            this.createFocusedCondition(trigger),
                        );
                        break;
                    case 'within':
                    case 'notwithin':
                        trigger.get('conditionCallbacks').push(
                            this.createWithinCondition(trigger, (condition.name === 'within'), ...condition.parameters),
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
                this.addEvent(element, trigger, 'change');
                return;
            }

            if (element.matches('input[type=hidden]')) {
                this.addEvent(element, trigger, 'change');
                return;
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
                this.addEvent(element, trigger, 'change');
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
            const atMostCount = (atLeast !== undefined && Math.floor(atMost) > 0)
                ? Math.floor(atMost)
                : supportedElements.size;

            return elementValues.size >= atLeastCount && elementValues.size <= atMostCount;
        };
    }

    /**
     * Creates a trigger that fires when a target element(s) is focused or blurred.
     *
     * @param {TriggerEntity} trigger
     */
    createFocusedCondition(trigger) {
        const supportedElements = new Set();

        trigger.get('elements').forEach((element) => {
            // All elements are supported (technically)
            supportedElements.add(element);
        });

        supportedElements.forEach((element) => {
            this.addEvent(element, trigger, 'focus');
            this.addEvent(element, trigger, 'blur');
        });

        return Array.from(supportedElements).some((element) => document.activeElement === element);
    }

    /**
     * Creates a trigger that fires when all supported elements are within a specific element(s).
     *
     * @param {TriggerEntity} trigger
     * @param {boolean} isWithin If the elements must be within the selector.
     * @param {string} selector The selector to check if the elements are within.
     */
    createWithinCondition(trigger, isWithin, selector) {
        const supportedElements = new Set();

        trigger.get('elements').forEach((element) => {
            // All elements are supported (technically)
            supportedElements.add(element);
        });

        supportedElements.forEach((element) => {
            this.addEvent(element, trigger, 'click');
            this.addEvent(element, trigger, 'change');
            this.addEvent(element, trigger, 'focus');
            this.addEvent(element, trigger, 'blur');
        });

        return Array.from(supportedElements).every(
            (element) => {
                let within = false;

                document.querySelectorAll(selector).forEach((parent) => {
                    if (within === true) {
                        return;
                    }
                    if (parent.contains(element)) {
                        within = true;
                    }
                });

                return within === isWithin;
            },
        );
    }

    /**
     * Manually runs all registered triggers, optionally for a specific element.
     *
     * This can be used to update the state of the triggers.
     */
    runEvents(forElement = undefined) {
        this.connectors.forEach((elementConnectors, element) => {
            if (forElement && element !== forElement) {
                return;
            }

            elementConnectors.forEach((connector) => {
                connector();
            });
        });
    }

    /**
     * Clears all registered events.
     *
     * This  will disable all triggers and their event listeners on the target elements.
     */
    resetEvents() {
        this.connectors.forEach((elementConnectors, element) => {
            elementConnectors.forEach((connector, event) => {
                element.removeEventListener(event, connector);
            });
        });

        this.connectors.clear();
        this.events.clear();
    }

    /**
     * Executes actions based on the trigger condition.
     *
     * Actions should be binary, and show one state when the condition is met, and another when it
     * is not. The second parameter is used to determine if the conditions of the trigger have been
     * met. If a trigger has multiple conditions, ALL conditions must be met.
     *
     * @param {TriggerEntity} trigger
     * @param {boolean} conditionMet
     */
    executeActions(trigger, conditionMet) {
        this.parseCommand(trigger.get('action')).forEach((action) => {
            // Allow plugins to override action(s) and prevent the default functionality from firing.
            if (this.snowboard.globalEvent('trigger.action', this.element, trigger, action, conditionMet) === false) {
                this.afterAction(trigger, this.element, {
                    action: action.name,
                    override: true,
                    conditionMet,
                });
                return;
            }

            // A one-way action won't occur if the condition is not met
            if (action.oneWay && !conditionMet) {
                return;
            }

            switch (action.name) {
                case 'show':
                case 'hide':
                    this.actionShow(
                        trigger,
                        conditionMet,
                        (action.parameters[0])
                            ? Array.from(this.element.querySelectorAll(action.parameters[0]))
                            : [this.element],
                        (action.name === 'show') ? conditionMet : !conditionMet,
                    );
                    break;
                case 'enable':
                case 'disable':
                    this.actionEnable(
                        trigger,
                        conditionMet,
                        (action.parameters[0])
                            ? Array.from(this.element.querySelectorAll(action.parameters[0]))
                            : [this.element],
                        (action.name === 'enable') ? conditionMet : !conditionMet,
                    );
                    break;
                case 'empty':
                    if (conditionMet) {
                        this.actionValue(
                            trigger,
                            conditionMet,
                            (action.parameters[0])
                                ? Array.from(this.element.querySelectorAll(action.parameters[0]))
                                : [this.element],
                            '',
                        );
                    }
                    break;
                case 'value':
                case 'valueOf':
                    this.actionValue(
                        trigger,
                        conditionMet,
                        (action.name === 'valueOf')
                            ? Array.from(this.element.querySelectorAll(action.parameters[0]))
                            : [this.element],
                        ...(action.parameters.length > 0 && action.name === 'valueOf')
                            ? action.parameters.slice(1)
                            : action.parameters,
                    );
                    break;
                case 'class':
                case 'classOf':
                    this.actionClass(
                        trigger,
                        conditionMet,
                        (action.name === 'classOf')
                            ? Array.from(this.element.querySelectorAll(action.parameters[0]))
                            : [this.element],
                        ...(action.name === 'classOf')
                            ? action.parameters.slice(1)
                            : action.parameters,
                    );
                    break;
                default:
            }
        });
    }

    /**
     * Shows or hides a trigger element.
     *
     * This action will toggle the `hide` class on the element, and set the `display` style to
     * `none` when hidden, and the original display value when shown.
     *
     * @param {TriggerEntity} trigger
     * @param {boolean} conditionMet
     * @param {HTMLElement[]} elements
     * @param {boolean} show
     */
    actionShow(trigger, conditionMet, elements, show) {
        elements.forEach((element) => {
            if (show && (!element.style.display || element.style.display === 'none')) {
                if (element.dataset.originalDisplay !== undefined) {
                    element.style.display = element.dataset.originalDisplay;
                    delete element.dataset.originalDisplay;
                } else if (element.style.display) {
                    element.style.display = null;
                }

                this.afterAction(trigger, element, {
                    action: 'show',
                    conditionMet,
                    show,
                });
            } else if (!show && (!element.style.display || element.style.display !== 'none')) {
                if (element.style.display) {
                    element.dataset.originalDisplay = element.style.display;
                }
                element.style.display = 'none';

                this.afterAction(trigger, element, {
                    action: 'show',
                    conditionMet,
                    show,
                });
            }
        });
    }

    /**
     * Enables or disables a trigger element.
     *
     * This action will toggle the `control-disabled` class on the element, and set the `disabled`
     * property to `true` when disabled, and `false` when enabled.
     *
     * @param {TriggerEntity} trigger
     * @param {boolean} conditionMet
     * @param {HTMLElement[]} elements
     * @param {boolean} enable
     */
    actionEnable(trigger, conditionMet, elements, enable) {
        elements.forEach((element) => {
            element.classList[(enable) ? 'remove' : 'add']('control-disabled');

            if (element.disabled !== undefined) {
                element.disabled = !enable;
            }

            this.afterAction(trigger, element, {
                action: 'enable',
                conditionMet,
                enable,
            });
        });
    }

    /**
     * Sets the value of either the trigger element or a child element(s) within.
     *
     * This is a one-way action if the unmet value is not defined.
     */
    actionValue(trigger, conditionMet, elements, value, unmetValue = undefined) {
        if (!conditionMet && unmetValue === undefined) {
            return;
        }

        const newValue = (conditionMet) ? value : unmetValue;

        elements.forEach((element) => {
            if (element.matches('input[type=checkbox], input[type=radio]')) {
                element.checked = (element.value === newValue);
                return;
            }

            if (element.matches('input, select, textarea')) {
                element.value = newValue;
                return;
            }

            element.textContent = newValue;

            this.afterAction(trigger, element, {
                action: 'value',
                conditionMet,
                value,
                unmetValue,
            });
        });
    }

    /**
     * Adds or removes the class from the trigger element or a child element(s) within.
     *
     * This will simply remove the class if the unmet class is not defined. Otherwise, the classes
     * will be toggled.
     */
    actionClass(trigger, conditionMet, elements, cssClass, unmetCssClass = undefined) {
        elements.forEach((element) => {
            if (conditionMet) {
                element.classList.add(cssClass);
                if (unmetCssClass) {
                    element.classList.remove(unmetCssClass);
                }
            } else {
                element.classList.remove(cssClass);
                if (unmetCssClass) {
                    element.classList.add(unmetCssClass);
                }
            }

            this.afterAction(trigger, element, {
                action: 'class',
                conditionMet,
                cssClass,
                unmetCssClass,
            });
        });
    }

    /**
     * Fires off an event when a trigger action has been executed.
     *
     * The element affected, the trigger and details about the action are passed through to the
     * event.
     *
     * @param {TriggerEntity} trigger
     * @param {HTMLElement} element
     * @param {Object} action
     */
    afterAction(trigger, element, action) {
        this.snowboard.debug('Trigger fired', element, trigger, action);
        this.snowboard.globalEvent('trigger.fired', element, trigger, action);
    }
}
