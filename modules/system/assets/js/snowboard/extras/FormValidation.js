import Singleton from '../abstracts/Singleton';

/**
 * Adds AJAX-driven form validation to Snowboard requests.
 *
 * Documentation for this feature can be found here:
 * https://wintercms.com/docs/snowboard/extras#ajax-validation
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class FormValidation extends Singleton {
    /**
     * Constructor.
     */
    construct() {
        this.errorBags = [];
    }

    /**
     * Defines listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxStart: 'clearValidation',
            ajaxValidationErrors: 'doValidation',
        };
    }

    /**
     * Ready event handler.
     */
    ready() {
        this.collectErrorBags(document);
    }

    /**
     * Retrieves validation errors from an AJAX response and passes them through to the error bags.
     *
     * This handler returns false to cancel any further validation handling, and prevents the flash
     * message that is displayed by default for field errors in AJAX requests from showing.
     *
     * @param {HTMLFormElement} form
     * @param {Object} invalidFields
     * @param {Request} request
     * @returns {Boolean}
     */
    doValidation(form, invalidFields, request) {
        if (request.element && request.element.dataset.requestValidate === undefined) {
            return null;
        }
        if (!form) {
            return null;
        }

        const errorBags = this.errorBags.filter((errorBag) => errorBag.form === form);
        errorBags.forEach((errorBag) => {
            this.showErrorBag(errorBag, invalidFields);
        });

        return false;
    }

    /**
     * Clears any validation errors in the given form.
     *
     * @param {Promise} promise
     * @param {Request} request
     * @returns {void}
     */
    clearValidation(promise, request) {
        if (request.element && request.element.dataset.requestValidate === undefined) {
            return;
        }
        if (!request.form) {
            return;
        }

        const errorBags = this.errorBags.filter((errorBag) => errorBag.form === request.form);
        errorBags.forEach((errorBag) => {
            this.hideErrorBag(errorBag);
        });
    }

    /**
     * Collects error bags (elements with "data-validate-error" attribute) and links them to a
     * placeholder and form.
     *
     * The error bags will be initially hidden, and will only show when validation errors occur.
     *
     * @param {HTMLElement} rootNode
     */
    collectErrorBags(rootNode) {
        rootNode.querySelectorAll('[data-validate-error], [data-validate-for]').forEach((errorBag) => {
            const form = errorBag.closest('form[data-request-validate]');

            // If this error bag does not reside within a validating form, remove it
            if (!form) {
                errorBag.parentNode.removeChild(errorBag);
                return;
            }

            // Find message list node, if available
            let messageListElement = null;
            if (errorBag.matches('[data-validate-error]')) {
                messageListElement = errorBag.querySelector('[data-message]');
            }

            // Create a placeholder node
            const placeholder = document.createComment('');

            // Register error bag and replace with placeholder
            const errorBagData = {
                element: errorBag,
                form,
                validateFor: (errorBag.dataset.validateFor)
                    ? errorBag.dataset.validateFor.split(/\s*,\s*/)
                    : '*',
                placeholder,
                messageListElement: (messageListElement)
                    ? messageListElement.cloneNode(true)
                    : null,
                messageListAnchor: null,
                customMessage: (errorBag.dataset.validateFor)
                    ? (errorBag.textContent !== '' || errorBag.childNodes.length > 0)
                    : false,
            };

            // If an message list element exists, create another placeholder to act as an anchor point
            if (messageListElement) {
                const messageListAnchor = document.createComment('');
                messageListElement.parentNode.replaceChild(messageListAnchor, messageListElement);
                errorBagData.messageListAnchor = messageListAnchor;
            }

            errorBag.parentNode.replaceChild(placeholder, errorBag);

            this.errorBags.push(errorBagData);
        });
    }

    /**
     * Hides an error bag, replacing the error messages with a placeholder node.
     *
     * @param {Object} errorBag
     */
    hideErrorBag(errorBag) {
        if (errorBag.element.isConnected) {
            errorBag.element.parentNode.replaceChild(errorBag.placeholder, errorBag.element);
        }
    }

    /**
     * Shows an error bag with the given invalid fields.
     *
     * @param {Object} errorBag
     * @param {Object} invalidFields
     */
    showErrorBag(errorBag, invalidFields) {
        if (!this.errorBagValidatesField(errorBag, invalidFields)) {
            return;
        }

        if (!errorBag.element.isConnected) {
            errorBag.placeholder.parentNode.replaceChild(errorBag.element, errorBag.placeholder);
        }

        if (errorBag.validateFor !== '*') {
            if (!errorBag.customMessage) {
                const firstField = Object.keys(invalidFields)
                    .filter((field) => errorBag.validateFor.includes(field))
                    .shift();
                [errorBag.element.innerHTML] = invalidFields[firstField];
            }
        } else if (errorBag.messageListElement) {
            // Remove previous error messages
            errorBag.element.querySelectorAll('[data-validation-message]').forEach((message) => {
                message.parentNode.removeChild(message);
            });

            Object.entries(invalidFields).forEach((entry) => {
                const [, errors] = entry;

                errors.forEach((error) => {
                    const messageElement = errorBag.messageListElement.cloneNode(true);
                    messageElement.dataset.validationMessage = '';
                    messageElement.innerHTML = error;
                    errorBag.messageListAnchor.after(messageElement);
                });
            });
        } else {
            [errorBag.element.innerHTML] = invalidFields[Object.keys(invalidFields).shift()];
        }
    }

    /**
     * Determines if a given error bag applies for the given invalid fields.
     *
     * @param {Object} errorBag
     * @param {Object} invalidFields
     * @returns {Boolean}
     */
    errorBagValidatesField(errorBag, invalidFields) {
        if (errorBag.validateFor === '*') {
            return true;
        }

        return Object.keys(invalidFields)
            .filter((field) => errorBag.validateFor.includes(field))
            .length > 0;
    }
}
