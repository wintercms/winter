/*jshint esversion: 6 */
const daysOfMonth = 42; // 6 weeks per month
const secondsOfDay = 86400;

class CalendarCache {
    /**
     *
     * @param int firstDay the first day of week, 0 = sunday ...
     * @param int capcity the default month data stored
     */
    constructor(methodName, firstDay = 0, capcity = 12) {
        this.clearCache();

        this.firstDay = firstDay;
        this.capcity = capcity;
        this._hideIndicatorCallback = null;
        this._showIndicatorCallback = null;
        this.methodName = methodName;
    }

    set hideIndicatorCallback(value) {
        this._hideIndicatorCallback = value;
    }
    get hideIndicatorCallback() {
        return this._hideIndicatorCallback;
    }

    set showIndicatorCallback(value) {
        this._showIndicatorCallback = value;
    }
    get showIndicatorCallback() {
        return this._showIndicatorCallback;
    }


    isEmpty() {
        return this.length === 0;
    }

    count() {
        return this.length;
    }

    clearCache() {
        this.cache = [];
        this.lfuCache = [];
        /**
         * cacheKey is MD5 string created by server, based on the query SQL not including monthData.startTime and endTime
         */
        this.cacheKey = '0';
        this.lastMonthReqeustData = null;
        this.length = 0;
        this.lastRequestStartTime = 0;
    }

    incrLFUCount(key) {
        let value = this.lfuCache[key];
        this.lfuCache[key] = (value === undefined) ? 1 : ++value;
    }

    removeOldCache() {
        if (this.count() < this.capcity) return;
        let minKey;
        let minValue = Number.MAX_SAFE_INTEGER;
        for (let key in this.lfuCache) {
            let element = this.lfuCache[key];
            if (minValue <= element) {
                minValue = element;
                minKey = key;
            }
        }
        delete this.lfuCache[minKey];
        delete this.cache[minKey];
        this.length--;
    }

    getCacheData(requestData) {
        // Maybe the first month
        if (this.isEmpty()) {
            return null;
        }
        let startTime = requestData.startTime;
        let endTime = requestData.endTime;
        let results = null;

        const self = this;

        for (let key in this.cache) {
            let element = this.cache[key];
            const timeKeys = key.split('-');
            if (this.cacheKey === timeKeys[0] &&
                startTime >= parseInt(timeKeys[1]) && endTime <= parseInt(timeKeys[2])) {
                self.incrLFUCount(key);
                results = element;
                break;
            }
        }
        return results;
    }

    /**
     *
     * return 6 weeks such as 2018 - 12 - 30 to 2019 - 01 - 05
     *
     * @param Array requestData
     */
    getMonthRequestData(requestData) {
        const startDate = new Date(requestData.startTime * 1000);

        if (startDate.getDay() === this.firstDay && (requestData.endTime - requestData.startTime) === daysOfMonth * secondsOfDay) {
            return requestData;
        }
        let firstDayOfMonth = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
        let daysDiff = firstDayOfMonth.getDay() - this.firstDay;
        let monthData;
        if (daysDiff !== 0) {
            // need to get the first day of week , eg: 2018-12-30 is the first day of jan, 2019
            if (daysDiff < 0) daysDiff = firstDayOfMonth.getDay() + this.firstDay;
            let firstDayOfMonthTime = firstDayOfMonth.getTime() / 1000 - secondsOfDay * daysDiff;
            monthData = {
                startTime: firstDayOfMonthTime,
                endTime: firstDayOfMonthTime + daysOfMonth * secondsOfDay,
                timeZone: requestData.timeZone,
            };
        } else {
            monthData = {
                startTime: requestData.startTime,
                endTime: requestData.startTime + daysOfMonth * secondsOfDay,
                timeZone: requestData.timeZone,
            };
        }
        return monthData;
    }

    /**
     *
     * the lastRequestStartTime is been used for loading the next or previous month
     *
     * @param integer startTime  unix Timestamp
     */
    setLastRequestTime(startTime) {
        this.lastRequestStartTime = startTime;
    }

    getLastMonthRequestData() {
        return this.lastMonthReqeustData;
    }

    saveCache(monthData, data) {
        const events = data.events;
        const startTime = monthData.startTime;
        const endTime = monthData.endTime;
        const key = data.cacheKey + '-' + startTime + '-' + endTime;
        this.setCacheKey(data.cacheKey);
        this.cache[key] = events;
        this.length++;
        this.incrLFUCount(key);
        this.removeOldCache();
    }

    setCacheKey(cacheKey = '0') {
        this.cacheKey = cacheKey;
    }

    showIndicator() {
        if (this.showIndicatorCallback) this.showIndicatorCallback();
    }

    hideIndicator() {
        if (this.hideIndicatorCallback) this.hideIndicatorCallback();
    }


    /**
     * Click the next button will load one more next month data
     * Click the previous button will load one more previous month data
     *
     * @param array requestData {startTime: unixTimestamp, endTime:, timeZone: string}
     */
    eagerRequest(requestData) {
        requestData = this.getMonthRequestData(requestData);
        let monthData = [];
        if (requestData.startTime > this.lastRequestStartTime) {
            // go to request next month
            monthData.startTime = requestData.endTime;
        } else {
            // go to request previous month
            monthData.startTime = requestData.startTime;
        }
        monthData.endTime = monthData.startTime + secondsOfDay;
        monthData.timeZone = requestData.timeZone;
        monthData = this.getMonthRequestData(monthData);

        this.lastRequestStartTime = requestData.startTime;

        let events = this.getCacheData(monthData);
        if (events !== null) return;
        const self = this;

        $.request(this.methodName, {
            data: monthData,
            success: function (data, textStatus, jqXHR) {
                self.saveCache(monthData, data);
            },
            error: function (jqXHR, textStatus, error) {
                this.error(jqXHR, textStatus, error);
            }
        });
    }

    saveFirstThreeMonthsData(data, monthDataList, onSuccessCallback) {
        const allEvents = data.events;
        let prevoiousMonthData = monthDataList[0];
        let currentMonthData = monthDataList[1];
        let nextMonthData = monthDataList[2];

        let previousEvents = [];
        let currentEvents = [];
        let nextEvents = [];

        for (let index in allEvents) {
            const event = allEvents[index];
            const eventStartTime = Date.parse(event.start) / 1000;
            const eventEndTime = Date.parse(event.end) / 1000;
            if (eventEndTime >= prevoiousMonthData.startTime &&
                eventStartTime < prevoiousMonthData.endTime) {
                previousEvents.push(event);
            }
            if (eventEndTime >= currentMonthData.startTime &&
                eventStartTime < currentMonthData.endTime) {
                currentEvents.push(event);
            }
            if (eventEndTime >= nextMonthData.startTime &&
                eventStartTime < nextMonthData.endTime) {
                nextEvents.push(event);
            }
        }

        const previousData = {
            cacheKey: data.cacheKey,
            events: previousEvents,
        };
        this.saveCache(prevoiousMonthData, previousData);

        const currentData = {
            cacheKey: data.cacheKey,
            events: currentEvents
        };
        this.saveCache(currentMonthData, currentData);

        onSuccessCallback(currentData.events);

        this.setLastRequestTime(currentMonthData.startTime);
        this.hideIndicator();

        const nextData = {
            cacheKey: data.cacheKey,
            events: nextEvents
        };
        this.saveCache(nextMonthData, nextData);
    }

    loadFirstThreeMonthsData(requestData, onSuccessCallback = () => {}, onErrorCallback = () => {}) {
        this.showIndicator();

        const currentMonthData = this.getMonthRequestData(requestData);
        this.lastMonthReqeustData = currentMonthData;
        // previous month
        let previousMonthData = [];
        previousMonthData.startTime = currentMonthData.startTime;
        previousMonthData.endTime = previousMonthData.startTime + secondsOfDay;
        previousMonthData.timeZone = currentMonthData.timeZone;
        previousMonthData = this.getMonthRequestData(previousMonthData);

        // next month
        let nextMonthData = [];
        nextMonthData.startTime = currentMonthData.endTime;
        nextMonthData.endTime = nextMonthData.startTime + secondsOfDay;
        nextMonthData.timeZone = currentMonthData.timeZone;
        nextMonthData = this.getMonthRequestData(nextMonthData);

        const monthData = {
            startTime: previousMonthData.startTime,
            endTime: nextMonthData.endTime,
            timeZone: currentMonthData.timeZone,
        };

        const self = this;
        $.request(this.methodName, {
            data: monthData,
            success: function (data, textStatus, jqXHR) {
                self.saveFirstThreeMonthsData(data, [previousMonthData, currentMonthData, nextMonthData], onSuccessCallback);
            },
            error: function (jqXHR, textStatus, error) {
                self.hideIndicator();
                this.error(jqXHR, textStatus, error);
                onErrorCallback();
            }
        });

    }

    requestEvents(requestData, onSuccessCallback = () => {}, onErrorCallback = () => {}) {
        if (this.isEmpty()) {
            this.loadFirstThreeMonthsData(requestData, onSuccessCallback, onErrorCallback);
            return;
        }

        let events = this.getCacheData(requestData);
        if (events !== null) {
            this.lastMonthReqeustData = requestData;
            this.eagerRequest(requestData);
            onSuccessCallback(events);
            return;
        }

        this.showIndicator();

        const monthData = this.getMonthRequestData(requestData);
        this.lastMonthReqeustData = monthData;

        const self = this;

        $.request(this.methodName, {
            data: monthData,
            success: function (data, textStatus, jqXHR) {
                const events = data.events;
                self.hideIndicator();
                // the events is whole month data
                self.saveCache(monthData, data);
                onSuccessCallback(events);
                self.eagerRequest(monthData);
            },
            error: function (jqXHR, textStatus, error) {
                self.hideIndicator();
                this.error(jqXHR, textStatus, error);
                onErrorCallback();
            }
        });
    }

    reloadLastMonth(onSuccessCallback = () => {}, onErrorCallback = () => {}) {
        const monthData = this.getLastMonthRequestData();
        this.clearCache();
        this.requestEvents(monthData, onSuccessCallback, onErrorCallback);
    }

    dispose() {
        this._hideIndicatorCallback = null;
        this._showIndicatorCallback = null;
        this.cache = [];
        this.lfuCache = [];
    }
}
