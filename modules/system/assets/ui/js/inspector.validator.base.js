/*
 * Inspector validator base class.
 */
+function ($) { "use strict";

    // NAMESPACES
    // ============================

    if ($.wn.inspector.validators === undefined)
        $.wn.inspector.validators = {}

    // CLASS DEFINITION
    // ============================

    var Base = $.wn.foundation.base,
        BaseProto = Base.prototype

    var BaseValidator = function(options) {
        this.options = options
        this.defaultMessage = 'Invalid property value.'

        Base.call(this)
    }

    BaseValidator.prototype = Object.create(BaseProto)
    BaseValidator.prototype.constructor = Base

    BaseValidator.prototype.dispose = function() {
        this.defaultMessage = null

        BaseProto.dispose.call(this)
    }

    BaseValidator.prototype.getMessage = function(defaultMessage) {
        if (this.options.message !== undefined) {
            return this.options.message
        }

        if (defaultMessage !== undefined) {
            return defaultMessage
        }

        return this.defaultMessage
    }

    BaseValidator.prototype.isScalar = function(value) {
        if (value === undefined || value === null) {
            return true
        }

        return !!(typeof value === 'string' || typeof value == 'number' || typeof value == 'boolean');
    }

    BaseValidator.prototype.isValid = function(value) {
        return null
    }

    $.wn.inspector.validators.base = BaseValidator
}(window.jQuery);