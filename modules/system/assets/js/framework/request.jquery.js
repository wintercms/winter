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
            $.fn.request = function(handler, options) {
                var $this = $(this).first()

                return october.request($this, handler, options)
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
}(window.october, window.document, jQuery));
