/**
 * Overlay element.
 *
 * Controls the overlay element that shrouds the page when a modal, popup or popover is open.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Overlay extends Snowboard.Singleton {
    /**
     * Constructor.
     *
     * @param {Snowboard} snowboard
     */
    constructor(snowboard) {
        super(snowboard);

        this.overlay = null;
        this.shown = false;
        this.color = '#000000';
        this.opacity = 0.42;
        this.speed = 175;
    }

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

    /**
     * Ready event handler.
     */
    ready() {
        this.createOverlay();
    }

    /**
     * Destructor.
     *
     * Destroys the overlay.
     */
    destructor() {
        this.destroyOverlay();
        super.destructor();
    }

    /**
     * Creates an overlay element and inserts it into the DOM.
     */
    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.id = 'overlay';
        this.setStyle();
        this.overlay.addEventListener('click', (event) => {
            this.snowboard.globalEvent('overlay.clicked', this.overlay, event);
        });

        document.body.appendChild(this.overlay);
    }

    /**
     * Removes and destroys the overlay element from the DOM.
     */
    destroyOverlay() {
        document.body.removeChild(this.overlay);
        this.overlay = null;
    }

    /**
     * Sets the styling on the overlay.
     */
    setStyle() {
        this.overlay.style.backgroundColor = this.color;
        this.overlay.style.position = 'fixed';
        this.overlay.style.top = 0;
        this.overlay.style.left = 0;
        this.overlay.style.zIndex = 1000;
        this.overlay.style.transitionProperty = 'opacity';
        this.overlay.style.transitionTimingFunction = 'ease-out';
        this.overlay.style.transitionDuration = `${this.speed}ms`;

        window.requestAnimationFrame(() => {
            if (this.shown) {
                this.overlay.style.width = '100%';
                this.overlay.style.height = '100%';
                this.overlay.style.opacity = this.opacity;
            } else {
                this.overlay.style.width = '0px';
                this.overlay.style.height = '0px';
                this.overlay.style.opacity = 0;
            }
        });
    }

    /**
     * Shows the overlay.
     *
     * Fires an "overlay.show" event, and follows up with an "overlay.shown" when the transition completes.
     */
    show() {
        this.snowboard.globalEvent('overlay.show', this.overlay);
        this.shown = true;
        this.overlay.style.width = '100%';
        this.overlay.style.height = '100%';
        window.requestAnimationFrame(() => {
            this.overlay.style.opacity = this.opacity;

            this.overlay.addEventListener('transitionend', () => {
                this.snowboard.globalEvent('overlay.shown', this.overlay);
            }, {
                once: true,
            });
        });
    }

    /**
     * Hides the overlay.
     *
     * Fires an "overlay.hide" event, and follows up with an "overlay.hidden" when the transition completes.
     */
    hide() {
        this.snowboard.globalEvent('overlay.hide', this.overlay);
        this.overlay.style.opacity = 0;
        this.overlay.addEventListener('transitionend', () => {
            this.shown = false;
            this.overlay.style.width = '0px';
            this.overlay.style.height = '0px';
            this.snowboard.globalEvent('overlay.hidden', this.overlay);
        }, {
            once: true,
        });
    }

    /**
     * Toggles the overlay.
     */
    toggle() {
        if (this.shown) {
            this.hide();
            return;
        }

        this.show();
    }

    /**
     * Sets the color of the overlay.
     *
     * Fluent method.
     *
     * @param {string} color
     * @returns {Overlay}
     */
    setColor(color) {
        this.color = String(color);
        this.setStyle();
        return this;
    }

    /**
     * Sets the opacity of the overlay.
     *
     * Fluent method.
     *
     * @param {Number} opacity
     * @returns {Overlay}
     */
    setOpacity(opacity) {
        this.opacity = Number(opacity);
        this.setStyle();
        return this;
    }

    /**
     * Sets the speed of the transition for the overlay.
     *
     * Fluent method.
     *
     * @param {Number} speed
     * @returns {Overlay}
     */
    setSpeed(speed) {
        this.speed = Number(speed);
        this.setStyle();
        return this;
    }
}
