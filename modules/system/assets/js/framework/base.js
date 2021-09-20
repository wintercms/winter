/* globals window */

/**
 * Winter CMS JavaScript framework.
 *
 * This class represents a new take on the Winter CMS JS framework, being fully modular and removing the dependency
 * for jQuery.
 *
 * @copyright 2021 Winter CMS
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com
 */
(function (window) {
    'use strict';

    // Do not double-instatiate
    if (window.winter) {
        return
    }

    var WinterFramework = function () {
        this._modules = {}
    }

    /**
     * Extends the Winter CMS JavaScript framework with a module.
     *
     * Extensions provide functionality for extendable methods and attachable methods. Extendable methods are
     * intended to be once-off implemented - ie. the first plugin that provides the method handles it. Attachable
     * methods can be listened by multiple extensions, similar in handling to events.
     *
     * @param {String} module
     * @param {(Object|Function)} extension
     */
    WinterFramework.prototype.extend = function (module, extension) {
        var winter = this

        if (typeof module !== 'string') {
            throw new Error('Module name must be specified as a string.')
        }
        if ((typeof extension !== 'object' && typeof extension !== 'function') || extension === null) {
            throw new Error('Module extension must be either an object or function that returns an object.')
        }

        // Add extension and attached method calls
        if (this.isExtentable(extension) || this.isAttachable(extension)) {
            if (extension.hasOwnProperty('extend')) {
                throw new Error(
                    'Module "' + module + '" is extendable but the "extend" property is already set. '
                    + 'Please ensure that the "extend" property is undefined.'
                )
            }

            extension.prototype.extend = function (name, childExtension) {
                var sourceModule = winter.getModule(module),
                    extensionMethods = {},
                    extendableMethods = sourceModule.prototype.extendable || [],
                    attachableMethods = sourceModule.prototype.attachable || [],
                    extendedMethods = {},
                    attachedMethods = {},
                    extensionObj = {}

                if (typeof childExtension === 'function') {
                    extensionMethods = Object.create({}, childExtension.call(sourceModule))
                } else {
                    extensionMethods = childExtension
                }

                // Allow for once-off boot functionality
                if (
                    extensionMethods.hasOwnProperty('boot')
                    && typeof extensionMethods.boot === 'function'
                ) {
                    extensionMethods.boot()
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
                    name: name,
                    methods: extendedMethods,
                    events: attachedMethods,
                }

                window.winter._modules[module].extensions.push(extensionObj)
            }

            extension.prototype.removeExtension = function (name) {
                if (window.winter.moduleHasExtension(module, name)) {
                    window.winter.removeModuleExtension(module, name)
                }
            }

            extension.prototype.removeExtensions = function () {
                window.winter.removeModuleExtensions(module)
            }
        }

        if (this.isExtentable(extension)) {
            if (extension.hasOwnProperty('callExtendable')) {
                throw new Error(
                    'Module "' + module + '" is extendable but the "callExtendable" property is already set. '
                    + 'Please ensure that the "callExtendable" property is undefined.'
                )
            }

            extension.prototype.callExtendable = function () {
                var args = Array.prototype.slice.call(arguments),
                    method = args.shift()

                if (this.extendable.indexOf(method) === -1) {
                    throw new Error('Module "' + module + '" does not extend the "' + method + '" method.')
                }

                if (window.winter.isSingleton(module)) {
                    return callExtendable(window.winter.getModuleInstance(module), module, method, args)
                }

                return callExtendable(this, module, method, args)
            }
        }

        if (this.isAttachable(extension)) {
            if (extension.hasOwnProperty('callAttachable')) {
                throw new Error(
                    'Module "' + module + '" is attachable but the "callAttachable" property is already set. '
                    + 'Please ensure that the "callAttachable" property is undefined.'
                )
            }

            extension.prototype.callAttachable = function () {
                var args = Array.prototype.slice.call(arguments),
                    method = args.shift(),
                    passThrough = args.shift()

                if (window.winter.isSingleton(module)) {
                    return callAttachable(window.winter.getModuleInstance(module), module, method, passThrough, args)
                }
                return callAttachable(this, module, method, passThrough, args)
            }
        }

        this._modules[module] = {
            instance: null,
            singleton: false,
            source: extension,
            extensions: [],
        }

        // Allow for once-off boot functionality
        if (
            extension.prototype.hasOwnProperty('boot')
            && typeof extension.prototype.boot === 'function'
        ) {
            extension.boot()
        }

        if (extension.prototype.singleton === true) {
            this._modules[module].instance = new extension()
            this._modules[module].singleton = true
            this[module] = this[module.toLowerCase()] = this._modules[module].instance
        } else {
            this[module] = this[module.toLowerCase()] = function () {
                var instance = this.getModule(module)
                var args = [null,].concat(Array.prototype.slice.call(arguments))
                var factory = instance.bind.apply(instance, args)

                return new factory()
            }
        }
    }

    WinterFramework.prototype.hasModule = function (module) {
        return this._modules.hasOwnProperty(module)
    }

    WinterFramework.prototype.getModule = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the Winter CMS JS framework')
        }

        return this._modules[module].source
    }

    WinterFramework.prototype.removeModule = function (module) {
        if (!this.hasModule(module)) {
            return
        }

        delete this._modules[module]
        delete this[module]
        delete this[module.toLowerCase()]
    }

    WinterFramework.prototype.isSingleton = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the Winter CMS JS framework')
        }

        return this._modules[module].singleton === true
    }

    WinterFramework.prototype.getModuleInstance = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the Winter CMS JS framework')
        }

        return this._modules[module].instance
    }

    WinterFramework.prototype.getModuleExtensions = function (module) {
        if (!this.hasModule(module)) {
            throw new Error('Module "' + module + '" has not been defined in the Winter CMS JS framework')
        }

        return this._modules[module].extensions
    }

    WinterFramework.prototype.isExtentable = function (extension) {
        return (
            extension.prototype.hasOwnProperty('extendable')
            && Array.isArray(extension.prototype.extendable)
        )
    }

    WinterFramework.prototype.moduleIsExtendable = function (module) {
        return this.isExtentable(this.getModule(module))
    }

    WinterFramework.prototype.isAttachable = function (extension) {
        return (
            extension.prototype.hasOwnProperty('attachable')
            && Array.isArray(extension.prototype.attachable)
        )
    }

    WinterFramework.prototype.moduleIsAttachable = function (module) {
        return this.isAttachable(this.getModule(module))
    }

    WinterFramework.prototype.moduleHasExtension = function (module, extension) {
        if (!this.hasModule(module)) {
            return false
        }

        var hasExtension = false;

        for (var i in this._modules[module].extensions) {
            if (this._modules[module].extensions[i].name === extension) {
                hasExtension = true
                break
            }
        }

        return hasExtension
    }

    var callExtendable = function (instance, module, method, args) {
        var extensions = window.winter.getModuleExtensions(module)

        for (var ext in extensions) {
            if (extensions[ext].methods.hasOwnProperty(method)) {
                var returned = extensions[ext].methods[method].apply(instance, args)
                if (returned !== false) {
                    return returned
                }
            }
        }

        return undefined
    }

    var callAttachable = function (instance, module, method, passThrough, args) {
        var extensions = window.winter.getModuleExtensions(module),
            extParams,
            extReturned

        for (var ext in extensions) {
            if (extensions[ext].events.hasOwnProperty(method)) {
                // Ensure that the first parameter is always the currently returned value
                extParams = args
                extParams.unshift(passThrough)

                extReturned = extensions[ext].events[method].apply(instance, extParams)
                if (extReturned === null) {
                    break
                }
                if (extReturned !== null) {
                    passThrough = extReturned
                }
            }
        }

        return passThrough
    }

    // Polyfill for missing Object.assign method in Internet Explorer
    if (typeof Object.assign !== 'function') {
        // Must be writable: true, enumerable: false, configurable: true
        Object.defineProperty(Object, 'assign', {
            value: function assign(target) {
                if (target === null || target === undefined) {
                    throw new TypeError('Cannot convert undefined or null to object')
                }

                var to = Object(target)

                for (var index = 1; index < arguments.length; index++) {
                    var nextSource = arguments[index]

                    if (nextSource !== null && nextSource !== undefined) {
                        for (var nextKey in nextSource) {
                            // Avoid bugs when hasOwnProperty is shadowed
                            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                to[nextKey] = nextSource[nextKey]
                            }
                        }
                    }
                }

                return to
            },
            writable: true,
            configurable: true,
        })
    }

    // Define Winter JS framework in global namespace
    window.winter = new WinterFramework()
}(window));
