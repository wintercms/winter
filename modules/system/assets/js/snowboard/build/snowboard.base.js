"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/js/snowboard/build/snowboard.base"],{

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

/***/ "./assets/js/snowboard/snowboard.base.js":
/*!***********************************************!*\
  !*** ./assets/js/snowboard/snowboard.base.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_Snowboard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main/Snowboard */ "./assets/js/snowboard/main/Snowboard.js");


(function (window) {
  var snowboard = new _main_Snowboard__WEBPACK_IMPORTED_MODULE_0__["default"](); // Cover all aliases

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
/******/ __webpack_require__.O(0, ["assets/js/vendor/vendor"], () => (__webpack_exec__("./assets/js/snowboard/snowboard.base.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYnVpbGQvc25vd2JvYXJkLmJhc2UuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7SUFDcUJBO0FBQ2pCO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0ksc0JBQVlDLFNBQVosRUFBdUI7QUFBQTs7QUFDbkIsU0FBS0EsU0FBTCxHQUFpQkEsU0FBakI7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7O1dBQ0ksd0JBQWU7QUFDWCxhQUFPLEVBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSxtQkFBVTtBQUNOLGFBQU8sRUFBUDtBQUNIO0FBRUQ7QUFDSjtBQUNBO0FBQ0E7QUFDQTs7OztXQUNJLHNCQUFhO0FBQ1QsV0FBS0MsTUFBTDtBQUNBLGFBQU8sS0FBS0QsU0FBWjtBQUNIOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDOUNMO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7SUFDcUJFOzs7Ozs7Ozs7Ozs7RUFBa0JIOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNidkM7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7SUFDcUJJO0FBQ2pCO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNJLHdCQUFZQyxJQUFaLEVBQWtCSixTQUFsQixFQUE2QkssUUFBN0IsRUFBdUM7QUFBQTs7QUFDbkMsU0FBS0QsSUFBTCxHQUFZQSxJQUFaO0FBQ0EsU0FBS0osU0FBTCxHQUFpQkEsU0FBakI7QUFDQSxTQUFLSyxRQUFMLEdBQWdCQSxRQUFoQjtBQUNBLFNBQUtDLFNBQUwsR0FBaUIsRUFBakI7QUFDQSxT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Q0E7QUFDSDs7QUFFRCxXQUFLK0MsY0FBTCxDQUFvQkYsSUFBcEI7QUFFQSxVQUFNRyxRQUFRLEdBQUdDLEtBQUssQ0FBQ0MsSUFBTixDQUFXTCxJQUFJLENBQUNHLFFBQWhCLENBQWpCO0FBRUFBLE1BQUFBLFFBQVEsQ0FBQ3pILE9BQVQsQ0FBaUIsVUFBQzRILEtBQUQsRUFBVztBQUN4QixjQUFJLENBQUNULFlBQUwsQ0FBa0JTLEtBQWxCO0FBQ0gsT0FGRDtBQUdIOzs7V0FFRCx3QkFBZU4sSUFBZixFQUFxQjtBQUNqQixVQUFJLENBQUNBLElBQUksQ0FBQ08sVUFBVixFQUFzQjtBQUNsQjtBQUNIOztBQUVELFdBQUssSUFBSXRDLENBQUMsR0FBRyxDQUFiLEVBQWdCQSxDQUFDLEdBQUcrQixJQUFJLENBQUNPLFVBQUwsQ0FBZ0JsSSxNQUFwQyxFQUE0QzRGLENBQUMsSUFBSSxDQUFqRCxFQUFvRDtBQUNoRCxZQUFNdUMsUUFBUSxHQUFHUixJQUFJLENBQUNPLFVBQUwsQ0FBZ0J4SSxJQUFoQixDQUFxQmtHLENBQXJCLEVBQXdCbEgsSUFBekM7QUFDQSxZQUFNMEosU0FBUyxHQUFHVCxJQUFJLENBQUNPLFVBQUwsQ0FBZ0J4SSxJQUFoQixDQUFxQmtHLENBQXJCLEVBQXdCdkIsS0FBMUM7QUFFQTtBQUNaO0FBQ0E7QUFDQTs7QUFDWTs7QUFDQSxZQUFJOEQsUUFBUSxDQUFDeEgsT0FBVCxDQUFpQixJQUFqQixNQUEyQixDQUEzQixJQUFnQ3lILFNBQVMsQ0FBQ3pILE9BQVYsQ0FBa0IsYUFBbEIsTUFBcUMsQ0FBekUsRUFBNEU7QUFDeEVnSCxVQUFBQSxJQUFJLENBQUNVLGVBQUwsQ0FBcUJGLFFBQXJCO0FBQ0g7QUFDSjtBQUNKOzs7O0VBdERrQzNKIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL2Fic3RyYWN0cy9QbHVnaW5CYXNlLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9hYnN0cmFjdHMvU2luZ2xldG9uLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9tYWluL1BsdWdpbkxvYWRlci5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvbWFpbi9Tbm93Ym9hcmQuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvanMvc25vd2JvYXJkL3Nub3dib2FyZC5iYXNlLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC91dGlsaXRpZXMvQ29va2llLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC91dGlsaXRpZXMvSnNvblBhcnNlci5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvdXRpbGl0aWVzL1Nhbml0aXplci5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFBsdWdpbiBiYXNlIGFic3RyYWN0LlxuICpcbiAqIFRoaXMgY2xhc3MgcHJvdmlkZXMgdGhlIGJhc2UgZnVuY3Rpb25hbGl0eSBmb3IgYWxsIHBsdWdpbnMuXG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFBsdWdpbkJhc2Uge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogVGhlIGNvbnN0cnVjdG9yIGlzIHByb3ZpZGVkIHRoZSBTbm93Ym9hcmQgZnJhbWV3b3JrIGluc3RhbmNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmR9IHNub3dib2FyZFxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHNub3dib2FyZCkge1xuICAgICAgICB0aGlzLnNub3dib2FyZCA9IHNub3dib2FyZDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIHRoZSByZXF1aXJlZCBwbHVnaW5zIGZvciB0aGlzIHNwZWNpZmljIG1vZHVsZSB0byB3b3JrLlxuICAgICAqXG4gICAgICogQHJldHVybnMge3N0cmluZ1tdfSBBbiBhcnJheSBvZiBwbHVnaW5zIHJlcXVpcmVkIGZvciB0aGlzIG1vZHVsZSB0byB3b3JrLCBhcyBzdHJpbmdzLlxuICAgICAqL1xuICAgIGRlcGVuZGVuY2llcygpIHtcbiAgICAgICAgcmV0dXJuIFtdO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgdGhlIGxpc3RlbmVyIG1ldGhvZHMgZm9yIGdsb2JhbCBldmVudHMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgICAqL1xuICAgIGxpc3RlbnMoKSB7XG4gICAgICAgIHJldHVybiB7fTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXN0cnVjdG9yLlxuICAgICAqXG4gICAgICogRmlyZWQgd2hlbiB0aGlzIHBsdWdpbiBpcyByZW1vdmVkLlxuICAgICAqL1xuICAgIGRlc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMuZGV0YWNoKCk7XG4gICAgICAgIGRlbGV0ZSB0aGlzLnNub3dib2FyZDtcbiAgICB9XG59XG4iLCJpbXBvcnQgUGx1Z2luQmFzZSBmcm9tICcuL1BsdWdpbkJhc2UnO1xuXG4vKipcbiAqIFNpbmdsZXRvbiBwbHVnaW4gYWJzdHJhY3QuXG4gKlxuICogVGhpcyBpcyBhIHNwZWNpYWwgZGVmaW5pdGlvbiBjbGFzcyB0aGF0IHRoZSBTbm93Ym9hcmQgZnJhbWV3b3JrIHdpbGwgdXNlIHRvIGludGVycHJldCB0aGUgY3VycmVudCBwbHVnaW4gYXMgYVxuICogXCJzaW5nbGV0b25cIi4gVGhpcyB3aWxsIGVuc3VyZSB0aGF0IG9ubHkgb25lIGluc3RhbmNlIG9mIHRoZSBwbHVnaW4gY2xhc3MgaXMgdXNlZCBhY3Jvc3MgdGhlIGJvYXJkLlxuICpcbiAqIFNpbmdsZXRvbnMgYXJlIGluaXRpYWxpc2VkIG9uIHRoZSBcImRvbVJlYWR5XCIgZXZlbnQgYnkgZGVmYXVsdC5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2luZ2xldG9uIGV4dGVuZHMgUGx1Z2luQmFzZSB7XG59XG4iLCJpbXBvcnQgUGx1Z2luQmFzZSBmcm9tICcuLi9hYnN0cmFjdHMvUGx1Z2luQmFzZSc7XG5pbXBvcnQgU2luZ2xldG9uIGZyb20gJy4uL2Fic3RyYWN0cy9TaW5nbGV0b24nO1xuXG4vKipcbiAqIFBsdWdpbiBsb2FkZXIgY2xhc3MuXG4gKlxuICogVGhpcyBpcyBhIHByb3ZpZGVyIChmYWN0b3J5KSBjbGFzcyBmb3IgYSBzaW5nbGUgcGx1Z2luIGFuZCBwcm92aWRlcyB0aGUgbGluayBiZXR3ZWVuIFNub3dib2FyZCBmcmFtZXdvcmsgZnVuY3Rpb25hbGl0eVxuICogYW5kIHRoZSB1bmRlcmx5aW5nIHBsdWdpbiBpbnN0YW5jZXMuIEl0IGFsc28gcHJvdmlkZXMgc29tZSBiYXNpYyBtb2NraW5nIG9mIHBsdWdpbiBtZXRob2RzIGZvciB0ZXN0aW5nLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQbHVnaW5Mb2FkZXIge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQmluZHMgdGhlIFdpbnRlciBmcmFtZXdvcmsgdG8gdGhlIGluc3RhbmNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG5hbWVcbiAgICAgKiBAcGFyYW0ge1Nub3dib2FyZH0gc25vd2JvYXJkXG4gICAgICogQHBhcmFtIHtQbHVnaW5CYXNlfSBpbnN0YW5jZVxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKG5hbWUsIHNub3dib2FyZCwgaW5zdGFuY2UpIHtcbiAgICAgICAgdGhpcy5uYW1lID0gbmFtZTtcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQgPSBzbm93Ym9hcmQ7XG4gICAgICAgIHRoaXMuaW5zdGFuY2UgPSBpbnN0YW5jZTtcbiAgICAgICAgdGhpcy5pbnN0YW5jZXMgPSBbXTtcbiAgICAgICAgdGhpcy5zaW5nbGV0b24gPSBpbnN0YW5jZS5wcm90b3R5cGUgaW5zdGFuY2VvZiBTaW5nbGV0b247XG4gICAgICAgIHRoaXMubW9ja3MgPSB7fTtcbiAgICAgICAgdGhpcy5vcmlnaW5hbEZ1bmN0aW9ucyA9IHt9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgdGhlIGN1cnJlbnQgcGx1Z2luIGhhcyBhIHNwZWNpZmljIG1ldGhvZCBhdmFpbGFibGUuXG4gICAgICpcbiAgICAgKiBSZXR1cm5zIGZhbHNlIGlmIHRoZSBjdXJyZW50IHBsdWdpbiBpcyBhIGNhbGxiYWNrIGZ1bmN0aW9uLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG1ldGhvZE5hbWVcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICBoYXNNZXRob2QobWV0aG9kTmFtZSkge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiAodHlwZW9mIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdID09PSAnZnVuY3Rpb24nKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDYWxscyBhIHByb3RvdHlwZSBtZXRob2QgZm9yIGEgcGx1Z2luLiBUaGlzIHNob3VsZCBnZW5lcmFsbHkgYmUgdXNlZCBmb3IgXCJzdGF0aWNcIiBjYWxscy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtZXRob2ROYW1lXG4gICAgICogQHBhcmFtIHsuLi59IGFyZ3NcbiAgICAgKiBAcmV0dXJucyB7YW55fVxuICAgICAqL1xuICAgIGNhbGxNZXRob2QoLi4ucGFyYW1ldGVycykge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgYXJncyA9IHBhcmFtZXRlcnM7XG4gICAgICAgIGNvbnN0IG1ldGhvZE5hbWUgPSBhcmdzLnNoaWZ0KCk7XG5cbiAgICAgICAgcmV0dXJuIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdKGFyZ3MpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYW4gaW5zdGFuY2Ugb2YgdGhlIGN1cnJlbnQgcGx1Z2luLlxuICAgICAqXG4gICAgICogLSBJZiB0aGlzIGlzIGEgY2FsbGJhY2sgZnVuY3Rpb24gcGx1Z2luLCB0aGUgZnVuY3Rpb24gd2lsbCBiZSByZXR1cm5lZC5cbiAgICAgKiAtIElmIHRoaXMgaXMgYSBzaW5nbGV0b24sIHRoZSBzaW5nbGUgaW5zdGFuY2Ugb2YgdGhlIHBsdWdpbiB3aWxsIGJlIHJldHVybmVkLlxuICAgICAqXG4gICAgICogQHJldHVybnMge1BsdWdpbkJhc2V8RnVuY3Rpb259XG4gICAgICovXG4gICAgZ2V0SW5zdGFuY2UoLi4ucGFyYW1ldGVycykge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlKC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICB9XG4gICAgICAgIGlmICghdGhpcy5kZXBlbmRlbmNpZXNGdWxmaWxsZWQoKSkge1xuICAgICAgICAgICAgY29uc3QgdW5tZXQgPSB0aGlzLmdldERlcGVuZGVuY2llcygpLmZpbHRlcigoaXRlbSkgPT4gIXRoaXMuc25vd2JvYXJkLmdldFBsdWdpbk5hbWVzKCkuaW5jbHVkZXMoaXRlbSkpO1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBUaGUgXCIke3RoaXMubmFtZX1cIiBwbHVnaW4gcmVxdWlyZXMgdGhlIGZvbGxvd2luZyBwbHVnaW5zOiAke3VubWV0LmpvaW4oJywgJyl9YCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMuaXNTaW5nbGV0b24oKSkge1xuICAgICAgICAgICAgaWYgKHRoaXMuaW5zdGFuY2VzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgICAgIHRoaXMuaW5pdGlhbGlzZVNpbmdsZXRvbigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBBcHBseSBtb2NrZWQgbWV0aG9kc1xuICAgICAgICAgICAgaWYgKE9iamVjdC5rZXlzKHRoaXMubW9ja3MpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICBPYmplY3QuZW50cmllcyh0aGlzLm9yaWdpbmFsRnVuY3Rpb25zKS5mb3JFYWNoKChlbnRyeSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBbbWV0aG9kTmFtZSwgY2FsbGJhY2tdID0gZW50cnk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaW5zdGFuY2VzWzBdW21ldGhvZE5hbWVdID0gY2FsbGJhY2s7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgT2JqZWN0LmVudHJpZXModGhpcy5tb2NrcykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgW21ldGhvZE5hbWUsIGNhbGxiYWNrXSA9IGVudHJ5O1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmluc3RhbmNlc1swXVttZXRob2ROYW1lXSA9ICguLi5wYXJhbXMpID0+IGNhbGxiYWNrKHRoaXMsIC4uLnBhcmFtcyk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlc1swXTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFwcGx5IG1vY2tlZCBtZXRob2RzIHRvIHByb3RvdHlwZVxuICAgICAgICBpZiAoT2JqZWN0LmtleXModGhpcy5tb2NrcykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgT2JqZWN0LmVudHJpZXModGhpcy5vcmlnaW5hbEZ1bmN0aW9ucykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBbbWV0aG9kTmFtZSwgY2FsbGJhY2tdID0gZW50cnk7XG4gICAgICAgICAgICAgICAgdGhpcy5pbnN0YW5jZS5wcm90b3R5cGVbbWV0aG9kTmFtZV0gPSBjYWxsYmFjaztcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgT2JqZWN0LmVudHJpZXModGhpcy5tb2NrcykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBbbWV0aG9kTmFtZSwgY2FsbGJhY2tdID0gZW50cnk7XG4gICAgICAgICAgICAgICAgdGhpcy5pbnN0YW5jZS5wcm90b3R5cGVbbWV0aG9kTmFtZV0gPSAoLi4ucGFyYW1zKSA9PiBjYWxsYmFjayh0aGlzLCAuLi5wYXJhbXMpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBuZXdJbnN0YW5jZSA9IG5ldyB0aGlzLmluc3RhbmNlKHRoaXMuc25vd2JvYXJkLCAuLi5wYXJhbWV0ZXJzKTtcbiAgICAgICAgbmV3SW5zdGFuY2UuZGV0YWNoID0gKCkgPT4gdGhpcy5pbnN0YW5jZXMuc3BsaWNlKHRoaXMuaW5zdGFuY2VzLmluZGV4T2YobmV3SW5zdGFuY2UpLCAxKTtcblxuICAgICAgICB0aGlzLmluc3RhbmNlcy5wdXNoKG5ld0luc3RhbmNlKTtcbiAgICAgICAgcmV0dXJuIG5ld0luc3RhbmNlO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldHMgYWxsIGluc3RhbmNlcyBvZiB0aGUgY3VycmVudCBwbHVnaW4uXG4gICAgICpcbiAgICAgKiBJZiB0aGlzIHBsdWdpbiBpcyBhIGNhbGxiYWNrIGZ1bmN0aW9uIHBsdWdpbiwgYW4gZW1wdHkgYXJyYXkgd2lsbCBiZSByZXR1cm5lZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtQbHVnaW5CYXNlW119XG4gICAgICovXG4gICAgZ2V0SW5zdGFuY2VzKCkge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybiBbXTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlcztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXRlcm1pbmVzIGlmIHRoZSBjdXJyZW50IHBsdWdpbiBpcyBhIHNpbXBsZSBjYWxsYmFjayBmdW5jdGlvbi5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgICAqL1xuICAgIGlzRnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAodHlwZW9mIHRoaXMuaW5zdGFuY2UgPT09ICdmdW5jdGlvbicgJiYgdGhpcy5pbnN0YW5jZS5wcm90b3R5cGUgaW5zdGFuY2VvZiBQbHVnaW5CYXNlID09PSBmYWxzZSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGV0ZXJtaW5lcyBpZiB0aGUgY3VycmVudCBwbHVnaW4gaXMgYSBzaW5nbGV0b24uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICBpc1NpbmdsZXRvbigpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuaW5zdGFuY2UucHJvdG90eXBlIGluc3RhbmNlb2YgU2luZ2xldG9uID09PSB0cnVlO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpc2VzIHRoZSBzaW5nbGV0b24gaW5zdGFuY2UuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICBpbml0aWFsaXNlU2luZ2xldG9uKCkge1xuICAgICAgICBpZiAoIXRoaXMuaXNTaW5nbGV0b24oKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgbmV3SW5zdGFuY2UgPSBuZXcgdGhpcy5pbnN0YW5jZSh0aGlzLnNub3dib2FyZCk7XG4gICAgICAgIG5ld0luc3RhbmNlLmRldGFjaCA9ICgpID0+IHRoaXMuaW5zdGFuY2VzLnNwbGljZSh0aGlzLmluc3RhbmNlcy5pbmRleE9mKG5ld0luc3RhbmNlKSwgMSk7XG4gICAgICAgIHRoaXMuaW5zdGFuY2VzLnB1c2gobmV3SW5zdGFuY2UpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldHMgdGhlIGRlcGVuZGVuY2llcyBvZiB0aGUgY3VycmVudCBwbHVnaW4uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZ2V0RGVwZW5kZW5jaWVzKCkge1xuICAgICAgICAvLyBDYWxsYmFjayBmdW5jdGlvbnMgY2Fubm90IGhhdmUgZGVwZW5kZW5jaWVzLlxuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybiBbXTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIE5vIGRlcGVuZGVuY3kgbWV0aG9kIHNwZWNpZmllZC5cbiAgICAgICAgaWYgKHR5cGVvZiB0aGlzLmluc3RhbmNlLnByb3RvdHlwZS5kZXBlbmRlbmNpZXMgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIHJldHVybiBbXTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLmluc3RhbmNlLnByb3RvdHlwZS5kZXBlbmRlbmNpZXMoKS5tYXAoKGl0ZW0pID0+IGl0ZW0udG9Mb3dlckNhc2UoKSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGV0ZXJtaW5lcyBpZiB0aGUgY3VycmVudCBwbHVnaW4gaGFzIGFsbCBpdHMgZGVwZW5kZW5jaWVzIGZ1bGZpbGxlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgICAqL1xuICAgIGRlcGVuZGVuY2llc0Z1bGZpbGxlZCgpIHtcbiAgICAgICAgY29uc3QgZGVwZW5kZW5jaWVzID0gdGhpcy5nZXREZXBlbmRlbmNpZXMoKTtcblxuICAgICAgICBsZXQgZnVsZmlsbGVkID0gdHJ1ZTtcbiAgICAgICAgZGVwZW5kZW5jaWVzLmZvckVhY2goKHBsdWdpbikgPT4ge1xuICAgICAgICAgICAgaWYgKCF0aGlzLnNub3dib2FyZC5oYXNQbHVnaW4ocGx1Z2luKSkge1xuICAgICAgICAgICAgICAgIGZ1bGZpbGxlZCA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZnVsZmlsbGVkO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFsbG93cyBhIG1ldGhvZCBvZiBhbiBpbnN0YW5jZSB0byBiZSBtb2NrZWQgZm9yIHRlc3RpbmcuXG4gICAgICpcbiAgICAgKiBUaGlzIG1vY2sgd2lsbCBiZSBhcHBsaWVkIGZvciB0aGUgbGlmZSBvZiBhbiBpbnN0YW5jZS4gRm9yIHNpbmdsZXRvbnMsIHRoZSBtb2NrIHdpbGwgYmUgYXBwbGllZCBmb3IgdGhlIGxpZmVcbiAgICAgKiBvZiB0aGUgcGFnZS5cbiAgICAgKlxuICAgICAqIE1vY2tzIGNhbm5vdCBiZSBhcHBsaWVkIHRvIGNhbGxiYWNrIGZ1bmN0aW9uIHBsdWdpbnMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbWV0aG9kTmFtZVxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgbW9jayhtZXRob2ROYW1lLCBjYWxsYmFjaykge1xuICAgICAgICBpZiAodGhpcy5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICghdGhpcy5pbnN0YW5jZS5wcm90b3R5cGVbbWV0aG9kTmFtZV0pIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgRnVuY3Rpb24gXCIke21ldGhvZE5hbWV9XCIgZG9lcyBub3QgZXhpc3QgYW5kIGNhbm5vdCBiZSBtb2NrZWRgKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMubW9ja3NbbWV0aG9kTmFtZV0gPSBjYWxsYmFjaztcbiAgICAgICAgdGhpcy5vcmlnaW5hbEZ1bmN0aW9uc1ttZXRob2ROYW1lXSA9IHRoaXMuaW5zdGFuY2UucHJvdG90eXBlW21ldGhvZE5hbWVdO1xuXG4gICAgICAgIGlmICh0aGlzLmlzU2luZ2xldG9uKCkgJiYgdGhpcy5pbnN0YW5jZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICB0aGlzLmluaXRpYWxpc2VTaW5nbGV0b24oKTtcblxuICAgICAgICAgICAgLy8gQXBwbHkgbW9ja2VkIG1ldGhvZFxuICAgICAgICAgICAgdGhpcy5pbnN0YW5jZXNbMF1bbWV0aG9kTmFtZV0gPSAoLi4ucGFyYW1ldGVycykgPT4gY2FsbGJhY2sodGhpcywgLi4ucGFyYW1ldGVycyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW1vdmVzIGEgbW9jayBjYWxsYmFjayBmcm9tIGZ1dHVyZSBpbnN0YW5jZXMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbWV0aG9kTmFtZVxuICAgICAqL1xuICAgIHVubW9jayhtZXRob2ROYW1lKSB7XG4gICAgICAgIGlmICh0aGlzLmlzRnVuY3Rpb24oKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGlmICghdGhpcy5tb2Nrc1ttZXRob2ROYW1lXSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuaXNTaW5nbGV0b24oKSkge1xuICAgICAgICAgICAgdGhpcy5pbnN0YW5jZXNbMF1bbWV0aG9kTmFtZV0gPSB0aGlzLm9yaWdpbmFsRnVuY3Rpb25zW21ldGhvZE5hbWVdO1xuICAgICAgICB9XG5cbiAgICAgICAgZGVsZXRlIHRoaXMubW9ja3NbbWV0aG9kTmFtZV07XG4gICAgICAgIGRlbGV0ZSB0aGlzLm9yaWdpbmFsRnVuY3Rpb25zW21ldGhvZE5hbWVdO1xuICAgIH1cbn1cbiIsImltcG9ydCBQbHVnaW5CYXNlIGZyb20gJy4uL2Fic3RyYWN0cy9QbHVnaW5CYXNlJztcbmltcG9ydCBTaW5nbGV0b24gZnJvbSAnLi4vYWJzdHJhY3RzL1NpbmdsZXRvbic7XG5pbXBvcnQgUGx1Z2luTG9hZGVyIGZyb20gJy4vUGx1Z2luTG9hZGVyJztcblxuaW1wb3J0IENvb2tpZSBmcm9tICcuLi91dGlsaXRpZXMvQ29va2llJztcbmltcG9ydCBKc29uUGFyc2VyIGZyb20gJy4uL3V0aWxpdGllcy9Kc29uUGFyc2VyJztcbmltcG9ydCBTYW5pdGl6ZXIgZnJvbSAnLi4vdXRpbGl0aWVzL1Nhbml0aXplcic7XG5cbi8qKlxuICogU25vd2JvYXJkIC0gdGhlIFdpbnRlciBKYXZhU2NyaXB0IGZyYW1ld29yay5cbiAqXG4gKiBUaGlzIGNsYXNzIHJlcHJlc2VudHMgdGhlIGJhc2Ugb2YgYSBtb2Rlcm4gdGFrZSBvbiB0aGUgV2ludGVyIEpTIGZyYW1ld29yaywgYmVpbmcgZnVsbHkgZXh0ZW5zaWJsZSBhbmQgdGFraW5nIGFkdmFudGFnZVxuICogb2YgbW9kZXJuIEphdmFTY3JpcHQgZmVhdHVyZXMgYnkgbGV2ZXJhZ2luZyB0aGUgTGFyYXZlbCBNaXggY29tcGlsYXRpb24gZnJhbWV3b3JrLiBJdCBhbHNvIGlzIGNvZGVkIHVwIHRvIHJlbW92ZSB0aGVcbiAqIGRlcGVuZGVuY3kgb2YgalF1ZXJ5LlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICogQGxpbmsgaHR0cHM6Ly93aW50ZXJjbXMuY29tL2RvY3Mvc25vd2JvYXJkL2ludHJvZHVjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTbm93Ym9hcmQge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtib29sZWFufSBhdXRvU2luZ2xldG9ucyBBdXRvbWF0aWNhbGx5IGxvYWQgc2luZ2xldG9ucyB3aGVuIERPTSBpcyByZWFkeS4gRGVmYXVsdDogYHRydWVgLlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZGVidWcgV2hldGhlciBkZWJ1Z2dpbmcgbG9ncyBzaG91bGQgYmUgc2hvd24uIERlZmF1bHQ6IGBmYWxzZWAuXG4gICAgICovXG4gICAgY29uc3RydWN0b3IoYXV0b1NpbmdsZXRvbnMsIGRlYnVnKSB7XG4gICAgICAgIHRoaXMuZGVidWdFbmFibGVkID0gKHR5cGVvZiBkZWJ1ZyA9PT0gJ2Jvb2xlYW4nICYmIGRlYnVnID09PSB0cnVlKTtcbiAgICAgICAgdGhpcy5hdXRvSW5pdFNpbmdsZXRvbnMgPSAodHlwZW9mIGF1dG9TaW5nbGV0b25zID09PSAnYm9vbGVhbicgJiYgYXV0b1NpbmdsZXRvbnMgPT09IGZhbHNlKTtcbiAgICAgICAgdGhpcy5wbHVnaW5zID0ge307XG5cbiAgICAgICAgdGhpcy5hdHRhY2hBYnN0cmFjdHMoKTtcbiAgICAgICAgdGhpcy5sb2FkVXRpbGl0aWVzKCk7XG4gICAgICAgIHRoaXMuaW5pdGlhbGlzZSgpO1xuXG4gICAgICAgIHRoaXMuZGVidWcoJ1Nub3dib2FyZCBmcmFtZXdvcmsgaW5pdGlhbGlzZWQnKTtcbiAgICB9XG5cbiAgICBhdHRhY2hBYnN0cmFjdHMoKSB7XG4gICAgICAgIHRoaXMuUGx1Z2luQmFzZSA9IFBsdWdpbkJhc2U7XG4gICAgICAgIHRoaXMuU2luZ2xldG9uID0gU2luZ2xldG9uO1xuICAgIH1cblxuICAgIGxvYWRVdGlsaXRpZXMoKSB7XG4gICAgICAgIHRoaXMuYWRkUGx1Z2luKCdjb29raWUnLCBDb29raWUpO1xuICAgICAgICB0aGlzLmFkZFBsdWdpbignanNvblBhcnNlcicsIEpzb25QYXJzZXIpO1xuICAgICAgICB0aGlzLmFkZFBsdWdpbignc2FuaXRpemVyJywgU2FuaXRpemVyKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXNlcyB0aGUgZnJhbWV3b3JrLlxuICAgICAqXG4gICAgICogQXR0YWNoZXMgYSBsaXN0ZW5lciBmb3IgdGhlIERPTSBiZWluZyByZWFkeSBhbmQgdHJpZ2dlcnMgYSBnbG9iYWwgXCJyZWFkeVwiIGV2ZW50IGZvciBwbHVnaW5zIHRvIGJlZ2luIGF0dGFjaGluZ1xuICAgICAqIHRoZW1zZWx2ZXMgdG8gdGhlIERPTS5cbiAgICAgKi9cbiAgICBpbml0aWFsaXNlKCkge1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsICgpID0+IHtcbiAgICAgICAgICAgIGlmICh0aGlzLmF1dG9Jbml0U2luZ2xldG9ucykge1xuICAgICAgICAgICAgICAgIHRoaXMuaW5pdGlhbGlzZVNpbmdsZXRvbnMoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMuZ2xvYmFsRXZlbnQoJ3JlYWR5Jyk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEluaXRpYWxpc2VzIGFuIGluc3RhbmNlIG9mIGV2ZXJ5IHNpbmdsZXRvbi5cbiAgICAgKi9cbiAgICBpbml0aWFsaXNlU2luZ2xldG9ucygpIHtcbiAgICAgICAgT2JqZWN0LnZhbHVlcyh0aGlzLnBsdWdpbnMpLmZvckVhY2goKHBsdWdpbikgPT4ge1xuICAgICAgICAgICAgaWYgKHBsdWdpbi5pc1NpbmdsZXRvbigpKSB7XG4gICAgICAgICAgICAgICAgcGx1Z2luLmluaXRpYWxpc2VTaW5nbGV0b24oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWRkcyBhIHBsdWdpbiB0byB0aGUgZnJhbWV3b3JrLlxuICAgICAqXG4gICAgICogUGx1Z2lucyBhcmUgdGhlIGNvcm5lcnN0b25lIGZvciBhZGRpdGlvbmFsIGZ1bmN0aW9uYWxpdHkgZm9yIFNub3dib2FyZC4gQSBwbHVnaW4gbXVzdCBlaXRoZXIgYmUgYW4gRVMyMDE1IGNsYXNzXG4gICAgICogdGhhdCBleHRlbmRzIHRoZSBQbHVnaW5CYXNlIG9yIFNpbmdsZXRvbiBhYnN0cmFjdCBjbGFzc2VzLCBvciBhIHNpbXBsZSBjYWxsYmFjayBmdW5jdGlvbi5cbiAgICAgKlxuICAgICAqIFdoZW4gYSBwbHVnaW4gaXMgYWRkZWQsIGl0IGlzIGF1dG9tYXRpY2FsbHkgYXNzaWduZWQgYXMgYSBuZXcgbWFnaWMgbWV0aG9kIGluIHRoZSBTbm93Ym9hcmQgY2xhc3MgdXNpbmcgdGhlIG5hbWVcbiAgICAgKiBwYXJhbWV0ZXIsIGFuZCBjYW4gYmUgY2FsbGVkIHZpYSB0aGlzIG1ldGhvZC4gVGhpcyBtZXRob2Qgd2lsbCBhbHdheXMgYmUgdGhlIFwibG93ZXJjYXNlXCIgdmVyc2lvbiBvZiB0aGlzIG5hbWUuXG4gICAgICpcbiAgICAgKiBGb3IgZXhhbXBsZSwgaWYgYSBwbHVnaW4gaXMgYXNzaWduZWQgdG8gdGhlIG5hbWUgXCJteVBsdWdpblwiLCBpdCBjYW4gYmUgY2FsbGVkIHZpYSBgU25vd2JvYXJkLm15cGx1Z2luKClgLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG5hbWVcbiAgICAgKiBAcGFyYW0ge1BsdWdpbkJhc2V8RnVuY3Rpb259IGluc3RhbmNlXG4gICAgICovXG4gICAgYWRkUGx1Z2luKG5hbWUsIGluc3RhbmNlKSB7XG4gICAgICAgIGNvbnN0IGxvd2VyTmFtZSA9IG5hbWUudG9Mb3dlckNhc2UoKTtcblxuICAgICAgICBpZiAodGhpcy5oYXNQbHVnaW4obG93ZXJOYW1lKSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBBIHBsdWdpbiBjYWxsZWQgXCIke25hbWV9XCIgaXMgYWxyZWFkeSByZWdpc3RlcmVkLmApO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHR5cGVvZiBpbnN0YW5jZSAhPT0gJ2Z1bmN0aW9uJyAmJiBpbnN0YW5jZSBpbnN0YW5jZW9mIFBsdWdpbkJhc2UgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ1RoZSBwcm92aWRlZCBwbHVnaW4gbXVzdCBleHRlbmQgdGhlIFBsdWdpbkJhc2UgY2xhc3MsIG9yIG11c3QgYmUgYSBjYWxsYmFjayBmdW5jdGlvbi4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzW25hbWVdICE9PSB1bmRlZmluZWQgfHwgdGhpc1tsb3dlck5hbWVdICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIGdpdmVuIG5hbWUgaXMgYWxyZWFkeSBpbiB1c2UgZm9yIGEgcHJvcGVydHkgb3IgbWV0aG9kIG9mIHRoZSBTbm93Ym9hcmQgY2xhc3MuJyk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnBsdWdpbnNbbG93ZXJOYW1lXSA9IG5ldyBQbHVnaW5Mb2FkZXIobG93ZXJOYW1lLCB0aGlzLCBpbnN0YW5jZSk7XG4gICAgICAgIGNvbnN0IGNhbGxiYWNrID0gKC4uLnBhcmFtZXRlcnMpID0+IHRoaXMucGx1Z2luc1tsb3dlck5hbWVdLmdldEluc3RhbmNlKC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICB0aGlzW25hbWVdID0gY2FsbGJhY2s7XG4gICAgICAgIHRoaXNbbG93ZXJOYW1lXSA9IGNhbGxiYWNrO1xuXG4gICAgICAgIHRoaXMuZGVidWcoYFBsdWdpbiBcIiR7bmFtZX1cIiByZWdpc3RlcmVkYCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBhIHBsdWdpbi5cbiAgICAgKlxuICAgICAqIFJlbW92ZXMgYSBwbHVnaW4gZnJvbSBTbm93Ym9hcmQsIGNhbGxpbmcgdGhlIGRlc3RydWN0b3IgbWV0aG9kIGZvciBhbGwgYWN0aXZlIGluc3RhbmNlcyBvZiB0aGUgcGx1Z2luLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IG5hbWVcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICByZW1vdmVQbHVnaW4obmFtZSkge1xuICAgICAgICBjb25zdCBsb3dlck5hbWUgPSBuYW1lLnRvTG93ZXJDYXNlKCk7XG5cbiAgICAgICAgaWYgKCF0aGlzLmhhc1BsdWdpbihsb3dlck5hbWUpKSB7XG4gICAgICAgICAgICB0aGlzLmRlYnVnKGBQbHVnaW4gXCIke25hbWV9XCIgYWxyZWFkeSByZW1vdmVkYCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBDYWxsIGRlc3RydWN0b3JzIGZvciBhbGwgaW5zdGFuY2VzXG4gICAgICAgIHRoaXMucGx1Z2luc1tsb3dlck5hbWVdLmdldEluc3RhbmNlcygpLmZvckVhY2goKGluc3RhbmNlKSA9PiB7XG4gICAgICAgICAgICBpbnN0YW5jZS5kZXN0cnVjdG9yKCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGRlbGV0ZSB0aGlzLnBsdWdpbnNbbG93ZXJOYW1lXTtcbiAgICAgICAgZGVsZXRlIHRoaXNbbG93ZXJOYW1lXTtcblxuICAgICAgICB0aGlzLmRlYnVnKGBQbHVnaW4gXCIke25hbWV9XCIgcmVtb3ZlZGApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgYSBwbHVnaW4gaGFzIGJlZW4gcmVnaXN0ZXJlZCBhbmQgaXMgYWN0aXZlLlxuICAgICAqXG4gICAgICogQSBwbHVnaW4gdGhhdCBpcyBzdGlsbCB3YWl0aW5nIGZvciBkZXBlbmRlbmNpZXMgdG8gYmUgcmVnaXN0ZXJlZCB3aWxsIG5vdCBiZSBhY3RpdmUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbmFtZVxuICAgICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgICAqL1xuICAgIGhhc1BsdWdpbihuYW1lKSB7XG4gICAgICAgIGNvbnN0IGxvd2VyTmFtZSA9IG5hbWUudG9Mb3dlckNhc2UoKTtcblxuICAgICAgICByZXR1cm4gKHRoaXMucGx1Z2luc1tsb3dlck5hbWVdICE9PSB1bmRlZmluZWQpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYW4gYXJyYXkgb2YgcmVnaXN0ZXJlZCBwbHVnaW5zIGFzIFBsdWdpbkxvYWRlciBvYmplY3RzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge1BsdWdpbkxvYWRlcltdfVxuICAgICAqL1xuICAgIGdldFBsdWdpbnMoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLnBsdWdpbnM7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmV0dXJucyBhbiBhcnJheSBvZiByZWdpc3RlcmVkIHBsdWdpbnMsIGJ5IG5hbWUuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZ2V0UGx1Z2luTmFtZXMoKSB7XG4gICAgICAgIHJldHVybiBPYmplY3Qua2V5cyh0aGlzLnBsdWdpbnMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgYSBQbHVnaW5Mb2FkZXIgb2JqZWN0IG9mIGEgZ2l2ZW4gcGx1Z2luLlxuICAgICAqXG4gICAgICogQHJldHVybnMge1BsdWdpbkxvYWRlcn1cbiAgICAgKi9cbiAgICBnZXRQbHVnaW4obmFtZSkge1xuICAgICAgICBpZiAoIXRoaXMuaGFzUGx1Z2luKG5hbWUpKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYE5vIHBsdWdpbiBjYWxsZWQgXCIke25hbWV9XCIgaGFzIGJlZW4gcmVnaXN0ZXJlZC5gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzLnBsdWdpbnNbbmFtZV07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRmluZHMgYWxsIHBsdWdpbnMgdGhhdCBsaXN0ZW4gdG8gdGhlIGdpdmVuIGV2ZW50LlxuICAgICAqXG4gICAgICogVGhpcyB3b3JrcyBmb3IgYm90aCBub3JtYWwgYW5kIHByb21pc2UgZXZlbnRzLiBJdCBkb2VzIE5PVCBjaGVjayB0aGF0IHRoZSBwbHVnaW4ncyBsaXN0ZW5lciBhY3R1YWxseSBleGlzdHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gZXZlbnROYW1lXG4gICAgICogQHJldHVybnMge3N0cmluZ1tdfSBUaGUgbmFtZSBvZiB0aGUgcGx1Z2lucyB0aGF0IGFyZSBsaXN0ZW5pbmcgdG8gdGhpcyBldmVudC5cbiAgICAgKi9cbiAgICBsaXN0ZW5zVG9FdmVudChldmVudE5hbWUpIHtcbiAgICAgICAgY29uc3QgcGx1Z2lucyA9IFtdO1xuXG4gICAgICAgIE9iamVjdC5lbnRyaWVzKHRoaXMucGx1Z2lucykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IFtuYW1lLCBwbHVnaW5dID0gZW50cnk7XG5cbiAgICAgICAgICAgIGlmIChwbHVnaW4uaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoIXBsdWdpbi5oYXNNZXRob2QoJ2xpc3RlbnMnKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgbGlzdGVuZXJzID0gcGx1Z2luLmNhbGxNZXRob2QoJ2xpc3RlbnMnKTtcblxuICAgICAgICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lcnNbZXZlbnROYW1lXSA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICBwbHVnaW5zLnB1c2gobmFtZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiBwbHVnaW5zO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENhbGxzIGEgZ2xvYmFsIGV2ZW50IHRvIGFsbCByZWdpc3RlcmVkIHBsdWdpbnMuXG4gICAgICpcbiAgICAgKiBJZiBhbnkgcGx1Z2luIHJldHVybnMgYSBgZmFsc2VgLCB0aGUgZXZlbnQgaXMgY29uc2lkZXJlZCBjYW5jZWxsZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gZXZlbnROYW1lXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59IElmIGV2ZW50IHdhcyBub3QgY2FuY2VsbGVkXG4gICAgICovXG4gICAgZ2xvYmFsRXZlbnQoZXZlbnROYW1lLCAuLi5wYXJhbWV0ZXJzKSB7XG4gICAgICAgIHRoaXMuZGVidWcoYENhbGxpbmcgZ2xvYmFsIGV2ZW50IFwiJHtldmVudE5hbWV9XCJgKTtcblxuICAgICAgICAvLyBGaW5kIG91dCB3aGljaCBwbHVnaW5zIGxpc3RlbiB0byB0aGlzIGV2ZW50IC0gaWYgbm9uZSBsaXN0ZW4gdG8gaXQsIHJldHVybiB0cnVlLlxuICAgICAgICBjb25zdCBsaXN0ZW5lcnMgPSB0aGlzLmxpc3RlbnNUb0V2ZW50KGV2ZW50TmFtZSk7XG4gICAgICAgIGlmIChsaXN0ZW5lcnMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICB0aGlzLmRlYnVnKGBObyBsaXN0ZW5lcnMgZm91bmQgZm9yIGdsb2JhbCBldmVudCBcIiR7ZXZlbnROYW1lfVwiYCk7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmRlYnVnKGBMaXN0ZW5lcnMgZm91bmQgZm9yIGdsb2JhbCBldmVudCBcIiR7ZXZlbnROYW1lfVwiOiAke2xpc3RlbmVycy5qb2luKCcsICcpfWApO1xuXG4gICAgICAgIGxldCBjYW5jZWxsZWQgPSBmYWxzZTtcblxuICAgICAgICBsaXN0ZW5lcnMuZm9yRWFjaCgobmFtZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgcGx1Z2luID0gdGhpcy5nZXRQbHVnaW4obmFtZSk7XG5cbiAgICAgICAgICAgIGlmIChwbHVnaW4uaXNGdW5jdGlvbigpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKHBsdWdpbi5pc1NpbmdsZXRvbigpICYmIHBsdWdpbi5nZXRJbnN0YW5jZXMoKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICBwbHVnaW4uaW5pdGlhbGlzZVNpbmdsZXRvbigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCBsaXN0ZW5NZXRob2QgPSBwbHVnaW4uY2FsbE1ldGhvZCgnbGlzdGVucycpW2V2ZW50TmFtZV07XG5cbiAgICAgICAgICAgIC8vIENhbGwgZXZlbnQgaGFuZGxlciBtZXRob2RzIGZvciBhbGwgcGx1Z2lucywgaWYgdGhleSBoYXZlIGEgbWV0aG9kIHNwZWNpZmllZCBmb3IgdGhlIGV2ZW50LlxuICAgICAgICAgICAgcGx1Z2luLmdldEluc3RhbmNlcygpLmZvckVhY2goKGluc3RhbmNlKSA9PiB7XG4gICAgICAgICAgICAgICAgLy8gSWYgYSBwbHVnaW4gaGFzIGNhbmNlbGxlZCB0aGUgZXZlbnQsIG5vIGZ1cnRoZXIgcGx1Z2lucyBhcmUgY29uc2lkZXJlZC5cbiAgICAgICAgICAgICAgICBpZiAoY2FuY2VsbGVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoIWluc3RhbmNlW2xpc3Rlbk1ldGhvZF0pIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBNaXNzaW5nIFwiJHtsaXN0ZW5NZXRob2R9XCIgbWV0aG9kIGluIFwiJHtuYW1lfVwiIHBsdWdpbmApO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChpbnN0YW5jZVtsaXN0ZW5NZXRob2RdKC4uLnBhcmFtZXRlcnMpID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICBjYW5jZWxsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmRlYnVnKGBHbG9iYWwgZXZlbnQgXCIke2V2ZW50TmFtZX1cIiBjYW5jZWxsZWQgYnkgXCIke25hbWV9XCIgcGx1Z2luYCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiAhY2FuY2VsbGVkO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENhbGxzIGEgZ2xvYmFsIGV2ZW50IHRvIGFsbCByZWdpc3RlcmVkIHBsdWdpbnMsIGV4cGVjdGluZyBhIFByb21pc2UgdG8gYmUgcmV0dXJuZWQgYnkgYWxsLlxuICAgICAqXG4gICAgICogVGhpcyBjb2xsYXRlcyBhbGwgcGx1Z2lucyByZXNwb25zZXMgaW50byBvbmUgbGFyZ2UgUHJvbWlzZSB0aGF0IGVpdGhlciBleHBlY3RzIGFsbCB0byBiZSByZXNvbHZlZCwgb3Igb25lIHRvIHJlamVjdC5cbiAgICAgKiBJZiBubyBsaXN0ZW5lcnMgYXJlIGZvdW5kLCBhIHJlc29sdmVkIFByb21pc2UgaXMgcmV0dXJuZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gZXZlbnROYW1lXG4gICAgICovXG4gICAgZ2xvYmFsUHJvbWlzZUV2ZW50KGV2ZW50TmFtZSwgLi4ucGFyYW1ldGVycykge1xuICAgICAgICB0aGlzLmRlYnVnKGBDYWxsaW5nIGdsb2JhbCBwcm9taXNlIGV2ZW50IFwiJHtldmVudE5hbWV9XCJgKTtcblxuICAgICAgICAvLyBGaW5kIG91dCB3aGljaCBwbHVnaW5zIGxpc3RlbiB0byB0aGlzIGV2ZW50IC0gaWYgbm9uZSBsaXN0ZW4gdG8gaXQsIHJldHVybiBhIHJlc29sdmVkIHByb21pc2UuXG4gICAgICAgIGNvbnN0IGxpc3RlbmVycyA9IHRoaXMubGlzdGVuc1RvRXZlbnQoZXZlbnROYW1lKTtcbiAgICAgICAgaWYgKGxpc3RlbmVycy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHRoaXMuZGVidWcoYE5vIGxpc3RlbmVycyBmb3VuZCBmb3IgZ2xvYmFsIHByb21pc2UgZXZlbnQgXCIke2V2ZW50TmFtZX1cImApO1xuICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVzb2x2ZSgpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuZGVidWcoYExpc3RlbmVycyBmb3VuZCBmb3IgZ2xvYmFsIHByb21pc2UgZXZlbnQgXCIke2V2ZW50TmFtZX1cIjogJHtsaXN0ZW5lcnMuam9pbignLCAnKX1gKTtcblxuICAgICAgICBjb25zdCBwcm9taXNlcyA9IFtdO1xuXG4gICAgICAgIGxpc3RlbmVycy5mb3JFYWNoKChuYW1lKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBwbHVnaW4gPSB0aGlzLmdldFBsdWdpbihuYW1lKTtcblxuICAgICAgICAgICAgaWYgKHBsdWdpbi5pc0Z1bmN0aW9uKCkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAocGx1Z2luLmlzU2luZ2xldG9uKCkgJiYgcGx1Z2luLmdldEluc3RhbmNlcygpLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgICAgIHBsdWdpbi5pbml0aWFsaXNlU2luZ2xldG9uKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGxpc3Rlbk1ldGhvZCA9IHBsdWdpbi5jYWxsTWV0aG9kKCdsaXN0ZW5zJylbZXZlbnROYW1lXTtcblxuICAgICAgICAgICAgLy8gQ2FsbCBldmVudCBoYW5kbGVyIG1ldGhvZHMgZm9yIGFsbCBwbHVnaW5zLCBpZiB0aGV5IGhhdmUgYSBtZXRob2Qgc3BlY2lmaWVkIGZvciB0aGUgZXZlbnQuXG4gICAgICAgICAgICBwbHVnaW4uZ2V0SW5zdGFuY2VzKCkuZm9yRWFjaCgoaW5zdGFuY2UpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBpbnN0YW5jZVByb21pc2UgPSBpbnN0YW5jZVtsaXN0ZW5NZXRob2RdKC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICAgICAgICAgIGlmIChpbnN0YW5jZVByb21pc2UgaW5zdGFuY2VvZiBQcm9taXNlID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcHJvbWlzZXMucHVzaChpbnN0YW5jZVByb21pc2UpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGlmIChwcm9taXNlcy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBQcm9taXNlLmFsbChwcm9taXNlcyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogTG9nIGEgZGVidWcgbWVzc2FnZS5cbiAgICAgKlxuICAgICAqIFRoZXNlIG1lc3NhZ2VzIGFyZSBvbmx5IHNob3duIHdoZW4gZGVidWdnaW5nIGlzIGVuYWJsZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICBkZWJ1ZyguLi5wYXJhbWV0ZXJzKSB7XG4gICAgICAgIGlmICghdGhpcy5kZWJ1Z0VuYWJsZWQpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qIGVzbGludC1kaXNhYmxlICovXG4gICAgICAgIGNvbnNvbGUuZ3JvdXBDb2xsYXBzZWQoJyVjW1Nub3dib2FyZF0nLCAnY29sb3I6IHJnYig0NSwgMTY3LCAxOTkpOyBmb250LXdlaWdodDogbm9ybWFsOycsIC4uLnBhcmFtZXRlcnMpO1xuICAgICAgICBjb25zb2xlLnRyYWNlKCk7XG4gICAgICAgIGNvbnNvbGUuZ3JvdXBFbmQoKTtcbiAgICAgICAgLyogZXNsaW50LWVuYWJsZSAqL1xuICAgIH1cbn1cbiIsImltcG9ydCBTbm93Ym9hcmQgZnJvbSAnLi9tYWluL1Nub3dib2FyZCc7XG5cbigod2luZG93KSA9PiB7XG4gICAgY29uc3Qgc25vd2JvYXJkID0gbmV3IFNub3dib2FyZCgpO1xuXG4gICAgLy8gQ292ZXIgYWxsIGFsaWFzZXNcbiAgICB3aW5kb3cuc25vd2JvYXJkID0gc25vd2JvYXJkO1xuICAgIHdpbmRvdy5Tbm93Ym9hcmQgPSBzbm93Ym9hcmQ7XG4gICAgd2luZG93LlNub3dCb2FyZCA9IHNub3dib2FyZDtcbn0pKHdpbmRvdyk7XG4iLCJpbXBvcnQgQmFzZUNvb2tpZSBmcm9tICdqcy1jb29raWUnO1xuaW1wb3J0IFNpbmdsZXRvbiBmcm9tICcuLi9hYnN0cmFjdHMvU2luZ2xldG9uJztcblxuLyoqXG4gKiBDb29raWUgdXRpbGl0eS5cbiAqXG4gKiBUaGlzIHV0aWxpdHkgaXMgYSB0aGluIHdyYXBwZXIgYXJvdW5kIHRoZSBcImpzLWNvb2tpZVwiIGxpYnJhcnkuXG4gKlxuICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vanMtY29va2llL2pzLWNvb2tpZVxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvb2tpZSBleHRlbmRzIFNpbmdsZXRvbiB7XG4gICAgY29uc3RydWN0b3Ioc25vd2JvYXJkKSB7XG4gICAgICAgIHN1cGVyKHNub3dib2FyZCk7XG5cbiAgICAgICAgdGhpcy5kZWZhdWx0cyA9IHtcbiAgICAgICAgICAgIGV4cGlyZXM6IG51bGwsXG4gICAgICAgICAgICBwYXRoOiAnLycsXG4gICAgICAgICAgICBkb21haW46IG51bGwsXG4gICAgICAgICAgICBzZWN1cmU6IGZhbHNlLFxuICAgICAgICAgICAgc2FtZVNpdGU6ICdMYXgnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNldCB0aGUgZGVmYXVsdCBjb29raWUgcGFyYW1ldGVycyBmb3IgYWxsIHN1YnNlcXVlbnQgXCJzZXRcIiBhbmQgXCJyZW1vdmVcIiBjYWxscy5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gICAgICovXG4gICAgc2V0RGVmYXVsdHMob3B0aW9ucykge1xuICAgICAgICBpZiAodHlwZW9mIG9wdGlvbnMgIT09ICdvYmplY3QnKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0Nvb2tpZSBkZWZhdWx0cyBtdXN0IGJlIHByb3ZpZGVkIGFzIGFuIG9iamVjdCcpO1xuICAgICAgICB9XG5cbiAgICAgICAgT2JqZWN0LmVudHJpZXMob3B0aW9ucykuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IFtrZXksIHZhbHVlXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICBpZiAodGhpcy5kZWZhdWx0c1trZXldICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmRlZmF1bHRzW2tleV0gPSB2YWx1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0IHRoZSBjdXJyZW50IGRlZmF1bHQgY29va2llIHBhcmFtZXRlcnMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgICAqL1xuICAgIGdldERlZmF1bHRzKCkge1xuICAgICAgICBjb25zdCBkZWZhdWx0cyA9IHt9O1xuXG4gICAgICAgIE9iamVjdC5lbnRyaWVzKHRoaXMuZGVmYXVsdHMpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICBjb25zdCBba2V5LCB2YWx1ZV0gPSBlbnRyeTtcblxuICAgICAgICAgICAgaWYgKHRoaXMuZGVmYXVsdHNba2V5XSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGRlZmF1bHRzW2tleV0gPSB2YWx1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIGRlZmF1bHRzO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldCBhIGNvb2tpZSBieSBuYW1lLlxuICAgICAqXG4gICAgICogSWYgYG5hbWVgIGlzIHVuZGVmaW5lZCwgcmV0dXJucyBhbGwgY29va2llcyBhcyBhbiBPYmplY3QuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgICAqIEByZXR1cm5zIHtPYmplY3R8U3RyaW5nfVxuICAgICAqL1xuICAgIGdldChuYW1lKSB7XG4gICAgICAgIGlmIChuYW1lID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIGNvbnN0IGNvb2tpZXMgPSBCYXNlQ29va2llLmdldCgpO1xuXG4gICAgICAgICAgICBPYmplY3QuZW50cmllcyhjb29raWVzKS5mb3JFYWNoKChlbnRyeSkgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IFtjb29raWVOYW1lLCBjb29raWVWYWx1ZV0gPSBlbnRyeTtcblxuICAgICAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KCdjb29raWUuZ2V0JywgY29va2llTmFtZSwgY29va2llVmFsdWUsIChuZXdWYWx1ZSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb29raWVzW2Nvb2tpZU5hbWVdID0gbmV3VmFsdWU7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIGNvb2tpZXM7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgdmFsdWUgPSBCYXNlQ29va2llLmdldChuYW1lKTtcblxuICAgICAgICAvLyBBbGxvdyBwbHVnaW5zIHRvIG92ZXJyaWRlIHRoZSBnb3R0ZW4gdmFsdWVcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2Nvb2tpZS5nZXQnLCBuYW1lLCB2YWx1ZSwgKG5ld1ZhbHVlKSA9PiB7XG4gICAgICAgICAgICB2YWx1ZSA9IG5ld1ZhbHVlO1xuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gdmFsdWU7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogU2V0IGEgY29va2llIGJ5IG5hbWUuXG4gICAgICpcbiAgICAgKiBZb3UgY2FuIHNwZWNpZnkgYWRkaXRpb25hbCBjb29raWUgcGFyYW1ldGVycyB0aHJvdWdoIHRoZSBcIm9wdGlvbnNcIiBwYXJhbWV0ZXIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSB2YWx1ZVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gICAgICogQHJldHVybnMge1N0cmluZ31cbiAgICAgKi9cbiAgICBzZXQobmFtZSwgdmFsdWUsIG9wdGlvbnMpIHtcbiAgICAgICAgbGV0IHNhdmVWYWx1ZSA9IHZhbHVlO1xuXG4gICAgICAgIC8vIEFsbG93IHBsdWdpbnMgdG8gb3ZlcnJpZGUgdGhlIHZhbHVlIHRvIHNhdmVcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2Nvb2tpZS5zZXQnLCBuYW1lLCB2YWx1ZSwgKG5ld1ZhbHVlKSA9PiB7XG4gICAgICAgICAgICBzYXZlVmFsdWUgPSBuZXdWYWx1ZTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIEJhc2VDb29raWUuc2V0KG5hbWUsIHNhdmVWYWx1ZSwge1xuICAgICAgICAgICAgLi4udGhpcy5nZXREZWZhdWx0cygpLFxuICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlIGEgY29va2llIGJ5IG5hbWUuXG4gICAgICpcbiAgICAgKiBZb3UgY2FuIHNwZWNpZnkgdGhlIGFkZGl0aW9uYWwgY29va2llIHBhcmFtZXRlcnMgdmlhIHRoZSBcIm9wdGlvbnNcIiBwYXJhbWV0ZXIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgcmVtb3ZlKG5hbWUsIG9wdGlvbnMpIHtcbiAgICAgICAgQmFzZUNvb2tpZS5yZW1vdmUobmFtZSwge1xuICAgICAgICAgICAgLi4udGhpcy5nZXREZWZhdWx0cygpLFxuICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgfSk7XG4gICAgfVxufVxuIiwiaW1wb3J0IFNpbmdsZXRvbiBmcm9tICcuLi9hYnN0cmFjdHMvU2luZ2xldG9uJztcblxuLyoqXG4gKiBKU09OIFBhcnNlciB1dGlsaXR5LlxuICpcbiAqIFRoaXMgdXRpbGl0eSBwYXJzZXMgSlNPTi1saWtlIGRhdGEgdGhhdCBkb2VzIG5vdCBzdHJpY3RseSBtZWV0IHRoZSBKU09OIHNwZWNpZmljYXRpb25zIGluIG9yZGVyIHRvIHNpbXBsaWZ5IGRldmVsb3BtZW50LlxuICogSXQgaXMgYSBzYWZlIHJlcGxhY2VtZW50IGZvciBKU09OLnBhcnNlKEpTT04uc3RyaW5naWZ5KGV2YWwoXCIoe1wiICsgdmFsdWUgKyBcIn0pXCIpKSkgdGhhdCBkb2VzIG5vdCByZXF1aXJlIHRoZSB1c2Ugb2YgZXZhbCgpXG4gKlxuICogQGF1dGhvciBBeXVtaSBIYW1hc2FraVxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL29jdG9iZXJjbXMvb2N0b2Jlci9wdWxsLzQ1MjdcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSnNvblBhcnNlciBleHRlbmRzIFNpbmdsZXRvbiB7XG4gICAgY29uc3RydWN0b3Ioc25vd2JvYXJkKSB7XG4gICAgICAgIHN1cGVyKHNub3dib2FyZCk7XG5cbiAgICAgICAgLy8gQWRkIHRvIGdsb2JhbCBmdW5jdGlvbiBmb3IgYmFja3dhcmRzIGNvbXBhdGliaWxpdHlcbiAgICAgICAgd2luZG93LnduSlNPTiA9IChqc29uKSA9PiB0aGlzLnBhcnNlKGpzb24pO1xuICAgICAgICB3aW5kb3cub2NKU09OID0gd2luZG93LnduSlNPTjtcbiAgICB9XG5cbiAgICBwYXJzZShzdHIpIHtcbiAgICAgICAgY29uc3QganNvblN0cmluZyA9IHRoaXMucGFyc2VTdHJpbmcoc3RyKTtcbiAgICAgICAgcmV0dXJuIEpTT04ucGFyc2UoanNvblN0cmluZyk7XG4gICAgfVxuXG4gICAgcGFyc2VTdHJpbmcodmFsdWUpIHtcbiAgICAgICAgbGV0IHN0ciA9IHZhbHVlLnRyaW0oKTtcblxuICAgICAgICBpZiAoIXN0ci5sZW5ndGgpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignQnJva2VuIEpTT04gb2JqZWN0LicpO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IHJlc3VsdCA9ICcnO1xuICAgICAgICBsZXQgdHlwZSA9IG51bGw7XG4gICAgICAgIGxldCBrZXkgPSBudWxsO1xuICAgICAgICBsZXQgYm9keSA9ICcnO1xuXG4gICAgICAgIC8qXG4gICAgICAgICogdGhlIG1pc3Rha2UgJywnXG4gICAgICAgICovXG4gICAgICAgIHdoaWxlIChzdHIgJiYgc3RyWzBdID09PSAnLCcpIHtcbiAgICAgICAgICAgIHN0ciA9IHN0ci5zdWJzdHIoMSk7XG4gICAgICAgIH1cblxuICAgICAgICAvKlxuICAgICAgICAqIHN0cmluZ1xuICAgICAgICAqL1xuICAgICAgICBpZiAoc3RyWzBdID09PSAnXCInIHx8IHN0clswXSA9PT0gJ1xcJycpIHtcbiAgICAgICAgICAgIGlmIChzdHJbc3RyLmxlbmd0aCAtIDFdICE9PSBzdHJbMF0pIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgc3RyaW5nIEpTT04gb2JqZWN0LicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBib2R5ID0gJ1wiJztcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAxOyBpIDwgc3RyLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0cltpXSA9PT0gJ1xcXFwnKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdHJbaSArIDFdID09PSAnXFwnJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaSArIDFdO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaV07XG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaSArPSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSBzdHJbMF0pIHtcbiAgICAgICAgICAgICAgICAgICAgYm9keSArPSAnXCInO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gYm9keTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ1wiJykge1xuICAgICAgICAgICAgICAgICAgICBib2R5ICs9ICdcXFxcXCInO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2ldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdJbnZhbGlkIHN0cmluZyBKU09OIG9iamVjdC4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8qXG4gICAgICAgICogYm9vbGVhblxuICAgICAgICAqL1xuICAgICAgICBpZiAoc3RyID09PSAndHJ1ZScgfHwgc3RyID09PSAnZmFsc2UnKSB7XG4gICAgICAgICAgICByZXR1cm4gc3RyO1xuICAgICAgICB9XG5cbiAgICAgICAgLypcbiAgICAgICAgKiBudWxsXG4gICAgICAgICovXG4gICAgICAgIGlmIChzdHIgPT09ICdudWxsJykge1xuICAgICAgICAgICAgcmV0dXJuICdudWxsJztcbiAgICAgICAgfVxuXG4gICAgICAgIC8qXG4gICAgICAgICogbnVtYmVyXG4gICAgICAgICovXG4gICAgICAgIGNvbnN0IG51bSA9IHBhcnNlRmxvYXQoc3RyKTtcbiAgICAgICAgaWYgKCFOdW1iZXIuaXNOYU4obnVtKSkge1xuICAgICAgICAgICAgcmV0dXJuIG51bS50b1N0cmluZygpO1xuICAgICAgICB9XG5cbiAgICAgICAgLypcbiAgICAgICAgKiBvYmplY3RcbiAgICAgICAgKi9cbiAgICAgICAgaWYgKHN0clswXSA9PT0gJ3snKSB7XG4gICAgICAgICAgICB0eXBlID0gJ25lZWRLZXknO1xuICAgICAgICAgICAga2V5ID0gbnVsbDtcbiAgICAgICAgICAgIHJlc3VsdCA9ICd7JztcblxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDE7IGkgPCBzdHIubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc0JsYW5rQ2hhcihzdHJbaV0pKSB7XG4gICAgICAgICAgICAgICAgICAgIC8qIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSAqL1xuICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKHR5cGUgPT09ICduZWVkS2V5JyAmJiAoc3RyW2ldID09PSAnXCInIHx8IHN0cltpXSA9PT0gJ1xcJycpKSB7XG4gICAgICAgICAgICAgICAgICAgIGtleSA9IHRoaXMucGFyc2VLZXkoc3RyLCBpICsgMSwgc3RyW2ldKTtcbiAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9IGBcIiR7a2V5fVwiYDtcbiAgICAgICAgICAgICAgICAgICAgaSArPSBrZXkubGVuZ3RoO1xuICAgICAgICAgICAgICAgICAgICBpICs9IDE7XG4gICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnYWZ0ZXJLZXknO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJ25lZWRLZXknICYmIHRoaXMuY2FuQmVLZXlIZWFkKHN0cltpXSkpIHtcbiAgICAgICAgICAgICAgICAgICAga2V5ID0gdGhpcy5wYXJzZUtleShzdHIsIGkpO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9IGtleTtcbiAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9ICdcIic7XG4gICAgICAgICAgICAgICAgICAgIGkgKz0ga2V5Lmxlbmd0aCAtIDE7XG4gICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnYWZ0ZXJLZXknO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJ2FmdGVyS2V5JyAmJiBzdHJbaV0gPT09ICc6Jykge1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJzonO1xuICAgICAgICAgICAgICAgICAgICB0eXBlID0gJzonO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJzonKSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgPSB0aGlzLmdldEJvZHkoc3RyLCBpKTtcblxuICAgICAgICAgICAgICAgICAgICBpID0gaSArIGJvZHkub3JpZ2luTGVuZ3RoIC0gMTtcbiAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9IHRoaXMucGFyc2VTdHJpbmcoYm9keS5ib2R5KTtcblxuICAgICAgICAgICAgICAgICAgICB0eXBlID0gJ2FmdGVyQm9keSc7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSAnYWZ0ZXJCb2R5JyB8fCB0eXBlID09PSAnbmVlZEtleScpIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGxhc3QgPSBpO1xuICAgICAgICAgICAgICAgICAgICB3aGlsZSAoc3RyW2xhc3RdID09PSAnLCcgfHwgdGhpcy5pc0JsYW5rQ2hhcihzdHJbbGFzdF0pKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsYXN0ICs9IDE7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0cltsYXN0XSA9PT0gJ30nICYmIGxhc3QgPT09IHN0ci5sZW5ndGggLSAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB3aGlsZSAocmVzdWx0W3Jlc3VsdC5sZW5ndGggLSAxXSA9PT0gJywnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcmVzdWx0LnN1YnN0cigwLCByZXN1bHQubGVuZ3RoIC0gMSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJ30nO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAobGFzdCAhPT0gaSAmJiByZXN1bHQgIT09ICd7Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9ICcsJztcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnbmVlZEtleSc7XG4gICAgICAgICAgICAgICAgICAgICAgICBpID0gbGFzdCAtIDE7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gb2JqZWN0IG5lYXIgJHtyZXN1bHR9YCk7XG4gICAgICAgIH1cblxuICAgICAgICAvKlxuICAgICAgICAqIGFycmF5XG4gICAgICAgICovXG4gICAgICAgIGlmIChzdHJbMF0gPT09ICdbJykge1xuICAgICAgICAgICAgcmVzdWx0ID0gJ1snO1xuICAgICAgICAgICAgdHlwZSA9ICduZWVkQm9keSc7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMTsgaSA8IHN0ci5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICcgJyB8fCBzdHJbaV0gPT09ICdcXG4nIHx8IHN0cltpXSA9PT0gJ1xcdCcpIHtcbiAgICAgICAgICAgICAgICAgICAgLyogZXNsaW50LWRpc2FibGUtbmV4dC1saW5lICovXG4gICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJ25lZWRCb2R5Jykge1xuICAgICAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnLCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnbnVsbCwnO1xuICAgICAgICAgICAgICAgICAgICAgICAgLyogZXNsaW50LWRpc2FibGUtbmV4dC1saW5lICovXG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnXScgJiYgaSA9PT0gc3RyLmxlbmd0aCAtIDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyZXN1bHRbcmVzdWx0Lmxlbmd0aCAtIDFdID09PSAnLCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSByZXN1bHQuc3Vic3RyKDAsIHJlc3VsdC5sZW5ndGggLSAxKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSAnXSc7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgYm9keSA9IHRoaXMuZ2V0Qm9keShzdHIsIGkpO1xuXG4gICAgICAgICAgICAgICAgICAgIGkgPSBpICsgYm9keS5vcmlnaW5MZW5ndGggLSAxO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gdGhpcy5wYXJzZVN0cmluZyhib2R5LmJvZHkpO1xuXG4gICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnYWZ0ZXJCb2R5JztcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHR5cGUgPT09ICdhZnRlckJvZHknKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICcsJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9ICcsJztcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGUgPSAnbmVlZEJvZHknO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBkZWFsIHdpdGggbWlzdGFrZSBcIixcIlxuICAgICAgICAgICAgICAgICAgICAgICAgd2hpbGUgKHN0cltpICsgMV0gPT09ICcsJyB8fCB0aGlzLmlzQmxhbmtDaGFyKHN0cltpICsgMV0pKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHN0cltpICsgMV0gPT09ICcsJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJ251bGwsJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaSArPSAxO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ10nICYmIGkgPT09IHN0ci5sZW5ndGggLSAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgKz0gJ10nO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBhcnJheSBuZWFyICR7cmVzdWx0fWApO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuICcnO1xuICAgIH1cblxuICAgIGdldEJvZHkoc3RyLCBwb3MpIHtcbiAgICAgICAgbGV0IGJvZHkgPSAnJztcblxuICAgICAgICAvLyBwYXJzZSBzdHJpbmcgYm9keVxuICAgICAgICBpZiAoc3RyW3Bvc10gPT09ICdcIicgfHwgc3RyW3Bvc10gPT09ICdcXCcnKSB7XG4gICAgICAgICAgICBib2R5ID0gc3RyW3Bvc107XG5cbiAgICAgICAgICAgIGZvciAobGV0IGkgPSBwb3MgKyAxOyBpIDwgc3RyLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0cltpXSA9PT0gJ1xcXFwnKSB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHkgKz0gc3RyW2ldO1xuICAgICAgICAgICAgICAgICAgICBpZiAoaSArIDEgPCBzdHIubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaSArPSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoc3RyW2ldID09PSBzdHJbcG9zXSkge1xuICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltwb3NdO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgb3JpZ2luTGVuZ3RoOiBib2R5Lmxlbmd0aCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHksXG4gICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaV07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIHN0cmluZyBib2R5IG5lYXIgJHtib2R5fWApO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gcGFyc2UgdHJ1ZSAvIGZhbHNlXG4gICAgICAgIGlmIChzdHJbcG9zXSA9PT0gJ3QnKSB7XG4gICAgICAgICAgICBpZiAoc3RyLmluZGV4T2YoJ3RydWUnLCBwb3MpID09PSBwb3MpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBvcmlnaW5MZW5ndGg6ICd0cnVlJy5sZW5ndGgsXG4gICAgICAgICAgICAgICAgICAgIGJvZHk6ICd0cnVlJyxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIGJvb2xlYW4gYm9keSBuZWFyICR7c3RyLnN1YnN0cigwLCBwb3MgKyAxMCl9YCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHN0cltwb3NdID09PSAnZicpIHtcbiAgICAgICAgICAgIGlmIChzdHIuaW5kZXhPZignZicsIHBvcykgPT09IHBvcykge1xuICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgIG9yaWdpbkxlbmd0aDogJ2ZhbHNlJy5sZW5ndGgsXG4gICAgICAgICAgICAgICAgICAgIGJvZHk6ICdmYWxzZScsXG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBib29sZWFuIGJvZHkgbmVhciAke3N0ci5zdWJzdHIoMCwgcG9zICsgMTApfWApO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gcGFyc2UgbnVsbFxuICAgICAgICBpZiAoc3RyW3Bvc10gPT09ICduJykge1xuICAgICAgICAgICAgaWYgKHN0ci5pbmRleE9mKCdudWxsJywgcG9zKSA9PT0gcG9zKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgb3JpZ2luTGVuZ3RoOiAnbnVsbCcubGVuZ3RoLFxuICAgICAgICAgICAgICAgICAgICBib2R5OiAnbnVsbCcsXG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBib29sZWFuIGJvZHkgbmVhciAke3N0ci5zdWJzdHIoMCwgcG9zICsgMTApfWApO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gcGFyc2UgbnVtYmVyXG4gICAgICAgIGlmIChzdHJbcG9zXSA9PT0gJy0nIHx8IHN0cltwb3NdID09PSAnKycgfHwgc3RyW3Bvc10gPT09ICcuJyB8fCAoc3RyW3Bvc10gPj0gJzAnICYmIHN0cltwb3NdIDw9ICc5JykpIHtcbiAgICAgICAgICAgIGJvZHkgPSAnJztcblxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IHBvczsgaSA8IHN0ci5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgICAgIGlmIChzdHJbaV0gPT09ICctJyB8fCBzdHJbaV0gPT09ICcrJyB8fCBzdHJbaV0gPT09ICcuJyB8fCAoc3RyW2ldID49ICcwJyAmJiBzdHJbaV0gPD0gJzknKSkge1xuICAgICAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpXTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgb3JpZ2luTGVuZ3RoOiBib2R5Lmxlbmd0aCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHksXG4gICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OIG51bWJlciBib2R5IG5lYXIgJHtib2R5fWApO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gcGFyc2Ugb2JqZWN0XG4gICAgICAgIGlmIChzdHJbcG9zXSA9PT0gJ3snIHx8IHN0cltwb3NdID09PSAnWycpIHtcbiAgICAgICAgICAgIGNvbnN0IHN0YWNrID0gW1xuICAgICAgICAgICAgICAgIHN0cltwb3NdLFxuICAgICAgICAgICAgXTtcbiAgICAgICAgICAgIGJvZHkgPSBzdHJbcG9zXTtcblxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IHBvcyArIDE7IGkgPCBzdHIubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICBib2R5ICs9IHN0cltpXTtcbiAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnXFxcXCcpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGkgKyAxIDwgc3RyLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYm9keSArPSBzdHJbaSArIDFdO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGkgKz0gMTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ1wiJykge1xuICAgICAgICAgICAgICAgICAgICBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gPT09ICdcIicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnBvcCgpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdICE9PSAnXFwnJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucHVzaChzdHJbaV0pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChzdHJbaV0gPT09ICdcXCcnKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChzdGFja1tzdGFjay5sZW5ndGggLSAxXSA9PT0gJ1xcJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnBvcCgpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdICE9PSAnXCInKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdGFjay5wdXNoKHN0cltpXSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdICE9PSAnXCInICYmIHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdICE9PSAnXFwnJykge1xuICAgICAgICAgICAgICAgICAgICBpZiAoc3RyW2ldID09PSAneycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnB1c2goJ3snKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChzdHJbaV0gPT09ICd9Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHN0YWNrW3N0YWNrLmxlbmd0aCAtIDFdID09PSAneycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdGFjay5wb3AoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiAkeyhzdHJbcG9zXSA9PT0gJ3snID8gJ29iamVjdCcgOiAnYXJyYXknKX0gYm9keSBuZWFyICR7Ym9keX1gKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChzdHJbaV0gPT09ICdbJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhY2sucHVzaCgnWycpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHN0cltpXSA9PT0gJ10nKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoc3RhY2tbc3RhY2subGVuZ3RoIC0gMV0gPT09ICdbJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YWNrLnBvcCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEJyb2tlbiBKU09OICR7KHN0cltwb3NdID09PSAneycgPyAnb2JqZWN0JyA6ICdhcnJheScpfSBib2R5IG5lYXIgJHtib2R5fWApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmICghc3RhY2subGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBvcmlnaW5MZW5ndGg6IGkgLSBwb3MsXG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5LFxuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiAkeyhzdHJbcG9zXSA9PT0gJ3snID8gJ29iamVjdCcgOiAnYXJyYXknKX0gYm9keSBuZWFyICR7Ym9keX1gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRocm93IG5ldyBFcnJvcihgQnJva2VuIEpTT04gYm9keSBuZWFyICR7c3RyLnN1YnN0cigocG9zIC0gNSA+PSAwKSA/IHBvcyAtIDUgOiAwLCA1MCl9YCk7XG4gICAgfVxuXG4gICAgcGFyc2VLZXkoc3RyLCBwb3MsIHF1b3RlKSB7XG4gICAgICAgIGxldCBrZXkgPSAnJztcblxuICAgICAgICBmb3IgKGxldCBpID0gcG9zOyBpIDwgc3RyLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICBpZiAocXVvdGUgJiYgcXVvdGUgPT09IHN0cltpXSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBrZXk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoIXF1b3RlICYmIChzdHJbaV0gPT09ICcgJyB8fCBzdHJbaV0gPT09ICc6JykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4ga2V5O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBrZXkgKz0gc3RyW2ldO1xuXG4gICAgICAgICAgICBpZiAoc3RyW2ldID09PSAnXFxcXCcgJiYgaSArIDEgPCBzdHIubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAga2V5ICs9IHN0cltpICsgMV07XG4gICAgICAgICAgICAgICAgaSArPSAxO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBCcm9rZW4gSlNPTiBzeW50YXggbmVhciAke2tleX1gKTtcbiAgICB9XG5cbiAgICBjYW5CZUtleUhlYWQoY2gpIHtcbiAgICAgICAgaWYgKGNoWzBdID09PSAnXFxcXCcpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoKGNoWzBdID49ICdhJyAmJiBjaFswXSA8PSAneicpIHx8IChjaFswXSA+PSAnQScgJiYgY2hbMF0gPD0gJ1onKSB8fCBjaFswXSA9PT0gJ18nKSB7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY2hbMF0gPj0gJzAnICYmIGNoWzBdIDw9ICc5Jykge1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGNoWzBdID09PSAnJCcpIHtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChjaC5jaGFyQ29kZUF0KDApID4gMjU1KSB7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICBpc0JsYW5rQ2hhcihjaCkge1xuICAgICAgICByZXR1cm4gY2ggPT09ICcgJyB8fCBjaCA9PT0gJ1xcbicgfHwgY2ggPT09ICdcXHQnO1xuICAgIH1cbn1cbiIsImltcG9ydCBTaW5nbGV0b24gZnJvbSAnLi4vYWJzdHJhY3RzL1NpbmdsZXRvbic7XG5cbi8qKlxuICogU2FuaXRpemVyIHV0aWxpdHkuXG4gKlxuICogQ2xpZW50LXNpZGUgSFRNTCBzYW5pdGl6ZXIgZGVzaWduZWQgbW9zdGx5IHRvIHByZXZlbnQgc2VsZi1YU1MgYXR0YWNrcy5cbiAqIFRoZSBzYW5pdGl6ZXIgdXRpbGl0eSB3aWxsIHN0cmlwIGFsbCBhdHRyaWJ1dGVzIHRoYXQgc3RhcnQgd2l0aCBgb25gICh1c3VhbGx5IEpTIGV2ZW50IGhhbmRsZXJzIGFzIGF0dHJpYnV0ZXMsIGkuZS4gYG9ubG9hZGAgb3IgYG9uZXJyb3JgKSBvciBjb250YWluIHRoZSBgamF2YXNjcmlwdDpgIHBzZXVkbyBwcm90b2NvbCBpbiB0aGVpciB2YWx1ZXMuXG4gKlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2FuaXRpemVyIGV4dGVuZHMgU2luZ2xldG9uIHtcbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICAvLyBBZGQgdG8gZ2xvYmFsIGZ1bmN0aW9uIGZvciBiYWNrd2FyZHMgY29tcGF0aWJpbGl0eVxuICAgICAgICB3aW5kb3cud25TYW5pdGl6ZSA9IChodG1sKSA9PiB0aGlzLnNhbml0aXplKGh0bWwpO1xuICAgICAgICB3aW5kb3cub2NTYW5pdGl6ZSA9IHdpbmRvdy53blNhbml0aXplO1xuICAgIH1cblxuICAgIHNhbml0aXplKGh0bWwsIGJvZHlPbmx5KSB7XG4gICAgICAgIGNvbnN0IHBhcnNlciA9IG5ldyBET01QYXJzZXIoKTtcbiAgICAgICAgY29uc3QgZG9tID0gcGFyc2VyLnBhcnNlRnJvbVN0cmluZyhodG1sLCAndGV4dC9odG1sJyk7XG4gICAgICAgIGNvbnN0IHJldHVybkJvZHlPbmx5ID0gKGJvZHlPbmx5ICE9PSB1bmRlZmluZWQgJiYgdHlwZW9mIGJvZHlPbmx5ID09PSAnYm9vbGVhbicpXG4gICAgICAgICAgICA/IGJvZHlPbmx5XG4gICAgICAgICAgICA6IHRydWU7XG5cbiAgICAgICAgdGhpcy5zYW5pdGl6ZU5vZGUoZG9tLmdldFJvb3ROb2RlKCkpO1xuXG4gICAgICAgIHJldHVybiAocmV0dXJuQm9keU9ubHkpID8gZG9tLmJvZHkuaW5uZXJIVE1MIDogZG9tLmlubmVySFRNTDtcbiAgICB9XG5cbiAgICBzYW5pdGl6ZU5vZGUobm9kZSkge1xuICAgICAgICBpZiAobm9kZS50YWdOYW1lID09PSAnU0NSSVBUJykge1xuICAgICAgICAgICAgbm9kZS5yZW1vdmUoKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMudHJpbUF0dHJpYnV0ZXMobm9kZSk7XG5cbiAgICAgICAgY29uc3QgY2hpbGRyZW4gPSBBcnJheS5mcm9tKG5vZGUuY2hpbGRyZW4pO1xuXG4gICAgICAgIGNoaWxkcmVuLmZvckVhY2goKGNoaWxkKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNhbml0aXplTm9kZShjaGlsZCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIHRyaW1BdHRyaWJ1dGVzKG5vZGUpIHtcbiAgICAgICAgaWYgKCFub2RlLmF0dHJpYnV0ZXMpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgbm9kZS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICBjb25zdCBhdHRyTmFtZSA9IG5vZGUuYXR0cmlidXRlcy5pdGVtKGkpLm5hbWU7XG4gICAgICAgICAgICBjb25zdCBhdHRyVmFsdWUgPSBub2RlLmF0dHJpYnV0ZXMuaXRlbShpKS52YWx1ZTtcblxuICAgICAgICAgICAgLypcbiAgICAgICAgICAgICogcmVtb3ZlIGF0dHJpYnV0ZXMgd2hlcmUgdGhlIG5hbWVzIHN0YXJ0IHdpdGggXCJvblwiIChmb3IgZXhhbXBsZTogb25sb2FkLCBvbmVycm9yLi4uKVxuICAgICAgICAgICAgKiByZW1vdmUgYXR0cmlidXRlcyB3aGVyZSB0aGUgdmFsdWUgc3RhcnRzIHdpdGggdGhlIFwiamF2YXNjcmlwdDpcIiBwc2V1ZG8gcHJvdG9jb2wgKGZvciBleGFtcGxlIGhyZWY9XCJqYXZhc2NyaXB0OmFsZXJ0KDEpXCIpXG4gICAgICAgICAgICAqL1xuICAgICAgICAgICAgLyogZXNsaW50LWRpc2FibGUtbmV4dC1saW5lICovXG4gICAgICAgICAgICBpZiAoYXR0ck5hbWUuaW5kZXhPZignb24nKSA9PT0gMCB8fCBhdHRyVmFsdWUuaW5kZXhPZignamF2YXNjcmlwdDonKSA9PT0gMCkge1xuICAgICAgICAgICAgICAgIG5vZGUucmVtb3ZlQXR0cmlidXRlKGF0dHJOYW1lKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cbn1cbiJdLCJuYW1lcyI6WyJQbHVnaW5CYXNlIiwic25vd2JvYXJkIiwiZGV0YWNoIiwiU2luZ2xldG9uIiwiUGx1Z2luTG9hZGVyIiwibmFtZSIsImluc3RhbmNlIiwiaW5zdGFuY2VzIiwic2luZ2xldG9uIiwicHJvdG90eXBlIiwibW9ja3MiLCJvcmlnaW5hbEZ1bmN0aW9ucyIsIm1ldGhvZE5hbWUiLCJpc0Z1bmN0aW9uIiwicGFyYW1ldGVycyIsImFyZ3MiLCJzaGlmdCIsImRlcGVuZGVuY2llc0Z1bGZpbGxlZCIsInVubWV0IiwiZ2V0RGVwZW5kZW5jaWVzIiwiZmlsdGVyIiwiaXRlbSIsImdldFBsdWdpbk5hbWVzIiwiaW5jbHVkZXMiLCJFcnJvciIsImpvaW4iLCJpc1NpbmdsZXRvbiIsImxlbmd0aCIsImluaXRpYWxpc2VTaW5nbGV0b24iLCJPYmplY3QiLCJrZXlzIiwiZW50cmllcyIsImZvckVhY2giLCJlbnRyeSIsImNhbGxiYWNrIiwicGFyYW1zIiwibmV3SW5zdGFuY2UiLCJzcGxpY2UiLCJpbmRleE9mIiwicHVzaCIsImRlcGVuZGVuY2llcyIsIm1hcCIsInRvTG93ZXJDYXNlIiwiZnVsZmlsbGVkIiwicGx1Z2luIiwiaGFzUGx1Z2luIiwiQ29va2llIiwiSnNvblBhcnNlciIsIlNhbml0aXplciIsIlNub3dib2FyZCIsImF1dG9TaW5nbGV0b25zIiwiZGVidWciLCJkZWJ1Z0VuYWJsZWQiLCJhdXRvSW5pdFNpbmdsZXRvbnMiLCJwbHVnaW5zIiwiYXR0YWNoQWJzdHJhY3RzIiwibG9hZFV0aWxpdGllcyIsImluaXRpYWxpc2UiLCJhZGRQbHVnaW4iLCJ3aW5kb3ciLCJhZGRFdmVudExpc3RlbmVyIiwiaW5pdGlhbGlzZVNpbmdsZXRvbnMiLCJnbG9iYWxFdmVudCIsInZhbHVlcyIsImxvd2VyTmFtZSIsInVuZGVmaW5lZCIsImdldEluc3RhbmNlIiwiZ2V0SW5zdGFuY2VzIiwiZGVzdHJ1Y3RvciIsImV2ZW50TmFtZSIsImhhc01ldGhvZCIsImxpc3RlbmVycyIsImNhbGxNZXRob2QiLCJsaXN0ZW5zVG9FdmVudCIsImNhbmNlbGxlZCIsImdldFBsdWdpbiIsImxpc3Rlbk1ldGhvZCIsIlByb21pc2UiLCJyZXNvbHZlIiwicHJvbWlzZXMiLCJpbnN0YW5jZVByb21pc2UiLCJhbGwiLCJjb25zb2xlIiwiZ3JvdXBDb2xsYXBzZWQiLCJ0cmFjZSIsImdyb3VwRW5kIiwiU25vd0JvYXJkIiwiQmFzZUNvb2tpZSIsImRlZmF1bHRzIiwiZXhwaXJlcyIsInBhdGgiLCJkb21haW4iLCJzZWN1cmUiLCJzYW1lU2l0ZSIsIm9wdGlvbnMiLCJrZXkiLCJ2YWx1ZSIsImNvb2tpZXMiLCJnZXQiLCJjb29raWVOYW1lIiwiY29va2llVmFsdWUiLCJuZXdWYWx1ZSIsInNhdmVWYWx1ZSIsInNldCIsImdldERlZmF1bHRzIiwicmVtb3ZlIiwid25KU09OIiwianNvbiIsInBhcnNlIiwib2NKU09OIiwic3RyIiwianNvblN0cmluZyIsInBhcnNlU3RyaW5nIiwiSlNPTiIsInRyaW0iLCJyZXN1bHQiLCJ0eXBlIiwiYm9keSIsInN1YnN0ciIsImkiLCJudW0iLCJwYXJzZUZsb2F0IiwiTnVtYmVyIiwiaXNOYU4iLCJ0b1N0cmluZyIsImlzQmxhbmtDaGFyIiwicGFyc2VLZXkiLCJjYW5CZUtleUhlYWQiLCJnZXRCb2R5Iiwib3JpZ2luTGVuZ3RoIiwibGFzdCIsInBvcyIsInN0YWNrIiwicG9wIiwicXVvdGUiLCJjaCIsImNoYXJDb2RlQXQiLCJ3blNhbml0aXplIiwiaHRtbCIsInNhbml0aXplIiwib2NTYW5pdGl6ZSIsImJvZHlPbmx5IiwicGFyc2VyIiwiRE9NUGFyc2VyIiwiZG9tIiwicGFyc2VGcm9tU3RyaW5nIiwicmV0dXJuQm9keU9ubHkiLCJzYW5pdGl6ZU5vZGUiLCJnZXRSb290Tm9kZSIsImlubmVySFRNTCIsIm5vZGUiLCJ0YWdOYW1lIiwidHJpbUF0dHJpYnV0ZXMiLCJjaGlsZHJlbiIsIkFycmF5IiwiZnJvbSIsImNoaWxkIiwiYXR0cmlidXRlcyIsImF0dHJOYW1lIiwiYXR0clZhbHVlIiwicmVtb3ZlQXR0cmlidXRlIl0sInNvdXJjZVJvb3QiOiIifQ==