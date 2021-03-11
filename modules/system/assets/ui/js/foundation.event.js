/*
 * Winter JavaScript foundation library.
 *
 * Light-weight utility functions for working with native DOM events. The functions
 * work with events directly, without jQuery, using the native JavaScript and DOM
 * features.
 *
 * Usage examples:
 *
 * $.wn.foundation.event.stop(ev)
 *
 */
+function ($) { "use strict";
     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    if ($.wn.foundation === undefined)
        $.wn.foundation = {}

    var Event = {
        /*
         * Returns the event target element.
         * If the second argument is provided (string), the function
         * will try to find the first parent with the tag name matching
         * the argument value.
         */
        getTarget: function(ev, tag) {
            var target = ev.target ? ev.target : ev.srcElement

            if (tag === undefined)
                return target

            var tagName = target.tagName

            while (tagName != tag) {
                target = target.parentNode

                if (!target)
                    return null

                tagName = target.tagName
            }

            return target
        },

        stop: function(ev) {
            if (ev.stopPropagation)
                ev.stopPropagation()
            else
                ev.cancelBubble = true

            if(ev.preventDefault)
                ev.preventDefault()
            else
                ev.returnValue = false
        },

        pageCoordinates: function(ev) {
            if (ev.pageX || ev.pageY) {
                return {
                    x: ev.pageX,
                    y: ev.pageY
                }
            }
            else if (ev.clientX || ev.clientY) {
                return {
                    x: (ev.clientX + document.body.scrollLeft + document.documentElement.scrollLeft),
                    y: (ev.clientY + document.body.scrollTop + document.documentElement.scrollTop)
                }
            }

            return {
                x: 0,
                y: 0
            }
        }
    }

    $.wn.foundation.event = Event;
}(window.jQuery);