import PluginBase from '../abstracts/PluginBase';

/**
 * Provides transition support for elements.
 *
 * Transition allows CSS transitions to be controlled and callbacks to be run once completed. It works similar to Vue
 * transitions with 3 stages of transition, and classes assigned to the element with the transition name suffixed with
 * the stage of transition:
 *
 *  - `in`: A class assigned to the element for the first frame of the transition, removed afterwards. This should be
 *      used to define the initial state of the transition.
 *  - `active`: A class assigned to the element for the duration of the transition. This should be used to define the
 *      transition itself.
 *  - `out`: A class assigned to the element after the first frame of the transition and kept to the end of the
 *      transition. This should define the end state of the transition.
 *
 * Usage:
 *      Snowboard.transition(document.element, 'transition', () => {
 *          console.log('Remove element after 7 seconds');
 *          this.remove();
 *      }, '7s');
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Transition extends PluginBase {
    /**
     * Constructor.
     *
     * @param {HTMLElement} element The element to transition
     * @param {string} transition The name of the transition, this prefixes the stages of transition.
     * @param {Function} callback An optional callback to call when the transition ends.
     * @param {Number} duration An optional override on the transition duration. Must be specified as 's' (secs) or 'ms' (msecs).
     * @param {Boolean} trailTo If true, the "out" class will remain after the end of the transition.
     */
    construct(element, transition, callback, duration, trailTo) {
        if (element instanceof HTMLElement === false) {
            throw new Error('A HTMLElement must be provided for transitioning');
        }
        this.element = element;

        if (typeof transition !== 'string') {
            throw new Error('Transition name must be specified as a string');
        }
        this.transition = transition;

        if (callback && typeof callback !== 'function') {
            throw new Error('Callback must be a valid function');
        }
        this.callback = callback;

        if (duration) {
            this.duration = this.parseDuration(duration);
        } else {
            this.duration = null;
        }

        this.trailTo = (trailTo === true);

        this.doTransition();
    }

    /**
     * Maps event classes to the given transition state.
     *
     * @param  {...any} args
     * @returns {Array}
     */
    eventClasses(...args) {
        const eventClasses = {
            in: `${this.transition}-in`,
            active: `${this.transition}-active`,
            out: `${this.transition}-out`,
        };

        if (args.length === 0) {
            return Object.values(eventClasses);
        }

        const returnClasses = [];
        Object.entries(eventClasses).forEach((entry) => {
            const [key, value] = entry;

            if (args.indexOf(key) !== -1) {
                returnClasses.push(value);
            }
        });

        return returnClasses;
    }

    /**
     * Executes the transition.
     *
     * @returns {void}
     */
    doTransition() {
        // Add duration override
        if (this.duration !== null) {
            this.element.style.transitionDuration = this.duration;
        }

        this.resetClasses();

        // Start transition - show "in" and "active" classes
        this.eventClasses('in', 'active').forEach((eventClass) => {
            this.element.classList.add(eventClass);
        });

        window.requestAnimationFrame(() => {
            // Ensure a transition exists
            if (window.getComputedStyle(this.element)['transition-duration'] !== '0s') {
                // Listen for the transition to end
                this.element.addEventListener('transitionend', () => this.onTransitionEnd(), {
                    once: true,
                });
                window.requestAnimationFrame(() => {
                    this.element.classList.remove(this.eventClasses('in')[0]);
                    this.element.classList.add(this.eventClasses('out')[0]);
                });
            } else {
                this.resetClasses();

                if (this.callback) {
                    this.callback.apply(this.element);
                }

                this.destruct();
            }
        });
    }

    /**
     * Callback function when the transition ends.
     *
     * When a transition ends, the instance of the transition is automatically destructed.
     *
     * @returns {void}
     */
    onTransitionEnd() {
        this.eventClasses('active', (!this.trailTo) ? 'out' : '').forEach((eventClass) => {
            this.element.classList.remove(eventClass);
        });

        if (this.callback) {
            this.callback.apply(this.element);
        }

        // Remove duration override
        if (this.duration !== null) {
            this.element.style.transitionDuration = null;
        }

        this.destruct();
    }

    /**
     * Cancels a transition.
     *
     * @returns {void}
     */
    cancel() {
        this.element.removeEventListener('transitionend', () => this.onTransitionEnd, {
            once: true,
        });

        this.resetClasses();

        // Remove duration override
        if (this.duration !== null) {
            this.element.style.transitionDuration = null;
        }

        // Call destructor
        this.destruct();
    }

    /**
     * Resets the classes, removing any transition classes.
     *
     * @returns {void}
     */
    resetClasses() {
        this.eventClasses().forEach((eventClass) => {
            this.element.classList.remove(eventClass);
        });
    }

    /**
     * Parses a given duration and converts it to a "ms" value.
     *
     * @param {String} duration
     * @returns {String}
     */
    parseDuration(duration) {
        const parsed = /^([0-9]+(\.[0-9]+)?)(m?s)?$/.exec(duration);
        const amount = Number(parsed[1]);
        const unit = (parsed[3] === 's')
            ? 'sec'
            : 'msec';

        return (unit === 'sec')
            ? `${amount * 1000}ms`
            : `${Math.floor(amount)}ms`;
    }
}
