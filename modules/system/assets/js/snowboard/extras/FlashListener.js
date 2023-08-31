import Singleton from '../abstracts/Singleton';

/**
 * Defines a default listener for flash events.
 *
 * Connects the Flash plugin to various events that use flash messages.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class FlashListener extends Singleton {
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['flash'];
    }

    /**
     * Defines listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxErrorMessage: 'ajaxErrorMessage',
            ajaxFlashMessages: 'ajaxFlashMessages',
        };
    }

    /**
     * Do flash messages for PHP flash responses.
     */
    ready() {
        document.querySelectorAll('[data-control="flash-message"]').forEach((element) => {
            this.snowboard.flash(
                element.innerHTML,
                element.dataset.flashType,
                element.dataset.flashDuration,
            );

            element.remove();
        });
    }

    /**
     * Shows a flash message for AJAX errors.
     *
     * @param {string} message
     * @returns {Boolean}
     */
    ajaxErrorMessage(message) {
        this.snowboard.flash(message, 'error');
        return false;
    }

    /**
     * Shows flash messages returned directly from AJAX functionality.
     *
     * @param {Object} messages
     */
    ajaxFlashMessages(messages) {
        Object.entries(messages).forEach((entry) => {
            const [cssClass, message] = entry;
            this.snowboard.flash(message, cssClass);
        });
        return false;
    }
}
