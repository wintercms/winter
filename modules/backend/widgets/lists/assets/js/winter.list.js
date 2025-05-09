/*
 * List Widget
 *
 * Dependences:
 * - Row Link Plugin (system/assets/ui/js/list.rowlink.js)
 */
+function ($) { "use strict";

    var ListWidget = function (element, options) {

        var $el = this.$el = $(element);

        this.options = options || {};

        var scrollClassContainer = options.scrollClassContainer !== undefined
            ? options.scrollClassContainer
            : $el.parent()

        $el.dragScroll({
            scrollClassContainer: scrollClassContainer,
            scrollSelector: 'thead',
            dragSelector: 'thead'
        })

        this.update()
    }

    ListWidget.DEFAULTS = {
    }

    ListWidget.prototype.update = function() {
        var
            list = this.$el,
            head = $('thead', list),
            body = $('tbody', list),
            foot = $('tfoot', list)

        /*
         * Bind check boxes
         */
        $('.list-checkbox input[type="checkbox"]', body).each(function(){
            var $el = $(this)
            if ($el.is(':checked'))
                $el.closest('tr').addClass('active')
        })

        head.on('change', '.list-checkbox input[type="checkbox"]', function(){
            var $el = $(this),
                checked = $el.is(':checked')

            $('.list-checkbox input[type="checkbox"]', body).prop('checked', checked)
            if (checked)
                $('tr', body).addClass('active')
            else
                $('tr', body).removeClass('active')
        })

        body.on('change', '.list-checkbox input[type="checkbox"]', function(){
            var $el = $(this),
                checked = $el.is(':checked')

            if (checked) {
                $el.closest('tr').addClass('active')
            }
            else {
                $('.list-checkbox input[type="checkbox"]', head).prop('checked', false)
                $el.closest('tr').removeClass('active')
            }
        })

        this.lastChecked = null

        body.on('click', '.list-checkbox input[type="checkbox"]', (e) => {
            const current = e.currentTarget

            if (this.lastChecked && e.shiftKey) {
                const checkboxes = $('.list-checkbox input[type="checkbox"]', body)
                const start = checkboxes.index(current)
                const end = checkboxes.index(this.lastChecked)

                checkboxes
                    .slice(Math.min(start, end), Math.max(start, end) + 1)
                    .each(function () {
                        $(this).prop('checked', current.checked).trigger('change')
                    })
            }

            this.lastChecked = current
        })
    }

    ListWidget.prototype.getChecked = function() {
        var
            list = this.$el,
            body = $('tbody', list)

        return  $('.list-checkbox input[type="checkbox"]', body).map(function(){
            var $el = $(this)
            if ($el.is(':checked'))
                return $el.val()
        }).get();
    }

    ListWidget.prototype.toggleChecked = function(el) {
        var $checkbox = $('.list-checkbox input[type="checkbox"]', $(el).closest('tr'))
        $checkbox.prop('checked', !$checkbox.is(':checked')).trigger('change')
    }

    // LIST WIDGET PLUGIN DEFINITION
    // ============================

    var old = $.fn.listWidget

    $.fn.listWidget = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result

        this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.listwidget')
            var options = $.extend({}, ListWidget.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.listwidget', (data = new ListWidget(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
      }

    $.fn.listWidget.Constructor = ListWidget

    // LIST WIDGET NO CONFLICT
    // =================

    $.fn.listWidget.noConflict = function () {
        $.fn.listWidget = old
        return this
    }

    // LIST WIDGET HELPERS
    // =================

     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    $.wn.listToggleChecked = function(el) {
        $(el)
            .closest('[data-control="listwidget"]')
            .listWidget('toggleChecked', el)
    }

    $.wn.listGetChecked = function(el) {
        return $(el)
            .closest('[data-control="listwidget"]')
            .listWidget('getChecked')
    }

    // LIST WIDGET DATA-API
    // ==============

    $(document).render(function(){
        $('[data-control="listwidget"]').listWidget();
    })

}(window.jQuery);
