/* globals window, console */

/**
 * Winter CMS JavaScript Request module.
 *
 * @copyright 2021 Winter CMS
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com
 */

if (!window.october) {
    throw new Error('The Winter CMS JS framework base must be loaded before the Request module can be registered.')
}

(function (winter, window) {
    'use strict';

    var Request = function () {
        var args = Array.prototype.slice.call(arguments)

        // Set defaults
        this.element = null
        this.form = null
        this.handler = null
        this.options = {}

        // Allow properties to be processed
        var element = this.processElement(args),
            handler = this.processHandler(args),
            options = this.processOptions(args),
            headers = this.processHeaders(args),
            data = this.processData(args)

        this.request = this.createRequest(element, handler, options, headers, data)
    }

    Request.prototype.extendable = [
        'processElement',
        'createRequest',
        'getForm',
    ]

    Request.prototype.attachable = [
        'processHandler',
        'processOptions',
        'processHeaders',
        'processData',
    ]

    Request.prototype.processElement = function (args) {
        this.element = this.callExtendable('processElement', args)

        return this.element
    }

    Request.prototype.getForm = function (args, element) {
        this.form = this.callExtendable('getForm', args, element)

        return this.form
    }

    Request.prototype.processHandler = function (args) {
        this.handler = this.callAttachable('processHandler', null, args)

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

    Request.prototype.processOptions = function (args) {
        this.options = {
            ajaxGlobal: false,
            beforeUpdate: null,
            browserValidate: false,
            confirm: false,
            files: false,
            flash: false,
            form: this.getForm(args, this.element),
            loading: false,
            redirect: [],
            url: window.location.href,
        }

        this.options = this.callAttachable('processOptions', this.options, args)

        if (this.options.files !== undefined && typeof FormData === 'undefined') {
            console.warn('This browser does not support file uploads via FormData')
            this.options.files = false
        }

        return this.options
    }

    Request.prototype.processHeaders = function (args) {
        this.headers = {
            'X-WINTER-REQUEST-HANDLER': this.handler,
            'X-WINTER-REQUEST-PARTIALS': this.extractPartials(this.options.update),
        }

        if (this.options.flash !== false && this.options.flash !== undefined) {
            this.headers['X-WINTER-REQUEST-FLASH'] = 1
        }

        var csrfToken = getXSRFToken()
        if (csrfToken) {
            this.headers['X-XSRF-TOKEN'] = csrfToken
        }

        this.callAttachable('processHeaders', args)

        return this.headers
    }

    Request.prototype.processData = function (args) {
        this.data = {}

        this.callAttachable('processData', args)

        return this.data
    }

    Request.prototype.extractPartials = function (update) {
        var result = []

        for (var partial in update) {
            result.push(partial)
        }

        return result.join('&')
    }

    Request.prototype.createRequest = function (element, handler, options, headers, data) {
        return this.callExtendable('createRequest', element, handler, options, headers, data)
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

    // Extend the Winter CMS JS framework
    winter.extend('Request', Request)
}(window.winter, window));
