/* globals window, jQuery */

/**
 * October CMS jQuery Request extension.
 *
 * @copyright 2021 Winter CMS
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com
 */

 if (!window.winter) {
    throw new Error('The Winter CMS JS framework base must be loaded before the jQuery Request module can be registered.')
}

(function (winter, document, $) {
    'use strict';

    winter.extend('Request', {
        boot: function () {
            // Create request function in jQuery
            $.fn.request = function(handler, options) {
                var $this = $(this).first()

                return winter.request($this, handler, options)
            }

            $.request = function(handler, options) {
                return $(document).request(handler, options)
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
        processHandler: function () {
            var args = arguments[1]

            return args[1]
        },
        processOptions: function (options, args) {
            return Object.assign({}, options, args[2])
        },
    })
}(window.winter, window.document, jQuery));
