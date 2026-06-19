import Sortable from 'sortablejs';

/*
 * List widget drag-and-drop reordering.
 *
 * Additive enhancement on top of the existing list widget. When a list is rendered with
 * `data-sortable="true"`, SortableJS is initialised on its <tbody> using the per-row drag
 * handle cell. On drop, the record ids in their new DOM order are posted to the list's reorder
 * handler, which assigns the sort order values server-side (by position) and re-renders.
 */
(function ($) {
    "use strict";

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

        tbody.wnListSortable = Sortable.create(tbody, {
            handle: '.list-cell-sort-handle',
            draggable: 'tr',
            filter: '.no-data',
            animation: 150,
            ghostClass: 'list-sortable-ghost',
            chosenClass: 'list-sortable-chosen',
            onEnd: function (event) {
                // Nothing changed if the row was dropped back in its original position.
                if (event.oldIndex === event.newIndex) {
                    return;
                }

                // The request is fired programmatically (not from a [data-request] element),
                // so show the stripe load indicator manually for feedback.
                var indicator = ($.wn && $.wn.stripeLoadIndicator) || ($.oc && $.oc.stripeLoadIndicator);
                if (indicator) {
                    indicator.show();
                }

                // Only the record ids (in their new order) are sent; the server assigns the
                // sort order values by position.
                $list.request(handler, {
                    data: { record_ids: collectIds(tbody) }
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
})(window.jQuery);
