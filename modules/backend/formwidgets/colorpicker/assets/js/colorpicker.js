/*
 * ColorPicker plugin
 *
 * Data attributes:
 * - data-control="colorpicker" - enables the plugin on an element
 * - data-data-locker="input#locker" - Input element to store and restore the chosen color
 *
 * JavaScript API:
 * $('div#someElement').colorPicker({ dataLocker: 'input#locker' })
 *
 * Dependences:
 * - Pickr (https://github.com/Simonwep/pickr)
 */

+function ($) { "use strict";

    // COLORPICKER CLASS DEFINITION
    // ============================

    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    var ColorPicker = function(element, options) {
        this.options   = options
        this.$el       = $(element)

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)

        // Init
        this.init()
    }

    ColorPicker.DEFAULTS = {
        showAlpha: false,
        format: 'hexa',
        allowEmpty: false,
        dataLocker: null,
        disabled: false
    }

    ColorPicker.prototype = Object.create(BaseProto)
    ColorPicker.prototype.constructor = ColorPicker

    /**
     * Initialise the widget.
     */
    ColorPicker.prototype.init = function() {
        this.$dataLocker = $(this.options.dataLocker, this.$el)
        this.$container = $('.colorpicker-container', this.$el)
        this.$colorPreview = $('[data-color-preview]', this.$el)
        this.$colorValue = $('[data-color-value]', this.$el)
        this.keyboardEntry = false
        this.originalColor = null

        // Create a Pickr instance
        this.pickr = Pickr.create({
            el: this.$colorPreview.get(0),
            theme: 'nano',
            disabled: this.options.disabled,
            swatches: this.options.availableColors,
            lockOpacity: !this.options.showAlpha,
            useAsButton: true,
            container: this.$el.get(0),
            comparison: true,
            showAlways: true,
            position: 'top-middle',
            components: {
                palette: this.options.allowCustom,
                preview: this.options.allowCustom,
                hue: this.options.allowCustom,
                opacity: this.options.showAlpha,
                interaction: {
                    input: false,
                    cancel: false,
                    clear: this.options.allowEmpty,
                    save: false,
                }
            },
            i18n: {
            },
        })

        // Set up events on various elements
        this.$colorValue.on('focus', this.proxy(this.onFocus))
        this.$colorValue.on('blur', this.proxy(this.onBlur))
        this.$colorValue.on('keyup', this.proxy(this.onEntry))
        this.$colorPreview.on('click', this.proxy(this.onColorClick))
        this.pickr.on('init', () => this.onPickerInit())
        this.pickr.on('change', (hsva) => this.onPickerChange(hsva))
        this.pickr.on('changestop', () => this.onPickerStopChange())
        this.pickr.on('swatchselect', (hsva) => this.onPickerSwatch(hsva))
        this.pickr.on('cancel', () => this.onPickerStopChange())
        this.pickr.on('hide', () => this.onPickerHide())
    }

    /**
     * Disposes the element.
     */
    ColorPicker.prototype.dispose = function () {
        this.$colorValue.off('focus', this.proxy(this.onFocus))
        this.$colorValue.off('blur', this.proxy(this.onBlur))
        this.$colorValue.off('keyup', this.proxy(this.onEntry))
        this.$colorPreview.off('click', this.proxy(this.onColorClick))

        if (this.pickr) {
            this.pickr.destroyAndRemove()
        }

        this.$el = null
        BaseProto.dispose.call(this)
    }

    /**
     * Show picker when focusing on text field for widget.
     */
    ColorPicker.prototype.onFocus = function () {
        this.showPicker()
    }

    /**
     * Show picker when the color preview next to the text field is clicked.
     * @param {Event} event
     */
    ColorPicker.prototype.onColorClick = function (event) {
        if ($(event.currentTarget).is(this.$colorValue) === false) {
            this.$colorValue.focus()
        }
        this.showPicker()
    }

    /**
     * Hide picker when the text field loses focus.
     */
    ColorPicker.prototype.onBlur = function () {
        this.hidePicker()
    }

    /**
     * Fired when a key is pressed while in the picker, or within the text field of the widget.
     * @param {Event} event
     * @returns null
     */
    ColorPicker.prototype.onEntry = function (event) {
        // Escape always acts as a cancel
        if (event.key === 'Escape') {
            this.keyboardEntry = false
            this.setColor(this.originalColor)
            this.hidePicker()
            this.$colorValue.blur()
            return
        }

        if (this.pickr.setColor(this.$colorValue.val())) {
            this.keyboardEntry = true
        } else {
            this.keyboardEntry = false
        }
    }

    /*
     * PICKER METHODS
     */

    /**
     * Shows the picker.
     *
     * This also prevents mouse clicks within the picker from making the text field lose focus.
     */
    ColorPicker.prototype.showPicker = function () {
        this.keyboardEntry = false
        this.originalColor = this.pickr.getColor().clone()
        this.pickr.show()

        $(this.pickr.getRoot().app).on('mousedown.pickr.overrideBlur', function (event) {
            // Prevent blur event of the text field firing if the mouse click started inside picker,
            // even if it ends up outside. This prevents the whitespace in the picker firing a blur
            // event and hiding the picker.
            event.preventDefault()
            event.stopPropagation()
        })
    }

    /**
     * Hides the picker.
     */
    ColorPicker.prototype.hidePicker = function () {
        $(this.pickr.getRoot().app).off('mousedown.pickr.overrideBlur')
        this.pickr.hide()
    }

    /**
     * Fires when the picker is first initialized for a widget.
     */
    ColorPicker.prototype.onPickerInit = function () {
        this.pickr.setColor(this.$dataLocker.val())

        switch (this.options.format) {
            case 'rgb':
                this.pickr.setColorRepresentation('RGBA')
                break
            case 'hsl':
                this.pickr.setColorRepresentation('HSLA')
                break
            case 'cmyk':
                this.pickr.setColorRepresentation('CMYK')
                break
            case 'hex':
            default:
                this.pickr.setColorRepresentation('HEX')
                break
        }

        this.hidePicker()
        this.setColor(this.pickr.getColor())
    }

    /**
     * Updates the selected color when the user stops dragging on the palette.
     */
    ColorPicker.prototype.onPickerStopChange = function () {
        this.setColor(this.pickr.getColor())
        this.onPickerChange(this.pickr.getColor())
    }

    /**
     * Fires when the user drags the selector on the color palette, or the hue/opacity sliders.
     * @param {HSVaColor} hsva
     */
    ColorPicker.prototype.onPickerChange = function (hsva) {
        this.keyboardEntry = false
        $(this.pickr.getRoot().preview.currentColor).text(this.valueFromHSVA(hsva))

        // Set the color selection text to black or white depending on which color is picked
        if (this.isLightColor(hsva)) {
            $(this.pickr.getRoot().preview.currentColor).css({ color: '#000' })
        } else {
            $(this.pickr.getRoot().preview.currentColor).css({ color: '#fff' })
        }
    }

    /**
     * Fires when the user picks a swatch color.
     * @param {HSVaColor} hsva
     */
    ColorPicker.prototype.onPickerSwatch = function (hsva) {
        this.keyboardEntry = false
        this.setColor(hsva)
    }

    /**
     * Fires when the picker is hidden.
     */
    ColorPicker.prototype.onPickerHide = function () {
        if (this.keyboardEntry) {
            this.setColor(this.pickr.getColor())
        }
        this.pickr.setColor(this.$dataLocker.val())
        this.$colorValue.val(this.$dataLocker.val())
    }

    /**
     * Gets the necessary color value based on the selected format.
     *
     * @param {HSVaColor} hsva
     * @param {String} overrideFormat Overrides the format specified in the widget config. Can be one of "rgb", "hsl",
     *  "cmyk" and "hex".
     * @returns {String}
     */
    ColorPicker.prototype.valueFromHSVA = function (hsva, overrideFormat) {
        var format = overrideFormat || this.options.format

        switch (format) {
            case 'rgb':
                return hsva.toRGBA().toString(3)
            case 'hsl':
                return hsva.toHSLA().toString(3)
            case 'cmyk':
                return hsva.toCMYK().toString(3)
            case 'hex':
            default:
                return hsva.toHEXA().toString()
        }
    }

    /**
     * Sets the color value for the widget and updates the color preview.
     *
     * @param {HSVaColor} hsva
     */
    ColorPicker.prototype.setColor = function(hsva) {
        this.$colorPreview.css('background', this.valueFromHSVA(hsva, 'hex'))
        this.$colorValue.val(this.valueFromHSVA(hsva))
        this.$dataLocker.val(this.valueFromHSVA(hsva))
    }

    /**
     * Determines if the given HSVAColor is a "light" color.
     *
     * @param {HSVaColor} hsva
     * @returns {Boolean}
     */
    ColorPicker.prototype.isLightColor = function (hsva) {
        var rgba = hsva.toRGBA()

        // Borrowed from https://awik.io/determine-color-bright-dark-using-javascript/
        var ratio = Math.sqrt(
            0.299 * (rgba[0] * rgba[0]) +
            0.587 * (rgba[1] * rgba[1]) +
            0.114 * (rgba[2] * rgba[0])
        )

        return (ratio > 127.5)
    }

    // COLORPICKER PLUGIN DEFINITION
    // ============================

    var old = $.fn.colorPicker

    $.fn.colorPicker = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.colorpicker')
            var options = $.extend({}, ColorPicker.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.colorpicker', (data = new ColorPicker(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.colorPicker.Constructor = ColorPicker

    // COLORPICKER NO CONFLICT
    // =================

    $.fn.colorPicker.noConflict = function () {
        $.fn.colorPicker = old
        return this
    }

    // COLORPICKER DATA-API
    // ===============

    $(document).render(function() {
        $('[data-control="colorpicker"]').colorPicker()
    })

}(window.jQuery);
