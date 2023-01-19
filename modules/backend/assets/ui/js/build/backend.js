/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/ui/js/ajax/Handler.js":
/*!**************************************!*\
  !*** ./assets/ui/js/ajax/Handler.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Handler)
/* harmony export */ });
/* harmony import */ var jquery_events_to_dom_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery-events-to-dom-events */ "../../node_modules/jquery-events-to-dom-events/dist/index.m.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


/**
 * Backend AJAX handler.
 *
 * This is a utility script that resolves some backwards-compatibility issues with the functionality
 * that relies on the old framework, and ensures that Snowboard works well within the Backend
 * environment.
 *
 * Functions:
 *  - Adds the "render" jQuery event to Snowboard requests that widgets use to initialise.
 *  - Ensures the CSRF token is included in requests.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Handler = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(Handler, _Snowboard$Singleton);
  var _super = _createSuper(Handler);
  function Handler() {
    _classCallCheck(this, Handler);
    return _super.apply(this, arguments);
  }
  _createClass(Handler, [{
    key: "listens",
    value:
    /**
     * Event listeners.
     *
     * @returns {Object}
     */
    function listens() {
      return {
        ready: 'ready',
        ajaxFetchOptions: 'ajaxFetchOptions',
        ajaxUpdateComplete: 'ajaxUpdateComplete'
      };
    }

    /**
     * Ready handler.
     *
     * Fires off a "render" event.
     */
  }, {
    key: "ready",
    value: function ready() {
      var _this = this;
      if (!window.jQuery) {
        return;
      }
      (0,jquery_events_to_dom_events__WEBPACK_IMPORTED_MODULE_0__.delegate)('render');

      // Add "render" event for backwards compatibility
      window.jQuery(document).trigger('render');

      // Add global event for rendering in Snowboard
      document.addEventListener('$render', function () {
        _this.snowboard.globalEvent('render');
      });
    }

    /**
     * Adds the jQuery AJAX prefilter that the old framework uses to inject the CSRF token in AJAX
     * calls.
     */
  }, {
    key: "addPrefilter",
    value: function addPrefilter() {
      var _this2 = this;
      if (!window.jQuery) {
        return;
      }
      window.jQuery.ajaxPrefilter(function (options) {
        if (_this2.hasToken()) {
          if (!options.headers) {
            options.headers = {};
          }
          options.headers['X-CSRF-TOKEN'] = _this2.getToken();
        }
      });
    }

    /**
     * Fetch options handler.
     *
     * Ensures that the CSRF token is included in Snowboard requests.
     *
     * @param {Object} options
     */
  }, {
    key: "ajaxFetchOptions",
    value: function ajaxFetchOptions(options) {
      if (this.hasToken()) {
        options.headers['X-CSRF-TOKEN'] = this.getToken();
      }
    }

    /**
     * Update complete handler.
     *
     * Fires off a "render" event when partials are updated so that any widgets included in
     * responses are correctly initialised.
     */
  }, {
    key: "ajaxUpdateComplete",
    value: function ajaxUpdateComplete() {
      if (!window.jQuery) {
        return;
      }

      // Add "render" event for backwards compatibility
      window.jQuery(document).trigger('render');
    }

    /**
     * Determines if a CSRF token is available.
     *
     * @returns {Boolean}
     */
  }, {
    key: "hasToken",
    value: function hasToken() {
      var tokenElement = document.querySelector('meta[name="csrf-token"]');
      if (!tokenElement) {
        return false;
      }
      if (!tokenElement.hasAttribute('content')) {
        return false;
      }
      return true;
    }

    /**
     * Gets the CSRF token.
     *
     * @returns {String}
     */
  }, {
    key: "getToken",
    value: function getToken() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
  }]);
  return Handler;
}(Snowboard.Singleton);


/***/ }),

/***/ "./assets/ui/js/index.js":
/*!*******************************!*\
  !*** ./assets/ui/js/index.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ajax_Handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ajax/Handler */ "./assets/ui/js/ajax/Handler.js");
/* harmony import */ var _ui_EventHandler__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ui/EventHandler */ "./assets/ui/js/ui/EventHandler.js");
/* harmony import */ var _ui_WidgetHandler__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ui/WidgetHandler */ "./assets/ui/js/ui/WidgetHandler.js");



if (window.Snowboard === undefined) {
  throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}
(function (Snowboard) {
  Snowboard.addPlugin('backend.ajax.handler', _ajax_Handler__WEBPACK_IMPORTED_MODULE_0__["default"]);
  Snowboard.addPlugin('backend.ui.eventHandler', _ui_EventHandler__WEBPACK_IMPORTED_MODULE_1__["default"]);
  Snowboard.addPlugin('backend.ui.widgetHandler', _ui_WidgetHandler__WEBPACK_IMPORTED_MODULE_2__["default"]);

  // Add the pre-filter immediately
  Snowboard['backend.ajax.handler']().addPrefilter();

  // Add polyfill for AssetManager
  window.AssetManager = {
    load: function load(assets, callback) {
      Snowboard.assetLoader().load(assets).then(function () {
        if (callback && typeof callback === 'function') {
          callback();
        }
      });
    }
  };
  window.assetManager = window.AssetManager;
})(window.Snowboard);

/***/ }),

/***/ "./assets/ui/js/ui/EventHandler.js":
/*!*****************************************!*\
  !*** ./assets/ui/js/ui/EventHandler.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ EventHandler)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
/**
 * Widget event handler.
 *
 * Extends a widget with event handling functionality, allowing for the quick definition of events
 * and listening for events on a specific instance of a widget.
 *
 * This is a complement to Snowboard's global events - these events will still fire in order to
 * allow external code to listen and handle events. Local events can cancel the global event (and
 * further local events) by returning `false` from the callback.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var EventHandler = /*#__PURE__*/function (_Snowboard$PluginBase) {
  _inherits(EventHandler, _Snowboard$PluginBase);
  var _super = _createSuper(EventHandler);
  function EventHandler() {
    _classCallCheck(this, EventHandler);
    return _super.apply(this, arguments);
  }
  _createClass(EventHandler, [{
    key: "construct",
    value:
    /**
     * Constructor.
     *
     * @param {PluginBase} instance
     * @param {String} eventPrefix
     */
    function construct(instance, eventPrefix) {
      if (instance instanceof Snowboard.PluginBase === false) {
        throw new Error('Event handling can only be applied to Snowboard classes.');
      }
      if (!eventPrefix) {
        throw new Error('Event prefix is required.');
      }
      this.instance = instance;
      this.eventPrefix = eventPrefix;
      this.events = [];
    }

    /**
     * Registers a listener for a widget's event.
     *
     * @param {String} event
     * @param {Function} callback
     */
  }, {
    key: "on",
    value: function on(event, callback) {
      this.events.push({
        event: event,
        callback: callback
      });
    }

    /**
     * Deregisters a listener for a widget's event.
     *
     * @param {String} event
     * @param {Function} callback
     */
  }, {
    key: "off",
    value: function off(event, callback) {
      this.events = this.events.filter(function (registeredEvent) {
        return registeredEvent.event !== event || registeredEvent.callback !== callback;
      });
    }

    /**
     * Registers a listener for a widget's event that will only fire once.
     *
     * @param {String} event
     * @param {Function} callback
     */
  }, {
    key: "once",
    value: function once(event, _callback) {
      var _this = this;
      var length = this.events.push({
        event: event,
        callback: function callback() {
          _callback.apply(void 0, arguments);
          _this.events.splice(length - 1, 1);
        }
      });
    }

    /**
     * Fires an event on the widget.
     *
     * Local events are fired first, then a global event is fired afterwards.
     *
     * @param {String} eventName
     * @param  {...any} parameters
     */
  }, {
    key: "fire",
    value: function fire(eventName) {
      for (var _len = arguments.length, parameters = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        parameters[_key - 1] = arguments[_key];
      }
      // Fire local events first
      var events = this.events.filter(function (registeredEvent) {
        return registeredEvent.event === eventName;
      });
      var cancelled = false;
      events.forEach(function (event) {
        if (cancelled) {
          return;
        }
        if (event.callback.apply(event, parameters) === false) {
          cancelled = true;
        }
      });
      if (!cancelled) {
        var _this$snowboard;
        (_this$snowboard = this.snowboard).globalEvent.apply(_this$snowboard, ["".concat(this.eventPrefix, ".").concat(eventName)].concat(parameters));
      }
    }

    /**
     * Fires a promise event on the widget.
     *
     * Local events are fired first, then a global event is fired afterwards.
     *
     * @param {String} eventName
     * @param  {...any} parameters
     */
  }, {
    key: "firePromise",
    value: function firePromise(eventName) {
      var _this2 = this;
      for (var _len2 = arguments.length, parameters = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        parameters[_key2 - 1] = arguments[_key2];
      }
      var events = this.events.filter(function (registeredEvent) {
        return registeredEvent.event === eventName;
      });
      var promises = events.filter(function (event) {
        return event !== null;
      }, events.map(function (event) {
        return event.callback.apply(event, parameters);
      }));
      Promise.all(promises).then(function () {
        var _this2$snowboard;
        (_this2$snowboard = _this2.snowboard).globalPromiseEvent.apply(_this2$snowboard, ["".concat(_this2.eventPrefix, ".").concat(eventName)].concat(parameters));
      });
    }
  }]);
  return EventHandler;
}(Snowboard.PluginBase);


/***/ }),

/***/ "./assets/ui/js/ui/WidgetHandler.js":
/*!******************************************!*\
  !*** ./assets/ui/js/ui/WidgetHandler.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ WidgetHandler)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
/**
 * Backend widget handler.
 *
 * Handles the creation and disposal of widgets in the Backend. Widgets should include this as
 * a dependency in order to be loaded and initialised after the handler, in order to correctly
 * register.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var WidgetHandler = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(WidgetHandler, _Snowboard$Singleton);
  var _super = _createSuper(WidgetHandler);
  function WidgetHandler() {
    _classCallCheck(this, WidgetHandler);
    return _super.apply(this, arguments);
  }
  _createClass(WidgetHandler, [{
    key: "construct",
    value:
    /**
     * Constructor.
     */
    function construct() {
      this.registeredWidgets = [];
      this.elements = [];
    }

    /**
     * Listeners.
     *
     * @returns {Object}
     */
  }, {
    key: "listens",
    value: function listens() {
      return {
        ready: 'onReady',
        render: 'onReady',
        ajaxUpdate: 'onAjaxUpdate'
      };
    }

    /**
     * Registers a widget as a given data control.
     *
     * Registering a widget will allow any element that contains a "data-control" attribute matching
     * the control name to be initialized with the given widget.
     *
     * You may optionally provide a callback that will be fired when an instance of the widget is
     * initialized - the callback will be provided the element and the widget instance as parameters.
     *
     * @param {String} control
     * @param {Snowboard.PluginBase} widget
     * @param {Function} callback
     */
  }, {
    key: "register",
    value: function register(control, widget, callback) {
      this.registeredWidgets.push({
        control: control,
        widget: widget,
        callback: callback
      });
    }

    /**
     * Unregisters a data control.
     *
     * @param {String} control
     */
  }, {
    key: "unregister",
    value: function unregister(control) {
      this.registeredWidgets = this.registeredWidgets.filter(function (widget) {
        return widget.control !== control;
      });
    }

    /**
     * Ready handler.
     *
     * Initializes widgets within the entire document.
     */
  }, {
    key: "onReady",
    value: function onReady() {
      this.initializeWidgets(document.body);
    }

    /**
     * AJAX update handler.
     *
     * Initializes widgets inside an update element from an AJAX response.
     *
     * @param {HTMLElement} element
     */
  }, {
    key: "onAjaxUpdate",
    value: function onAjaxUpdate(element) {
      this.initializeWidgets(element);
    }

    /**
     * Initializes all widgets within an element.
     *
     * If an element contains a "data-control" attribute matching a registered widget, the widget
     * is initialized and attached to the element as a "widget" property.
     *
     * Only one widget may be initialized to a particular element.
     *
     * @param {HTMLElement} element
     */
  }, {
    key: "initializeWidgets",
    value: function initializeWidgets(element) {
      var _this = this;
      this.registeredWidgets.forEach(function (widget) {
        var instances = element.querySelectorAll("[data-control=\"".concat(widget.control, "\"]:not([data-widget-initialized])"));
        if (instances.length) {
          instances.forEach(function (instance) {
            // Prevent double-widget initialization
            if (instance.dataset.widgetInitialized) {
              return;
            }
            var widgetInstance = _this.snowboard[widget.widget](instance);
            _this.elements.push({
              element: instance,
              instance: widgetInstance
            });
            instance.dataset.widgetInitialized = true;
            _this.snowboard.globalEvent('backend.widget.initialized', instance, widgetInstance);
            if (typeof widget.callback === 'function') {
              widget.callback(widgetInstance, instance);
            }
          });
        }
      });
    }

    /**
     * Returns a widget that is attached to the given element, if any.
     *
     * @param {HTMLElement} element
     * @returns {Snowboard.PluginBase|null}
     */
  }, {
    key: "getWidget",
    value: function getWidget(element) {
      var found = this.elements.find(function (widget) {
        return widget.element === element;
      });
      if (found) {
        return found.instance;
      }
      return null;
    }
  }]);
  return WidgetHandler;
}(Snowboard.Singleton);


/***/ }),

/***/ "../../node_modules/jquery-events-to-dom-events/dist/index.m.js":
/*!**********************************************************************!*\
  !*** ../../node_modules/jquery-events-to-dom-events/dist/index.m.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "abnegate": () => (/* binding */ e),
/* harmony export */   "delegate": () => (/* binding */ t)
/* harmony export */ });
var t=function(t,e){void 0===e&&(e=["event"]);var n=t.startsWith("$")?function(){return window.$(document).trigger(t.slice(1),[].slice.call(arguments)[0].detail)}:function(){var n=arguments,i=e.reduce(function(t,e,i){return t[e]=[].slice.call(n)[i],t},{});i.event.target.dispatchEvent(new CustomEvent("$"+t,{detail:i,bubbles:!0,cancelable:!0}))};return t.startsWith("$")?document.addEventListener(t,n):window.$(document).on(t,n),n},e=function(t,e){t.startsWith("$")?document.removeEventListener(t,e):window.$(document).off(t,e)};
//# sourceMappingURL=index.m.js.map


/***/ }),

/***/ "./formwidgets/visualeditor/assets/less/visualeditor.less":
/*!****************************************************************!*\
  !*** ./formwidgets/visualeditor/assets/less/visualeditor.less ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/assets/ui/js/build/backend": 0,
/******/ 			"formwidgets/visualeditor/assets/css/visualeditor": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk_wintercms_wn_backend_module"] = self["webpackChunk_wintercms_wn_backend_module"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["formwidgets/visualeditor/assets/css/visualeditor"], () => (__webpack_require__("./assets/ui/js/index.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["formwidgets/visualeditor/assets/css/visualeditor"], () => (__webpack_require__("./formwidgets/visualeditor/assets/less/visualeditor.less")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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SSxxRkFBcUYsaUJBQWlCLGlGQUFxSDtBQUNyakI7Ozs7Ozs7Ozs7OztBQ0RBOzs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOzs7OztXQ3pCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLCtCQUErQix3Q0FBd0M7V0FDdkU7V0FDQTtXQUNBO1dBQ0E7V0FDQSxpQkFBaUIscUJBQXFCO1dBQ3RDO1dBQ0E7V0FDQSxrQkFBa0IscUJBQXFCO1dBQ3ZDO1dBQ0E7V0FDQSxLQUFLO1dBQ0w7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBOzs7OztXQzNCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHlDQUF5Qyx3Q0FBd0M7V0FDakY7V0FDQTtXQUNBOzs7OztXQ1BBOzs7OztXQ0FBO1dBQ0E7V0FDQTtXQUNBLHVEQUF1RCxpQkFBaUI7V0FDeEU7V0FDQSxnREFBZ0QsYUFBYTtXQUM3RDs7Ozs7V0NOQTs7V0FFQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQSxNQUFNLHFCQUFxQjtXQUMzQjtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBOztXQUVBO1dBQ0E7V0FDQTs7Ozs7VUVqREE7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9hamF4L0hhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9pbmRleC5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL3VpL0V2ZW50SGFuZGxlci5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL3VpL1dpZGdldEhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uLi8uLi9ub2RlX21vZHVsZXMvanF1ZXJ5LWV2ZW50cy10by1kb20tZXZlbnRzL2Rpc3QvaW5kZXgubS5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vZm9ybXdpZGdldHMvdmlzdWFsZWRpdG9yL2Fzc2V0cy9sZXNzL3Zpc3VhbGVkaXRvci5sZXNzIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvY2h1bmsgbG9hZGVkIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ydW50aW1lL2hhc093blByb3BlcnR5IHNob3J0aGFuZCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9tYWtlIG5hbWVzcGFjZSBvYmplY3QiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvanNvbnAgY2h1bmsgbG9hZGluZyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svYmVmb3JlLXN0YXJ0dXAiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3N0YXJ0dXAiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL2FmdGVyLXN0YXJ0dXAiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgZGVsZWdhdGUgfSBmcm9tICdqcXVlcnktZXZlbnRzLXRvLWRvbS1ldmVudHMnO1xuXG4vKipcbiAqIEJhY2tlbmQgQUpBWCBoYW5kbGVyLlxuICpcbiAqIFRoaXMgaXMgYSB1dGlsaXR5IHNjcmlwdCB0aGF0IHJlc29sdmVzIHNvbWUgYmFja3dhcmRzLWNvbXBhdGliaWxpdHkgaXNzdWVzIHdpdGggdGhlIGZ1bmN0aW9uYWxpdHlcbiAqIHRoYXQgcmVsaWVzIG9uIHRoZSBvbGQgZnJhbWV3b3JrLCBhbmQgZW5zdXJlcyB0aGF0IFNub3dib2FyZCB3b3JrcyB3ZWxsIHdpdGhpbiB0aGUgQmFja2VuZFxuICogZW52aXJvbm1lbnQuXG4gKlxuICogRnVuY3Rpb25zOlxuICogIC0gQWRkcyB0aGUgXCJyZW5kZXJcIiBqUXVlcnkgZXZlbnQgdG8gU25vd2JvYXJkIHJlcXVlc3RzIHRoYXQgd2lkZ2V0cyB1c2UgdG8gaW5pdGlhbGlzZS5cbiAqICAtIEVuc3VyZXMgdGhlIENTUkYgdG9rZW4gaXMgaW5jbHVkZWQgaW4gcmVxdWVzdHMuXG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEhhbmRsZXIgZXh0ZW5kcyBTbm93Ym9hcmQuU2luZ2xldG9uIHtcbiAgICAvKipcbiAgICAgKiBFdmVudCBsaXN0ZW5lcnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgICAqL1xuICAgIGxpc3RlbnMoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICByZWFkeTogJ3JlYWR5JyxcbiAgICAgICAgICAgIGFqYXhGZXRjaE9wdGlvbnM6ICdhamF4RmV0Y2hPcHRpb25zJyxcbiAgICAgICAgICAgIGFqYXhVcGRhdGVDb21wbGV0ZTogJ2FqYXhVcGRhdGVDb21wbGV0ZScsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVhZHkgaGFuZGxlci5cbiAgICAgKlxuICAgICAqIEZpcmVzIG9mZiBhIFwicmVuZGVyXCIgZXZlbnQuXG4gICAgICovXG4gICAgcmVhZHkoKSB7XG4gICAgICAgIGlmICghd2luZG93LmpRdWVyeSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGRlbGVnYXRlKCdyZW5kZXInKTtcblxuICAgICAgICAvLyBBZGQgXCJyZW5kZXJcIiBldmVudCBmb3IgYmFja3dhcmRzIGNvbXBhdGliaWxpdHlcbiAgICAgICAgd2luZG93LmpRdWVyeShkb2N1bWVudCkudHJpZ2dlcigncmVuZGVyJyk7XG5cbiAgICAgICAgLy8gQWRkIGdsb2JhbCBldmVudCBmb3IgcmVuZGVyaW5nIGluIFNub3dib2FyZFxuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCckcmVuZGVyJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ3JlbmRlcicpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBZGRzIHRoZSBqUXVlcnkgQUpBWCBwcmVmaWx0ZXIgdGhhdCB0aGUgb2xkIGZyYW1ld29yayB1c2VzIHRvIGluamVjdCB0aGUgQ1NSRiB0b2tlbiBpbiBBSkFYXG4gICAgICogY2FsbHMuXG4gICAgICovXG4gICAgYWRkUHJlZmlsdGVyKCkge1xuICAgICAgICBpZiAoIXdpbmRvdy5qUXVlcnkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHdpbmRvdy5qUXVlcnkuYWpheFByZWZpbHRlcigob3B0aW9ucykgPT4ge1xuICAgICAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgICAgIGlmICghb3B0aW9ucy5oZWFkZXJzKSB7XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaGVhZGVycyA9IHt9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBvcHRpb25zLmhlYWRlcnNbJ1gtQ1NSRi1UT0tFTiddID0gdGhpcy5nZXRUb2tlbigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBGZXRjaCBvcHRpb25zIGhhbmRsZXIuXG4gICAgICpcbiAgICAgKiBFbnN1cmVzIHRoYXQgdGhlIENTUkYgdG9rZW4gaXMgaW5jbHVkZWQgaW4gU25vd2JvYXJkIHJlcXVlc3RzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBhamF4RmV0Y2hPcHRpb25zKG9wdGlvbnMpIHtcbiAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgb3B0aW9ucy5oZWFkZXJzWydYLUNTUkYtVE9LRU4nXSA9IHRoaXMuZ2V0VG9rZW4oKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFVwZGF0ZSBjb21wbGV0ZSBoYW5kbGVyLlxuICAgICAqXG4gICAgICogRmlyZXMgb2ZmIGEgXCJyZW5kZXJcIiBldmVudCB3aGVuIHBhcnRpYWxzIGFyZSB1cGRhdGVkIHNvIHRoYXQgYW55IHdpZGdldHMgaW5jbHVkZWQgaW5cbiAgICAgKiByZXNwb25zZXMgYXJlIGNvcnJlY3RseSBpbml0aWFsaXNlZC5cbiAgICAgKi9cbiAgICBhamF4VXBkYXRlQ29tcGxldGUoKSB7XG4gICAgICAgIGlmICghd2luZG93LmpRdWVyeSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQWRkIFwicmVuZGVyXCIgZXZlbnQgZm9yIGJhY2t3YXJkcyBjb21wYXRpYmlsaXR5XG4gICAgICAgIHdpbmRvdy5qUXVlcnkoZG9jdW1lbnQpLnRyaWdnZXIoJ3JlbmRlcicpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgYSBDU1JGIHRva2VuIGlzIGF2YWlsYWJsZS5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtCb29sZWFufVxuICAgICAqL1xuICAgIGhhc1Rva2VuKCkge1xuICAgICAgICBjb25zdCB0b2tlbkVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJyk7XG5cbiAgICAgICAgaWYgKCF0b2tlbkVsZW1lbnQpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRva2VuRWxlbWVudC5oYXNBdHRyaWJ1dGUoJ2NvbnRlbnQnKSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0cyB0aGUgQ1NSRiB0b2tlbi5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAgICovXG4gICAgZ2V0VG9rZW4oKSB7XG4gICAgICAgIHJldHVybiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJykuZ2V0QXR0cmlidXRlKCdjb250ZW50Jyk7XG4gICAgfVxufVxuIiwiaW1wb3J0IEJhY2tlbmRBamF4SGFuZGxlciBmcm9tICcuL2FqYXgvSGFuZGxlcic7XG5pbXBvcnQgQmFja2VuZFVpRXZlbnRIYW5kbGVyIGZyb20gJy4vdWkvRXZlbnRIYW5kbGVyJztcbmltcG9ydCBCYWNrZW5kVWlXaWRnZXRIYW5kbGVyIGZyb20gJy4vdWkvV2lkZ2V0SGFuZGxlcic7XG5cbmlmICh3aW5kb3cuU25vd2JvYXJkID09PSB1bmRlZmluZWQpIHtcbiAgICB0aHJvdyBuZXcgRXJyb3IoJ1Nub3dib2FyZCBtdXN0IGJlIGxvYWRlZCBpbiBvcmRlciB0byB1c2UgdGhlIEJhY2tlbmQgVUkuJyk7XG59XG5cbigoU25vd2JvYXJkKSA9PiB7XG4gICAgU25vd2JvYXJkLmFkZFBsdWdpbignYmFja2VuZC5hamF4LmhhbmRsZXInLCBCYWNrZW5kQWpheEhhbmRsZXIpO1xuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2JhY2tlbmQudWkuZXZlbnRIYW5kbGVyJywgQmFja2VuZFVpRXZlbnRIYW5kbGVyKTtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdiYWNrZW5kLnVpLndpZGdldEhhbmRsZXInLCBCYWNrZW5kVWlXaWRnZXRIYW5kbGVyKTtcblxuICAgIC8vIEFkZCB0aGUgcHJlLWZpbHRlciBpbW1lZGlhdGVseVxuICAgIFNub3dib2FyZFsnYmFja2VuZC5hamF4LmhhbmRsZXInXSgpLmFkZFByZWZpbHRlcigpO1xuXG4gICAgLy8gQWRkIHBvbHlmaWxsIGZvciBBc3NldE1hbmFnZXJcbiAgICB3aW5kb3cuQXNzZXRNYW5hZ2VyID0ge1xuICAgICAgICBsb2FkOiAoYXNzZXRzLCBjYWxsYmFjaykgPT4ge1xuICAgICAgICAgICAgU25vd2JvYXJkLmFzc2V0TG9hZGVyKCkubG9hZChhc3NldHMpLnRoZW4oXG4gICAgICAgICAgICAgICAgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoY2FsbGJhY2sgJiYgdHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0sXG4gICAgfTtcbiAgICB3aW5kb3cuYXNzZXRNYW5hZ2VyID0gd2luZG93LkFzc2V0TWFuYWdlcjtcbn0pKHdpbmRvdy5Tbm93Ym9hcmQpO1xuIiwiLyoqXG4gKiBXaWRnZXQgZXZlbnQgaGFuZGxlci5cbiAqXG4gKiBFeHRlbmRzIGEgd2lkZ2V0IHdpdGggZXZlbnQgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eSwgYWxsb3dpbmcgZm9yIHRoZSBxdWljayBkZWZpbml0aW9uIG9mIGV2ZW50c1xuICogYW5kIGxpc3RlbmluZyBmb3IgZXZlbnRzIG9uIGEgc3BlY2lmaWMgaW5zdGFuY2Ugb2YgYSB3aWRnZXQuXG4gKlxuICogVGhpcyBpcyBhIGNvbXBsZW1lbnQgdG8gU25vd2JvYXJkJ3MgZ2xvYmFsIGV2ZW50cyAtIHRoZXNlIGV2ZW50cyB3aWxsIHN0aWxsIGZpcmUgaW4gb3JkZXIgdG9cbiAqIGFsbG93IGV4dGVybmFsIGNvZGUgdG8gbGlzdGVuIGFuZCBoYW5kbGUgZXZlbnRzLiBMb2NhbCBldmVudHMgY2FuIGNhbmNlbCB0aGUgZ2xvYmFsIGV2ZW50IChhbmRcbiAqIGZ1cnRoZXIgbG9jYWwgZXZlbnRzKSBieSByZXR1cm5pbmcgYGZhbHNlYCBmcm9tIHRoZSBjYWxsYmFjay5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjIgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXZlbnRIYW5kbGVyIGV4dGVuZHMgU25vd2JvYXJkLlBsdWdpbkJhc2Uge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtQbHVnaW5CYXNlfSBpbnN0YW5jZVxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBldmVudFByZWZpeFxuICAgICAqL1xuICAgIGNvbnN0cnVjdChpbnN0YW5jZSwgZXZlbnRQcmVmaXgpIHtcbiAgICAgICAgaWYgKGluc3RhbmNlIGluc3RhbmNlb2YgU25vd2JvYXJkLlBsdWdpbkJhc2UgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0V2ZW50IGhhbmRsaW5nIGNhbiBvbmx5IGJlIGFwcGxpZWQgdG8gU25vd2JvYXJkIGNsYXNzZXMuJyk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKCFldmVudFByZWZpeCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdFdmVudCBwcmVmaXggaXMgcmVxdWlyZWQuJyk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5pbnN0YW5jZSA9IGluc3RhbmNlO1xuICAgICAgICB0aGlzLmV2ZW50UHJlZml4ID0gZXZlbnRQcmVmaXg7XG4gICAgICAgIHRoaXMuZXZlbnRzID0gW107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVnaXN0ZXJzIGEgbGlzdGVuZXIgZm9yIGEgd2lkZ2V0J3MgZXZlbnQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnRcbiAgICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqL1xuICAgIG9uKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgICB0aGlzLmV2ZW50cy5wdXNoKHtcbiAgICAgICAgICAgIGV2ZW50LFxuICAgICAgICAgICAgY2FsbGJhY2ssXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlcmVnaXN0ZXJzIGEgbGlzdGVuZXIgZm9yIGEgd2lkZ2V0J3MgZXZlbnQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnRcbiAgICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqL1xuICAgIG9mZihldmVudCwgY2FsbGJhY2spIHtcbiAgICAgICAgdGhpcy5ldmVudHMgPSB0aGlzLmV2ZW50cy5maWx0ZXIoKHJlZ2lzdGVyZWRFdmVudCkgPT4gcmVnaXN0ZXJlZEV2ZW50LmV2ZW50ICE9PSBldmVudCB8fCByZWdpc3RlcmVkRXZlbnQuY2FsbGJhY2sgIT09IGNhbGxiYWNrKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZWdpc3RlcnMgYSBsaXN0ZW5lciBmb3IgYSB3aWRnZXQncyBldmVudCB0aGF0IHdpbGwgb25seSBmaXJlIG9uY2UuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnRcbiAgICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqL1xuICAgIG9uY2UoZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICAgIGNvbnN0IGxlbmd0aCA9IHRoaXMuZXZlbnRzLnB1c2goe1xuICAgICAgICAgICAgZXZlbnQsXG4gICAgICAgICAgICBjYWxsYmFjazogKC4uLnBhcmFtZXRlcnMpID0+IHtcbiAgICAgICAgICAgICAgICBjYWxsYmFjayguLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgICAgICAgICB0aGlzLmV2ZW50cy5zcGxpY2UobGVuZ3RoIC0gMSwgMSk7XG4gICAgICAgICAgICB9LFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBGaXJlcyBhbiBldmVudCBvbiB0aGUgd2lkZ2V0LlxuICAgICAqXG4gICAgICogTG9jYWwgZXZlbnRzIGFyZSBmaXJlZCBmaXJzdCwgdGhlbiBhIGdsb2JhbCBldmVudCBpcyBmaXJlZCBhZnRlcndhcmRzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IGV2ZW50TmFtZVxuICAgICAqIEBwYXJhbSAgey4uLmFueX0gcGFyYW1ldGVyc1xuICAgICAqL1xuICAgIGZpcmUoZXZlbnROYW1lLCAuLi5wYXJhbWV0ZXJzKSB7XG4gICAgICAgIC8vIEZpcmUgbG9jYWwgZXZlbnRzIGZpcnN0XG4gICAgICAgIGNvbnN0IGV2ZW50cyA9IHRoaXMuZXZlbnRzLmZpbHRlcigocmVnaXN0ZXJlZEV2ZW50KSA9PiByZWdpc3RlcmVkRXZlbnQuZXZlbnQgPT09IGV2ZW50TmFtZSk7XG4gICAgICAgIGxldCBjYW5jZWxsZWQgPSBmYWxzZTtcbiAgICAgICAgZXZlbnRzLmZvckVhY2goKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICBpZiAoY2FuY2VsbGVkKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKGV2ZW50LmNhbGxiYWNrKC4uLnBhcmFtZXRlcnMpID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIGNhbmNlbGxlZCA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGlmICghY2FuY2VsbGVkKSB7XG4gICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudChgJHt0aGlzLmV2ZW50UHJlZml4fS4ke2V2ZW50TmFtZX1gLCAuLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZpcmVzIGEgcHJvbWlzZSBldmVudCBvbiB0aGUgd2lkZ2V0LlxuICAgICAqXG4gICAgICogTG9jYWwgZXZlbnRzIGFyZSBmaXJlZCBmaXJzdCwgdGhlbiBhIGdsb2JhbCBldmVudCBpcyBmaXJlZCBhZnRlcndhcmRzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IGV2ZW50TmFtZVxuICAgICAqIEBwYXJhbSAgey4uLmFueX0gcGFyYW1ldGVyc1xuICAgICAqL1xuICAgIGZpcmVQcm9taXNlKGV2ZW50TmFtZSwgLi4ucGFyYW1ldGVycykge1xuICAgICAgICBjb25zdCBldmVudHMgPSB0aGlzLmV2ZW50cy5maWx0ZXIoKHJlZ2lzdGVyZWRFdmVudCkgPT4gcmVnaXN0ZXJlZEV2ZW50LmV2ZW50ID09PSBldmVudE5hbWUpO1xuICAgICAgICBjb25zdCBwcm9taXNlcyA9IGV2ZW50cy5maWx0ZXIoKGV2ZW50KSA9PiBldmVudCAhPT0gbnVsbCwgZXZlbnRzLm1hcCgoZXZlbnQpID0+IGV2ZW50LmNhbGxiYWNrKC4uLnBhcmFtZXRlcnMpKSk7XG5cbiAgICAgICAgUHJvbWlzZS5hbGwocHJvbWlzZXMpLnRoZW4oXG4gICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsUHJvbWlzZUV2ZW50KGAke3RoaXMuZXZlbnRQcmVmaXh9LiR7ZXZlbnROYW1lfWAsIC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgKTtcbiAgICB9XG59XG4iLCIvKipcbiAqIEJhY2tlbmQgd2lkZ2V0IGhhbmRsZXIuXG4gKlxuICogSGFuZGxlcyB0aGUgY3JlYXRpb24gYW5kIGRpc3Bvc2FsIG9mIHdpZGdldHMgaW4gdGhlIEJhY2tlbmQuIFdpZGdldHMgc2hvdWxkIGluY2x1ZGUgdGhpcyBhc1xuICogYSBkZXBlbmRlbmN5IGluIG9yZGVyIHRvIGJlIGxvYWRlZCBhbmQgaW5pdGlhbGlzZWQgYWZ0ZXIgdGhlIGhhbmRsZXIsIGluIG9yZGVyIHRvIGNvcnJlY3RseVxuICogcmVnaXN0ZXIuXG4gKlxuICogQGNvcHlyaWdodCAyMDIyIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFdpZGdldEhhbmRsZXIgZXh0ZW5kcyBTbm93Ym9hcmQuU2luZ2xldG9uIHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3QoKSB7XG4gICAgICAgIHRoaXMucmVnaXN0ZXJlZFdpZGdldHMgPSBbXTtcbiAgICAgICAgdGhpcy5lbGVtZW50cyA9IFtdO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIExpc3RlbmVycy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAnb25SZWFkeScsXG4gICAgICAgICAgICByZW5kZXI6ICdvblJlYWR5JyxcbiAgICAgICAgICAgIGFqYXhVcGRhdGU6ICdvbkFqYXhVcGRhdGUnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlZ2lzdGVycyBhIHdpZGdldCBhcyBhIGdpdmVuIGRhdGEgY29udHJvbC5cbiAgICAgKlxuICAgICAqIFJlZ2lzdGVyaW5nIGEgd2lkZ2V0IHdpbGwgYWxsb3cgYW55IGVsZW1lbnQgdGhhdCBjb250YWlucyBhIFwiZGF0YS1jb250cm9sXCIgYXR0cmlidXRlIG1hdGNoaW5nXG4gICAgICogdGhlIGNvbnRyb2wgbmFtZSB0byBiZSBpbml0aWFsaXplZCB3aXRoIHRoZSBnaXZlbiB3aWRnZXQuXG4gICAgICpcbiAgICAgKiBZb3UgbWF5IG9wdGlvbmFsbHkgcHJvdmlkZSBhIGNhbGxiYWNrIHRoYXQgd2lsbCBiZSBmaXJlZCB3aGVuIGFuIGluc3RhbmNlIG9mIHRoZSB3aWRnZXQgaXNcbiAgICAgKiBpbml0aWFsaXplZCAtIHRoZSBjYWxsYmFjayB3aWxsIGJlIHByb3ZpZGVkIHRoZSBlbGVtZW50IGFuZCB0aGUgd2lkZ2V0IGluc3RhbmNlIGFzIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gY29udHJvbFxuICAgICAqIEBwYXJhbSB7U25vd2JvYXJkLlBsdWdpbkJhc2V9IHdpZGdldFxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgcmVnaXN0ZXIoY29udHJvbCwgd2lkZ2V0LCBjYWxsYmFjaykge1xuICAgICAgICB0aGlzLnJlZ2lzdGVyZWRXaWRnZXRzLnB1c2goe1xuICAgICAgICAgICAgY29udHJvbCxcbiAgICAgICAgICAgIHdpZGdldCxcbiAgICAgICAgICAgIGNhbGxiYWNrLFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBVbnJlZ2lzdGVycyBhIGRhdGEgY29udHJvbC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBjb250cm9sXG4gICAgICovXG4gICAgdW5yZWdpc3Rlcihjb250cm9sKSB7XG4gICAgICAgIHRoaXMucmVnaXN0ZXJlZFdpZGdldHMgPSB0aGlzLnJlZ2lzdGVyZWRXaWRnZXRzLmZpbHRlcigod2lkZ2V0KSA9PiB3aWRnZXQuY29udHJvbCAhPT0gY29udHJvbCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVhZHkgaGFuZGxlci5cbiAgICAgKlxuICAgICAqIEluaXRpYWxpemVzIHdpZGdldHMgd2l0aGluIHRoZSBlbnRpcmUgZG9jdW1lbnQuXG4gICAgICovXG4gICAgb25SZWFkeSgpIHtcbiAgICAgICAgdGhpcy5pbml0aWFsaXplV2lkZ2V0cyhkb2N1bWVudC5ib2R5KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBSkFYIHVwZGF0ZSBoYW5kbGVyLlxuICAgICAqXG4gICAgICogSW5pdGlhbGl6ZXMgd2lkZ2V0cyBpbnNpZGUgYW4gdXBkYXRlIGVsZW1lbnQgZnJvbSBhbiBBSkFYIHJlc3BvbnNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZWxlbWVudFxuICAgICAqL1xuICAgIG9uQWpheFVwZGF0ZShlbGVtZW50KSB7XG4gICAgICAgIHRoaXMuaW5pdGlhbGl6ZVdpZGdldHMoZWxlbWVudCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZXMgYWxsIHdpZGdldHMgd2l0aGluIGFuIGVsZW1lbnQuXG4gICAgICpcbiAgICAgKiBJZiBhbiBlbGVtZW50IGNvbnRhaW5zIGEgXCJkYXRhLWNvbnRyb2xcIiBhdHRyaWJ1dGUgbWF0Y2hpbmcgYSByZWdpc3RlcmVkIHdpZGdldCwgdGhlIHdpZGdldFxuICAgICAqIGlzIGluaXRpYWxpemVkIGFuZCBhdHRhY2hlZCB0byB0aGUgZWxlbWVudCBhcyBhIFwid2lkZ2V0XCIgcHJvcGVydHkuXG4gICAgICpcbiAgICAgKiBPbmx5IG9uZSB3aWRnZXQgbWF5IGJlIGluaXRpYWxpemVkIHRvIGEgcGFydGljdWxhciBlbGVtZW50LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZWxlbWVudFxuICAgICAqL1xuICAgIGluaXRpYWxpemVXaWRnZXRzKGVsZW1lbnQpIHtcbiAgICAgICAgdGhpcy5yZWdpc3RlcmVkV2lkZ2V0cy5mb3JFYWNoKCh3aWRnZXQpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGluc3RhbmNlcyA9IGVsZW1lbnQucXVlcnlTZWxlY3RvckFsbChgW2RhdGEtY29udHJvbD1cIiR7d2lkZ2V0LmNvbnRyb2x9XCJdOm5vdChbZGF0YS13aWRnZXQtaW5pdGlhbGl6ZWRdKWApO1xuXG4gICAgICAgICAgICBpZiAoaW5zdGFuY2VzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGluc3RhbmNlcy5mb3JFYWNoKChpbnN0YW5jZSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAvLyBQcmV2ZW50IGRvdWJsZS13aWRnZXQgaW5pdGlhbGl6YXRpb25cbiAgICAgICAgICAgICAgICAgICAgaWYgKGluc3RhbmNlLmRhdGFzZXQud2lkZ2V0SW5pdGlhbGl6ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHdpZGdldEluc3RhbmNlID0gdGhpcy5zbm93Ym9hcmRbd2lkZ2V0LndpZGdldF0oaW5zdGFuY2UpO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmVsZW1lbnRzLnB1c2goe1xuICAgICAgICAgICAgICAgICAgICAgICAgZWxlbWVudDogaW5zdGFuY2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBpbnN0YW5jZTogd2lkZ2V0SW5zdGFuY2UsXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBpbnN0YW5jZS5kYXRhc2V0LndpZGdldEluaXRpYWxpemVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2JhY2tlbmQud2lkZ2V0LmluaXRpYWxpemVkJywgaW5zdGFuY2UsIHdpZGdldEluc3RhbmNlKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIHdpZGdldC5jYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgd2lkZ2V0LmNhbGxiYWNrKHdpZGdldEluc3RhbmNlLCBpbnN0YW5jZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmV0dXJucyBhIHdpZGdldCB0aGF0IGlzIGF0dGFjaGVkIHRvIHRoZSBnaXZlbiBlbGVtZW50LCBpZiBhbnkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICogQHJldHVybnMge1Nub3dib2FyZC5QbHVnaW5CYXNlfG51bGx9XG4gICAgICovXG4gICAgZ2V0V2lkZ2V0KGVsZW1lbnQpIHtcbiAgICAgICAgY29uc3QgZm91bmQgPSB0aGlzLmVsZW1lbnRzLmZpbmQoKHdpZGdldCkgPT4gd2lkZ2V0LmVsZW1lbnQgPT09IGVsZW1lbnQpO1xuXG4gICAgICAgIGlmIChmb3VuZCkge1xuICAgICAgICAgICAgcmV0dXJuIGZvdW5kLmluc3RhbmNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxufVxuIiwidmFyIHQ9ZnVuY3Rpb24odCxlKXt2b2lkIDA9PT1lJiYoZT1bXCJldmVudFwiXSk7dmFyIG49dC5zdGFydHNXaXRoKFwiJFwiKT9mdW5jdGlvbigpe3JldHVybiB3aW5kb3cuJChkb2N1bWVudCkudHJpZ2dlcih0LnNsaWNlKDEpLFtdLnNsaWNlLmNhbGwoYXJndW1lbnRzKVswXS5kZXRhaWwpfTpmdW5jdGlvbigpe3ZhciBuPWFyZ3VtZW50cyxpPWUucmVkdWNlKGZ1bmN0aW9uKHQsZSxpKXtyZXR1cm4gdFtlXT1bXS5zbGljZS5jYWxsKG4pW2ldLHR9LHt9KTtpLmV2ZW50LnRhcmdldC5kaXNwYXRjaEV2ZW50KG5ldyBDdXN0b21FdmVudChcIiRcIit0LHtkZXRhaWw6aSxidWJibGVzOiEwLGNhbmNlbGFibGU6ITB9KSl9O3JldHVybiB0LnN0YXJ0c1dpdGgoXCIkXCIpP2RvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIodCxuKTp3aW5kb3cuJChkb2N1bWVudCkub24odCxuKSxufSxlPWZ1bmN0aW9uKHQsZSl7dC5zdGFydHNXaXRoKFwiJFwiKT9kb2N1bWVudC5yZW1vdmVFdmVudExpc3RlbmVyKHQsZSk6d2luZG93LiQoZG9jdW1lbnQpLm9mZih0LGUpfTtleHBvcnR7ZSBhcyBhYm5lZ2F0ZSx0IGFzIGRlbGVnYXRlfTtcbi8vIyBzb3VyY2VNYXBwaW5nVVJMPWluZGV4Lm0uanMubWFwXG4iLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiLCIvLyBUaGUgbW9kdWxlIGNhY2hlXG52YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG5cbi8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG5mdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuXHR2YXIgY2FjaGVkTW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXTtcblx0aWYgKGNhY2hlZE1vZHVsZSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0cmV0dXJuIGNhY2hlZE1vZHVsZS5leHBvcnRzO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuLy8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbl9fd2VicGFja19yZXF1aXJlX18ubSA9IF9fd2VicGFja19tb2R1bGVzX187XG5cbiIsInZhciBkZWZlcnJlZCA9IFtdO1xuX193ZWJwYWNrX3JlcXVpcmVfXy5PID0gKHJlc3VsdCwgY2h1bmtJZHMsIGZuLCBwcmlvcml0eSkgPT4ge1xuXHRpZihjaHVua0lkcykge1xuXHRcdHByaW9yaXR5ID0gcHJpb3JpdHkgfHwgMDtcblx0XHRmb3IodmFyIGkgPSBkZWZlcnJlZC5sZW5ndGg7IGkgPiAwICYmIGRlZmVycmVkW2kgLSAxXVsyXSA+IHByaW9yaXR5OyBpLS0pIGRlZmVycmVkW2ldID0gZGVmZXJyZWRbaSAtIDFdO1xuXHRcdGRlZmVycmVkW2ldID0gW2NodW5rSWRzLCBmbiwgcHJpb3JpdHldO1xuXHRcdHJldHVybjtcblx0fVxuXHR2YXIgbm90RnVsZmlsbGVkID0gSW5maW5pdHk7XG5cdGZvciAodmFyIGkgPSAwOyBpIDwgZGVmZXJyZWQubGVuZ3RoOyBpKyspIHtcblx0XHR2YXIgW2NodW5rSWRzLCBmbiwgcHJpb3JpdHldID0gZGVmZXJyZWRbaV07XG5cdFx0dmFyIGZ1bGZpbGxlZCA9IHRydWU7XG5cdFx0Zm9yICh2YXIgaiA9IDA7IGogPCBjaHVua0lkcy5sZW5ndGg7IGorKykge1xuXHRcdFx0aWYgKChwcmlvcml0eSAmIDEgPT09IDAgfHwgbm90RnVsZmlsbGVkID49IHByaW9yaXR5KSAmJiBPYmplY3Qua2V5cyhfX3dlYnBhY2tfcmVxdWlyZV9fLk8pLmV2ZXJ5KChrZXkpID0+IChfX3dlYnBhY2tfcmVxdWlyZV9fLk9ba2V5XShjaHVua0lkc1tqXSkpKSkge1xuXHRcdFx0XHRjaHVua0lkcy5zcGxpY2Uoai0tLCAxKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdGZ1bGZpbGxlZCA9IGZhbHNlO1xuXHRcdFx0XHRpZihwcmlvcml0eSA8IG5vdEZ1bGZpbGxlZCkgbm90RnVsZmlsbGVkID0gcHJpb3JpdHk7XG5cdFx0XHR9XG5cdFx0fVxuXHRcdGlmKGZ1bGZpbGxlZCkge1xuXHRcdFx0ZGVmZXJyZWQuc3BsaWNlKGktLSwgMSlcblx0XHRcdHZhciByID0gZm4oKTtcblx0XHRcdGlmIChyICE9PSB1bmRlZmluZWQpIHJlc3VsdCA9IHI7XG5cdFx0fVxuXHR9XG5cdHJldHVybiByZXN1bHQ7XG59OyIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCIvLyBubyBiYXNlVVJJXG5cbi8vIG9iamVjdCB0byBzdG9yZSBsb2FkZWQgYW5kIGxvYWRpbmcgY2h1bmtzXG4vLyB1bmRlZmluZWQgPSBjaHVuayBub3QgbG9hZGVkLCBudWxsID0gY2h1bmsgcHJlbG9hZGVkL3ByZWZldGNoZWRcbi8vIFtyZXNvbHZlLCByZWplY3QsIFByb21pc2VdID0gY2h1bmsgbG9hZGluZywgMCA9IGNodW5rIGxvYWRlZFxudmFyIGluc3RhbGxlZENodW5rcyA9IHtcblx0XCIvYXNzZXRzL3VpL2pzL2J1aWxkL2JhY2tlbmRcIjogMCxcblx0XCJmb3Jtd2lkZ2V0cy92aXN1YWxlZGl0b3IvYXNzZXRzL2Nzcy92aXN1YWxlZGl0b3JcIjogMFxufTtcblxuLy8gbm8gY2h1bmsgb24gZGVtYW5kIGxvYWRpbmdcblxuLy8gbm8gcHJlZmV0Y2hpbmdcblxuLy8gbm8gcHJlbG9hZGVkXG5cbi8vIG5vIEhNUlxuXG4vLyBubyBITVIgbWFuaWZlc3RcblxuX193ZWJwYWNrX3JlcXVpcmVfXy5PLmogPSAoY2h1bmtJZCkgPT4gKGluc3RhbGxlZENodW5rc1tjaHVua0lkXSA9PT0gMCk7XG5cbi8vIGluc3RhbGwgYSBKU09OUCBjYWxsYmFjayBmb3IgY2h1bmsgbG9hZGluZ1xudmFyIHdlYnBhY2tKc29ucENhbGxiYWNrID0gKHBhcmVudENodW5rTG9hZGluZ0Z1bmN0aW9uLCBkYXRhKSA9PiB7XG5cdHZhciBbY2h1bmtJZHMsIG1vcmVNb2R1bGVzLCBydW50aW1lXSA9IGRhdGE7XG5cdC8vIGFkZCBcIm1vcmVNb2R1bGVzXCIgdG8gdGhlIG1vZHVsZXMgb2JqZWN0LFxuXHQvLyB0aGVuIGZsYWcgYWxsIFwiY2h1bmtJZHNcIiBhcyBsb2FkZWQgYW5kIGZpcmUgY2FsbGJhY2tcblx0dmFyIG1vZHVsZUlkLCBjaHVua0lkLCBpID0gMDtcblx0aWYoY2h1bmtJZHMuc29tZSgoaWQpID0+IChpbnN0YWxsZWRDaHVua3NbaWRdICE9PSAwKSkpIHtcblx0XHRmb3IobW9kdWxlSWQgaW4gbW9yZU1vZHVsZXMpIHtcblx0XHRcdGlmKF9fd2VicGFja19yZXF1aXJlX18ubyhtb3JlTW9kdWxlcywgbW9kdWxlSWQpKSB7XG5cdFx0XHRcdF9fd2VicGFja19yZXF1aXJlX18ubVttb2R1bGVJZF0gPSBtb3JlTW9kdWxlc1ttb2R1bGVJZF07XG5cdFx0XHR9XG5cdFx0fVxuXHRcdGlmKHJ1bnRpbWUpIHZhciByZXN1bHQgPSBydW50aW1lKF9fd2VicGFja19yZXF1aXJlX18pO1xuXHR9XG5cdGlmKHBhcmVudENodW5rTG9hZGluZ0Z1bmN0aW9uKSBwYXJlbnRDaHVua0xvYWRpbmdGdW5jdGlvbihkYXRhKTtcblx0Zm9yKDtpIDwgY2h1bmtJZHMubGVuZ3RoOyBpKyspIHtcblx0XHRjaHVua0lkID0gY2h1bmtJZHNbaV07XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGluc3RhbGxlZENodW5rcywgY2h1bmtJZCkgJiYgaW5zdGFsbGVkQ2h1bmtzW2NodW5rSWRdKSB7XG5cdFx0XHRpbnN0YWxsZWRDaHVua3NbY2h1bmtJZF1bMF0oKTtcblx0XHR9XG5cdFx0aW5zdGFsbGVkQ2h1bmtzW2NodW5rSWRdID0gMDtcblx0fVxuXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXy5PKHJlc3VsdCk7XG59XG5cbnZhciBjaHVua0xvYWRpbmdHbG9iYWwgPSBzZWxmW1wid2VicGFja0NodW5rX3dpbnRlcmNtc193bl9iYWNrZW5kX21vZHVsZVwiXSA9IHNlbGZbXCJ3ZWJwYWNrQ2h1bmtfd2ludGVyY21zX3duX2JhY2tlbmRfbW9kdWxlXCJdIHx8IFtdO1xuY2h1bmtMb2FkaW5nR2xvYmFsLmZvckVhY2god2VicGFja0pzb25wQ2FsbGJhY2suYmluZChudWxsLCAwKSk7XG5jaHVua0xvYWRpbmdHbG9iYWwucHVzaCA9IHdlYnBhY2tKc29ucENhbGxiYWNrLmJpbmQobnVsbCwgY2h1bmtMb2FkaW5nR2xvYmFsLnB1c2guYmluZChjaHVua0xvYWRpbmdHbG9iYWwpKTsiLCIiLCIvLyBzdGFydHVwXG4vLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbi8vIFRoaXMgZW50cnkgbW9kdWxlIGRlcGVuZHMgb24gb3RoZXIgbG9hZGVkIGNodW5rcyBhbmQgZXhlY3V0aW9uIG5lZWQgdG8gYmUgZGVsYXllZFxuX193ZWJwYWNrX3JlcXVpcmVfXy5PKHVuZGVmaW5lZCwgW1wiZm9ybXdpZGdldHMvdmlzdWFsZWRpdG9yL2Fzc2V0cy9jc3MvdmlzdWFsZWRpdG9yXCJdLCAoKSA9PiAoX193ZWJwYWNrX3JlcXVpcmVfXyhcIi4vYXNzZXRzL3VpL2pzL2luZGV4LmpzXCIpKSlcbnZhciBfX3dlYnBhY2tfZXhwb3J0c19fID0gX193ZWJwYWNrX3JlcXVpcmVfXy5PKHVuZGVmaW5lZCwgW1wiZm9ybXdpZGdldHMvdmlzdWFsZWRpdG9yL2Fzc2V0cy9jc3MvdmlzdWFsZWRpdG9yXCJdLCAoKSA9PiAoX193ZWJwYWNrX3JlcXVpcmVfXyhcIi4vZm9ybXdpZGdldHMvdmlzdWFsZWRpdG9yL2Fzc2V0cy9sZXNzL3Zpc3VhbGVkaXRvci5sZXNzXCIpKSlcbl9fd2VicGFja19leHBvcnRzX18gPSBfX3dlYnBhY2tfcmVxdWlyZV9fLk8oX193ZWJwYWNrX2V4cG9ydHNfXyk7XG4iLCIiXSwibmFtZXMiOlsiZGVsZWdhdGUiLCJIYW5kbGVyIiwicmVhZHkiLCJhamF4RmV0Y2hPcHRpb25zIiwiYWpheFVwZGF0ZUNvbXBsZXRlIiwid2luZG93IiwialF1ZXJ5IiwiZG9jdW1lbnQiLCJ0cmlnZ2VyIiwiYWRkRXZlbnRMaXN0ZW5lciIsInNub3dib2FyZCIsImdsb2JhbEV2ZW50IiwiYWpheFByZWZpbHRlciIsIm9wdGlvbnMiLCJoYXNUb2tlbiIsImhlYWRlcnMiLCJnZXRUb2tlbiIsInRva2VuRWxlbWVudCIsInF1ZXJ5U2VsZWN0b3IiLCJoYXNBdHRyaWJ1dGUiLCJnZXRBdHRyaWJ1dGUiLCJTbm93Ym9hcmQiLCJTaW5nbGV0b24iLCJCYWNrZW5kQWpheEhhbmRsZXIiLCJCYWNrZW5kVWlFdmVudEhhbmRsZXIiLCJCYWNrZW5kVWlXaWRnZXRIYW5kbGVyIiwidW5kZWZpbmVkIiwiRXJyb3IiLCJhZGRQbHVnaW4iLCJhZGRQcmVmaWx0ZXIiLCJBc3NldE1hbmFnZXIiLCJsb2FkIiwiYXNzZXRzIiwiY2FsbGJhY2siLCJhc3NldExvYWRlciIsInRoZW4iLCJhc3NldE1hbmFnZXIiLCJFdmVudEhhbmRsZXIiLCJpbnN0YW5jZSIsImV2ZW50UHJlZml4IiwiUGx1Z2luQmFzZSIsImV2ZW50cyIsImV2ZW50IiwicHVzaCIsImZpbHRlciIsInJlZ2lzdGVyZWRFdmVudCIsImxlbmd0aCIsInNwbGljZSIsImV2ZW50TmFtZSIsInBhcmFtZXRlcnMiLCJjYW5jZWxsZWQiLCJmb3JFYWNoIiwicHJvbWlzZXMiLCJtYXAiLCJQcm9taXNlIiwiYWxsIiwiZ2xvYmFsUHJvbWlzZUV2ZW50IiwiV2lkZ2V0SGFuZGxlciIsInJlZ2lzdGVyZWRXaWRnZXRzIiwiZWxlbWVudHMiLCJyZW5kZXIiLCJhamF4VXBkYXRlIiwiY29udHJvbCIsIndpZGdldCIsImluaXRpYWxpemVXaWRnZXRzIiwiYm9keSIsImVsZW1lbnQiLCJpbnN0YW5jZXMiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZGF0YXNldCIsIndpZGdldEluaXRpYWxpemVkIiwid2lkZ2V0SW5zdGFuY2UiLCJmb3VuZCIsImZpbmQiXSwic291cmNlUm9vdCI6IiJ9