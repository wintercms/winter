import { delegate } from 'jquery-events-to-dom-events';

/**
 * Backend AJAX handler.
 *
 * This is a utility script that resolves some backwards-compatibility issues with the functionality
 * that relies on the old framework, and ensures that Snowboard works well within the Backend
 * environment.
 *
 * Functions:
 *  - Adds the "render" jQuery event to Snowboard requests that widgets use to initialise.
 *  - Ensures the CSRF token is included in requests.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Handler extends Snowboard.Singleton {
    /**
     * Event listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxFetchOptions: 'ajaxFetchOptions',
            ajaxUpdateComplete: 'ajaxUpdateComplete',
        };
    }

    /**
     * Ready handler.
     *
     * Fires off a "render" event.
     */
    ready() {
        if (!window.jQuery) {
            return;
        }
        delegate('render');

        // Add global event for rendering in Snowboard
        delegate('render');
        document.addEventListener('$render', () => {
            this.snowboard.globalEvent('render');
        });

        // Add "render" event for backwards compatibility
        window.jQuery(document).trigger('render');

        // Add global event for rendering in Snowboard
        document.addEventListener('$render', () => {
            this.snowboard.globalEvent('render');
        });
    }

    /**
     * Adds the jQuery AJAX prefilter that the old framework uses to inject the CSRF token in AJAX
     * calls.
     */
    addPrefilter() {
        if (!window.jQuery) {
            return;
        }

        window.jQuery.ajaxPrefilter((options) => {
            if (this.hasToken()) {
                if (!options.headers) {
                    options.headers = {};
                }
                options.headers['X-CSRF-TOKEN'] = this.getToken();
            }
        });
    }

    /**
     * Fetch options handler.
     *
     * Ensures that the CSRF token is included in Snowboard requests.
     *
     * @param {Object} options
     */
    ajaxFetchOptions(options) {
        if (this.hasToken()) {
            options.headers['X-CSRF-TOKEN'] = this.getToken();
        }
    }

    /**
     * Update complete handler.
     *
     * Fires off a "render" event when partials are updated so that any widgets included in
     * responses are correctly initialised.
     */
    ajaxUpdateComplete() {
        if (!window.jQuery) {
            return;
        }

        // Add "render" event for backwards compatibility
        window.jQuery(document).trigger('render');
    }

    /**
     * Determines if a CSRF token is available.
     *
     * @returns {Boolean}
     */
    hasToken() {
        const tokenElement = document.querySelector('meta[name="csrf-token"]');

        if (!tokenElement) {
            return false;
        }
        if (!tokenElement.hasAttribute('content')) {
            return false;
        }

        return true;
    }

    /**
     * Gets the CSRF token.
     *
     * @returns {String}
     */
    getToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
}
