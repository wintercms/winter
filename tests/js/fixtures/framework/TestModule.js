/* globals window */

(function (window) {
    'use strict';

    var TestModule = function () {
    }

    TestModule.prototype.testMethod = function () {
        return 'Tested'
    }

    window.winter.extend('TestModule', TestModule)
})(window);
