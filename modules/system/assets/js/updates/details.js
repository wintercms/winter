/*
 * Details page
 */

+function ($) { "use strict";

    var UpdateDetails = function () {
        this.init()
    }

    UpdateDetails.prototype.init = function() {

        $(document).ready(function() {
            $('.plugin-details-content pre').addClass('prettyprint')
            prettyPrint()
        })

    }

     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    $.wn.updateDetails = new UpdateDetails;

}(window.jQuery);
