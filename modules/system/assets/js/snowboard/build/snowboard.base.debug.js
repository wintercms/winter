"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/js/snowboard/build/snowboard.base.debug"],{

/***/ "./assets/js/snowboard/abstracts/PluginBase.js":
/*!*****************************************************!*\
  !*** ./assets/js/snowboard/abstracts/PluginBase.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ PluginBase)
/* harmony export */ });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

/**
 * Plugin base abstract.
 *
 * This class provides the base functionality for all plugins.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var PluginBase = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * The constructor is provided the Snowboard framework instance.
   *
   * @param {Snowboard} snowboard
   */
  function PluginBase(snowboard) {
    _classCallCheck(this, PluginBase);

    this.snowboard = snowboard;
  }
  /**
   * Defines the required plugins for this specific module to work.
   *
   * @returns {string[]} An array of plugins required for this module to work, as strings.
   */


  _createClass(PluginBase, [{
    key: "dependencies",
    value: function dependencies() {
      return [];
    }
    /**
     * Defines the listener methods for global events.
     *
     * @returns {Object}
     */

  }, {
    key: "listens",
    value: function listens() {
      return {};
    }
    /**
     * Destructor.
     *
     * Fired when this plugin is removed.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      this.detach();
      delete this.snowboard;
    }
  }]);

  return PluginBase;
}();



/***/ }),

/***/ "./assets/js/snowboard/abstracts/Singleton.js":
/*!****************************************************!*\
  !*** ./assets/js/snowboard/abstracts/Singleton.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Singleton)
/* harmony export */ });
/* harmony import */ var _PluginBase__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PluginBase */ "./assets/js/snowboard/abstracts/PluginBase.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


/**
 * Singleton plugin abstract.
 *
 * This is a special definition class that the Snowboard framework will use to interpret the current plugin as a
 * "singleton". This will ensure that only one instance of the plugin class is used across the board.
 *
 * Singletons are initialised on the "domReady" event by default.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */

var Singleton = /*#__PURE__*/function (_PluginBase) {
  _inherits(Singleton, _PluginBase);

  var _super = _createSuper(Singleton);

  function Singleton() {
    _classCallCheck(this, Singleton);

    return _super.apply(this, arguments);
  }

  return _createClass(Singleton);
}(_PluginBase__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./assets/js/snowboard/main/PluginLoader.js":
/*!**************************************************!*\
  !*** ./assets/js/snowboard/main/PluginLoader.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ PluginLoader)
/* harmony export */ });
/* harmony import */ var _abstracts_PluginBase__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../abstracts/PluginBase */ "./assets/js/snowboard/abstracts/PluginBase.js");
/* harmony import */ var _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../abstracts/Singleton */ "./assets/js/snowboard/abstracts/Singleton.js");
function _construct(Parent, args, Class) { if (_isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }



/**
 * Plugin loader class.
 *
 * This is a provider (factory) class for a single plugin and provides the link between Snowboard framework functionality
 * and the underlying plugin instances. It also provides some basic mocking of plugin methods for testing.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */

var PluginLoader = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * Binds the Winter framework to the instance.
   *
   * @param {string} name
   * @param {Snowboard} snowboard
   * @param {PluginBase} instance
   */
  function PluginLoader(name, snowboard, instance) {
    _classCallCheck(this, PluginLoader);

    this.name = name;
    this.snowboard = snowboard;
    this.instance = instance;
    this.instances = [];
    this.singleton = instance.prototype instanceof _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__["default"];
    this.mocks = {};
    this.originalFunctions = {};
  }
  /**
   * Determines if the current plugin has a specific method available.
   *
   * Returns false if the current plugin is a callback function.
   *
   * @param {string} methodName
   * @returns {boolean}
   */


  _createClass(PluginLoader, [{
    key: "hasMethod",
    value: function hasMethod(methodName) {
      if (this.isFunction()) {
        return false;
      }

      return typeof this.instance.prototype[methodName] === 'function';
    }
    /**
     * Calls a prototype method for a plugin. This should generally be used for "static" calls.
     *
     * @param {string} methodName
     * @param {...} args
     * @returns {any}
     */

  }, {
    key: "callMethod",
    value: function callMethod() {
      if (this.isFunction()) {
        return null;
      }

      for (var _len = arguments.length, parameters = new Array(_len), _key = 0; _key < _len; _key++) {
        parameters[_key] = arguments[_key];
      }

      var args = parameters;
      var methodName = args.shift();
      return this.instance.prototype[methodName](args);
    }
    /**
     * Returns an instance of the current plugin.
     *
     * - If this is a callback function plugin, the function will be returned.
     * - If this is a singleton, the single instance of the plugin will be returned.
     *
     * @returns {PluginBase|Function}
     */

  }, {
    key: "getInstance",
    value: function getInstance() {
      var _this = this;

      for (var _len2 = arguments.length, parameters = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        parameters[_key2] = arguments[_key2];
      }

      if (this.isFunction()) {
        return this.instance.apply(this, parameters);
      }

      if (!this.dependenciesFulfilled()) {
        var unmet = this.getDependencies().filter(function (item) {
          return !_this.snowboard.getPluginNames().includes(item);
        });
        throw new Error("The \"".concat(this.name, "\" plugin requires the following plugins: ").concat(unmet.join(', ')));
      }

      if (this.isSingleton()) {
        if (this.instances.length === 0) {
          this.initialiseSingleton();
        } // Apply mocked methods


        if (Object.keys(this.mocks).length > 0) {
          Object.entries(this.originalFunctions).forEach(function (entry) {
            var _entry = _slicedToArray(entry, 2),
                methodName = _entry[0],
                callback = _entry[1];

            _this.instances[0][methodName] = callback;
          });
          Object.entries(this.mocks).forEach(function (entry) {
            var _entry2 = _slicedToArray(entry, 2),
                methodName = _entry2[0],
                callback = _entry2[1];

            _this.instances[0][methodName] = function () {
              for (var _len3 = arguments.length, params = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
                params[_key3] = arguments[_key3];
              }

              return callback.apply(void 0, [_this].concat(params));
            };
          });
        }

        return this.instances[0];
      } // Apply mocked methods to prototype


      if (Object.keys(this.mocks).length > 0) {
        Object.entries(this.originalFunctions).forEach(function (entry) {
          var _entry3 = _slicedToArray(entry, 2),
              methodName = _entry3[0],
              callback = _entry3[1];

          _this.instance.prototype[methodName] = callback;
        });
        Object.entries(this.mocks).forEach(function (entry) {
          var _entry4 = _slicedToArray(entry, 2),
              methodName = _entry4[0],
              callback = _entry4[1];

          _this.instance.prototype[methodName] = function () {
            for (var _len4 = arguments.length, params = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
              params[_key4] = arguments[_key4];
            }

            return callback.apply(void 0, [_this].concat(params));
          };
        });
      }

      var newInstance = _construct(this.instance, [this.snowboard].concat(parameters));

      newInstance.detach = function () {
        return _this.instances.splice(_this.instances.indexOf(newInstance), 1);
      };

      this.instances.push(newInstance);
      return newInstance;
    }
    /**
     * Gets all instances of the current plugin.
     *
     * If this plugin is a callback function plugin, an empty array will be returned.
     *
     * @returns {PluginBase[]}
     */

  }, {
    key: "getInstances",
    value: function getInstances() {
      if (this.isFunction()) {
        return [];
      }

      return this.instances;
    }
    /**
     * Determines if the current plugin is a simple callback function.
     *
     * @returns {boolean}
     */

  }, {
    key: "isFunction",
    value: function isFunction() {
      return typeof this.instance === 'function' && this.instance.prototype instanceof _abstracts_PluginBase__WEBPACK_IMPORTED_MODULE_0__["default"] === false;
    }
    /**
     * Determines if the current plugin is a singleton.
     *
     * @returns {boolean}
     */

  }, {
    key: "isSingleton",
    value: function isSingleton() {
      return this.instance.prototype instanceof _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__["default"] === true;
    }
    /**
     * Initialises the singleton instance.
     *
     * @returns {void}
     */

  }, {
    key: "initialiseSingleton",
    value: function initialiseSingleton() {
      var _this2 = this;

      if (!this.isSingleton()) {
        return;
      }

      var newInstance = new this.instance(this.snowboard);

      newInstance.detach = function () {
        return _this2.instances.splice(_this2.instances.indexOf(newInstance), 1);
      };

      this.instances.push(newInstance);
    }
    /**
     * Gets the dependencies of the current plugin.
     *
     * @returns {string[]}
     */

  }, {
    key: "getDependencies",
    value: function getDependencies() {
      // Callback functions cannot have dependencies.
      if (this.isFunction()) {
        return [];
      } // No dependency method specified.


      if (typeof this.instance.prototype.dependencies !== 'function') {
        return [];
      }

      return this.instance.prototype.dependencies().map(function (item) {
        return item.toLowerCase();
      });
    }
    /**
     * Determines if the current plugin has all its dependencies fulfilled.
     *
     * @returns {boolean}
     */

  }, {
    key: "dependenciesFulfilled",
    value: function dependenciesFulfilled() {
      var _this3 = this;

      var dependencies = this.getDependencies();
      var fulfilled = true;
      dependencies.forEach(function (plugin) {
        if (!_this3.snowboard.hasPlugin(plugin)) {
          fulfilled = false;
        }
      });
      return fulfilled;
    }
    /**
     * Allows a method of an instance to be mocked for testing.
     *
     * This mock will be applied for the life of an instance. For singletons, the mock will be applied for the life
     * of the page.
     *
     * Mocks cannot be applied to callback function plugins.
     *
     * @param {string} methodName
     * @param {Function} callback
     */

  }, {
    key: "mock",
    value: function mock(methodName, callback) {
      var _this4 = this;

      if (this.isFunction()) {
        return;
      }

      if (!this.instance.prototype[methodName]) {
        throw new Error("Function \"".concat(methodName, "\" does not exist and cannot be mocked"));
      }

      this.mocks[methodName] = callback;
      this.originalFunctions[methodName] = this.instance.prototype[methodName];

      if (this.isSingleton() && this.instances.length === 0) {
        this.initialiseSingleton(); // Apply mocked method

        this.instances[0][methodName] = function () {
          for (var _len5 = arguments.length, parameters = new Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
            parameters[_key5] = arguments[_key5];
          }

          return callback.apply(void 0, [_this4].concat(parameters));
        };
      }
    }
    /**
     * Removes a mock callback from future instances.
     *
     * @param {string} methodName
     */

  }, {
    key: "unmock",
    value: function unmock(methodName) {
      if (this.isFunction()) {
        return;
      }

      if (!this.mocks[methodName]) {
        return;
      }

      if (this.isSingleton()) {
        this.instances[0][methodName] = this.originalFunctions[methodName];
      }

      delete this.mocks[methodName];
      delete this.originalFunctions[methodName];
    }
  }]);

  return PluginLoader;
}();



/***/ }),

/***/ "./assets/js/snowboard/main/Snowboard.js":
/*!***********************************************!*\
  !*** ./assets/js/snowboard/main/Snowboard.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Snowboard)
/* harmony export */ });
/* harmony import */ var _abstracts_PluginBase__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../abstracts/PluginBase */ "./assets/js/snowboard/abstracts/PluginBase.js");
/* harmony import */ var _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../abstracts/Singleton */ "./assets/js/snowboard/abstracts/Singleton.js");
/* harmony import */ var _PluginLoader__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./PluginLoader */ "./assets/js/snowboard/main/PluginLoader.js");
/* harmony import */ var _utilities_Cookie__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utilities/Cookie */ "./assets/js/snowboard/utilities/Cookie.js");
/* harmony import */ var _utilities_JsonParser__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utilities/JsonParser */ "./assets/js/snowboard/utilities/JsonParser.js");
/* harmony import */ var _utilities_Sanitizer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../utilities/Sanitizer */ "./assets/js/snowboard/utilities/Sanitizer.js");
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }







/**
 * Snowboard - the Winter JavaScript framework.
 *
 * This class represents the base of a modern take on the Winter JS framework, being fully extensible and taking advantage
 * of modern JavaScript features by leveraging the Laravel Mix compilation framework. It also is coded up to remove the
 * dependency of jQuery.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com/docs/snowboard/introduction
 */

var Snowboard = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * @param {boolean} autoSingletons Automatically load singletons when DOM is ready. Default: `true`.
   * @param {boolean} debug Whether debugging logs should be shown. Default: `false`.
   */
  function Snowboard(autoSingletons, debug) {
    _classCallCheck(this, Snowboard);

    this.debugEnabled = typeof debug === 'boolean' && debug === true;
    this.autoInitSingletons = typeof autoSingletons === 'boolean' && autoSingletons === false;
    this.plugins = {};
    this.attachAbstracts();
    this.loadUtilities();
    this.initialise();
    this.debug('Snowboard framework initialised');
  }

  _createClass(Snowboard, [{
    key: "attachAbstracts",
    value: function attachAbstracts() {
      this.PluginBase = _abstracts_PluginBase__WEBPACK_IMPORTED_MODULE_0__["default"];
      this.Singleton = _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__["default"];
    }
  }, {
    key: "loadUtilities",
    value: function loadUtilities() {
      this.addPlugin('cookie', _utilities_Cookie__WEBPACK_IMPORTED_MODULE_3__["default"]);
      this.addPlugin('jsonParser', _utilities_JsonParser__WEBPACK_IMPORTED_MODULE_4__["default"]);
      this.addPlugin('sanitizer', _utilities_Sanitizer__WEBPACK_IMPORTED_MODULE_5__["default"]);
    }
    /**
     * Initialises the framework.
     *
     * Attaches a listener for the DOM being ready and triggers a global "ready" event for plugins to begin attaching
     * themselves to the DOM.
     */

  }, {
    key: "initialise",
    value: function initialise() {
      var _this = this;

      window.addEventListener('DOMContentLoaded', function () {
        if (_this.autoInitSingletons) {
          _this.initialiseSingletons();
        }

        _this.globalEvent('ready');
      });
    }
    /**
     * Initialises an instance of every singleton.
     */

  }, {
    key: "initialiseSingletons",
    value: function initialiseSingletons() {
      Object.values(this.plugins).forEach(function (plugin) {
        if (plugin.isSingleton()) {
          plugin.initialiseSingleton();
        }
      });
    }
    /**
     * Adds a plugin to the framework.
     *
     * Plugins are the cornerstone for additional functionality for Snowboard. A plugin must either be an ES2015 class
     * that extends the PluginBase or Singleton abstract classes, or a simple callback function.
     *
     * When a plugin is added, it is automatically assigned as a new magic method in the Snowboard class using the name
     * parameter, and can be called via this method. This method will always be the "lowercase" version of this name.
     *
     * For example, if a plugin is assigned to the name "myPlugin", it can be called via `Snowboard.myplugin()`.
     *
     * @param {string} name
     * @param {PluginBase|Function} instance
     */

  }, {
    key: "addPlugin",
    value: function addPlugin(name, instance) {
      var _this2 = this;

      var lowerName = name.toLowerCase();

      if (this.hasPlugin(lowerName)) {
        throw new Error("A plugin called \"".concat(name, "\" is already registered."));
      }

      if (typeof instance !== 'function' && instance instanceof _abstracts_PluginBase__WEBPACK_IMPORTED_MODULE_0__["default"] === false) {
        throw new Error('The provided plugin must extend the PluginBase class, or must be a callback function.');
      }

      if (this[name] !== undefined || this[lowerName] !== undefined) {
        throw new Error('The given name is already in use for a property or method of the Snowboard class.');
      }

      this.plugins[lowerName] = new _PluginLoader__WEBPACK_IMPORTED_MODULE_2__["default"](lowerName, this, instance);

      var callback = function callback() {
        var _this2$plugins$lowerN;

        return (_this2$plugins$lowerN = _this2.plugins[lowerName]).getInstance.apply(_this2$plugins$lowerN, arguments);
      };

      this[name] = callback;
      this[lowerName] = callback;
      this.debug("Plugin \"".concat(name, "\" registered"));
    }
    /**
     * Removes a plugin.
     *
     * Removes a plugin from Snowboard, calling the destructor method for all active instances of the plugin.
     *
     * @param {string} name
     * @returns {void}
     */

  }, {
    key: "removePlugin",
    value: function removePlugin(name) {
      var lowerName = name.toLowerCase();

      if (!this.hasPlugin(lowerName)) {
        this.debug("Plugin \"".concat(name, "\" already removed"));
        return;
      } // Call destructors for all instances


      this.plugins[lowerName].getInstances().forEach(function (instance) {
        instance.destructor();
      });
      delete this.plugins[lowerName];
      delete this[lowerName];
      this.debug("Plugin \"".concat(name, "\" removed"));
    }
    /**
     * Determines if a plugin has been registered and is active.
     *
     * A plugin that is still waiting for dependencies to be registered will not be active.
     *
     * @param {string} name
     * @returns {boolean}
     */

  }, {
    key: "hasPlugin",
    value: function hasPlugin(name) {
      var lowerName = name.toLowerCase();
      return this.plugins[lowerName] !== undefined;
    }
    /**
     * Returns an array of registered plugins as PluginLoader objects.
     *
     * @returns {PluginLoader[]}
     */

  }, {
    key: "getPlugins",
    value: function getPlugins() {
      return this.plugins;
    }
    /**
     * Returns an array of registered plugins, by name.
     *
     * @returns {string[]}
     */

  }, {
    key: "getPluginNames",
    value: function getPluginNames() {
      return Object.keys(this.plugins);
    }
    /**
     * Returns a PluginLoader object of a given plugin.
     *
     * @returns {PluginLoader}
     */

  }, {
    key: "getPlugin",
    value: function getPlugin(name) {
      if (!this.hasPlugin(name)) {
        throw new Error("No plugin called \"".concat(name, "\" has been registered."));
      }

      return this.plugins[name];
    }
    /**
     * Finds all plugins that listen to the given event.
     *
     * This works for both normal and promise events. It does NOT check that the plugin's listener actually exists.
     *
     * @param {string} eventName
     * @returns {string[]} The name of the plugins that are listening to this event.
     */

  }, {
    key: "listensToEvent",
    value: function listensToEvent(eventName) {
      var plugins = [];
      Object.entries(this.plugins).forEach(function (entry) {
        var _entry = _slicedToArray(entry, 2),
            name = _entry[0],
            plugin = _entry[1];

        if (plugin.isFunction()) {
          return;
        }

        if (!plugin.hasMethod('listens')) {
          return;
        }

        var listeners = plugin.callMethod('listens');

        if (typeof listeners[eventName] === 'string') {
          plugins.push(name);
        }
      });
      return plugins;
    }
    /**
     * Calls a global event to all registered plugins.
     *
     * If any plugin returns a `false`, the event is considered cancelled.
     *
     * @param {string} eventName
     * @returns {boolean} If event was not cancelled
     */

  }, {
    key: "globalEvent",
    value: function globalEvent(eventName) {
      var _this3 = this;

      for (var _len = arguments.length, parameters = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        parameters[_key - 1] = arguments[_key];
      }

      this.debug("Calling global event \"".concat(eventName, "\"")); // Find out which plugins listen to this event - if none listen to it, return true.

      var listeners = this.listensToEvent(eventName);

      if (listeners.length === 0) {
        this.debug("No listeners found for global event \"".concat(eventName, "\""));
        return true;
      }

      this.debug("Listeners found for global event \"".concat(eventName, "\": ").concat(listeners.join(', ')));
      var cancelled = false;
      listeners.forEach(function (name) {
        var plugin = _this3.getPlugin(name);

        if (plugin.isFunction()) {
          return;
        }

        if (plugin.isSingleton() && plugin.getInstances().length === 0) {
          plugin.initialiseSingleton();
        }

        var listenMethod = plugin.callMethod('listens')[eventName]; // Call event handler methods for all plugins, if they have a method specified for the event.

        plugin.getInstances().forEach(function (instance) {
          // If a plugin has cancelled the event, no further plugins are considered.
          if (cancelled) {
            return;
          }

          if (!instance[listenMethod]) {
            throw new Error("Missing \"".concat(listenMethod, "\" method in \"").concat(name, "\" plugin"));
          }

          if (instance[listenMethod].apply(instance, parameters) === false) {
            cancelled = true;

            _this3.debug("Global event \"".concat(eventName, "\" cancelled by \"").concat(name, "\" plugin"));
          }
        });
      });
      return !cancelled;
    }
    /**
     * Calls a global event to all registered plugins, expecting a Promise to be returned by all.
     *
     * This collates all plugins responses into one large Promise that either expects all to be resolved, or one to reject.
     * If no listeners are found, a resolved Promise is returned.
     *
     * @param {string} eventName
     */

  }, {
    key: "globalPromiseEvent",
    value: function globalPromiseEvent(eventName) {
      var _this4 = this;

      for (var _len2 = arguments.length, parameters = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        parameters[_key2 - 1] = arguments[_key2];
      }

      this.debug("Calling global promise event \"".concat(eventName, "\"")); // Find out which plugins listen to this event - if none listen to it, return a resolved promise.

      var listeners = this.listensToEvent(eventName);

      if (listeners.length === 0) {
        this.debug("No listeners found for global promise event \"".concat(eventName, "\""));
        return Promise.resolve();
      }

      this.debug("Listeners found for global promise event \"".concat(eventName, "\": ").concat(listeners.join(', ')));
      var promises = [];
      listeners.forEach(function (name) {
        var plugin = _this4.getPlugin(name);

        if (plugin.isFunction()) {
          return;
        }

        if (plugin.isSingleton() && plugin.getInstances().length === 0) {
          plugin.initialiseSingleton();
        }

        var listenMethod = plugin.callMethod('listens')[eventName]; // Call event handler methods for all plugins, if they have a method specified for the event.

        plugin.getInstances().forEach(function (instance) {
          var instancePromise = instance[listenMethod].apply(instance, parameters);

          if (instancePromise instanceof Promise === false) {
            return;
          }

          promises.push(instancePromise);
        });
      });

      if (promises.length === 0) {
        return Promise.resolve();
      }

      return Promise.all(promises);
    }
    /**
     * Log a debug message.
     *
     * These messages are only shown when debugging is enabled.
     *
     * @returns {void}
     */

  }, {
    key: "debug",
    value: function debug() {
      var _console;

      if (!this.debugEnabled) {
        return;
      }
      /* eslint-disable */


      for (var _len3 = arguments.length, parameters = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        parameters[_key3] = arguments[_key3];
      }

      (_console = console).groupCollapsed.apply(_console, ['%c[Snowboard]', 'color: rgb(45, 167, 199); font-weight: normal;'].concat(parameters));

      console.trace();
      console.groupEnd();
      /* eslint-enable */
    }
  }]);

  return Snowboard;
}();



/***/ }),

/***/ "./assets/js/snowboard/snowboard.base.debug.js":
/*!*****************************************************!*\
  !*** ./assets/js/snowboard/snowboard.base.debug.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_Snowboard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main/Snowboard */ "./assets/js/snowboard/main/Snowboard.js");


(function (window) {
  var snowboard = new _main_Snowboard__WEBPACK_IMPORTED_MODULE_0__["default"](true, true); // Cover all aliases

  window.snowboard = snowboard;
  window.Snowboard = snowboard;
  window.SnowBoard = snowboard;
})(window);

/***/ }),

/***/ "./assets/js/snowboard/utilities/Cookie.js":
/*!*************************************************!*\
  !*** ./assets/js/snowboard/utilities/Cookie.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Cookie)
/* harmony export */ });
/* harmony import */ var js_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! js-cookie */ "../../node_modules/js-cookie/dist/js.cookie.mjs");
/* harmony import */ var _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../abstracts/Singleton */ "./assets/js/snowboard/abstracts/Singleton.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

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
 * Cookie utility.
 *
 * This utility is a thin wrapper around the "js-cookie" library.
 *
 * @see https://github.com/js-cookie/js-cookie
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */

var Cookie = /*#__PURE__*/function (_Singleton) {
  _inherits(Cookie, _Singleton);

  var _super = _createSuper(Cookie);

  function Cookie(snowboard) {
    var _this;

    _classCallCheck(this, Cookie);

    _this = _super.call(this, snowboard);
    _this.defaults = {
      expires: null,
      path: '/',
      domain: null,
      secure: false,
      sameSite: 'Lax'
    };
    return _this;
  }
  /**
   * Set the default cookie parameters for all subsequent "set" and "remove" calls.
   *
   * @param {Object} options
   */


  _createClass(Cookie, [{
    key: "setDefaults",
    value: function setDefaults(options) {
      var _this2 = this;

      if (_typeof(options) !== 'object') {
        throw new Error('Cookie defaults must be provided as an object');
      }

      Object.entries(options).forEach(function (entry) {
        var _entry = _slicedToArray(entry, 2),
            key = _entry[0],
            value = _entry[1];

        if (_this2.defaults[key] !== undefined) {
          _this2.defaults[key] = value;
        }
      });
    }
    /**
     * Get the current default cookie parameters.
     *
     * @returns {Object}
     */

  }, {
    key: "getDefaults",
    value: function getDefaults() {
      var _this3 = this;

      var defaults = {};
      Object.entries(this.defaults).forEach(function (entry) {
        var _entry2 = _slicedToArray(entry, 2),
            key = _entry2[0],
            value = _entry2[1];

        if (_this3.defaults[key] !== null) {
          defaults[key] = value;
        }
      });
      return defaults;
    }
    /**
     * Get a cookie by name.
     *
     * If `name` is undefined, returns all cookies as an Object.
     *
     * @param {String} name
     * @returns {Object|String}
     */

  }, {
    key: "get",
    value: function get(name) {
      var _this4 = this;

      if (name === undefined) {
        var cookies = js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].get();
        Object.entries(cookies).forEach(function (entry) {
          var _entry3 = _slicedToArray(entry, 2),
              cookieName = _entry3[0],
              cookieValue = _entry3[1];

          _this4.snowboard.globalEvent('cookie.get', cookieName, cookieValue, function (newValue) {
            cookies[cookieName] = newValue;
          });
        });
        return cookies;
      }

      var value = js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].get(name); // Allow plugins to override the gotten value

      this.snowboard.globalEvent('cookie.get', name, value, function (newValue) {
        value = newValue;
      });
      return value;
    }
    /**
     * Set a cookie by name.
     *
     * You can specify additional cookie parameters through the "options" parameter.
     *
     * @param {String} name
     * @param {String} value
     * @param {Object} options
     * @returns {String}
     */

  }, {
    key: "set",
    value: function set(name, value, options) {
      var saveValue = value; // Allow plugins to override the value to save

      this.snowboard.globalEvent('cookie.set', name, value, function (newValue) {
        saveValue = newValue;
      });
      return js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].set(name, saveValue, _objectSpread(_objectSpread({}, this.getDefaults()), options));
    }
    /**
     * Remove a cookie by name.
     *
     * You can specify the additional cookie parameters via the "options" parameter.
     *
     * @param {String} name
     * @param {Object} options
     * @returns {void}
     */

  }, {
    key: "remove",
    value: function remove(name, options) {
      js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].remove(name, _objectSpread(_objectSpread({}, this.getDefaults()), options));
    }
  }]);

  return Cookie;
}(_abstracts_Singleton__WEBPACK_IMPORTED_MODULE_1__["default"]);



/***/ }),

/***/ "./assets/js/snowboard/utilities/JsonParser.js":
/*!*****************************************************!*\
  !*** ./assets/js/snowboard/utilities/JsonParser.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ JsonParser)
/* harmony export */ });
/* harmony import */ var _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../abstracts/Singleton */ "./assets/js/snowboard/abstracts/Singleton.js");
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
 * JSON Parser utility.
 *
 * This utility parses JSON-like data that does not strictly meet the JSON specifications in order to simplify development.
 * It is a safe replacement for JSON.parse(JSON.stringify(eval("({" + value + "})"))) that does not require the use of eval()
 *
 * @author Ayumi Hamasaki
 * @author Ben Thomson <git@alfreido.com>
 * @see https://github.com/octobercms/october/pull/4527
 */

var JsonParser = /*#__PURE__*/function (_Singleton) {
  _inherits(JsonParser, _Singleton);

  var _super = _createSuper(JsonParser);

  function JsonParser(snowboard) {
    var _this;

    _classCallCheck(this, JsonParser);

    _this = _super.call(this, snowboard); // Add to global function for backwards compatibility

    window.wnJSON = function (json) {
      return _this.parse(json);
    };

    window.ocJSON = window.wnJSON;
    return _this;
  }

  _createClass(JsonParser, [{
    key: "parse",
    value: function parse(str) {
      var jsonString = this.parseString(str);
      return JSON.parse(jsonString);
    }
  }, {
    key: "parseString",
    value: function parseString(value) {
      var str = value.trim();

      if (!str.length) {
        throw new Error('Broken JSON object.');
      }

      var result = '';
      var type = null;
      var key = null;
      var body = '';
      /*
      * the mistake ','
      */

      while (str && str[0] === ',') {
        str = str.substr(1);
      }
      /*
      * string
      */


      if (str[0] === '"' || str[0] === '\'') {
        if (str[str.length - 1] !== str[0]) {
          throw new Error('Invalid string JSON object.');
        }

        body = '"';

        for (var i = 1; i < str.length; i += 1) {
          if (str[i] === '\\') {
            if (str[i + 1] === '\'') {
              body += str[i + 1];
            } else {
              body += str[i];
              body += str[i + 1];
            }

            i += 1;
          } else if (str[i] === str[0]) {
            body += '"';
            return body;
          } else if (str[i] === '"') {
            body += '\\"';
          } else {
            body += str[i];
          }
        }

        throw new Error('Invalid string JSON object.');
      }
      /*
      * boolean
      */


      if (str === 'true' || str === 'false') {
        return str;
      }
      /*
      * null
      */


      if (str === 'null') {
        return 'null';
      }
      /*
      * number
      */


      var num = parseFloat(str);

      if (!Number.isNaN(num)) {
        return num.toString();
      }
      /*
      * object
      */


      if (str[0] === '{') {
        type = 'needKey';
        key = null;
        result = '{';

        for (var _i = 1; _i < str.length; _i += 1) {
          if (this.isBlankChar(str[_i])) {
            /* eslint-disable-next-line */
            continue;
          }

          if (type === 'needKey' && (str[_i] === '"' || str[_i] === '\'')) {
            key = this.parseKey(str, _i + 1, str[_i]);
            result += "\"".concat(key, "\"");
            _i += key.length;
            _i += 1;
            type = 'afterKey';
          } else if (type === 'needKey' && this.canBeKeyHead(str[_i])) {
            key = this.parseKey(str, _i);
            result += '"';
            result += key;
            result += '"';
            _i += key.length - 1;
            type = 'afterKey';
          } else if (type === 'afterKey' && str[_i] === ':') {
            result += ':';
            type = ':';
          } else if (type === ':') {
            body = this.getBody(str, _i);
            _i = _i + body.originLength - 1;
            result += this.parseString(body.body);
            type = 'afterBody';
          } else if (type === 'afterBody' || type === 'needKey') {
            var last = _i;

            while (str[last] === ',' || this.isBlankChar(str[last])) {
              last += 1;
            }

            if (str[last] === '}' && last === str.length - 1) {
              while (result[result.length - 1] === ',') {
                result = result.substr(0, result.length - 1);
              }

              result += '}';
              return result;
            }

            if (last !== _i && result !== '{') {
              result += ',';
              type = 'needKey';
              _i = last - 1;
            }
          }
        }

        throw new Error("Broken JSON object near ".concat(result));
      }
      /*
      * array
      */


      if (str[0] === '[') {
        result = '[';
        type = 'needBody';

        for (var _i2 = 1; _i2 < str.length; _i2 += 1) {
          if (str[_i2] === ' ' || str[_i2] === '\n' || str[_i2] === '\t') {
            /* eslint-disable-next-line */
            continue;
          } else if (type === 'needBody') {
            if (str[_i2] === ',') {
              result += 'null,';
              /* eslint-disable-next-line */

              continue;
            }

            if (str[_i2] === ']' && _i2 === str.length - 1) {
              if (result[result.length - 1] === ',') {
                result = result.substr(0, result.length - 1);
              }

              result += ']';
              return result;
            }

            body = this.getBody(str, _i2);
            _i2 = _i2 + body.originLength - 1;
            result += this.parseString(body.body);
            type = 'afterBody';
          } else if (type === 'afterBody') {
            if (str[_i2] === ',') {
              result += ',';
              type = 'needBody'; // deal with mistake ","

              while (str[_i2 + 1] === ',' || this.isBlankChar(str[_i2 + 1])) {
                if (str[_i2 + 1] === ',') {
                  result += 'null,';
                }

                _i2 += 1;
              }
            } else if (str[_i2] === ']' && _i2 === str.length - 1) {
              result += ']';
              return result;
            }
          }
        }

        throw new Error("Broken JSON array near ".concat(result));
      }

      return '';
    }
  }, {
    key: "getBody",
    value: function getBody(str, pos) {
      var body = ''; // parse string body

      if (str[pos] === '"' || str[pos] === '\'') {
        body = str[pos];

        for (var i = pos + 1; i < str.length; i += 1) {
          if (str[i] === '\\') {
            body += str[i];

            if (i + 1 < str.length) {
              body += str[i + 1];
            }

            i += 1;
          } else if (str[i] === str[pos]) {
            body += str[pos];
            return {
              originLength: body.length,
              body: body
            };
          } else {
            body += str[i];
          }
        }

        throw new Error("Broken JSON string body near ".concat(body));
      } // parse true / false


      if (str[pos] === 't') {
        if (str.indexOf('true', pos) === pos) {
          return {
            originLength: 'true'.length,
            body: 'true'
          };
        }

        throw new Error("Broken JSON boolean body near ".concat(str.substr(0, pos + 10)));
      }

      if (str[pos] === 'f') {
        if (str.indexOf('f', pos) === pos) {
          return {
            originLength: 'false'.length,
            body: 'false'
          };
        }

        throw new Error("Broken JSON boolean body near ".concat(str.substr(0, pos + 10)));
      } // parse null


      if (str[pos] === 'n') {
        if (str.indexOf('null', pos) === pos) {
          return {
            originLength: 'null'.length,
            body: 'null'
          };
        }

        throw new Error("Broken JSON boolean body near ".concat(str.substr(0, pos + 10)));
      } // parse number


      if (str[pos] === '-' || str[pos] === '+' || str[pos] === '.' || str[pos] >= '0' && str[pos] <= '9') {
        body = '';

        for (var _i3 = pos; _i3 < str.length; _i3 += 1) {
          if (str[_i3] === '-' || str[_i3] === '+' || str[_i3] === '.' || str[_i3] >= '0' && str[_i3] <= '9') {
            body += str[_i3];
          } else {
            return {
              originLength: body.length,
              body: body
            };
          }
        }

        throw new Error("Broken JSON number body near ".concat(body));
      } // parse object


      if (str[pos] === '{' || str[pos] === '[') {
        var stack = [str[pos]];
        body = str[pos];

        for (var _i4 = pos + 1; _i4 < str.length; _i4 += 1) {
          body += str[_i4];

          if (str[_i4] === '\\') {
            if (_i4 + 1 < str.length) {
              body += str[_i4 + 1];
            }

            _i4 += 1;
          } else if (str[_i4] === '"') {
            if (stack[stack.length - 1] === '"') {
              stack.pop();
            } else if (stack[stack.length - 1] !== '\'') {
              stack.push(str[_i4]);
            }
          } else if (str[_i4] === '\'') {
            if (stack[stack.length - 1] === '\'') {
              stack.pop();
            } else if (stack[stack.length - 1] !== '"') {
              stack.push(str[_i4]);
            }
          } else if (stack[stack.length - 1] !== '"' && stack[stack.length - 1] !== '\'') {
            if (str[_i4] === '{') {
              stack.push('{');
            } else if (str[_i4] === '}') {
              if (stack[stack.length - 1] === '{') {
                stack.pop();
              } else {
                throw new Error("Broken JSON ".concat(str[pos] === '{' ? 'object' : 'array', " body near ").concat(body));
              }
            } else if (str[_i4] === '[') {
              stack.push('[');
            } else if (str[_i4] === ']') {
              if (stack[stack.length - 1] === '[') {
                stack.pop();
              } else {
                throw new Error("Broken JSON ".concat(str[pos] === '{' ? 'object' : 'array', " body near ").concat(body));
              }
            }
          }

          if (!stack.length) {
            return {
              originLength: _i4 - pos,
              body: body
            };
          }
        }

        throw new Error("Broken JSON ".concat(str[pos] === '{' ? 'object' : 'array', " body near ").concat(body));
      }

      throw new Error("Broken JSON body near ".concat(str.substr(pos - 5 >= 0 ? pos - 5 : 0, 50)));
    }
  }, {
    key: "parseKey",
    value: function parseKey(str, pos, quote) {
      var key = '';

      for (var i = pos; i < str.length; i += 1) {
        if (quote && quote === str[i]) {
          return key;
        }

        if (!quote && (str[i] === ' ' || str[i] === ':')) {
          return key;
        }

        key += str[i];

        if (str[i] === '\\' && i + 1 < str.length) {
          key += str[i + 1];
          i += 1;
        }
      }

      throw new Error("Broken JSON syntax near ".concat(key));
    }
  }, {
    key: "canBeKeyHead",
    value: function canBeKeyHead(ch) {
      if (ch[0] === '\\') {
        return false;
      }

      if (ch[0] >= 'a' && ch[0] <= 'z' || ch[0] >= 'A' && ch[0] <= 'Z' || ch[0] === '_') {
        return true;
      }

      if (ch[0] >= '0' && ch[0] <= '9') {
        return true;
      }

      if (ch[0] === '$') {
        return true;
      }

      if (ch.charCodeAt(0) > 255) {
        return true;
      }

      return false;
    }
  }, {
    key: "isBlankChar",
    value: function isBlankChar(ch) {
      return ch === ' ' || ch === '\n' || ch === '\t';
    }
  }]);

  return JsonParser;
}(_abstracts_Singleton__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./assets/js/snowboard/utilities/Sanitizer.js":
/*!****************************************************!*\
  !*** ./assets/js/snowboard/utilities/Sanitizer.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Sanitizer)
/* harmony export */ });
/* harmony import */ var _abstracts_Singleton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../abstracts/Singleton */ "./assets/js/snowboard/abstracts/Singleton.js");
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
 * Sanitizer utility.
 *
 * Client-side HTML sanitizer designed mostly to prevent self-XSS attacks.
 * The sanitizer utility will strip all attributes that start with `on` (usually JS event handlers as attributes, i.e. `onload` or `onerror`) or contain the `javascript:` pseudo protocol in their values.
 *
 * @author Ben Thomson <git@alfreido.com>
 */

var Sanitizer = /*#__PURE__*/function (_Singleton) {
  _inherits(Sanitizer, _Singleton);

  var _super = _createSuper(Sanitizer);

  function Sanitizer(snowboard) {
    var _this;

    _classCallCheck(this, Sanitizer);

    _this = _super.call(this, snowboard); // Add to global function for backwards compatibility

    window.wnSanitize = function (html) {
      return _this.sanitize(html);
    };

    window.ocSanitize = window.wnSanitize;
    return _this;
  }

  _createClass(Sanitizer, [{
    key: "sanitize",
    value: function sanitize(html, bodyOnly) {
      var parser = new DOMParser();
      var dom = parser.parseFromString(html, 'text/html');
      var returnBodyOnly = bodyOnly !== undefined && typeof bodyOnly === 'boolean' ? bodyOnly : true;
      this.sanitizeNode(dom.getRootNode());
      return returnBodyOnly ? dom.body.innerHTML : dom.innerHTML;
    }
  }, {
    key: "sanitizeNode",
    value: function sanitizeNode(node) {
      var _this2 = this;

      if (node.tagName === 'SCRIPT') {
        node.remove();
        return;
      }

      this.trimAttributes(node);
      var children = Array.from(node.children);
      children.forEach(function (child) {
        _this2.sanitizeNode(child);
      });
    }
  }, {
    key: "trimAttributes",
    value: function trimAttributes(node) {
      if (!node.attributes) {
        return;
      }

      for (var i = 0; i < node.attributes.length; i += 1) {
        var attrName = node.attributes.item(i).name;
        var attrValue = node.attributes.item(i).value;
        /*
        * remove attributes where the names start with "on" (for example: onload, onerror...)
        * remove attributes where the value starts with the "javascript:" pseudo protocol (for example href="javascript:alert(1)")
        */

        /* eslint-disable-next-line */

        if (attrName.indexOf('on') === 0 || attrValue.indexOf('javascript:') === 0) {
          node.removeAttribute(attrName);
        }
      }
    }
  }]);

  return Sanitizer;
}(_abstracts_Singleton__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["assets/js/vendor/vendor"], () => (__webpack_exec__("./assets/js/snowboard/snowboard.base.debug.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYnVpbGQvc25vd2JvYXJkLmJhc2UuZGVidWcuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7SUFDcUJBO0FBQ2pCO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0ksc0JBQVlDLFNBQVosRUFBdUI7QUFBQTs7QUFDbkIsU0FBS0EsU0FBTCxHQUFpQkEsU0FBakI7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7O1dBQ0ksd0JBQWU7QUFDWCxhQUFPLEVBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSxtQkFBVTtBQUNOLGFBQU8sRUFBUDtBQUNIO0FBRUQ7QUFDSjtBQUNBO0FBQ0E7QUFDQTs7OztXQUNJLHNCQUFhO0FBQ1QsV0FBS0MsTUFBTDtBQUNBLGFBQU8sS0FBS0QsU0FBWjtBQUNIOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDOUNMO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7SUFDcUJFOzs7Ozs7Ozs7Ozs7RUFBa0JIOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNidkM7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7SUFDcUJJO0FBQ2pCO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNJLHdCQUFZQyxJQUFaLEVBQWtCSixTQUFsQixFQUE2QkssUUFBN0IsRUFBdUM7QUFBQTs7QUFDbkMsU0FBS0QsSUFBTCxHQUFZQSxJQUFaO0FBQ0EsU0FBS0osU0FBTCxHQUFpQkEsU0FBakI7QUFDQSxTQUFLSyxRQUFMLEdBQWdCQSxRQUFoQjtBQUNBLFNBQUtDLFNBQUwsR0FBaUIsRUFBakI7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QkQsUUFBQUEsSUFBSSxDQUFDN0MsTUFBTDtBQUNBO0FBQ0g7O0FBRUQsV0FBSytDLGNBQUwsQ0FBb0JGLElBQXBCO0FBRUEsVUFBTUcsUUFBUSxHQUFHQyxLQUFLLENBQUNDLElBQU4sQ0FBV0wsSUFBSSxDQUFDRyxRQUFoQixDQUFqQjtBQUVBQSxNQUFBQSxRQUFRLENBQUN6SCxPQUFULENBQWlCLFVBQUM0SCxLQUFELEVBQVc7QUFDeEIsY0FBSSxDQUFDVCxZQUFMLENBQWtCUyxLQUFsQjtBQUNILE9BRkQ7QUFHSDs7O1dBRUQsd0JBQWVOLElBQWYsRUFBcUI7QUFDakIsVUFBSSxDQUFDQSxJQUFJLENBQUNPLFVBQVYsRUFBc0I7QUFDbEI7QUFDSDs7QUFFRCxXQUFLLElBQUl0QyxDQUFDLEdBQUcsQ0FBYixFQUFnQkEsQ0FBQyxHQUFHK0IsSUFBSSxDQUFDTyxVQUFMLENBQWdCbEksTUFBcEMsRUFBNEM0RixDQUFDLElBQUksQ0FBakQsRUFBb0Q7QUFDaEQsWUFBTXVDLFFBQVEsR0FBR1IsSUFBSSxDQUFDTyxVQUFMLENBQWdCeEksSUFBaEIsQ0FBcUJrRyxDQUFyQixFQUF3QmxILElBQXpDO0FBQ0EsWUFBTTBKLFNBQVMsR0FBR1QsSUFBSSxDQUFDTyxVQUFMLENBQWdCeEksSUFBaEIsQ0FBcUJrRyxDQUFyQixFQUF3QnZCLEtBQTFDO0FBRUE7QUFDWjtBQUNBO0FBQ0E7O0FBQ1k7O0FBQ0EsWUFBSThELFFBQVEsQ0FBQ3hILE9BQVQsQ0FBaUIsSUFBakIsTUFBMkIsQ0FBM0IsSUFBZ0N5SCxTQUFTLENBQUN6SCxPQUFWLENBQWtCLGFBQWxCLE1BQXFDLENBQXpFLEVBQTRFO0FBQ3hFZ0gsVUFBQUEsSUFBSSxDQUFDVSxlQUFMLENBQXFCRixRQUFyQjtBQUNIO0FBQ0o7QUFDSjs7OztFQXREa0MzSiIsInNvdXJjZXMiOlsid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9hYnN0cmFjdHMvUGx1Z2luQmFzZS5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYWJzdHJhY3RzL1NpbmdsZXRvbi5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvbWFpbi9QbHVnaW5Mb2FkZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL21haW4vU25vd2JvYXJkLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9zbm93Ym9hcmQuYmFzZS5kZWJ1Zy5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvdXRpbGl0aWVzL0Nvb2tpZS5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvdXRpbGl0aWVzL0pzb25QYXJzZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL3V0aWxpdGllcy9TYW5pdGl6ZXIuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBQbHVnaW4gYmFzZSBhYnN0cmFjdC5cbiAqXG4gKiBUaGlzIGNsYXNzIHByb3ZpZGVzIHRoZSBiYXNlIGZ1bmN0aW9uYWxpdHkgZm9yIGFsbCBwbHVnaW5zLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQbHVnaW5CYXNlIHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIFRoZSBjb25zdHJ1Y3RvciBpcyBwcm92aWRlZCB0aGUgU25vd2JvYXJkIGZyYW1ld29yayBpbnN0YW5jZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U25vd2JvYXJkfSBzbm93Ym9hcmRcbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQgPSBzbm93Ym9hcmQ7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVmaW5lcyB0aGUgcmVxdWlyZWQgcGx1Z2lucyBmb3IgdGhpcyBzcGVjaWZpYyBtb2R1bGUgdG8gd29yay5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtzdHJpbmdbXX0gQW4gYXJyYXkgb2YgcGx1Z2lucyByZXF1aXJlZCBmb3IgdGhpcyBtb2R1bGUgdG8gd29yaywgYXMgc3RyaW5ncy5cbiAgICAgKi9cbiAgICBkZXBlbmRlbmNpZXMoKSB7XG4gICAgICAgIHJldHVybiBbXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIHRoZSBsaXN0ZW5lciBtZXRob2RzIGZvciBnbG9iYWwgZXZlbnRzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge307XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVzdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIEZpcmVkIHdoZW4gdGhpcyBwbHVnaW4gaXMgcmVtb3ZlZC5cbiAgICAgKi9cbiAgICBkZXN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmRldGFjaCgpO1xuICAgICAgICBkZWxldGUgdGhpcy5zbm93Ym9hcmQ7XG4gICAgfVxufVxuIiwiaW1wb3J0IFBsdWdpbkJhc2UgZnJvbSAnLi9QbHVnaW5CYXNlJztcblxuLyoqXG4gKiBTaW5nbGV0b24gcGx1Z2luIGFic3RyYWN0LlxuICpcbiAqIFRoaXMgaXMgYSBzcGVjaWFsIGRlZmluaXRpb24gY2xhc3MgdGhhdCB0aGUgU25vd2JvYXJkIGZyYW1ld29yayB3aWxsIHVzZSB0byBpbnRlcnByZXQgdGhlIGN1cnJlbnQgcGx1Z2luIGFzIGFcbiAqIFwic2luZ2xldG9uXCIuIFRoaXMgd2lsbCBlbnN1cmUgdGhhdCBvbmx5IG9uZSBpbnN0YW5jZSBvZiB0aGUgcGx1Z2luIGNsYXNzIGlzIHVzZWQgYWNyb3NzIHRoZSBib2FyZC5cbiAqXG4gKiBTaW5nbGV0b25zIGFyZSBpbml0aWFsaXNlZCBvbiB0aGUgXCJkb21SZWFkeVwiIGV2ZW50IGJ5IGRlZmF1bHQuXG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNpbmdsZXRvbiBleHRlbmRzIFBsdWdpbkJhc2Uge1xufVxuIiwiaW1wb3J0IFBsdWdpbkJhc2UgZnJvbSAnLi4vYWJzdHJhY3RzL1BsdWdpbkJhc2UnO1xuaW1wb3J0IFNpbmdsZXRvbiBmcm9tICcuLi9hYnN0cmFjdHMvU2luZ2xldG9uJztcblxuLyoqXG4gKiBQbHVnaW4gbG9hZGVyIGNsYXNzLlxuICpcbiAqIFRoaXMgaXMgYSBwcm92aWRlciAoZmFjdG9yeSkgY2xhc3MgZm9yIGEgc2luZ2xlIHBsdWdpbiBhbmQgcHJvdmlkZXMgdGhlIGxpbmsgYmV0d2VlbiBTbm93Ym9hcmQgZnJhbWV3b3JrIGZ1bmN0aW9uYWxpdHlcbiAqIGFuZCB0aGUgdW5kZXJseWluZyBwbHVnaW4gaW5zdGFuY2VzLiBJdCBhbHNvIHByb3ZpZGVzIHNvbWUgYmFzaWMgbW9ja2luZyBvZiBwbHVnaW4gbWV0aG9kcyBmb3IgdGVzdGluZy5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGx1Z2luTG9hZGVyIHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIEJpbmRzIHRoZSBXaW50ZXIgZnJhbWV3b3JrIHRvIHRoZSBpbnN0YW5jZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmR9IHNub3dib2FyZFxuICAgICAqIEBwYXJhbSB7UGx1Z2luQmFzZX0gaW5zdGFuY2VcbiAgICAgKi9cbiAgICBjb25zdHJ1Y3RvcihuYW1lLCBzbm93Ym9hcmQsIGluc3RhbmNlKSB7XG4gICAgICAgIHRoaXMubmFtZSA9IG5hbWU7XG4gICAgICAgIHRoaXMuc25vd2JvYXJkID0gc25vd2JvYXJkO1xuICAgICAgICB0aGlzLmluc3RhbmNlID0gaW5zdGFuY2U7XG4gICAgICAgIHRoaXMuaW5zdGFuY2VzID0gW107XG4gICAgICAgIHRoaXMuc2luZ2xldG9uID0gaW5zdGFuY2UucHJvdG90eXBlIGluc3RhbmNlb2YgU2luZ2xldG9uO1xuICAgICAgICB0aGlzLm1vY2tzID0ge307XG4gICAgICAgIHRoaXMub3JpZ2luYWxGdW5jdGlvbnMgPSB7fTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXRlcm1pbmVzIGlmIHRoZSBjdXJyZW50IHBsdWdpbiBoYXMgYSBzcGVjaWZpYyBtZXRob2QgYXZhaWxhYmxlLlxuICAgICAqXG4gICAgICogUmV0dXJucyBmYWxzZSBpZiB0aGUgY3VycmVudCBwbHVnaW4gaXMgYSBjYWxsYmFjayBmdW5jdGlvbi5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtZXRob2ROYW1lXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAgICovXG4gICAgaGFzTWV0aG9kKG1ldGhvZE5hbWUpIHtcbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gKHR5cGVvZiB0aGlzLmluc3RhbmNlLnByb3RvdHlwZVttZXRob2ROYW1lXSA9PT0gJ2Z1bmN0aW9uJyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ2FsbHMgYSBwcm90b3R5cGUgbWV0aG9kIGZvciBhIHBsdWdpbi4gVGhpcyBzaG91bGQgZ2VuZXJhbGx5IGJlIHVzZWQgZm9yIFwic3RhdGljXCIgY2FsbHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbWV0aG9kTmFtZVxuICAgICAqIEBwYXJhbSB7Li4ufSBhcmdzXG4gICAgICogQHJldHVybnMge2FueX1cbiAgICAgKi9cbiAgICBjYWxsTWV0aG9kKC4uLnBhcmFtZXRlcnMpIHtcbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGFyZ3MgPSBwYXJhbWV0ZXJzO1xuICAgICAgICBjb25zdCBtZXRob2ROYW1lID0gYXJncy5zaGlmdCgpO1xuXG4gICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlLnByb3RvdHlwZVttZXRob2ROYW1lXShhcmdzKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGFuIGluc3RhbmNlIG9mIHRoZSBjdXJyZW50IHBsdWdpbi5cbiAgICAgKlxuICAgICAqIC0gSWYgdGhpcyBpcyBhIGNhbGxiYWNrIGZ1bmN0aW9uIHBsdWdpbiwgdGhlIGZ1bmN0aW9uIHdpbGwgYmUgcmV0dXJuZWQuXG4gICAgICogLSBJZiB0aGlzIGlzIGEgc2luZ2xldG9uLCB0aGUgc2luZ2xlIGluc3RhbmNlIG9mIHRoZSBwbHVnaW4gd2lsbCBiZSByZXR1cm5lZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtQbHVnaW5CYXNlfEZ1bmN0aW9ufVxuICAgICAqL1xuICAgIGdldEluc3RhbmNlKC4uLnBhcmFtZXRlcnMpIHtcbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5pbnN0YW5jZSguLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRoaXMuZGVwZW5kZW5jaWVzRnVsZmlsbGVkKCkpIHtcbiAgICAgICAgICAgIGNvbnN0IHVubWV0ID0gdGhpcy5nZXREZXBlbmRlbmNpZXMoKS5maWx0ZXIoKGl0ZW0pID0+ICF0aGlzLnNub3dib2FyZC5nZXRQbHVnaW5OYW1lcygpLmluY2x1ZGVzKGl0ZW0pKTtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgVGhlIFwiJHt0aGlzLm5hbWV9XCIgcGx1Z2luIHJlcXVpcmVzIHRoZSBmb2xsb3dpbmcgcGx1Z2luczogJHt1bm1ldC5qb2luKCcsICcpfWApO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0aGlzLmlzU2luZ2xldG9uKCkpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLmluc3RhbmNlcy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICB0aGlzLmluaXRpYWxpc2VTaW5nbGV0b24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gQXBwbHkgbW9ja2VkIG1ldGhvZHNcbiAgICAgICAgICAgIGlmIChPYmplY3Qua2V5cyh0aGlzLm1vY2tzKS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgT2JqZWN0LmVudHJpZXModGhpcy5vcmlnaW5hbEZ1bmN0aW9ucykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgW21ldGhvZE5hbWUsIGNhbGxiYWNrXSA9IGVudHJ5O1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmluc3RhbmNlc1swXVttZXRob2ROYW1lXSA9IGNhbGxiYWNrO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIE9iamVjdC5lbnRyaWVzKHRoaXMubW9ja3MpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IFttZXRob2ROYW1lLCBjYWxsYmFja10gPSBlbnRyeTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5pbnN0YW5jZXNbMF1bbWV0aG9kTmFtZV0gPSAoLi4ucGFyYW1zKSA9PiBjYWxsYmFjayh0aGlzLCAuLi5wYXJhbXMpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gdGhpcy5pbnN0YW5jZXNbMF07XG4gICAgICAgIH1cblxuICAgICAgICAvLyBBcHBseSBtb2NrZWQgbWV0aG9kcyB0byBwcm90b3R5cGVcbiAgICAgICAgaWYgKE9iamVjdC5rZXlzKHRoaXMubW9ja3MpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIE9iamVjdC5lbnRyaWVzKHRoaXMub3JpZ2luYWxGdW5jdGlvbnMpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgW21ldGhvZE5hbWUsIGNhbGxiYWNrXSA9IGVudHJ5O1xuICAgICAgICAgICAgICAgIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdID0gY2FsbGJhY2s7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIE9iamVjdC5lbnRyaWVzKHRoaXMubW9ja3MpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgW21ldGhvZE5hbWUsIGNhbGxiYWNrXSA9IGVudHJ5O1xuICAgICAgICAgICAgICAgIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdID0gKC4uLnBhcmFtcykgPT4gY2FsbGJhY2sodGhpcywgLi4ucGFyYW1zKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgbmV3SW5zdGFuY2UgPSBuZXcgdGhpcy5pbnN0YW5jZSh0aGlzLnNub3dib2FyZCwgLi4ucGFyYW1ldGVycyk7XG4gICAgICAgIG5ld0luc3RhbmNlLmRldGFjaCA9ICgpID0+IHRoaXMuaW5zdGFuY2VzLnNwbGljZSh0aGlzLmluc3RhbmNlcy5pbmRleE9mKG5ld0luc3RhbmNlKSwgMSk7XG5cbiAgICAgICAgdGhpcy5pbnN0YW5jZXMucHVzaChuZXdJbnN0YW5jZSk7XG4gICAgICAgIHJldHVybiBuZXdJbnN0YW5jZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIGFsbCBpbnN0YW5jZXMgb2YgdGhlIGN1cnJlbnQgcGx1Z2luLlxuICAgICAqXG4gICAgICogSWYgdGhpcyBwbHVnaW4gaXMgYSBjYWxsYmFjayBmdW5jdGlvbiBwbHVnaW4sIGFuIGVtcHR5IGFycmF5IHdpbGwgYmUgcmV0dXJuZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7UGx1Z2luQmFzZVtdfVxuICAgICAqL1xuICAgIGdldEluc3RhbmNlcygpIHtcbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5pbnN0YW5jZXM7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGV0ZXJtaW5lcyBpZiB0aGUgY3VycmVudCBwbHVnaW4gaXMgYSBzaW1wbGUgY2FsbGJhY2sgZnVuY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICBpc0Z1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKHR5cGVvZiB0aGlzLmluc3RhbmNlID09PSAnZnVuY3Rpb24nICYmIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlIGluc3RhbmNlb2YgUGx1Z2luQmFzZSA9PT0gZmFsc2UpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgdGhlIGN1cnJlbnQgcGx1Z2luIGlzIGEgc2luZ2xldG9uLlxuICAgICAqXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAgICovXG4gICAgaXNTaW5nbGV0b24oKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlLnByb3RvdHlwZSBpbnN0YW5jZW9mIFNpbmdsZXRvbiA9PT0gdHJ1ZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXNlcyB0aGUgc2luZ2xldG9uIGluc3RhbmNlLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgaW5pdGlhbGlzZVNpbmdsZXRvbigpIHtcbiAgICAgICAgaWYgKCF0aGlzLmlzU2luZ2xldG9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IG5ld0luc3RhbmNlID0gbmV3IHRoaXMuaW5zdGFuY2UodGhpcy5zbm93Ym9hcmQpO1xuICAgICAgICBuZXdJbnN0YW5jZS5kZXRhY2ggPSAoKSA9PiB0aGlzLmluc3RhbmNlcy5zcGxpY2UodGhpcy5pbnN0YW5jZXMuaW5kZXhPZihuZXdJbnN0YW5jZSksIDEpO1xuICAgICAgICB0aGlzLmluc3RhbmNlcy5wdXNoKG5ld0luc3RhbmNlKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXRzIHRoZSBkZXBlbmRlbmNpZXMgb2YgdGhlIGN1cnJlbnQgcGx1Z2luLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3N0cmluZ1tdfVxuICAgICAqL1xuICAgIGdldERlcGVuZGVuY2llcygpIHtcbiAgICAgICAgLy8gQ2FsbGJhY2sgZnVuY3Rpb25zIGNhbm5vdCBoYXZlIGRlcGVuZGVuY2llcy5cbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cblxuICAgICAgICAvLyBObyBkZXBlbmRlbmN5IG1ldGhvZCBzcGVjaWZpZWQuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5pbnN0YW5jZS5wcm90b3R5cGUuZGVwZW5kZW5jaWVzICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5pbnN0YW5jZS5wcm90b3R5cGUuZGVwZW5kZW5jaWVzKCkubWFwKChpdGVtKSA9PiBpdGVtLnRvTG93ZXJDYXNlKCkpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgdGhlIGN1cnJlbnQgcGx1Z2luIGhhcyBhbGwgaXRzIGRlcGVuZGVuY2llcyBmdWxmaWxsZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICBkZXBlbmRlbmNpZXNGdWxmaWxsZWQoKSB7XG4gICAgICAgIGNvbnN0IGRlcGVuZGVuY2llcyA9IHRoaXMuZ2V0RGVwZW5kZW5jaWVzKCk7XG5cbiAgICAgICAgbGV0IGZ1bGZpbGxlZCA9IHRydWU7XG4gICAgICAgIGRlcGVuZGVuY2llcy5mb3JFYWNoKChwbHVnaW4pID0+IHtcbiAgICAgICAgICAgIGlmICghdGhpcy5zbm93Ym9hcmQuaGFzUGx1Z2luKHBsdWdpbikpIHtcbiAgICAgICAgICAgICAgICBmdWxmaWxsZWQgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIGZ1bGZpbGxlZDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBbGxvd3MgYSBtZXRob2Qgb2YgYW4gaW5zdGFuY2UgdG8gYmUgbW9ja2VkIGZvciB0ZXN0aW5nLlxuICAgICAqXG4gICAgICogVGhpcyBtb2NrIHdpbGwgYmUgYXBwbGllZCBmb3IgdGhlIGxpZmUgb2YgYW4gaW5zdGFuY2UuIEZvciBzaW5nbGV0b25zLCB0aGUgbW9jayB3aWxsIGJlIGFwcGxpZWQgZm9yIHRoZSBsaWZlXG4gICAgICogb2YgdGhlIHBhZ2UuXG4gICAgICpcbiAgICAgKiBNb2NrcyBjYW5ub3QgYmUgYXBwbGllZCB0byBjYWxsYmFjayBmdW5jdGlvbiBwbHVnaW5zLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG1ldGhvZE5hbWVcbiAgICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqL1xuICAgIG1vY2sobWV0aG9kTmFtZSwgY2FsbGJhY2spIHtcbiAgICAgICAgaWYgKHRoaXMuaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIXRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEZ1bmN0aW9uIFwiJHttZXRob2ROYW1lfVwiIGRvZXMgbm90IGV4aXN0IGFuZCBjYW5ub3QgYmUgbW9ja2VkYCk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm1vY2tzW21ldGhvZE5hbWVdID0gY2FsbGJhY2s7XG4gICAgICAgIHRoaXMub3JpZ2luYWxGdW5jdGlvbnNbbWV0aG9kTmFtZV0gPSB0aGlzLmluc3RhbmNlLnByb3RvdHlwZVttZXRob2ROYW1lXTtcblxuICAgICAgICBpZiAodGhpcy5pc1NpbmdsZXRvbigpICYmIHRoaXMuaW5zdGFuY2VzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgdGhpcy5pbml0aWFsaXNlU2luZ2xldG9uKCk7XG5cbiAgICAgICAgICAgIC8vIEFwcGx5IG1vY2tlZCBtZXRob2RcbiAgICAgICAgICAgIHRoaXMuaW5zdGFuY2VzWzBdW21ldGhvZE5hbWVdID0gKC4uLnBhcmFtZXRlcnMpID0+IGNhbGxiYWNrKHRoaXMsIC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBhIG1vY2sgY2FsbGJhY2sgZnJvbSBmdXR1cmUgaW5zdGFuY2VzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG1ldGhvZE5hbWVcbiAgICAgKi9cbiAgICB1bm1vY2sobWV0aG9kTmFtZSkge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRoaXMubW9ja3NbbWV0aG9kTmFtZV0pIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLmlzU2luZ2xldG9uKCkpIHtcbiAgICAgICAgICAgIHRoaXMuaW5zdGFuY2VzWzBdW21ldGhvZE5hbWVdID0gdGhpcy5vcmlnaW5hbEZ1bmN0aW9uc1ttZXRob2ROYW1lXTtcbiAgICAgICAgfVxuXG4gICAgICAgIGRlbGV0ZSB0aGlzLm1vY2tzW21ldGhvZE5hbWVdO1xuICAgICAgICBkZWxldGUgdGhpcy5vcmlnaW5hbEZ1bmN0aW9uc1ttZXRob2ROYW1lXTtcbiAgICB9XG59XG4iLCJpbXBvcnQgUGx1Z2luQmFzZSBmcm9tICcuLi9hYnN0cmFjdHMvUGx1Z2luQmFzZSc7XG5pbXBvcnQgU2luZ2xldG9uIGZyb20gJy4uL2Fic3RyYWN0cy9TaW5nbGV0b24nO1xuaW1wb3J0IFBsdWdpbkxvYWRlciBmcm9tICcuL1BsdWdpbkxvYWRlcic7XG5cbmltcG9ydCBDb29raWUgZnJvbSAnLi4vdXRpbGl0aWVzL0Nvb2tpZSc7XG5pbXBvcnQgSnNvblBhcnNlciBmcm9tICcuLi91dGlsaXRpZXMvSnNvblBhcnNlcic7XG5pbXBvcnQgU2FuaXRpemVyIGZyb20gJy4uL3V0aWxpdGllcy9TYW5pdGl6ZXInO1xuXG4vKipcbiAqIFNub3dib2FyZCAtIHRoZSBXaW50ZXIgSmF2YVNjcmlwdCBmcmFtZXdvcmsuXG4gKlxuICogVGhpcyBjbGFzcyByZXByZXNlbnRzIHRoZSBiYXNlIG9mIGEgbW9kZXJuIHRha2Ugb24gdGhlIFdpbnRlciBKUyBmcmFtZXdvcmssIGJlaW5nIGZ1bGx5IGV4dGVuc2libGUgYW5kIHRha2luZyBhZHZhbnRhZ2VcbiAqIG9mIG1vZGVybiBKYXZhU2NyaXB0IGZlYXR1cmVzIGJ5IGxldmVyYWdpbmcgdGhlIExhcmF2ZWwgTWl4IGNvbXBpbGF0aW9uIGZyYW1ld29yay4gSXQgYWxzbyBpcyBjb2RlZCB1cCB0byByZW1vdmUgdGhlXG4gKiBkZXBlbmRlbmN5IG9mIGpRdWVyeS5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqIEBsaW5rIGh0dHBzOi8vd2ludGVyY21zLmNvbS9kb2NzL3Nub3dib2FyZC9pbnRyb2R1Y3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU25vd2JvYXJkIHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gYXV0b1NpbmdsZXRvbnMgQXV0b21hdGljYWxseSBsb2FkIHNpbmdsZXRvbnMgd2hlbiBET00gaXMgcmVhZHkuIERlZmF1bHQ6IGB0cnVlYC5cbiAgICAgKiBAcGFyYW0ge2Jvb2xlYW59IGRlYnVnIFdoZXRoZXIgZGVidWdnaW5nIGxvZ3Mgc2hvdWxkIGJlIHNob3duLiBEZWZhdWx0OiBgZmFsc2VgLlxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKGF1dG9TaW5nbGV0b25zLCBkZWJ1Zykge1xuICAgICAgICB0aGlzLmRlYnVnRW5hYmxlZCA9ICh0eXBlb2YgZGVidWcgPT09ICdib29sZWFuJyAmJiBkZWJ1ZyA9PT0gdHJ1ZSk7XG4gICAgICAgIHRoaXMuYXV0b0luaXRTaW5nbGV0b25zID0gKHR5cGVvZiBhdXRvU2luZ2xldG9ucyA9PT0gJ2Jvb2xlYW4nICYmIGF1dG9TaW5nbGV0b25zID09PSBmYWxzZSk7XG4gICAgICAgIHRoaXMucGx1Z2lucyA9IHt9O1xuXG4gICAgICAgIHRoaXMuYXR0YWNoQWJzdHJhY3RzKCk7XG4gICAgICAgIHRoaXMubG9hZFV0aWxpdGllcygpO1xuICAgICAgICB0aGlzLmluaXRpYWxpc2UoKTtcblxuICAgICAgICB0aGlzLmRlYnVnKCdTbm93Ym9hcmQgZnJhbWV3b3JrIGluaXRpYWxpc2VkJyk7XG4gICAgfVxuXG4gICAgYXR0YWNoQWJzdHJhY3RzKCkge1xuICAgICAgICB0aGlzLlBsdWdpbkJhc2UgPSBQbHVnaW5CYXNlO1xuICAgICAgICB0aGlzLlNpbmdsZXRvbiA9IFNpbmdsZXRvbjtcbiAgICB9XG5cbiAgICBsb2FkVXRpbGl0aWVzKCkge1xuICAgICAgICB0aGlzLmFkZFBsdWdpbignY29va2llJywgQ29va2llKTtcbiAgICAgICAgdGhpcy5hZGRQbHVnaW4oJ2pzb25QYXJzZXInLCBKc29uUGFyc2VyKTtcbiAgICAgICAgdGhpcy5hZGRQbHVnaW4oJ3Nhbml0aXplcicsIFNhbml0aXplcik7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGlzZXMgdGhlIGZyYW1ld29yay5cbiAgICAgKlxuICAgICAqIEF0dGFjaGVzIGEgbGlzdGVuZXIgZm9yIHRoZSBET00gYmVpbmcgcmVhZHkgYW5kIHRyaWdnZXJzIGEgZ2xvYmFsIFwicmVhZHlcIiBldmVudCBmb3IgcGx1Z2lucyB0byBiZWdpbiBhdHRhY2hpbmdcbiAgICAgKiB0aGVtc2VsdmVzIHRvIHRoZSBET00uXG4gICAgICovXG4gICAgaW5pdGlhbGlzZSgpIHtcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCAoKSA9PiB7XG4gICAgICAgICAgICBpZiAodGhpcy5hdXRvSW5pdFNpbmdsZXRvbnMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmluaXRpYWxpc2VTaW5nbGV0b25zKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLmdsb2JhbEV2ZW50KCdyZWFkeScpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXNlcyBhbiBpbnN0YW5jZSBvZiBldmVyeSBzaW5nbGV0b24uXG4gICAgICovXG4gICAgaW5pdGlhbGlzZVNpbmdsZXRvbnMoKSB7XG4gICAgICAgIE9iamVjdC52YWx1ZXModGhpcy5wbHVnaW5zKS5mb3JFYWNoKChwbHVnaW4pID0+IHtcbiAgICAgICAgICAgIGlmIChwbHVnaW4uaXNTaW5nbGV0b24oKSkge1xuICAgICAgICAgICAgICAgIHBsdWdpbi5pbml0aWFsaXNlU2luZ2xldG9uKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFkZHMgYSBwbHVnaW4gdG8gdGhlIGZyYW1ld29yay5cbiAgICAgKlxuICAgICAqIFBsdWdpbnMgYXJlIHRoZSBjb3JuZXJzdG9uZSBmb3IgYWRkaXRpb25hbCBmdW5jdGlvbmFsaXR5IGZvciBTbm93Ym9hcmQuIEEgcGx1Z2luIG11c3QgZWl0aGVyIGJlIGFuIEVTMjAxNSBjbGFzc1xuICAgICAqIHRoYXQgZXh0ZW5kcyB0aGUgUGx1Z2luQmFzZSBvciBTaW5nbGV0b24gYWJzdHJhY3QgY2xhc3Nlcywgb3IgYSBzaW1wbGUgY2FsbGJhY2sgZnVuY3Rpb24uXG4gICAgICpcbiAgICAgKiBXaGVuIGEgcGx1Z2luIGlzIGFkZGVkLCBpdCBpcyBhdXRvbWF0aWNhbGx5IGFzc2lnbmVkIGFzIGEgbmV3IG1hZ2ljIG1ldGhvZCBpbiB0aGUgU25vd2JvYXJkIGNsYXNzIHVzaW5nIHRoZSBuYW1lXG4gICAgICogcGFyYW1ldGVyLCBhbmQgY2FuIGJlIGNhbGxlZCB2aWEgdGhpcyBtZXRob2QuIFRoaXMgbWV0aG9kIHdpbGwgYWx3YXlzIGJlIHRoZSBcImxvd2VyY2FzZVwiIHZlcnNpb24gb2YgdGhpcyBuYW1lLlxuICAgICAqXG4gICAgICogRm9yIGV4YW1wbGUsIGlmIGEgcGx1Z2luIGlzIGFzc2lnbmVkIHRvIHRoZSBuYW1lIFwibXlQbHVnaW5cIiwgaXQgY2FuIGJlIGNhbGxlZCB2aWEgYFNub3dib2FyZC5teXBsdWdpbigpYC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lXG4gICAgICogQHBhcmFtIHtQbHVnaW5CYXNlfEZ1bmN0aW9ufSBpbnN0YW5jZVxuICAgICAqL1xuICAgIGFkZFBsdWdpbihuYW1lLCBpbnN0YW5jZSkge1xuICAgICAgICBjb25zdCBsb3dlck5hbWUgPSBuYW1lLnRvTG93ZXJDYXNlKCk7XG5cbiAgICAgICAgaWYgKHRoaXMuaGFzUGx1Z2luKGxvd2VyTmFtZSkpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQSBwbHVnaW4gY2FsbGVkIFwiJHtuYW1lfVwiIGlzIGFscmVhZHkgcmVnaXN0ZXJlZC5gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0eXBlb2YgaW5zdGFuY2UgIT09ICdmdW5jdGlvbicgJiYgaW5zdGFuY2UgaW5zdGFuY2VvZiBQbHVnaW5CYXNlID09PSBmYWxzZSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdUaGUgcHJvdmlkZWQgcGx1Z2luIG11c3QgZXh0ZW5kIHRoZSBQbHVnaW5CYXNlIGNsYXNzLCBvciBtdXN0IGJlIGEgY2FsbGJhY2sgZnVuY3Rpb24uJyk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpc1tuYW1lXSAhPT0gdW5kZWZpbmVkIHx8IHRoaXNbbG93ZXJOYW1lXSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ1RoZSBnaXZlbiBuYW1lIGlzIGFscmVhZHkgaW4gdXNlIGZvciBhIHByb3BlcnR5IG9yIG1ldGhvZCBvZiB0aGUgU25vd2JvYXJkIGNsYXNzLicpO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5wbHVnaW5zW2xvd2VyTmFtZV0gPSBuZXcgUGx1Z2luTG9hZGVyKGxvd2VyTmFtZSwgdGhpcywgaW5zdGFuY2UpO1xuICAgICAgICBjb25zdCBjYWxsYmFjayA9ICguLi5wYXJhbWV0ZXJzKSA9PiB0aGlzLnBsdWdpbnNbbG93ZXJOYW1lXS5nZXRJbnN0YW5jZSguLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgdGhpc1tuYW1lXSA9IGNhbGxiYWNrO1xuICAgICAgICB0aGlzW2xvd2VyTmFtZV0gPSBjYWxsYmFjaztcblxuICAgICAgICB0aGlzLmRlYnVnKGBQbHVnaW4gXCIke25hbWV9XCIgcmVnaXN0ZXJlZGApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlbW92ZXMgYSBwbHVnaW4uXG4gICAgICpcbiAgICAgKiBSZW1vdmVzIGEgcGx1Z2luIGZyb20gU25vd2JvYXJkLCBjYWxsaW5nIHRoZSBkZXN0cnVjdG9yIG1ldGhvZCBmb3IgYWxsIGFjdGl2ZSBpbnN0YW5jZXMgb2YgdGhlIHBsdWdpbi5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgcmVtb3ZlUGx1Z2luKG5hbWUpIHtcbiAgICAgICAgY29uc3QgbG93ZXJOYW1lID0gbmFtZS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgIGlmICghdGhpcy5oYXNQbHVnaW4obG93ZXJOYW1lKSkge1xuICAgICAgICAgICAgdGhpcy5kZWJ1ZyhgUGx1Z2luIFwiJHtuYW1lfVwiIGFscmVhZHkgcmVtb3ZlZGApO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2FsbCBkZXN0cnVjdG9ycyBmb3IgYWxsIGluc3RhbmNlc1xuICAgICAgICB0aGlzLnBsdWdpbnNbbG93ZXJOYW1lXS5nZXRJbnN0YW5jZXMoKS5mb3JFYWNoKChpbnN0YW5jZSkgPT4ge1xuICAgICAgICAgICAgaW5zdGFuY2UuZGVzdHJ1Y3RvcigpO1xuICAgICAgICB9KTtcblxuICAgICAgICBkZWxldGUgdGhpcy5wbHVnaW5zW2xvd2VyTmFtZV07XG4gICAgICAgIGRlbGV0ZSB0aGlzW2xvd2VyTmFtZV07XG5cbiAgICAgICAgdGhpcy5kZWJ1ZyhgUGx1Z2luIFwiJHtuYW1lfVwiIHJlbW92ZWRgKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXRlcm1pbmVzIGlmIGEgcGx1Z2luIGhhcyBiZWVuIHJlZ2lzdGVyZWQgYW5kIGlzIGFjdGl2ZS5cbiAgICAgKlxuICAgICAqIEEgcGx1Z2luIHRoYXQgaXMgc3RpbGwgd2FpdGluZyBmb3IgZGVwZW5kZW5jaWVzIHRvIGJlIHJlZ2lzdGVyZWQgd2lsbCBub3QgYmUgYWN0aXZlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG5hbWVcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICBoYXNQbHVnaW4obmFtZSkge1xuICAgICAgICBjb25zdCBsb3dlck5hbWUgPSBuYW1lLnRvTG93ZXJDYXNlKCk7XG5cbiAgICAgICAgcmV0dXJuICh0aGlzLnBsdWdpbnNbbG93ZXJOYW1lXSAhPT0gdW5kZWZpbmVkKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGFuIGFycmF5IG9mIHJlZ2lzdGVyZWQgcGx1Z2lucyBhcyBQbHVnaW5Mb2FkZXIgb2JqZWN0cy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtQbHVnaW5Mb2FkZXJbXX1cbiAgICAgKi9cbiAgICBnZXRQbHVnaW5zKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5wbHVnaW5zO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYW4gYXJyYXkgb2YgcmVnaXN0ZXJlZCBwbHVnaW5zLCBieSBuYW1lLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3N0cmluZ1tdfVxuICAgICAqL1xuICAgIGdldFBsdWdpbk5hbWVzKCkge1xuICAgICAgICByZXR1cm4gT2JqZWN0LmtleXModGhpcy5wbHVnaW5zKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGEgUGx1Z2luTG9hZGVyIG9iamVjdCBvZiBhIGdpdmVuIHBsdWdpbi5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtQbHVnaW5Mb2FkZXJ9XG4gICAgICovXG4gICAgZ2V0UGx1Z2luKG5hbWUpIHtcbiAgICAgICAgaWYgKCF0aGlzLmhhc1BsdWdpbihuYW1lKSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBObyBwbHVnaW4gY2FsbGVkIFwiJHtuYW1lfVwiIGhhcyBiZWVuIHJlZ2lzdGVyZWQuYCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5wbHVnaW5zW25hbWVdO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZpbmRzIGFsbCBwbHVnaW5zIHRoYXQgbGlzdGVuIHRvIHRoZSBnaXZlbiBldmVudC5cbiAgICAgKlxuICAgICAqIFRoaXMgd29ya3MgZm9yIGJvdGggbm9ybWFsIGFuZCBwcm9taXNlIGV2ZW50cy4gSXQgZG9lcyBOT1QgY2hlY2sgdGhhdCB0aGUgcGx1Z2luJ3MgbGlzdGVuZXIgYWN0dWFsbHkgZXhpc3RzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGV2ZW50TmFtZVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmdbXX0gVGhlIG5hbWUgb2YgdGhlIHBsdWdpbnMgdGhhdCBhcmUgbGlzdGVuaW5nIHRvIHRoaXMgZXZlbnQuXG4gICAgICovXG4gICAgbGlzdGVuc1RvRXZlbnQoZXZlbnROYW1lKSB7XG4gICAgICAgIGNvbnN0IHBsdWdpbnMgPSBbXTtcblxuICAgICAgICBPYmplY3QuZW50cmllcyh0aGlzLnBsdWdpbnMpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICBjb25zdCBbbmFtZSwgcGx1Z2luXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICBpZiAocGx1Z2luLmlzRnVuY3Rpb24oKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFwbHVnaW4uaGFzTWV0aG9kKCdsaXN0ZW5zJykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGxpc3RlbmVycyA9IHBsdWdpbi5jYWxsTWV0aG9kKCdsaXN0ZW5zJyk7XG5cbiAgICAgICAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXJzW2V2ZW50TmFtZV0gPT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgcGx1Z2lucy5wdXNoKG5hbWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gcGx1Z2lucztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDYWxscyBhIGdsb2JhbCBldmVudCB0byBhbGwgcmVnaXN0ZXJlZCBwbHVnaW5zLlxuICAgICAqXG4gICAgICogSWYgYW55IHBsdWdpbiByZXR1cm5zIGEgYGZhbHNlYCwgdGhlIGV2ZW50IGlzIGNvbnNpZGVyZWQgY2FuY2VsbGVkLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGV2ZW50TmFtZVxuICAgICAqIEByZXR1cm5zIHtib29sZWFufSBJZiBldmVudCB3YXMgbm90IGNhbmNlbGxlZFxuICAgICAqL1xuICAgIGdsb2JhbEV2ZW50KGV2ZW50TmFtZSwgLi4ucGFyYW1ldGVycykge1xuICAgICAgICB0aGlzLmRlYnVnKGBDYWxsaW5nIGdsb2JhbCBldmVudCBcIiR7ZXZlbnROYW1lfVwiYCk7XG5cbiAgICAgICAgLy8gRmluZCBvdXQgd2hpY2ggcGx1Z2lucyBsaXN0ZW4gdG8gdGhpcyBldmVudCAtIGlmIG5vbmUgbGlzdGVuIHRvIGl0LCByZXR1cm4gdHJ1ZS5cbiAgICAgICAgY29uc3QgbGlzdGVuZXJzID0gdGhpcy5saXN0ZW5zVG9FdmVudChldmVudE5hbWUpO1xuICAgICAgICBpZiAobGlzdGVuZXJzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgdGhpcy5kZWJ1ZyhgTm8gbGlzdGVuZXJzIGZvdW5kIGZvciBnbG9iYWwgZXZlbnQgXCIke2V2ZW50TmFtZX1cImApO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5kZWJ1ZyhgTGlzdGVuZXJzIGZvdW5kIGZvciBnbG9iYWwgZXZlbnQgXCIke2V2ZW50TmFtZX1cIjogJHtsaXN0ZW5lcnMuam9pbignLCAnKX1gKTtcblxuICAgICAgICBsZXQgY2FuY2VsbGVkID0gZmFsc2U7XG5cbiAgICAgICAgbGlzdGVuZXJzLmZvckVhY2goKG5hbWUpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IHBsdWdpbiA9IHRoaXMuZ2V0UGx1Z2luKG5hbWUpO1xuXG4gICAgICAgICAgICBpZiAocGx1Z2luLmlzRnVuY3Rpb24oKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChwbHVnaW4uaXNTaW5nbGV0b24oKSAmJiBwbHVnaW4uZ2V0SW5zdGFuY2VzKCkubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICAgICAgcGx1Z2luLmluaXRpYWxpc2VTaW5nbGV0b24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgbGlzdGVuTWV0aG9kID0gcGx1Z2luLmNhbGxNZXRob2QoJ2xpc3RlbnMnKVtldmVudE5hbWVdO1xuXG4gICAgICAgICAgICAvLyBDYWxsIGV2ZW50IGhhbmRsZXIgbWV0aG9kcyBmb3IgYWxsIHBsdWdpbnMsIGlmIHRoZXkgaGF2ZSBhIG1ldGhvZCBzcGVjaWZpZWQgZm9yIHRoZSBldmVudC5cbiAgICAgICAgICAgIHBsdWdpbi5nZXRJbnN0YW5jZXMoKS5mb3JFYWNoKChpbnN0YW5jZSkgPT4ge1xuICAgICAgICAgICAgICAgIC8vIElmIGEgcGx1Z2luIGhhcyBjYW5jZWxsZWQgdGhlIGV2ZW50LCBubyBmdXJ0aGVyIHBsdWdpbnMgYXJlIGNvbnNpZGVyZWQuXG4gICAgICAgICAgICAgICAgaWYgKGNhbmNlbGxlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCFpbnN0YW5jZVtsaXN0ZW5NZXRob2RdKSB7XG4gICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgTWlzc2luZyBcIiR7bGlzdGVuTWV0aG9kfVwiIG1ldGhvZCBpbiBcIiR7bmFtZX1cIiBwbHVnaW5gKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2VbbGlzdGVuTWV0aG9kXSguLi5wYXJhbWV0ZXJzKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgY2FuY2VsbGVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kZWJ1ZyhgR2xvYmFsIGV2ZW50IFwiJHtldmVudE5hbWV9XCIgY2FuY2VsbGVkIGJ5IFwiJHtuYW1lfVwiIHBsdWdpbmApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gIWNhbmNlbGxlZDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDYWxscyBhIGdsb2JhbCBldmVudCB0byBhbGwgcmVnaXN0ZXJlZCBwbHVnaW5zLCBleHBlY3RpbmcgYSBQcm9taXNlIHRvIGJlIHJldHVybmVkIGJ5IGFsbC5cbiAgICAgKlxuICAgICAqIFRoaXMgY29sbGF0ZXMgYWxsIHBsdWdpbnMgcmVzcG9uc2VzIGludG8gb25lIGxhcmdlIFByb21pc2UgdGhhdCBlaXRoZXIgZXhwZWN0cyBhbGwgdG8gYmUgcmVzb2x2ZWQsIG9yIG9uZSB0byByZWplY3QuXG4gICAgICogSWYgbm8gbGlzdGVuZXJzIGFyZSBmb3VuZCwgYSByZXNvbHZlZCBQcm9taXNlIGlzIHJldHVybmVkLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGV2ZW50TmFtZVxuICAgICAqL1xuICAgIGdsb2JhbFByb21pc2VFdmVudChldmVudE5hbWUsIC4uLnBhcmFtZXRlcnMpIHtcbiAgICAgICAgdGhpcy5kZWJ1ZyhgQ2FsbGluZyBnbG9iYWwgcHJvbWlzZSBldmVudCBcIiR7ZXZlbnROYW1lfVwiYCk7XG5cbiAgICAgICAgLy8gRmluZCBvdXQgd2hpY2ggcGx1Z2lucyBsaXN0ZW4gdG8gdGhpcyBldmVudCAtIGlmIG5vbmUgbGlzdGVuIHRvIGl0LCByZXR1cm4gYSByZXNvbHZlZCBwcm9taXNlLlxuICAgICAgICBjb25zdCBsaXN0ZW5lcnMgPSB0aGlzLmxpc3RlbnNUb0V2ZW50KGV2ZW50TmFtZSk7XG4gICAgICAgIGlmIChsaXN0ZW5lcnMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICB0aGlzLmRlYnVnKGBObyBsaXN0ZW5lcnMgZm91bmQgZm9yIGdsb2JhbCBwcm9taXNlIGV2ZW50IFwiJHtldmVudE5hbWV9XCJgKTtcbiAgICAgICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUoKTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmRlYnVnKGBMaXN0ZW5lcnMgZm91bmQgZm9yIGdsb2JhbCBwcm9taXNlIGV2ZW50IFwiJHtldmVudE5hbWV9XCI6ICR7bGlzdGVuZXJzLmpvaW4oJywgJyl9YCk7XG5cbiAgICAgICAgY29uc3QgcHJvbWlzZXMgPSBbXTtcblxuICAgICAgICBsaXN0ZW5lcnMuZm9yRWFjaCgobmFtZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgcGx1Z2luID0gdGhpcy5nZXRQbHVnaW4obmFtZSk7XG5cbiAgICAgICAgICAgIGlmIChwbHVnaW4uaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKHBsdWdpbi5pc1NpbmdsZXRvbigpICYmIHBsdWdpbi5nZXRJbnN0YW5jZXMoKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICBwbHVnaW4uaW5pdGlhbGlzZVNpbmdsZXRvbigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCBsaXN0ZW5NZXRob2QgPSBwbHVnaW4uY2FsbE1ldGhvZCgnbGlzdGVucycpW2V2ZW50TmFtZV07XG5cbiAgICAgICAgICAgIC8vIENhbGwgZXZlbnQgaGFuZGxlciBtZXRob2RzIGZvciBhbGwgcGx1Z2lucywgaWYgdGhleSBoYXZlIGEgbWV0aG9kIHNwZWNpZmllZCBmb3IgdGhlIGV2ZW50LlxuICAgICAgICAgICAgcGx1Z2luLmdldEluc3RhbmNlcygpLmZvckVhY2goKGluc3RhbmNlKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgaW5zdGFuY2VQcm9taXNlID0gaW5zdGFuY2VbbGlzdGVuTWV0aG9kXSguLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2VQcm9taXNlIGluc3RhbmNlb2YgUHJvbWlzZSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHByb21pc2VzLnB1c2goaW5zdGFuY2VQcm9taXNlKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAocHJvbWlzZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gUHJvbWlzZS5hbGwocHJvbWlzZXMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIExvZyBhIGRlYnVnIG1lc3NhZ2UuXG4gICAgICpcbiAgICAgKiBUaGVzZSBtZXNzYWdlcyBhcmUgb25seSBzaG93biB3aGVuIGRlYnVnZ2luZyBpcyBlbmFibGVkLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgZGVidWcoLi4ucGFyYW1ldGVycykge1xuICAgICAgICBpZiAoIXRoaXMuZGVidWdFbmFibGVkKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvKiBlc2xpbnQtZGlzYWJsZSAqL1xuICAgICAgICBjb25zb2xlLmdyb3VwQ29sbGFwc2VkKCclY1tTbm93Ym9hcmRdJywgJ2NvbG9yOiByZ2IoNDUsIDE2NywgMTk5KTsgZm9udC13ZWlnaHQ6IG5vcm1hbDsnLCAuLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgY29uc29sZS50cmFjZSgpO1xuICAgICAgICBjb25zb2xlLmdyb3VwRW5kKCk7XG4gICAgICAgIC8qIGVzbGludC1lbmFibGUgKi9cbiAgICB9XG59XG4iLCJpbXBvcnQgU25vd2JvYXJkIGZyb20gJy4vbWFpbi9Tbm93Ym9hcmQnO1xuXG4oKHdpbmRvdykgPT4ge1xuICAgIGNvbnN0IHNub3dib2FyZCA9IG5ldyBTbm93Ym9hcmQodHJ1ZSwgdHJ1ZSk7XG5cbiAgICAvLyBDb3ZlciBhbGwgYWxpYXNlc1xuICAgIHdpbmRvdy5zbm93Ym9hcmQgPSBzbm93Ym9hcmQ7XG4gICAgd2luZG93LlNub3dib2FyZCA9IHNub3dib2FyZDtcbiAgICB3aW5kb3cuU25vd0JvYXJkID0gc25vd2JvYXJkO1xufSkod2luZG93KTtcbiIsImltcG9ydCBCYXNlQ29va2llIGZyb20gJ2pzLWNvb2tpZSc7XG5pbXBvcnQgU2luZ2xldG9uIGZyb20gJy4uL2Fic3RyYWN0cy9TaW5nbGV0b24nO1xuXG4vKipcbiAqIENvb2tpZSB1dGlsaXR5LlxuICpcbiAqIFRoaXMgdXRpbGl0eSBpcyBhIHRoaW4gd3JhcHBlciBhcm91bmQgdGhlIFwianMtY29va2llXCIgbGlicmFyeS5cbiAqXG4gKiBAc2VlIGh0dHBzOi8vZ2l0aHViLmNvbS9qcy1jb29raWUvanMtY29va2llXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ29va2llIGV4dGVuZHMgU2luZ2xldG9uIHtcbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICB0aGlzLmRlZmF1bHRzID0ge1xuICAgICAgICAgICAgZXhwaXJlczogbnVsbCxcbiAgICAgICAgICAgIHBhdGg6ICcvJyxcbiAgICAgICAgICAgIGRvbWFpbjogbnVsbCxcbiAgICAgICAgICAgIHNlY3VyZTogZmFsc2UsXG4gICAgICAgICAgICBzYW1lU2l0ZTogJ0xheCcsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogU2V0IHRoZSBkZWZhdWx0IGNvb2tpZSBwYXJhbWV0ZXJzIGZvciBhbGwgc3Vic2VxdWVudCBcInNldFwiIGFuZCBcInJlbW92ZVwiIGNhbGxzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBzZXREZWZhdWx0cyhvcHRpb25zKSB7XG4gICAgICAgIGlmICh0eXBlb2Ygb3B0aW9ucyAhPT0gJ29iamVjdCcpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignQ29va2llIGRlZmF1bHRzIG11c3QgYmUgcHJvdmlkZWQgYXMgYW4gb2JqZWN0Jyk7XG4gICAgICAgIH1cblxuICAgICAgICBPYmplY3QuZW50cmllcyhvcHRpb25zKS5mb3JFYWNoKChlbnRyeSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgW2tleSwgdmFsdWVdID0gZW50cnk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLmRlZmF1bHRzW2tleV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIHRoaXMuZGVmYXVsdHNba2V5XSA9IHZhbHVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGN1cnJlbnQgZGVmYXVsdCBjb29raWUgcGFyYW1ldGVycy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgZ2V0RGVmYXVsdHMoKSB7XG4gICAgICAgIGNvbnN0IGRlZmF1bHRzID0ge307XG5cbiAgICAgICAgT2JqZWN0LmVudHJpZXModGhpcy5kZWZhdWx0cykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IFtrZXksIHZhbHVlXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICBpZiAodGhpcy5kZWZhdWx0c1trZXldICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgZGVmYXVsdHNba2V5XSA9IHZhbHVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZGVmYXVsdHM7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0IGEgY29va2llIGJ5IG5hbWUuXG4gICAgICpcbiAgICAgKiBJZiBgbmFtZWAgaXMgdW5kZWZpbmVkLCByZXR1cm5zIGFsbCBjb29raWVzIGFzIGFuIE9iamVjdC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAgICogQHJldHVybnMge09iamVjdHxTdHJpbmd9XG4gICAgICovXG4gICAgZ2V0KG5hbWUpIHtcbiAgICAgICAgaWYgKG5hbWUgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgY29uc3QgY29va2llcyA9IEJhc2VDb29raWUuZ2V0KCk7XG5cbiAgICAgICAgICAgIE9iamVjdC5lbnRyaWVzKGNvb2tpZXMpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgW2Nvb2tpZU5hbWUsIGNvb2tpZVZhbHVlXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2Nvb2tpZS5nZXQnLCBjb29raWVOYW1lLCBjb29raWVWYWx1ZSwgKG5ld1ZhbHVlKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGNvb2tpZXNbY29va2llTmFtZV0gPSBuZXdWYWx1ZTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gY29va2llcztcbiAgICAgICAgfVxuXG4gICAgICAgIGxldCB2YWx1ZSA9IEJhc2VDb29raWUuZ2V0KG5hbWUpO1xuXG4gICAgICAgIC8vIEFsbG93IHBsdWdpbnMgdG8gb3ZlcnJpZGUgdGhlIGdvdHRlbiB2YWx1ZVxuICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnY29va2llLmdldCcsIG5hbWUsIHZhbHVlLCAobmV3VmFsdWUpID0+IHtcbiAgICAgICAgICAgIHZhbHVlID0gbmV3VmFsdWU7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiB2YWx1ZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZXQgYSBjb29raWUgYnkgbmFtZS5cbiAgICAgKlxuICAgICAqIFlvdSBjYW4gc3BlY2lmeSBhZGRpdGlvbmFsIGNvb2tpZSBwYXJhbWV0ZXJzIHRocm91Z2ggdGhlIFwib3B0aW9uc1wiIHBhcmFtZXRlci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHZhbHVlXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICAgKiBAcmV0dXJucyB7U3RyaW5nfVxuICAgICAqL1xuICAgIHNldChuYW1lLCB2YWx1ZSwgb3B0aW9ucykge1xuICAgICAgICBsZXQgc2F2ZVZhbHVlID0gdmFsdWU7XG5cbiAgICAgICAgLy8gQWxsb3cgcGx1Z2lucyB0byBvdmVycmlkZSB0aGUgdmFsdWUgdG8gc2F2ZVxuICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnY29va2llLnNldCcsIG5hbWUsIHZhbHVlLCAobmV3VmFsdWUpID0+IHtcbiAgICAgICAgICAgIHNhdmVWYWx1ZSA9IG5ld1ZhbHVlO1xuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gQmFzZUNvb2tpZS5zZXQobmFtZSwgc2F2ZVZhbHVlLCB7XG4gICAgICAgICAgICAuLi50aGlzLmdldERlZmF1bHRzKCksXG4gICAgICAgICAgICAuLi5vcHRpb25zLFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW1vdmUgYSBjb29raWUgYnkgbmFtZS5cbiAgICAgKlxuICAgICAqIFlvdSBjYW4gc3BlY2lmeSB0aGUgYWRkaXRpb25hbCBjb29raWUgcGFyYW1ldGVycyB2aWEgdGhlIFwib3B0aW9uc1wiIHBhcmFtZXRlci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICByZW1vdmUobmFtZSwgb3B0aW9ucykge1xuICAgICAgICBCYXNlQ29va2llLnJlbW92ZShuYW1lLCB7XG4gICAgICAgICAgICAuLi50aGlzLmdldERlZmF1bHRzKCksXG4gICAgICAgICAgICAuLi5vcHRpb25zLFxuICAgICAgICB9KTtcbiAgICB9XG59XG4iLCJpbXBvcnQgU2luZ2xldG9uIGZyb20gJy4uL2Fic3RyYWN0cy9TaW5nbGV0b24nO1xuXG4vKipcbiAqIEpTT04gUGFyc2VyIHV0aWxpdHkuXG4gKlxuICogVGhpcyB1dGlsaXR5IHBhcnNlcyBKU09OLWxpa2UgZGF0YSB0aGF0IGRvZXMgbm90IHN0cmljdGx5IG1lZXQgdGhlIEpTT04gc3BlY2lmaWNhdGlvbnMgaW4gb3JkZXIgdG8gc2ltcGxpZnkgZGV2ZWxvcG1lbnQuXG4gKiBJdCBpcyBhIHNhZmUgcmVwbGFjZW1lbnQgZm9yIEpTT04ucGFyc2UoSlNPTi5zdHJpbmdpZnkoZXZhbChcIih7XCIgKyB2YWx1ZSArIFwifSlcIikpKSB0aGF0IGRvZXMgbm90IHJlcXVpcmUgdGhlIHVzZSBvZiBldmFsKClcbiAqXG4gKiBAYXV0aG9yIEF5dW1pIEhhbWFzYWtpXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vb2N0b2JlcmNtcy9vY3RvYmVyL3B1bGwvNDUyN1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBKc29uUGFyc2VyIGV4dGVuZHMgU2luZ2xldG9uIHtcbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICAvLyBBZGQgdG8gZ2xvYmFsIGZ1bmN0aW9uIGZvciBiYWNrd2FyZHMgY29tcGF0aWJpbGl0eVxuICAgICAgICB3aW5kb3cud25KU09OID0gKGpzb24pID0+IHRoaXMucGFyc2UoanNvbik7XG4gICAgICAgIHdpbmRvdy5vY0pTT04gPSB3aW5kb3cud25KU09OO1xuICAgIH1cblxuICAgIHBhcnNlKHN0cikge1xuICAgICAgICBjb25zdCBqc29uU3RyaW5nID0gdGhpcy5wYXJzZVN0cmluZyhzdHIpO1xuICAgICAgICByZXR1cm4gSlNPTi5wYXJzZShqc29uU3RyaW5nKTtcbiAgICB9XG5cbiAgICBwYXJzZVN0cmluZyh2YWx1ZSkge1xuICAgICAgICBsZXQgc3RyID0gdmFsdWUudHJpbSgpO1xuXG4gICAgICAgIGlmICghc3RyLmxlbmd0aCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdCcm9rZW4gSlNPTiBvYmplY3QuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgcmVzdWx0ID0gJyc7XG4gICAgICAgIGxldCB0eXBlID0gbnVsbDtcbiAgICAgICAgbGV0IGtleSA9IG51bGw7XG4gICAgICAgIGxldCBib2R5ID0gJyc7XG5cbiAgICAgICAgLypcbiAgICAgICAgKiB0aGUgbWlzdGFrZSAnLCdcbiAgICAgICAgKi9cbiAgICAgICAgd2hpbGUgKHN0ciAmJiBzdHJbMF0gPT09ICcsJykge1xuICAgICAgICAgICAgc3RyID0gc3RyLnN1YnN0cigxKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qXG4gICAgICAgICogc3RyaW5nXG4gICAgICAgICovXG4gICAgICAgIGlmIChzdHJbMF0gPT09ICdcIicgfHwgc3RyWzBdID09PSAnXFwnJykge1xuICAgICAgICAgICAgaWYgKHN0cltzdHIubGVuZ3RoIC0gMV0gIT09IHN0clswXSkge1xuICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignSW52YWxpZCBzdHJpbmcgSlNPTiBvYmplY3QuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGJvZHkgPSAnXCInO1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDE7IGkgPCBzdHIubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnXFxcXCcpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0cltpICsgMV0gPT09ICdcXCcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2kgKyAxXTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpICs9IDE7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChzdHJbaV0gPT09IHN0clswXSkge1xuICAgICAgICAgICAgICAgICAgICBib2R5ICs9ICdcIic7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBib2R5O1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSAnXCInKSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gJ1xcXFxcIic7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaV07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgc3RyaW5nIEpTT04gb2JqZWN0LicpO1xuICAgICAgICB9XG5cbiAgICAgICAgLypcbiAgICAgICAgKiBib29sZWFuXG4gICAgICAgICovXG4gICAgICAgIGlmIChzdHIgPT09ICd0cnVlJyB8fCBzdHIgPT09ICdmYWxzZScpIHtcbiAgICAgICAgICAgIHJldHVybiBzdHI7XG4gICAgICAgIH1cblxuICAgICAgICAvKlxuICAgICAgICAqIG51bGxcbiAgICAgICAgKi9cbiAgICAgICAgaWYgKHN0ciA9PT0gJ251bGwnKSB7XG4gICAgICAgICAgICByZXR1cm4gJ251bGwnO1xuICAgICAgICB9XG5cbiAgICAgICAgLypcbiAgICAgICAgKiBudW1iZXJcbiAgICAgICAgKi9cbiAgICAgICAgY29uc3QgbnVtID0gcGFyc2VGbG9hdChzdHIpO1xuICAgICAgICBpZiAoIU51bWJlci5pc05hTihudW0pKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVtLnRvU3RyaW5nKCk7XG4gICAgICAgIH1cblxuICAgICAgICAvKlxuICAgICAgICAqIG9iamVjdFxuICAgICAgICAqL1xuICAgICAgICBpZiAoc3RyWzBdID09PSAneycpIHtcbiAgICAgICAgICAgIHR5cGUgPSAnbmVlZEtleSc7XG4gICAgICAgICAgICBrZXkgPSBudWxsO1xuICAgICAgICAgICAgcmVzdWx0ID0gJ3snO1xuXG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMTsgaSA8IHN0ci5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLmlzQmxhbmtDaGFyKHN0cltpXSkpIHtcbiAgICAgICAgICAgICAgICAgICAgLyogZXNsaW50LWRpc2FibGUtbmV4dC1saW5lICovXG4gICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiAodHlwZSA9PT0gJ25lZWRLZXknICYmIChzdHJbaV0gPT09ICdcIicgfHwgc3RyW2ldID09PSAnXFwnJykpIHtcbiAgICAgICAgICAgICAgICAgICAga2V5ID0gdGhpcy5wYXJzZUtleShzdHIsIGkgKyAxLCBzdHJbaV0pO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gYFwiJHtrZXl9XCJgO1xuICAgICAgICAgICAgICAgICAgICBpICs9IGtleS5sZW5ndGg7XG4gICAgICAgICAgICAgICAgICAgIGkgKz0gMTtcbiAgICAgICAgICAgICAgICAgICAgdHlwZSA9ICdhZnRlcktleSc7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSAnbmVlZEtleScgJiYgdGhpcy5jYW5CZUtleUhlYWQoc3RyW2ldKSkge1xuICAgICAgICAgICAgICAgICAgICBrZXkgPSB0aGlzLnBhcnNlS2V5KHN0ciwgaSk7XG4gICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnXCInO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0ga2V5O1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgaSArPSBrZXkubGVuZ3RoIC0gMTtcbiAgICAgICAgICAgICAgICAgICAgdHlwZSA9ICdhZnRlcktleSc7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSAnYWZ0ZXJLZXknICYmIHN0cltpXSA9PT0gJzonKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnOic7XG4gICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnOic7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSAnOicpIHtcbiAgICAgICAgICAgICAgICAgICAgYm9keSA9IHRoaXMuZ2V0Qm9keShzdHIsIGkpO1xuXG4gICAgICAgICAgICAgICAgICAgIGkgPSBpICsgYm9keS5vcmlnaW5MZW5ndGggLSAxO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gdGhpcy5wYXJzZVN0cmluZyhib2R5LmJvZHkpO1xuXG4gICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnYWZ0ZXJCb2R5JztcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHR5cGUgPT09ICdhZnRlckJvZHknIHx8IHR5cGUgPT09ICduZWVkS2V5Jykge1xuICAgICAgICAgICAgICAgICAgICBsZXQgbGFzdCA9IGk7XG4gICAgICAgICAgICAgICAgICAgIHdoaWxlIChzdHJbbGFzdF0gPT09ICcsJyB8fCB0aGlzLmlzQmxhbmtDaGFyKHN0cltsYXN0XSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhc3QgKz0gMTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAoc3RyW2xhc3RdID09PSAnfScgJiYgbGFzdCA9PT0gc3RyLmxlbmd0aCAtIDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHdoaWxlIChyZXN1bHRbcmVzdWx0Lmxlbmd0aCAtIDFdID09PSAnLCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSByZXN1bHQuc3Vic3RyKDAsIHJlc3VsdC5sZW5ndGggLSAxKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnfSc7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGlmIChsYXN0ICE9PSBpICYmIHJlc3VsdCAhPT0gJ3snKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJywnO1xuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZSA9ICduZWVkS2V5JztcbiAgICAgICAgICAgICAgICAgICAgICAgIGkgPSBsYXN0IC0gMTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBvYmplY3QgbmVhciAke3Jlc3VsdH1gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qXG4gICAgICAgICogYXJyYXlcbiAgICAgICAgKi9cbiAgICAgICAgaWYgKHN0clswXSA9PT0gJ1snKSB7XG4gICAgICAgICAgICByZXN1bHQgPSAnWyc7XG4gICAgICAgICAgICB0eXBlID0gJ25lZWRCb2R5JztcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAxOyBpIDwgc3RyLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0cltpXSA9PT0gJyAnIHx8IHN0cltpXSA9PT0gJ1xcbicgfHwgc3RyW2ldID09PSAnXFx0Jykge1xuICAgICAgICAgICAgICAgICAgICAvKiBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgKi9cbiAgICAgICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSAnbmVlZEJvZHknKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICcsJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9ICdudWxsLCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAvKiBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgKi9cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICddJyAmJiBpID09PSBzdHIubGVuZ3RoIC0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3VsdFtyZXN1bHQubGVuZ3RoIC0gMV0gPT09ICcsJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHJlc3VsdC5zdWJzdHIoMCwgcmVzdWx0Lmxlbmd0aCAtIDEpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9ICddJztcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBib2R5ID0gdGhpcy5nZXRCb2R5KHN0ciwgaSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaSA9IGkgKyBib2R5Lm9yaWdpbkxlbmd0aCAtIDE7XG4gICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSB0aGlzLnBhcnNlU3RyaW5nKGJvZHkuYm9keSk7XG5cbiAgICAgICAgICAgICAgICAgICAgdHlwZSA9ICdhZnRlckJvZHknO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJ2FmdGVyQm9keScpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0cltpXSA9PT0gJywnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJywnO1xuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZSA9ICduZWVkQm9keSc7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGRlYWwgd2l0aCBtaXN0YWtlIFwiLFwiXG4gICAgICAgICAgICAgICAgICAgICAgICB3aGlsZSAoc3RyW2kgKyAxXSA9PT0gJywnIHx8IHRoaXMuaXNCbGFua0NoYXIoc3RyW2kgKyAxXSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoc3RyW2kgKyAxXSA9PT0gJywnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnbnVsbCwnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpICs9IDE7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSAnXScgJiYgaSA9PT0gc3RyLmxlbmd0aCAtIDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnXSc7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIGFycmF5IG5lYXIgJHtyZXN1bHR9YCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gJyc7XG4gICAgfVxuXG4gICAgZ2V0Qm9keShzdHIsIHBvcykge1xuICAgICAgICBsZXQgYm9keSA9ICcnO1xuXG4gICAgICAgIC8vIHBhcnNlIHN0cmluZyBib2R5XG4gICAgICAgIGlmIChzdHJbcG9zXSA9PT0gJ1wiJyB8fCBzdHJbcG9zXSA9PT0gJ1xcJycpIHtcbiAgICAgICAgICAgIGJvZHkgPSBzdHJbcG9zXTtcblxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IHBvcyArIDE7IGkgPCBzdHIubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnXFxcXCcpIHtcbiAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaV07XG4gICAgICAgICAgICAgICAgICAgIGlmIChpICsgMSA8IHN0ci5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2kgKyAxXTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpICs9IDE7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChzdHJbaV0gPT09IHN0cltwb3NdKSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW3Bvc107XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBvcmlnaW5MZW5ndGg6IGJvZHkubGVuZ3RoLFxuICAgICAgICAgICAgICAgICAgICAgICAgYm9keSxcbiAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gc3RyaW5nIGJvZHkgbmVhciAke2JvZHl9YCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBwYXJzZSB0cnVlIC8gZmFsc2VcbiAgICAgICAgaWYgKHN0cltwb3NdID09PSAndCcpIHtcbiAgICAgICAgICAgIGlmIChzdHIuaW5kZXhPZigndHJ1ZScsIHBvcykgPT09IHBvcykge1xuICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgIG9yaWdpbkxlbmd0aDogJ3RydWUnLmxlbmd0aCxcbiAgICAgICAgICAgICAgICAgICAgYm9keTogJ3RydWUnLFxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gYm9vbGVhbiBib2R5IG5lYXIgJHtzdHIuc3Vic3RyKDAsIHBvcyArIDEwKX1gKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoc3RyW3Bvc10gPT09ICdmJykge1xuICAgICAgICAgICAgaWYgKHN0ci5pbmRleE9mKCdmJywgcG9zKSA9PT0gcG9zKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgb3JpZ2luTGVuZ3RoOiAnZmFsc2UnLmxlbmd0aCxcbiAgICAgICAgICAgICAgICAgICAgYm9keTogJ2ZhbHNlJyxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIGJvb2xlYW4gYm9keSBuZWFyICR7c3RyLnN1YnN0cigwLCBwb3MgKyAxMCl9YCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBwYXJzZSBudWxsXG4gICAgICAgIGlmIChzdHJbcG9zXSA9PT0gJ24nKSB7XG4gICAgICAgICAgICBpZiAoc3RyLmluZGV4T2YoJ251bGwnLCBwb3MpID09PSBwb3MpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBvcmlnaW5MZW5ndGg6ICdudWxsJy5sZW5ndGgsXG4gICAgICAgICAgICAgICAgICAgIGJvZHk6ICdudWxsJyxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIGJvb2xlYW4gYm9keSBuZWFyICR7c3RyLnN1YnN0cigwLCBwb3MgKyAxMCl9YCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBwYXJzZSBudW1iZXJcbiAgICAgICAgaWYgKHN0cltwb3NdID09PSAnLScgfHwgc3RyW3Bvc10gPT09ICcrJyB8fCBzdHJbcG9zXSA9PT0gJy4nIHx8IChzdHJbcG9zXSA+PSAnMCcgJiYgc3RyW3Bvc10gPD0gJzknKSkge1xuICAgICAgICAgICAgYm9keSA9ICcnO1xuXG4gICAgICAgICAgICBmb3IgKGxldCBpID0gcG9zOyBpIDwgc3RyLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0cltpXSA9PT0gJy0nIHx8IHN0cltpXSA9PT0gJysnIHx8IHN0cltpXSA9PT0gJy4nIHx8IChzdHJbaV0gPj0gJzAnICYmIHN0cltpXSA8PSAnOScpKSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2ldO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBvcmlnaW5MZW5ndGg6IGJvZHkubGVuZ3RoLFxuICAgICAgICAgICAgICAgICAgICAgICAgYm9keSxcbiAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gbnVtYmVyIGJvZHkgbmVhciAke2JvZHl9YCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBwYXJzZSBvYmplY3RcbiAgICAgICAgaWYgKHN0cltwb3NdID09PSAneycgfHwgc3RyW3Bvc10gPT09ICdbJykge1xuICAgICAgICAgICAgY29uc3Qgc3RhY2sgPSBbXG4gICAgICAgICAgICAgICAgc3RyW3Bvc10sXG4gICAgICAgICAgICBdO1xuICAgICAgICAgICAgYm9keSA9IHN0cltwb3NdO1xuXG4gICAgICAgICAgICBmb3IgKGxldCBpID0gcG9zICsgMTsgaSA8IHN0ci5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2ldO1xuICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICdcXFxcJykge1xuICAgICAgICAgICAgICAgICAgICBpZiAoaSArIDEgPCBzdHIubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaSArPSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSAnXCInKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdGFja1tzdGFjay5sZW5ndGggLSAxXSA9PT0gJ1wiJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucG9wKCk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gIT09ICdcXCcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdGFjay5wdXNoKHN0cltpXSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ1xcJycpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdID09PSAnXFwnJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucG9wKCk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gIT09ICdcIicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnB1c2goc3RyW2ldKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gIT09ICdcIicgJiYgc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gIT09ICdcXCcnKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICd7Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucHVzaCgneycpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ30nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gPT09ICd7Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnBvcCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OICR7KHN0cltwb3NdID09PSAneycgPyAnb2JqZWN0JyA6ICdhcnJheScpfSBib2R5IG5lYXIgJHtib2R5fWApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ1snKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdGFjay5wdXNoKCdbJyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSAnXScpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChzdGFja1tzdGFjay5sZW5ndGggLSAxXSA9PT0gJ1snKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucG9wKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gJHsoc3RyW3Bvc10gPT09ICd7JyA/ICdvYmplY3QnIDogJ2FycmF5Jyl9IGJvZHkgbmVhciAke2JvZHl9YCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKCFzdGFjay5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9yaWdpbkxlbmd0aDogaSAtIHBvcyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHksXG4gICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OICR7KHN0cltwb3NdID09PSAneycgPyAnb2JqZWN0JyA6ICdhcnJheScpfSBib2R5IG5lYXIgJHtib2R5fWApO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBib2R5IG5lYXIgJHtzdHIuc3Vic3RyKChwb3MgLSA1ID49IDApID8gcG9zIC0gNSA6IDAsIDUwKX1gKTtcbiAgICB9XG5cbiAgICBwYXJzZUtleShzdHIsIHBvcywgcXVvdGUpIHtcbiAgICAgICAgbGV0IGtleSA9ICcnO1xuXG4gICAgICAgIGZvciAobGV0IGkgPSBwb3M7IGkgPCBzdHIubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIGlmIChxdW90ZSAmJiBxdW90ZSA9PT0gc3RyW2ldKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGtleTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmICghcXVvdGUgJiYgKHN0cltpXSA9PT0gJyAnIHx8IHN0cltpXSA9PT0gJzonKSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBrZXk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGtleSArPSBzdHJbaV07XG5cbiAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICdcXFxcJyAmJiBpICsgMSA8IHN0ci5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBrZXkgKz0gc3RyW2kgKyAxXTtcbiAgICAgICAgICAgICAgICBpICs9IDE7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIHN5bnRheCBuZWFyICR7a2V5fWApO1xuICAgIH1cblxuICAgIGNhbkJlS2V5SGVhZChjaCkge1xuICAgICAgICBpZiAoY2hbMF0gPT09ICdcXFxcJykge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG4gICAgICAgIGlmICgoY2hbMF0gPj0gJ2EnICYmIGNoWzBdIDw9ICd6JykgfHwgKGNoWzBdID49ICdBJyAmJiBjaFswXSA8PSAnWicpIHx8IGNoWzBdID09PSAnXycpIHtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChjaFswXSA+PSAnMCcgJiYgY2hbMF0gPD0gJzknKSB7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY2hbMF0gPT09ICckJykge1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGNoLmNoYXJDb2RlQXQoMCkgPiAyNTUpIHtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIGlzQmxhbmtDaGFyKGNoKSB7XG4gICAgICAgIHJldHVybiBjaCA9PT0gJyAnIHx8IGNoID09PSAnXFxuJyB8fCBjaCA9PT0gJ1xcdCc7XG4gICAgfVxufVxuIiwiaW1wb3J0IFNpbmdsZXRvbiBmcm9tICcuLi9hYnN0cmFjdHMvU2luZ2xldG9uJztcblxuLyoqXG4gKiBTYW5pdGl6ZXIgdXRpbGl0eS5cbiAqXG4gKiBDbGllbnQtc2lkZSBIVE1MIHNhbml0aXplciBkZXNpZ25lZCBtb3N0bHkgdG8gcHJldmVudCBzZWxmLVhTUyBhdHRhY2tzLlxuICogVGhlIHNhbml0aXplciB1dGlsaXR5IHdpbGwgc3RyaXAgYWxsIGF0dHJpYnV0ZXMgdGhhdCBzdGFydCB3aXRoIGBvbmAgKHVzdWFsbHkgSlMgZXZlbnQgaGFuZGxlcnMgYXMgYXR0cmlidXRlcywgaS5lLiBgb25sb2FkYCBvciBgb25lcnJvcmApIG9yIGNvbnRhaW4gdGhlIGBqYXZhc2NyaXB0OmAgcHNldWRvIHByb3RvY29sIGluIHRoZWlyIHZhbHVlcy5cbiAqXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTYW5pdGl6ZXIgZXh0ZW5kcyBTaW5nbGV0b24ge1xuICAgIGNvbnN0cnVjdG9yKHNub3dib2FyZCkge1xuICAgICAgICBzdXBlcihzbm93Ym9hcmQpO1xuXG4gICAgICAgIC8vIEFkZCB0byBnbG9iYWwgZnVuY3Rpb24gZm9yIGJhY2t3YXJkcyBjb21wYXRpYmlsaXR5XG4gICAgICAgIHdpbmRvdy53blNhbml0aXplID0gKGh0bWwpID0+IHRoaXMuc2FuaXRpemUoaHRtbCk7XG4gICAgICAgIHdpbmRvdy5vY1Nhbml0aXplID0gd2luZG93LnduU2FuaXRpemU7XG4gICAgfVxuXG4gICAgc2FuaXRpemUoaHRtbCwgYm9keU9ubHkpIHtcbiAgICAgICAgY29uc3QgcGFyc2VyID0gbmV3IERPTVBhcnNlcigpO1xuICAgICAgICBjb25zdCBkb20gPSBwYXJzZXIucGFyc2VGcm9tU3RyaW5nKGh0bWwsICd0ZXh0L2h0bWwnKTtcbiAgICAgICAgY29uc3QgcmV0dXJuQm9keU9ubHkgPSAoYm9keU9ubHkgIT09IHVuZGVmaW5lZCAmJiB0eXBlb2YgYm9keU9ubHkgPT09ICdib29sZWFuJylcbiAgICAgICAgICAgID8gYm9keU9ubHlcbiAgICAgICAgICAgIDogdHJ1ZTtcblxuICAgICAgICB0aGlzLnNhbml0aXplTm9kZShkb20uZ2V0Um9vdE5vZGUoKSk7XG5cbiAgICAgICAgcmV0dXJuIChyZXR1cm5Cb2R5T25seSkgPyBkb20uYm9keS5pbm5lckhUTUwgOiBkb20uaW5uZXJIVE1MO1xuICAgIH1cblxuICAgIHNhbml0aXplTm9kZShub2RlKSB7XG4gICAgICAgIGlmIChub2RlLnRhZ05hbWUgPT09ICdTQ1JJUFQnKSB7XG4gICAgICAgICAgICBub2RlLnJlbW92ZSgpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy50cmltQXR0cmlidXRlcyhub2RlKTtcblxuICAgICAgICBjb25zdCBjaGlsZHJlbiA9IEFycmF5LmZyb20obm9kZS5jaGlsZHJlbik7XG5cbiAgICAgICAgY2hpbGRyZW4uZm9yRWFjaCgoY2hpbGQpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2FuaXRpemVOb2RlKGNoaWxkKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgdHJpbUF0dHJpYnV0ZXMobm9kZSkge1xuICAgICAgICBpZiAoIW5vZGUuYXR0cmlidXRlcykge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBub2RlLmF0dHJpYnV0ZXMubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIGNvbnN0IGF0dHJOYW1lID0gbm9kZS5hdHRyaWJ1dGVzLml0ZW0oaSkubmFtZTtcbiAgICAgICAgICAgIGNvbnN0IGF0dHJWYWx1ZSA9IG5vZGUuYXR0cmlidXRlcy5pdGVtKGkpLnZhbHVlO1xuXG4gICAgICAgICAgICAvKlxuICAgICAgICAgICAgKiByZW1vdmUgYXR0cmlidXRlcyB3aGVyZSB0aGUgbmFtZXMgc3RhcnQgd2l0aCBcIm9uXCIgKGZvciBleGFtcGxlOiBvbmxvYWQsIG9uZXJyb3IuLi4pXG4gICAgICAgICAgICAqIHJlbW92ZSBhdHRyaWJ1dGVzIHdoZXJlIHRoZSB2YWx1ZSBzdGFydHMgd2l0aCB0aGUgXCJqYXZhc2NyaXB0OlwiIHBzZXVkbyBwcm90b2NvbCAoZm9yIGV4YW1wbGUgaHJlZj1cImphdmFzY3JpcHQ6YWxlcnQoMSlcIilcbiAgICAgICAgICAgICovXG4gICAgICAgICAgICAvKiBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgKi9cbiAgICAgICAgICAgIGlmIChhdHRyTmFtZS5pbmRleE9mKCdvbicpID09PSAwIHx8IGF0dHJWYWx1ZS5pbmRleE9mKCdqYXZhc2NyaXB0OicpID09PSAwKSB7XG4gICAgICAgICAgICAgICAgbm9kZS5yZW1vdmVBdHRyaWJ1dGUoYXR0ck5hbWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxufVxuIl0sIm5hbWVzIjpbIlBsdWdpbkJhc2UiLCJzbm93Ym9hcmQiLCJkZXRhY2giLCJTaW5nbGV0b24iLCJQbHVnaW5Mb2FkZXIiLCJuYW1lIiwiaW5zdGFuY2UiLCJpbnN0YW5jZXMiLCJzaW5nbGV0b24iLCJwcm90b3R5cGUiLCJtb2NrcyIsIm9yaWdpbmFsRnVuY3Rpb25zIiwibWV0aG9kTmFtZSIsImlzRnVuY3Rpb24iLCJwYXJhbWV0ZXJzIiwiYXJncyIsInNoaWZ0IiwiZGVwZW5kZW5jaWVzRnVsZmlsbGVkIiwidW5tZXQiLCJnZXREZXBlbmRlbmNpZXMiLCJmaWx0ZXIiLCJpdGVtIiwiZ2V0UGx1Z2luTmFtZXMiLCJpbmNsdWRlcyIsIkVycm9yIiwiam9pbiIsImlzU2luZ2xldG9uIiwibGVuZ3RoIiwiaW5pdGlhbGlzZVNpbmdsZXRvbiIsIk9iamVjdCIsImtleXMiLCJlbnRyaWVzIiwiZm9yRWFjaCIsImVudHJ5IiwiY2FsbGJhY2siLCJwYXJhbXMiLCJuZXdJbnN0YW5jZSIsInNwbGljZSIsImluZGV4T2YiLCJwdXNoIiwiZGVwZW5kZW5jaWVzIiwibWFwIiwidG9Mb3dlckNhc2UiLCJmdWxmaWxsZWQiLCJwbHVnaW4iLCJoYXNQbHVnaW4iLCJDb29raWUiLCJKc29uUGFyc2VyIiwiU2FuaXRpemVyIiwiU25vd2JvYXJkIiwiYXV0b1NpbmdsZXRvbnMiLCJkZWJ1ZyIsImRlYnVnRW5hYmxlZCIsImF1dG9Jbml0U2luZ2xldG9ucyIsInBsdWdpbnMiLCJhdHRhY2hBYnN0cmFjdHMiLCJsb2FkVXRpbGl0aWVzIiwiaW5pdGlhbGlzZSIsImFkZFBsdWdpbiIsIndpbmRvdyIsImFkZEV2ZW50TGlzdGVuZXIiLCJpbml0aWFsaXNlU2luZ2xldG9ucyIsImdsb2JhbEV2ZW50IiwidmFsdWVzIiwibG93ZXJOYW1lIiwidW5kZWZpbmVkIiwiZ2V0SW5zdGFuY2UiLCJnZXRJbnN0YW5jZXMiLCJkZXN0cnVjdG9yIiwiZXZlbnROYW1lIiwiaGFzTWV0aG9kIiwibGlzdGVuZXJzIiwiY2FsbE1ldGhvZCIsImxpc3RlbnNUb0V2ZW50IiwiY2FuY2VsbGVkIiwiZ2V0UGx1Z2luIiwibGlzdGVuTWV0aG9kIiwiUHJvbWlzZSIsInJlc29sdmUiLCJwcm9taXNlcyIsImluc3RhbmNlUHJvbWlzZSIsImFsbCIsImNvbnNvbGUiLCJncm91cENvbGxhcHNlZCIsInRyYWNlIiwiZ3JvdXBFbmQiLCJTbm93Qm9hcmQiLCJCYXNlQ29va2llIiwiZGVmYXVsdHMiLCJleHBpcmVzIiwicGF0aCIsImRvbWFpbiIsInNlY3VyZSIsInNhbWVTaXRlIiwib3B0aW9ucyIsImtleSIsInZhbHVlIiwiY29va2llcyIsImdldCIsImNvb2tpZU5hbWUiLCJjb29raWVWYWx1ZSIsIm5ld1ZhbHVlIiwic2F2ZVZhbHVlIiwic2V0IiwiZ2V0RGVmYXVsdHMiLCJyZW1vdmUiLCJ3bkpTT04iLCJqc29uIiwicGFyc2UiLCJvY0pTT04iLCJzdHIiLCJqc29uU3RyaW5nIiwicGFyc2VTdHJpbmciLCJKU09OIiwidHJpbSIsInJlc3VsdCIsInR5cGUiLCJib2R5Iiwic3Vic3RyIiwiaSIsIm51bSIsInBhcnNlRmxvYXQiLCJOdW1iZXIiLCJpc05hTiIsInRvU3RyaW5nIiwiaXNCbGFua0NoYXIiLCJwYXJzZUtleSIsImNhbkJlS2V5SGVhZCIsImdldEJvZHkiLCJvcmlnaW5MZW5ndGgiLCJsYXN0IiwicG9zIiwic3RhY2siLCJwb3AiLCJxdW90ZSIsImNoIiwiY2hhckNvZGVBdCIsInduU2FuaXRpemUiLCJodG1sIiwic2FuaXRpemUiLCJvY1Nhbml0aXplIiwiYm9keU9ubHkiLCJwYXJzZXIiLCJET01QYXJzZXIiLCJkb20iLCJwYXJzZUZyb21TdHJpbmciLCJyZXR1cm5Cb2R5T25seSIsInNhbml0aXplTm9kZSIsImdldFJvb3ROb2RlIiwiaW5uZXJIVE1MIiwibm9kZSIsInRhZ05hbWUiLCJ0cmltQXR0cmlidXRlcyIsImNoaWxkcmVuIiwiQXJyYXkiLCJmcm9tIiwiY2hpbGQiLCJhdHRyaWJ1dGVzIiwiYXR0ck5hbWUiLCJhdHRyVmFsdWUiLCJyZW1vdmVBdHRyaWJ1dGUiXSwic291cmNlUm9vdCI6IiJ9