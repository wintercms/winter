import '../../less/sensitive.less';

((Snowboard) => {
    /**
     * Sensitive form widget.
     *
     * Renders a text field that acts as a password or secret field, but can be revealed by the
     * user.
     *
     * @author Ben Thomson <git@alfreido.com>
     * @copyright 2023 Winter CMS
     */
    class Sensitive extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.config = this.snowboard.dataConfig(this, element);
            this.clean = Boolean(element.dataset.clean);
            this.hidden = true;

            // Child elements
            this.input = element.querySelector('[data-input]');
            this.toggle = element.querySelector('[data-toggle]');
            this.icon = element.querySelector('[data-icon]');
            this.loader = element.querySelector('[data-loader]');
            this.copy = element.querySelector('[data-copy]');

            // Events
            this.events = {
                input: () => this.onInput(),
                toggle: () => this.onToggle(),
                tabChange: () => this.onTabChange(),
                copy: () => this.onCopy(),
            };

            this.attachEvents();
        }

        /**
         * Sets the default options for this widget.
         *
         * Available options:
         *
         *  - `data-read-only` - If set, this field will be read-only, but revealable.
         *  - `data-disabled` - If set, this field will be disabled, and unrevealable.
         *  - `data-event-handler=""` - Defines the AJAX event handler that will provide the revealed value.
         *  - `data-hide-on-tab-change` - If set, this field will be hidden when the browser is switched
         *      to another tab or minimized.
         *
         * @returns {Object}
         */
        defaults() {
            return {
                readOnly: false,
                disabled: false,
                eventHandler: null,
                hideOnTabChange: false,
            };
        }

        /**
         * Attaches event listeners for several interactions.
         */
        attachEvents() {
            this.input.addEventListener('keydown', this.events.input);
            this.toggle.addEventListener('click', this.events.toggle);

            if (this.config.get('hideOnTabChange')) {
                // Watch for tab change or minimise
                document.addEventListener('visibilitychange', this.events.tabChange);
            }

            if (this.copy) {
                this.copy.addEventListener('click', this.events.copy);
            }
        }

        /**
         * Destructor.
         */
        destruct() {
            this.input.removeEventListener('keydown', this.events.input);
            this.toggle.removeEventListener('click', this.events.toggle);

            if (this.config.get('hideOnTabChange')) {
                // Watch for tab change or minimise
                document.removeEventListener('visibilitychange', this.events.tabChange);
            }

            if (this.copy) {
                this.copy.removeEventListener('click', this.events.copy);
            }

            this.input = null;
            this.toggle = null;
            this.icon = null;
            this.loader = null;

            super.destruct();
        }

        /**
         * Input handler.
         *
         * Resets a clean field to empty if the user types anything in the field without revealing it.
         */
        onInput() {
            if (this.clean) {
                this.clean = false;
                this.input.value = '';
            }
        }

        /**
         * Toggle handler.
         *
         * Reveals the value and toggles the visibility of a revealed value.
         */
        onToggle() {
            if (this.input.value !== '' && this.clean) {
                this.reveal();
            } else {
                this.toggleVisibility();
            }
        }

        /**
         * Tab change handler.
         *
         * Fires when the browser is minimized or switched to another tab. This will hide the value.
         */
        onTabChange() {
            if (document.hidden && !this.hidden) {
                this.toggleVisibility();
            }
        }

        /**
         * Copy handler.
         *
         * Copies the value to the clipboard.
         */
        onCopy() {
            const promise = new Promise((resolve, reject) => {
                if (this.input.value !== '' && this.clean) {
                    this.reveal(resolve).then(
                        () => resolve(),
                        () => reject(),
                    );
                } else {
                    resolve();
                }
            });

            promise.then(
                () => {
                    const isHidden = this.hidden;

                    if (this.hidden) {
                        this.toggleVisibility();
                    }

                    this.input.focus();
                    this.input.select();

                    try {
                        const blob = new Blob([this.input.value], { type: 'text/plain' });
                        const item = new ClipboardItem({ 'text/plain': blob });
                        navigator.clipboard.write([item]);
                    } catch (error) {
                        this.snowboard.error(`Clipboard API not supported - ${error}`);
                    }

                    this.input.blur();
                    if (isHidden) {
                        this.toggleVisibility();
                    }
                },
                (error) => {
                    this.snowboard.error(`Unable to retrieve hidden value - ${error}`);
                },
            );
        }

        /**
         * Toggles the visibility of the value within the sensitive field.
         */
        toggleVisibility() {
            this.input.setAttribute('type', (this.hidden) ? 'text' : 'password');
            this.icon.classList.toggle('icon-eye');
            this.icon.classList.toggle('icon-eye-slash');
            this.hidden = !this.hidden;
        }

        /**
         * Reveals the value of the sensitive field.
         *
         * This will call an AJAX event handler to retrieve the value, allowing for values to be
         * manipulated or controlled by the server.
         *
         * @returns {Promise}
         */
        reveal() {
            return new Promise((resolve, reject) => {
                // Show loader
                this.icon.style.visibility = 'hidden';
                this.loader.classList.remove('hide');

                this.snowboard.request(this.input, this.config.get('eventHandler'), {
                    success: (data) => {
                        this.input.value = data.value;
                        this.clean = false;

                        // Hide loader and reveal
                        this.icon.style.visibility = 'visible';
                        this.loader.classList.add('hide');
                        this.toggleVisibility();
                        resolve();
                    },
                    error: (error) => {
                        reject(new Error(error));
                    },
                });
            });
        }
    }

    Snowboard.addPlugin('backend.formwidget.sensitive', Sensitive);
    Snowboard['backend.ui.widgethandler']().register('sensitive', 'backend.formwidget.sensitive');
})(window.Snowboard);
