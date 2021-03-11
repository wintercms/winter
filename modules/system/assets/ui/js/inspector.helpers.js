/*
 * Inspector helper functions.
 *
 */
+function ($) { "use strict";

    // NAMESPACES
    // ============================

     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    if ($.wn.inspector === undefined)
        $.wn.inspector = {}

    $.wn.inspector.helpers = {}

    $.wn.inspector.helpers.generateElementUniqueId = function(element) {
        if (element.hasAttribute('data-inspector-id')) {
            return element.getAttribute('data-inspector-id')
        }

        var id = $.wn.inspector.helpers.generateUniqueId()
        element.setAttribute('data-inspector-id', id)

        return id
    }

    $.wn.inspector.helpers.generateUniqueId = function() {
        return "inspectorid-" + Math.floor(Math.random() * new Date().getTime());
    }

}(window.jQuery)