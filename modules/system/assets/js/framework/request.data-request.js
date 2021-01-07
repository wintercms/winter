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
            // Attach to elements for data requests
            $(document).on('submit', '[data-request]', function (event) {
                event.preventDefault()
                $(this).request()
            })
        },
    })
}(window.october, window.document, jQuery));
