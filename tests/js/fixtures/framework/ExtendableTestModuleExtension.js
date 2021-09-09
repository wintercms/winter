/* globals window */

(function (window) {
    'use strict';

    var ExtendableTestModuleExtension = function () {
    }

    ExtendableTestModuleExtension.prototype.extendableMethod = function () {
        return 'extended'
    }

    window.winter.ExtendableTestModule().extend('ExtendableTestModuleExtension', new ExtendableTestModuleExtension())
})(window);
