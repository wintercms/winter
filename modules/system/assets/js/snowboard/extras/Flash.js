import PluginBase from '../abstracts/PluginBase';

/**
 * Provides flash messages for the CMS.
 *
 * Flash messages will pop up at the top center of the page and will remain for 7 seconds by default. Hovering over
 * the message will reset and pause the timer. Clicking on the flash message will dismiss it.
 *
 * Arguments:
 *  - "message": The content of the flash message. HTML is accepted.
 *  - "type": The type of flash message. This is appended as a class to the flash message itself.
 *  - "duration": How long the flash message will stay visible for, in seconds. Default: 7 seconds.
 *
 * Usage:
 *      Snowboard.flash('This is a flash message', 'info', 8);
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Flash extends PluginBase {
    /**
     * Constructor.
     *
     * @param {string} message
     * @param {string} type
     * @param {Number} duration
     */
    construct(message, type, duration) {
        this.message = message;
        this.type = type || 'default';
        this.duration = Number(duration || 7);

        if (this.duration < 0) {
            throw new Error('Flash duration must be a positive number, or zero');
        }

        this.clear();
        this.timer = null;
        this.flashTimer = null;
        this.create();
    }

    /**
     * Defines dependencies.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['transition'];
    }

    /**
     * Destructor.
     *
     * This will ensure the flash message is removed and timeout is cleared if the module is removed.
     */
    destruct() {
        if (this.timer !== null) {
            window.clearTimeout(this.timer);
        }

        if (this.flashTimer) {
            this.flashTimer.remove();
        }

        if (this.flash) {
            this.flash.remove();
            this.flash = null;
            this.flashTimer = null;
        }

        super.destruct();
    }

    /**
     * Creates the flash message.
     */
    create() {
        this.snowboard.globalEvent('flash.create', this);

        this.flash = document.createElement('DIV');
        this.flash.innerHTML = this.message;
        this.flash.classList.add('flash-message', this.type);
        this.flash.removeAttribute('data-control');
        this.flash.addEventListener('click', () => this.remove());
        this.flash.addEventListener('mouseover', () => this.stopTimer());
        this.flash.addEventListener('mouseout', () => this.startTimer());

        if (this.duration > 0) {
            this.flashTimer = document.createElement('DIV');
            this.flashTimer.classList.add('flash-timer');
            this.flash.appendChild(this.flashTimer);
        } else {
            this.flash.classList.add('no-timer');
        }

        // Add to body
        document.body.appendChild(this.flash);

        this.snowboard.transition(this.flash, 'show', () => {
            this.startTimer();
        });
    }

    /**
     * Removes the flash message.
     */
    remove() {
        this.snowboard.globalEvent('flash.remove', this);

        this.stopTimer();

        this.snowboard.transition(this.flash, 'hide', () => {
            this.flash.remove();
            this.flash = null;
            this.destruct();
        });
    }

    /**
     * Clears all flash messages available on the page.
     */
    clear() {
        document.querySelectorAll('body > div.flash-message').forEach((element) => element.remove());
    }

    /**
     * Starts the timer for this flash message.
     */
    startTimer() {
        if (this.duration === 0) {
            return;
        }

        this.timerTrans = this.snowboard.transition(this.flashTimer, 'timeout', null, `${this.duration}.0s`, true);
        this.timer = window.setTimeout(() => this.remove(), this.duration * 1000);
    }

    /**
     * Resets the timer for this flash message.
     */
    stopTimer() {
        if (this.timerTrans) {
            this.timerTrans.cancel();
        }
        if (this.timer) {
            window.clearTimeout(this.timer);
        }
    }
}
