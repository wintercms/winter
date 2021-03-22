/* globals window, jQuery */

/**
 * Winter CMS HTML Data Attribute Request extension.
 *
 * @copyright 2021 Winter CMS
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com
 */

if (!window.winter) {
    throw new Error('The Winter CMS JS framework base must be loaded before the Data Attribute Request module can be registered.')
}

(function (winter, document, $) {
    'use strict';

    winter.extend('Request', {
        boot: function () {
            // Create request function in jQuery
            $.fn.request = function(handler, options) {
                var $this = $(this).first()
                var data  = {
                    evalBeforeUpdate: $this.data('request-before-update'),
                    evalSuccess: $this.data('request-success'),
                    evalError: $this.data('request-error'),
                    evalComplete: $this.data('request-complete'),
                    ajaxGlobal: $this.data('request-ajax-global'),
                    confirm: $this.data('request-confirm'),
                    redirect: $this.data('request-redirect'),
                    loading: $this.data('request-loading'),
                    flash: $this.data('request-flash'),
                    files: $this.data('request-files'),
                    browserValidate: $this.data('browser-validate'),
                    form: $this.data('request-form'),
                    url: $this.data('request-url'),
                    update: paramToObj('data-request-update', $this.data('request-update')),
                    data: paramToObj('data-request-data', $this.data('request-data'))
                }
                if (!handler) {
                    handler = $this.data('request')
                }

                options = $.extend(true, {}, winter.request.DEFAULTS, data, typeof options == 'object' && options)

                return winter.request($this, handler, options)
            }

            // Attach to elements for data requests
            $(document).on('submit', '[data-request]', function (event) {
                event.preventDefault()
                $(this).request()
            })
        },
    })

    var paramToObj = function (name, value) {
        if (value === undefined) {
            value = ''
        }
        if (typeof value == 'object') {
            return value
        }

        try {
            return winter.json.parse('{' + value + '}')
        } catch (e) {
            throw new Error('Error parsing the ' + name + ' attribute value. ' + e)
        }
    }
}(window.winter, window.document, jQuery));
