import { createApp } from 'vue';
import Inspector from './Inspector.vue';

/**
 * Inspector manager.
 *
 * This class provides the management and initialization of Inspector widgets on a page.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Manager extends Snowboard.Singleton {
    /**
     * Constructor.
     *
     * @param {Snowboard} snowboard
     */
    constructor(snowboard) {
        super(snowboard);

        this.inspectableElements = [];
        this.currentInspector = null;
    }

    /**
     * Defines the dependencies.
     *
     * @returns {Array}
     */
    dependencies() {
        return ['overlay', 'tooltip'];
    }

    /**
     * Defines listeners for events.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            'overlay.clicked': 'hideInspector',
        };
    }

    /**
     * Ready event handler.
     */
    ready() {
        this.bindInspectableElements();
    }

    /**
     * Destructor.
     *
     * Fired when this plugin is removed.
     */
    destructor() {
        this.unbindInspectableElements();
        this.inspectableElements = [];

        super.destructor();
    }

    /**
     * Searches for, and binds an event to, inspectable elements.
     */
    bindInspectableElements() {
        window.document.querySelectorAll('[data-inspectable]').forEach((element) => {
            const inspectorData = {
                element,
                form: this.findForm(element),
                inspectorElement: null,
                inspectorVue: null,
                container: this.findInspectableContainer(element),
                valueBag: this.findInspectableValues(element),
                handler: (event) => this.inspectableClick.call(this, event, inspectorData),
                title: element.dataset.inspectorTitle || 'Inspector',
                description: element.dataset.inspectorDescription || null,
                config: element.dataset.inspectorConfig || null,
                className: element.dataset.inspectorClass || null,
                offset: {
                    x: element.dataset.inspectorOffsetX || element.dataset.inspectorOffset || 0,
                    y: element.dataset.inspectorOffsetY || element.dataset.inspectorOffset || 0,
                },
                placement: element.dataset.inspectorPlacement || null,
                fallbackPlacement: element.dataset.inspectorFallbackPlacement || 'bottom',
                cssClasses: element.dataset.inspectorCssClass || null,
            };

            this.inspectableElements.push(inspectorData);
            element.addEventListener('click', inspectorData.handler);
        });
    }

    /**
     * Unbinds all inspectable elements.
     */
    unbindInspectableElements() {
    }

    createInspector(inspector) {
        // Create a new inspector <div> to house the Vue instance
        inspector.inspectorElement = document.createElement('div');
        document.body.appendChild(inspector.inspectorElement);
        this.currentInspector = inspector;

        // Create Vue instance and mount it to the above <div>
        inspector.inspectorVue = createApp(Inspector, {
            snowboard: this.snowboard,
            inspectedElement: inspector.element,
            form: inspector.form,
            valueBag: inspector.valueBag,
            title: inspector.title,
            description: inspector.description,
            className: inspector.className,
            placement: inspector.placement,
            fallbackPlacement: inspector.fallbackPlacement,
            offsetX: inspector.offset.x,
            offsetY: inspector.offset.y,
            hideFn: () => this.hideInspector(),
            config: inspector.config,
        });
        inspector.inspectorVue.config.unwrapInjectedRef = true;
        inspector.inspectorVue.mount(inspector.inspectorElement);
    }

    hideInspector() {
        if (!this.currentInspector) {
            return;
        }

        this.currentInspector.inspectorVue.unmount();
        document.body.removeChild(this.currentInspector.inspectorElement);
        this.currentInspector = null;
    }

    inspectableClick(event, inspector) {
        event.preventDefault();

        if (inspector === this.currentInspector) {
            return;
        }

        this.createInspector(inspector);
    }

    /**
     * Searches up the hierarchy for a container for Inspectable elements.
     *
     * @param {HTMLElement} element
     * @returns {HTMLElement|null}
     */
    findInspectableContainer(element) {
        let currentElement = element;

        while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
            if (currentElement.matches('[data-inspector-container]')) {
                return currentElement;
            }

            currentElement = currentElement.parentElement;
        }

        return null;
    }

    /**
     * Searches up the hierarchy for a container for Inspectable elements.
     *
     * @param {HTMLElement} element
     * @returns {HTMLElement|null}
     */
    findInspectableValues(element) {
        const valueBag = element.querySelector('[data-inspector-values]');

        if (valueBag) {
            return valueBag;
        }

        const container = this.findInspectableContainer(element);

        if (!container) {
            return null;
        }

        return container.querySelector('[data-inspector-values]');
    }

    /**
     * Finds the form that the element belongs to.
     *
     * @param {HTMLElement} element
     * @returns {HTMLElement|undefined}
     */
    findForm(element) {
        return element.closest('form');
    }
}
