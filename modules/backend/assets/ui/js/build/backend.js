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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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SSxxRkFBcUYsaUJBQWlCLGlGQUFxSDtBQUNyakI7Ozs7Ozs7Ozs7OztBQ0RBOzs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOzs7OztXQ3pCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLCtCQUErQix3Q0FBd0M7V0FDdkU7V0FDQTtXQUNBO1dBQ0E7V0FDQSxpQkFBaUIscUJBQXFCO1dBQ3RDO1dBQ0E7V0FDQSxrQkFBa0IscUJBQXFCO1dBQ3ZDO1dBQ0E7V0FDQSxLQUFLO1dBQ0w7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBOzs7OztXQzNCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHlDQUF5Qyx3Q0FBd0M7V0FDakY7V0FDQTtXQUNBOzs7OztXQ1BBOzs7OztXQ0FBO1dBQ0E7V0FDQTtXQUNBLHVEQUF1RCxpQkFBaUI7V0FDeEU7V0FDQSxnREFBZ0QsYUFBYTtXQUM3RDs7Ozs7V0NOQTs7V0FFQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTs7V0FFQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQSxNQUFNLHFCQUFxQjtXQUMzQjtXQUNBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBOztXQUVBO1dBQ0E7V0FDQTs7Ozs7VUVqREE7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9hamF4L0hhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9pbmRleC5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL3VpL0V2ZW50SGFuZGxlci5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL3VpL1dpZGdldEhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uLi8uLi9ub2RlX21vZHVsZXMvanF1ZXJ5LWV2ZW50cy10by1kb20tZXZlbnRzL2Rpc3QvaW5kZXgubS5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vZm9ybXdpZGdldHMvdmlzdWFsZWRpdG9yL2Fzc2V0cy9sZXNzL3Zpc3VhbGVkaXRvci5sZXNzPzg1NTUiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9jaHVuayBsb2FkZWQiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvaGFzT3duUHJvcGVydHkgc2hvcnRoYW5kIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9qc29ucCBjaHVuayBsb2FkaW5nIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9iZWZvcmUtc3RhcnR1cCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svc3RhcnR1cCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svYWZ0ZXItc3RhcnR1cCJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgeyBkZWxlZ2F0ZSB9IGZyb20gJ2pxdWVyeS1ldmVudHMtdG8tZG9tLWV2ZW50cyc7XG5cbi8qKlxuICogQmFja2VuZCBBSkFYIGhhbmRsZXIuXG4gKlxuICogVGhpcyBpcyBhIHV0aWxpdHkgc2NyaXB0IHRoYXQgcmVzb2x2ZXMgc29tZSBiYWNrd2FyZHMtY29tcGF0aWJpbGl0eSBpc3N1ZXMgd2l0aCB0aGUgZnVuY3Rpb25hbGl0eVxuICogdGhhdCByZWxpZXMgb24gdGhlIG9sZCBmcmFtZXdvcmssIGFuZCBlbnN1cmVzIHRoYXQgU25vd2JvYXJkIHdvcmtzIHdlbGwgd2l0aGluIHRoZSBCYWNrZW5kXG4gKiBlbnZpcm9ubWVudC5cbiAqXG4gKiBGdW5jdGlvbnM6XG4gKiAgLSBBZGRzIHRoZSBcInJlbmRlclwiIGpRdWVyeSBldmVudCB0byBTbm93Ym9hcmQgcmVxdWVzdHMgdGhhdCB3aWRnZXRzIHVzZSB0byBpbml0aWFsaXNlLlxuICogIC0gRW5zdXJlcyB0aGUgQ1NSRiB0b2tlbiBpcyBpbmNsdWRlZCBpbiByZXF1ZXN0cy5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSGFuZGxlciBleHRlbmRzIFNub3dib2FyZC5TaW5nbGV0b24ge1xuICAgIC8qKlxuICAgICAqIEV2ZW50IGxpc3RlbmVycy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICAgICAgYWpheEZldGNoT3B0aW9uczogJ2FqYXhGZXRjaE9wdGlvbnMnLFxuICAgICAgICAgICAgYWpheFVwZGF0ZUNvbXBsZXRlOiAnYWpheFVwZGF0ZUNvbXBsZXRlJyxcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZWFkeSBoYW5kbGVyLlxuICAgICAqXG4gICAgICogRmlyZXMgb2ZmIGEgXCJyZW5kZXJcIiBldmVudC5cbiAgICAgKi9cbiAgICByZWFkeSgpIHtcbiAgICAgICAgaWYgKCF3aW5kb3cualF1ZXJ5KSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgZGVsZWdhdGUoJ3JlbmRlcicpO1xuXG4gICAgICAgIC8vIEFkZCBcInJlbmRlclwiIGV2ZW50IGZvciBiYWNrd2FyZHMgY29tcGF0aWJpbGl0eVxuICAgICAgICB3aW5kb3cualF1ZXJ5KGRvY3VtZW50KS50cmlnZ2VyKCdyZW5kZXInKTtcblxuICAgICAgICAvLyBBZGQgZ2xvYmFsIGV2ZW50IGZvciByZW5kZXJpbmcgaW4gU25vd2JvYXJkXG4gICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJyRyZW5kZXInLCAoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgncmVuZGVyJyk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFkZHMgdGhlIGpRdWVyeSBBSkFYIHByZWZpbHRlciB0aGF0IHRoZSBvbGQgZnJhbWV3b3JrIHVzZXMgdG8gaW5qZWN0IHRoZSBDU1JGIHRva2VuIGluIEFKQVhcbiAgICAgKiBjYWxscy5cbiAgICAgKi9cbiAgICBhZGRQcmVmaWx0ZXIoKSB7XG4gICAgICAgIGlmICghd2luZG93LmpRdWVyeSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgd2luZG93LmpRdWVyeS5hamF4UHJlZmlsdGVyKChvcHRpb25zKSA9PiB7XG4gICAgICAgICAgICBpZiAodGhpcy5oYXNUb2tlbigpKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFvcHRpb25zLmhlYWRlcnMpIHtcbiAgICAgICAgICAgICAgICAgICAgb3B0aW9ucy5oZWFkZXJzID0ge307XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIG9wdGlvbnMuaGVhZGVyc1snWC1DU1JGLVRPS0VOJ10gPSB0aGlzLmdldFRva2VuKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZldGNoIG9wdGlvbnMgaGFuZGxlci5cbiAgICAgKlxuICAgICAqIEVuc3VyZXMgdGhhdCB0aGUgQ1NSRiB0b2tlbiBpcyBpbmNsdWRlZCBpbiBTbm93Ym9hcmQgcmVxdWVzdHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9uc1xuICAgICAqL1xuICAgIGFqYXhGZXRjaE9wdGlvbnMob3B0aW9ucykge1xuICAgICAgICBpZiAodGhpcy5oYXNUb2tlbigpKSB7XG4gICAgICAgICAgICBvcHRpb25zLmhlYWRlcnNbJ1gtQ1NSRi1UT0tFTiddID0gdGhpcy5nZXRUb2tlbigpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVXBkYXRlIGNvbXBsZXRlIGhhbmRsZXIuXG4gICAgICpcbiAgICAgKiBGaXJlcyBvZmYgYSBcInJlbmRlclwiIGV2ZW50IHdoZW4gcGFydGlhbHMgYXJlIHVwZGF0ZWQgc28gdGhhdCBhbnkgd2lkZ2V0cyBpbmNsdWRlZCBpblxuICAgICAqIHJlc3BvbnNlcyBhcmUgY29ycmVjdGx5IGluaXRpYWxpc2VkLlxuICAgICAqL1xuICAgIGFqYXhVcGRhdGVDb21wbGV0ZSgpIHtcbiAgICAgICAgaWYgKCF3aW5kb3cualF1ZXJ5KSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBBZGQgXCJyZW5kZXJcIiBldmVudCBmb3IgYmFja3dhcmRzIGNvbXBhdGliaWxpdHlcbiAgICAgICAgd2luZG93LmpRdWVyeShkb2N1bWVudCkudHJpZ2dlcigncmVuZGVyJyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGV0ZXJtaW5lcyBpZiBhIENTUkYgdG9rZW4gaXMgYXZhaWxhYmxlLlxuICAgICAqXG4gICAgICogQHJldHVybnMge0Jvb2xlYW59XG4gICAgICovXG4gICAgaGFzVG9rZW4oKSB7XG4gICAgICAgIGNvbnN0IHRva2VuRWxlbWVudCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ21ldGFbbmFtZT1cImNzcmYtdG9rZW5cIl0nKTtcblxuICAgICAgICBpZiAoIXRva2VuRWxlbWVudCkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG4gICAgICAgIGlmICghdG9rZW5FbGVtZW50Lmhhc0F0dHJpYnV0ZSgnY29udGVudCcpKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIHRoZSBDU1JGIHRva2VuLlxuICAgICAqXG4gICAgICogQHJldHVybnMge1N0cmluZ31cbiAgICAgKi9cbiAgICBnZXRUb2tlbigpIHtcbiAgICAgICAgcmV0dXJuIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ21ldGFbbmFtZT1cImNzcmYtdG9rZW5cIl0nKS5nZXRBdHRyaWJ1dGUoJ2NvbnRlbnQnKTtcbiAgICB9XG59XG4iLCJpbXBvcnQgQmFja2VuZEFqYXhIYW5kbGVyIGZyb20gJy4vYWpheC9IYW5kbGVyJztcbmltcG9ydCBCYWNrZW5kVWlFdmVudEhhbmRsZXIgZnJvbSAnLi91aS9FdmVudEhhbmRsZXInO1xuaW1wb3J0IEJhY2tlbmRVaVdpZGdldEhhbmRsZXIgZnJvbSAnLi91aS9XaWRnZXRIYW5kbGVyJztcblxuaWYgKHdpbmRvdy5Tbm93Ym9hcmQgPT09IHVuZGVmaW5lZCkge1xuICAgIHRocm93IG5ldyBFcnJvcignU25vd2JvYXJkIG11c3QgYmUgbG9hZGVkIGluIG9yZGVyIHRvIHVzZSB0aGUgQmFja2VuZCBVSS4nKTtcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdiYWNrZW5kLmFqYXguaGFuZGxlcicsIEJhY2tlbmRBamF4SGFuZGxlcik7XG4gICAgU25vd2JvYXJkLmFkZFBsdWdpbignYmFja2VuZC51aS5ldmVudEhhbmRsZXInLCBCYWNrZW5kVWlFdmVudEhhbmRsZXIpO1xuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2JhY2tlbmQudWkud2lkZ2V0SGFuZGxlcicsIEJhY2tlbmRVaVdpZGdldEhhbmRsZXIpO1xuXG4gICAgLy8gQWRkIHRoZSBwcmUtZmlsdGVyIGltbWVkaWF0ZWx5XG4gICAgU25vd2JvYXJkWydiYWNrZW5kLmFqYXguaGFuZGxlciddKCkuYWRkUHJlZmlsdGVyKCk7XG5cbiAgICAvLyBBZGQgcG9seWZpbGwgZm9yIEFzc2V0TWFuYWdlclxuICAgIHdpbmRvdy5Bc3NldE1hbmFnZXIgPSB7XG4gICAgICAgIGxvYWQ6IChhc3NldHMsIGNhbGxiYWNrKSA9PiB7XG4gICAgICAgICAgICBTbm93Ym9hcmQuYXNzZXRMb2FkZXIoKS5sb2FkKGFzc2V0cykudGhlbihcbiAgICAgICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChjYWxsYmFjayAmJiB0eXBlb2YgY2FsbGJhY2sgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgKTtcbiAgICAgICAgfSxcbiAgICB9O1xuICAgIHdpbmRvdy5hc3NldE1hbmFnZXIgPSB3aW5kb3cuQXNzZXRNYW5hZ2VyO1xufSkod2luZG93LlNub3dib2FyZCk7XG4iLCIvKipcbiAqIFdpZGdldCBldmVudCBoYW5kbGVyLlxuICpcbiAqIEV4dGVuZHMgYSB3aWRnZXQgd2l0aCBldmVudCBoYW5kbGluZyBmdW5jdGlvbmFsaXR5LCBhbGxvd2luZyBmb3IgdGhlIHF1aWNrIGRlZmluaXRpb24gb2YgZXZlbnRzXG4gKiBhbmQgbGlzdGVuaW5nIGZvciBldmVudHMgb24gYSBzcGVjaWZpYyBpbnN0YW5jZSBvZiBhIHdpZGdldC5cbiAqXG4gKiBUaGlzIGlzIGEgY29tcGxlbWVudCB0byBTbm93Ym9hcmQncyBnbG9iYWwgZXZlbnRzIC0gdGhlc2UgZXZlbnRzIHdpbGwgc3RpbGwgZmlyZSBpbiBvcmRlciB0b1xuICogYWxsb3cgZXh0ZXJuYWwgY29kZSB0byBsaXN0ZW4gYW5kIGhhbmRsZSBldmVudHMuIExvY2FsIGV2ZW50cyBjYW4gY2FuY2VsIHRoZSBnbG9iYWwgZXZlbnQgKGFuZFxuICogZnVydGhlciBsb2NhbCBldmVudHMpIGJ5IHJldHVybmluZyBgZmFsc2VgIGZyb20gdGhlIGNhbGxiYWNrLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMiBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFdmVudEhhbmRsZXIgZXh0ZW5kcyBTbm93Ym9hcmQuUGx1Z2luQmFzZSB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1BsdWdpbkJhc2V9IGluc3RhbmNlXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IGV2ZW50UHJlZml4XG4gICAgICovXG4gICAgY29uc3RydWN0KGluc3RhbmNlLCBldmVudFByZWZpeCkge1xuICAgICAgICBpZiAoaW5zdGFuY2UgaW5zdGFuY2VvZiBTbm93Ym9hcmQuUGx1Z2luQmFzZSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignRXZlbnQgaGFuZGxpbmcgY2FuIG9ubHkgYmUgYXBwbGllZCB0byBTbm93Ym9hcmQgY2xhc3Nlcy4nKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIWV2ZW50UHJlZml4KSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0V2ZW50IHByZWZpeCBpcyByZXF1aXJlZC4nKTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmluc3RhbmNlID0gaW5zdGFuY2U7XG4gICAgICAgIHRoaXMuZXZlbnRQcmVmaXggPSBldmVudFByZWZpeDtcbiAgICAgICAgdGhpcy5ldmVudHMgPSBbXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZWdpc3RlcnMgYSBsaXN0ZW5lciBmb3IgYSB3aWRnZXQncyBldmVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBldmVudFxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgb24oZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICAgIHRoaXMuZXZlbnRzLnB1c2goe1xuICAgICAgICAgICAgZXZlbnQsXG4gICAgICAgICAgICBjYWxsYmFjayxcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVyZWdpc3RlcnMgYSBsaXN0ZW5lciBmb3IgYSB3aWRnZXQncyBldmVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBldmVudFxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgb2ZmKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgICB0aGlzLmV2ZW50cyA9IHRoaXMuZXZlbnRzLmZpbHRlcigocmVnaXN0ZXJlZEV2ZW50KSA9PiByZWdpc3RlcmVkRXZlbnQuZXZlbnQgIT09IGV2ZW50IHx8IHJlZ2lzdGVyZWRFdmVudC5jYWxsYmFjayAhPT0gY2FsbGJhY2spO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlZ2lzdGVycyBhIGxpc3RlbmVyIGZvciBhIHdpZGdldCdzIGV2ZW50IHRoYXQgd2lsbCBvbmx5IGZpcmUgb25jZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBldmVudFxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgb25jZShldmVudCwgY2FsbGJhY2spIHtcbiAgICAgICAgY29uc3QgbGVuZ3RoID0gdGhpcy5ldmVudHMucHVzaCh7XG4gICAgICAgICAgICBldmVudCxcbiAgICAgICAgICAgIGNhbGxiYWNrOiAoLi4ucGFyYW1ldGVycykgPT4ge1xuICAgICAgICAgICAgICAgIGNhbGxiYWNrKC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICAgICAgICAgIHRoaXMuZXZlbnRzLnNwbGljZShsZW5ndGggLSAxLCAxKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZpcmVzIGFuIGV2ZW50IG9uIHRoZSB3aWRnZXQuXG4gICAgICpcbiAgICAgKiBMb2NhbCBldmVudHMgYXJlIGZpcmVkIGZpcnN0LCB0aGVuIGEgZ2xvYmFsIGV2ZW50IGlzIGZpcmVkIGFmdGVyd2FyZHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnROYW1lXG4gICAgICogQHBhcmFtICB7Li4uYW55fSBwYXJhbWV0ZXJzXG4gICAgICovXG4gICAgZmlyZShldmVudE5hbWUsIC4uLnBhcmFtZXRlcnMpIHtcbiAgICAgICAgLy8gRmlyZSBsb2NhbCBldmVudHMgZmlyc3RcbiAgICAgICAgY29uc3QgZXZlbnRzID0gdGhpcy5ldmVudHMuZmlsdGVyKChyZWdpc3RlcmVkRXZlbnQpID0+IHJlZ2lzdGVyZWRFdmVudC5ldmVudCA9PT0gZXZlbnROYW1lKTtcbiAgICAgICAgbGV0IGNhbmNlbGxlZCA9IGZhbHNlO1xuICAgICAgICBldmVudHMuZm9yRWFjaCgoZXZlbnQpID0+IHtcbiAgICAgICAgICAgIGlmIChjYW5jZWxsZWQpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoZXZlbnQuY2FsbGJhY2soLi4ucGFyYW1ldGVycykgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgY2FuY2VsbGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKCFjYW5jZWxsZWQpIHtcbiAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KGAke3RoaXMuZXZlbnRQcmVmaXh9LiR7ZXZlbnROYW1lfWAsIC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRmlyZXMgYSBwcm9taXNlIGV2ZW50IG9uIHRoZSB3aWRnZXQuXG4gICAgICpcbiAgICAgKiBMb2NhbCBldmVudHMgYXJlIGZpcmVkIGZpcnN0LCB0aGVuIGEgZ2xvYmFsIGV2ZW50IGlzIGZpcmVkIGFmdGVyd2FyZHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnROYW1lXG4gICAgICogQHBhcmFtICB7Li4uYW55fSBwYXJhbWV0ZXJzXG4gICAgICovXG4gICAgZmlyZVByb21pc2UoZXZlbnROYW1lLCAuLi5wYXJhbWV0ZXJzKSB7XG4gICAgICAgIGNvbnN0IGV2ZW50cyA9IHRoaXMuZXZlbnRzLmZpbHRlcigocmVnaXN0ZXJlZEV2ZW50KSA9PiByZWdpc3RlcmVkRXZlbnQuZXZlbnQgPT09IGV2ZW50TmFtZSk7XG4gICAgICAgIGNvbnN0IHByb21pc2VzID0gZXZlbnRzLmZpbHRlcigoZXZlbnQpID0+IGV2ZW50ICE9PSBudWxsLCBldmVudHMubWFwKChldmVudCkgPT4gZXZlbnQuY2FsbGJhY2soLi4ucGFyYW1ldGVycykpKTtcblxuICAgICAgICBQcm9taXNlLmFsbChwcm9taXNlcykudGhlbihcbiAgICAgICAgICAgICgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxQcm9taXNlRXZlbnQoYCR7dGhpcy5ldmVudFByZWZpeH0uJHtldmVudE5hbWV9YCwgLi4ucGFyYW1ldGVycyk7XG4gICAgICAgICAgICB9LFxuICAgICAgICApO1xuICAgIH1cbn1cbiIsIi8qKlxuICogQmFja2VuZCB3aWRnZXQgaGFuZGxlci5cbiAqXG4gKiBIYW5kbGVzIHRoZSBjcmVhdGlvbiBhbmQgZGlzcG9zYWwgb2Ygd2lkZ2V0cyBpbiB0aGUgQmFja2VuZC4gV2lkZ2V0cyBzaG91bGQgaW5jbHVkZSB0aGlzIGFzXG4gKiBhIGRlcGVuZGVuY3kgaW4gb3JkZXIgdG8gYmUgbG9hZGVkIGFuZCBpbml0aWFsaXNlZCBhZnRlciB0aGUgaGFuZGxlciwgaW4gb3JkZXIgdG8gY29ycmVjdGx5XG4gKiByZWdpc3Rlci5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjIgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgV2lkZ2V0SGFuZGxlciBleHRlbmRzIFNub3dib2FyZC5TaW5nbGV0b24ge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdCgpIHtcbiAgICAgICAgdGhpcy5yZWdpc3RlcmVkV2lkZ2V0cyA9IFtdO1xuICAgICAgICB0aGlzLmVsZW1lbnRzID0gW107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogTGlzdGVuZXJzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgcmVhZHk6ICdvblJlYWR5JyxcbiAgICAgICAgICAgIHJlbmRlcjogJ29uUmVhZHknLFxuICAgICAgICAgICAgYWpheFVwZGF0ZTogJ29uQWpheFVwZGF0ZScsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVnaXN0ZXJzIGEgd2lkZ2V0IGFzIGEgZ2l2ZW4gZGF0YSBjb250cm9sLlxuICAgICAqXG4gICAgICogUmVnaXN0ZXJpbmcgYSB3aWRnZXQgd2lsbCBhbGxvdyBhbnkgZWxlbWVudCB0aGF0IGNvbnRhaW5zIGEgXCJkYXRhLWNvbnRyb2xcIiBhdHRyaWJ1dGUgbWF0Y2hpbmdcbiAgICAgKiB0aGUgY29udHJvbCBuYW1lIHRvIGJlIGluaXRpYWxpemVkIHdpdGggdGhlIGdpdmVuIHdpZGdldC5cbiAgICAgKlxuICAgICAqIFlvdSBtYXkgb3B0aW9uYWxseSBwcm92aWRlIGEgY2FsbGJhY2sgdGhhdCB3aWxsIGJlIGZpcmVkIHdoZW4gYW4gaW5zdGFuY2Ugb2YgdGhlIHdpZGdldCBpc1xuICAgICAqIGluaXRpYWxpemVkIC0gdGhlIGNhbGxiYWNrIHdpbGwgYmUgcHJvdmlkZWQgdGhlIGVsZW1lbnQgYW5kIHRoZSB3aWRnZXQgaW5zdGFuY2UgYXMgcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBjb250cm9sXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmQuUGx1Z2luQmFzZX0gd2lkZ2V0XG4gICAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAgICAgKi9cbiAgICByZWdpc3Rlcihjb250cm9sLCB3aWRnZXQsIGNhbGxiYWNrKSB7XG4gICAgICAgIHRoaXMucmVnaXN0ZXJlZFdpZGdldHMucHVzaCh7XG4gICAgICAgICAgICBjb250cm9sLFxuICAgICAgICAgICAgd2lkZ2V0LFxuICAgICAgICAgICAgY2FsbGJhY2ssXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFVucmVnaXN0ZXJzIGEgZGF0YSBjb250cm9sLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IGNvbnRyb2xcbiAgICAgKi9cbiAgICB1bnJlZ2lzdGVyKGNvbnRyb2wpIHtcbiAgICAgICAgdGhpcy5yZWdpc3RlcmVkV2lkZ2V0cyA9IHRoaXMucmVnaXN0ZXJlZFdpZGdldHMuZmlsdGVyKCh3aWRnZXQpID0+IHdpZGdldC5jb250cm9sICE9PSBjb250cm9sKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZWFkeSBoYW5kbGVyLlxuICAgICAqXG4gICAgICogSW5pdGlhbGl6ZXMgd2lkZ2V0cyB3aXRoaW4gdGhlIGVudGlyZSBkb2N1bWVudC5cbiAgICAgKi9cbiAgICBvblJlYWR5KCkge1xuICAgICAgICB0aGlzLmluaXRpYWxpemVXaWRnZXRzKGRvY3VtZW50LmJvZHkpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFKQVggdXBkYXRlIGhhbmRsZXIuXG4gICAgICpcbiAgICAgKiBJbml0aWFsaXplcyB3aWRnZXRzIGluc2lkZSBhbiB1cGRhdGUgZWxlbWVudCBmcm9tIGFuIEFKQVggcmVzcG9uc2UuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICovXG4gICAgb25BamF4VXBkYXRlKGVsZW1lbnQpIHtcbiAgICAgICAgdGhpcy5pbml0aWFsaXplV2lkZ2V0cyhlbGVtZW50KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplcyBhbGwgd2lkZ2V0cyB3aXRoaW4gYW4gZWxlbWVudC5cbiAgICAgKlxuICAgICAqIElmIGFuIGVsZW1lbnQgY29udGFpbnMgYSBcImRhdGEtY29udHJvbFwiIGF0dHJpYnV0ZSBtYXRjaGluZyBhIHJlZ2lzdGVyZWQgd2lkZ2V0LCB0aGUgd2lkZ2V0XG4gICAgICogaXMgaW5pdGlhbGl6ZWQgYW5kIGF0dGFjaGVkIHRvIHRoZSBlbGVtZW50IGFzIGEgXCJ3aWRnZXRcIiBwcm9wZXJ0eS5cbiAgICAgKlxuICAgICAqIE9ubHkgb25lIHdpZGdldCBtYXkgYmUgaW5pdGlhbGl6ZWQgdG8gYSBwYXJ0aWN1bGFyIGVsZW1lbnQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICovXG4gICAgaW5pdGlhbGl6ZVdpZGdldHMoZWxlbWVudCkge1xuICAgICAgICB0aGlzLnJlZ2lzdGVyZWRXaWRnZXRzLmZvckVhY2goKHdpZGdldCkgPT4ge1xuICAgICAgICAgICAgY29uc3QgaW5zdGFuY2VzID0gZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKGBbZGF0YS1jb250cm9sPVwiJHt3aWRnZXQuY29udHJvbH1cIl06bm90KFtkYXRhLXdpZGdldC1pbml0aWFsaXplZF0pYCk7XG5cbiAgICAgICAgICAgIGlmIChpbnN0YW5jZXMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgaW5zdGFuY2VzLmZvckVhY2goKGluc3RhbmNlKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFByZXZlbnQgZG91YmxlLXdpZGdldCBpbml0aWFsaXphdGlvblxuICAgICAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2UuZGF0YXNldC53aWRnZXRJbml0aWFsaXplZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3Qgd2lkZ2V0SW5zdGFuY2UgPSB0aGlzLnNub3dib2FyZFt3aWRnZXQud2lkZ2V0XShpbnN0YW5jZSk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudHMucHVzaCh7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbGVtZW50OiBpbnN0YW5jZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGluc3RhbmNlOiB3aWRnZXRJbnN0YW5jZSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIGluc3RhbmNlLmRhdGFzZXQud2lkZ2V0SW5pdGlhbGl6ZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYmFja2VuZC53aWRnZXQuaW5pdGlhbGl6ZWQnLCBpbnN0YW5jZSwgd2lkZ2V0SW5zdGFuY2UpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2Ygd2lkZ2V0LmNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB3aWRnZXQuY2FsbGJhY2sod2lkZ2V0SW5zdGFuY2UsIGluc3RhbmNlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGEgd2lkZ2V0IHRoYXQgaXMgYXR0YWNoZWQgdG8gdGhlIGdpdmVuIGVsZW1lbnQsIGlmIGFueS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsZW1lbnRcbiAgICAgKiBAcmV0dXJucyB7U25vd2JvYXJkLlBsdWdpbkJhc2V8bnVsbH1cbiAgICAgKi9cbiAgICBnZXRXaWRnZXQoZWxlbWVudCkge1xuICAgICAgICBjb25zdCBmb3VuZCA9IHRoaXMuZWxlbWVudHMuZmluZCgod2lkZ2V0KSA9PiB3aWRnZXQuZWxlbWVudCA9PT0gZWxlbWVudCk7XG5cbiAgICAgICAgaWYgKGZvdW5kKSB7XG4gICAgICAgICAgICByZXR1cm4gZm91bmQuaW5zdGFuY2U7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG59XG4iLCJ2YXIgdD1mdW5jdGlvbih0LGUpe3ZvaWQgMD09PWUmJihlPVtcImV2ZW50XCJdKTt2YXIgbj10LnN0YXJ0c1dpdGgoXCIkXCIpP2Z1bmN0aW9uKCl7cmV0dXJuIHdpbmRvdy4kKGRvY3VtZW50KS50cmlnZ2VyKHQuc2xpY2UoMSksW10uc2xpY2UuY2FsbChhcmd1bWVudHMpWzBdLmRldGFpbCl9OmZ1bmN0aW9uKCl7dmFyIG49YXJndW1lbnRzLGk9ZS5yZWR1Y2UoZnVuY3Rpb24odCxlLGkpe3JldHVybiB0W2VdPVtdLnNsaWNlLmNhbGwobilbaV0sdH0se30pO2kuZXZlbnQudGFyZ2V0LmRpc3BhdGNoRXZlbnQobmV3IEN1c3RvbUV2ZW50KFwiJFwiK3Qse2RldGFpbDppLGJ1YmJsZXM6ITAsY2FuY2VsYWJsZTohMH0pKX07cmV0dXJuIHQuc3RhcnRzV2l0aChcIiRcIik/ZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcih0LG4pOndpbmRvdy4kKGRvY3VtZW50KS5vbih0LG4pLG59LGU9ZnVuY3Rpb24odCxlKXt0LnN0YXJ0c1dpdGgoXCIkXCIpP2RvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIodCxlKTp3aW5kb3cuJChkb2N1bWVudCkub2ZmKHQsZSl9O2V4cG9ydHtlIGFzIGFibmVnYXRlLHQgYXMgZGVsZWdhdGV9O1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9aW5kZXgubS5qcy5tYXBcbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4vLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuX193ZWJwYWNrX3JlcXVpcmVfXy5tID0gX193ZWJwYWNrX21vZHVsZXNfXztcblxuIiwidmFyIGRlZmVycmVkID0gW107XG5fX3dlYnBhY2tfcmVxdWlyZV9fLk8gPSAocmVzdWx0LCBjaHVua0lkcywgZm4sIHByaW9yaXR5KSA9PiB7XG5cdGlmKGNodW5rSWRzKSB7XG5cdFx0cHJpb3JpdHkgPSBwcmlvcml0eSB8fCAwO1xuXHRcdGZvcih2YXIgaSA9IGRlZmVycmVkLmxlbmd0aDsgaSA+IDAgJiYgZGVmZXJyZWRbaSAtIDFdWzJdID4gcHJpb3JpdHk7IGktLSkgZGVmZXJyZWRbaV0gPSBkZWZlcnJlZFtpIC0gMV07XG5cdFx0ZGVmZXJyZWRbaV0gPSBbY2h1bmtJZHMsIGZuLCBwcmlvcml0eV07XG5cdFx0cmV0dXJuO1xuXHR9XG5cdHZhciBub3RGdWxmaWxsZWQgPSBJbmZpbml0eTtcblx0Zm9yICh2YXIgaSA9IDA7IGkgPCBkZWZlcnJlZC5sZW5ndGg7IGkrKykge1xuXHRcdHZhciBbY2h1bmtJZHMsIGZuLCBwcmlvcml0eV0gPSBkZWZlcnJlZFtpXTtcblx0XHR2YXIgZnVsZmlsbGVkID0gdHJ1ZTtcblx0XHRmb3IgKHZhciBqID0gMDsgaiA8IGNodW5rSWRzLmxlbmd0aDsgaisrKSB7XG5cdFx0XHRpZiAoKHByaW9yaXR5ICYgMSA9PT0gMCB8fCBub3RGdWxmaWxsZWQgPj0gcHJpb3JpdHkpICYmIE9iamVjdC5rZXlzKF9fd2VicGFja19yZXF1aXJlX18uTykuZXZlcnkoKGtleSkgPT4gKF9fd2VicGFja19yZXF1aXJlX18uT1trZXldKGNodW5rSWRzW2pdKSkpKSB7XG5cdFx0XHRcdGNodW5rSWRzLnNwbGljZShqLS0sIDEpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0ZnVsZmlsbGVkID0gZmFsc2U7XG5cdFx0XHRcdGlmKHByaW9yaXR5IDwgbm90RnVsZmlsbGVkKSBub3RGdWxmaWxsZWQgPSBwcmlvcml0eTtcblx0XHRcdH1cblx0XHR9XG5cdFx0aWYoZnVsZmlsbGVkKSB7XG5cdFx0XHRkZWZlcnJlZC5zcGxpY2UoaS0tLCAxKVxuXHRcdFx0dmFyIHIgPSBmbigpO1xuXHRcdFx0aWYgKHIgIT09IHVuZGVmaW5lZCkgcmVzdWx0ID0gcjtcblx0XHR9XG5cdH1cblx0cmV0dXJuIHJlc3VsdDtcbn07IiwiLy8gZGVmaW5lIGdldHRlciBmdW5jdGlvbnMgZm9yIGhhcm1vbnkgZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5kID0gKGV4cG9ydHMsIGRlZmluaXRpb24pID0+IHtcblx0Zm9yKHZhciBrZXkgaW4gZGVmaW5pdGlvbikge1xuXHRcdGlmKF9fd2VicGFja19yZXF1aXJlX18ubyhkZWZpbml0aW9uLCBrZXkpICYmICFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywga2V5KSkge1xuXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIGtleSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGRlZmluaXRpb25ba2V5XSB9KTtcblx0XHR9XG5cdH1cbn07IiwiX193ZWJwYWNrX3JlcXVpcmVfXy5vID0gKG9iaiwgcHJvcCkgPT4gKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmosIHByb3ApKSIsIi8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uciA9IChleHBvcnRzKSA9PiB7XG5cdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuXHR9XG5cdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG59OyIsIi8vIG5vIGJhc2VVUklcblxuLy8gb2JqZWN0IHRvIHN0b3JlIGxvYWRlZCBhbmQgbG9hZGluZyBjaHVua3Ncbi8vIHVuZGVmaW5lZCA9IGNodW5rIG5vdCBsb2FkZWQsIG51bGwgPSBjaHVuayBwcmVsb2FkZWQvcHJlZmV0Y2hlZFxuLy8gW3Jlc29sdmUsIHJlamVjdCwgUHJvbWlzZV0gPSBjaHVuayBsb2FkaW5nLCAwID0gY2h1bmsgbG9hZGVkXG52YXIgaW5zdGFsbGVkQ2h1bmtzID0ge1xuXHRcIi9hc3NldHMvdWkvanMvYnVpbGQvYmFja2VuZFwiOiAwLFxuXHRcImZvcm13aWRnZXRzL3Zpc3VhbGVkaXRvci9hc3NldHMvY3NzL3Zpc3VhbGVkaXRvclwiOiAwXG59O1xuXG4vLyBubyBjaHVuayBvbiBkZW1hbmQgbG9hZGluZ1xuXG4vLyBubyBwcmVmZXRjaGluZ1xuXG4vLyBubyBwcmVsb2FkZWRcblxuLy8gbm8gSE1SXG5cbi8vIG5vIEhNUiBtYW5pZmVzdFxuXG5fX3dlYnBhY2tfcmVxdWlyZV9fLk8uaiA9IChjaHVua0lkKSA9PiAoaW5zdGFsbGVkQ2h1bmtzW2NodW5rSWRdID09PSAwKTtcblxuLy8gaW5zdGFsbCBhIEpTT05QIGNhbGxiYWNrIGZvciBjaHVuayBsb2FkaW5nXG52YXIgd2VicGFja0pzb25wQ2FsbGJhY2sgPSAocGFyZW50Q2h1bmtMb2FkaW5nRnVuY3Rpb24sIGRhdGEpID0+IHtcblx0dmFyIFtjaHVua0lkcywgbW9yZU1vZHVsZXMsIHJ1bnRpbWVdID0gZGF0YTtcblx0Ly8gYWRkIFwibW9yZU1vZHVsZXNcIiB0byB0aGUgbW9kdWxlcyBvYmplY3QsXG5cdC8vIHRoZW4gZmxhZyBhbGwgXCJjaHVua0lkc1wiIGFzIGxvYWRlZCBhbmQgZmlyZSBjYWxsYmFja1xuXHR2YXIgbW9kdWxlSWQsIGNodW5rSWQsIGkgPSAwO1xuXHRpZihjaHVua0lkcy5zb21lKChpZCkgPT4gKGluc3RhbGxlZENodW5rc1tpZF0gIT09IDApKSkge1xuXHRcdGZvcihtb2R1bGVJZCBpbiBtb3JlTW9kdWxlcykge1xuXHRcdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKG1vcmVNb2R1bGVzLCBtb2R1bGVJZCkpIHtcblx0XHRcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tW21vZHVsZUlkXSA9IG1vcmVNb2R1bGVzW21vZHVsZUlkXTtcblx0XHRcdH1cblx0XHR9XG5cdFx0aWYocnVudGltZSkgdmFyIHJlc3VsdCA9IHJ1bnRpbWUoX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cdH1cblx0aWYocGFyZW50Q2h1bmtMb2FkaW5nRnVuY3Rpb24pIHBhcmVudENodW5rTG9hZGluZ0Z1bmN0aW9uKGRhdGEpO1xuXHRmb3IoO2kgPCBjaHVua0lkcy5sZW5ndGg7IGkrKykge1xuXHRcdGNodW5rSWQgPSBjaHVua0lkc1tpXTtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oaW5zdGFsbGVkQ2h1bmtzLCBjaHVua0lkKSAmJiBpbnN0YWxsZWRDaHVua3NbY2h1bmtJZF0pIHtcblx0XHRcdGluc3RhbGxlZENodW5rc1tjaHVua0lkXVswXSgpO1xuXHRcdH1cblx0XHRpbnN0YWxsZWRDaHVua3NbY2h1bmtJZF0gPSAwO1xuXHR9XG5cdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fLk8ocmVzdWx0KTtcbn1cblxudmFyIGNodW5rTG9hZGluZ0dsb2JhbCA9IHNlbGZbXCJ3ZWJwYWNrQ2h1bmtfd2ludGVyY21zX3duX2JhY2tlbmRfbW9kdWxlXCJdID0gc2VsZltcIndlYnBhY2tDaHVua193aW50ZXJjbXNfd25fYmFja2VuZF9tb2R1bGVcIl0gfHwgW107XG5jaHVua0xvYWRpbmdHbG9iYWwuZm9yRWFjaCh3ZWJwYWNrSnNvbnBDYWxsYmFjay5iaW5kKG51bGwsIDApKTtcbmNodW5rTG9hZGluZ0dsb2JhbC5wdXNoID0gd2VicGFja0pzb25wQ2FsbGJhY2suYmluZChudWxsLCBjaHVua0xvYWRpbmdHbG9iYWwucHVzaC5iaW5kKGNodW5rTG9hZGluZ0dsb2JhbCkpOyIsIiIsIi8vIHN0YXJ0dXBcbi8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuLy8gVGhpcyBlbnRyeSBtb2R1bGUgZGVwZW5kcyBvbiBvdGhlciBsb2FkZWQgY2h1bmtzIGFuZCBleGVjdXRpb24gbmVlZCB0byBiZSBkZWxheWVkXG5fX3dlYnBhY2tfcmVxdWlyZV9fLk8odW5kZWZpbmVkLCBbXCJmb3Jtd2lkZ2V0cy92aXN1YWxlZGl0b3IvYXNzZXRzL2Nzcy92aXN1YWxlZGl0b3JcIl0sICgpID0+IChfX3dlYnBhY2tfcmVxdWlyZV9fKFwiLi9hc3NldHMvdWkvanMvaW5kZXguanNcIikpKVxudmFyIF9fd2VicGFja19leHBvcnRzX18gPSBfX3dlYnBhY2tfcmVxdWlyZV9fLk8odW5kZWZpbmVkLCBbXCJmb3Jtd2lkZ2V0cy92aXN1YWxlZGl0b3IvYXNzZXRzL2Nzcy92aXN1YWxlZGl0b3JcIl0sICgpID0+IChfX3dlYnBhY2tfcmVxdWlyZV9fKFwiLi9mb3Jtd2lkZ2V0cy92aXN1YWxlZGl0b3IvYXNzZXRzL2xlc3MvdmlzdWFsZWRpdG9yLmxlc3NcIikpKVxuX193ZWJwYWNrX2V4cG9ydHNfXyA9IF9fd2VicGFja19yZXF1aXJlX18uTyhfX3dlYnBhY2tfZXhwb3J0c19fKTtcbiIsIiJdLCJuYW1lcyI6WyJkZWxlZ2F0ZSIsIkhhbmRsZXIiLCJyZWFkeSIsImFqYXhGZXRjaE9wdGlvbnMiLCJhamF4VXBkYXRlQ29tcGxldGUiLCJ3aW5kb3ciLCJqUXVlcnkiLCJkb2N1bWVudCIsInRyaWdnZXIiLCJhZGRFdmVudExpc3RlbmVyIiwic25vd2JvYXJkIiwiZ2xvYmFsRXZlbnQiLCJhamF4UHJlZmlsdGVyIiwib3B0aW9ucyIsImhhc1Rva2VuIiwiaGVhZGVycyIsImdldFRva2VuIiwidG9rZW5FbGVtZW50IiwicXVlcnlTZWxlY3RvciIsImhhc0F0dHJpYnV0ZSIsImdldEF0dHJpYnV0ZSIsIlNub3dib2FyZCIsIlNpbmdsZXRvbiIsIkJhY2tlbmRBamF4SGFuZGxlciIsIkJhY2tlbmRVaUV2ZW50SGFuZGxlciIsIkJhY2tlbmRVaVdpZGdldEhhbmRsZXIiLCJ1bmRlZmluZWQiLCJFcnJvciIsImFkZFBsdWdpbiIsImFkZFByZWZpbHRlciIsIkFzc2V0TWFuYWdlciIsImxvYWQiLCJhc3NldHMiLCJjYWxsYmFjayIsImFzc2V0TG9hZGVyIiwidGhlbiIsImFzc2V0TWFuYWdlciIsIkV2ZW50SGFuZGxlciIsImluc3RhbmNlIiwiZXZlbnRQcmVmaXgiLCJQbHVnaW5CYXNlIiwiZXZlbnRzIiwiZXZlbnQiLCJwdXNoIiwiZmlsdGVyIiwicmVnaXN0ZXJlZEV2ZW50IiwibGVuZ3RoIiwic3BsaWNlIiwiZXZlbnROYW1lIiwicGFyYW1ldGVycyIsImNhbmNlbGxlZCIsImZvckVhY2giLCJwcm9taXNlcyIsIm1hcCIsIlByb21pc2UiLCJhbGwiLCJnbG9iYWxQcm9taXNlRXZlbnQiLCJXaWRnZXRIYW5kbGVyIiwicmVnaXN0ZXJlZFdpZGdldHMiLCJlbGVtZW50cyIsInJlbmRlciIsImFqYXhVcGRhdGUiLCJjb250cm9sIiwid2lkZ2V0IiwiaW5pdGlhbGl6ZVdpZGdldHMiLCJib2R5IiwiZWxlbWVudCIsImluc3RhbmNlcyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJkYXRhc2V0Iiwid2lkZ2V0SW5pdGlhbGl6ZWQiLCJ3aWRnZXRJbnN0YW5jZSIsImZvdW5kIiwiZmluZCJdLCJzb3VyY2VSb290IjoiIn0=