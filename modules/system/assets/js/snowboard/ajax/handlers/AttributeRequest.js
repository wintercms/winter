import Singleton from '../../abstracts/Singleton';

/**
 * Enable Data Attributes API for AJAX requests.
 *
 * This is an extension of the base AJAX functionality that includes handling of HTML data attributes for processing
 * AJAX requests. It is separated from the base AJAX functionality to allow developers to opt-out of data attribute
 * requests if they do not intend to use them.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class AttributeRequest extends Singleton {
    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
            ajaxSetup: 'onAjaxSetup',
        };
    }

    /**
     * Ready event callback.
     *
     * Attaches handlers to the window to listen for all request interactions.
     */
    ready() {
        this.attachHandlers();
        this.disableDefaultFormValidation();
    }

    /**
     * Dependencies.
     *
     * @returns {string[]}
     */
    dependencies() {
        return ['request', 'jsonParser'];
    }

    /**
     * Destructor.
     *
     * Detaches all handlers.
     */
    destruct() {
        this.detachHandlers();

        super.destruct();
    }

    /**
     * Attaches the necessary handlers for all request interactions.
     */
    attachHandlers() {
        window.addEventListener('change', (event) => this.changeHandler(event));
        window.addEventListener('click', (event) => this.clickHandler(event));
        window.addEventListener('keydown', (event) => this.keyDownHandler(event));
        window.addEventListener('submit', (event) => this.submitHandler(event));
    }

    /**
     * Disables default form validation for AJAX forms.
     *
     * A form that contains a `data-request` attribute to specify an AJAX call without including a `data-browser-validate`
     * attribute means that the AJAX callback function will likely be handling the validation instead.
     */
    disableDefaultFormValidation() {
        document.querySelectorAll('form[data-request]:not([data-browser-validate])').forEach((form) => {
            form.setAttribute('novalidate', true);
        });
    }

    /**
     * Detaches the necessary handlers for all request interactions.
     */
    detachHandlers() {
        window.removeEventListener('change', (event) => this.changeHandler(event));
        window.removeEventListener('click', (event) => this.clickHandler(event));
        window.removeEventListener('keydown', (event) => this.keyDownHandler(event));
        window.removeEventListener('submit', (event) => this.submitHandler(event));
    }

    /**
     * Handles changes to select, radio, checkbox and file inputs.
     *
     * @param {Event} event
     */
    changeHandler(event) {
        // Check that we are changing a valid element
        if (!event.target.matches(
            'select[data-request], input[type=radio][data-request], input[type=checkbox][data-request], input[type=file][data-request]',
        )) {
            return;
        }

        this.processRequestOnElement(event.target);
    }

    /**
     * Handles clicks on hyperlinks and buttons.
     *
     * This event can bubble up the hierarchy to find a suitable request element.
     *
     * @param {Event} event
     */
    clickHandler(event) {
        let currentElement = event.target;

        while (currentElement && currentElement.tagName !== 'HTML') {
            if (!currentElement.matches(
                'a[data-request], button[data-request], input[type=button][data-request], input[type=submit][data-request]',
            )) {
                currentElement = currentElement.parentElement;
            } else {
                event.preventDefault();
                this.processRequestOnElement(currentElement);
                break;
            }
        }
    }

    /**
     * Handles key presses on inputs
     *
     * @param {Event} event
     */
    keyDownHandler(event) {
        // Check that we are inputting into a valid element
        if (!event.target.matches(
            'input',
        )) {
            return;
        }

        // Check that the input type is valid
        const validTypes = [
            'checkbox',
            'color',
            'date',
            'datetime',
            'datetime-local',
            'email',
            'image',
            'month',
            'number',
            'password',
            'radio',
            'range',
            'search',
            'tel',
            'text',
            'time',
            'url',
            'week',
        ];
        if (validTypes.indexOf(event.target.getAttribute('type')) === -1) {
            return;
        }

        if (event.key === 'Enter' && event.target.matches('*[data-request]')) {
            this.processRequestOnElement(event.target);
            event.preventDefault();
            event.stopImmediatePropagation();
        } else if (event.target.matches('*[data-track-input]')) {
            this.trackInput(event.target);
        }
    }

    /**
     * Handles form submissions.
     *
     * @param {Event} event
     */
    submitHandler(event) {
        // Check that we are submitting a valid form
        if (!event.target.matches(
            'form[data-request]',
        )) {
            return;
        }

        event.preventDefault();

        this.processRequestOnElement(event.target);
    }

    /**
     * Processes a request on a given element, using its data attributes.
     *
     * @param {HTMLElement} element
     */
    processRequestOnElement(element) {
        const data = element.dataset;

        const handler = String(data.request);
        const options = {
            confirm: ('requestConfirm' in data) ? String(data.requestConfirm) : null,
            redirect: ('requestRedirect' in data) ? String(data.requestRedirect) : null,
            loading: ('requestLoading' in data) ? String(data.requestLoading) : null,
            stripe: ('requestStripe' in data) ? data.requestStripe === 'true' : true,
            flash: ('requestFlash' in data),
            files: ('requestFiles' in data),
            browserValidate: ('requestBrowserValidate' in data),
            form: ('requestForm' in data) ? String(data.requestForm) : null,
            url: ('requestUrl' in data) ? String(data.requestUrl) : null,
            update: ('requestUpdate' in data) ? this.parseData(String(data.requestUpdate)) : [],
            data: ('requestData' in data) ? this.parseData(String(data.requestData)) : [],
        };

        this.snowboard.request(element, handler, options);
    }

    /**
     * Sets up an AJAX request via HTML attributes.
     *
     * @param {Request} request
     */
    onAjaxSetup(request) {
        if (!request.element) {
            return;
        }

        const fieldName = request.element.getAttribute('name');

        const data = {
            ...this.getParentRequestData(request.element),
            ...request.options.data,
        };

        if (request.element && request.element.matches('input, textarea, select, button') && !request.form && fieldName && !request.options.data[fieldName]) {
            data[fieldName] = request.element.value;
        }

        request.options.data = data;
    }

    /**
     * Parses and collates all data from elements up the DOM hierarchy.
     *
     * @param {Element} target
     * @returns {Object}
     */
    getParentRequestData(target) {
        const elements = [];
        let data = {};
        let currentElement = target;

        while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
            elements.push(currentElement.parentElement);
            currentElement = currentElement.parentElement;
        }

        elements.reverse();

        elements.forEach((element) => {
            const elementData = element.dataset;

            if ('requestData' in elementData) {
                data = {
                    ...data,
                    ...this.parseData(elementData.requestData),
                };
            }
        });

        return data;
    }

    /**
     * Parses data in the Winter/October JSON format.
     *
     * @param {String} data
     * @returns {Object}
     */
    parseData(data) {
        let value;

        if (data === undefined) {
            value = '';
        }
        if (typeof value === 'object') {
            return value;
        }

        try {
            return this.snowboard.jsonparser().parse(`{${data}}`);
        } catch (e) {
            throw new Error(`Error parsing the data attribute on element: ${e.message}`);
        }
    }

    trackInput(element) {
        const { lastValue } = element.dataset;
        const interval = element.dataset.trackInput || 300;

        if (lastValue !== undefined && lastValue === element.value) {
            return;
        }

        this.resetTrackInputTimer(element);

        element.dataset.inputTimer = window.setTimeout(() => {
            if (element.dataset.request) {
                this.processRequestOnElement(element);
                return;
            }

            // Traverse up the hierarchy and find a form that sends an AJAX query
            let currentElement = element;
            while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
                currentElement = currentElement.parentElement;

                if (currentElement.tagName === 'FORM' && currentElement.dataset.request) {
                    this.processRequestOnElement(currentElement);
                    break;
                }
            }
        }, interval);
    }

    resetTrackInputTimer(element) {
        if (element.dataset.inputTimer) {
            window.clearTimeout(element.dataset.inputTimer);
            element.dataset.inputTimer = null;
        }
    }
}
