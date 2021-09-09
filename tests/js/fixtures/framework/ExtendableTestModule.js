/* globals window */

(function (window) {
    'use strict';

    var ExtendableTestModule = function () {
    }

    ExtendableTestModule.prototype.extendable = [
        'extendableMethod',
    ]

    ExtendableTestModule.prototype.extendableMethod = function () {
        var result = this.callExtendable('extendableMethod')

        if (!result) {
            result = 'default'
        }

        return result
    }

    window.winter.extend('ExtendableTestModule', ExtendableTestModule)
})(window);
