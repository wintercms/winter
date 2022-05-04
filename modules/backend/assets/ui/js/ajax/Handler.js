export default class Handler extends Snowboard.Singleton {
    listens() {
        return {
            ready: 'ready',
            ajaxFetchOptions: 'ajaxFetchOptions',
            ajaxUpdateComplete: 'ajaxUpdateComplete',
        };
    }

    ready() {
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

        // Add "render" event for backwards compatibility
        window.jQuery(document).trigger('render');
    }

    ajaxFetchOptions(options) {
        if (this.hasToken()) {
            options.headers['X-CSRF-TOKEN'] = this.getToken();
        }
    }

    ajaxUpdateComplete() {
        if (!window.jQuery) {
            return;
        }

        // Add "render" event for backwards compatibility
        window.jQuery(document).trigger('render');
    }

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

    getToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
}
