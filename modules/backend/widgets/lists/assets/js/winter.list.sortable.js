/*
 * List widget drag-and-drop reordering.
 *
 * Minimal, additive enhancement layered on top of the existing list widget. When a list is
 * rendered with `data-sortable="true"`, SortableJS is initialised on its <tbody> using the
 * per-row drag handle. On drop the new record order is posted to the list's reorder handler,
 * which persists the order and re-renders the list.
 *
 * The record ids are collected in their new DOM order and assigned sequential 1..N sort
 * order values. Because sortable lists show every record (pagination is disabled), a simple
 * positional numbering is unambiguous and also handles freshly added (deferred) records that
 * do not yet have a stored sort order.
 */
+function ($) {
    "use strict";

    if (typeof window.Sortable === 'undefined') {
        return;
    }

    function collectIds(tbody) {
        return Array.prototype.map.call(
            tbody.querySelectorAll('tr[data-record-id]'),
            function (tr) {
                return tr.getAttribute('data-record-id');
            }
        );
    }

    function initList(listEl) {
        var tbody = listEl.querySelector('tbody');
        if (!tbody || tbody.wnListSortable) {
            return;
        }

        var handler = listEl.getAttribute('data-reorder-handler');
        if (!handler) {
            return;
        }

        var $list = $(listEl);

        // SortableJS dispatches a native "change" event on the list root (the tbody) while
        // an item is being dragged. Stop it bubbling to the surrounding form's change monitor
        // so reordering — which persists immediately — does not flag the form as having
        // unsaved changes. Real form-field changes (target = input/select/textarea) are left
        // untouched.
        tbody.addEventListener('change', function (event) {
            if (event.target === tbody) {
                event.stopPropagation();
            }
        });

        tbody.wnListSortable = window.Sortable.create(tbody, {
            handle: '.list-cell-sort-handle',
            draggable: 'tr',
            filter: '.no-data',
            animation: 150,
            ghostClass: 'list-sortable-ghost',
            chosenClass: 'list-sortable-chosen',
            onEnd: function () {
                var ids = collectIds(tbody);
                var orders = ids.map(function (id, index) {
                    return index + 1;
                });

                // The request is fired programmatically (not from a [data-request]
                // element), so show the stripe load indicator manually for feedback.
                var indicator = ($.wn && $.wn.stripeLoadIndicator) || ($.oc && $.oc.stripeLoadIndicator);
                if (indicator) {
                    indicator.show();
                }

                $list.request(handler, {
                    data: { record_ids: ids, sort_orders: orders }
                }).always(function () {
                    if (indicator) {
                        indicator.hide();
                    }
                });
            }
        });
    }

    function initAll() {
        var lists = document.querySelectorAll('[data-control="listwidget"][data-sortable="true"]');
        Array.prototype.forEach.call(lists, initList);
    }

    $(document).ready(initAll);

    // Re-initialise after the list partial is replaced by an AJAX update (e.g. onRefresh).
    $(document).on('render', initAll);
}(window.jQuery);
