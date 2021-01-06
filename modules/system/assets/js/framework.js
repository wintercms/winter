/* globals window, console */

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

    var callExtendable = function (instance, module, args) {
        var extensions = window.october.getModuleExtensions(module),
            params = Array.prototype.slice.call(args),
            method = params.shift()

        for (var ext in extensions) {
            if (extensions[ext].methods.hasOwnProperty(method)) {
                var returned = extensions[ext].methods[method].apply(instance, params)
                if (returned !== false) {
                    return returned
                }
            }
        }

        return undefined
    }

    var callAttachable = function (instance, module, args) {
        var extensions = window.october.getModuleExtensions(module),
            params = Array.prototype.slice.call(args),
            method = params.shift()

        for (var ext in extensions) {
            if (extensions[ext].events.hasOwnProperty(method)) {
                var returned = extensions[ext].events[method].apply(instance, params)
                if (returned === false) {
                    break
                }
            }
        }
    }

    // Define OctoberJs in global namespace
    window.october = new OctoberJsFramework()
}(window));

/**
 * October CMS JavaScript Request module.
 *
 * @copyright 2016-2021 Alexey Bobkov, Samuel Georges, Luke Towers
 * @author Ben Thomson <git@alfreido.com>
 * @link https://octobercms.com
 */
(function (window) {
    'use strict';

    var Request = function () {
        var args = Array.prototype.slice.call(arguments)

        this.initialize(args)

        // Allow properties to be processed
        var element = this.processElement(),
            handler = this.processHandler(),
            options = this.processOptions(),
            headers = this.processHeaders(),
            data = this.processData()

        this.request = this.createRequest(element, handler, options, headers, data)
    }

    Request.prototype.extendable = [
        'initialize',
        'processElement',
        'getForm',
    ]

    Request.prototype.attachable = [
        'processHandler',
        'processOptions',
        'processHeaders',
        'processData',
    ]

    Request.prototype.initialize = function (args) {
        this.callExtendable('initialize', args)
    }

    Request.prototype.processElement = function () {
        this.element = undefined

        return this.callExtendable('processElement')
    }

    Request.prototype.getForm = function (element) {
        return this.callExtendable('getForm', element)
    }

    Request.prototype.processHandler = function () {
        this.handler = undefined

        this.callAttachable('processHandler')

        if (this.handler === undefined) {
            throw new Error('The request handler name is not specified.')
        }

        if (typeof this.handler !== 'string') {
            throw new Error('The request handler must be specified as a string.')
        }

        if (!this.handler.match(/^(?:\w+\:{2})?on*/)) {
            throw new Error('Invalid handler name. The correct handler name format is: "on[Handler]" - eg. "onEvent".')
        }

        return this.handler
    }

    Request.prototype.processOptions = function () {
        this.options = {
            ajaxGlobal: false,
            beforeUpdate: null,
            browserValidate: false,
            confirm: false,
            files: false,
            flash: false,
            form: this.getForm(this.element),
            loading: false,
            redirect: [],
            url: window.location.href,
        }

        this.callAttachable('processOptions')

        if (this.options.files !== undefined && typeof FormData === 'undefined') {
            console.warn('This browser does not support file uploads via FormData')
            this.options.files = false
        }

        return this.options
    }

    Request.prototype.processHeaders = function () {
        this.headers = {
            'X-OCTOBER-REQUEST-HANDLER': this.handler,
            'X-OCTOBER-REQUEST-PARTIALS': this.extractPartials(this.options.update),
        }

        if (this.options.flash !== false && this.options.flash !== undefined) {
            this.headers['X-OCTOBER-REQUEST-FLASH'] = 1
        }

        var csrfToken = getXSRFToken()
        if (csrfToken) {
            this.headers['X-XSRF-TOKEN'] = csrfToken
        }

        this.callAttachable('processHeaders')

        return this.headers
    }

    Request.prototype.processData = function () {
        this.data = {}

        this.callAttachable('processData')

        return this.data
    }

    function getXSRFToken() {
        var cookieValue = null

        if (window.document.cookie && window.document.cookie != '') {
            var cookies = window.document.cookie.split(';')
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim()

                if (cookie.substring(0, 11) == ('XSRF-TOKEN' + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(11))
                    break
                }
            }
        }

        return cookieValue
    }

    window.october.extend('Request', Request)
}(window));

// jQuery AJAX framework extension
(function (october, document, $) {
    'use strict';

    october.extend('Request', {
        boot: function () {
            // Create request function in jQuery
            $.fn.request = function(handler, option) {
                var $this = $(this).first()
                var data  = {
                    evalBeforeUpdate: $this.data('request-before-update'),
                    evalSuccess: $this.data('request-success'),
                    evalError: $this.data('request-error'),
                    evalComplete: $this.data('request-complete'),
                    ajaxGlobal: $this.data('request-ajax-global'),
                    confirm: $this.data('request-confirm'),
                    redirect: $this.data('request-redirect'),
                    loading: $this.data('request-loading'),
                    flash: $this.data('request-flash'),
                    files: $this.data('request-files'),
                    browserValidate: $this.data('browser-validate'),
                    form: $this.data('request-form'),
                    url: $this.data('request-url'),
                    // update: paramToObj('data-request-update', $this.data('request-update')),
                    // data: paramToObj('data-request-data', $this.data('request-data'))
                }
                if (!handler) {
                    handler = $this.data('request')
                }

                var options = $.extend(true, {}, october.request.DEFAULTS, data, typeof option == 'object' && option)

                return october.request($this, handler, options)
            }

            $.request = function(handler, option) {
                return $(document).request(handler, option)
            }

            /*
            * Invent our own event that unifies document.ready with window.ajaxUpdateComplete
            *
            * $(document).render(function() { })
            * $(document).on('render', function() { })
            */
            $(function triggerRenderOnReady() {
                $(document).trigger('render')
            })

            $(window).on('ajaxUpdateComplete', function triggerRenderOnAjaxUpdateComplete() {
                $(document).trigger('render')
            })

            $.fn.render = function(callback) {
                $(document).on('render', callback)
            }
        },
        initialize: function (args) {
            this.element = null
            this.handler = args[0]
            this.options = args[1] || {}
        },
    })

    function paramToObj (name, value) {
        if (value === undefined) {
            value = ''
        }
        if (typeof value == 'object') {
            return value
        }

        try {
            return ocJSON('{' + value + '}')
        } catch (e) {
            throw new Error('Error parsing the ' + name + ' attribute value. ' + e)
        }
    }

    function ocJSON (json) {
        var jsonString = parse(json);
        return JSON.parse(jsonString);
    }
}(window.october, window.document, jQuery));

// Data Request API extension
(function (october, document, $) {
    'use strict';

    october.extend('Request', {
        boot: function () {
            // Attach to elements for data requests
            $(document).on('submit', '[data-request]', function documentOnSubmit() {
                $(this).request()
                return false
            })
        },
    })
}(window.october, window.document, jQuery));
