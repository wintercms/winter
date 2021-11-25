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
export default class EnableDataRequest extends Singleton {
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
        return ['request'];
    }

    /**
     * Destructor.
     *
     * Detaches all handlers.
     */
    destructor() {
        this.detachHandlers();

        super.destructor();
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
                'select[data-request], input[type=radio][data-request], input[type=checkbox][data-request], input[type=file][data-request]'
            )) {
            return;
        }

        console.log(event);
    }

    /**
     * Handles clicks on hyperlinks and buttons.
     *
     * @param {Event} event
     */
    clickHandler(event) {
        // Check that we are clicking a valid element
        if (!event.target.matches(
                'a[data-request], button[data-request], input[type=button][data-request], input[type=submit][data-request]'
            )) {
            return;
        }

        event.preventDefault();

        console.log(event);
    }

    /**
     * Handles key presses on inputs
     *
     * @param {Event} event
     */
    keyDownHandler(event) {
        // Check that we are inputting into a valid element
        if (!event.target.matches(
                'input[type=text][data-request], input[type=submit][data-request], input[type=password][data-request], textarea[data-request]'
            )) {
            return;
        }

        console.log(event);
    }

    /**
     * Handles form submissions.
     *
     * @param {Event} event
     */
    submitHandler(event) {
        // Check that we are submitting a valid form
        if (!event.target.matches(
                'form[data-request]'
            )) {
            return;
        }

        event.preventDefault();

        this.processEventOnElement(event.target);
    }

    /**
     * Processes a request on a given element, using its data attributes.
     *
     * @param {HTMLElement} element
     */
    processEventOnElement(element) {
        const data = element.dataset;

        const handler = String(data.request);
        const options = {
            confirm: ('requestConfirm' in data) ? String(data.requestConfirm) : null,
            redirect: ('requestRedirect' in data) ? String(data.requestRedirect) : null,
            loading: ('requestLoading' in data) ? String(data.requestLoading) : null,
            flash: ('requestFlash' in data) ? Boolean(data.requestFlash) : null,
            files: ('requestFiles' in data) ? Boolean(data.requestFiles) : null,
            browserValidate: ('requestBrowserValidate' in data) ? Boolean(data.requestBrowserValidate) : null,
            form: ('requestForm' in data) ? String(data.requestForm) : null,
            url: ('requestUrl' in data) ? String(data.requestUrl) : null,
            update: ('requestUpdate' in data) ? this.parseData(String(data.requestUpdate)) : [],
            data: ('requestData' in data) ? this.parseData(String(data.requestData)) : [],
        };

        this.winter.request(element, handler, options);
    }

    ajaxSetup(request) {
        const fieldName = request.element.getAttribute('name');

        const data = Object.assign({}, this.getParentRequestData(request.element), request.options.data);

        if (request.element && request.element.matches('input, textarea, select, button') && !request.form && fieldName && !request.options.data[fieldName]) {
            data[fieldName] = request.element.value;
        }

        if (request.form) {
            const formData = new FormData(request.form);
            for (const entry of formData) {
                data[entry[0]] = entry[1];
            }
        }

        request.options.data = data;
    }

    getParentRequestData(element) {
        const elements = [];
        let data = {};
        let currentElement = element;

        while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
            elements.push(currentElement.parentElement);
            currentElement = currentElement.parentElement;
        }

        elements.reverse();

        elements.forEach((element) => {
            const elementData = element.dataset;

            if ('requestData' in elementData) {
                data = Object.assign({}, data, this.parseData(elementData.requestData));
            }
        });

        return data;
    }
}
