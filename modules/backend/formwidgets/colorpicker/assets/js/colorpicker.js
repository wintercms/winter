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
        allowEmpty: false,
        dataLocker: null,
        disabled: false
    }

    ColorPicker.prototype = Object.create(BaseProto)
    ColorPicker.prototype.constructor = ColorPicker

    ColorPicker.prototype.init = function() {
        this.$dataLocker  = $(this.options.dataLocker, this.$el)
        this.$colorList = $('> ul', this.$el)
        this.$customColor = $('[data-custom-color]', this.$el)
        this.$customColorSpan = $('> span', this.$customColor)
        this.originalColor = this.$customColor.data('hexColor')
        this.pickr = null

        if (!this.options.disabled) {
            this.$colorList.on('click', '> li', (event) => {
                this.selectColor($(event.currentTarget))
                this.$dataLocker.trigger('change')
            })
        }

        /*
         * Custom color
         */
        if (this.$customColor.length) {
            this.pickr = Pickr.create({
                el: this.$customColor.get(0),
                theme: 'nano',
                disabled: this.options.disabled,
                swatches: [],
                lockOpacity: !this.options.showAlpha,
                useAsButton: true,
                container: this.$customColor.parent().get(0),
                comparison: false,
                components: {
                    palette: false,
                    preview: true,
                    opacity: this.options.showAlpha,
                    hue: true,
                    interaction: {
                        input: true,
                        cancel: true,
                        clear: this.options.allowEmpty,
                        save: true,
                    }
                },
                i18n: {
                    'btn:save': $.wn.lang.get('colorpicker.choose', 'Ok'),
                    'btn:cancel': $.wn.lang.get('colorpicker.close', 'Close'),
                },
            })

            this.$customColor.on('click', this.proxy(this.showPicker))
            this.pickr.on('show', () => this.onShow())
            this.pickr.on('change', (hsva) => this.onChange(hsva))
            this.pickr.on('save', (hsva) => this.updateColor)
            this.pickr.on('cancel', () => this.onCancel)

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

    ColorPicker.prototype.onShow = function () {
        this.pickr.setColor(this.$customColorSpan.css('background-color'))
        this.pickr.setColorRepresentation('HEX')
    }

    ColorPicker.prototype.onChange = function (hsva) {
        this.$customColorSpan.css('background', hsva.toHEXA())
    }

    ColorPicker.prototype.setCustomColor = function(hexColor) {
        if (this.$customColor.length) {
            this.$customColor.data('hexColor', hexColor)
            this.$customColor.spectrum('set', hexColor)
        }

        this.setColor(hexColor)
    }

    ColorPicker.prototype.setColor = function(hexColor) {
        this.$dataLocker.val(hexColor)
    }

    ColorPicker.prototype.selectColor = function(el) {
        var $item = $(el)

        $item
            .addClass('active')
            .siblings().removeClass('active')

        this.setColor($item.data('hexColor'))

        if($item.data('hexColor').length > 0) {
            $item.addClass('sp-clear-display')
        }
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
