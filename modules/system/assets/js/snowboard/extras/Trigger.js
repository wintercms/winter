import PluginBase from '../abstracts/PluginBase';

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
         * The triggers for this element.
         */
        this.triggers = new Map();

        this.parseTriggers();

        // this.config = this.snowboard.dataConfig(this, element);

        // this.trigger = this.config.get('trigger');
        // if (!this.trigger) {
        //     this.snowboard.error('Trigger selector is not specified.', element);
        //     return;
        // }

        // this.condition = this.config.get('triggerWhen')
        //     ?? this.config.get('triggerCondition');

        // if (!this.condition || !this.isValidCondition(this.condition)) {
        //     this.snowboard.error('Trigger condition is not specified or not valid.', element);
        //     return;
        // }

        // this.action = this.config.get('triggerAction');
        // if (!this.action || !this.isValidAction(this.condition)) {
        //     this.snowboard.error('Trigger action is not specified or not valid.', element);
        //     return;
        // }

        // console.log(this.parseCommand(this.condition));
        // console.log(this.parseCommand(this.action));
    }

    parseTriggers() {
        const { dataset } = this.element;
        const triggers = new Map();

        Object.keys(dataset).forEach((key) => {
            if (!key.startsWith('trigger')) {
                return;
            }

            let trigger = key.replace('trigger', '').toLowerCase();



            if (trigger.endsWith('Parent')) {
                trigger = trigger.replace('Parent', '');
                if (!triggers.has(trigger)) {
                    triggers.set(trigger, new Map());
                    triggers.get(trigger).set('parent', dataset[key]);
                }
                return;
            }
            if (trigger.endsWith('When')) {
                trigger = trigger.replace('When', '');
                if (!triggers.has(trigger)) {
                    triggers.set(trigger, new Map());
                    triggers.get(trigger).set('parent', dataset[key]);
                }
            }
        });
    }

    /**
     * Determines if the provided trigger condition is valid.
     *
     * @param {string} condition
     * @returns
     */
    isValidCondition(condition) {
        return [
            'checked',
            'unchecked',
            'empty',
            'value',
            'oneOf',
            'allOf',
            'focus',
            'blur',
        ].includes(this.parseCommand(condition).name.toLowerCase());
    }

    parseCommand(command) {
        // Support old-format value command (value[foo,bar])
        if (command.startsWith('value')) {
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

            return {
                name: 'value',
                parameters: values,
            };
        }

        if (!command.includes(':')) {
            return {
                name: command,
                parameters: [],
            };
        }

        const [name, parameters] = command.split(':', 2);

        if (!parameters.includes(',')) {
            return {
                name,
                parameters: [parameters],
            };
        }

        const splitValues = parameters.replace(/("[^"]*")|('[^']*')/g, (quoted) => quoted.replace(/,/g, '|||'))
            .split(',')
            .map((splitValue) => splitValue.replace(/\|\|\|/g, ',').replace(/^['"]|['"]$/g, '').trim());

        return {
            name,
            parameters: splitValues,
        };
    }

    isValidAction(action) {
        return [
            'show',
            'hide',
            'enable',
            'disable',
            'value',
            'empty',
            'attr',
        ].includes(this.parseCommand(action).name.toLowerCase());
    }
}
