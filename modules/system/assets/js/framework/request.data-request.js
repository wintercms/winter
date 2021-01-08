/* globals window, jQuery */

/**
 * October CMS HTML Data Attribute Request extension.
 *
 * @copyright 2016-2021 Alexey Bobkov, Samuel Georges, Luke Towers
 * @author Ben Thomson <git@alfreido.com>
 * @link https://octobercms.com
 */
(function (october, document, $) {
    'use strict';

    october.extend('Request', {
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

                options = $.extend(true, {}, october.request.DEFAULTS, data, typeof options == 'object' && options)

                return october.request($this, handler, options)
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
            return october.json.parse('{' + value + '}')
        } catch (e) {
            throw new Error('Error parsing the ' + name + ' attribute value. ' + e)
        }
    }
}(window.october, window.document, jQuery));
