/* globals window */

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
(function (window) {
    'use strict';

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
                methods: extendedMethods,
                events: attachedMethods,
            }

            this._modules[module].extensions.push(extensionObj)
        } else {
            // We are extending the framework itself

            // Add extension and attached method calls
            extension.prototype.callExtendable = function () {
                var args = Array.prototype.slice.call(arguments),
                    method = args.shift()

                if (window.october.isSingleton(module)) {
                    return callExtendable(window.october.getModuleInstance(module), module, method, args)
                }
                return callExtendable(this, module, method, args)
            }
            extension.prototype.callAttachable = function () {
                var args = Array.prototype.slice.call(arguments),
                    method = args.shift(),
                    passThrough = args.shift()

                if (window.october.isSingleton(module)) {
                    return callAttachable(window.october.getModuleInstance(module), module, method, passThrough, args)
                }
                return callAttachable(this, module, method, passThrough, args)
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

    var callExtendable = function (instance, module, method, args) {
        var extensions = window.october.getModuleExtensions(module)

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
        var extensions = window.october.getModuleExtensions(module),
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
          value: function assign(target, varArgs) { // .length of function is 2
            if (target === null || target === undefined) {
              throw new TypeError('Cannot convert undefined or null to object');
            }

            var to = Object(target);

            for (var index = 1; index < arguments.length; index++) {
              var nextSource = arguments[index];

              if (nextSource !== null && nextSource !== undefined) {
                for (var nextKey in nextSource) {
                  // Avoid bugs when hasOwnProperty is shadowed
                  if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                    to[nextKey] = nextSource[nextKey];
                  }
                }
              }
            }
            return to;
          },
          writable: true,
          configurable: true,
        });
      }


    // Define OctoberJs in global namespace
    window.october = new OctoberJsFramework()
}(window));
