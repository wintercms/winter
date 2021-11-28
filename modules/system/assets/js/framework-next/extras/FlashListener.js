/**
 * Defines a default listener for flash events.
 *
 * Connects the Flash module to various events that use flash messages.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class FlashListener extends Winter.Singleton {
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['flash'];
    }

    /**
     * Shows a flash message for AJAX errors.
     *
     * @param {string} message
     * @returns {Boolean}
     */
    ajaxErrorMessage(message) {
        this.winter.flash(message, 'error');
        return false;
    }
}
