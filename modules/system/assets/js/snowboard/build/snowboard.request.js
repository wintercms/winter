"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/js/snowboard/build/snowboard.request"],{

/***/ "./assets/js/snowboard/ajax/Request.js":
/*!*********************************************!*\
  !*** ./assets/js/snowboard/ajax/Request.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "../../node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

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
 * Request plugin.
 *
 * This is the default AJAX handler which will run using the `fetch()` method that is default in modern browsers.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Request = /*#__PURE__*/function (_Snowboard$PluginBase) {
  _inherits(Request, _Snowboard$PluginBase);

  var _super = _createSuper(Request);

  /**
   * Constructor.
   *
   * @param {Snowboard} snowboard
   * @param {HTMLElement|string} element
   * @param {string} handler
   * @param {Object} options
   */
  function Request(snowboard, element, handler, options) {
    var _this;

    _classCallCheck(this, Request);

    _this = _super.call(this, snowboard);

    if (typeof element === 'string') {
      var matchedElement = document.querySelector(element);

      if (matchedElement === null) {
        throw new Error("No element was found with the given selector: ".concat(element));
      }

      _this.element = matchedElement;
    } else {
      _this.element = element;
    }

    _this.handler = handler;
    _this.options = options || {};
    _this.fetchOptions = {};
    _this.responseData = null;
    _this.responseError = null;
    _this.cancelled = false;

    _this.checkRequest();

    if (!_this.snowboard.globalEvent('ajaxSetup', _assertThisInitialized(_this))) {
      _this.cancelled = true;
      return _possibleConstructorReturn(_this);
    }

    if (_this.element) {
      var event = new Event('ajaxSetup', {
        cancelable: true
      });
      event.request = _assertThisInitialized(_this);

      _this.element.dispatchEvent(event);

      if (event.defaultPrevented) {
        _this.cancelled = true;
        return _possibleConstructorReturn(_this);
      }
    }

    if (!_this.doClientValidation()) {
      _this.cancelled = true;
      return _possibleConstructorReturn(_this);
    }

    if (_this.confirm) {
      _this.doConfirm().then(function (confirmed) {
        if (confirmed) {
          _this.doAjax().then(function (response) {
            if (response.cancelled) {
              _this.cancelled = true;
              return;
            }

            _this.responseData = response;

            _this.processUpdate(response).then(function () {
              if (response.X_WINTER_SUCCESS === false) {
                _this.processError(response);
              } else {
                _this.processResponse(response);
              }
            });
          }, function (error) {
            _this.responseError = error;

            _this.processError(error);
          })["finally"](function () {
            if (_this.cancelled === true) {
              return;
            }

            if (_this.options.complete && typeof _this.options.complete === 'function') {
              _this.options.complete(_this.responseData, _assertThisInitialized(_this));
            }

            _this.snowboard.globalEvent('ajaxDone', _this.responseData, _assertThisInitialized(_this));

            if (_this.element) {
              var _event = new Event('ajaxAlways');

              _event.request = _assertThisInitialized(_this);
              _event.responseData = _this.responseData;
              _event.responseError = _this.responseError;

              _this.element.dispatchEvent(_event);
            }
          });
        }
      });
    } else {
      _this.doAjax().then(function (response) {
        if (response.cancelled) {
          _this.cancelled = true;
          return;
        }

        _this.responseData = response;

        _this.processUpdate(response).then(function () {
          if (response.X_WINTER_SUCCESS === false) {
            _this.processError(response);
          } else {
            _this.processResponse(response);
          }
        });
      }, function (error) {
        _this.responseError = error;

        _this.processError(error);
      })["finally"](function () {
        if (_this.cancelled === true) {
          return;
        }

        if (_this.options.complete && typeof _this.options.complete === 'function') {
          _this.options.complete(_this.responseData, _assertThisInitialized(_this));
        }

        _this.snowboard.globalEvent('ajaxDone', _this.responseData, _assertThisInitialized(_this));

        if (_this.element) {
          var _event2 = new Event('ajaxAlways');

          _event2.request = _assertThisInitialized(_this);
          _event2.responseData = _this.responseData;
          _event2.responseError = _this.responseError;

          _this.element.dispatchEvent(_event2);
        }
      });
    }

    return _this;
  }
  /**
   * Dependencies for this plugin.
   *
   * @returns {string[]}
   */


  _createClass(Request, [{
    key: "dependencies",
    value: function dependencies() {
      return ['cookie', 'jsonParser'];
    }
    /**
     * Validates the element and handler given in the request.
     */

  }, {
    key: "checkRequest",
    value: function checkRequest() {
      if (this.element && this.element instanceof Element === false) {
        throw new Error('The element provided must be an Element instance');
      }

      if (this.handler === undefined) {
        throw new Error('The AJAX handler name is not specified.');
      }

      if (!this.handler.match(/^(?:\w+:{2})?on*/)) {
        throw new Error('Invalid AJAX handler name. The correct handler name format is: "onEvent".');
      }
    }
    /**
     * Creates a Fetch request.
     *
     * This method is made available for plugins to extend or override the default fetch() settings with their own.
     *
     * @returns {Promise}
     */

  }, {
    key: "getFetch",
    value: function getFetch() {
      this.fetchOptions = this.options.fetchOptions !== undefined && _typeof(this.options.fetchOptions) === 'object' ? this.options.fetchOptions : {
        method: 'POST',
        headers: this.headers,
        body: this.data,
        redirect: 'follow',
        mode: 'same-origin'
      };
      this.snowboard.globalEvent('ajaxFetchOptions', this.fetchOptions, this);
      return fetch(this.url, this.fetchOptions);
    }
    /**
     * Run client-side validation on the form, if available.
     *
     * @returns {boolean}
     */

  }, {
    key: "doClientValidation",
    value: function doClientValidation() {
      if (this.options.browserValidate === true && this.form) {
        if (this.form.checkValidity() === false) {
          this.form.reportValidity();
          return false;
        }
      }

      return true;
    }
    /**
     * Executes the AJAX query.
     *
     * Returns a Promise object for when the AJAX request is completed.
     *
     * @returns {Promise}
     */

  }, {
    key: "doAjax",
    value: function doAjax() {
      var _this2 = this;

      // Allow plugins to cancel the AJAX request before sending
      if (this.snowboard.globalEvent('ajaxBeforeSend', this) === false) {
        return Promise.resolve({
          cancelled: true
        });
      }

      var ajaxPromise = new Promise(function (resolve, reject) {
        _this2.getFetch().then(function (response) {
          if (!response.ok && response.status !== 406) {
            if (response.headers.has('Content-Type') && response.headers.get('Content-Type').includes('/json')) {
              response.json().then(function (responseData) {
                reject(_this2.renderError(responseData.message, responseData.exception, responseData.file, responseData.line, responseData.trace));
              }, function (error) {
                reject(_this2.renderError("Unable to parse JSON response: ".concat(error)));
              });
            } else {
              response.text().then(function (responseText) {
                reject(_this2.renderError(responseText));
              }, function (error) {
                reject(_this2.renderError("Unable to process response: ".concat(error)));
              });
            }

            return;
          }

          if (response.headers.has('Content-Type') && response.headers.get('Content-Type').includes('/json')) {
            response.json().then(function (responseData) {
              resolve(_objectSpread(_objectSpread({}, responseData), {}, {
                X_WINTER_SUCCESS: response.status !== 406,
                X_WINTER_RESPONSE_CODE: response.status
              }));
            }, function (error) {
              reject(_this2.renderError("Unable to parse JSON response: ".concat(error)));
            });
          } else {
            response.text().then(function (responseData) {
              resolve(responseData);
            }, function (error) {
              reject(_this2.renderError("Unable to process response: ".concat(error)));
            });
          }
        }, function (responseError) {
          reject(_this2.renderError("Unable to retrieve a response from the server: ".concat(responseError)));
        });
      });
      this.snowboard.globalEvent('ajaxStart', ajaxPromise, this);

      if (this.element) {
        var event = new Event('ajaxPromise');
        event.promise = ajaxPromise;
        this.element.dispatchEvent(event);
      }

      return ajaxPromise;
    }
    /**
     * Prepares for updating the partials from the AJAX response.
     *
     * If any partials are returned from the AJAX response, this method will also action the partial updates.
     *
     * Returns a Promise object which tracks when the partial update is complete.
     *
     * @param {Object} response
     * @returns {Promise}
     */

  }, {
    key: "processUpdate",
    value: function processUpdate(response) {
      var _this3 = this;

      return new Promise(function (resolve, reject) {
        if (typeof _this3.options.beforeUpdate === 'function') {
          if (_this3.options.beforeUpdate.apply(_this3, [response]) === false) {
            reject();
            return;
          }
        } // Extract partial information


        var partials = {};
        Object.entries(response).forEach(function (entry) {
          var _entry = _slicedToArray(entry, 2),
              key = _entry[0],
              value = _entry[1];

          if (key.substr(0, 8) !== 'X_WINTER') {
            partials[key] = value;
          }
        });

        if (Object.keys(partials).length === 0) {
          resolve();
          return;
        }

        var promises = _this3.snowboard.globalPromiseEvent('ajaxBeforeUpdate', response, _this3);

        promises.then(function () {
          _this3.doUpdate(partials).then(function () {
            // Allow for HTML redraw
            window.requestAnimationFrame(function () {
              return resolve();
            });
          }, function () {
            reject();
          });
        }, function () {
          reject();
        });
      });
    }
    /**
     * Updates the partials with the given content.
     *
     * @param {Object} partials
     * @returns {Promise}
     */

  }, {
    key: "doUpdate",
    value: function doUpdate(partials) {
      var _this4 = this;

      return new Promise(function (resolve) {
        var affected = [];
        Object.entries(partials).forEach(function (entry) {
          var _entry2 = _slicedToArray(entry, 2),
              partial = _entry2[0],
              content = _entry2[1];

          var selector = _this4.options.update && _this4.options.update[partial] ? _this4.options.update[partial] : partial;
          var mode = 'replace';

          if (selector.substr(0, 1) === '@') {
            mode = 'append';
            selector = selector.substr(1);
          } else if (selector.substr(0, 1) === '^') {
            mode = 'prepend';
            selector = selector.substr(1);
          }

          var elements = document.querySelectorAll(selector);

          if (elements.length > 0) {
            elements.forEach(function (element) {
              switch (mode) {
                case 'append':
                  element.innerHTML += content;
                  break;

                case 'prepend':
                  element.innerHTML = content + element.innerHTML;
                  break;

                case 'replace':
                default:
                  element.innerHTML = content;
                  break;
              }

              affected.push(element); // Fire update event for each element that is updated

              _this4.snowboard.globalEvent('ajaxUpdate', element, content, _this4);

              var event = new Event('ajaxUpdate');
              event.content = content;
              element.dispatchEvent(event);
            });
          }
        });

        _this4.snowboard.globalEvent('ajaxUpdateComplete', affected, _this4);

        resolve();
      });
    }
    /**
     * Processes the response data.
     *
     * This fires off all necessary processing functions depending on the response, ie. if there's any flash
     * messages to handle, or any redirects to be undertaken.
     *
     * @param {Object} response
     * @returns {void}
     */

  }, {
    key: "processResponse",
    value: function processResponse(response) {
      if (this.options.success && typeof this.options.success === 'function') {
        if (!this.options.success(this.responseData, this)) {
          return;
        }
      } // Allow plugins to cancel any further response handling


      if (this.snowboard.globalEvent('ajaxSuccess', this.responseData, this) === false) {
        return;
      } // Allow the element to cancel any further response handling


      if (this.element) {
        var event = new Event('ajaxDone', {
          cancelable: true
        });
        event.responseData = this.responseData;
        event.request = this;
        this.element.dispatchEvent(event);

        if (event.defaultPrevented) {
          return;
        }
      }

      if (this.flash && response.X_WINTER_FLASH_MESSAGES) {
        this.processFlashMessages(response.X_WINTER_FLASH_MESSAGES);
      } // Check for a redirect from the response, or use the redirect as specified in the options.


      if (this.redirect || response.X_WINTER_REDIRECT) {
        this.processRedirect(this.redirect || response.X_WINTER_REDIRECT);
        return;
      }

      if (response.X_WINTER_ASSETS) {
        this.processAssets(response.X_WINTER_ASSETS);
      }
    }
    /**
     * Processes an error response from the AJAX request.
     *
     * This fires off all necessary processing functions depending on the error response, ie. if there's any error or
     * validation messages to handle.
     *
     * @param {Object|Error} error
     */

  }, {
    key: "processError",
    value: function processError(error) {
      if (this.options.error && typeof this.options.error === 'function') {
        if (!this.options.error(this.responseError, this)) {
          return;
        }
      } // Allow plugins to cancel any further error handling


      if (this.snowboard.globalEvent('ajaxError', this.responseError, this) === false) {
        return;
      } // Allow the element to cancel any further error handling


      if (this.element) {
        var event = new Event('ajaxFail', {
          cancelable: true
        });
        event.responseError = this.responseError;
        event.request = this;
        this.element.dispatchEvent(event);

        if (event.defaultPrevented) {
          return;
        }
      }

      if (error instanceof Error) {
        this.processErrorMessage(error.message);
      } else {
        // Process validation errors
        if (error.X_WINTER_ERROR_FIELDS) {
          this.processValidationErrors(error.X_WINTER_ERROR_FIELDS);
        }

        if (error.X_WINTER_ERROR_MESSAGE) {
          this.processErrorMessage(error.X_WINTER_ERROR_MESSAGE);
        }
      }
    }
    /**
     * Processes a redirect response.
     *
     * By default, this processor will simply redirect the user in their browser.
     *
     * Plugins can augment this functionality from the `ajaxRedirect` event. You may also override this functionality on
     * a per-request basis through the `handleRedirectResponse` callback option. If a `false` is returned from either, the
     * redirect will be cancelled.
     *
     * @param {string} url
     * @returns {void}
     */

  }, {
    key: "processRedirect",
    value: function processRedirect(url) {
      var _this5 = this;

      // Run a custom per-request redirect handler. If false is returned, don't run the redirect.
      if (typeof this.options.handleRedirectResponse === 'function') {
        if (this.options.handleRedirectResponse.apply(this, [url]) === false) {
          return;
        }
      } // Allow plugins to cancel the redirect


      if (this.snowboard.globalEvent('ajaxRedirect', url, this) === false) {
        return;
      } // Indicate that the AJAX request is finished if we're still on the current page
      // so that the loading indicator for redirects that just change the hash value of
      // the URL instead of leaving the page will properly stop.
      // @see https://github.com/octobercms/october/issues/2780


      window.addEventListener('popstate', function () {
        if (_this5.element) {
          var event = document.createEvent('CustomEvent');
          event.eventName = 'ajaxRedirected';

          _this5.element.dispatchEvent(event);
        }
      }, {
        once: true
      });
      window.location.assign(url);
    }
    /**
     * Processes an error message.
     *
     * By default, this processor will simply alert the user through a simple `alert()` call.
     *
     * Plugins can augment this functionality from the `ajaxErrorMessage` event. You may also override this functionality
     * on a per-request basis through the `handleErrorMessage` callback option. If a `false` is returned from either, the
     * error message handling will be cancelled.
     *
     * @param {string} message
     * @returns {void}
     */

  }, {
    key: "processErrorMessage",
    value: function processErrorMessage(message) {
      // Run a custom per-request handler for error messages. If false is returned, do not process the error messages
      // any further.
      if (typeof this.options.handleErrorMessage === 'function') {
        if (this.options.handleErrorMessage.apply(this, [message]) === false) {
          return;
        }
      } // Allow plugins to cancel the error message being shown


      if (this.snowboard.globalEvent('ajaxErrorMessage', message, this) === false) {
        return;
      } // By default, show a browser error message


      window.alert(message);
    }
    /**
     * Processes flash messages from the response.
     *
     * By default, no flash message handling will occur.
     *
     * Plugins can augment this functionality from the `ajaxFlashMessages` event. You may also override this functionality
     * on a per-request basis through the `handleFlashMessages` callback option. If a `false` is returned from either, the
     * flash message handling will be cancelled.
     *
     * @param {Object} messages
     * @returns
     */

  }, {
    key: "processFlashMessages",
    value: function processFlashMessages(messages) {
      // Run a custom per-request flash handler. If false is returned, don't show the flash message
      if (typeof this.options.handleFlashMessages === 'function') {
        if (this.options.handleFlashMessages.apply(this, [messages]) === false) {
          return;
        }
      }

      this.snowboard.globalEvent('ajaxFlashMessages', messages, this);
    }
    /**
     * Processes validation errors for fields.
     *
     * By default, no validation error handling will occur.
     *
     * Plugins can augment this functionality from the `ajaxValidationErrors` event. You may also override this functionality
     * on a per-request basis through the `handleValidationErrors` callback option. If a `false` is returned from either, the
     * validation error handling will be cancelled.
     *
     * @param {Object} fields
     * @returns
     */

  }, {
    key: "processValidationErrors",
    value: function processValidationErrors(fields) {
      if (typeof this.options.handleValidationErrors === 'function') {
        if (this.options.handleValidationErrors.apply(this, [this.form, fields]) === false) {
          return;
        }
      } // Allow plugins to cancel the validation errors being handled


      this.snowboard.globalEvent('ajaxValidationErrors', this.form, fields, this);
    }
    /**
     * Confirms the request with the user before proceeding.
     *
     * This is an asynchronous method. By default, it will use the browser's `confirm()` method to query the user to
     * confirm the action. This method will return a Promise with a boolean value depending on whether the user confirmed
     * or not.
     *
     * Plugins can augment this functionality from the `ajaxConfirmMessage` event. You may also override this functionality
     * on a per-request basis through the `handleConfirmMessage` callback option. If a `false` is returned from either,
     * the confirmation is assumed to have been denied.
     *
     * @returns {Promise}
     */

  }, {
    key: "doConfirm",
    value: function () {
      var _doConfirm = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee() {
        var promises, fulfilled;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!(typeof this.options.handleConfirmMessage === 'function')) {
                  _context.next = 4;
                  break;
                }

                if (!(this.options.handleConfirmMessage.apply(this, [this.confirm]) === false)) {
                  _context.next = 3;
                  break;
                }

                return _context.abrupt("return", false);

              case 3:
                return _context.abrupt("return", true);

              case 4:
                if (!(this.snowboard.listensToEvent('ajaxConfirmMessage').length === 0)) {
                  _context.next = 6;
                  break;
                }

                return _context.abrupt("return", window.confirm(this.confirm));

              case 6:
                // Run custom plugin confirmations
                promises = this.snowboard.globalPromiseEvent('ajaxConfirmMessage', this.confirm, this);
                _context.prev = 7;
                _context.next = 10;
                return promises;

              case 10:
                fulfilled = _context.sent;

                if (!fulfilled) {
                  _context.next = 13;
                  break;
                }

                return _context.abrupt("return", true);

              case 13:
                _context.next = 18;
                break;

              case 15:
                _context.prev = 15;
                _context.t0 = _context["catch"](7);
                return _context.abrupt("return", false);

              case 18:
                return _context.abrupt("return", false);

              case 19:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[7, 15]]);
      }));

      function doConfirm() {
        return _doConfirm.apply(this, arguments);
      }

      return doConfirm;
    }()
  }, {
    key: "form",
    get: function get() {
      if (this.options.form) {
        return this.options.form;
      }

      if (!this.element) {
        return null;
      }

      if (this.element.tagName === 'FORM') {
        return this.element;
      }

      return this.element.closest('form');
    }
  }, {
    key: "context",
    get: function get() {
      return {
        handler: this.handler,
        options: this.options
      };
    }
  }, {
    key: "headers",
    get: function get() {
      var headers = {
        'X-Requested-With': 'XMLHttpRequest',
        // Keeps compatibility with jQuery AJAX
        'X-WINTER-REQUEST-HANDLER': this.handler,
        'X-WINTER-REQUEST-PARTIALS': this.extractPartials(this.options.update || [])
      };

      if (this.flash) {
        headers['X-WINTER-REQUEST-FLASH'] = 1;
      }

      if (this.xsrfToken) {
        headers['X-XSRF-TOKEN'] = this.xsrfToken;
      }

      return headers;
    }
  }, {
    key: "loading",
    get: function get() {
      return this.options.loading || false;
    }
  }, {
    key: "url",
    get: function get() {
      return this.options.url || window.location.href;
    }
  }, {
    key: "redirect",
    get: function get() {
      return this.options.redirect && this.options.redirect.length ? this.options.redirect : null;
    }
  }, {
    key: "flash",
    get: function get() {
      return this.options.flash || false;
    }
  }, {
    key: "files",
    get: function get() {
      if (this.options.files === true) {
        if (FormData === undefined) {
          this.snowboard.debug('This browser does not support file uploads');
          return false;
        }

        return true;
      }

      return false;
    }
  }, {
    key: "xsrfToken",
    get: function get() {
      return this.snowboard.cookie().get('XSRF-TOKEN');
    }
  }, {
    key: "data",
    get: function get() {
      var data = _typeof(this.options.data) === 'object' ? this.options.data : {};
      var formData = new FormData(this.form || undefined);

      if (Object.keys(data).length > 0) {
        Object.entries(data).forEach(function (entry) {
          var _entry3 = _slicedToArray(entry, 2),
              key = _entry3[0],
              value = _entry3[1];

          formData.append(key, value);
        });
      }

      return formData;
    }
  }, {
    key: "confirm",
    get: function get() {
      return this.options.confirm || false;
    }
    /**
     * Extracts partials.
     *
     * @param {Object} update
     * @returns {string}
     */

  }, {
    key: "extractPartials",
    value: function extractPartials(update) {
      return Object.keys(update).join('&');
    }
    /**
     * Renders an error with useful debug information.
     *
     * This method is used internally when the AJAX request could not be completed or processed correctly due to an error.
     *
     * @param {string} message
     * @param {string} exception
     * @param {string} file
     * @param {Number} line
     * @param {string[]} trace
     * @returns {Error}
     */

  }, {
    key: "renderError",
    value: function renderError(message, exception, file, line, trace) {
      var error = new Error(message);
      error.exception = exception || null;
      error.file = file || null;
      error.line = line || null;
      error.trace = trace || [];
      return error;
    }
  }]);

  return Request;
}(Snowboard.PluginBase);

Snowboard.addPlugin('request', Request);

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["assets/js/vendor/vendor"], () => (__webpack_exec__("./assets/js/snowboard/ajax/Request.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYnVpbGQvc25vd2JvYXJkLnJlcXVlc3QuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7SUFDTUE7Ozs7O0FBQ0Y7QUFDSjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNJLG1CQUFZQyxTQUFaLEVBQXVCQyxPQUF2QixFQUFnQ0MsT0FBaEMsRUFBeUNDLE9BQXpDLEVBQWtEO0FBQUE7O0FBQUE7O0FBQzlDLDhCQUFNSCxTQUFOOztBQUVBLFFBQUksT0FBT0MsT0FBUCxLQUFtQixRQUF2QixFQUFpQztBQUM3QixVQUFNRyxjQUFjLEdBQUdDLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1QkwsT0FBdkIsQ0FBdkI7O0FBQ0EsVUFBSUcsY0FBYyxLQUFLLElBQXZCLEVBQTZCO0FBQ3pCLGNBQU0sSUFBSUcsS0FBSix5REFBMkROLE9BQTNELEVBQU47QUFDSDs7QUFDRCxZQUFLQSxPQUFMLEdBQWVHLGNBQWY7QUFDSCxLQU5ELE1BTU87QUFDSCxZQUFLSCxPQUFMLEdBQWVBLE9BQWY7QUFDSDs7QUFDRCxVQUFLQyxPQUFMLEdBQWVBLE9BQWY7QUFDQSxVQUFLQyxPQUFMLEdBQWVBLE9BQU8sSUFBSSxFQUExQjtBQUNBLFVBQUtLLFlBQUwsR0FBb0IsRUFBcEI7QUFDQSxVQUFLQyxZQUFMLEdBQW9CLElBQXBCO0FBQ0EsVUFBS0MsYUFBTCxHQUFxQixJQUFyQjtBQUNBLFVBQUtDLFNBQUwsR0FBaUIsS0FBakI7O0FBRUEsVUFBS0MsWUFBTDs7QUFDQSxRQUFJLENBQUMsTUFBS1osU0FBTCxDQUFlYSxXQUFmLENBQTJCLFdBQTNCLGdDQUFMLEVBQW9EO0FBQ2hELFlBQUtGLFNBQUwsR0FBaUIsSUFBakI7QUFDQTtBQUNIOztBQUNELFFBQUksTUFBS1YsT0FBVCxFQUFrQjtB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WixFQUFvQjZDLElBQXBCLENBQXlCLEdBQXpCLENBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7OztXQUNJLHFCQUFZM0UsT0FBWixFQUFxQkMsU0FBckIsRUFBZ0NDLElBQWhDLEVBQXNDQyxJQUF0QyxFQUE0Q0MsS0FBNUMsRUFBbUQ7QUFDL0MsVUFBTWpDLEtBQUssR0FBRyxJQUFJeEIsS0FBSixDQUFVcUQsT0FBVixDQUFkO0FBQ0E3QixNQUFBQSxLQUFLLENBQUM4QixTQUFOLEdBQWtCQSxTQUFTLElBQUksSUFBL0I7QUFDQTlCLE1BQUFBLEtBQUssQ0FBQytCLElBQU4sR0FBYUEsSUFBSSxJQUFJLElBQXJCO0FBQ0EvQixNQUFBQSxLQUFLLENBQUNnQyxJQUFOLEdBQWFBLElBQUksSUFBSSxJQUFyQjtBQUNBaEMsTUFBQUEsS0FBSyxDQUFDaUMsS0FBTixHQUFjQSxLQUFLLElBQUksRUFBdkI7QUFDQSxhQUFPakMsS0FBUDtBQUNIOzs7O0VBendCaUJ5RyxTQUFTLENBQUNDOztBQTR3QmhDRCxTQUFTLENBQUNFLFNBQVYsQ0FBb0IsU0FBcEIsRUFBK0IzSSxPQUEvQiIsInNvdXJjZXMiOlsid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL2pzL3Nub3dib2FyZC9hamF4L1JlcXVlc3QuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBSZXF1ZXN0IHBsdWdpbi5cbiAqXG4gKiBUaGlzIGlzIHRoZSBkZWZhdWx0IEFKQVggaGFuZGxlciB3aGljaCB3aWxsIHJ1biB1c2luZyB0aGUgYGZldGNoKClgIG1ldGhvZCB0aGF0IGlzIGRlZmF1bHQgaW4gbW9kZXJuIGJyb3dzZXJzLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5jbGFzcyBSZXF1ZXN0IGV4dGVuZHMgU25vd2JvYXJkLlBsdWdpbkJhc2Uge1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmR9IHNub3dib2FyZFxuICAgICAqIEBwYXJhbSB7SFRNTEVsZW1lbnR8c3RyaW5nfSBlbGVtZW50XG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGhhbmRsZXJcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9uc1xuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHNub3dib2FyZCwgZWxlbWVudCwgaGFuZGxlciwgb3B0aW9ucykge1xuICAgICAgICBzdXBlcihzbm93Ym9hcmQpO1xuXG4gICAgICAgIGlmICh0eXBlb2YgZWxlbWVudCA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgIGNvbnN0IG1hdGNoZWRFbGVtZW50ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihlbGVtZW50KTtcbiAgICAgICAgICAgIGlmIChtYXRjaGVkRWxlbWVudCA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihgTm8gZWxlbWVudCB3YXMgZm91bmQgd2l0aCB0aGUgZ2l2ZW4gc2VsZWN0b3I6ICR7ZWxlbWVudH1gKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMuZWxlbWVudCA9IG1hdGNoZWRFbGVtZW50O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5lbGVtZW50ID0gZWxlbWVudDtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLmhhbmRsZXIgPSBoYW5kbGVyO1xuICAgICAgICB0aGlzLm9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgICAgICB0aGlzLmZldGNoT3B0aW9ucyA9IHt9O1xuICAgICAgICB0aGlzLnJlc3BvbnNlRGF0YSA9IG51bGw7XG4gICAgICAgIHRoaXMucmVzcG9uc2VFcnJvciA9IG51bGw7XG4gICAgICAgIHRoaXMuY2FuY2VsbGVkID0gZmFsc2U7XG5cbiAgICAgICAgdGhpcy5jaGVja1JlcXVlc3QoKTtcbiAgICAgICAgaWYgKCF0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheFNldHVwJywgdGhpcykpIHtcbiAgICAgICAgICAgIHRoaXMuY2FuY2VsbGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZiAodGhpcy5lbGVtZW50KSB7XG4gICAgICAgICAgICBjb25zdCBldmVudCA9IG5ldyBFdmVudCgnYWpheFNldHVwJywgeyBjYW5jZWxhYmxlOiB0cnVlIH0pO1xuICAgICAgICAgICAgZXZlbnQucmVxdWVzdCA9IHRoaXM7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG5cbiAgICAgICAgICAgIGlmIChldmVudC5kZWZhdWx0UHJldmVudGVkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5jYW5jZWxsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmICghdGhpcy5kb0NsaWVudFZhbGlkYXRpb24oKSkge1xuICAgICAgICAgICAgdGhpcy5jYW5jZWxsZWQgPSB0cnVlO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuY29uZmlybSkge1xuICAgICAgICAgICAgdGhpcy5kb0NvbmZpcm0oKS50aGVuKChjb25maXJtZWQpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoY29uZmlybWVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZG9BamF4KCkudGhlbihcbiAgICAgICAgICAgICAgICAgICAgICAgIChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5jYW5jZWxsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5jYW5jZWxsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucmVzcG9uc2VEYXRhID0gcmVzcG9uc2U7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5wcm9jZXNzVXBkYXRlKHJlc3BvbnNlKS50aGVuKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2UuWF9XSU5URVJfU1VDQ0VTUyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnByb2Nlc3NFcnJvcihyZXNwb25zZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucHJvY2Vzc1Jlc3BvbnNlKHJlc3BvbnNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucmVzcG9uc2VFcnJvciA9IGVycm9yO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucHJvY2Vzc0Vycm9yKGVycm9yKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICkuZmluYWxseSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5jYW5jZWxsZWQgPT09IHRydWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLm9wdGlvbnMuY29tcGxldGUgJiYgdHlwZW9mIHRoaXMub3B0aW9ucy5jb21wbGV0ZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMub3B0aW9ucy5jb21wbGV0ZSh0aGlzLnJlc3BvbnNlRGF0YSwgdGhpcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheERvbmUnLCB0aGlzLnJlc3BvbnNlRGF0YSwgdGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICh0aGlzLmVsZW1lbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBldmVudCA9IG5ldyBFdmVudCgnYWpheEFsd2F5cycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV2ZW50LnJlcXVlc3QgPSB0aGlzO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV2ZW50LnJlc3BvbnNlRGF0YSA9IHRoaXMucmVzcG9uc2VEYXRhO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV2ZW50LnJlc3BvbnNlRXJyb3IgPSB0aGlzLnJlc3BvbnNlRXJyb3I7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5lbGVtZW50LmRpc3BhdGNoRXZlbnQoZXZlbnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHRoaXMuZG9BamF4KCkudGhlbihcbiAgICAgICAgICAgICAgICAocmVzcG9uc2UpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLmNhbmNlbGxlZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5jYW5jZWxsZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmVzcG9uc2VEYXRhID0gcmVzcG9uc2U7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucHJvY2Vzc1VwZGF0ZShyZXNwb25zZSkudGhlbihcbiAgICAgICAgICAgICAgICAgICAgICAgICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2UuWF9XSU5URVJfU1VDQ0VTUyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5wcm9jZXNzRXJyb3IocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucHJvY2Vzc1Jlc3BvbnNlKHJlc3BvbnNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgKGVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmVzcG9uc2VFcnJvciA9IGVycm9yO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnByb2Nlc3NFcnJvcihlcnJvcik7XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICkuZmluYWxseSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuY2FuY2VsbGVkID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5vcHRpb25zLmNvbXBsZXRlICYmIHR5cGVvZiB0aGlzLm9wdGlvbnMuY29tcGxldGUgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5vcHRpb25zLmNvbXBsZXRlKHRoaXMucmVzcG9uc2VEYXRhLCB0aGlzKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhEb25lJywgdGhpcy5yZXNwb25zZURhdGEsIHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuZWxlbWVudCkge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBldmVudCA9IG5ldyBFdmVudCgnYWpheEFsd2F5cycpO1xuICAgICAgICAgICAgICAgICAgICBldmVudC5yZXF1ZXN0ID0gdGhpcztcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucmVzcG9uc2VEYXRhID0gdGhpcy5yZXNwb25zZURhdGE7XG4gICAgICAgICAgICAgICAgICAgIGV2ZW50LnJlc3BvbnNlRXJyb3IgPSB0aGlzLnJlc3BvbnNlRXJyb3I7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudC5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlcGVuZGVuY2llcyBmb3IgdGhpcyBwbHVnaW4uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWydjb29raWUnLCAnanNvblBhcnNlciddO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFZhbGlkYXRlcyB0aGUgZWxlbWVudCBhbmQgaGFuZGxlciBnaXZlbiBpbiB0aGUgcmVxdWVzdC5cbiAgICAgKi9cbiAgICBjaGVja1JlcXVlc3QoKSB7XG4gICAgICAgIGlmICh0aGlzLmVsZW1lbnQgJiYgdGhpcy5lbGVtZW50IGluc3RhbmNlb2YgRWxlbWVudCA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIGVsZW1lbnQgcHJvdmlkZWQgbXVzdCBiZSBhbiBFbGVtZW50IGluc3RhbmNlJyk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpcy5oYW5kbGVyID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlIEFKQVggaGFuZGxlciBuYW1lIGlzIG5vdCBzcGVjaWZpZWQuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIXRoaXMuaGFuZGxlci5tYXRjaCgvXig/Olxcdys6ezJ9KT9vbiovKSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdJbnZhbGlkIEFKQVggaGFuZGxlciBuYW1lLiBUaGUgY29ycmVjdCBoYW5kbGVyIG5hbWUgZm9ybWF0IGlzOiBcIm9uRXZlbnRcIi4nKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENyZWF0ZXMgYSBGZXRjaCByZXF1ZXN0LlxuICAgICAqXG4gICAgICogVGhpcyBtZXRob2QgaXMgbWFkZSBhdmFpbGFibGUgZm9yIHBsdWdpbnMgdG8gZXh0ZW5kIG9yIG92ZXJyaWRlIHRoZSBkZWZhdWx0IGZldGNoKCkgc2V0dGluZ3Mgd2l0aCB0aGVpciBvd24uXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7UHJvbWlzZX1cbiAgICAgKi9cbiAgICBnZXRGZXRjaCgpIHtcbiAgICAgICAgdGhpcy5mZXRjaE9wdGlvbnMgPSAodGhpcy5vcHRpb25zLmZldGNoT3B0aW9ucyAhPT0gdW5kZWZpbmVkICYmIHR5cGVvZiB0aGlzLm9wdGlvbnMuZmV0Y2hPcHRpb25zID09PSAnb2JqZWN0JylcbiAgICAgICAgICAgID8gdGhpcy5vcHRpb25zLmZldGNoT3B0aW9uc1xuICAgICAgICAgICAgOiB7XG4gICAgICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgaGVhZGVyczogdGhpcy5oZWFkZXJzLFxuICAgICAgICAgICAgICAgIGJvZHk6IHRoaXMuZGF0YSxcbiAgICAgICAgICAgICAgICByZWRpcmVjdDogJ2ZvbGxvdycsXG4gICAgICAgICAgICAgICAgbW9kZTogJ3NhbWUtb3JpZ2luJyxcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhGZXRjaE9wdGlvbnMnLCB0aGlzLmZldGNoT3B0aW9ucywgdGhpcyk7XG5cbiAgICAgICAgcmV0dXJuIGZldGNoKHRoaXMudXJsLCB0aGlzLmZldGNoT3B0aW9ucyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUnVuIGNsaWVudC1zaWRlIHZhbGlkYXRpb24gb24gdGhlIGZvcm0sIGlmIGF2YWlsYWJsZS5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgICAqL1xuICAgIGRvQ2xpZW50VmFsaWRhdGlvbigpIHtcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5icm93c2VyVmFsaWRhdGUgPT09IHRydWUgJiYgdGhpcy5mb3JtKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5mb3JtLmNoZWNrVmFsaWRpdHkoKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZvcm0ucmVwb3J0VmFsaWRpdHkoKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBFeGVjdXRlcyB0aGUgQUpBWCBxdWVyeS5cbiAgICAgKlxuICAgICAqIFJldHVybnMgYSBQcm9taXNlIG9iamVjdCBmb3Igd2hlbiB0aGUgQUpBWCByZXF1ZXN0IGlzIGNvbXBsZXRlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtQcm9taXNlfVxuICAgICAqL1xuICAgIGRvQWpheCgpIHtcbiAgICAgICAgLy8gQWxsb3cgcGx1Z2lucyB0byBjYW5jZWwgdGhlIEFKQVggcmVxdWVzdCBiZWZvcmUgc2VuZGluZ1xuICAgICAgICBpZiAodGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhCZWZvcmVTZW5kJywgdGhpcykgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKHtcbiAgICAgICAgICAgICAgICBjYW5jZWxsZWQ6IHRydWUsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGFqYXhQcm9taXNlID0gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5nZXRGZXRjaCgpLnRoZW4oXG4gICAgICAgICAgICAgICAgKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmICghcmVzcG9uc2Uub2sgJiYgcmVzcG9uc2Uuc3RhdHVzICE9PSA0MDYpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5oZWFkZXJzLmhhcygnQ29udGVudC1UeXBlJykgJiYgcmVzcG9uc2UuaGVhZGVycy5nZXQoJ0NvbnRlbnQtVHlwZScpLmluY2x1ZGVzKCcvanNvbicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzcG9uc2UuanNvbigpLnRoZW4oXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIChyZXNwb25zZURhdGEpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlamVjdCh0aGlzLnJlbmRlckVycm9yKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3BvbnNlRGF0YS5tZXNzYWdlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3BvbnNlRGF0YS5leGNlcHRpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzcG9uc2VEYXRhLmZpbGUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzcG9uc2VEYXRhLmxpbmUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzcG9uc2VEYXRhLnRyYWNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVqZWN0KHRoaXMucmVuZGVyRXJyb3IoYFVuYWJsZSB0byBwYXJzZSBKU09OIHJlc3BvbnNlOiAke2Vycm9yfWApKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXNwb25zZS50ZXh0KCkudGhlbihcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKHJlc3BvbnNlVGV4dCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVqZWN0KHRoaXMucmVuZGVyRXJyb3IocmVzcG9uc2VUZXh0KSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVqZWN0KHRoaXMucmVuZGVyRXJyb3IoYFVuYWJsZSB0byBwcm9jZXNzIHJlc3BvbnNlOiAke2Vycm9yfWApKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLmhlYWRlcnMuaGFzKCdDb250ZW50LVR5cGUnKSAmJiByZXNwb25zZS5oZWFkZXJzLmdldCgnQ29udGVudC1UeXBlJykuaW5jbHVkZXMoJy9qc29uJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3BvbnNlLmpzb24oKS50aGVuKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIChyZXNwb25zZURhdGEpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuLi5yZXNwb25zZURhdGEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBYX1dJTlRFUl9TVUNDRVNTOiByZXNwb25zZS5zdGF0dXMgIT09IDQwNixcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIFhfV0lOVEVSX1JFU1BPTlNFX0NPREU6IHJlc3BvbnNlLnN0YXR1cyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAoZXJyb3IpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVqZWN0KHRoaXMucmVuZGVyRXJyb3IoYFVuYWJsZSB0byBwYXJzZSBKU09OIHJlc3BvbnNlOiAke2Vycm9yfWApKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3BvbnNlLnRleHQoKS50aGVuKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIChyZXNwb25zZURhdGEpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShyZXNwb25zZURhdGEpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgKGVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlamVjdCh0aGlzLnJlbmRlckVycm9yKGBVbmFibGUgdG8gcHJvY2VzcyByZXNwb25zZTogJHtlcnJvcn1gKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIChyZXNwb25zZUVycm9yKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHJlamVjdCh0aGlzLnJlbmRlckVycm9yKGBVbmFibGUgdG8gcmV0cmlldmUgYSByZXNwb25zZSBmcm9tIHRoZSBzZXJ2ZXI6ICR7cmVzcG9uc2VFcnJvcn1gKSk7XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KCdhamF4U3RhcnQnLCBhamF4UHJvbWlzZSwgdGhpcyk7XG5cbiAgICAgICAgaWYgKHRoaXMuZWxlbWVudCkge1xuICAgICAgICAgICAgY29uc3QgZXZlbnQgPSBuZXcgRXZlbnQoJ2FqYXhQcm9taXNlJyk7XG4gICAgICAgICAgICBldmVudC5wcm9taXNlID0gYWpheFByb21pc2U7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gYWpheFByb21pc2U7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUHJlcGFyZXMgZm9yIHVwZGF0aW5nIHRoZSBwYXJ0aWFscyBmcm9tIHRoZSBBSkFYIHJlc3BvbnNlLlxuICAgICAqXG4gICAgICogSWYgYW55IHBhcnRpYWxzIGFyZSByZXR1cm5lZCBmcm9tIHRoZSBBSkFYIHJlc3BvbnNlLCB0aGlzIG1ldGhvZCB3aWxsIGFsc28gYWN0aW9uIHRoZSBwYXJ0aWFsIHVwZGF0ZXMuXG4gICAgICpcbiAgICAgKiBSZXR1cm5zIGEgUHJvbWlzZSBvYmplY3Qgd2hpY2ggdHJhY2tzIHdoZW4gdGhlIHBhcnRpYWwgdXBkYXRlIGlzIGNvbXBsZXRlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IHJlc3BvbnNlXG4gICAgICogQHJldHVybnMge1Byb21pc2V9XG4gICAgICovXG4gICAgcHJvY2Vzc1VwZGF0ZShyZXNwb25zZSkge1xuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICAgICAgaWYgKHR5cGVvZiB0aGlzLm9wdGlvbnMuYmVmb3JlVXBkYXRlID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5iZWZvcmVVcGRhdGUuYXBwbHkodGhpcywgW3Jlc3BvbnNlXSkgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlamVjdCgpO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBFeHRyYWN0IHBhcnRpYWwgaW5mb3JtYXRpb25cbiAgICAgICAgICAgIGNvbnN0IHBhcnRpYWxzID0ge307XG4gICAgICAgICAgICBPYmplY3QuZW50cmllcyhyZXNwb25zZSkuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBba2V5LCB2YWx1ZV0gPSBlbnRyeTtcblxuICAgICAgICAgICAgICAgIGlmIChrZXkuc3Vic3RyKDAsIDgpICE9PSAnWF9XSU5URVInKSB7XG4gICAgICAgICAgICAgICAgICAgIHBhcnRpYWxzW2tleV0gPSB2YWx1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgaWYgKE9iamVjdC5rZXlzKHBhcnRpYWxzKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCBwcm9taXNlcyA9IHRoaXMuc25vd2JvYXJkLmdsb2JhbFByb21pc2VFdmVudCgnYWpheEJlZm9yZVVwZGF0ZScsIHJlc3BvbnNlLCB0aGlzKTtcbiAgICAgICAgICAgIHByb21pc2VzLnRoZW4oXG4gICAgICAgICAgICAgICAgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmRvVXBkYXRlKHBhcnRpYWxzKS50aGVuKFxuICAgICAgICAgICAgICAgICAgICAgICAgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEFsbG93IGZvciBIVE1MIHJlZHJhd1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5yZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT4gcmVzb2x2ZSgpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVqZWN0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICByZWplY3QoKTtcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVXBkYXRlcyB0aGUgcGFydGlhbHMgd2l0aCB0aGUgZ2l2ZW4gY29udGVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBwYXJ0aWFsc1xuICAgICAqIEByZXR1cm5zIHtQcm9taXNlfVxuICAgICAqL1xuICAgIGRvVXBkYXRlKHBhcnRpYWxzKSB7XG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgYWZmZWN0ZWQgPSBbXTtcblxuICAgICAgICAgICAgT2JqZWN0LmVudHJpZXMocGFydGlhbHMpLmZvckVhY2goKGVudHJ5KSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc3QgW3BhcnRpYWwsIGNvbnRlbnRdID0gZW50cnk7XG5cbiAgICAgICAgICAgICAgICBsZXQgc2VsZWN0b3IgPSAodGhpcy5vcHRpb25zLnVwZGF0ZSAmJiB0aGlzLm9wdGlvbnMudXBkYXRlW3BhcnRpYWxdKVxuICAgICAgICAgICAgICAgICAgICA/IHRoaXMub3B0aW9ucy51cGRhdGVbcGFydGlhbF1cbiAgICAgICAgICAgICAgICAgICAgOiBwYXJ0aWFsO1xuXG4gICAgICAgICAgICAgICAgbGV0IG1vZGUgPSAncmVwbGFjZSc7XG5cbiAgICAgICAgICAgICAgICBpZiAoc2VsZWN0b3Iuc3Vic3RyKDAsIDEpID09PSAnQCcpIHtcbiAgICAgICAgICAgICAgICAgICAgbW9kZSA9ICdhcHBlbmQnO1xuICAgICAgICAgICAgICAgICAgICBzZWxlY3RvciA9IHNlbGVjdG9yLnN1YnN0cigxKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHNlbGVjdG9yLnN1YnN0cigwLCAxKSA9PT0gJ14nKSB7XG4gICAgICAgICAgICAgICAgICAgIG1vZGUgPSAncHJlcGVuZCc7XG4gICAgICAgICAgICAgICAgICAgIHNlbGVjdG9yID0gc2VsZWN0b3Iuc3Vic3RyKDEpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNvbnN0IGVsZW1lbnRzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbChzZWxlY3Rvcik7XG4gICAgICAgICAgICAgICAgaWYgKGVsZW1lbnRzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudHMuZm9yRWFjaCgoZWxlbWVudCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3dpdGNoIChtb2RlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAnYXBwZW5kJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZWxlbWVudC5pbm5lckhUTUwgKz0gY29udGVudDtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAncHJlcGVuZCc6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVsZW1lbnQuaW5uZXJIVE1MID0gY29udGVudCArIGVsZW1lbnQuaW5uZXJIVE1MO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXNlICdyZXBsYWNlJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbGVtZW50LmlubmVySFRNTCA9IGNvbnRlbnQ7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBhZmZlY3RlZC5wdXNoKGVsZW1lbnQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBGaXJlIHVwZGF0ZSBldmVudCBmb3IgZWFjaCBlbGVtZW50IHRoYXQgaXMgdXBkYXRlZFxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhVcGRhdGUnLCBlbGVtZW50LCBjb250ZW50LCB0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGV2ZW50ID0gbmV3IEV2ZW50KCdhamF4VXBkYXRlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBldmVudC5jb250ZW50ID0gY29udGVudDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheFVwZGF0ZUNvbXBsZXRlJywgYWZmZWN0ZWQsIHRoaXMpO1xuXG4gICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFByb2Nlc3NlcyB0aGUgcmVzcG9uc2UgZGF0YS5cbiAgICAgKlxuICAgICAqIFRoaXMgZmlyZXMgb2ZmIGFsbCBuZWNlc3NhcnkgcHJvY2Vzc2luZyBmdW5jdGlvbnMgZGVwZW5kaW5nIG9uIHRoZSByZXNwb25zZSwgaWUuIGlmIHRoZXJlJ3MgYW55IGZsYXNoXG4gICAgICogbWVzc2FnZXMgdG8gaGFuZGxlLCBvciBhbnkgcmVkaXJlY3RzIHRvIGJlIHVuZGVydGFrZW4uXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gcmVzcG9uc2VcbiAgICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICAgKi9cbiAgICBwcm9jZXNzUmVzcG9uc2UocmVzcG9uc2UpIHtcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5zdWNjZXNzICYmIHR5cGVvZiB0aGlzLm9wdGlvbnMuc3VjY2VzcyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgaWYgKCF0aGlzLm9wdGlvbnMuc3VjY2Vzcyh0aGlzLnJlc3BvbnNlRGF0YSwgdGhpcykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBBbGxvdyBwbHVnaW5zIHRvIGNhbmNlbCBhbnkgZnVydGhlciByZXNwb25zZSBoYW5kbGluZ1xuICAgICAgICBpZiAodGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhTdWNjZXNzJywgdGhpcy5yZXNwb25zZURhdGEsIHRoaXMpID09PSBmYWxzZSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQWxsb3cgdGhlIGVsZW1lbnQgdG8gY2FuY2VsIGFueSBmdXJ0aGVyIHJlc3BvbnNlIGhhbmRsaW5nXG4gICAgICAgIGlmICh0aGlzLmVsZW1lbnQpIHtcbiAgICAgICAgICAgIGNvbnN0IGV2ZW50ID0gbmV3IEV2ZW50KCdhamF4RG9uZScsIHsgY2FuY2VsYWJsZTogdHJ1ZSB9KTtcbiAgICAgICAgICAgIGV2ZW50LnJlc3BvbnNlRGF0YSA9IHRoaXMucmVzcG9uc2VEYXRhO1xuICAgICAgICAgICAgZXZlbnQucmVxdWVzdCA9IHRoaXM7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG5cbiAgICAgICAgICAgIGlmIChldmVudC5kZWZhdWx0UHJldmVudGVkKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuZmxhc2ggJiYgcmVzcG9uc2UuWF9XSU5URVJfRkxBU0hfTUVTU0FHRVMpIHtcbiAgICAgICAgICAgIHRoaXMucHJvY2Vzc0ZsYXNoTWVzc2FnZXMocmVzcG9uc2UuWF9XSU5URVJfRkxBU0hfTUVTU0FHRVMpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgZm9yIGEgcmVkaXJlY3QgZnJvbSB0aGUgcmVzcG9uc2UsIG9yIHVzZSB0aGUgcmVkaXJlY3QgYXMgc3BlY2lmaWVkIGluIHRoZSBvcHRpb25zLlxuICAgICAgICBpZiAodGhpcy5yZWRpcmVjdCB8fCByZXNwb25zZS5YX1dJTlRFUl9SRURJUkVDVCkge1xuICAgICAgICAgICAgdGhpcy5wcm9jZXNzUmVkaXJlY3QodGhpcy5yZWRpcmVjdCB8fCByZXNwb25zZS5YX1dJTlRFUl9SRURJUkVDVCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocmVzcG9uc2UuWF9XSU5URVJfQVNTRVRTKSB7XG4gICAgICAgICAgICB0aGlzLnByb2Nlc3NBc3NldHMocmVzcG9uc2UuWF9XSU5URVJfQVNTRVRTKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFByb2Nlc3NlcyBhbiBlcnJvciByZXNwb25zZSBmcm9tIHRoZSBBSkFYIHJlcXVlc3QuXG4gICAgICpcbiAgICAgKiBUaGlzIGZpcmVzIG9mZiBhbGwgbmVjZXNzYXJ5IHByb2Nlc3NpbmcgZnVuY3Rpb25zIGRlcGVuZGluZyBvbiB0aGUgZXJyb3IgcmVzcG9uc2UsIGllLiBpZiB0aGVyZSdzIGFueSBlcnJvciBvclxuICAgICAqIHZhbGlkYXRpb24gbWVzc2FnZXMgdG8gaGFuZGxlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R8RXJyb3J9IGVycm9yXG4gICAgICovXG4gICAgcHJvY2Vzc0Vycm9yKGVycm9yKSB7XG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMuZXJyb3IgJiYgdHlwZW9mIHRoaXMub3B0aW9ucy5lcnJvciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgaWYgKCF0aGlzLm9wdGlvbnMuZXJyb3IodGhpcy5yZXNwb25zZUVycm9yLCB0aGlzKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFsbG93IHBsdWdpbnMgdG8gY2FuY2VsIGFueSBmdXJ0aGVyIGVycm9yIGhhbmRsaW5nXG4gICAgICAgIGlmICh0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheEVycm9yJywgdGhpcy5yZXNwb25zZUVycm9yLCB0aGlzKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFsbG93IHRoZSBlbGVtZW50IHRvIGNhbmNlbCBhbnkgZnVydGhlciBlcnJvciBoYW5kbGluZ1xuICAgICAgICBpZiAodGhpcy5lbGVtZW50KSB7XG4gICAgICAgICAgICBjb25zdCBldmVudCA9IG5ldyBFdmVudCgnYWpheEZhaWwnLCB7IGNhbmNlbGFibGU6IHRydWUgfSk7XG4gICAgICAgICAgICBldmVudC5yZXNwb25zZUVycm9yID0gdGhpcy5yZXNwb25zZUVycm9yO1xuICAgICAgICAgICAgZXZlbnQucmVxdWVzdCA9IHRoaXM7XG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG5cbiAgICAgICAgICAgIGlmIChldmVudC5kZWZhdWx0UHJldmVudGVkKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGVycm9yIGluc3RhbmNlb2YgRXJyb3IpIHtcbiAgICAgICAgICAgIHRoaXMucHJvY2Vzc0Vycm9yTWVzc2FnZShlcnJvci5tZXNzYWdlKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIC8vIFByb2Nlc3MgdmFsaWRhdGlvbiBlcnJvcnNcbiAgICAgICAgICAgIGlmIChlcnJvci5YX1dJTlRFUl9FUlJPUl9GSUVMRFMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnByb2Nlc3NWYWxpZGF0aW9uRXJyb3JzKGVycm9yLlhfV0lOVEVSX0VSUk9SX0ZJRUxEUyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChlcnJvci5YX1dJTlRFUl9FUlJPUl9NRVNTQUdFKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5wcm9jZXNzRXJyb3JNZXNzYWdlKGVycm9yLlhfV0lOVEVSX0VSUk9SX01FU1NBR0UpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUHJvY2Vzc2VzIGEgcmVkaXJlY3QgcmVzcG9uc2UuXG4gICAgICpcbiAgICAgKiBCeSBkZWZhdWx0LCB0aGlzIHByb2Nlc3NvciB3aWxsIHNpbXBseSByZWRpcmVjdCB0aGUgdXNlciBpbiB0aGVpciBicm93c2VyLlxuICAgICAqXG4gICAgICogUGx1Z2lucyBjYW4gYXVnbWVudCB0aGlzIGZ1bmN0aW9uYWxpdHkgZnJvbSB0aGUgYGFqYXhSZWRpcmVjdGAgZXZlbnQuIFlvdSBtYXkgYWxzbyBvdmVycmlkZSB0aGlzIGZ1bmN0aW9uYWxpdHkgb25cbiAgICAgKiBhIHBlci1yZXF1ZXN0IGJhc2lzIHRocm91Z2ggdGhlIGBoYW5kbGVSZWRpcmVjdFJlc3BvbnNlYCBjYWxsYmFjayBvcHRpb24uIElmIGEgYGZhbHNlYCBpcyByZXR1cm5lZCBmcm9tIGVpdGhlciwgdGhlXG4gICAgICogcmVkaXJlY3Qgd2lsbCBiZSBjYW5jZWxsZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsXG4gICAgICogQHJldHVybnMge3ZvaWR9XG4gICAgICovXG4gICAgcHJvY2Vzc1JlZGlyZWN0KHVybCkge1xuICAgICAgICAvLyBSdW4gYSBjdXN0b20gcGVyLXJlcXVlc3QgcmVkaXJlY3QgaGFuZGxlci4gSWYgZmFsc2UgaXMgcmV0dXJuZWQsIGRvbid0IHJ1biB0aGUgcmVkaXJlY3QuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5vcHRpb25zLmhhbmRsZVJlZGlyZWN0UmVzcG9uc2UgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLm9wdGlvbnMuaGFuZGxlUmVkaXJlY3RSZXNwb25zZS5hcHBseSh0aGlzLCBbdXJsXSkgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gQWxsb3cgcGx1Z2lucyB0byBjYW5jZWwgdGhlIHJlZGlyZWN0XG4gICAgICAgIGlmICh0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheFJlZGlyZWN0JywgdXJsLCB0aGlzKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEluZGljYXRlIHRoYXQgdGhlIEFKQVggcmVxdWVzdCBpcyBmaW5pc2hlZCBpZiB3ZSdyZSBzdGlsbCBvbiB0aGUgY3VycmVudCBwYWdlXG4gICAgICAgIC8vIHNvIHRoYXQgdGhlIGxvYWRpbmcgaW5kaWNhdG9yIGZvciByZWRpcmVjdHMgdGhhdCBqdXN0IGNoYW5nZSB0aGUgaGFzaCB2YWx1ZSBvZlxuICAgICAgICAvLyB0aGUgVVJMIGluc3RlYWQgb2YgbGVhdmluZyB0aGUgcGFnZSB3aWxsIHByb3Blcmx5IHN0b3AuXG4gICAgICAgIC8vIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL29jdG9iZXJjbXMvb2N0b2Jlci9pc3N1ZXMvMjc4MFxuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcigncG9wc3RhdGUnLCAoKSA9PiB7XG4gICAgICAgICAgICBpZiAodGhpcy5lbGVtZW50KSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZXZlbnQgPSBkb2N1bWVudC5jcmVhdGVFdmVudCgnQ3VzdG9tRXZlbnQnKTtcbiAgICAgICAgICAgICAgICBldmVudC5ldmVudE5hbWUgPSAnYWpheFJlZGlyZWN0ZWQnO1xuICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudC5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSwge1xuICAgICAgICAgICAgb25jZTogdHJ1ZSxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmFzc2lnbih1cmwpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFByb2Nlc3NlcyBhbiBlcnJvciBtZXNzYWdlLlxuICAgICAqXG4gICAgICogQnkgZGVmYXVsdCwgdGhpcyBwcm9jZXNzb3Igd2lsbCBzaW1wbHkgYWxlcnQgdGhlIHVzZXIgdGhyb3VnaCBhIHNpbXBsZSBgYWxlcnQoKWAgY2FsbC5cbiAgICAgKlxuICAgICAqIFBsdWdpbnMgY2FuIGF1Z21lbnQgdGhpcyBmdW5jdGlvbmFsaXR5IGZyb20gdGhlIGBhamF4RXJyb3JNZXNzYWdlYCBldmVudC4gWW91IG1heSBhbHNvIG92ZXJyaWRlIHRoaXMgZnVuY3Rpb25hbGl0eVxuICAgICAqIG9uIGEgcGVyLXJlcXVlc3QgYmFzaXMgdGhyb3VnaCB0aGUgYGhhbmRsZUVycm9yTWVzc2FnZWAgY2FsbGJhY2sgb3B0aW9uLiBJZiBhIGBmYWxzZWAgaXMgcmV0dXJuZWQgZnJvbSBlaXRoZXIsIHRoZVxuICAgICAqIGVycm9yIG1lc3NhZ2UgaGFuZGxpbmcgd2lsbCBiZSBjYW5jZWxsZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbWVzc2FnZVxuICAgICAqIEByZXR1cm5zIHt2b2lkfVxuICAgICAqL1xuICAgIHByb2Nlc3NFcnJvck1lc3NhZ2UobWVzc2FnZSkge1xuICAgICAgICAvLyBSdW4gYSBjdXN0b20gcGVyLXJlcXVlc3QgaGFuZGxlciBmb3IgZXJyb3IgbWVzc2FnZXMuIElmIGZhbHNlIGlzIHJldHVybmVkLCBkbyBub3QgcHJvY2VzcyB0aGUgZXJyb3IgbWVzc2FnZXNcbiAgICAgICAgLy8gYW55IGZ1cnRoZXIuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5vcHRpb25zLmhhbmRsZUVycm9yTWVzc2FnZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5oYW5kbGVFcnJvck1lc3NhZ2UuYXBwbHkodGhpcywgW21lc3NhZ2VdKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBBbGxvdyBwbHVnaW5zIHRvIGNhbmNlbCB0aGUgZXJyb3IgbWVzc2FnZSBiZWluZyBzaG93blxuICAgICAgICBpZiAodGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ2FqYXhFcnJvck1lc3NhZ2UnLCBtZXNzYWdlLCB0aGlzKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEJ5IGRlZmF1bHQsIHNob3cgYSBicm93c2VyIGVycm9yIG1lc3NhZ2VcbiAgICAgICAgd2luZG93LmFsZXJ0KG1lc3NhZ2UpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFByb2Nlc3NlcyBmbGFzaCBtZXNzYWdlcyBmcm9tIHRoZSByZXNwb25zZS5cbiAgICAgKlxuICAgICAqIEJ5IGRlZmF1bHQsIG5vIGZsYXNoIG1lc3NhZ2UgaGFuZGxpbmcgd2lsbCBvY2N1ci5cbiAgICAgKlxuICAgICAqIFBsdWdpbnMgY2FuIGF1Z21lbnQgdGhpcyBmdW5jdGlvbmFsaXR5IGZyb20gdGhlIGBhamF4Rmxhc2hNZXNzYWdlc2AgZXZlbnQuIFlvdSBtYXkgYWxzbyBvdmVycmlkZSB0aGlzIGZ1bmN0aW9uYWxpdHlcbiAgICAgKiBvbiBhIHBlci1yZXF1ZXN0IGJhc2lzIHRocm91Z2ggdGhlIGBoYW5kbGVGbGFzaE1lc3NhZ2VzYCBjYWxsYmFjayBvcHRpb24uIElmIGEgYGZhbHNlYCBpcyByZXR1cm5lZCBmcm9tIGVpdGhlciwgdGhlXG4gICAgICogZmxhc2ggbWVzc2FnZSBoYW5kbGluZyB3aWxsIGJlIGNhbmNlbGxlZC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBtZXNzYWdlc1xuICAgICAqIEByZXR1cm5zXG4gICAgICovXG4gICAgcHJvY2Vzc0ZsYXNoTWVzc2FnZXMobWVzc2FnZXMpIHtcbiAgICAgICAgLy8gUnVuIGEgY3VzdG9tIHBlci1yZXF1ZXN0IGZsYXNoIGhhbmRsZXIuIElmIGZhbHNlIGlzIHJldHVybmVkLCBkb24ndCBzaG93IHRoZSBmbGFzaCBtZXNzYWdlXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5vcHRpb25zLmhhbmRsZUZsYXNoTWVzc2FnZXMgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLm9wdGlvbnMuaGFuZGxlRmxhc2hNZXNzYWdlcy5hcHBseSh0aGlzLCBbbWVzc2FnZXNdKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNub3dib2FyZC5nbG9iYWxFdmVudCgnYWpheEZsYXNoTWVzc2FnZXMnLCBtZXNzYWdlcywgdGhpcyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUHJvY2Vzc2VzIHZhbGlkYXRpb24gZXJyb3JzIGZvciBmaWVsZHMuXG4gICAgICpcbiAgICAgKiBCeSBkZWZhdWx0LCBubyB2YWxpZGF0aW9uIGVycm9yIGhhbmRsaW5nIHdpbGwgb2NjdXIuXG4gICAgICpcbiAgICAgKiBQbHVnaW5zIGNhbiBhdWdtZW50IHRoaXMgZnVuY3Rpb25hbGl0eSBmcm9tIHRoZSBgYWpheFZhbGlkYXRpb25FcnJvcnNgIGV2ZW50LiBZb3UgbWF5IGFsc28gb3ZlcnJpZGUgdGhpcyBmdW5jdGlvbmFsaXR5XG4gICAgICogb24gYSBwZXItcmVxdWVzdCBiYXNpcyB0aHJvdWdoIHRoZSBgaGFuZGxlVmFsaWRhdGlvbkVycm9yc2AgY2FsbGJhY2sgb3B0aW9uLiBJZiBhIGBmYWxzZWAgaXMgcmV0dXJuZWQgZnJvbSBlaXRoZXIsIHRoZVxuICAgICAqIHZhbGlkYXRpb24gZXJyb3IgaGFuZGxpbmcgd2lsbCBiZSBjYW5jZWxsZWQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gZmllbGRzXG4gICAgICogQHJldHVybnNcbiAgICAgKi9cbiAgICBwcm9jZXNzVmFsaWRhdGlvbkVycm9ycyhmaWVsZHMpIHtcbiAgICAgICAgaWYgKHR5cGVvZiB0aGlzLm9wdGlvbnMuaGFuZGxlVmFsaWRhdGlvbkVycm9ycyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5oYW5kbGVWYWxpZGF0aW9uRXJyb3JzLmFwcGx5KHRoaXMsIFt0aGlzLmZvcm0sIGZpZWxkc10pID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFsbG93IHBsdWdpbnMgdG8gY2FuY2VsIHRoZSB2YWxpZGF0aW9uIGVycm9ycyBiZWluZyBoYW5kbGVkXG4gICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KCdhamF4VmFsaWRhdGlvbkVycm9ycycsIHRoaXMuZm9ybSwgZmllbGRzLCB0aGlzKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDb25maXJtcyB0aGUgcmVxdWVzdCB3aXRoIHRoZSB1c2VyIGJlZm9yZSBwcm9jZWVkaW5nLlxuICAgICAqXG4gICAgICogVGhpcyBpcyBhbiBhc3luY2hyb25vdXMgbWV0aG9kLiBCeSBkZWZhdWx0LCBpdCB3aWxsIHVzZSB0aGUgYnJvd3NlcidzIGBjb25maXJtKClgIG1ldGhvZCB0byBxdWVyeSB0aGUgdXNlciB0b1xuICAgICAqIGNvbmZpcm0gdGhlIGFjdGlvbi4gVGhpcyBtZXRob2Qgd2lsbCByZXR1cm4gYSBQcm9taXNlIHdpdGggYSBib29sZWFuIHZhbHVlIGRlcGVuZGluZyBvbiB3aGV0aGVyIHRoZSB1c2VyIGNvbmZpcm1lZFxuICAgICAqIG9yIG5vdC5cbiAgICAgKlxuICAgICAqIFBsdWdpbnMgY2FuIGF1Z21lbnQgdGhpcyBmdW5jdGlvbmFsaXR5IGZyb20gdGhlIGBhamF4Q29uZmlybU1lc3NhZ2VgIGV2ZW50LiBZb3UgbWF5IGFsc28gb3ZlcnJpZGUgdGhpcyBmdW5jdGlvbmFsaXR5XG4gICAgICogb24gYSBwZXItcmVxdWVzdCBiYXNpcyB0aHJvdWdoIHRoZSBgaGFuZGxlQ29uZmlybU1lc3NhZ2VgIGNhbGxiYWNrIG9wdGlvbi4gSWYgYSBgZmFsc2VgIGlzIHJldHVybmVkIGZyb20gZWl0aGVyLFxuICAgICAqIHRoZSBjb25maXJtYXRpb24gaXMgYXNzdW1lZCB0byBoYXZlIGJlZW4gZGVuaWVkLlxuICAgICAqXG4gICAgICogQHJldHVybnMge1Byb21pc2V9XG4gICAgICovXG4gICAgYXN5bmMgZG9Db25maXJtKCkge1xuICAgICAgICAvLyBBbGxvdyBmb3IgYSBjdXN0b20gaGFuZGxlciBmb3IgdGhlIGNvbmZpcm1hdGlvbiwgcGVyIHJlcXVlc3QuXG4gICAgICAgIGlmICh0eXBlb2YgdGhpcy5vcHRpb25zLmhhbmRsZUNvbmZpcm1NZXNzYWdlID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5vcHRpb25zLmhhbmRsZUNvbmZpcm1NZXNzYWdlLmFwcGx5KHRoaXMsIFt0aGlzLmNvbmZpcm1dKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgbm8gcGx1Z2lucyBoYXZlIGN1c3RvbWlzZWQgdGhlIGNvbmZpcm1hdGlvbiwgdXNlIGEgc2ltcGxlIGJyb3dzZXIgY29uZmlybWF0aW9uLlxuICAgICAgICBpZiAodGhpcy5zbm93Ym9hcmQubGlzdGVuc1RvRXZlbnQoJ2FqYXhDb25maXJtTWVzc2FnZScpLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgcmV0dXJuIHdpbmRvdy5jb25maXJtKHRoaXMuY29uZmlybSk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBSdW4gY3VzdG9tIHBsdWdpbiBjb25maXJtYXRpb25zXG4gICAgICAgIGNvbnN0IHByb21pc2VzID0gdGhpcy5zbm93Ym9hcmQuZ2xvYmFsUHJvbWlzZUV2ZW50KCdhamF4Q29uZmlybU1lc3NhZ2UnLCB0aGlzLmNvbmZpcm0sIHRoaXMpO1xuXG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgICBjb25zdCBmdWxmaWxsZWQgPSBhd2FpdCBwcm9taXNlcztcbiAgICAgICAgICAgIGlmIChmdWxmaWxsZWQpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIGdldCBmb3JtKCkge1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLmZvcm0pIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLm9wdGlvbnMuZm9ybTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRoaXMuZWxlbWVudCkge1xuICAgICAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMuZWxlbWVudC50YWdOYW1lID09PSAnRk9STScpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmVsZW1lbnQ7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpcy5lbGVtZW50LmNsb3Nlc3QoJ2Zvcm0nKTtcbiAgICB9XG5cbiAgICBnZXQgY29udGV4dCgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGhhbmRsZXI6IHRoaXMuaGFuZGxlcixcbiAgICAgICAgICAgIG9wdGlvbnM6IHRoaXMub3B0aW9ucyxcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICBnZXQgaGVhZGVycygpIHtcbiAgICAgICAgY29uc3QgaGVhZGVycyA9IHtcbiAgICAgICAgICAgICdYLVJlcXVlc3RlZC1XaXRoJzogJ1hNTEh0dHBSZXF1ZXN0JywgLy8gS2VlcHMgY29tcGF0aWJpbGl0eSB3aXRoIGpRdWVyeSBBSkFYXG4gICAgICAgICAgICAnWC1XSU5URVItUkVRVUVTVC1IQU5ETEVSJzogdGhpcy5oYW5kbGVyLFxuICAgICAgICAgICAgJ1gtV0lOVEVSLVJFUVVFU1QtUEFSVElBTFMnOiB0aGlzLmV4dHJhY3RQYXJ0aWFscyh0aGlzLm9wdGlvbnMudXBkYXRlIHx8IFtdKSxcbiAgICAgICAgfTtcblxuICAgICAgICBpZiAodGhpcy5mbGFzaCkge1xuICAgICAgICAgICAgaGVhZGVyc1snWC1XSU5URVItUkVRVUVTVC1GTEFTSCddID0gMTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLnhzcmZUb2tlbikge1xuICAgICAgICAgICAgaGVhZGVyc1snWC1YU1JGLVRPS0VOJ10gPSB0aGlzLnhzcmZUb2tlbjtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBoZWFkZXJzO1xuICAgIH1cblxuICAgIGdldCBsb2FkaW5nKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5vcHRpb25zLmxvYWRpbmcgfHwgZmFsc2U7XG4gICAgfVxuXG4gICAgZ2V0IHVybCgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMub3B0aW9ucy51cmwgfHwgd2luZG93LmxvY2F0aW9uLmhyZWY7XG4gICAgfVxuXG4gICAgZ2V0IHJlZGlyZWN0KCkge1xuICAgICAgICByZXR1cm4gKHRoaXMub3B0aW9ucy5yZWRpcmVjdCAmJiB0aGlzLm9wdGlvbnMucmVkaXJlY3QubGVuZ3RoKSA/IHRoaXMub3B0aW9ucy5yZWRpcmVjdCA6IG51bGw7XG4gICAgfVxuXG4gICAgZ2V0IGZsYXNoKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5vcHRpb25zLmZsYXNoIHx8IGZhbHNlO1xuICAgIH1cblxuICAgIGdldCBmaWxlcygpIHtcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5maWxlcyA9PT0gdHJ1ZSkge1xuICAgICAgICAgICAgaWYgKEZvcm1EYXRhID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNub3dib2FyZC5kZWJ1ZygnVGhpcyBicm93c2VyIGRvZXMgbm90IHN1cHBvcnQgZmlsZSB1cGxvYWRzJyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICBnZXQgeHNyZlRva2VuKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5zbm93Ym9hcmQuY29va2llKCkuZ2V0KCdYU1JGLVRPS0VOJyk7XG4gICAgfVxuXG4gICAgZ2V0IGRhdGEoKSB7XG4gICAgICAgIGNvbnN0IGRhdGEgPSAodHlwZW9mIHRoaXMub3B0aW9ucy5kYXRhID09PSAnb2JqZWN0JykgPyB0aGlzLm9wdGlvbnMuZGF0YSA6IHt9O1xuXG4gICAgICAgIGNvbnN0IGZvcm1EYXRhID0gbmV3IEZvcm1EYXRhKHRoaXMuZm9ybSB8fCB1bmRlZmluZWQpO1xuICAgICAgICBpZiAoT2JqZWN0LmtleXMoZGF0YSkubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgT2JqZWN0LmVudHJpZXMoZGF0YSkuZm9yRWFjaCgoZW50cnkpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBba2V5LCB2YWx1ZV0gPSBlbnRyeTtcbiAgICAgICAgICAgICAgICBmb3JtRGF0YS5hcHBlbmQoa2V5LCB2YWx1ZSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBmb3JtRGF0YTtcbiAgICB9XG5cbiAgICBnZXQgY29uZmlybSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMub3B0aW9ucy5jb25maXJtIHx8IGZhbHNlO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEV4dHJhY3RzIHBhcnRpYWxzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IHVwZGF0ZVxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAgICovXG4gICAgZXh0cmFjdFBhcnRpYWxzKHVwZGF0ZSkge1xuICAgICAgICByZXR1cm4gT2JqZWN0LmtleXModXBkYXRlKS5qb2luKCcmJyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVuZGVycyBhbiBlcnJvciB3aXRoIHVzZWZ1bCBkZWJ1ZyBpbmZvcm1hdGlvbi5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIGlzIHVzZWQgaW50ZXJuYWxseSB3aGVuIHRoZSBBSkFYIHJlcXVlc3QgY291bGQgbm90IGJlIGNvbXBsZXRlZCBvciBwcm9jZXNzZWQgY29ycmVjdGx5IGR1ZSB0byBhbiBlcnJvci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGV4Y2VwdGlvblxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBmaWxlXG4gICAgICogQHBhcmFtIHtOdW1iZXJ9IGxpbmVcbiAgICAgKiBAcGFyYW0ge3N0cmluZ1tdfSB0cmFjZVxuICAgICAqIEByZXR1cm5zIHtFcnJvcn1cbiAgICAgKi9cbiAgICByZW5kZXJFcnJvcihtZXNzYWdlLCBleGNlcHRpb24sIGZpbGUsIGxpbmUsIHRyYWNlKSB7XG4gICAgICAgIGNvbnN0IGVycm9yID0gbmV3IEVycm9yKG1lc3NhZ2UpO1xuICAgICAgICBlcnJvci5leGNlcHRpb24gPSBleGNlcHRpb24gfHwgbnVsbDtcbiAgICAgICAgZXJyb3IuZmlsZSA9IGZpbGUgfHwgbnVsbDtcbiAgICAgICAgZXJyb3IubGluZSA9IGxpbmUgfHwgbnVsbDtcbiAgICAgICAgZXJyb3IudHJhY2UgPSB0cmFjZSB8fCBbXTtcbiAgICAgICAgcmV0dXJuIGVycm9yO1xuICAgIH1cbn1cblxuU25vd2JvYXJkLmFkZFBsdWdpbigncmVxdWVzdCcsIFJlcXVlc3QpO1xuIl0sIm5hbWVzIjpbIlJlcXVlc3QiLCJzbm93Ym9hcmQiLCJlbGVtZW50IiwiaGFuZGxlciIsIm9wdGlvbnMiLCJtYXRjaGVkRWxlbWVudCIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsIkVycm9yIiwiZmV0Y2hPcHRpb25zIiwicmVzcG9uc2VEYXRhIiwicmVzcG9uc2VFcnJvciIsImNhbmNlbGxlZCIsImNoZWNrUmVxdWVzdCIsImdsb2JhbEV2ZW50IiwiZXZlbnQiLCJFdmVudCIsImNhbmNlbGFibGUiLCJyZXF1ZXN0IiwiZGlzcGF0Y2hFdmVudCIsImRlZmF1bHRQcmV2ZW50ZWQiLCJkb0NsaWVudFZhbGlkYXRpb24iLCJjb25maXJtIiwiZG9Db25maXJtIiwidGhlbiIsImNvbmZpcm1lZCIsImRvQWpheCIsInJlc3BvbnNlIiwicHJvY2Vzc1VwZGF0ZSIsIlhfV0lOVEVSX1NVQ0NFU1MiLCJwcm9jZXNzRXJyb3IiLCJwcm9jZXNzUmVzcG9uc2UiLCJlcnJvciIsImNvbXBsZXRlIiwiRWxlbWVudCIsInVuZGVmaW5lZCIsIm1hdGNoIiwibWV0aG9kIiwiaGVhZGVycyIsImJvZHkiLCJkYXRhIiwicmVkaXJlY3QiLCJtb2RlIiwiZmV0Y2giLCJ1cmwiLCJicm93c2VyVmFsaWRhdGUiLCJmb3JtIiwiY2hlY2tWYWxpZGl0eSIsInJlcG9ydFZhbGlkaXR5IiwiUHJvbWlzZSIsInJlc29sdmUiLCJhamF4UHJvbWlzZSIsInJlamVjdCIsImdldEZldGNoIiwib2siLCJzdGF0dXMiLCJoYXMiLCJnZXQiLCJpbmNsdWRlcyIsImpzb24iLCJyZW5kZXJFcnJvciIsIm1lc3NhZ2UiLCJleGNlcHRpb24iLCJmaWxlIiwibGluZSIsInRyYWNlIiwidGV4dCIsInJlc3BvbnNlVGV4dCIsIlhfV0lOVEVSX1JFU1BPTlNFX0NPREUiLCJwcm9taXNlIiwiYmVmb3JlVXBkYXRlIiwiYXBwbHkiLCJwYXJ0aWFscyIsIk9iamVjdCIsImVudHJpZXMiLCJmb3JFYWNoIiwiZW50cnkiLCJrZXkiLCJ2YWx1ZSIsInN1YnN0ciIsImtleXMiLCJsZW5ndGgiLCJwcm9taXNlcyIsImdsb2JhbFByb21pc2VFdmVudCIsImRvVXBkYXRlIiwid2luZG93IiwicmVxdWVzdEFuaW1hdGlvbkZyYW1lIiwiYWZmZWN0ZWQiLCJwYXJ0aWFsIiwiY29udGVudCIsInNlbGVjdG9yIiwidXBkYXRlIiwiZWxlbWVudHMiLCJxdWVyeVNlbGVjdG9yQWxsIiwiaW5uZXJIVE1MIiwicHVzaCIsInN1Y2Nlc3MiLCJmbGFzaCIsIlhfV0lOVEVSX0ZMQVNIX01FU1NBR0VTIiwicHJvY2Vzc0ZsYXNoTWVzc2FnZXMiLCJYX1dJTlRFUl9SRURJUkVDVCIsInByb2Nlc3NSZWRpcmVjdCIsIlhfV0lOVEVSX0FTU0VUUyIsInByb2Nlc3NBc3NldHMiLCJwcm9jZXNzRXJyb3JNZXNzYWdlIiwiWF9XSU5URVJfRVJST1JfRklFTERTIiwicHJvY2Vzc1ZhbGlkYXRpb25FcnJvcnMiLCJYX1dJTlRFUl9FUlJPUl9NRVNTQUdFIiwiaGFuZGxlUmVkaXJlY3RSZXNwb25zZSIsImFkZEV2ZW50TGlzdGVuZXIiLCJjcmVhdGVFdmVudCIsImV2ZW50TmFtZSIsIm9uY2UiLCJsb2NhdGlvbiIsImFzc2lnbiIsImhhbmRsZUVycm9yTWVzc2FnZSIsImFsZXJ0IiwibWVzc2FnZXMiLCJoYW5kbGVGbGFzaE1lc3NhZ2VzIiwiZmllbGRzIiwiaGFuZGxlVmFsaWRhdGlvbkVycm9ycyIsImhhbmRsZUNvbmZpcm1NZXNzYWdlIiwibGlzdGVuc1RvRXZlbnQiLCJmdWxmaWxsZWQiLCJ0YWdOYW1lIiwiY2xvc2VzdCIsImV4dHJhY3RQYXJ0aWFscyIsInhzcmZUb2tlbiIsImxvYWRpbmciLCJocmVmIiwiZmlsZXMiLCJGb3JtRGF0YSIsImRlYnVnIiwiY29va2llIiwiZm9ybURhdGEiLCJhcHBlbmQiLCJqb2luIiwiU25vd2JvYXJkIiwiUGx1Z2luQmFzZSIsImFkZFBsdWdpbiJdLCJzb3VyY2VSb290IjoiIn0=