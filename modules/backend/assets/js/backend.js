/*
 * Winter General Utilities
 */

/*
 * Path helpers
 */

if ($.wn === undefined)
    $.wn = {}
if ($.oc === undefined)
    $.oc = $.wn

$.wn.backendUrl = function(url) {
    var backendBasePath = $('meta[name="backend-base-path"]').attr('content')

    if (!backendBasePath)
        return url

    if (url.substr(0, 1) == '/')
        url = url.substr(1)

    return backendBasePath + '/' + url
}

/*
 * String escape
 */
if ($.wn === undefined)
    $.wn = {}
if ($.oc === undefined)
    $.oc = $.wn

$.wn.escapeHtmlString = function(string) {
    var htmlEscapes = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;'
        },
        htmlEscaper = /[&<>"'\/]/g

    return ('' + string).replace(htmlEscaper, function(match) {
        return htmlEscapes[match];
    })
}

/*
 * Inverse Click Event (not used)
 *
 * Calls the handler function if the user has clicked outside the object
 * and not on any of the elements in the exception list.
 */
/*
$.fn.extend({
    clickOutside: function(handler, exceptions) {
        var $this = this;

        $('body').on('click', function(event) {
            if (exceptions && $.inArray(event.target, exceptions) > -1) {
                return;
            } else if ($.contains($this[0], event.target)) {
                return;
            } else {
                handler(event, $this);
            }
        });

        return this;
    }
})
*/

/*
 * Browser Fixes
 * - If another fix using JS is necessary, move this logic to backend.fixes.js
 */

/*
 * Internet Explorer v11
 * - IE11 will not honor height 100% when overflow is used on the Y axis.
 */
if (!!window.MSInputMethodContext && !!document.documentMode) {
    $(window).on('resize', function() {
        fixMediaManager()
        fixSidebar()
    })

    function fixMediaManager() {
        var $el = $('div[data-control="media-manager"] .control-scrollpad')
        $el.height($el.parent().height())
    }

    function fixSidebar() {
        $('#layout-sidenav').height(Math.max(
            $('#layout-body').innerHeight(),
            $(window).height() - $('#layout-mainmenu').height()
        ))
    }
}
