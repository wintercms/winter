/*
 * Winter JavaScript foundation library.
 *
 * Utility functions for working back-end client-side UI controls.
 *
 * Usage examples:
 *
 * $.wn.foundation.controlUtils.markDisposable(el)
 * $.wn.foundation.controlUtils.disposeControls(container)
 *
 */
+function ($) { "use strict";
     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    if ($.wn.foundation === undefined)
        $.wn.foundation = {}

    var ControlUtils = {
        markDisposable: function(el) {
            el.setAttribute('data-disposable', '')
        },

        /*
         * Destroys all disposable controls in a container.
         * The disposable controls should watch the dispose-control
         * event.
         */
        disposeControls: function(container) {
            var controls = container.querySelectorAll('[data-disposable]')

            for (var i=0, len=controls.length; i<len; i++)
                $(controls[i]).triggerHandler('dispose-control')

            if (container.hasAttribute('data-disposable'))
                $(container).triggerHandler('dispose-control')
        }
    }

    $.wn.foundation.controlUtils = ControlUtils;

    $(document).on('ajaxBeforeReplace', function(ev){
        // Automatically dispose controls in an element
        // before the element contents is replaced.
        // The ajaxBeforeReplace event is triggered in
        // framework.js

        $.wn.foundation.controlUtils.disposeControls(ev.target)
    })
}(window.jQuery);