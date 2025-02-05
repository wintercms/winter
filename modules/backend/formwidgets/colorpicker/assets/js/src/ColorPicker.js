import Pickr from '@simonwep/pickr';
import '@simonwep/pickr/dist/themes/nano.min.css';
import '../../less/colorpicker.less';

((Snowboard, $) => {
    /**
     * Color picker widget.
     *
     * The color picker widget allows for easy colour selection from a color swatches, or a custom
     * color from a palette. The colour can be returned in various formats.
     *
     * @author Ben Thomson <git@alfreido.com>
     * @copyright 2023 Winter CMS
     */
    class ColorPicker extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.pickr = null;
            this.config = this.snowboard.dataConfig(this, element);

            if (typeof this.config.get('formats') === 'string') {
                this.config.set('formats', [this.config.get('formats')]);
            }

            // Child elements
            this.dataLocker = element.querySelector(this.config.get('dataLocker'));
            this.container = element.querySelector('.colorpicker-container');
            this.colorPreview = element.querySelector('[data-color-preview]');
            this.colorValue = element.querySelector('[data-color-value]');

            // User inputs
            this.keyboardEntry = false;
            this.originalColor = null;
            this.originalFormat = null;
            this.formatSet = false;

            // Events
            this.events = {
                focus: () => this.onFocus(),
                blur: () => this.onBlur(),
                keydown: (event) => this.onKeyDown(event),
                keyup: (event) => this.onKeyUp(event),
                colorClick: (event) => this.onColorClick(event),
                pickrInit: () => this.onPickerInit(),
                pickrChange: (hsva) => this.onPickerChange(hsva),
                pickrStopChange: () => this.onPickerStopChange(),
                pickrSwatch: (hsva) => this.onPickerSwatch(hsva),
                pickrCancel: () => this.onPickerStopChange(),
                pickrHide: () => this.onPickerHide(),
                pickrClear: () => this.onPickerClear(),
                stop: (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                },
            };

            this.createPickr();
            this.attachEvents();
        }

        /**
         * Sets the default options for this widget.
         *
         * Available options:
         *
         * - `data-allow-custom`: If set, allows custom colors to be picked or entered, outside
         *      of the available colors.
         * - `data-allow-empty`: If set, allows the color to be cleared.
         * - `data-available-colors=""`: An array of colors to be used as swatches.
         * - `data-data-locker=""`: A selector for the element that will be used to contain the selected color value.
         * - `data-disabled`: If set, disables the color picker.
         * - `data-formats=""`: The format to use for the color value. Can be `hex`, `rgb`, `hsl`, or `cmyk`.
         * - `data-show-alpha`: If set, shows the alpha channel.
         *
         * @returns {Object}
         */
        defaults() {
            return {
                allowCustom: false,
                allowEmpty: false,
                availableColors: [],
                dataLocker: null,
                disabled: false,
                formats: 'hex',
                showAlpha: false,
            };
        }

        /**
         * Create a Pickr instance.
         */
        createPickr() {
            this.pickr = Pickr.create({
                el: this.colorPreview,
                theme: 'nano',
                disabled: this.config.get('disabled'),
                swatches: this.config.get('availableColors'),
                lockOpacity: !this.config.get('showAlpha'),
                useAsButton: true,
                container: this.element,
                comparison: true,
                showAlways: true,
                position: 'top-middle',
                components: {
                    palette: this.config.get('allowCustom'),
                    preview: this.config.get('allowCustom'),
                    hue: this.config.get('allowCustom'),
                    opacity: this.config.get('showAlpha'),
                    interaction: {
                        hex: (this.config.get('formats').length > 1 && this.config.get('formats').includes('hex')),
                        rgba: (this.config.get('formats').length > 1 && this.config.get('formats').includes('rgb')),
                        hsla: (this.config.get('formats').length > 1 && this.config.get('formats').includes('hsl')),
                        cmyk: (this.config.get('formats').length > 1 && this.config.get('formats').includes('cmyk')),

                        input: false,
                        cancel: false,
                        clear: this.config.get('allowEmpty'),
                        save: false,
                    },
                },
                i18n: {
                    'btn:last-color': $.wn.lang.get('colorpicker.last_color', 'Use previously selected color'),
                    'aria:palette': $.wn.lang.get('colorpicker.aria_palette', 'Color selection area'),
                    'aria:hue': $.wn.lang.get('colorpicker.aria_hue', 'Hue selection slider'),
                    'aria:opacity': $.wn.lang.get('colorpicker.aria_opacity', 'Opacity selection slider'),
                },
            });
        }

        /**
         * Attaches event listeners for several interactions.
         */
        attachEvents() {
            this.colorValue.addEventListener('focus', this.events.focus);
            this.colorValue.addEventListener('blur', this.events.blur);
            this.colorValue.addEventListener('keydown', this.events.keydown);
            this.colorValue.addEventListener('keyup', this.events.keyup);

            this.colorPreview.addEventListener('click', this.events.colorClick);

            this.pickr.on('init', this.events.pickrInit);
            this.pickr.on('change', this.events.pickrChange);
            this.pickr.on('changestop', this.events.pickrStopChange);
            this.pickr.on('swatchselect', this.events.pickrSwatch);
            this.pickr.on('cancel', this.events.pickrCancel);
            this.pickr.on('hide', this.events.pickrHide);
            this.pickr.on('clear', this.events.pickrClear);
        }

        /**
         * Destructor.
         */
        destruct() {
            this.colorValue.removeEventListener('focus', this.events.focus);
            this.colorValue.removeEventListener('blur', this.events.blur);
            this.colorValue.removeEventListener('keydown', this.events.keydown);
            this.colorValue.removeEventListener('keyup', this.events.keyup);

            this.colorPreview.removeEventListener('click', this.events.colorClick);

            this.pickr.off('init', this.events.pickrInit);
            this.pickr.off('change', this.events.pickrChange);
            this.pickr.off('changestop', this.events.pickrStopChange);
            this.pickr.off('swatchselect', this.events.pickrSwatch);
            this.pickr.off('cancel', this.events.pickrCancel);
            this.pickr.off('hide', this.events.pickrHide);
            this.pickr.off('clear', this.events.pickrClear);

            this.pickr.destroyAndRemove();

            this.dataLocker = null;
            this.container = null;
            this.colorPreview = null;
            this.colorValue = null;
            this.config = null;

            super.destruct();
        }

        /**
         * Show picker when focusing on text field for widget.
         */
        onFocus() {
            this.showPicker();
        }

        /**
         * Show picker when the color preview next to the text field is clicked.
         *
         * @param {Event} event
         */
        onColorClick(event) {
            if (event.currentTarget !== this.colorValue) {
                this.colorValue.focus();
            }
            this.showPicker();
        }

        /**
         * Hide picker when the text field loses focus.
         */
        onBlur() {
            this.hidePicker();
        }

        /**
         * Fired when a key is pressed down.
         *
         * We use this to disable the Enter key submitting the form by mistake.
         *
         * @param {Event} event
         */
        onKeyDown(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        }

        /**
         * Fired when a key is pressed while in the picker, or within the text field of the widget.
         *
         * @param {Event} event
         */
        onKeyUp(event) {
            // Escape always acts as a cancel
            if (event.key === 'Escape') {
                this.keyboardEntry = false;
                this.setColor(this.originalColor);
                this.hidePicker();
                this.colorValue.blur();
                event.stopPropagation();
                return;
            }

            // Enter will select the current color and hide the picker
            if (event.key === 'Enter') {
                this.setColor(this.pickr.getColor());
                this.hidePicker();
                this.colorValue.blur();
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            this.keyboardEntry = (
                (this.colorValue.value === '' && this.config.get('allowEmpty'))
                || this.pickr.setColor(this.colorValue.value)
            );
        }

        /**
         * Shows the color picker.
         *
         * This also prevents mouse clicks within the picker from making the text field lose focus.
         */
        showPicker() {
            this.keyboardEntry = false;
            this.originalColor = this.pickr.getColor().clone();
            this.originalFormat = this.getCurrentFormat();
            this.pickr.show();

            // Prevent blur event of the text field firing if the mouse click started inside picker,
            // even if it ends up outside. This prevents the whitespace in the picker firing a blur
            // event and hiding the picker.
            this.pickr.getRoot().app.addEventListener('mousedown', this.events.stop);
        }

        /**
         * Hides the picker.
         */
        hidePicker() {
            this.pickr.getRoot().app.removeEventListener('mousedown', this.events.stop);
            this.pickr.hide();
        }

        /**
         * Fires when the picker is first initialized for a widget.
         */
        onPickerInit() {
            if (this.dataLocker.value) {
                this.pickr.setColor(this.dataLocker.value);
            }

            this.hidePicker();

            if (this.dataLocker.value) {
                if (this.config.get('formats').length === 1) {
                    this.setColorFormat(this.config.get('formats')[0]);
                }

                this.setColor(this.pickr.getColor());
            }
        }

        /**
         * Fires when the user drags the selector on the color palette, or the hue/opacity sliders.
         * @param {HSVaColor} hsva
         */
        onPickerChange(hsva) {
            this.keyboardEntry = false;

            if (!this.formatSet && this.config.get('formats').length === 1) {
                this.setColorFormat(this.config.get('formats')[0]);
            }

            this.pickr.getRoot().preview.currentColor.innerText = this.valueFromHSVA(hsva);

            // If the format changes, change the value
            if (this.getCurrentFormat() !== this.originalFormat) {
                this.setColor(hsva);
                this.originalFormat = this.getCurrentFormat();
            }

            // Set the color selection text to black or white depending on which color is picked
            if (this.isLightColor(hsva)) {
                this.pickr.getRoot().preview.currentColor.style.color = '#000';
            } else {
                this.pickr.getRoot().preview.currentColor.style.color = '#fff';
            }
        }

        /**
         * Updates the selected color when the user stops dragging on the palette.
         */
        onPickerStopChange() {
            this.setColor(this.pickr.getColor());
            if (this.config.get('formats').length === 1) {
                this.setColorFormat(this.config.get('formats')[0]);
            }
            this.onPickerChange(this.pickr.getColor());
        }

        /**
         * Fires when the user picks a swatch color.
         *
         * @param {HSVaColor} hsva
         */
        onPickerSwatch(hsva) {
            this.keyboardEntry = false;
            this.setColor(hsva);

            if (this.config.get('formats').length === 1) {
                this.setColorFormat(this.config.get('formats')[0]);
            }
        }

        /**
         * Fires when the picker is hidden.
         */
        onPickerHide() {
            if (this.keyboardEntry) {
                if (this.colorValue.value === '' && this.config.get('allowEmpty')) {
                    this.setColor();
                } else {
                    this.setColor(this.pickr.getColor());

                    if (this.config.get('formats').length === 1) {
                        this.setColorFormat(this.config.get('formats')[0]);
                    }
                }
            }

            if (this.dataLocker.value === '') {
                this.pickr.setColor(null);
            } else {
                this.pickr.setColor(this.dataLocker.value);
            }
            this.colorValue.value = this.dataLocker.value;

            if (
                this.originalColor !== null
                && this.valueFromHSVA(this.pickr.getColor()) !== this.valueFromHSVA(this.originalColor)
            ) {
                const event = new Event('change');
                event.color = this.colorValue.value;
                this.element.dispatchEvent(event);
            }
        }

        /**
         * Fires when the picker is cleared.
         */
        onPickerClear() {
            this.setColor();
            this.hidePicker();
            this.colorValue.blur();
        }

        /**
         * Gets the necessary color value based on the selected format.
         *
         * @param {HSVaColor} hsva
         * @param {String} overrideFormat Overrides the format specified in the widget config. Can be one of "rgb", "hsl",
         *  "cmyk" and "hex".
         * @returns {String}
         */
        valueFromHSVA(hsva, overrideFormat) {
            const format = overrideFormat || this.getCurrentFormat();

            switch (format) {
                case 'rgb':
                    return hsva.toRGBA().toString(1);
                case 'hsl':
                    return hsva.toHSLA().toString(1);
                case 'cmyk':
                    return hsva.toCMYK().toString(1);
                case 'hex':
                default:
                    return hsva.toHEXA().toString();
            }
        }

        /**
         * Gets the current color representation from Pickr and translates it to our lowercase color format.
         *
         * @returns {String}
         */
        getCurrentFormat() {
            const currentFormat = this.pickr.getColorRepresentation();

            switch (currentFormat) {
                case 'RGBA':
                    return 'rgb';
                case 'HSLA':
                    return 'hsl';
                case 'CMYK':
                    return 'cmyk';
                case 'HEXA':
                default:
                    return 'hex';
            }
        }

        /**
         * Sets the color value for the widget and updates the color preview.
         *
         * @param {HSVaColor?} hsva
         */
        setColor(hsva) {
            if (hsva === undefined && !this.config.get('allowEmpty') && this.originalColor) {
                this.colorPreview.style.background = this.valueFromHSVA(this.originalColor, 'hex');
                this.colorValue.value = this.valueFromHSVA(this.originalColor);
                this.dataLocker.value = this.valueFromHSVA(this.originalColor);
            } else if (hsva === undefined) {
                this.colorPreview.style.background = '#fff';
                this.colorValue.value = '';
                this.dataLocker.value = '';
            } else {
                this.colorPreview.style.background = this.valueFromHSVA(hsva, 'hex');
                this.colorValue.value = this.valueFromHSVA(hsva);
                this.dataLocker.value = this.valueFromHSVA(hsva);
            }
        }

        /**
         * Sets the color format used for the value.
         *
         * @param {String} format One of "rgb", "hsl", "cmyk", "hex"
         */
        setColorFormat(format) {
            switch (format) {
                case 'rgb':
                    this.pickr.setColorRepresentation('RGBA');
                    break;
                case 'hsl':
                    this.pickr.setColorRepresentation('HSLA');
                    break;
                case 'cmyk':
                    this.pickr.setColorRepresentation('CMYK');
                    break;
                case 'hex':
                default:
                    this.pickr.setColorRepresentation('HEX');
                    break;
            }

            this.formatSet = true;
        }

        /**
         * Determines if the given HSVAColor is a "light" color.
         *
         * @param {HSVaColor} hsva
         * @returns {Boolean}
         */
        isLightColor(hsva) {
            const rgba = hsva.toRGBA();

            // Borrowed from https://awik.io/determine-color-bright-dark-using-javascript/
            const ratio = Math.sqrt(
                0.299 * (rgba[0] * rgba[0])
                + 0.587 * (rgba[1] * rgba[1])
                + 0.114 * (rgba[2] * rgba[2]),
            );

            // If alpha drops by 30%, then assume it is a light color
            if (rgba[3] < 0.7) {
                return true;
            }

            return (ratio > 127.5);
        }
    }

    Snowboard.addPlugin('backend.formwidget.colorpicker', ColorPicker);
    Snowboard['backend.ui.widgethandler']().register('colorpicker', 'backend.formwidget.colorpicker');
})(window.Snowboard, window.jQuery);
