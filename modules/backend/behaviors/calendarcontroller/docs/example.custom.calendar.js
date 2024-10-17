// Sample for config_calendar.yaml -> recordOnClick
+ function ($) {
    "use strict";

    var EventController = function () {

        this.onEventClick = function (data, startDate, endDate, event, eventEl) {
            alert('eventID  = '+ event.id);
        }

    }
    $.wn.eventController = new EventController;

}(window.jQuery);
