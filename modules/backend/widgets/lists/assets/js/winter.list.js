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
            self = this,
            list = this.$el,
            head = $('thead', list),
            body = $('tbody', list),
            foot = $('tfoot', list)

        /*
         * A list refresh replaces the contents of the outer .list-widget element, not the
         * element itself, so it is the only place selection state can outlive pagination.
         */
        this.$widget = list.closest('.list-widget')
        this.$banner = this.$widget.children('.list-selection')

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

            self.refreshSelection()
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

            self.refreshSelection()
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

        this.$banner.on('click', '.list-selection-select-all', function () {
            self.selectAllMatching()
        })

        this.$banner.on('click', '.list-selection-clear', function () {
            self.setAllRowsChecked(false)
        })

        this.restoreSelection()
    }

    /*
     * Re-enters "all matching" after a refresh, but only while the banner still describes the
     * same query: the fingerprint is rendered server-side from the active search and filters,
     * so a changed filter clears the selection without the client having to detect it.
     */
    ListWidget.prototype.restoreSelection = function() {
        var stored = this.$widget.data('listSelectAll')

        if (stored && this.$banner.length && stored === String(this.$banner.data('selectionFingerprint'))) {
            this.setAllRowsChecked(true)
            return
        }

        if (stored) {
            this.$widget.removeData('listSelectAll')
        }

        this.refreshSelection()

        /*
         * Nothing is selected in the newly rendered list, so any bulk button left enabled by
         * the previous page's selection has to be re-evaluated against this DOM.
         */
        $('[data-trigger]').filter(function () {
            return String($(this).data('trigger')).indexOf('.control-list') !== -1
        }).trigger('oc.triggerOn.update')
    }

    ListWidget.prototype.selectAllMatching = function() {
        this.$widget.data('listSelectAll', String(this.$banner.data('selectionFingerprint')))
        this.setAllRowsChecked(true)
    }

    ListWidget.prototype.setAllRowsChecked = function(checked) {
        $('.list-checkbox input[type="checkbox"]', $('tbody', this.$el))
            .prop('checked', checked)
            .trigger('change')

        // Reflect the row state in the header checkbox, which the row handler only ever clears.
        $('.list-checkbox input[type="checkbox"]', $('thead', this.$el)).prop('checked', checked)
    }

    /*
     * The banner offers "select all matching" only once the whole page is selected - a
     * partial selection is a deliberate choice - and a whole-query selection ends the moment
     * a row is unchecked, because it no longer describes what is selected.
     */
    ListWidget.prototype.refreshSelection = function() {
        if (!this.$banner.length) {
            return
        }

        var boxes = $('.list-checkbox input[type="checkbox"]', $('tbody', this.$el)),
            allChecked = boxes.length > 0 && boxes.filter(':checked').length === boxes.length

        if (!allChecked) {
            this.$widget.removeData('listSelectAll')
            this.$banner.removeClass('is-page is-all')
            return
        }

        this.$banner
            .toggleClass('is-all', !!this.$widget.data('listSelectAll'))
            .toggleClass('is-page', !this.$widget.data('listSelectAll'))
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

    /*
     * Returns what the user has selected: the visible checked ids, and whether the selection
     * is every record matching the current query. Bulk actions that post `checked` through
     * the framework's request pipeline get the whole-query fields injected for them (see the
     * ajaxSetup handler below); this is for callers that build a request themselves.
     */
    ListWidget.prototype.getSelection = function() {
        var fingerprint = this.$widget.data('listSelectAll')

        return {
            checked: this.getChecked(),
            all: fingerprint ? 1 : 0,
            fingerprint: fingerprint || null
        }
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

    $.wn.listGetSelection = function(el) {
        return $(el)
            .closest('[data-control="listwidget"]')
            .listWidget('getSelection')
    }

    // WHOLE-QUERY SELECTION BRIDGE
    // =================

    /*
     * Any bulk action that posts `checked` gains whole-query support without being touched:
     * the request carries the selection mode, and a handler that resolves it through the
     * selection API acts on every matching record. A handler still reading post('checked')
     * keeps acting on the page, exactly as before.
     */
    $(document).on('ajaxSetup', function(e, context) {
        var data = context.options.data

        if (!data || typeof data !== 'object' || !('checked' in data)) {
            return
        }

        /*
         * Drop any consumer left over from an earlier request by this element before deciding
         * anything else. A cancelled confirmation leaves one bound - ajaxPromise never fires -
         * and it must not act on whatever selection exists by the time this element is used
         * again.
         */
        var $trigger = $(e.target).off('ajaxPromise.listSelection')

        /*
         * The button that fires a bulk action lives in the toolbar, outside the list, so it
         * cannot be traced back to a widget. Only a list in "all matching" mode carries
         * state, and two of those sharing one button is not a real arrangement.
         */
        var $widget = $('.list-widget').filter(function() {
            return !!$(this).data('listSelectAll')
        }).first()

        if (!$widget.length) {
            return
        }

        data.checked_all = 1
        data.checked_fingerprint = $widget.data('listSelectAll')

        /*
         * Appended rather than replaced, so the author's wording survives and the note reads
         * correctly whatever the action is. A confirmed dialog re-runs this handler with
         * options.confirm already nulled, so the note cannot be appended twice.
         */
        if (context.options.confirm) {
            context.options.confirm += '\n\n' + $widget.children('.list-selection').data('confirmAll')
        }

        /*
         * Consume the selection when the request really goes out: ajaxPromise fires after the
         * confirmation gate, so a cancelled confirmation leaves the selection alone, and
         * before the response updates the DOM.
         *
         * Bound on the element rather than delegated from the document because the stripe
         * load indicator delegates ajaxPromise at the document and stops its propagation for
         * any [data-stripe-load-indicator] element (modules/system/assets/ui/js/loader.stripe.js),
         * which silences document-level handlers - and every scaffolded bulk button has that
         * attribute.
         *
         * The banner drops to page mode rather than disappearing: the rows are still visibly
         * checked, and it must not keep claiming records that the next request will not send.
         */
        $trigger.one('ajaxPromise.listSelection', function() {
            // The selection may already be gone - a row unchecked while the dialog was open.
            if (!$widget.data('listSelectAll')) {
                return
            }

            $widget.removeData('listSelectAll')
            $widget.children('.list-selection').removeClass('is-all').addClass('is-page')
        })
    })

    // LIST WIDGET DATA-API
    // ==============

    $(document).render(function(){
        $('[data-control="listwidget"]').listWidget();
    })

}(window.jQuery);
