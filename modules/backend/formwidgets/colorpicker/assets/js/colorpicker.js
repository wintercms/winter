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

    ColorPicker.prototype.init = function() {
        this.$dataLocker = $(this.options.dataLocker, this.$el)
        this.$container = $('.colorpicker-container', this.$el)
        this.$colorPreview = $('[data-color-preview]', this.$el)
        this.$colorValue = $('[data-color-value]', this.$el)

        this.pickr = Pickr.create({
            el: this.$colorPreview.get(0),
            theme: 'nano',
            disabled: this.options.disabled,
            swatches: this.options.availableColors,
            lockOpacity: !this.options.showAlpha,
            useAsButton: true,
            container: this.$el.get(0),
            comparison: false,
            showAlways: true,
            position: 'top-middle',
            components: {
                palette: this.options.allowCustom,
                preview: false,
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

        this.$colorValue.on('focus', this.proxy(this.onFocus))
        this.$colorValue.on('blur', this.proxy(this.onBlur))
        this.$colorValue.on('keypress', this.proxy(this.onEntry))
        this.$colorPreview.on('click', this.proxy(this.onColorClick))
        this.pickr.on('init', () => this.onPickerInit())
        this.pickr.on('change', (hsva) => this.onPickerChange(hsva))
        this.pickr.on('save', (hsva) => this.updateColor)
        this.pickr.on('cancel', () => this.onPickerCancel())
        this.pickr.on('hide', () => this.onPickerHide())
    }

    ColorPicker.prototype.dispose = function () {
        this.$customColor.off('click', this.proxy(this.showPicker))

        if (this.pickr) {
            this.pickr.destroyAndRemove()
        }

        this.$el = null
        BaseProto.dispose.call(this)
    }

    ColorPicker.prototype.onFocus = function () {
        this.showPicker()
    }

    ColorPicker.prototype.onColorClick = function (event) {
        if ($(event.currentTarget).is(this.$colorValue) === false) {
            this.$colorValue.focus()
        }
        this.showPicker()
    }

    ColorPicker.prototype.onBlur = function () {
        this.hidePicker()
    }

    ColorPicker.prototype.onEntry = function (event) {
        // this.pickr.
    }

    /**
     * PICKER METHODS
     */

    ColorPicker.prototype.showPicker = function () {
        this.pickr.show()

        $(this.pickr.getRoot().app).on('mousedown.pickr.overrideBlur', function (event) {
            // Prevent blur event of the text field firing if the mouse click started inside picker,
            // even if it ends up outside. This prevents the whitespace in the picker firing a blur
            // event and hiding the picker.
            event.preventDefault()
            event.stopPropagation()
        });
    }

    ColorPicker.prototype.hidePicker = function () {
        $(this.pickr.getRoot().app).off('mousedown.pickr.overrideBlur')
        this.pickr.hide()
    }

    ColorPicker.prototype.onPickerInit = function () {
        this.pickr.setColor(this.$dataLocker.val())

        switch (this.options.format) {
            case 'rgb':
                this.pickr.setColorRepresentation('RGBA')
                break;
            case 'hsl':
                this.pickr.setColorRepresentation('HSLA')
                break;
            case 'cmyk':
                this.pickr.setColorRepresentation('CMYK')
                break;
            case 'hex':
            default:
                this.pickr.setColorRepresentation('HEX')
                break;
        }

        this.hidePicker()
    }

    ColorPicker.prototype.onPickerChange = function (hsva) {
        this.$colorPreview.css('background', this.valueFromHSVA(hsva, 'hex'))
        this.$colorValue.val(this.valueFromHSVA(hsva))
        this.$dataLocker.val(this.valueFromHSVA(hsva))
    }

    ColorPicker.prototype.onPickerCancel = function () {
        this.hidePicker()
    }

    ColorPicker.prototype.onPickerHide = function () {
        this.pickr.setColor(this.$dataLocker.val())
        this.$colorValue.val(this.$dataLocker.val())
    }

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

    ColorPicker.prototype.setColor = function(hexColor) {
        this.$dataLocker.val(hexColor)
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
