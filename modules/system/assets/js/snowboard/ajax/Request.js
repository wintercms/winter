/**
 * Request plugin.
 *
 * This is the default AJAX handler which will run using the `fetch()` method that is default in modern browsers.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
class Request extends Snowboard.PluginBase {
    /**
     * Constructor.
     *
     * @param {Snowboard} snowboard
     * @param {HTMLElement|string} element
     * @param {string} handler
     * @param {Object} options
     */
    constructor(snowboard, element, handler, options) {
        super(snowboard);

        if (typeof element === 'string') {
            const matchedElement = document.querySelector(element);
            if (matchedElement === null) {
                throw new Error(`No element was found with the given selector: ${element}`);
            }
            this.element = matchedElement;
        } else {
            this.element = element;
        }
        this.handler = handler;
        this.options = options || {};
        this.fetchOptions = {};
        this.responseData = null;
        this.responseError = null;
        this.cancelled = false;

        this.checkRequest();
        if (!this.snowboard.globalEvent('ajaxSetup', this)) {
            this.cancelled = true;
            return;
        }
        if (this.element) {
            const event = new Event('ajaxSetup', { cancelable: true });
            event.request = this;
            this.element.dispatchEvent(event);

            if (event.defaultPrevented) {
                this.cancelled = true;
                return;
            }
        }

        if (!this.doClientValidation()) {
            this.cancelled = true;
            return;
        }

        if (this.confirm) {
            this.doConfirm().then((confirmed) => {
                if (confirmed) {
                    this.doAjax().then(
                        (response) => {
                            if (response.cancelled) {
                                this.cancelled = true;
                                return;
                            }
                            this.responseData = response;
                            this.processUpdate(response).then(
                                () => {
                                    if (response.X_WINTER_SUCCESS === false) {
                                        this.processError(response);
                                    } else {
                                        this.processResponse(response);
                                    }
                                },
                            );
                        },
                        (error) => {
                            this.responseError = error;
                            this.processError(error);
                        },
                    ).finally(() => {
                        if (this.cancelled === true) {
                            return;
                        }

                        if (this.options.complete && typeof this.options.complete === 'function') {
                            this.options.complete(this.responseData, this);
                        }
                        this.snowboard.globalEvent('ajaxDone', this.responseData, this);

                        if (this.element) {
                            const event = new Event('ajaxAlways');
                            event.request = this;
                            event.responseData = this.responseData;
                            event.responseError = this.responseError;
                            this.element.dispatchEvent(event);
                        }
                    });
                }
            });
        } else {
            this.doAjax().then(
                (response) => {
                    if (response.cancelled) {
                        this.cancelled = true;
                        return;
                    }
                    this.responseData = response;
                    this.processUpdate(response).then(
                        () => {
                            if (response.X_WINTER_SUCCESS === false) {
                                this.processError(response);
                            } else {
                                this.processResponse(response);
                            }
                        },
                    );
                },
                (error) => {
                    this.responseError = error;
                    this.processError(error);
                },
            ).finally(() => {
                if (this.cancelled === true) {
                    return;
                }

                if (this.options.complete && typeof this.options.complete === 'function') {
                    this.options.complete(this.responseData, this);
                }
                this.snowboard.globalEvent('ajaxDone', this.responseData, this);

                if (this.element) {
                    const event = new Event('ajaxAlways');
                    event.request = this;
                    event.responseData = this.responseData;
                    event.responseError = this.responseError;
                    this.element.dispatchEvent(event);
                }
            });
        }
    }

    /**
     * Dependencies for this plugin.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['cookie', 'jsonParser'];
    }

    /**
     * Validates the element and handler given in the request.
     */
    checkRequest() {
        if (this.element && this.element instanceof Element === false) {
            throw new Error('The element provided must be an Element instance');
        }

        if (this.handler === undefined) {
            throw new Error('The AJAX handler name is not specified.');
        }

        if (!this.handler.match(/^(?:\w+:{2})?on*/)) {
            throw new Error('Invalid AJAX handler name. The correct handler name format is: "onEvent".');
        }
    }

    /**
     * Creates a Fetch request.
     *
     * This method is made available for plugins to extend or override the default fetch() settings with their own.
     *
     * @returns {Promise}
     */
    getFetch() {
        this.fetchOptions = (this.options.fetchOptions !== undefined && typeof this.options.fetchOptions === 'object')
            ? this.options.fetchOptions
            : {
                method: 'POST',
                headers: this.headers,
                body: this.data,
                redirect: 'follow',
                mode: 'same-origin',
            };

        this.snowboard.globalEvent('ajaxFetchOptions', this.fetchOptions, this);

        return fetch(this.url, this.fetchOptions);
    }

    /**
     * Run client-side validation on the form, if available.
     *
     * @returns {boolean}
     */
    doClientValidation() {
        if (this.options.browserValidate === true && this.form) {
            if (this.form.checkValidity() === false) {
                this.form.reportValidity();
                return false;
            }
        }

        return true;
    }

    /**
     * Executes the AJAX query.
     *
     * Returns a Promise object for when the AJAX request is completed.
     *
     * @returns {Promise}
     */
    doAjax() {
        // Allow plugins to cancel the AJAX request before sending
        if (this.snowboard.globalEvent('ajaxBeforeSend', this) === false) {
            return Promise.resolve({
                cancelled: true,
            });
        }

        const ajaxPromise = new Promise((resolve, reject) => {
            this.getFetch().then(
                (response) => {
                    if (!response.ok && response.status !== 406) {
                        if (response.headers.has('Content-Type') && response.headers.get('Content-Type').includes('/json')) {
                            response.json().then(
                                (responseData) => {
                                    reject(this.renderError(
                                        responseData.message,
                                        responseData.exception,
                                        responseData.file,
                                        responseData.line,
                                        responseData.trace,
                                    ));
                                },
                                (error) => {
                                    reject(this.renderError(`Unable to parse JSON response: ${error}`));
                                },
                            );
                        } else {
                            response.text().then(
                                (responseText) => {
                                    reject(this.renderError(responseText));
                                },
                                (error) => {
                                    reject(this.renderError(`Unable to process response: ${error}`));
                                },
                            );
                        }
                        return;
                    }

                    if (response.headers.has('Content-Type') && response.headers.get('Content-Type').includes('/json')) {
                        response.json().then(
                            (responseData) => {
                                resolve({
                                    ...responseData,
                                    X_WINTER_SUCCESS: response.status !== 406,
                                    X_WINTER_RESPONSE_CODE: response.status,
                                });
                            },
                            (error) => {
                                reject(this.renderError(`Unable to parse JSON response: ${error}`));
                            },
                        );
                    } else {
                        response.text().then(
                            (responseData) => {
                                resolve(responseData);
                            },
                            (error) => {
                                reject(this.renderError(`Unable to process response: ${error}`));
                            },
                        );
                    }
                },
                (responseError) => {
                    reject(this.renderError(`Unable to retrieve a response from the server: ${responseError}`));
                },
            );
        });

        this.snowboard.globalEvent('ajaxStart', ajaxPromise, this);

        if (this.element) {
            const event = new Event('ajaxPromise');
            event.promise = ajaxPromise;
            this.element.dispatchEvent(event);
        }

        return ajaxPromise;
    }

    /**
     * Prepares for updating the partials from the AJAX response.
     *
     * If any partials are returned from the AJAX response, this method will also action the partial updates.
     *
     * Returns a Promise object which tracks when the partial update is complete.
     *
     * @param {Object} response
     * @returns {Promise}
     */
    processUpdate(response) {
        return new Promise((resolve, reject) => {
            if (typeof this.options.beforeUpdate === 'function') {
                if (this.options.beforeUpdate.apply(this, [response]) === false) {
                    reject();
                    return;
                }
            }

            // Extract partial information
            const partials = {};
            Object.entries(response).forEach((entry) => {
                const [key, value] = entry;

                if (key.substr(0, 8) !== 'X_WINTER') {
                    partials[key] = value;
                }
            });

            if (Object.keys(partials).length === 0) {
                resolve();
                return;
            }

            const promises = this.snowboard.globalPromiseEvent('ajaxBeforeUpdate', response, this);
            promises.then(
                () => {
                    this.doUpdate(partials).then(
                        () => {
                            // Allow for HTML redraw
                            window.requestAnimationFrame(() => resolve());
                        },
                        () => {
                            reject();
                        },
                    );
                },
                () => {
                    reject();
                },
            );
        });
    }

    /**
     * Updates the partials with the given content.
     *
     * @param {Object} partials
     * @returns {Promise}
     */
    doUpdate(partials) {
        return new Promise((resolve) => {
            const affected = [];

            Object.entries(partials).forEach((entry) => {
                const [partial, content] = entry;

                let selector = (this.options.update && this.options.update[partial])
                    ? this.options.update[partial]
                    : partial;

                let mode = 'replace';

                if (selector.substr(0, 1) === '@') {
                    mode = 'append';
                    selector = selector.substr(1);
                } else if (selector.substr(0, 1) === '^') {
                    mode = 'prepend';
                    selector = selector.substr(1);
                }

                const elements = document.querySelectorAll(selector);
                if (elements.length > 0) {
                    elements.forEach((element) => {
                        switch (mode) {
                            case 'append':
                                element.innerHTML += content;
                                break;
                            case 'prepend':
                                element.innerHTML = content + element.innerHTML;
                                break;
                            case 'replace':
                            default:
                                element.innerHTML = content;
                                break;
                        }

                        affected.push(element);

                        // Fire update event for each element that is updated
                        this.snowboard.globalEvent('ajaxUpdate', element, content, this);
                        const event = new Event('ajaxUpdate');
                        event.content = content;
                        element.dispatchEvent(event);
                    });
                }
            });

            this.snowboard.globalEvent('ajaxUpdateComplete', affected, this);

            resolve();
        });
    }

    /**
     * Processes the response data.
     *
     * This fires off all necessary processing functions depending on the response, ie. if there's any flash
     * messages to handle, or any redirects to be undertaken.
     *
     * @param {Object} response
     * @returns {void}
     */
    processResponse(response) {
        if (this.options.success && typeof this.options.success === 'function') {
            if (!this.options.success(this.responseData, this)) {
                return;
            }
        }

        // Allow plugins to cancel any further response handling
        if (this.snowboard.globalEvent('ajaxSuccess', this.responseData, this) === false) {
            return;
        }

        // Allow the element to cancel any further response handling
        if (this.element) {
            const event = new Event('ajaxDone', { cancelable: true });
            event.responseData = this.responseData;
            event.request = this;
            this.element.dispatchEvent(event);

            if (event.defaultPrevented) {
                return;
            }
        }

        if (this.flash && response.X_WINTER_FLASH_MESSAGES) {
            this.processFlashMessages(response.X_WINTER_FLASH_MESSAGES);
        }

        // Check for a redirect from the response, or use the redirect as specified in the options.
        if (this.redirect || response.X_WINTER_REDIRECT) {
            this.processRedirect(this.redirect || response.X_WINTER_REDIRECT);
            return;
        }

        if (response.X_WINTER_ASSETS) {
            this.processAssets(response.X_WINTER_ASSETS);
        }
    }

    /**
     * Processes an error response from the AJAX request.
     *
     * This fires off all necessary processing functions depending on the error response, ie. if there's any error or
     * validation messages to handle.
     *
     * @param {Object|Error} error
     */
    processError(error) {
        if (this.options.error && typeof this.options.error === 'function') {
            if (!this.options.error(this.responseError, this)) {
                return;
            }
        }

        // Allow plugins to cancel any further error handling
        if (this.snowboard.globalEvent('ajaxError', this.responseError, this) === false) {
            return;
        }

        // Allow the element to cancel any further error handling
        if (this.element) {
            const event = new Event('ajaxFail', { cancelable: true });
            event.responseError = this.responseError;
            event.request = this;
            this.element.dispatchEvent(event);

            if (event.defaultPrevented) {
                return;
            }
        }

        if (error instanceof Error) {
            this.processErrorMessage(error.message);
        } else {
            // Process validation errors
            if (error.X_WINTER_ERROR_FIELDS) {
                this.processValidationErrors(error.X_WINTER_ERROR_FIELDS);
            }

            if (error.X_WINTER_ERROR_MESSAGE) {
                this.processErrorMessage(error.X_WINTER_ERROR_MESSAGE);
            }
        }
    }

    /**
     * Processes a redirect response.
     *
     * By default, this processor will simply redirect the user in their browser.
     *
     * Plugins can augment this functionality from the `ajaxRedirect` event. You may also override this functionality on
     * a per-request basis through the `handleRedirectResponse` callback option. If a `false` is returned from either, the
     * redirect will be cancelled.
     *
     * @param {string} url
     * @returns {void}
     */
    processRedirect(url) {
        // Run a custom per-request redirect handler. If false is returned, don't run the redirect.
        if (typeof this.options.handleRedirectResponse === 'function') {
            if (this.options.handleRedirectResponse.apply(this, [url]) === false) {
                return;
            }
        }

        // Allow plugins to cancel the redirect
        if (this.snowboard.globalEvent('ajaxRedirect', url, this) === false) {
            return;
        }

        // Indicate that the AJAX request is finished if we're still on the current page
        // so that the loading indicator for redirects that just change the hash value of
        // the URL instead of leaving the page will properly stop.
        // @see https://github.com/octobercms/october/issues/2780
        window.addEventListener('popstate', () => {
            if (this.element) {
                const event = document.createEvent('CustomEvent');
                event.eventName = 'ajaxRedirected';
                this.element.dispatchEvent(event);
            }
        }, {
            once: true,
        });

        window.location.assign(url);
    }

    /**
     * Processes an error message.
     *
     * By default, this processor will simply alert the user through a simple `alert()` call.
     *
     * Plugins can augment this functionality from the `ajaxErrorMessage` event. You may also override this functionality
     * on a per-request basis through the `handleErrorMessage` callback option. If a `false` is returned from either, the
     * error message handling will be cancelled.
     *
     * @param {string} message
     * @returns {void}
     */
    processErrorMessage(message) {
        // Run a custom per-request handler for error messages. If false is returned, do not process the error messages
        // any further.
        if (typeof this.options.handleErrorMessage === 'function') {
            if (this.options.handleErrorMessage.apply(this, [message]) === false) {
                return;
            }
        }

        // Allow plugins to cancel the error message being shown
        if (this.snowboard.globalEvent('ajaxErrorMessage', message, this) === false) {
            return;
        }

        // By default, show a browser error message
        window.alert(message);
    }

    /**
     * Processes flash messages from the response.
     *
     * By default, no flash message handling will occur.
     *
     * Plugins can augment this functionality from the `ajaxFlashMessages` event. You may also override this functionality
     * on a per-request basis through the `handleFlashMessages` callback option. If a `false` is returned from either, the
     * flash message handling will be cancelled.
     *
     * @param {Object} messages
     * @returns
     */
    processFlashMessages(messages) {
        // Run a custom per-request flash handler. If false is returned, don't show the flash message
        if (typeof this.options.handleFlashMessages === 'function') {
            if (this.options.handleFlashMessages.apply(this, [messages]) === false) {
                return;
            }
        }

        this.snowboard.globalEvent('ajaxFlashMessages', messages, this);
    }

    /**
     * Processes validation errors for fields.
     *
     * By default, no validation error handling will occur.
     *
     * Plugins can augment this functionality from the `ajaxValidationErrors` event. You may also override this functionality
     * on a per-request basis through the `handleValidationErrors` callback option. If a `false` is returned from either, the
     * validation error handling will be cancelled.
     *
     * @param {Object} fields
     * @returns
     */
    processValidationErrors(fields) {
        if (typeof this.options.handleValidationErrors === 'function') {
            if (this.options.handleValidationErrors.apply(this, [this.form, fields]) === false) {
                return;
            }
        }

        // Allow plugins to cancel the validation errors being handled
        this.snowboard.globalEvent('ajaxValidationErrors', this.form, fields, this);
    }

    /**
     * Confirms the request with the user before proceeding.
     *
     * This is an asynchronous method. By default, it will use the browser's `confirm()` method to query the user to
     * confirm the action. This method will return a Promise with a boolean value depending on whether the user confirmed
     * or not.
     *
     * Plugins can augment this functionality from the `ajaxConfirmMessage` event. You may also override this functionality
     * on a per-request basis through the `handleConfirmMessage` callback option. If a `false` is returned from either,
     * the confirmation is assumed to have been denied.
     *
     * @returns {Promise}
     */
    async doConfirm() {
        // Allow for a custom handler for the confirmation, per request.
        if (typeof this.options.handleConfirmMessage === 'function') {
            if (this.options.handleConfirmMessage.apply(this, [this.confirm]) === false) {
                return false;
            }

            return true;
        }

        // If no plugins have customised the confirmation, use a simple browser confirmation.
        if (this.snowboard.listensToEvent('ajaxConfirmMessage').length === 0) {
            return window.confirm(this.confirm);
        }

        // Run custom plugin confirmations
        const promises = this.snowboard.globalPromiseEvent('ajaxConfirmMessage', this.confirm, this);

        try {
            const fulfilled = await promises;
            if (fulfilled) {
                return true;
            }
        } catch (e) {
            return false;
        }

        return false;
    }

    get form() {
        if (this.options.form) {
            return this.options.form;
        }
        if (!this.element) {
            return null;
        }
        if (this.element.tagName === 'FORM') {
            return this.element;
        }

        return this.element.closest('form');
    }

    get context() {
        return {
            handler: this.handler,
            options: this.options,
        };
    }

    get headers() {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest', // Keeps compatibility with jQuery AJAX
            'X-WINTER-REQUEST-HANDLER': this.handler,
            'X-WINTER-REQUEST-PARTIALS': this.extractPartials(this.options.update || []),
        };

        if (this.flash) {
            headers['X-WINTER-REQUEST-FLASH'] = 1;
        }

        if (this.xsrfToken) {
            headers['X-XSRF-TOKEN'] = this.xsrfToken;
        }

        return headers;
    }

    get loading() {
        return this.options.loading || false;
    }

    get url() {
        return this.options.url || window.location.href;
    }

    get redirect() {
        return (this.options.redirect && this.options.redirect.length) ? this.options.redirect : null;
    }

    get flash() {
        return this.options.flash || false;
    }

    get files() {
        if (this.options.files === true) {
            if (FormData === undefined) {
                this.snowboard.debug('This browser does not support file uploads');
                return false;
            }

            return true;
        }

        return false;
    }

    get xsrfToken() {
        return this.snowboard.cookie().get('XSRF-TOKEN');
    }

    get data() {
        const data = (typeof this.options.data === 'object') ? this.options.data : {};

        const formData = new FormData(this.form || undefined);
        if (Object.keys(data).length > 0) {
            Object.entries(data).forEach((entry) => {
                const [key, value] = entry;
                formData.append(key, value);
            });
        }

        return formData;
    }

    get confirm() {
        return this.options.confirm || false;
    }

    /**
     * Extracts partials.
     *
     * @param {Object} update
     * @returns {string}
     */
    extractPartials(update) {
        return Object.keys(update).join('&');
    }

    /**
     * Renders an error with useful debug information.
     *
     * This method is used internally when the AJAX request could not be completed or processed correctly due to an error.
     *
     * @param {string} message
     * @param {string} exception
     * @param {string} file
     * @param {Number} line
     * @param {string[]} trace
     * @returns {Error}
     */
    renderError(message, exception, file, line, trace) {
        const error = new Error(message);
        error.exception = exception || null;
        error.file = file || null;
        error.line = line || null;
        error.trace = trace || [];
        return error;
    }
}

Snowboard.addPlugin('request', Request);
