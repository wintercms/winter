/**
 * Allows attaching a loading class on elements that an AJAX request is targeting.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
 export default class AttachLoading extends Winter.Singleton {
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

    ajaxStart(request) {
        if (!request.element) {
            return;
        }

        if (request.element.tagName === 'FORM') {
            const loadElements = request.element.querySelectorAll('[data-attach-loading]');
            if (loadElements.length > 0) {
                loadElements.forEach((element) => {
                    const loadingClass = (element.dataset.attachLoading !== '')
                        ? element.dataset.attachLoading
                        : 'wn-loading';

                    element.classList.add(loadingClass);
                });
            }
        } else if (request.element.dataset.attachLoading !== undefined) {
            const loadingClass = (request.element.dataset.attachLoading !== '')
                ? request.element.dataset.attachLoading
                : 'wn-loading';

            request.element.classList.add(loadingClass);
        }
    }

    ajaxDone(request) {
        if (!request.element) {
            return;
        }

        if (request.element.tagName === 'FORM') {
            const loadElements = request.element.querySelectorAll('[data-attach-loading]');
            if (loadElements.length > 0) {
                loadElements.forEach((element) => {
                    const loadingClass = (element.dataset.attachLoading !== '')
                        ? element.dataset.attachLoading
                        : 'wn-loading';

                    element.classList.remove(loadingClass);
                });
            }
        } else if (request.element.dataset.attachLoading !== undefined) {
            const loadingClass = (request.element.dataset.attachLoading !== '')
                ? request.element.dataset.attachLoading
                : 'wn-loading';

            request.element.classList.remove(loadingClass);
        }
    }
 }
