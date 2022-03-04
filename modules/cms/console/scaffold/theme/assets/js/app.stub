/*
 * Application
 */
(function($) {
    "use strict";

    jQuery(document).ready(function($) {
        /*-------------------------------
        WINTER CMS FLASH MESSAGE HANDLING
        ---------------------------------*/
        $(document).on('ajaxSetup', function(event, context) {
            // Enable AJAX handling of Flash messages on all AJAX requests
            context.options.flash = true;

            // Enable the StripeLoadIndicator on all AJAX requests
            context.options.loading = $.oc.stripeLoadIndicator;

            // Handle Flash Messages
            context.options.handleFlashMessage = function(message, type) {
                $.oc.flashMsg({ text: message, class: type });
            };

            // Handle Error Messages
            context.options.handleErrorMessage = function(message) {
                $.oc.flashMsg({ text: message, class: 'error' });
            };
        });
    });
}(jQuery));

if (typeof(gtag) !== 'function') {
    gtag = function() { console.log('GoogleAnalytics not present.'); }
}
