/*
 * Stubs for testing winter.form.js in isolation.
 *
 * Provides minimal implementations of WinterCMS dependencies that the
 * form widget relies on at load time.
 */

// ocJSON - used by paramToObj inside the form widget
window.ocJSON = function (str) {
    return JSON.parse(str);
};

// $(document).render() - used by the form widget to auto-initialize on DOM ready
jQuery.fn.render = function (fn) {
    fn();
};

// $.fn.request() - stub that synchronously invokes the success callback
jQuery.fn.request = function (handler, options) {
    var deferred = jQuery.Deferred();
    deferred.success = function (fn) {
        fn();
        return deferred;
    };
    return deferred;
};

// $.fn.loadIndicator() - no-op stub
jQuery.fn.loadIndicator = function () {
    return this;
};
