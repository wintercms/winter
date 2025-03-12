
+ function ($) {
    "use strict";
    const Base = $.wn.foundation.base,
        BaseProto = Base.prototype;

    const Calendar = function (element, options) {
        this.options = options;
        this.$el = $(element);
        this.calendarControl =  null;
        this.$loadContainer = this.$el.find('.loading-indicator-container:first');
        this.firstDay = options.firstDay;

        this.calendarCache = new CalendarCache(this.makeEventHandler('onRefreshEvents'), this.firstDay);
        const self = this;
        this.calendarCache.showIndicatorCallback = function(){
            self.$loadContainer.loadIndicator();
        };

        this.calendarCache.hideIndicatorCallback = function(){
            self.$loadContainer.loadIndicator('hide');
        };

        $.wn.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    Calendar.prototype = Object.create(BaseProto)
    Calendar.prototype.constructor = Calendar

    Calendar.DEFAULTS = {
        alias: null,
        initialView: 'dayGridMonth',
        displayModes: 'dayGridMonth',
        editable: false,
        clickDate: null,
    }

    Calendar.prototype.init = function () {
        let self = this;
        $(document).ready(function () {
            self.initCalendarControl();
        });

        this.$el.on('dispose-control', this.proxy(this.dispose));
        $(document).on('ajaxComplete', this.proxy(this.onFilterUpdate));
        $(document).on('wn.beforeRequest', this.proxy(this.beforeFilterRequestSend));
    };

    Calendar.prototype.dispose = function () {
        this.calendarCache.dispose();
        $(document).off('ajaxComplete', this.proxy(this.onFilterUpdate));
        this.$el.off('dispose-control', this.proxy(this.dispose));
        $(document).off('wn.beforeRequest', this.proxy(this.beforeFilterRequestSend));
        this.$el.removeData('wn.calendar');

        this.$el = null
        this.options = null
        BaseProto.dispose.call(this)
    }
    // Deprecated
    Calendar.prototype.unbind = function () {
        this.dispose()
    }

    Calendar.prototype.initCalendarControl = function(){
        const $calendar = this.$el.find('.calendar-control');
        const self = this;
        const locale = $('meta[name="backend-locale"]').attr('content');
        const timezone = $('meta[name="backend-timezone"]').attr('content');
        const container = document.querySelector(".calendar-container");

        this.calendarControl = new FullCalendar.Calendar($calendar[0], {
            // Configuration
            initialView: this.options.initialView,
            firstDay: this.firstDay,
            timeZone: timezone,
            locale: locale,

            // Toolbar
            headerToolbar: {
                start: 'prev,next today',
                center: 'title',
                end: this.options.displayModes
            },

            // Date Nav Links
            navLinks: true, // Determines if day names and week names are clickable.

            // Week Numbers
            weekNumbers: true,
            weekNumbersWithinDays: true,

            // Event Dragging & Resizing
            editable: this.options.editable,

            //  Event Display
            eventDisplay: 'block', // render single-day timed events as solid filled rectangle
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
            },

            // Event Popover
            dayMaxEventRows: true, // allow "more" link when too many events
            dayMaxEvents: true, // when too many events in a day, show the popover


            // Events
            eventClick: function (info) {
                self.onEventClick(info);
            },
            eventRender: function(info) {
                const tooltipContent = info.event.extendedProps.tooltip;
                if (tooltipContent) {
                    let tooltip = new Tooltip(info.el, {
                        title: tooltipContent,
                        placement: 'top',
                        trigger: 'hover',
                        container: container
                    });
                }
            },
            events: function (fetchInfo, successCallback, failureCallback){
                self.onPrevNextButtonClick(fetchInfo, successCallback, failureCallback);
            },
            dateClick: function(info){
                self.onDateClick(info);
            }

        });
        this.calendarControl.render();

    };

    Calendar.prototype.beforeFilterRequestSend = function(ev, context){
        if (context.handler !== "calendarFilter::onFilterUpdate" &&
            context.handler !== "calendarToolbarSearch::onSubmit") {
                return true;
        }

        const monthRequestData = this.calendarCache.getLastMonthRequestData();
        if (monthRequestData === null) return;

        context.options.data.calendar_time = monthRequestData;
    }

    Calendar.prototype.onPrevNextButtonClick = function (fetchInfo, successCallback, failureCallback){
        this.refreshEvents(fetchInfo.start.getTime() / 1000,
            fetchInfo.end.getTime() / 1000, fetchInfo.timeZone, successCallback, failureCallback);
    };

    Calendar.prototype.refreshEvents = function (startTime, endTime, timeZone, onSuccessCallback = function () { }, onErrorCallback = function () { }) {
        const data = {
            startTime: startTime,
            endTime: endTime,
            timeZone: timeZone
        };
        this.clearEvents();
        this.calendarCache.requestEvents(data, onSuccessCallback, onErrorCallback);
    }

    Calendar.prototype.reloadLastMonth = function() {
        this.clearEvents();
        const self = this;
        this.calendarCache.reloadLastMonth(function(events){
            self.addEvents(events);
        });
    }

    Calendar.prototype.onEventClick = function(info){
        info.jsEvent.preventDefault();
        const url = info.event.url;
        if (url) {
            if (url.startsWith('http') || (!url.startsWith('$'))) {
                location.href = url;
            } else {
                const elements = url.split('.');
                let funcName = elements.pop(); // remove the last element
                const objectName = elements.join('.');
                const index = funcName.indexOf('(');
                funcName = funcName.substring(0, index);
                const object = eval(objectName);
                object[funcName](info, info.event.start, info.event.end, info.event, info.el);
            }
        }
    }

    Calendar.prototype.disposeCalendarControl = function () {
        if (this.calendarControl){
            this.calendarControl.off('dateClick', this.proxy(this.onDateClick));
            this.calendarControl.destroy();
            this.calendarControl = null;
        }
    }

    Calendar.prototype.onDateClick = function (info) {
        if (this.options.clickDate == null || this.options.clickDate.length === 0) return;
        const elements = this.options.clickDate.split('.');
        let funcName =  elements.pop(); // remove the last element
        const objectName = elements.join('.');

        const index = funcName.indexOf('(');
        funcName = funcName.substring(0, index);
        const object = eval(objectName);
        object[funcName](info, info.date, info.dateStr, info.allDay, info.dayEl, info.jsEvent, info.view);
    }

    Calendar.prototype.addEvent = function (eventObj = null) {
        this.calendarControl.addEvent(eventObj);
    }
    Calendar.prototype.addEvents = function (eventList) {

        const self = this;
        this.calendarControl.batchRendering(function() {
            for(let event of eventList){
                self.addEvent(event);
            }
        });
    }

    /**
     * Make Event Handler, same as PHP $this->getEventHandler('xxx')
     */
    Calendar.prototype.makeEventHandler = function (methodName) {
        return this.options.alias + "::" + methodName;
    }
    Calendar.prototype.clearEvents = function(){
        if (this.calendarControl === null ) return;
        const events = this.calendarControl.getEvents();
        if (events === null) return;
        this.calendarControl.batchRendering(function() {
            events.forEach(event => {
                event.remove();
            });
        });

    }

    Calendar.prototype.onFilterUpdate = function (event, context, responseData, textStatus, jqXHR){
        const data = responseData.responseJSON;
        if (data && data.hasOwnProperty('id')
                && data.hasOwnProperty('events')
                && data.hasOwnProperty('method')) {
            if (data.id === 'calendar' && data.method === 'onRefresh') {
                this.clearEvents();
                const requestData  = {
                    startTime: data.startTime,
                    endTime: data.endTime
                }
                this.calendarCache.saveCache(requestData, data);
                //clear the prevous requestTime
                this.calendarCache.setLastRequestTime(0);
                this.calendarCache.eagerRequest(requestData);
                this.addEvents(data.events);
            }
        }
    }

    // CALENDAR CONTROL PLUGIN DEFINITION
    // ============================

    const old = $.fn.calendarControl

    $.fn.calendarControl = function (option) {
        var args = Array.prototype.slice.call(arguments, 1),
            result
        this.each(function () {
            var $this = $(this)
            var data = $this.data('wn.calendar')
            var options = $.extend({}, Calendar.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('wn.calendar', (data = new Calendar(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.calendarControl.Constructor = Calendar

    // CALENDAR CONTROL NO CONFLICT
    // =================

    $.fn.calendarControl.noConflict = function () {
        $.fn.calendarControl = old
        return this
    }

    // CALENDAR CONTROL DATA-API
    // ===============

    $(document).render(function () {
        $('[data-control="calendar"]').calendarControl()
    });

}(window.jQuery);
