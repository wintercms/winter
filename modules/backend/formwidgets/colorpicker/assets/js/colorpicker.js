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
        this.$colorPreview = $('[data-color-preview]', this.$el)
        this.$colorValue = $('[data-color-value]', this.$el)

        this.pickr = Pickr.create({
            el: this.$colorPreview.get(0),
            theme: 'nano',
            disabled: this.options.disabled,
            swatches: [],
            lockOpacity: !this.options.showAlpha,
            useAsButton: true,
            container: this.$el.get(0),
            comparison: false,
            components: {
                palette: false,
                preview: true,
                opacity: this.options.showAlpha,
                hue: true,
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
        this.pickr.hide()

        this.$colorValue.on('focus', this.proxy(this.showPicker))
        this.$colorValue.on('click', this.proxy(this.showPicker))
        this.$colorPreview.on('click', this.proxy(this.showPicker))
        this.pickr.on('init', () => this.onInit())
        this.pickr.on('change', (hsva) => this.onChange(hsva))
        this.pickr.on('save', (hsva) => this.updateColor)
        this.pickr.on('cancel', () => this.onCancel())
        this.pickr.on('hide', () => this.onHide())

        /*this.$customColor.spectrum({
            preferredFormat: 'hex',
            showInput: true,
            showAlpha: this.options.showAlpha,
            allowEmpty: this.options.allowEmpty,
            color: this.$customColor.data('hexColor'),
            chooseText: $.wn.lang.get('colorpicker.choose', 'Ok'),
            cancelText: '⨯',
            appendTo: 'parent',
            disabled: this.options.disabled,
            hide: function(color) {
                var hex = color ? color.toHexString() : ''
                self.$customColorSpan.css('background', hex)
            },
            show: function(color) {
                self.selectColor(self.$customColor)
            },
            move: function(color) {
                var hex = color ? color.toHexString() : ''
                self.$customColorSpan.css('background', hex)
            },
            change: function(color) {
                var hex = color ? color.toHexString() : ''
                self.setCustomColor(hex)
            }
        })*/
    }

    ColorPicker.prototype.dispose = function () {
        this.$customColor.off('click', this.proxy(this.showPicker))

        if (this.pickr) {
            this.pickr.destroyAndRemove()
        }

        this.$el = null
        BaseProto.dispose.call(this)
    }

    ColorPicker.prototype.showPicker = function () {
        this.pickr.show()
    }

    ColorPicker.prototype.onInit = function () {
        this.pickr.setColor(this.$dataLocker.val())
        this.pickr.setColorRepresentation('HEX')
    }

    ColorPicker.prototype.onChange = function (hsva) {
        this.$colorPreview.css('background', this.valueFromHSVA(hsva, 'hexa'))
        this.$colorValue.val(this.valueFromHSVA(hsva))
    }

    ColorPicker.prototype.onCancel = function () {
        this.pickr.hide()
    }

    ColorPicker.prototype.onHide = function () {
        this.pickr.setColor(this.$dataLocker.val())
        this.$colorValue.val(this.$dataLocker.val())
    }

    ColorPicker.prototype.valueFromHSVA = function (hsva, overrideFormat) {
        var format = overrideFormat || this.options.format

        switch (format) {
            case 'hex':
            case 'hexa':
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
