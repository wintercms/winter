function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _construct(Parent, args, Class) { if (_isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * Module abstract.
 *
 * This class provides the base functionality for all modules.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Module = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * The constructor is provided the Winter framework instance.
   *
   * @param {Winter} winter
   */
  function Module(winter) {
    _classCallCheck(this, Module);

    this.winter = winter;
  }
  /**
   * Defines the required modules for this specific module to work.
   *
   * @returns {string[]} An array of modules required for this module to work, as strings.
   */


  _createClass(Module, [{
    key: "dependencies",
    value: function dependencies() {
      return [];
    }
    /**
     * Ready event callback.
     *
     * Fired when the DOM is fully loaded.
     */

  }, {
    key: "ready",
    value: function ready() {}
    /**
     * Destructor.
     *
     * Fired when this module is removed.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      this.detach();
      delete this.winter;
    }
  }]);

  return Module;
}();
/**
 * Singleton module abstract.
 *
 * This is a special definition class that the Winter framework will use to interpret the current module as a
 * "singleton". This will ensure that only one instance of the module class is used across the board.
 *
 * Singletons are initialised on the "domReady" event.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */


var Singleton = /*#__PURE__*/function (_Module) {
  _inherits(Singleton, _Module);

  var _super = _createSuper(Singleton);

  function Singleton() {
    _classCallCheck(this, Singleton);

    return _super.apply(this, arguments);
  }

  return Singleton;
}(Module);
/**
 * Module factory class.
 *
 * This is a provider class for a single module and provides the link between Winter framework functionality and the
 * underlying module instances.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */


var ModuleFactory = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * Binds the Winter framework to the instance.
   *
   * @param {string} name
   * @param {Winter} winter
   * @param {Module} instance
   */
  function ModuleFactory(name, winter, instance) {
    _classCallCheck(this, ModuleFactory);

    this.name = name;
    this.winter = winter;
    this.instance = instance;
    this.instances = [];
    this.singleton = instance.prototype instanceof Singleton;
  }
  /**
   * Determines if the current module has a specific method available.
   *
   * Returns false if the current module is a callback function.
   *
   * @param {string} methodName
   * @returns {boolean}
   */


  _createClass(ModuleFactory, [{
    key: "hasMethod",
    value: function hasMethod(methodName) {
      if (this.isFunction()) {
        return false;
      }

      return typeof this.instance.prototype[methodName] === 'function';
    }
    /**
     * Returns an instance of the current module.
     *
     * If this is a callback function module, the function will be returned.
     *
     * If this is a singleton, the single instance of the module will be returned.
     *
     * @returns {Module|Function}
     */

  }, {
    key: "getInstance",
    value: function getInstance() {
      var _this = this;

      if (this.isFunction()) {
        return this.instance.apply(this, arguments);
      }

      if (!this.dependenciesFulfilled()) {
        var unmet = this.getDependencies().filter(function (item) {
          return !_this.winter.getModuleNames().includes(item);
        });
        throw new Error("The \"".concat(this.name, "\" module requires the following modules: ").concat(unmet.join(', ')));
      }

      if (this.singleton) {
        if (this.instances.length === 0) {
          var _newInstance = _construct(this.instance, [this.winter].concat(Array.prototype.slice.call(arguments)));

          _newInstance.detach = function () {
            return _this.instances.splice(_this.instances.indexOf(_newInstance), 1);
          };

          this.instances.push(_newInstance);
        }

        return this.instances[0];
      }

      var newInstance = _construct(this.instance, [this.winter].concat(Array.prototype.slice.call(arguments)));

      newInstance.detach = function () {
        return _this.instances.splice(_this.instances.indexOf(newInstance), 1);
      };

      this.instances.push(newInstance);
      return newInstance;
    }
    /**
     * Gets all instances of the current module.
     *
     * If this module is a callback function module, an empty array will be returned.
     *
     * @returns {Module[]}
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
     * Determines if the current module is a simple callback function.
     *
     * @returns {boolean}
     */

  }, {
    key: "isFunction",
    value: function isFunction() {
      return typeof this.instance === 'function' && this.instance.prototype instanceof Module === false;
    }
    /**
     * Determines if the current module is a singleton.
     *
     * @returns {boolean}
     */

  }, {
    key: "isSingleton",
    value: function isSingleton() {
      return this.instance.prototype instanceof Singleton === true;
    }
    /**
     * Gets the dependencies of the current module.
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
     * Determines if the current module has all its dependencies fulfilled.
     *
     * @returns {boolean}
     */

  }, {
    key: "dependenciesFulfilled",
    value: function dependenciesFulfilled() {
      var _this2 = this;

      var dependencies = this.getDependencies();
      var fulfilled = true;
      dependencies.forEach(function (module) {
        if (!_this2.winter.hasModule(module)) {
          fulfilled = false;
        }
      });
      return fulfilled;
    }
  }]);

  return ModuleFactory;
}();
/**
 * Winter JavaScript framework.
 *
 * This class represents the base of a modern take on the Winter JS framework, being fully modular and taking advantage
 * of modern JavaScript features by leveraging the Laravel Mix compilation framework. It also is coded up to remove the
 * dependency of jQuery.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com/docs/ajax/introduction
 */


var Winter = /*#__PURE__*/function () {
  /**
   * Constructor.
   *
   * @param {boolean} debug Whether debugging logs should be shown.
   */
  function Winter(debug) {
    _classCallCheck(this, Winter);

    /* develblock:start */
    this.debugEnabled = typeof debug === 'boolean' && debug === true ? true : false;
    /* develblock:end */

    this.modules = {};
    this.initialise();
    /* develblock:start */

    this.debug('Winter framework initialised');
    /* develblock:end */
  }
  /**
   * Initialises the framework.
   *
   * Attaches a listener for the DOM being ready and triggers a global "ready" event for modules to begin attaching
   * themselves to the DOM.
   */


  _createClass(Winter, [{
    key: "initialise",
    value: function initialise() {
      var _this3 = this;

      window.addEventListener('DOMContentLoaded', function () {
        _this3.initialiseSingletons();

        _this3.globalEvent('ready');
      });
    }
    /**
     * Initialises an instance of every singleton.
     */

  }, {
    key: "initialiseSingletons",
    value: function initialiseSingletons() {
      Object.values(this.modules).forEach(function (mod) {
        if (mod.isSingleton()) {
          mod.getInstance();
        }
      });
    }
    /**
     * Add a module to the framework.
     *
     * Modules are the cornerstone for additional functionality for the Winter JavaScript framework. A module must either
     * be a ES2015 class that extends the Module abstract class, or a simple callback function.
     *
     * For module classes, you can opt to extend the Singleton abstract class to define the module as a singleton, in
     * which the same instance will be used for all uses of the module.
     *
     * @param {string} name
     * @param {ModuleAbstract|Function} instance
     */

  }, {
    key: "addModule",
    value: function addModule(name, instance) {
      var lowerName = name.toLowerCase();

      if (this.hasModule(lowerName)) {
        throw new Error("A module called \"".concat(name, "\" is already registered."));
      }

      if (typeof instance !== 'function' && instance instanceof ModuleAbstract === false) {
        throw new Error("The provided module must be a Module class instance or callback function.");
      }

      this.modules[lowerName] = new ModuleFactory(lowerName, this, instance);

      this[lowerName] = function () {
        var _this$modules$lowerNa;

        return (_this$modules$lowerNa = this.modules[lowerName]).getInstance.apply(_this$modules$lowerNa, arguments);
      };
      /* develblock:start */


      this.debug("Module \"".concat(name, "\" registered"));
      /* develblock:end */
    }
    /**
     * Removes a module.
     *
     * Removes a module from the Winter JavaScript framework, calling the destructor method for all active instances of
     * the module.
     *
     * @param {string} name
     * @returns {void}
     */

  }, {
    key: "removeModule",
    value: function removeModule(name) {
      var lowerName = name.toLowerCase();

      if (this.modules[lowerName] === undefined) {
        /* develblock:start */
        this.debug("Module \"".concat(name, "\" already removed"));
        /* develblock:end */

        return;
      } // Call destructors for all instances


      this.modules[lowerName].getInstances().forEach(function (instance) {
        instance.destructor();
      });
      delete this.modules[lowerName];
      delete this[lowerName];
      /* develblock:start */

      this.debug("Module \"".concat(name, "\" removed"));
      /* develblock:end */
    }
    /**
     * Determines if a module has been registered and is active.
     *
     * A module that is still waiting for dependencies to be registered will not be active.
     *
     * @param {string} name
     * @returns {boolean}
     */

  }, {
    key: "hasModule",
    value: function hasModule(name) {
      var lowerName = name.toLowerCase();
      return this.modules[lowerName] !== undefined;
    }
    /**
     * Returns an array of registered modules as ModuleFactory objects.
     *
     * @returns {ModuleFactory[]}
     */

  }, {
    key: "getModules",
    value: function getModules() {
      return this.modules;
    }
    /**
     * Returns an array of registered modules, by name.
     *
     * @returns {string[]}
     */

  }, {
    key: "getModuleNames",
    value: function getModuleNames() {
      return Object.keys(this.modules);
    }
    /**
     * Finds all modules that listen to the given event.
     *
     * This works for both normal and promise events.
     *
     * @param {string} eventName
     * @returns {string[]} The name of the modules that are listening to this event.
     */

  }, {
    key: "listensToEvent",
    value: function listensToEvent(eventName) {
      var modules = [];

      for (var _i = 0, _Object$entries = Object.entries(this.modules); _i < _Object$entries.length; _i++) {
        var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
            name = _Object$entries$_i[0],
            mod = _Object$entries$_i[1];

        if (mod.isFunction()) {
          continue;
        }

        if (mod.hasMethod(eventName)) {
          modules.push(name);
        }
      }

      return modules;
    }
    /**
     * Calls a global event to all registered modules.
     *
     * If any module returns a `false`, the event is considered cancelled.
     *
     * @param {string} eventName
     * @returns {boolean} If event was not cancelled
     */

  }, {
    key: "globalEvent",
    value: function globalEvent(eventName) {
      /* develblock:start */
      this.debug("Calling global event \"".concat(eventName, "\""));
      /* develblock:end */

      var cancelled = false;
      var args = Array.from(arguments);
      args.shift();
      Object.values(this.modules).forEach(function (mod) {
        if (mod.isFunction()) {
          return;
        } // Call event handler methods for all modules, if they have a method specified for the event.


        if (mod.hasMethod(eventName)) {
          mod.getInstances().forEach(function (instance) {
            // If a module has cancelled the event, no further modules are considered.
            if (cancelled) {
              return;
            }

            if (instance[eventName].apply(instance, _toConsumableArray(args)) === false) {
              cancelled = true;
            }
          });
        }
      });
      return !cancelled;
    }
    /**
     * Calls a global event to all registered modules, expecting a Promise to be returned by all.
     *
     * This collates all module responses into one large Promise that either expects all to be resolved, or one to reject.
     * If no listeners are found, a resolved Promise is returned.
     *
     * @param {string} eventName
     */

  }, {
    key: "globalPromiseEvent",
    value: function globalPromiseEvent(eventName) {
      /* develblock:start */
      this.debug("Calling global promise event \"".concat(eventName, "\""));
      /* develblock:end */

      var promises = [];
      var args = Array.from(arguments);
      args.shift();
      Object.values(this.modules).forEach(function (mod) {
        if (mod.isFunction()) {
          return;
        } // Call event handler methods for all modules, if they have a method specified for the event.


        if (mod.hasMethod(eventName)) {
          mod.getInstances().forEach(function (instance) {
            var instancePromise = instance[eventName].apply(instance, _toConsumableArray(args));

            if (instancePromise instanceof Promise === false) {
              return;
            }

            promises.push(instancePromise);
          });
        }
      });

      if (promises.length === 0) {
        return Promise.resolve();
      }

      return Promise.all(promises);
    }
    /* develblock:start */

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

      (_console = console).log.apply(_console, ["%c[Winter]", "color: rgb(45, 167, 199);"].concat(Array.prototype.slice.call(arguments)));
    }
    /* develblock:end */

  }]);

  return Winter;
}();

window.winter = new Winter();

(function (winter) {
  winter.addModule('debounce', function (fn) {
    // This holds the requestAnimationFrame reference, so we can cancel it if we wish
    var frame; // The debounce function returns a new function that can receive a variable number of arguments

    return function () {
      for (var _len = arguments.length, params = new Array(_len), _key = 0; _key < _len; _key++) {
        params[_key] = arguments[_key];
      }

      // If the frame variable has been defined, clear it now, and queue for next frame
      if (frame) {
        cancelAnimationFrame(frame);
      } // Queue our function call for the next frame


      frame = requestAnimationFrame(function () {
        // Call our function and pass any params we received
        fn.apply(void 0, params);
      });
    };
  });
})(window.winter);

(function (winter) {
  var JsonParser = /*#__PURE__*/function (_Singleton) {
    _inherits(JsonParser, _Singleton);

    var _super2 = _createSuper(JsonParser);

    function JsonParser(winter) {
      var _this4;

      _classCallCheck(this, JsonParser);

      _this4 = _super2.call(this, winter); // Add to global function for backwards compatibility

      window.wnJSON = function (json) {
        return _this4.parse(json);
      };

      return _this4;
    }

    _createClass(JsonParser, [{
      key: "parse",
      value: function parse(str) {
        var jsonString = this.parseString(str);
        return JSON.parse(jsonString);
      }
    }, {
      key: "parseString",
      value: function parseString(str) {
        str = str.trim();

        if (!str.length) {
          throw new Error('Broken JSON object.');
        }

        var result = '',
            type = null,
            key = null,
            body = '';
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

          for (var i = 1; i < str.length; i++) {
            if (str[i] === '\\') {
              if (str[i + 1] === '\'') {
                body += str[i + 1];
              } else {
                body += str[i];
                body += str[i + 1];
              }

              i++;
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

        if (!isNaN(num)) {
          return num.toString();
        }
        /*
        * object
        */


        if (str[0] === '{') {
          type = 'needKey';
          key = null;
          result = '{';

          for (var i = 1; i < str.length; i++) {
            if (this.isBlankChar(str[i])) {
              continue;
            } else if (type === 'needKey' && (str[i] === '"' || str[i] === '\'')) {
              key = this.parseKey(str, i + 1, str[i]);
              result += '"' + key + '"';
              i += key.length;
              i += 1;
              type = 'afterKey';
            } else if (type === 'needKey' && this.canBeKeyHead(str[i])) {
              key = this.parseKey(str, i);
              result += '"';
              result += key;
              result += '"';
              i += key.length - 1;
              type = 'afterKey';
            } else if (type === 'afterKey' && str[i] === ':') {
              result += ':';
              type = ':';
            } else if (type === ':') {
              body = this.getBody(str, i);
              i = i + body.originLength - 1;
              result += this.parseString(body.body);
              type = 'afterBody';
            } else if (type === 'afterBody' || type === 'needKey') {
              var last = i;

              while (str[last] === ',' || this.isBlankChar(str[last])) {
                last++;
              }

              if (str[last] === '}' && last === str.length - 1) {
                while (result[result.length - 1] === ',') {
                  result = result.substr(0, result.length - 1);
                }

                result += '}';
                return result;
              } else if (last !== i && result !== '{') {
                result += ',';
                type = 'needKey';
                i = last - 1;
              }
            }
          }

          throw new Error('Broken JSON object near ' + result);
        }
        /*
        * array
        */


        if (str[0] === '[') {
          result = '[';
          type = 'needBody';

          for (var i = 1; i < str.length; i++) {
            if (' ' === str[i] || '\n' === str[i] || '\t' === str[i]) {
              continue;
            } else if (type === 'needBody') {
              if (str[i] === ',') {
                result += 'null,';
                continue;
              }

              if (str[i] === ']' && i === str.length - 1) {
                if (result[result.length - 1] === ",") {
                  result = result.substr(0, result.length - 1);
                }

                result += ']';
                return result;
              }

              body = this.getBody(str, i);
              i = i + body.originLength - 1;
              result += this.parseString(body.body);
              type = 'afterBody';
            } else if (type === 'afterBody') {
              if (str[i] === ',') {
                result += ',';
                type = 'needBody'; // deal with mistake ","

                while (str[i + 1] === ',' || this.isBlankChar(str[i + 1])) {
                  if (str[i + 1] === ',') {
                    result += 'null,';
                  }

                  i++;
                }
              } else if (str[i] === ']' && i === str.length - 1) {
                result += ']';
                return result;
              }
            }
          }

          throw new Error('Broken JSON array near ' + result);
        }
      }
    }, {
      key: "getBody",
      value: function getBody(str, pos) {
        var body = ''; // parse string body

        if (str[pos] === '"' || str[pos] === '\'') {
          body = str[pos];

          for (var i = pos + 1; i < str.length; i++) {
            if (str[i] === '\\') {
              body += str[i];

              if (i + 1 < str.length) {
                body += str[i + 1];
              }

              i++;
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

          throw new Error('Broken JSON string body near ' + body);
        } // parse true / false


        if (str[pos] === 't') {
          if (str.indexOf('true', pos) === pos) {
            return {
              originLength: 'true'.length,
              body: 'true'
            };
          }

          throw new Error('Broken JSON boolean body near ' + str.substr(0, pos + 10));
        }

        if (str[pos] === 'f') {
          if (str.indexOf('f', pos) === pos) {
            return {
              originLength: 'false'.length,
              body: 'false'
            };
          }

          throw new Error('Broken JSON boolean body near ' + str.substr(0, pos + 10));
        } // parse null


        if (str[pos] === 'n') {
          if (str.indexOf('null', pos) === pos) {
            return {
              originLength: 'null'.length,
              body: 'null'
            };
          }

          throw new Error('Broken JSON boolean body near ' + str.substr(0, pos + 10));
        } // parse number


        if (str[pos] === '-' || str[pos] === '+' || str[pos] === '.' || str[pos] >= '0' && str[pos] <= '9') {
          body = '';

          for (var i = pos; i < str.length; i++) {
            if (str[i] === '-' || str[i] === '+' || str[i] === '.' || str[i] >= '0' && str[i] <= '9') {
              body += str[i];
            } else {
              return {
                originLength: body.length,
                body: body
              };
            }
          }

          throw new Error('Broken JSON number body near ' + body);
        } // parse object


        if (str[pos] === '{' || str[pos] === '[') {
          var stack = [str[pos]];
          body = str[pos];

          for (var i = pos + 1; i < str.length; i++) {
            body += str[i];

            if (str[i] === '\\') {
              if (i + 1 < str.length) {
                body += str[i + 1];
              }

              i++;
            } else if (str[i] === '"') {
              if (stack[stack.length - 1] === '"') {
                stack.pop();
              } else if (stack[stack.length - 1] !== '\'') {
                stack.push(str[i]);
              }
            } else if (str[i] === '\'') {
              if (stack[stack.length - 1] === '\'') {
                stack.pop();
              } else if (stack[stack.length - 1] !== '"') {
                stack.push(str[i]);
              }
            } else if (stack[stack.length - 1] !== '"' && stack[stack.length - 1] !== '\'') {
              if (str[i] === '{') {
                stack.push('{');
              } else if (str[i] === '}') {
                if (stack[stack.length - 1] === '{') {
                  stack.pop();
                } else {
                  throw new Error('Broken JSON ' + (str[pos] === '{' ? 'object' : 'array') + ' body near ' + body);
                }
              } else if (str[i] === '[') {
                stack.push('[');
              } else if (str[i] === ']') {
                if (stack[stack.length - 1] === '[') {
                  stack.pop();
                } else {
                  throw new Error('Broken JSON ' + (str[pos] === '{' ? 'object' : 'array') + ' body near ' + body);
                }
              }
            }

            if (!stack.length) {
              return {
                originLength: i - pos,
                body: body
              };
            }
          }

          throw new Error('Broken JSON ' + (str[pos] === '{' ? 'object' : 'array') + ' body near ' + body);
        }

        throw new Error('Broken JSON body near ' + str.substr(pos - 5 >= 0 ? pos - 5 : 0, 50));
      }
    }, {
      key: "parseKey",
      value: function parseKey(str, pos, quote) {
        var key = '';

        for (var i = pos; i < str.length; i++) {
          if (quote && quote === str[i]) {
            return key;
          } else if (!quote && (str[i] === ' ' || str[i] === ':')) {
            return key;
          }

          key += str[i];

          if (str[i] === '\\' && i + 1 < str.length) {
            key += str[i + 1];
            i++;
          }
        }

        throw new Error('Broken JSON syntax near ' + key);
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
  }(Singleton);

  winter.addModule('jsonParser', JsonParser);
})(window.winter);

(function (winter) {
  var Sanitizer = /*#__PURE__*/function (_Singleton2) {
    _inherits(Sanitizer, _Singleton2);

    var _super3 = _createSuper(Sanitizer);

    function Sanitizer(winter) {
      var _this5;

      _classCallCheck(this, Sanitizer);

      _this5 = _super3.call(this, winter); // Add to global function for backwards compatibility

      window.wnSanitize = function (html) {
        return _this5.sanitize(html);
      };

      return _this5;
    }

    _createClass(Sanitizer, [{
      key: "sanitize",
      value: function sanitize(html) {
        var parser = new DOMParser();
        var dom = parser.parseFromString(html, 'text/html');
        this.sanitizeNode(dom.getRootNode());
        return dom.documentElement.innerHTML;
      }
    }, {
      key: "sanitizeNode",
      value: function sanitizeNode(node) {
        var _this6 = this;

        if (node.tagName === 'SCRIPT') {
          node.remove();
          return;
        }

        this.trimAttributes(node);
        var children = Array.from(node.children);
        children.forEach(function (child) {
          _this6.sanitizeNode(child);
        });
      }
    }, {
      key: "trimAttributes",
      value: function trimAttributes(node) {
        if (!node.attributes) {
          return;
        }

        for (var i = 0; i < node.attributes.length; i++) {
          var attrName = node.attributes.item(i).name;
          var attrValue = node.attributes.item(i).value;
          /*
          * remove attributes where the names start with "on" (for example: onload, onerror...)
          * remove attributes where the value starts with the "javascript:" pseudo protocol (for example href="javascript:alert(1)")
          */

          if (attrName.indexOf('on') === 0 || attrValue.indexOf('javascript:') === 0) {
            node.removeAttribute(attrName);
          }
        }
      }
    }]);

    return Sanitizer;
  }(Singleton);

  winter.addModule('sanitizer', Sanitizer);
})(window.winter);
