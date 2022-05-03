export default class Handler extends Snowboard.Singleton {
    listens() {
        return {
            ready: 'ready',
            ajaxFetchOptions: 'ajaxFetchOptions',
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
    }

    ajaxFetchOptions(options) {
        if (this.hasToken()) {
            options.headers['X-CSRF-TOKEN'] = this.getToken();
        }
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
