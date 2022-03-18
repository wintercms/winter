"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/js/snowboard/build/snowboard.extras"],{

/***/ "./assets/js/snowboard/extras/AttachLoading.js":
/*!*****************************************************!*\
  !*** ./assets/js/snowboard/extras/AttachLoading.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ AttachLoading)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Allows attaching a loading class on elements that an AJAX request is targeting.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var AttachLoading = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(AttachLoading, _Snowboard$Singleton);

  var _super = _createSuper(AttachLoading);

  function AttachLoading() {
    _classCallCheck(this, AttachLoading);

    return _super.apply(this, arguments);
  }

  _createClass(AttachLoading, [{
    key: "dependencies",
    value:
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    function dependencies() {
      return ['request'];
    }
    /**
     * Defines listeners.
     *
     * @returns {Object}
     */

  }, {
    key: "listens",
    value: function listens() {
      return {
        ajaxStart: 'ajaxStart',
        ajaxDone: 'ajaxDone'
      };
    }
  }, {
    key: "ajaxStart",
    value: function ajaxStart(promise, request) {
      var _this = this;

      if (!request.element) {
        return;
      }

      if (request.element.tagName === 'FORM') {
        var loadElements = request.element.querySelectorAll('[data-attach-loading]');

        if (loadElements.length > 0) {
          loadElements.forEach(function (element) {
            element.classList.add(_this.getLoadingClass(element));
          });
        }
      } else if (request.element.dataset.attachLoading !== undefined) {
        request.element.classList.add(this.getLoadingClass(request.element));
      }
    }
  }, {
    key: "ajaxDone",
    value: function ajaxDone(data, request) {
      var _this2 = this;

      if (!request.element) {
        return;
      }

      if (request.element.tagName === 'FORM') {
        var loadElements = request.element.querySelectorAll('[data-attach-loading]');

        if (loadElements.length > 0) {
          loadElements.forEach(function (element) {
            element.classList.remove(_this2.getLoadingClass(element));
          });
        }
      } else if (request.element.dataset.attachLoading !== undefined) {
        request.element.classList.remove(this.getLoadingClass(request.element));
      }
    }
  }, {
    key: "getLoadingClass",
    value: function getLoadingClass(element) {
      return element.dataset.attachLoading !== undefined && element.dataset.attachLoading !== '' ? element.dataset.attachLoading : 'wn-loading';
    }
  }]);

  return AttachLoading;
}(Snowboard.Singleton);



/***/ }),

/***/ "./assets/js/snowboard/extras/Flash.js":
/*!*********************************************!*\
  !*** ./assets/js/snowboard/extras/Flash.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Flash)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _get() { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(arguments.length < 3 ? target : receiver); } return desc.value; }; } return _get.apply(this, arguments); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Provides flash messages for the CMS.
 *
 * Flash messages will pop up at the top center of the page and will remain for 7 seconds by default. Hovering over
 * the message will reset and pause the timer. Clicking on the flash message will dismiss it.
 *
 * Arguments:
 *  - "message": The content of the flash message. HTML is accepted.
 *  - "type": The type of flash message. This is appended as a class to the flash message itself.
 *  - "duration": How long the flash message will stay visible for, in seconds. Default: 7 seconds.
 *
 * Usage:
 *      Snowboard.flash('This is a flash message', 'info', 8);
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Flash = /*#__PURE__*/function (_Snowboard$PluginBase) {
  _inherits(Flash, _Snowboard$PluginBase);

  var _super = _createSuper(Flash);

  /**
   * Constructor.
   *
   * @param {Snowboard} snowboard
   * @param {string} message
   * @param {string} type
   * @param {Number} duration
   */
  function Flash(snowboard, message, type, duration) {
    var _this;

    _classCallCheck(this, Flash);

    _this = _super.call(this, snowboard);
    _this.message = message;
    _this.type = type || 'default';
    _this.duration = duration || 7;

    _this.clear();

    _this.timer = null;
    _this.flashTimer = null;

    _this.create();

    return _this;
  }
  /**
   * Defines dependencies.
   *
   * @returns {string[]}
   */


  _createClass(Flash, [{
    key: "dependencies",
    value: function dependencies() {
      return ['transition'];
    }
    /**
     * Destructor.
     *
     * This will ensure the flash message is removed and timeout is cleared if the module is removed.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      if (this.timer !== null) {
        window.clearTimeout(this.timer);
      }

      if (this.flash) {
        this.flashTimer.remove();
        this.flash.remove();
        this.flash = null;
        this.flashTimer = null;
      }

      _get(_getPrototypeOf(Flash.prototype), "destructor", this).call(this);
    }
    /**
     * Creates the flash message.
     */

  }, {
    key: "create",
    value: function create() {
      var _this2 = this;

      this.flash = document.createElement('DIV');
      this.flashTimer = document.createElement('DIV');
      this.flash.innerHTML = this.message;
      this.flash.classList.add('flash-message', this.type);
      this.flashTimer.classList.add('flash-timer');
      this.flash.removeAttribute('data-control');
      this.flash.addEventListener('click', function () {
        return _this2.remove();
      });
      this.flash.addEventListener('mouseover', function () {
        return _this2.stopTimer();
      });
      this.flash.addEventListener('mouseout', function () {
        return _this2.startTimer();
      }); // Add to body

      this.flash.appendChild(this.flashTimer);
      document.body.appendChild(this.flash);
      this.snowboard.transition(this.flash, 'show', function () {
        _this2.startTimer();
      });
    }
    /**
     * Removes the flash message.
     */

  }, {
    key: "remove",
    value: function remove() {
      var _this3 = this;

      this.stopTimer();
      this.snowboard.transition(this.flash, 'hide', function () {
        _this3.flash.remove();

        _this3.flash = null;

        _this3.destructor();
      });
    }
    /**
     * Clears all flash messages available on the page.
     */

  }, {
    key: "clear",
    value: function clear() {
      document.querySelectorAll('body > div.flash-message').forEach(function (element) {
        return element.remove();
      });
    }
    /**
     * Starts the timer for this flash message.
     */

  }, {
    key: "startTimer",
    value: function startTimer() {
      var _this4 = this;

      this.timerTrans = this.snowboard.transition(this.flashTimer, 'timeout', null, "".concat(this.duration, ".0s"), true);
      this.timer = window.setTimeout(function () {
        return _this4.remove();
      }, this.duration * 1000);
    }
    /**
     * Resets the timer for this flash message.
     */

  }, {
    key: "stopTimer",
    value: function stopTimer() {
      if (this.timerTrans) {
        this.timerTrans.cancel();
      }

      window.clearTimeout(this.timer);
    }
  }]);

  return Flash;
}(Snowboard.PluginBase);



/***/ }),

/***/ "./assets/js/snowboard/extras/FlashListener.js":
/*!*****************************************************!*\
  !*** ./assets/js/snowboard/extras/FlashListener.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FlashListener)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Defines a default listener for flash events.
 *
 * Connects the Flash plugin to various events that use flash messages.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var FlashListener = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(FlashListener, _Snowboard$Singleton);

  var _super = _createSuper(FlashListener);

  function FlashListener() {
    _classCallCheck(this, FlashListener);

    return _super.apply(this, arguments);
  }

  _createClass(FlashListener, [{
    key: "dependencies",
    value:
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    function dependencies() {
      return ['flash'];
    }
    /**
     * Defines listeners.
     *
     * @returns {Object}
     */

  }, {
    key: "listens",
    value: function listens() {
      return {
        ready: 'ready',
        ajaxErrorMessage: 'ajaxErrorMessage',
        ajaxFlashMessages: 'ajaxFlashMessages'
      };
    }
    /**
     * Do flash messages for PHP flash responses.
     */

  }, {
    key: "ready",
    value: function ready() {
      var _this = this;

      document.querySelectorAll('[data-control="flash-message"]').forEach(function (element) {
        _this.snowboard.flash(element.innerHTML, element.dataset.flashType, element.dataset.flashDuration);
      });
    }
    /**
     * Shows a flash message for AJAX errors.
     *
     * @param {string} message
     * @returns {Boolean}
     */

  }, {
    key: "ajaxErrorMessage",
    value: function ajaxErrorMessage(message) {
      this.snowboard.flash(message, 'error');
      return false;
    }
    /**
     * Shows flash messages returned directly from AJAX functionality.
     *
     * @param {Object} messages
     */

  }, {
    key: "ajaxFlashMessages",
    value: function ajaxFlashMessages(messages) {
      var _this2 = this;

      Object.entries(messages).forEach(function (entry) {
        var _entry = _slicedToArray(entry, 2),
            cssClass = _entry[0],
            message = _entry[1];

        _this2.snowboard.flash(message, cssClass);
      });
      return false;
    }
  }]);

  return FlashListener;
}(Snowboard.Singleton);



/***/ }),

/***/ "./assets/js/snowboard/extras/StripeLoader.js":
/*!****************************************************!*\
  !*** ./assets/js/snowboard/extras/StripeLoader.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ StripeLoader)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Displays a stripe at the top of the page that indicates loading.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var StripeLoader = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(StripeLoader, _Snowboard$Singleton);

  var _super = _createSuper(StripeLoader);

  function StripeLoader() {
    _classCallCheck(this, StripeLoader);

    return _super.apply(this, arguments);
  }

  _createClass(StripeLoader, [{
    key: "dependencies",
    value:
    /**
     * Defines dependenices.
     *
     * @returns {string[]}
     */
    function dependencies() {
      return ['request'];
    }
    /**
     * Defines listeners.
     *
     * @returns {Object}
     */

  }, {
    key: "listens",
    value: function listens() {
      return {
        ready: 'ready',
        ajaxStart: 'ajaxStart'
      };
    }
  }, {
    key: "ready",
    value: function ready() {
      this.counter = 0;
      this.createStripe();
    }
  }, {
    key: "ajaxStart",
    value: function ajaxStart(promise) {
      var _this = this;

      this.show();
      promise["catch"](function () {
        _this.hide();
      })["finally"](function () {
        _this.hide();
      });
    }
  }, {
    key: "createStripe",
    value: function createStripe() {
      this.indicator = document.createElement('DIV');
      this.stripe = document.createElement('DIV');
      this.stripeLoaded = document.createElement('DIV');
      this.indicator.classList.add('stripe-loading-indicator', 'loaded');
      this.stripe.classList.add('stripe');
      this.stripeLoaded.classList.add('stripe-loaded');
      this.indicator.appendChild(this.stripe);
      this.indicator.appendChild(this.stripeLoaded);
      document.body.appendChild(this.indicator);
    }
  }, {
    key: "show",
    value: function show() {
      this.counter += 1;
      var newStripe = this.stripe.cloneNode(true);
      this.indicator.appendChild(newStripe);
      this.stripe.remove();
      this.stripe = newStripe;

      if (this.counter > 1) {
        return;
      }

      this.indicator.classList.remove('loaded');
      document.body.classList.add('wn-loading');
    }
  }, {
    key: "hide",
    value: function hide(force) {
      this.counter -= 1;

      if (force === true) {
        this.counter = 0;
      }

      if (this.counter <= 0) {
        this.indicator.classList.add('loaded');
        document.body.classList.remove('wn-loading');
      }
    }
  }]);

  return StripeLoader;
}(Snowboard.Singleton);



/***/ }),

/***/ "./assets/js/snowboard/extras/StylesheetLoader.js":
/*!********************************************************!*\
  !*** ./assets/js/snowboard/extras/StylesheetLoader.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ StylesheetLoader)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Embeds the "extras" stylesheet into the page, if it is not loaded through the theme.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var StylesheetLoader = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(StylesheetLoader, _Snowboard$Singleton);

  var _super = _createSuper(StylesheetLoader);

  function StylesheetLoader() {
    _classCallCheck(this, StylesheetLoader);

    return _super.apply(this, arguments);
  }

  _createClass(StylesheetLoader, [{
    key: "listens",
    value:
    /**
     * Defines listeners.
     *
     * @returns {Object}
     */
    function listens() {
      return {
        ready: 'ready'
      };
    }
  }, {
    key: "ready",
    value: function ready() {
      var stylesLoaded = false; // Determine if stylesheet is already loaded

      document.querySelectorAll('link[rel="stylesheet"]').forEach(function (css) {
        if (css.href.endsWith('/modules/system/assets/css/snowboard.extras.css')) {
          stylesLoaded = true;
        }
      });

      if (!stylesLoaded) {
        var stylesheet = document.createElement('link');
        stylesheet.setAttribute('rel', 'stylesheet');
        stylesheet.setAttribute('href', '/modules/system/assets/css/snowboard.extras.css');
        document.head.appendChild(stylesheet);
      }
    }
  }]);

  return StylesheetLoader;
}(Snowboard.Singleton);



/***/ }),

/***/ "./assets/js/snowboard/extras/Transition.js":
/*!**************************************************!*\
  !*** ./assets/js/snowboard/extras/Transition.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Transition)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * Provides transition support for elements.
 *
 * Transition allows CSS transitions to be controlled and callbacks to be run once completed. It works similar to Vue
 * transitions with 3 stages of transition, and classes assigned to the element with the transition name suffixed with
 * the stage of transition:
 *
 *  - `in`: A class assigned to the element for the first frame of the transition, removed afterwards. This should be
 *      used to define the initial state of the transition.
 *  - `active`: A class assigned to the element for the duration of the transition. This should be used to define the
 *      transition itself.
 *  - `out`: A class assigned to the element after the first frame of the transition and kept to the end of the
 *      transition. This should define the end state of the transition.
 *
 * Usage:
 *      Snowboard.transition(document.element, 'transition', () => {
 *          console.log('Remove element after 7 seconds');
 *          this.remove();
 *      }, '7s');
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Transition = /*#__PURE__*/function (_Snowboard$PluginBase) {
  _inherits(Transition, _Snowboard$PluginBase);

  var _super = _createSuper(Transition);

  /**
   * Constructor.
   *
   * @param {Snowboard} snowboard
   * @param {HTMLElement} element The element to transition
   * @param {string} transition The name of the transition, this prefixes the stages of transition.
   * @param {Function} callback An optional callback to call when the transition ends.
   * @param {Number} duration An optional override on the transition duration. Must be specified as 's' (secs) or 'ms' (msecs).
   * @param {Boolean} trailTo If true, the "out" class will remain after the end of the transition.
   */
  function Transition(snowboard, element, transition, callback, duration, trailTo) {
    var _this;

    _classCallCheck(this, Transition);

    _this = _super.call(this, snowboard);

    if (element instanceof HTMLElement === false) {
      throw new Error('A HTMLElement must be provided for transitioning');
    }

    _this.element = element;

    if (typeof transition !== 'string') {
      throw new Error('Transition name must be specified as a string');
    }

    _this.transition = transition;

    if (callback && typeof callback !== 'function') {
      throw new Error('Callback must be a valid function');
    }

    _this.callback = callback;

    if (duration) {
      _this.duration = _this.parseDuration(duration);
    } else {
      _this.duration = null;
    }

    _this.trailTo = trailTo === true;

    _this.doTransition();

    return _this;
  }
  /**
   * Maps event classes to the given transition state.
   *
   * @param  {...any} args
   * @returns {Array}
   */


  _createClass(Transition, [{
    key: "eventClasses",
    value: function eventClasses() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      var eventClasses = {
        "in": "".concat(this.transition, "-in"),
        active: "".concat(this.transition, "-active"),
        out: "".concat(this.transition, "-out")
      };

      if (args.length === 0) {
        return Object.values(eventClasses);
      }

      var returnClasses = [];
      Object.entries(eventClasses).forEach(function (entry) {
        var _entry = _slicedToArray(entry, 2),
            key = _entry[0],
            value = _entry[1];

        if (args.indexOf(key) !== -1) {
          returnClasses.push(value);
        }
      });
      return returnClasses;
    }
    /**
     * Executes the transition.
     *
     * @returns {void}
     */

  }, {
    key: "doTransition",
    value: function doTransition() {
      var _this2 = this;

      // Add duration override
      if (this.duration !== null) {
        this.element.style.transitionDuration = this.duration;
      }

      this.resetClasses(); // Start transition - show "in" and "active" classes

      this.eventClasses('in', 'active').forEach(function (eventClass) {
        _this2.element.classList.add(eventClass);
      });
      window.requestAnimationFrame(function () {
        // Ensure a transition exists
        if (window.getComputedStyle(_this2.element)['transition-duration'] !== '0s') {
          // Listen for the transition to end
          _this2.element.addEventListener('transitionend', function () {
            return _this2.onTransitionEnd();
          }, {
            once: true
          });

          window.requestAnimationFrame(function () {
            _this2.element.classList.remove(_this2.eventClasses('in')[0]);

            _this2.element.classList.add(_this2.eventClasses('out')[0]);
          });
        } else {
          _this2.resetClasses();

          if (_this2.callback) {
            _this2.callback.apply(_this2.element);
          }

          _this2.destructor();
        }
      });
    }
    /**
     * Callback function when the transition ends.
     *
     * When a transition ends, the instance of the transition is automatically destructed.
     *
     * @returns {void}
     */

  }, {
    key: "onTransitionEnd",
    value: function onTransitionEnd() {
      var _this3 = this;

      this.eventClasses('active', !this.trailTo ? 'out' : '').forEach(function (eventClass) {
        _this3.element.classList.remove(eventClass);
      });

      if (this.callback) {
        this.callback.apply(this.element);
      } // Remove duration override


      if (this.duration !== null) {
        this.element.style.transitionDuration = null;
      }

      this.destructor();
    }
    /**
     * Cancels a transition.
     *
     * @returns {void}
     */

  }, {
    key: "cancel",
    value: function cancel() {
      var _this4 = this;

      this.element.removeEventListener('transitionend', function () {
        return _this4.onTransitionEnd;
      }, {
        once: true
      });
      this.resetClasses(); // Remove duration override

      if (this.duration !== null) {
        this.element.style.transitionDuration = null;
      }

      this.destructor();
    }
    /**
     * Resets the classes, removing any transition classes.
     *
     * @returns {void}
     */

  }, {
    key: "resetClasses",
    value: function resetClasses() {
      var _this5 = this;

      this.eventClasses().forEach(function (eventClass) {
        _this5.element.classList.remove(eventClass);
      });
    }
    /**
     * Parses a given duration and converts it to a "ms" value.
     *
     * @param {String} duration
     * @returns {String}
     */

  }, {
    key: "parseDuration",
    value: function parseDuration(duration) {
      var parsed = /^([0-9]+(\.[0-9]+)?)(m?s)?$/.exec(duration);
      var amount = Number(parsed[1]);
      var unit = parsed[3] === 's' ? 'sec' : 'msec';
      return unit === 'sec' ? "".concat(amount * 1000, "ms") : "".concat(Math.floor(amount), "ms");
    }
  }]);

  return Transition;
}(Snowboard.PluginBase);



/***/ }),

/***/ "./assets/js/snowboard/snowboard.extras.js":
/*!*************************************************!*\
  !*** ./assets/js/snowboard/snowboard.extras.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _extras_Flash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./extras/Flash */ "./assets/js/snowboard/extras/Flash.js");
/* harmony import */ var _extras_FlashListener__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./extras/FlashListener */ "./assets/js/snowboard/extras/FlashListener.js");
/* harmony import */ var _extras_Transition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./extras/Transition */ "./assets/js/snowboard/extras/Transition.js");
/* harmony import */ var _extras_AttachLoading__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./extras/AttachLoading */ "./assets/js/snowboard/extras/AttachLoading.js");
/* harmony import */ var _extras_StripeLoader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./extras/StripeLoader */ "./assets/js/snowboard/extras/StripeLoader.js");
/* harmony import */ var _extras_StylesheetLoader__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./extras/StylesheetLoader */ "./assets/js/snowboard/extras/StylesheetLoader.js");







(function (Snowboard) {
  Snowboard.addPlugin('extrasStyles', _extras_StylesheetLoader__WEBPACK_IMPORTED_MODULE_5__["default"]);
  Snowboard.addPlugin('transition', _extras_Transition__WEBPACK_IMPORTED_MODULE_2__["default"]);
  Snowboard.addPlugin('flash', _extras_Flash__WEBPACK_IMPORTED_MODULE_0__["default"]);
  Snowboard.addPlugin('flashListener', _extras_FlashListener__WEBPACK_IMPORTED_MODULE_1__["default"]);
  Snowboard.addPlugin('attachLoading', _extras_AttachLoading__WEBPACK_IMPORTED_MODULE_3__["default"]);
  Snowboard.addPlugin('stripeLoader', _extras_StripeLoader__WEBPACK_IMPORTED_MODULE_4__["default"]);
})(window.Snowboard);

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/js/snowboard/snowboard.extras.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYnVpbGQvc25vd2JvYXJkLmV4dHJhcy5qcyIsIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtJQUNxQkE7Ozs7Ozs7Ozs7Ozs7O0FBQ2pCO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDSSw0QkFBZTtBQUNYLGFBQU8sQ0FBQyxTQUFELENBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSxtQkFBVTtBQUNOLGFBQU87QUFDSEMsUUFBQUEsU0FBUyxFQUFFLFdBRFI7QUFFSEMsUUFBQUEsUUFBUSxFQUFFO0FBRlAsT0FBUDtBQUlIOzs7V0FFRCxtQkFBVUMsT0FBVixFQUFtQkMsT0FBbkIsRUFBNEI7QUFBQTs7QUFDeEIsVUFBSSxDQUFDQSxPQUFPLENBQUNDLE9BQWIsRUFBc0I7QUFDbEI7QUFDSDs7QUFFRCxVQUFJRCxPQUFPLENBQUNDLE9BQVIsQ0FBZ0JDLE9BQWhCLEtBQTRCLE1BQWhDLEVBQXdDO0FBQ3BDLFlBQU1DLFlBQVksR0FBR0gsT0FBTyxDQUFDQyxPQUFSLENBQWdCRyxnQkFBaEIsQ0FBaUMsdUJBQWpDLENBQXJCOztBQUNBLFlBQUlELFlBQVksQ0FBQ0UsTUFBYixHQUFzQixDQUExQixFQUE2QjtBQUN6QkYsVUFBQUEsWUFBWSxDQUFDRyxPQUFiLENBQXFCLFVBQUNMLE9BQUQsRUFBYTtBQUM5QkEsWUFBQUEsT0FBTyxDQUFDTSxTQUFSLENBQWtCQyxHQUFsQixDQUFzQixLQUFJLENBQUNDLGVBQUwsQ0FBcUJSLE9BQXJCLENBQXRCO0FBQ0gsV0FGRDtBQUdIO0FBQ0osT0FQRCxNQU9PLElBQUlELE9BQU8sQ0FBQ0MsT0FBUixDQUFnQlMsT0FBaEIsQ0FBd0JDLGFBQXhCLEtBQTBDQyxTQUE5QyxFQUF5RDtBQUM1RFosUUFBQUEsT0FBTyxDQUFDQyxPQUFSLENBQWdCTSxTQUFoQixDQUEwQkMsR0FB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LGNBQXBCLEVBQW9DMUMsZ0VBQXBDO0FBQ0FuRCxFQUFBQSxTQUFTLENBQUM2RixTQUFWLENBQW9CLFlBQXBCLEVBQWtDbEMsMERBQWxDO0FBQ0EzRCxFQUFBQSxTQUFTLENBQUM2RixTQUFWLENBQW9CLE9BQXBCLEVBQTZCM0YscURBQTdCO0FBQ0FGLEVBQUFBLFNBQVMsQ0FBQzZGLFNBQVYsQ0FBb0IsZUFBcEIsRUFBcUNoRSw2REFBckM7QUFDQTdCLEVBQUFBLFNBQVMsQ0FBQzZGLFNBQVYsQ0FBb0IsZUFBcEIsRUFBcUNoSCw2REFBckM7QUFDQW1CLEVBQUFBLFNBQVMsQ0FBQzZGLFNBQVYsQ0FBb0IsY0FBcEIsRUFBb0NyRCw0REFBcEM7QUFDSCxDQVBELEVBT0c3QixNQUFNLENBQUNYLFNBUFYiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvZXh0cmFzL0F0dGFjaExvYWRpbmcuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL2V4dHJhcy9GbGFzaC5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvZXh0cmFzL0ZsYXNoTGlzdGVuZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL2V4dHJhcy9TdHJpcGVMb2FkZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL2V4dHJhcy9TdHlsZXNoZWV0TG9hZGVyLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9leHRyYXMvVHJhbnNpdGlvbi5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvc25vd2JvYXJkLmV4dHJhcy5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIEFsbG93cyBhdHRhY2hpbmcgYSBsb2FkaW5nIGNsYXNzIG9uIGVsZW1lbnRzIHRoYXQgYW4gQUpBWCByZXF1ZXN0IGlzIHRhcmdldGluZy5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQXR0YWNoTG9hZGluZyBleHRlbmRzIFNub3dib2FyZC5TaW5nbGV0b24ge1xuICAgIC8qKlxuICAgICAqIERlZmluZXMgZGVwZW5kZW5pY2VzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3N0cmluZ1tdfVxuICAgICAqL1xuICAgIGRlcGVuZGVuY2llcygpIHtcbiAgICAgICAgcmV0dXJuIFsncmVxdWVzdCddO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgbGlzdGVuZXJzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgYWpheFN0YXJ0OiAnYWpheFN0YXJ0JyxcbiAgICAgICAgICAgIGFqYXhEb25lOiAnYWpheERvbmUnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIGFqYXhTdGFydChwcm9taXNlLCByZXF1ZXN0KSB7XG4gICAgICAgIGlmICghcmVxdWVzdC5lbGVtZW50KSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocmVxdWVzdC5lbGVtZW50LnRhZ05hbWUgPT09ICdGT1JNJykge1xuICAgICAgICAgICAgY29uc3QgbG9hZEVsZW1lbnRzID0gcmVxdWVzdC5lbGVtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tkYXRhLWF0dGFjaC1sb2FkaW5nXScpO1xuICAgICAgICAgICAgaWYgKGxvYWRFbGVtZW50cy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgbG9hZEVsZW1lbnRzLmZvckVhY2goKGVsZW1lbnQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuZ2V0TG9hZGluZ0NsYXNzKGVsZW1lbnQpKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIGlmIChyZXF1ZXN0LmVsZW1lbnQuZGF0YXNldC5hdHRhY2hMb2FkaW5nICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHJlcXVlc3QuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuZ2V0TG9hZGluZ0NsYXNzKHJlcXVlc3QuZWxlbWVudCkpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgYWpheERvbmUoZGF0YSwgcmVxdWVzdCkge1xuICAgICAgICBpZiAoIXJlcXVlc3QuZWxlbWVudCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHJlcXVlc3QuZWxlbWVudC50YWdOYW1lID09PSAnRk9STScpIHtcbiAgICAgICAgICAgIGNvbnN0IGxvYWRFbGVtZW50cyA9IHJlcXVlc3QuZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKCdbZGF0YS1hdHRhY2gtbG9hZGluZ10nKTtcbiAgICAgICAgICAgIGlmIChsb2FkRWxlbWVudHMubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgIGxvYWRFbGVtZW50cy5mb3JFYWNoKChlbGVtZW50KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSh0aGlzLmdldExvYWRpbmdDbGFzcyhlbGVtZW50KSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSBpZiAocmVxdWVzdC5lbGVtZW50LmRhdGFzZXQuYXR0YWNoTG9hZGluZyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICByZXF1ZXN0LmVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSh0aGlzLmdldExvYWRpbmdDbGFzcyhyZXF1ZXN0LmVsZW1lbnQpKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGdldExvYWRpbmdDbGFzcyhlbGVtZW50KSB7XG4gICAgICAgIHJldHVybiAoZWxlbWVudC5kYXRhc2V0LmF0dGFjaExvYWRpbmcgIT09IHVuZGVmaW5lZCAmJiBlbGVtZW50LmRhdGFzZXQuYXR0YWNoTG9hZGluZyAhPT0gJycpXG4gICAgICAgICAgICA/IGVsZW1lbnQuZGF0YXNldC5hdHRhY2hMb2FkaW5nXG4gICAgICAgICAgICA6ICd3bi1sb2FkaW5nJztcbiAgICB9XG59XG4iLCIvKipcbiAqIFByb3ZpZGVzIGZsYXNoIG1lc3NhZ2VzIGZvciB0aGUgQ01TLlxuICpcbiAqIEZsYXNoIG1lc3NhZ2VzIHdpbGwgcG9wIHVwIGF0IHRoZSB0b3AgY2VudGVyIG9mIHRoZSBwYWdlIGFuZCB3aWxsIHJlbWFpbiBmb3IgNyBzZWNvbmRzIGJ5IGRlZmF1bHQuIEhvdmVyaW5nIG92ZXJcbiAqIHRoZSBtZXNzYWdlIHdpbGwgcmVzZXQgYW5kIHBhdXNlIHRoZSB0aW1lci4gQ2xpY2tpbmcgb24gdGhlIGZsYXNoIG1lc3NhZ2Ugd2lsbCBkaXNtaXNzIGl0LlxuICpcbiAqIEFyZ3VtZW50czpcbiAqICAtIFwibWVzc2FnZVwiOiBUaGUgY29udGVudCBvZiB0aGUgZmxhc2ggbWVzc2FnZS4gSFRNTCBpcyBhY2NlcHRlZC5cbiAqICAtIFwidHlwZVwiOiBUaGUgdHlwZSBvZiBmbGFzaCBtZXNzYWdlLiBUaGlzIGlzIGFwcGVuZGVkIGFzIGEgY2xhc3MgdG8gdGhlIGZsYXNoIG1lc3NhZ2UgaXRzZWxmLlxuICogIC0gXCJkdXJhdGlvblwiOiBIb3cgbG9uZyB0aGUgZmxhc2ggbWVzc2FnZSB3aWxsIHN0YXkgdmlzaWJsZSBmb3IsIGluIHNlY29uZHMuIERlZmF1bHQ6IDcgc2Vjb25kcy5cbiAqXG4gKiBVc2FnZTpcbiAqICAgICAgU25vd2JvYXJkLmZsYXNoKCdUaGlzIGlzIGEgZmxhc2ggbWVzc2FnZScsICdpbmZvJywgOCk7XG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZsYXNoIGV4dGVuZHMgU25vd2JvYXJkLlBsdWdpbkJhc2Uge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmR9IHNub3dib2FyZFxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IHR5cGVcbiAgICAgKiBAcGFyYW0ge051bWJlcn0gZHVyYXRpb25cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQsIG1lc3NhZ2UsIHR5cGUsIGR1cmF0aW9uKSB7XG4gICAgICAgIHN1cGVyKHNub3dib2FyZCk7XG5cbiAgICAgICAgdGhpcy5tZXNzYWdlID0gbWVzc2FnZTtcbiAgICAgICAgdGhpcy50eXBlID0gdHlwZSB8fCAnZGVmYXVsdCc7XG4gICAgICAgIHRoaXMuZHVyYXRpb24gPSBkdXJhdGlvbiB8fCA3O1xuXG4gICAgICAgIHRoaXMuY2xlYXIoKTtcbiAgICAgICAgdGhpcy50aW1lciA9IG51bGw7XG4gICAgICAgIHRoaXMuZmxhc2hUaW1lciA9IG51bGw7XG4gICAgICAgIHRoaXMuY3JlYXRlKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBkZXBlbmRlbmNpZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWyd0cmFuc2l0aW9uJ107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVzdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIFRoaXMgd2lsbCBlbnN1cmUgdGhlIGZsYXNoIG1lc3NhZ2UgaXMgcmVtb3ZlZCBhbmQgdGltZW91dCBpcyBjbGVhcmVkIGlmIHRoZSBtb2R1bGUgaXMgcmVtb3ZlZC5cbiAgICAgKi9cbiAgICBkZXN0cnVjdG9yKCkge1xuICAgICAgICBpZiAodGhpcy50aW1lciAhPT0gbnVsbCkge1xuICAgICAgICAgICAgd2luZG93LmNsZWFyVGltZW91dCh0aGlzLnRpbWVyKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLmZsYXNoKSB7XG4gICAgICAgICAgICB0aGlzLmZsYXNoVGltZXIucmVtb3ZlKCk7XG4gICAgICAgICAgICB0aGlzLmZsYXNoLnJlbW92ZSgpO1xuICAgICAgICAgICAgdGhpcy5mbGFzaCA9IG51bGw7XG4gICAgICAgICAgICB0aGlzLmZsYXNoVGltZXIgPSBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgc3VwZXIuZGVzdHJ1Y3RvcigpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENyZWF0ZXMgdGhlIGZsYXNoIG1lc3NhZ2UuXG4gICAgICovXG4gICAgY3JlYXRlKCkge1xuICAgICAgICB0aGlzLmZsYXNoID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgIHRoaXMuZmxhc2hUaW1lciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0RJVicpO1xuICAgICAgICB0aGlzLmZsYXNoLmlubmVySFRNTCA9IHRoaXMubWVzc2FnZTtcbiAgICAgICAgdGhpcy5mbGFzaC5jbGFzc0xpc3QuYWRkKCdmbGFzaC1tZXNzYWdlJywgdGhpcy50eXBlKTtcbiAgICAgICAgdGhpcy5mbGFzaFRpbWVyLmNsYXNzTGlzdC5hZGQoJ2ZsYXNoLXRpbWVyJyk7XG4gICAgICAgIHRoaXMuZmxhc2gucmVtb3ZlQXR0cmlidXRlKCdkYXRhLWNvbnRyb2wnKTtcbiAgICAgICAgdGhpcy5mbGFzaC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsICgpID0+IHRoaXMucmVtb3ZlKCkpO1xuICAgICAgICB0aGlzLmZsYXNoLmFkZEV2ZW50TGlzdGVuZXIoJ21vdXNlb3ZlcicsICgpID0+IHRoaXMuc3RvcFRpbWVyKCkpO1xuICAgICAgICB0aGlzLmZsYXNoLmFkZEV2ZW50TGlzdGVuZXIoJ21vdXNlb3V0JywgKCkgPT4gdGhpcy5zdGFydFRpbWVyKCkpO1xuXG4gICAgICAgIC8vIEFkZCB0byBib2R5XG4gICAgICAgIHRoaXMuZmxhc2guYXBwZW5kQ2hpbGQodGhpcy5mbGFzaFRpbWVyKTtcbiAgICAgICAgZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZCh0aGlzLmZsYXNoKTtcblxuICAgICAgICB0aGlzLnNub3dib2FyZC50cmFuc2l0aW9uKHRoaXMuZmxhc2gsICdzaG93JywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5zdGFydFRpbWVyKCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbW92ZXMgdGhlIGZsYXNoIG1lc3NhZ2UuXG4gICAgICovXG4gICAgcmVtb3ZlKCkge1xuICAgICAgICB0aGlzLnN0b3BUaW1lcigpO1xuXG4gICAgICAgIHRoaXMuc25vd2JvYXJkLnRyYW5zaXRpb24odGhpcy5mbGFzaCwgJ2hpZGUnLCAoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmZsYXNoLnJlbW92ZSgpO1xuICAgICAgICAgICAgdGhpcy5mbGFzaCA9IG51bGw7XG4gICAgICAgICAgICB0aGlzLmRlc3RydWN0b3IoKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ2xlYXJzIGFsbCBmbGFzaCBtZXNzYWdlcyBhdmFpbGFibGUgb24gdGhlIHBhZ2UuXG4gICAgICovXG4gICAgY2xlYXIoKSB7XG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ2JvZHkgPiBkaXYuZmxhc2gtbWVzc2FnZScpLmZvckVhY2goKGVsZW1lbnQpID0+IGVsZW1lbnQucmVtb3ZlKCkpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFN0YXJ0cyB0aGUgdGltZXIgZm9yIHRoaXMgZmxhc2ggbWVzc2FnZS5cbiAgICAgKi9cbiAgICBzdGFydFRpbWVyKCkge1xuICAgICAgICB0aGlzLnRpbWVyVHJhbnMgPSB0aGlzLnNub3dib2FyZC50cmFuc2l0aW9uKHRoaXMuZmxhc2hUaW1lciwgJ3RpbWVvdXQnLCBudWxsLCBgJHt0aGlzLmR1cmF0aW9ufS4wc2AsIHRydWUpO1xuICAgICAgICB0aGlzLnRpbWVyID0gd2luZG93LnNldFRpbWVvdXQoKCkgPT4gdGhpcy5yZW1vdmUoKSwgdGhpcy5kdXJhdGlvbiAqIDEwMDApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlc2V0cyB0aGUgdGltZXIgZm9yIHRoaXMgZmxhc2ggbWVzc2FnZS5cbiAgICAgKi9cbiAgICBzdG9wVGltZXIoKSB7XG4gICAgICAgIGlmICh0aGlzLnRpbWVyVHJhbnMpIHtcbiAgICAgICAgICAgIHRoaXMudGltZXJUcmFucy5jYW5jZWwoKTtcbiAgICAgICAgfVxuICAgICAgICB3aW5kb3cuY2xlYXJUaW1lb3V0KHRoaXMudGltZXIpO1xuICAgIH1cbn1cbiIsIi8qKlxuICogRGVmaW5lcyBhIGRlZmF1bHQgbGlzdGVuZXIgZm9yIGZsYXNoIGV2ZW50cy5cbiAqXG4gKiBDb25uZWN0cyB0aGUgRmxhc2ggcGx1Z2luIHRvIHZhcmlvdXMgZXZlbnRzIHRoYXQgdXNlIGZsYXNoIG1lc3NhZ2VzLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGbGFzaExpc3RlbmVyIGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBkZXBlbmRlbmljZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWydmbGFzaCddO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgbGlzdGVuZXJzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgcmVhZHk6ICdyZWFkeScsXG4gICAgICAgICAgICBhamF4RXJyb3JNZXNzYWdlOiAnYWpheEVycm9yTWVzc2FnZScsXG4gICAgICAgICAgICBhamF4Rmxhc2hNZXNzYWdlczogJ2FqYXhGbGFzaE1lc3NhZ2VzJyxcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEbyBmbGFzaCBtZXNzYWdlcyBmb3IgUEhQIGZsYXNoIHJlc3BvbnNlcy5cbiAgICAgKi9cbiAgICByZWFkeSgpIHtcbiAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW2RhdGEtY29udHJvbD1cImZsYXNoLW1lc3NhZ2VcIl0nKS5mb3JFYWNoKChlbGVtZW50KSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNub3dib2FyZC5mbGFzaChcbiAgICAgICAgICAgICAgICBlbGVtZW50LmlubmVySFRNTCxcbiAgICAgICAgICAgICAgICBlbGVtZW50LmRhdGFzZXQuZmxhc2hUeXBlLFxuICAgICAgICAgICAgICAgIGVsZW1lbnQuZGF0YXNldC5mbGFzaER1cmF0aW9uLFxuICAgICAgICAgICAgKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogU2hvd3MgYSBmbGFzaCBtZXNzYWdlIGZvciBBSkFYIGVycm9ycy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlXG4gICAgICogQHJldHVybnMge0Jvb2xlYW59XG4gICAgICovXG4gICAgYWpheEVycm9yTWVzc2FnZShtZXNzYWdlKSB7XG4gICAgICAgIHRoaXMuc25vd2JvYXJkLmZsYXNoKG1lc3NhZ2UsICdlcnJvcicpO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogU2hvd3MgZmxhc2ggbWVzc2FnZXMgcmV0dXJuZWQgZGlyZWN0bHkgZnJvbSBBSkFYIGZ1bmN0aW9uYWxpdHkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gbWVzc2FnZXNcbiAgICAgKi9cbiAgICBhamF4Rmxhc2hNZXNzYWdlcyhtZXNzYWdlcykge1xuICAgICAgICBPYmplY3QuZW50cmllcyhtZXNzYWdlcykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IFtjc3NDbGFzcywgbWVzc2FnZV0gPSBlbnRyeTtcbiAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLmZsYXNoKG1lc3NhZ2UsIGNzc0NsYXNzKTtcbiAgICAgICAgfSk7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG59XG4iLCIvKipcbiAqIERpc3BsYXlzIGEgc3RyaXBlIGF0IHRoZSB0b3Agb2YgdGhlIHBhZ2UgdGhhdCBpbmRpY2F0ZXMgbG9hZGluZy5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3RyaXBlTG9hZGVyIGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBkZXBlbmRlbmljZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWydyZXF1ZXN0J107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBsaXN0ZW5lcnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgICAqL1xuICAgIGxpc3RlbnMoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICByZWFkeTogJ3JlYWR5JyxcbiAgICAgICAgICAgIGFqYXhTdGFydDogJ2FqYXhTdGFydCcsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgcmVhZHkoKSB7XG4gICAgICAgIHRoaXMuY291bnRlciA9IDA7XG5cbiAgICAgICAgdGhpcy5jcmVhdGVTdHJpcGUoKTtcbiAgICB9XG5cbiAgICBhamF4U3RhcnQocHJvbWlzZSkge1xuICAgICAgICB0aGlzLnNob3coKTtcblxuICAgICAgICBwcm9taXNlLmNhdGNoKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICB9KS5maW5hbGx5KCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBjcmVhdGVTdHJpcGUoKSB7XG4gICAgICAgIHRoaXMuaW5kaWNhdG9yID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgIHRoaXMuc3RyaXBlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgIHRoaXMuc3RyaXBlTG9hZGVkID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG5cbiAgICAgICAgdGhpcy5pbmRpY2F0b3IuY2xhc3NMaXN0LmFkZCgnc3RyaXBlLWxvYWRpbmctaW5kaWNhdG9yJywgJ2xvYWRlZCcpO1xuICAgICAgICB0aGlzLnN0cmlwZS5jbGFzc0xpc3QuYWRkKCdzdHJpcGUnKTtcbiAgICAgICAgdGhpcy5zdHJpcGVMb2FkZWQuY2xhc3NMaXN0LmFkZCgnc3RyaXBlLWxvYWRlZCcpO1xuXG4gICAgICAgIHRoaXMuaW5kaWNhdG9yLmFwcGVuZENoaWxkKHRoaXMuc3RyaXBlKTtcbiAgICAgICAgdGhpcy5pbmRpY2F0b3IuYXBwZW5kQ2hpbGQodGhpcy5zdHJpcGVMb2FkZWQpO1xuXG4gICAgICAgIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGhpcy5pbmRpY2F0b3IpO1xuICAgIH1cblxuICAgIHNob3coKSB7XG4gICAgICAgIHRoaXMuY291bnRlciArPSAxO1xuXG4gICAgICAgIGNvbnN0IG5ld1N0cmlwZSA9IHRoaXMuc3RyaXBlLmNsb25lTm9kZSh0cnVlKTtcbiAgICAgICAgdGhpcy5pbmRpY2F0b3IuYXBwZW5kQ2hpbGQobmV3U3RyaXBlKTtcbiAgICAgICAgdGhpcy5zdHJpcGUucmVtb3ZlKCk7XG4gICAgICAgIHRoaXMuc3RyaXBlID0gbmV3U3RyaXBlO1xuXG4gICAgICAgIGlmICh0aGlzLmNvdW50ZXIgPiAxKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmluZGljYXRvci5jbGFzc0xpc3QucmVtb3ZlKCdsb2FkZWQnKTtcbiAgICAgICAgZG9jdW1lbnQuYm9keS5jbGFzc0xpc3QuYWRkKCd3bi1sb2FkaW5nJyk7XG4gICAgfVxuXG4gICAgaGlkZShmb3JjZSkge1xuICAgICAgICB0aGlzLmNvdW50ZXIgLT0gMTtcblxuICAgICAgICBpZiAoZm9yY2UgPT09IHRydWUpIHtcbiAgICAgICAgICAgIHRoaXMuY291bnRlciA9IDA7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpcy5jb3VudGVyIDw9IDApIHtcbiAgICAgICAgICAgIHRoaXMuaW5kaWNhdG9yLmNsYXNzTGlzdC5hZGQoJ2xvYWRlZCcpO1xuICAgICAgICAgICAgZG9jdW1lbnQuYm9keS5jbGFzc0xpc3QucmVtb3ZlKCd3bi1sb2FkaW5nJyk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCIvKipcbiAqIEVtYmVkcyB0aGUgXCJleHRyYXNcIiBzdHlsZXNoZWV0IGludG8gdGhlIHBhZ2UsIGlmIGl0IGlzIG5vdCBsb2FkZWQgdGhyb3VnaCB0aGUgdGhlbWUuXG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN0eWxlc2hlZXRMb2FkZXIgZXh0ZW5kcyBTbm93Ym9hcmQuU2luZ2xldG9uIHtcbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIGxpc3RlbmVycy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIHJlYWR5KCkge1xuICAgICAgICBsZXQgc3R5bGVzTG9hZGVkID0gZmFsc2U7XG5cbiAgICAgICAgLy8gRGV0ZXJtaW5lIGlmIHN0eWxlc2hlZXQgaXMgYWxyZWFkeSBsb2FkZWRcbiAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnbGlua1tyZWw9XCJzdHlsZXNoZWV0XCJdJykuZm9yRWFjaCgoY3NzKSA9PiB7XG4gICAgICAgICAgICBpZiAoY3NzLmhyZWYuZW5kc1dpdGgoJy9tb2R1bGVzL3N5c3RlbS9hc3NldHMvY3NzL3Nub3dib2FyZC5leHRyYXMuY3NzJykpIHtcbiAgICAgICAgICAgICAgICBzdHlsZXNMb2FkZWQgPSB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICBpZiAoIXN0eWxlc0xvYWRlZCkge1xuICAgICAgICAgICAgY29uc3Qgc3R5bGVzaGVldCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2xpbmsnKTtcbiAgICAgICAgICAgIHN0eWxlc2hlZXQuc2V0QXR0cmlidXRlKCdyZWwnLCAnc3R5bGVzaGVldCcpO1xuICAgICAgICAgICAgc3R5bGVzaGVldC5zZXRBdHRyaWJ1dGUoJ2hyZWYnLCAnL21vZHVsZXMvc3lzdGVtL2Fzc2V0cy9jc3Mvc25vd2JvYXJkLmV4dHJhcy5jc3MnKTtcbiAgICAgICAgICAgIGRvY3VtZW50LmhlYWQuYXBwZW5kQ2hpbGQoc3R5bGVzaGVldCk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCIvKipcbiAqIFByb3ZpZGVzIHRyYW5zaXRpb24gc3VwcG9ydCBmb3IgZWxlbWVudHMuXG4gKlxuICogVHJhbnNpdGlvbiBhbGxvd3MgQ1NTIHRyYW5zaXRpb25zIHRvIGJlIGNvbnRyb2xsZWQgYW5kIGNhbGxiYWNrcyB0byBiZSBydW4gb25jZSBjb21wbGV0ZWQuIEl0IHdvcmtzIHNpbWlsYXIgdG8gVnVlXG4gKiB0cmFuc2l0aW9ucyB3aXRoIDMgc3RhZ2VzIG9mIHRyYW5zaXRpb24sIGFuZCBjbGFzc2VzIGFzc2lnbmVkIHRvIHRoZSBlbGVtZW50IHdpdGggdGhlIHRyYW5zaXRpb24gbmFtZSBzdWZmaXhlZCB3aXRoXG4gKiB0aGUgc3RhZ2Ugb2YgdHJhbnNpdGlvbjpcbiAqXG4gKiAgLSBgaW5gOiBBIGNsYXNzIGFzc2lnbmVkIHRvIHRoZSBlbGVtZW50IGZvciB0aGUgZmlyc3QgZnJhbWUgb2YgdGhlIHRyYW5zaXRpb24sIHJlbW92ZWQgYWZ0ZXJ3YXJkcy4gVGhpcyBzaG91bGQgYmVcbiAqICAgICAgdXNlZCB0byBkZWZpbmUgdGhlIGluaXRpYWwgc3RhdGUgb2YgdGhlIHRyYW5zaXRpb24uXG4gKiAgLSBgYWN0aXZlYDogQSBjbGFzcyBhc3NpZ25lZCB0byB0aGUgZWxlbWVudCBmb3IgdGhlIGR1cmF0aW9uIG9mIHRoZSB0cmFuc2l0aW9uLiBUaGlzIHNob3VsZCBiZSB1c2VkIHRvIGRlZmluZSB0aGVcbiAqICAgICAgdHJhbnNpdGlvbiBpdHNlbGYuXG4gKiAgLSBgb3V0YDogQSBjbGFzcyBhc3NpZ25lZCB0byB0aGUgZWxlbWVudCBhZnRlciB0aGUgZmlyc3QgZnJhbWUgb2YgdGhlIHRyYW5zaXRpb24gYW5kIGtlcHQgdG8gdGhlIGVuZCBvZiB0aGVcbiAqICAgICAgdHJhbnNpdGlvbi4gVGhpcyBzaG91bGQgZGVmaW5lIHRoZSBlbmQgc3RhdGUgb2YgdGhlIHRyYW5zaXRpb24uXG4gKlxuICogVXNhZ2U6XG4gKiAgICAgIFNub3dib2FyZC50cmFuc2l0aW9uKGRvY3VtZW50LmVsZW1lbnQsICd0cmFuc2l0aW9uJywgKCkgPT4ge1xuICogICAgICAgICAgY29uc29sZS5sb2coJ1JlbW92ZSBlbGVtZW50IGFmdGVyIDcgc2Vjb25kcycpO1xuICogICAgICAgICAgdGhpcy5yZW1vdmUoKTtcbiAqICAgICAgfSwgJzdzJyk7XG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRyYW5zaXRpb24gZXh0ZW5kcyBTbm93Ym9hcmQuUGx1Z2luQmFzZSB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1Nub3dib2FyZH0gc25vd2JvYXJkXG4gICAgICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZWxlbWVudCBUaGUgZWxlbWVudCB0byB0cmFuc2l0aW9uXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IHRyYW5zaXRpb24gVGhlIG5hbWUgb2YgdGhlIHRyYW5zaXRpb24sIHRoaXMgcHJlZml4ZXMgdGhlIHN0YWdlcyBvZiB0cmFuc2l0aW9uLlxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrIEFuIG9wdGlvbmFsIGNhbGxiYWNrIHRvIGNhbGwgd2hlbiB0aGUgdHJhbnNpdGlvbiBlbmRzLlxuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBkdXJhdGlvbiBBbiBvcHRpb25hbCBvdmVycmlkZSBvbiB0aGUgdHJhbnNpdGlvbiBkdXJhdGlvbi4gTXVzdCBiZSBzcGVjaWZpZWQgYXMgJ3MnIChzZWNzKSBvciAnbXMnIChtc2VjcykuXG4gICAgICogQHBhcmFtIHtCb29sZWFufSB0cmFpbFRvIElmIHRydWUsIHRoZSBcIm91dFwiIGNsYXNzIHdpbGwgcmVtYWluIGFmdGVyIHRoZSBlbmQgb2YgdGhlIHRyYW5zaXRpb24uXG4gICAgICovXG4gICAgY29uc3RydWN0b3Ioc25vd2JvYXJkLCBlbGVtZW50LCB0cmFuc2l0aW9uLCBjYWxsYmFjaywgZHVyYXRpb24sIHRyYWlsVG8pIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICBpZiAoZWxlbWVudCBpbnN0YW5jZW9mIEhUTUxFbGVtZW50ID09PSBmYWxzZSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdBIEhUTUxFbGVtZW50IG11c3QgYmUgcHJvdmlkZWQgZm9yIHRyYW5zaXRpb25pbmcnKTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmVsZW1lbnQgPSBlbGVtZW50O1xuXG4gICAgICAgIGlmICh0eXBlb2YgdHJhbnNpdGlvbiAhPT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVHJhbnNpdGlvbiBuYW1lIG11c3QgYmUgc3BlY2lmaWVkIGFzIGEgc3RyaW5nJyk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy50cmFuc2l0aW9uID0gdHJhbnNpdGlvbjtcblxuICAgICAgICBpZiAoY2FsbGJhY2sgJiYgdHlwZW9mIGNhbGxiYWNrICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0NhbGxiYWNrIG11c3QgYmUgYSB2YWxpZCBmdW5jdGlvbicpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuY2FsbGJhY2sgPSBjYWxsYmFjaztcblxuICAgICAgICBpZiAoZHVyYXRpb24pIHtcbiAgICAgICAgICAgIHRoaXMuZHVyYXRpb24gPSB0aGlzLnBhcnNlRHVyYXRpb24oZHVyYXRpb24pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5kdXJhdGlvbiA9IG51bGw7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnRyYWlsVG8gPSAodHJhaWxUbyA9PT0gdHJ1ZSk7XG5cbiAgICAgICAgdGhpcy5kb1RyYW5zaXRpb24oKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYXBzIGV2ZW50IGNsYXNzZXMgdG8gdGhlIGdpdmVuIHRyYW5zaXRpb24gc3RhdGUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0gIHsuLi5hbnl9IGFyZ3NcbiAgICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAgICovXG4gICAgZXZlbnRDbGFzc2VzKC4uLmFyZ3MpIHtcbiAgICAgICAgY29uc3QgZXZlbnRDbGFzc2VzID0ge1xuICAgICAgICAgICAgaW46IGAke3RoaXMudHJhbnNpdGlvbn0taW5gLFxuICAgICAgICAgICAgYWN0aXZlOiBgJHt0aGlzLnRyYW5zaXRpb259LWFjdGl2ZWAsXG4gICAgICAgICAgICBvdXQ6IGAke3RoaXMudHJhbnNpdGlvbn0tb3V0YCxcbiAgICAgICAgfTtcblxuICAgICAgICBpZiAoYXJncy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHJldHVybiBPYmplY3QudmFsdWVzKGV2ZW50Q2xhc3Nlcyk7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCByZXR1cm5DbGFzc2VzID0gW107XG4gICAgICAgIE9iamVjdC5lbnRyaWVzKGV2ZW50Q2xhc3NlcykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IFtrZXksIHZhbHVlXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICBpZiAoYXJncy5pbmRleE9mKGtleSkgIT09IC0xKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuQ2xhc3Nlcy5wdXNoKHZhbHVlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHJldHVybkNsYXNzZXM7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRXhlY3V0ZXMgdGhlIHRyYW5zaXRpb24uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICBkb1RyYW5zaXRpb24oKSB7XG4gICAgICAgIC8vIEFkZCBkdXJhdGlvbiBvdmVycmlkZVxuICAgICAgICBpZiAodGhpcy5kdXJhdGlvbiAhPT0gbnVsbCkge1xuICAgICAgICAgICAgdGhpcy5lbGVtZW50LnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IHRoaXMuZHVyYXRpb247XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnJlc2V0Q2xhc3NlcygpO1xuXG4gICAgICAgIC8vIFN0YXJ0IHRyYW5zaXRpb24gLSBzaG93IFwiaW5cIiBhbmQgXCJhY3RpdmVcIiBjbGFzc2VzXG4gICAgICAgIHRoaXMuZXZlbnRDbGFzc2VzKCdpbicsICdhY3RpdmUnKS5mb3JFYWNoKChldmVudENsYXNzKSA9PiB7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuY2xhc3NMaXN0LmFkZChldmVudENsYXNzKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiB7XG4gICAgICAgICAgICAvLyBFbnN1cmUgYSB0cmFuc2l0aW9uIGV4aXN0c1xuICAgICAgICAgICAgaWYgKHdpbmRvdy5nZXRDb21wdXRlZFN0eWxlKHRoaXMuZWxlbWVudClbJ3RyYW5zaXRpb24tZHVyYXRpb24nXSAhPT0gJzBzJykge1xuICAgICAgICAgICAgICAgIC8vIExpc3RlbiBmb3IgdGhlIHRyYW5zaXRpb24gdG8gZW5kXG4gICAgICAgICAgICAgICAgdGhpcy5lbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ3RyYW5zaXRpb25lbmQnLCAoKSA9PiB0aGlzLm9uVHJhbnNpdGlvbkVuZCgpLCB7XG4gICAgICAgICAgICAgICAgICAgIG9uY2U6IHRydWUsXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKHRoaXMuZXZlbnRDbGFzc2VzKCdpbicpWzBdKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5lbGVtZW50LmNsYXNzTGlzdC5hZGQodGhpcy5ldmVudENsYXNzZXMoJ291dCcpWzBdKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5yZXNldENsYXNzZXMoKTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmNhbGxiYWNrKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY2FsbGJhY2suYXBwbHkodGhpcy5lbGVtZW50KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLmRlc3RydWN0b3IoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ2FsbGJhY2sgZnVuY3Rpb24gd2hlbiB0aGUgdHJhbnNpdGlvbiBlbmRzLlxuICAgICAqXG4gICAgICogV2hlbiBhIHRyYW5zaXRpb24gZW5kcywgdGhlIGluc3RhbmNlIG9mIHRoZSB0cmFuc2l0aW9uIGlzIGF1dG9tYXRpY2FsbHkgZGVzdHJ1Y3RlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHt2b2lkfVxuICAgICAqL1xuICAgIG9uVHJhbnNpdGlvbkVuZCgpIHtcbiAgICAgICAgdGhpcy5ldmVudENsYXNzZXMoJ2FjdGl2ZScsICghdGhpcy50cmFpbFRvKSA/ICdvdXQnIDogJycpLmZvckVhY2goKGV2ZW50Q2xhc3MpID0+IHtcbiAgICAgICAgICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKGV2ZW50Q2xhc3MpO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAodGhpcy5jYWxsYmFjaykge1xuICAgICAgICAgICAgdGhpcy5jYWxsYmFjay5hcHBseSh0aGlzLmVsZW1lbnQpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gUmVtb3ZlIGR1cmF0aW9uIG92ZXJyaWRlXG4gICAgICAgIGlmICh0aGlzLmR1cmF0aW9uICE9PSBudWxsKSB7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuc3R5bGUudHJhbnNpdGlvbkR1cmF0aW9uID0gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuZGVzdHJ1Y3RvcigpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENhbmNlbHMgYSB0cmFuc2l0aW9uLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgY2FuY2VsKCkge1xuICAgICAgICB0aGlzLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcigndHJhbnNpdGlvbmVuZCcsICgpID0+IHRoaXMub25UcmFuc2l0aW9uRW5kLCB7XG4gICAgICAgICAgICBvbmNlOiB0cnVlLFxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLnJlc2V0Q2xhc3NlcygpO1xuXG4gICAgICAgIC8vIFJlbW92ZSBkdXJhdGlvbiBvdmVycmlkZVxuICAgICAgICBpZiAodGhpcy5kdXJhdGlvbiAhPT0gbnVsbCkge1xuICAgICAgICAgICAgdGhpcy5lbGVtZW50LnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IG51bGw7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLmRlc3RydWN0b3IoKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXNldHMgdGhlIGNsYXNzZXMsIHJlbW92aW5nIGFueSB0cmFuc2l0aW9uIGNsYXNzZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICByZXNldENsYXNzZXMoKSB7XG4gICAgICAgIHRoaXMuZXZlbnRDbGFzc2VzKCkuZm9yRWFjaCgoZXZlbnRDbGFzcykgPT4ge1xuICAgICAgICAgICAgdGhpcy5lbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUoZXZlbnRDbGFzcyk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFBhcnNlcyBhIGdpdmVuIGR1cmF0aW9uIGFuZCBjb252ZXJ0cyBpdCB0byBhIFwibXNcIiB2YWx1ZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBkdXJhdGlvblxuICAgICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAgICovXG4gICAgcGFyc2VEdXJhdGlvbihkdXJhdGlvbikge1xuICAgICAgICBjb25zdCBwYXJzZWQgPSAvXihbMC05XSsoXFwuWzAtOV0rKT8pKG0/cyk/JC8uZXhlYyhkdXJhdGlvbik7XG4gICAgICAgIGNvbnN0IGFtb3VudCA9IE51bWJlcihwYXJzZWRbMV0pO1xuICAgICAgICBjb25zdCB1bml0ID0gKHBhcnNlZFszXSA9PT0gJ3MnKVxuICAgICAgICAgICAgPyAnc2VjJ1xuICAgICAgICAgICAgOiAnbXNlYyc7XG5cbiAgICAgICAgcmV0dXJuICh1bml0ID09PSAnc2VjJylcbiAgICAgICAgICAgID8gYCR7YW1vdW50ICogMTAwMH1tc2BcbiAgICAgICAgICAgIDogYCR7TWF0aC5mbG9vcihhbW91bnQpfW1zYDtcbiAgICB9XG59XG4iLCJpbXBvcnQgRmxhc2ggZnJvbSAnLi9leHRyYXMvRmxhc2gnO1xuaW1wb3J0IEZsYXNoTGlzdGVuZXIgZnJvbSAnLi9leHRyYXMvRmxhc2hMaXN0ZW5lcic7XG5pbXBvcnQgVHJhbnNpdGlvbiBmcm9tICcuL2V4dHJhcy9UcmFuc2l0aW9uJztcbmltcG9ydCBBdHRhY2hMb2FkaW5nIGZyb20gJy4vZXh0cmFzL0F0dGFjaExvYWRpbmcnO1xuaW1wb3J0IFN0cmlwZUxvYWRlciBmcm9tICcuL2V4dHJhcy9TdHJpcGVMb2FkZXInO1xuaW1wb3J0IFN0eWxlc2hlZXRMb2FkZXIgZnJvbSAnLi9leHRyYXMvU3R5bGVzaGVldExvYWRlcic7XG5cbigoU25vd2JvYXJkKSA9PiB7XG4gICAgU25vd2JvYXJkLmFkZFBsdWdpbignZXh0cmFzU3R5bGVzJywgU3R5bGVzaGVldExvYWRlcik7XG4gICAgU25vd2JvYXJkLmFkZFBsdWdpbigndHJhbnNpdGlvbicsIFRyYW5zaXRpb24pO1xuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2ZsYXNoJywgRmxhc2gpO1xuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2ZsYXNoTGlzdGVuZXInLCBGbGFzaExpc3RlbmVyKTtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdhdHRhY2hMb2FkaW5nJywgQXR0YWNoTG9hZGluZyk7XG4gICAgU25vd2JvYXJkLmFkZFBsdWdpbignc3RyaXBlTG9hZGVyJywgU3RyaXBlTG9hZGVyKTtcbn0pKHdpbmRvdy5Tbm93Ym9hcmQpO1xuIl0sIm5hbWVzIjpbIkF0dGFjaExvYWRpbmciLCJhamF4U3RhcnQiLCJhamF4RG9uZSIsInByb21pc2UiLCJyZXF1ZXN0IiwiZWxlbWVudCIsInRhZ05hbWUiLCJsb2FkRWxlbWVudHMiLCJxdWVyeVNlbGVjdG9yQWxsIiwibGVuZ3RoIiwiZm9yRWFjaCIsImNsYXNzTGlzdCIsImFkZCIsImdldExvYWRpbmdDbGFzcyIsImRhdGFzZXQiLCJhdHRhY2hMb2FkaW5nIiwidW5kZWZpbmVkIiwiZGF0YSIsInJlbW92ZSIsIlNub3dib2FyZCIsIlNpbmdsZXRvbiIsIkZsYXNoIiwic25vd2JvYXJkIiwibWVzc2FnZSIsInR5cGUiLCJkdXJhdGlvbiIsImNsZWFyIiwidGltZXIiLCJmbGFzaFRpbWVyIiwiY3JlYXRlIiwid2luZG93IiwiY2xlYXJUaW1lb3V0IiwiZmxhc2giLCJkb2N1bWVudCIsImNyZWF0ZUVsZW1lbnQiLCJpbm5lckhUTUwiLCJyZW1vdmVBdHRyaWJ1dGUiLCJhZGRFdmVudExpc3RlbmVyIiwic3RvcFRpbWVyIiwic3RhcnRUaW1lciIsImFwcGVuZENoaWxkIiwiYm9keSIsInRyYW5zaXRpb24iLCJkZXN0cnVjdG9yIiwidGltZXJUcmFucyIsInNldFRpbWVvdXQiLCJjYW5jZWwiLCJQbHVnaW5CYXNlIiwiRmxhc2hMaXN0ZW5lciIsInJlYWR5IiwiYWpheEVycm9yTWVzc2FnZSIsImFqYXhGbGFzaE1lc3NhZ2VzIiwiZmxhc2hUeXBlIiwiZmxhc2hEdXJhdGlvbiIsIm1lc3NhZ2VzIiwiT2JqZWN0IiwiZW50cmllcyIsImVudHJ5IiwiY3NzQ2xhc3MiLCJTdHJpcGVMb2FkZXIiLCJjb3VudGVyIiwiY3JlYXRlU3RyaXBlIiwic2hvdyIsImhpZGUiLCJpbmRpY2F0b3IiLCJzdHJpcGUiLCJzdHJpcGVMb2FkZWQiLCJuZXdTdHJpcGUiLCJjbG9uZU5vZGUiLCJmb3JjZSIsIlN0eWxlc2hlZXRMb2FkZXIiLCJzdHlsZXNMb2FkZWQiLCJjc3MiLCJocmVmIiwiZW5kc1dpdGgiLCJzdHlsZXNoZWV0Iiwic2V0QXR0cmlidXRlIiwiaGVhZCIsIlRyYW5zaXRpb24iLCJjYWxsYmFjayIsInRyYWlsVG8iLCJIVE1MRWxlbWVudCIsIkVycm9yIiwicGFyc2VEdXJhdGlvbiIsImRvVHJhbnNpdGlvbiIsImFyZ3MiLCJldmVudENsYXNzZXMiLCJhY3RpdmUiLCJvdXQiLCJ2YWx1ZXMiLCJyZXR1cm5DbGFzc2VzIiwia2V5IiwidmFsdWUiLCJpbmRleE9mIiwicHVzaCIsInN0eWxlIiwidHJhbnNpdGlvbkR1cmF0aW9uIiwicmVzZXRDbGFzc2VzIiwiZXZlbnRDbGFzcyIsInJlcXVlc3RBbmltYXRpb25GcmFtZSIsImdldENvbXB1dGVkU3R5bGUiLCJvblRyYW5zaXRpb25FbmQiLCJvbmNlIiwiYXBwbHkiLCJyZW1vdmVFdmVudExpc3RlbmVyIiwicGFyc2VkIiwiZXhlYyIsImFtb3VudCIsIk51bWJlciIsInVuaXQiLCJNYXRoIiwiZmxvb3IiLCJhZGRQbHVnaW4iXSwic291cmNlUm9vdCI6IiJ9