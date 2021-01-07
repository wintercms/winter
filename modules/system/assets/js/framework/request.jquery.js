/* globals window, jQuery */

/**
 * October CMS jQuery Request extension.
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
            $.fn.request = function(handler, option) {
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
                    // update: paramToObj('data-request-update', $this.data('request-update')),
                    // data: paramToObj('data-request-data', $this.data('request-data'))
                }
                if (!handler) {
                    handler = $this.data('request')
                }

                var options = $.extend(true, {}, october.request.DEFAULTS, data, typeof option == 'object' && option)

                return october.request($this, handler, options)
            }

            $.request = function(handler, option) {
                return $(document).request(handler, option)
            }

            /*
            * Invent our own event that unifies document.ready with window.ajaxUpdateComplete
            *
            * $(document).render(function() { })
            * $(document).on('render', function() { })
            */
            $(function triggerRenderOnReady() {
                $(document).trigger('render')
            })

            $(window).on('ajaxUpdateComplete', function triggerRenderOnAjaxUpdateComplete() {
                $(document).trigger('render')
            })

            $.fn.render = function(callback) {
                $(document).on('render', callback)
            }
        },
        processElement: function (args) {
            return args[0]
        },
        getForm: function (args, element) {
            return args[2].form ? $(args[2].form) : element.closest('form')
        },
        processHandler: function (args) {
            this.handler = args[1]
        },
        processOptions: function (args) {
            return args[2] || {}
        },
    })
}(window.october, window.document, jQuery));
