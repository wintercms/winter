/*
 * Sample click handlers for the Calendar behavior.
 *
 * Wire these up from config_calendar.yaml:
 *
 *     recordOnClick: $.wn.eventCalendar.onEventClick(:data, :startDate, :endDate, :event, :eventEl)
 *     onClickDate:   $.wn.eventCalendar.onClickDate(:data, :date, :dateStr, :allDay, :dayEl, :event, :view)
 *
 * The object referenced by the config (here `$.wn.eventCalendar`) is resolved and invoked when
 * the corresponding interaction occurs.
 */
+function ($) {
    "use strict";

    var EventCalendar = function () {

        // Called when an existing event is clicked.
        this.onEventClick = function (data, startDate, endDate, event, eventEl) {
            // `event` is the FullCalendar event object; open the record it points to.
            if (event.url) {
                window.location.href = event.url;
            }
        };

        // Called when an empty date/time cell is clicked, e.g. to create a new event.
        this.onClickDate = function (data, date, dateStr, allDay, dayEl, event, view) {
            // For example, open the create form pre-filled with the clicked date.
            window.location.href = 'author/plugin/events/create?start_at=' + encodeURIComponent(dateStr);
        };

    };

    $.wn = $.wn || {};
    $.wn.eventCalendar = new EventCalendar();

}(window.jQuery);
