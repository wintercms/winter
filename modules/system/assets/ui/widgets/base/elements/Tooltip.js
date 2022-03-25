/**
 * Tooltip element.
 *
 * Creates tooltips for elements.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Overlay extends Snowboard.Singleton {
    /**
     * Defines the dependencies.
     *
     * @returns {Array}
     */
    dependencies() {
        return ['transition'];
    }

    /**
     * Defines listeners for events.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        this.attachEventListeners();
    }

    destructor() {
        this.detachEventListeners();
    }

    attachEventListeners() {
        document.body.addEventListener('mouseover', (event) => this.onMouseOver(event), {
            capture: true,
            passive: true,
        });
        document.body.addEventListener('mouseout', (event) => this.onMouseOut(event), {
            capture: true,
            passive: true,
        });
    }

    detachEventListeners() {
        document.body.removeEventListener('mouseover', (event) => this.onMouseOver(event), {
            passive: true,
        });
        document.body.removeEventListener('mouseout', (event) => this.onMouseOut(event), {
            passive: true,
        });
    }

    onMouseOver(event) {
        const element = event.target;

        if (!element.matches('[data-tooltip]')) {
            return;
        }

        if (!element.dataset.activeTooltip) {

        }
    }

    onMouseOut(event) {
        const element = event.target;

        if (!element.matches('[data-tooltip]')) {
            return;
        }
    }
}
