import Singleton from '../abstracts/Singleton';

/**
 * Displays a stripe at the top of the page that indicates loading.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class StripeLoader extends Singleton {
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['request'];
    }

    /**
     * Defines listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxStart: 'ajaxStart',
        };
    }

    ready() {
        this.counter = 0;

        this.createStripe();
    }

    ajaxStart(promise, request) {
        if (request.loading === false || request.options.stripe === false) {
            return;
        }

        this.show();

        promise.then(() => {
            this.hide();
        }).catch(() => {
            this.hide();
        });
    }

    createStripe() {
        this.indicator = document.createElement('DIV');
        this.stripe = document.createElement('DIV');
        this.stripeLoaded = document.createElement('DIV');

        this.indicator.classList.add('stripe-loading-indicator', 'loaded');
        this.stripe.classList.add('stripe');
        this.stripeLoaded.classList.add('stripe-loaded');

        this.indicator.appendChild(this.stripe);
        this.indicator.appendChild(this.stripeLoaded);

        document.body.appendChild(this.indicator);
    }

    show() {
        this.counter += 1;

        const newStripe = this.stripe.cloneNode(true);
        this.indicator.appendChild(newStripe);
        this.stripe.remove();
        this.stripe = newStripe;

        if (this.counter > 1) {
            return;
        }

        this.indicator.classList.remove('loaded');
        document.body.classList.add('wn-loading');
    }

    hide(force) {
        this.counter -= 1;

        if (force === true) {
            this.counter = 0;
        }

        if (this.counter <= 0) {
            this.indicator.classList.add('loaded');
            document.body.classList.remove('wn-loading');
        }
    }
}
