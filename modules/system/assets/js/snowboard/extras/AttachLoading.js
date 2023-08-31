import Singleton from '../abstracts/Singleton';

/**
 * Allows attaching a loading class on elements that an AJAX request is targeting.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class AttachLoading extends Singleton {
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
            ajaxStart: 'ajaxStart',
            ajaxDone: 'ajaxDone',
        };
    }

    ajaxStart(promise, request) {
        if (!request.element) {
            return;
        }

        if (request.element.tagName === 'FORM') {
            const loadElements = request.element.querySelectorAll('[data-attach-loading]');
            if (loadElements.length > 0) {
                loadElements.forEach((element) => {
                    element.classList.add(this.getLoadingClass(element));
                });
            }
        } else if (request.element.dataset.attachLoading !== undefined) {
            request.element.classList.add(this.getLoadingClass(request.element));
        }
    }

    ajaxDone(data, request) {
        if (!request.element) {
            return;
        }

        if (request.element.tagName === 'FORM') {
            const loadElements = request.element.querySelectorAll('[data-attach-loading]');
            if (loadElements.length > 0) {
                loadElements.forEach((element) => {
                    element.classList.remove(this.getLoadingClass(element));
                });
            }
        } else if (request.element.dataset.attachLoading !== undefined) {
            request.element.classList.remove(this.getLoadingClass(request.element));
        }
    }

    getLoadingClass(element) {
        return (element.dataset.attachLoading !== undefined && element.dataset.attachLoading !== '')
            ? element.dataset.attachLoading
            : 'wn-loading';
    }
}
