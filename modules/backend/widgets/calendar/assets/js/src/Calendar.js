import CalendarCache from './CalendarCache';

/*
 * Calendar widget control (Snowboard).
 *
 * Ported from the original Storm ($.wn.foundation) control. It wraps a FullCalendar v6
 * instance, fetches events through the month-window CalendarCache (routed via Snowboard's
 * request layer), and bridges to the Storm-based Toolbar search / Filter widgets through the
 * legacy jQuery `wn.beforeRequest` / `ajaxComplete` events those widgets still emit.
 *
 * NOTE: not yet verified in a browser - see the widget PR notes. The FullCalendar global is
 * provided by the committed vendor bundle registered in Calendar::loadAssets().
 */
((Snowboard) => {
    const $ = window.jQuery;

    class Calendar extends Snowboard.PluginBase {
        construct(element) {
            this.element = element;
            this.$el = $(element);
            this.config = this.snowboard.dataConfig(this, element);

            this.calendarControl = null;
            this.$loadContainer = this.$el.find('.loading-indicator-container:first');
            this.firstDay = parseInt(this.config.get('firstDay'), 10) || 0;

            this.cache = new CalendarCache(
                this.makeEventHandler('onRefreshEvents'),
                (handler, options) => this.snowboard.request(this.element, handler, options),
                this.firstDay
            );
            this.cache.showIndicatorCallback = () => this.showIndicator();
            this.cache.hideIndicatorCallback = () => this.hideIndicator();

            // Bridge to the Storm-based search/filter widgets, which still emit jQuery AJAX events.
            this.onBeforeRequest = (ev, context) => this.beforeFilterRequestSend(ev, context);
            this.onAjaxComplete = (ev, context, responseData, textStatus, jqXHR) =>
                this.onFilterUpdate(ev, context, responseData, textStatus, jqXHR);
            $(document).on('wn.beforeRequest', this.onBeforeRequest);
            $(document).on('ajaxComplete', this.onAjaxComplete);

            this.initCalendarControl();
        }

        /**
         * Config keys read from the element's data-* attributes.
         */
        defaults() {
            return {
                alias: null,
                initialView: 'dayGridMonth',
                displayModes: 'dayGridMonth',
                firstDay: 0,
                timezone: 'local',
                editable: false,
                clickDate: null,
            };
        }

        destruct() {
            $(document).off('wn.beforeRequest', this.onBeforeRequest);
            $(document).off('ajaxComplete', this.onAjaxComplete);

            this.disposeCalendarControl();

            if (this.cache) {
                this.cache.dispose();
            }

            this.cache = null;
            this.$loadContainer = null;
            this.$el = null;
            this.element = null;

            super.destruct();
        }

        showIndicator() {
            if (this.$loadContainer) {
                this.$loadContainer.loadIndicator();
            }
        }

        hideIndicator() {
            if (this.$loadContainer) {
                this.$loadContainer.loadIndicator('hide');
            }
        }

        initCalendarControl() {
            const $calendar = this.$el.find('.calendar-control');
            const self = this;
            const locale = $('meta[name="backend-locale"]').attr('content');
            // Prefer the widget-configured timezone, falling back to the backend meta tag.
            // Named timezones (anything other than 'local' / 'UTC') require a FullCalendar
            // named-timezone plugin; without one v6 treats them as UTC.
            const timezone = this.config.get('timezone')
                || $('meta[name="backend-timezone"]').attr('content')
                || 'local';

            this.calendarControl = new FullCalendar.Calendar($calendar[0], {
                // Configuration
                initialView: this.config.get('initialView'),
                firstDay: this.firstDay,
                timeZone: timezone,
                locale: locale,

                // Toolbar
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: this.config.get('displayModes')
                },

                // Date Nav Links
                navLinks: true, // Determines if day names and week names are clickable.

                // Week Numbers
                weekNumbers: true,

                // Event Dragging & Resizing
                editable: Boolean(this.config.get('editable')),

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
                // v6 replacement for v4's eventRender. The original popover tooltip relied on a
                // popper/tooltip lib that shipped with the (now removed) v4 vendor tree; fall back
                // to a native title attribute until a tooltip lib is reintroduced.
                eventDidMount: function (info) {
                    const tooltipContent = info.event.extendedProps.tooltip;
                    if (tooltipContent) {
                        info.el.setAttribute('title', tooltipContent);
                    }
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    self.onPrevNextButtonClick(fetchInfo, successCallback, failureCallback);
                },
                dateClick: function (info) {
                    self.onDateClick(info);
                }
            });
            this.calendarControl.render();
        }

        beforeFilterRequestSend(ev, context) {
            if (context.handler !== 'calendarFilter::onFilterUpdate' &&
                context.handler !== 'calendarToolbarSearch::onSubmit') {
                return true;
            }

            const monthRequestData = this.cache.getLastMonthRequestData();
            if (monthRequestData === null) return;

            context.options.data.calendar_time = monthRequestData;
        }

        onPrevNextButtonClick(fetchInfo, successCallback, failureCallback) {
            this.refreshEvents(
                fetchInfo.start.getTime() / 1000,
                fetchInfo.end.getTime() / 1000,
                fetchInfo.timeZone,
                successCallback,
                failureCallback
            );
        }

        refreshEvents(startTime, endTime, timeZone, onSuccessCallback = () => {}, onErrorCallback = () => {}) {
            const data = {
                startTime: startTime,
                endTime: endTime,
                timeZone: timeZone
            };
            this.clearEvents();
            this.cache.requestEvents(data, onSuccessCallback, onErrorCallback);
        }

        reloadLastMonth() {
            this.clearEvents();
            this.cache.reloadLastMonth((events) => {
                this.addEvents(events);
            });
        }

        onEventClick(info) {
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
                    const object = eval(objectName); // eslint-disable-line no-eval
                    object[funcName](info, info.event.start, info.event.end, info.event, info.el);
                }
            }
        }

        disposeCalendarControl() {
            if (this.calendarControl) {
                this.calendarControl.destroy();
                this.calendarControl = null;
            }
        }

        onDateClick(info) {
            const clickDate = this.config.get('clickDate');
            if (clickDate == null || clickDate.length === 0) return;
            const elements = clickDate.split('.');
            let funcName = elements.pop(); // remove the last element
            const objectName = elements.join('.');

            const index = funcName.indexOf('(');
            funcName = funcName.substring(0, index);
            const object = eval(objectName); // eslint-disable-line no-eval
            object[funcName](info, info.date, info.dateStr, info.allDay, info.dayEl, info.jsEvent, info.view);
        }

        addEvent(eventObj = null) {
            this.calendarControl.addEvent(eventObj);
        }

        addEvents(eventList) {
            // v6 removed Calendar.batchRendering(); events are simply added one by one.
            for (const event of eventList) {
                this.addEvent(event);
            }
        }

        /**
         * Make Event Handler, same as PHP $this->getEventHandler('xxx')
         */
        makeEventHandler(methodName) {
            return this.config.get('alias') + '::' + methodName;
        }

        clearEvents() {
            if (this.calendarControl === null) return;
            const events = this.calendarControl.getEvents();
            if (!events) return;
            events.forEach((event) => {
                event.remove();
            });
        }

        onFilterUpdate(event, context, responseData) {
            const data = responseData.responseJSON;
            if (data && Object.prototype.hasOwnProperty.call(data, 'id')
                && Object.prototype.hasOwnProperty.call(data, 'events')
                && Object.prototype.hasOwnProperty.call(data, 'method')) {
                if (data.id === 'calendar' && data.method === 'onRefresh') {
                    this.clearEvents();
                    const requestData = {
                        startTime: data.startTime,
                        endTime: data.endTime
                    };
                    this.cache.saveCache(requestData, data);
                    // clear the previous request time
                    this.cache.setLastRequestTime(0);
                    this.cache.eagerRequest(requestData);
                    this.addEvents(data.events);
                }
            }
        }
    }

    Snowboard.addPlugin('backend.widget.calendar', Calendar);
    Snowboard['backend.ui.widgethandler']().register('calendar', 'backend.widget.calendar');
})(window.Snowboard);
