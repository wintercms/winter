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
        return ['system.ui.overlay'];
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
                inspectorElement: null,
                inspectorVue: null,
                container: this.findInspectableContainer(element),
                handler: (event) => this.inspectableClick.call(this, event, inspectorData),
                title: element.dataset.inspectorTitle || 'Inspector',
                description: element.dataset.inspectorDescription || null,
                config: element.dataset.inspectorConfig || null,
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
            title: inspector.title,
            description: inspector.description,
            placement: inspector.placement,
            fallbackPlacement: inspector.fallbackPlacement,
            offsetX: inspector.offset.x,
            offsetY: inspector.offset.y,
        });
        inspector.inspectorVue.mount(inspector.inspectorElement);
    }

    hideInspector() {
        if (!this.currentInspector) {
            return;
        }

        this.currentInspector.inspectorVue.unmount();
        document.body.removeChild(this.currentInspector.inspectorElement);
    }

    inspectableClick(event, inspector) {
        event.preventDefault();

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
}
