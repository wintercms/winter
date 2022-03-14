/**
 * Overlay element.
 *
 * Controls the overlay element that shrouds the page when a modal, popup or popover is open.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Overlay extends Snowboard.Singleton
{
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
        this.opacity = 0.5;
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
        this.overlay.style.width = '100%';
        this.overlay.style.height = '100%';

        if (this.shown) {
            this.overlay.style.display = 'block';
            this.overlay.style.opacity = this.opacity;
        } else {
            this.overlay.style.display = 'none';
            this.overlay.style.opacity = 0;
        }

        window.requestAnimationFrame(() => {
            this.overlay.style.transitionProperty = 'opacity';
            this.overlay.style.transitionTimingFunction = 'ease-in';
            this.overlay.style.transitionDuration = `${this.speed}ms`;
        });
    }

    show() {
        this.shown = true;
        this.overlay.style.display = 'block';
        this.overlay.style.opacity = this.opacity;
    }

    hide() {
        this.overlay.style.opacity = 0;
        this.overlay.addEventListener('transitionend', () => {
            this.shown = false;
            this.overlay.style.display = 'none';
        }, {
            once: true,
        });
    }

    toggle() {
        if (this.shown) {
            this.hide();
            return;
        }

        this.show();
    }
}
