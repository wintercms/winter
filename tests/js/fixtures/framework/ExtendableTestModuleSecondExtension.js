/* globals window */

(function (window) {
    'use strict';

    var ExtendableTestModuleSecondExtension = function () {
    }

    ExtendableTestModuleSecondExtension.prototype.extendableMethod = function () {
        return 'second'
    }

    window.winter.ExtendableTestModule().extend('ExtendableTestModuleSecondExtension', new ExtendableTestModuleSecondExtension())
})(window);
