"use strict";

/**
 * October CMS JavaScript framework.
 *
 * This class represents a new take on the October CMS JS framework, being fully modular and removing the dependency
 * for jQuery.
 *
 * @copyright 2016-2021 Alexey Bobkov, Samuel Georges, Luke Towers
 * @author Ben Thomson <git@alfreido.com>
 * @link https://octobercms.com
 */
+function (window) {
    // Do not double-instatiate
    if (window.october) {
        return
    }

    var OctoberJsFramework = function () {
        this._modules = {}
    }

    /**
     * Extends the October CMS JavaScript framework, or any registered module that allows extensions.
     *
     * Extensions provide functionality for extendable methods and attachable methods. Extendable methods are
     * intended to be once-off implemented - ie. the first plugin that provides the method handles it. Attachable
     * methods can be listened by multiple extensions, similar in handling to events.
     *
     * @param {String} module
     * @param {(Object|Function)} extension
     */
    OctoberJsFramework.prototype.extend = function (module, extension) {
        if (typeof module !== 'string') {
            throw new Error('Module name must be specified as a string.')
        }
        if ((typeof extension !== 'object' && typeof extension !== 'function') || extension === null) {
            throw new Error('Module extension must be either an object or function that returns an object.')
        }

        if (this.hasModule(module)) {
            // We are extending a registered module
            if (!this.moduleIsExtendable(module) && !this.moduleIsAttachable(module)) {
                throw new Error('Module "' + module + '" cannot be extended')
            }

            var sourceModule = this.getModule(module),
                extensionMethods = {},
                extendableMethods = sourceModule.prototype.extendable || [],
                attachableMethods = sourceModule.prototype.attachable || [],
                extendedMethods = {},
                attachedMethods = {},
                extensionObj = {}

            if (typeof extension === 'function') {
                extensionMethods = Object.create({}, extension.call(sourceModule))
            } else {
                extensionMethods = extension
            }

            for (var method in extensionMethods) {
                // Allow extensions or attachments only to methods allowed by the module. Discard all others.
                if (extendableMethods.indexOf(method) === -1 && attachableMethods.indexOf(method) === -1) {
                    delete extensionMethods[method]
                }
                if (extendableMethods.indexOf(method) !== -1) {
                    extendedMethods[method] = extensionMethods[method]
                } else {
                    attachedMethods[method] = extensionMethods[method]
                }
            }

            // No extensions or attachments were made
            if (Object.keys(extendedMethods).length === 0 && Object.keys(attachedMethods).length === 0) {
                return
            }

            extensionObj = {
                methods: extendedMethods,
                events: attachedMethods
            }

            this._modules[module].extensions.push(extensionObj)
        } else {
            // We are extending the framework itself

            // Add extension and attached method calls
            extension.prototype.callExtendable = function () {
                if (window.october.isSingleton(module)) {
                    return callExtendable(window.october.getModuleInstance(module), module, arguments)
                }
                return callExtendable(this, module, arguments)
            }
            extension.prototype.callAttachable = function () {
                if (window.october.isSingleton(module)) {
                    return callAttachable(window.october.getModuleInstance(module), module, arguments)
                }
                return callAttachable(this, module, arguments)
            }

            this._modules[module] = {
                instance: null,
                singleton: false,
                source: extension,
                extensions: []
            }

            if (extension.prototype.singleton === true) {
                this._modules[module].instance = new extension
                this._modules[module].singleton = true
                this[module] = this[module.toLowerCase()] = this._modules[module].instance
            } else {
                this[module] = this[module.toLowerCase()] = function () {
                    var instance = this.getModule(module)
                    var args = [null].concat(Array.prototype.slice.call(arguments));
                    var factory = instance.bind.apply(instance, args)

                    return new factory()
                }
            }
        }
    }

    OctoberJsFramework.prototype.hasModule = function (module) {
        return this._modules.hasOwnProperty(module)
    }

    OctoberJsFramework.prototype.getModule = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the October CMS JS framework')
        }

        return this._modules[module].source
    }

    OctoberJsFramework.prototype.isSingleton = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the October CMS JS framework')
        }

        return this._modules[module].singleton === true
    }

    OctoberJsFramework.prototype.getModuleInstance = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the October CMS JS framework')
        }

        return this._modules[module].instance
    }

    OctoberJsFramework.prototype.getModuleExtensions = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the October CMS JS framework')
        }

        return this._modules[module].extensions
    }

    OctoberJsFramework.prototype.moduleIsExtendable = function (module) {
        var sourceModule = this.getModule(module)

        return (
            sourceModule.prototype.hasOwnProperty('extendable')
            && Array.isArray(sourceModule.prototype.extendable)
        )
    }

    OctoberJsFramework.prototype.moduleIsAttachable = function (module) {
        var sourceModule = this.getModule(module)

        return (
            sourceModule.prototype.hasOwnProperty('attachable')
            && Array.isArray(sourceModule.prototype.attachable)
        )
    }

    var callExtendable = function (instance, module, args) {
        var extensions = window.october.getModuleExtensions(module),
            params = Array.prototype.slice.call(args),
            method = params.shift()

        for (var ext in extensions) {
            if (extensions[ext].methods.hasOwnProperty(method)) {
                var returned = extensions[ext].methods[method].apply(instance, params);
                if (returned !== false) {
                    return returned
                }
            }
        }
    }

    var callAttachable = function (instance, module, args) {
        var extensions = window.october.getModuleExtensions(module),
            params = Array.prototype.slice.call(args),
            method = params.shift()

        for (var ext in extensions) {
            if (extensions[ext].events.hasOwnProperty(method)) {
                var returned = extensions[ext].events[method].apply(instance, params);
                if (returned === false) {
                    break
                }
            }
        }
    }

    // Define OctoberJs in global namespace
    window.october = new OctoberJsFramework()
}(window);

/**
 * October CMS JavaScript Request module.
 *
 * @copyright 2016-2021 Alexey Bobkov, Samuel Georges, Luke Towers
 * @author Ben Thomson <git@alfreido.com>
 * @link https://octobercms.com
 */
+function (window) {
    var Request = function () {
        var args = Array.prototype.slice.call(arguments)

        this.initialize(args);

        this.element = this.processElement(this.element)
        this.handler = this.processHandler(this.handler)
        this.options = this.processOptions(this.options)

        console.log(this.element)
        console.log(this.handler)
        console.log(this.options)

        // Validate handler
        if (handler === undefined) {
            throw new Error('The request handler name is not specified.')
        }

        if (!handler.match(/^(?:\w+\:{2})?on*/)) {
            throw new Error('Invalid handler name. The correct handler name format is: "onEvent".')
        }
    }

    Request.prototype.extendable = [
        'initialize',
        'processElement',
        'processHandler',
        'processOptions',
        'getForm',
    ]

    Request.prototype.initialize = function (args) {
        this.callExtendable('initialize', args)
    }

    Request.prototype.processElement = function (element) {
        return this.callExtendable('processElement', element)
    }

    Request.prototype.processHandler = function (handler) {
        console.log(handler)
        var handlerName = this.callExtendable('processHandler', handler)
        console.log(handlerName)

        if (handlerName === undefined) {
            throw new Error('The request handler name is not specified.')
        }

        if (!handlerName.match(/^(?:\w+\:{2})?on*/)) {
            throw new Error('Invalid handler name. The correct handler name format is: "onEvent".')
        }

        return handlerName
    }

    Request.prototype.processOptions = function (options) {
        var opts = this.callExtendable('processOptions', options)

        return opts
    }

    window.october.extend('Request', Request)
}(window);

// Test extension
+function (window) {
    window.october.extend('Request', {
        initialize: function (args) {
            this.element = null
            this.handler = args[0]
            this.options = args[1] || {}
        }
    })
}(window);

window.october.request('onTest')
