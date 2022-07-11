import { DateTime } from 'luxon';

/**
 * DateTime control.
 *
 * This control uses the Luxon time library to display time and date values. It will be attached to
 * `<time data-datetime-control>` elements.
 *
 * Usage:
 *
 * <time
 *      data-datetime-control
 *      datetime="2014-11-19 01:21:57"
 *      data-format="dddd Do [o]f MMMM YYYY hh:mm:ss A"
 *      data-timezone="Australia/Sydney"
 *      data-locale="en-au">This text will be replaced</time>
 *
 * Alias options:
 *
 * time             -> 6:28 AM
 * timeLong         -> 6:28:01 AM
 * date             -> 04/23/2016
 * dateMin          -> 4/23/2016
 * dateLong         -> April 23, 2016
 * dateLongMin      -> Apr 23, 2016
 * dateTime         -> April 23, 2016 6:28 AM
 * dateTimeMin      -> Apr 23, 2016 6:28 AM
 * dateTimeLong     -> Saturday, April 23, 2016 6:28 AM
 * dateTimeLongMin  -> Sat, Apr 23, 2016 6:29 AM
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class DateTimeControl extends Snowboard.PluginBase {
    construct(element) {
        this.element = element;
        if (this.element.dataset.controlInitialised) {
            this.destruct();
            return;
        }
        this.element.dataset.controlInitialised = true;
        this.element.dataset.disposable = true;

        this.config = this.snowboard.dataConfig(this, element);
        this.appTimezone = this.getAppTimezone();
        this.backendTimezone = this.getBackendTimezone();
        this.backendLocale = this.getBackendLocale();
        this.format = this.getFormat();

        this.dateTime = this.element.getAttribute('datetime');
        if (!this.dateTime) {
            throw new Error('The "datetime" attribute is required for a DateTime control.');
        }

        this.updateElement();
    }

    defaults() {
        return {
            format: 'ccc ff',
            formatAlias: null,
            ignoreTimezone: false,
            timeSince: false,
            timeTense: false,
        };
    }

    updateElement() {
        this.element.innerText = this.getFormattedValue();
    }

    getFormattedValue() {
        const dateObj = DateTime.fromSQL(this.dateTime)
            .setZone(this.appTimezone)
            .setLocale(this.backendLocale)
            .setZone(this.backendTimezone);

        if (this.timeSince) {
            return dateObj.toRelative();
        }
        if (this.timeTense) {
            const weekAgo = DateTime.now().minus({ weeks: 1 });
            if (weekAgo < dateObj) {
                return dateObj.toRelativeCalendar({ unit: 'days' });
            }
        }
        return dateObj.toFormat(this.format);
    }

    formatAliases() {
        return {
            time: 't',
            timeLong: 'tt',
            date: 'D',
            dateMin: 'D',
            dateLong: 'DDD',
            dateLongMin: 'DD',
            dateTime: 'fff',
            dateTimeMin: 'ff',
            dateTimeLong: 'ffff',
            dateTimeLongMin: 'ccc ff',
        };
    }

    getAppTimezone() {
        return this.config.get('ignoreTimezone')
            ? 'UTC'
            : this.getMetaValue('app-timezone', 'UTC');
    }

    getBackendTimezone() {
        return this.config.get('ignoreTimezone')
            ? 'UTC'
            : this.getMetaValue('backend-timezone', 'UTC');
    }

    getBackendLocale() {
        return this.getMetaValue('backend-locale', 'en-US');
    }

    getFormat() {
        if (
            this.config.get('formatAlias')
            && Object.keys(this.formatAliases()).includes(this.config.get('formatAlias'))
        ) {
            return this.formatAliases()[this.config.get('formatAlias')];
        }

        return this.config.get('format');
    }

    getMetaValue(name, defaultValue = null) {
        const meta = document.querySelector(`meta[name="${name}"]`);

        if (!meta) {
            return defaultValue;
        }

        return meta.getAttribute('content');
    }
}
