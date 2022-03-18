(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/js/snowboard/build/snowboard.data-attr"],{

/***/ "./assets/js/snowboard/ajax/handlers/AttributeRequest.js":
/*!***************************************************************!*\
  !*** ./assets/js/snowboard/ajax/handlers/AttributeRequest.js ***!
  \***************************************************************/
/***/ (() => {

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

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
 * Enable Data Attributes API for AJAX requests.
 *
 * This is an extension of the base AJAX functionality that includes handling of HTML data attributes for processing
 * AJAX requests. It is separated from the base AJAX functionality to allow developers to opt-out of data attribute
 * requests if they do not intend to use them.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var AttributeRequest = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(AttributeRequest, _Snowboard$Singleton);

  var _super = _createSuper(AttributeRequest);

  function AttributeRequest() {
    _classCallCheck(this, AttributeRequest);

    return _super.apply(this, arguments);
  }

  _createClass(AttributeRequest, [{
    key: "listens",
    value:
    /**
     * Listeners.
     *
     * @returns {Object}
     */
    function listens() {
      return {
        ready: 'ready',
        ajaxSetup: 'onAjaxSetup'
      };
    }
    /**
     * Ready event callback.
     *
     * Attaches handlers to the window to listen for all request interactions.
     */

  }, {
    key: "ready",
    value: function ready() {
      this.attachHandlers();
      this.disableDefaultFormValidation();
    }
    /**
     * Dependencies.
     *
     * @returns {string[]}
     */

  }, {
    key: "dependencies",
    value: function dependencies() {
      return ['request', 'jsonParser'];
    }
    /**
     * Destructor.
     *
     * Detaches all handlers.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      this.detachHandlers();

      _get(_getPrototypeOf(AttributeRequest.prototype), "destructor", this).call(this);
    }
    /**
     * Attaches the necessary handlers for all request interactions.
     */

  }, {
    key: "attachHandlers",
    value: function attachHandlers() {
      var _this = this;

      window.addEventListener('change', function (event) {
        return _this.changeHandler(event);
      });
      window.addEventListener('click', function (event) {
        return _this.clickHandler(event);
      });
      window.addEventListener('keydown', function (event) {
        return _this.keyDownHandler(event);
      });
      window.addEventListener('submit', function (event) {
        return _this.submitHandler(event);
      });
    }
    /**
     * Disables default form validation for AJAX forms.
     *
     * A form that contains a `data-request` attribute to specify an AJAX call without including a `data-browser-validate`
     * attribute means that the AJAX callback function will likely be handling the validation instead.
     */

  }, {
    key: "disableDefaultFormValidation",
    value: function disableDefaultFormValidation() {
      document.querySelectorAll('form[data-request]:not([data-browser-validate])').forEach(function (form) {
        form.setAttribute('novalidate', true);
      });
    }
    /**
     * Detaches the necessary handlers for all request interactions.
     */

  }, {
    key: "detachHandlers",
    value: function detachHandlers() {
      var _this2 = this;

      window.removeEventListener('change', function (event) {
        return _this2.changeHandler(event);
      });
      window.removeEventListener('click', function (event) {
        return _this2.clickHandler(event);
      });
      window.removeEventListener('keydown', function (event) {
        return _this2.keyDownHandler(event);
      });
      window.removeEventListener('submit', function (event) {
        return _this2.submitHandler(event);
      });
    }
    /**
     * Handles changes to select, radio, checkbox and file inputs.
     *
     * @param {Event} event
     */

  }, {
    key: "changeHandler",
    value: function changeHandler(event) {
      // Check that we are changing a valid element
      if (!event.target.matches('select[data-request], input[type=radio][data-request], input[type=checkbox][data-request], input[type=file][data-request]')) {
        return;
      }

      this.processRequestOnElement(event.target);
    }
    /**
     * Handles clicks on hyperlinks and buttons.
     *
     * This event can bubble up the hierarchy to find a suitable request element.
     *
     * @param {Event} event
     */

  }, {
    key: "clickHandler",
    value: function clickHandler(event) {
      var currentElement = event.target;

      while (currentElement.tagName !== 'HTML') {
        if (!currentElement.matches('a[data-request], button[data-request], input[type=button][data-request], input[type=submit][data-request]')) {
          currentElement = currentElement.parentElement;
        } else {
          event.preventDefault();
          this.processRequestOnElement(currentElement);
          break;
        }
      }
    }
    /**
     * Handles key presses on inputs
     *
     * @param {Event} event
     */

  }, {
    key: "keyDownHandler",
    value: function keyDownHandler(event) {
      // Check that we are inputting into a valid element
      if (!event.target.matches('input')) {
        return;
      } // Check that the input type is valid


      var validTypes = ['checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'image', 'month', 'number', 'password', 'radio', 'range', 'search', 'tel', 'text', 'time', 'url', 'week'];

      if (validTypes.indexOf(event.target.getAttribute('type')) === -1) {
        return;
      }

      if (event.key === 'Enter' && event.target.matches('*[data-request]')) {
        this.processRequestOnElement(event.target);
        event.preventDefault();
        event.stopImmediatePropagation();
      } else if (event.target.matches('*[data-track-input]')) {
        this.trackInput(event.target);
      }
    }
    /**
     * Handles form submissions.
     *
     * @param {Event} event
     */

  }, {
    key: "submitHandler",
    value: function submitHandler(event) {
      // Check that we are submitting a valid form
      if (!event.target.matches('form[data-request]')) {
        return;
      }

      event.preventDefault();
      this.processRequestOnElement(event.target);
    }
    /**
     * Processes a request on a given element, using its data attributes.
     *
     * @param {HTMLElement} element
     */

  }, {
    key: "processRequestOnElement",
    value: function processRequestOnElement(element) {
      var data = element.dataset;
      var handler = String(data.request);
      var options = {
        confirm: 'requestConfirm' in data ? String(data.requestConfirm) : null,
        redirect: 'requestRedirect' in data ? String(data.requestRedirect) : null,
        loading: 'requestLoading' in data ? String(data.requestLoading) : null,
        flash: 'requestFlash' in data,
        files: 'requestFiles' in data,
        browserValidate: 'requestBrowserValidate' in data,
        form: 'requestForm' in data ? String(data.requestForm) : null,
        url: 'requestUrl' in data ? String(data.requestUrl) : null,
        update: 'requestUpdate' in data ? this.parseData(String(data.requestUpdate)) : [],
        data: 'requestData' in data ? this.parseData(String(data.requestData)) : []
      };
      this.snowboard.request(element, handler, options);
    }
    /**
     * Sets up an AJAX request via HTML attributes.
     *
     * @param {Request} request
     */

  }, {
    key: "onAjaxSetup",
    value: function onAjaxSetup(request) {
      if (!request.element) {
        return;
      }

      var fieldName = request.element.getAttribute('name');

      var data = _objectSpread(_objectSpread({}, this.getParentRequestData(request.element)), request.options.data);

      if (request.element && request.element.matches('input, textarea, select, button') && !request.form && fieldName && !request.options.data[fieldName]) {
        data[fieldName] = request.element.value;
      }

      request.options.data = data;
    }
    /**
     * Parses and collates all data from elements up the DOM hierarchy.
     *
     * @param {Element} target
     * @returns {Object}
     */

  }, {
    key: "getParentRequestData",
    value: function getParentRequestData(target) {
      var _this3 = this;

      var elements = [];
      var data = {};
      var currentElement = target;

      while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
        elements.push(currentElement.parentElement);
        currentElement = currentElement.parentElement;
      }

      elements.reverse();
      elements.forEach(function (element) {
        var elementData = element.dataset;

        if ('requestData' in elementData) {
          data = _objectSpread(_objectSpread({}, data), _this3.parseData(elementData.requestData));
        }
      });
      return data;
    }
    /**
     * Parses data in the Winter/October JSON format.
     *
     * @param {String} data
     * @returns {Object}
     */

  }, {
    key: "parseData",
    value: function parseData(data) {
      var value;

      if (data === undefined) {
        value = '';
      }

      if (_typeof(value) === 'object') {
        return value;
      }

      try {
        return this.snowboard.jsonparser().parse("{".concat(data, "}"));
      } catch (e) {
        throw new Error("Error parsing the data attribute on element: ".concat(e.message));
      }
    }
  }, {
    key: "trackInput",
    value: function trackInput(element) {
      var _this4 = this;

      var lastValue = element.dataset.lastValue;
      var interval = element.dataset.trackInput || 300;

      if (lastValue !== undefined && lastValue === element.value) {
        return;
      }

      this.resetTrackInputTimer(element);
      element.dataset.trackInput = window.setTimeout(function () {
        if (element.dataset.request) {
          _this4.processRequestOnElement(element);

          return;
        } // Traverse up the hierarchy and find a form that sends an AJAX query


        var currentElement = element;

        while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
          currentElement = currentElement.parentElement;

          if (currentElement.tagName === 'FORM' && currentElement.dataset.request) {
            _this4.processRequestOnElement(currentElement);

            break;
          }
        }
      }, interval);
    }
  }, {
    key: "resetTrackInputTimer",
    value: function resetTrackInputTimer(element) {
      if (element.dataset.trackInput) {
        window.clearTimeout(element.dataset.trackInput);
        element.dataset.trackInput = null;
      }
    }
  }]);

  return AttributeRequest;
}(Snowboard.Singleton);

Snowboard.addPlugin('attributeRequest', AttributeRequest);

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/js/snowboard/ajax/handlers/AttributeRequest.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYnVpbGQvc25vd2JvYXJkLmRhdGEtYXR0ci5qcyIsIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7SUFDTUE7Ozs7Ozs7Ozs7Ozs7O0FBQ0Y7QUFDSjtBQUNBO0FBQ0E7QUFDQTtBQUNJLHVCQUFVO0FBQ04sYUFBTztBQUNIQyxRQUFBQSxLQUFLLEVBQUUsT0FESjtBQUVIQyxRQUFBQSxTQUFTLEVBQUU7QUFGUixPQUFQO0FBSUg7QUFFRDtBQUNKO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksaUJBQVE7QUFDSixXQUFLQyxjQUFMO0FBQ0EsV0FBS0MsNEJBQUw7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSx3QkFBZTtBQUNYLGFBQU8sQ0FBQyxTQUFELEVBQVksWUFBWixDQUFQO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksc0JBQWE7QUFDVCxXQUFLQyxjQUFMOztBQUVBO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7Ozs7V0FDSSwwQkFBaUI7QUFBQTs7QUFDYkMsTUFBQUEsTUFBTSxDQUFDQyxnQkFBUCxDQUF3QixRQUF4QixFQUFrQyxVQUFDQyxLQUFEO0FBQUEsZUFBVyxLQUFJLENBQUNDLGFBQUwsQ0FBbUJELEtBQW5CLENBQVg7QUFBQSxPQUFsQztBQUNBRixNQUFBQSxNQUFNLENBQUNDLGdCQUFQLENBQXdCLE9BQXhCLEVBQWlDLFVBQUNDLEtBQUQ7QUFBQSxlQUFXLEtBQUksQ0FBQ0UsWUFBTCxDQUFrQkYsS0FBbEIsQ0FBWDtBQUFBLE9BQWpDO0FBQ0FGLE1BQUFBLE1BQU0sQ0FBQ0MsZ0JBQVAsQ0FBd0IsU0FBeEIsRUFBbUMsVUFBQ0MsS0FBRDtBQUFBLGVBQVcsS0FBSSxDQUFDRyxjQUFMLENBQW9CSCxLQUFwQixDQUFYO0FBQUEsT0FBbkM7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QSxZQUFJVixjQUFjLEdBQUdVLE9BQXJCOztBQUNBLGVBQU9WLGNBQWMsQ0FBQ0UsYUFBZixJQUFnQ0YsY0FBYyxDQUFDRSxhQUFmLENBQTZCRCxPQUE3QixLQUF5QyxNQUFoRixFQUF3RjtBQUNwRkQsVUFBQUEsY0FBYyxHQUFHQSxjQUFjLENBQUNFLGFBQWhDOztBQUVBLGNBQUlGLGNBQWMsQ0FBQ0MsT0FBZixLQUEyQixNQUEzQixJQUFxQ0QsY0FBYyxDQUFDWSxPQUFmLENBQXVCRyxPQUFoRSxFQUF5RTtBQUNyRSxrQkFBSSxDQUFDaEIsdUJBQUwsQ0FBNkJDLGNBQTdCOztBQUNBO0FBQ0g7QUFDSjtBQUNKLE9BaEI0QixFQWdCMUJnRCxRQWhCMEIsQ0FBN0I7QUFpQkg7OztXQUVELDhCQUFxQnRDLE9BQXJCLEVBQThCO0FBQzFCLFVBQUlBLE9BQU8sQ0FBQ0UsT0FBUixDQUFnQkgsVUFBcEIsRUFBZ0M7QUFDNUJ6QixRQUFBQSxNQUFNLENBQUNtRSxZQUFQLENBQW9CekMsT0FBTyxDQUFDRSxPQUFSLENBQWdCSCxVQUFwQztBQUNBQyxRQUFBQSxPQUFPLENBQUNFLE9BQVIsQ0FBZ0JILFVBQWhCLEdBQTZCLElBQTdCO0FBQ0g7QUFDSjs7OztFQTdUMEIyQyxTQUFTLENBQUNDOztBQWdVekNELFNBQVMsQ0FBQ0UsU0FBVixDQUFvQixrQkFBcEIsRUFBd0M1RSxnQkFBeEMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy9qcy9zbm93Ym9hcmQvYWpheC9oYW5kbGVycy9BdHRyaWJ1dGVSZXF1ZXN0LmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogRW5hYmxlIERhdGEgQXR0cmlidXRlcyBBUEkgZm9yIEFKQVggcmVxdWVzdHMuXG4gKlxuICogVGhpcyBpcyBhbiBleHRlbnNpb24gb2YgdGhlIGJhc2UgQUpBWCBmdW5jdGlvbmFsaXR5IHRoYXQgaW5jbHVkZXMgaGFuZGxpbmcgb2YgSFRNTCBkYXRhIGF0dHJpYnV0ZXMgZm9yIHByb2Nlc3NpbmdcbiAqIEFKQVggcmVxdWVzdHMuIEl0IGlzIHNlcGFyYXRlZCBmcm9tIHRoZSBiYXNlIEFKQVggZnVuY3Rpb25hbGl0eSB0byBhbGxvdyBkZXZlbG9wZXJzIHRvIG9wdC1vdXQgb2YgZGF0YSBhdHRyaWJ1dGVcbiAqIHJlcXVlc3RzIGlmIHRoZXkgZG8gbm90IGludGVuZCB0byB1c2UgdGhlbS5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuY2xhc3MgQXR0cmlidXRlUmVxdWVzdCBleHRlbmRzIFNub3dib2FyZC5TaW5nbGV0b24ge1xuICAgIC8qKlxuICAgICAqIExpc3RlbmVycy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICAgICAgYWpheFNldHVwOiAnb25BamF4U2V0dXAnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlYWR5IGV2ZW50IGNhbGxiYWNrLlxuICAgICAqXG4gICAgICogQXR0YWNoZXMgaGFuZGxlcnMgdG8gdGhlIHdpbmRvdyB0byBsaXN0ZW4gZm9yIGFsbCByZXF1ZXN0IGludGVyYWN0aW9ucy5cbiAgICAgKi9cbiAgICByZWFkeSgpIHtcbiAgICAgICAgdGhpcy5hdHRhY2hIYW5kbGVycygpO1xuICAgICAgICB0aGlzLmRpc2FibGVEZWZhdWx0Rm9ybVZhbGlkYXRpb24oKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXBlbmRlbmNpZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7c3RyaW5nW119XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWydyZXF1ZXN0JywgJ2pzb25QYXJzZXInXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXN0cnVjdG9yLlxuICAgICAqXG4gICAgICogRGV0YWNoZXMgYWxsIGhhbmRsZXJzLlxuICAgICAqL1xuICAgIGRlc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMuZGV0YWNoSGFuZGxlcnMoKTtcblxuICAgICAgICBzdXBlci5kZXN0cnVjdG9yKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQXR0YWNoZXMgdGhlIG5lY2Vzc2FyeSBoYW5kbGVycyBmb3IgYWxsIHJlcXVlc3QgaW50ZXJhY3Rpb25zLlxuICAgICAqL1xuICAgIGF0dGFjaEhhbmRsZXJzKCkge1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgKGV2ZW50KSA9PiB0aGlzLmNoYW5nZUhhbmRsZXIoZXZlbnQpKTtcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgKGV2ZW50KSA9PiB0aGlzLmNsaWNrSGFuZGxlcihldmVudCkpO1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcigna2V5ZG93bicsIChldmVudCkgPT4gdGhpcy5rZXlEb3duSGFuZGxlcihldmVudCkpO1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcignc3VibWl0JywgKGV2ZW50KSA9PiB0aGlzLnN1Ym1pdEhhbmRsZXIoZXZlbnQpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEaXNhYmxlcyBkZWZhdWx0IGZvcm0gdmFsaWRhdGlvbiBmb3IgQUpBWCBmb3Jtcy5cbiAgICAgKlxuICAgICAqIEEgZm9ybSB0aGF0IGNvbnRhaW5zIGEgYGRhdGEtcmVxdWVzdGAgYXR0cmlidXRlIHRvIHNwZWNpZnkgYW4gQUpBWCBjYWxsIHdpdGhvdXQgaW5jbHVkaW5nIGEgYGRhdGEtYnJvd3Nlci12YWxpZGF0ZWBcbiAgICAgKiBhdHRyaWJ1dGUgbWVhbnMgdGhhdCB0aGUgQUpBWCBjYWxsYmFjayBmdW5jdGlvbiB3aWxsIGxpa2VseSBiZSBoYW5kbGluZyB0aGUgdmFsaWRhdGlvbiBpbnN0ZWFkLlxuICAgICAqL1xuICAgIGRpc2FibGVEZWZhdWx0Rm9ybVZhbGlkYXRpb24oKSB7XG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ2Zvcm1bZGF0YS1yZXF1ZXN0XTpub3QoW2RhdGEtYnJvd3Nlci12YWxpZGF0ZV0pJykuZm9yRWFjaCgoZm9ybSkgPT4ge1xuICAgICAgICAgICAgZm9ybS5zZXRBdHRyaWJ1dGUoJ25vdmFsaWRhdGUnLCB0cnVlKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGV0YWNoZXMgdGhlIG5lY2Vzc2FyeSBoYW5kbGVycyBmb3IgYWxsIHJlcXVlc3QgaW50ZXJhY3Rpb25zLlxuICAgICAqL1xuICAgIGRldGFjaEhhbmRsZXJzKCkge1xuICAgICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgKGV2ZW50KSA9PiB0aGlzLmNoYW5nZUhhbmRsZXIoZXZlbnQpKTtcbiAgICAgICAgd2luZG93LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgKGV2ZW50KSA9PiB0aGlzLmNsaWNrSGFuZGxlcihldmVudCkpO1xuICAgICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcigna2V5ZG93bicsIChldmVudCkgPT4gdGhpcy5rZXlEb3duSGFuZGxlcihldmVudCkpO1xuICAgICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcignc3VibWl0JywgKGV2ZW50KSA9PiB0aGlzLnN1Ym1pdEhhbmRsZXIoZXZlbnQpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGVzIGNoYW5nZXMgdG8gc2VsZWN0LCByYWRpbywgY2hlY2tib3ggYW5kIGZpbGUgaW5wdXRzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICAgKi9cbiAgICBjaGFuZ2VIYW5kbGVyKGV2ZW50KSB7XG4gICAgICAgIC8vIENoZWNrIHRoYXQgd2UgYXJlIGNoYW5naW5nIGEgdmFsaWQgZWxlbWVudFxuICAgICAgICBpZiAoIWV2ZW50LnRhcmdldC5tYXRjaGVzKFxuICAgICAgICAgICAgJ3NlbGVjdFtkYXRhLXJlcXVlc3RdLCBpbnB1dFt0eXBlPXJhZGlvXVtkYXRhLXJlcXVlc3RdLCBpbnB1dFt0eXBlPWNoZWNrYm94XVtkYXRhLXJlcXVlc3RdLCBpbnB1dFt0eXBlPWZpbGVdW2RhdGEtcmVxdWVzdF0nLFxuICAgICAgICApKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnByb2Nlc3NSZXF1ZXN0T25FbGVtZW50KGV2ZW50LnRhcmdldCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyBjbGlja3Mgb24gaHlwZXJsaW5rcyBhbmQgYnV0dG9ucy5cbiAgICAgKlxuICAgICAqIFRoaXMgZXZlbnQgY2FuIGJ1YmJsZSB1cCB0aGUgaGllcmFyY2h5IHRvIGZpbmQgYSBzdWl0YWJsZSByZXF1ZXN0IGVsZW1lbnQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgICAqL1xuICAgIGNsaWNrSGFuZGxlcihldmVudCkge1xuICAgICAgICBsZXQgY3VycmVudEVsZW1lbnQgPSBldmVudC50YXJnZXQ7XG5cbiAgICAgICAgd2hpbGUgKGN1cnJlbnRFbGVtZW50LnRhZ05hbWUgIT09ICdIVE1MJykge1xuICAgICAgICAgICAgaWYgKCFjdXJyZW50RWxlbWVudC5tYXRjaGVzKFxuICAgICAgICAgICAgICAgICdhW2RhdGEtcmVxdWVzdF0sIGJ1dHRvbltkYXRhLXJlcXVlc3RdLCBpbnB1dFt0eXBlPWJ1dHRvbl1bZGF0YS1yZXF1ZXN0XSwgaW5wdXRbdHlwZT1zdWJtaXRdW2RhdGEtcmVxdWVzdF0nLFxuICAgICAgICAgICAgKSkge1xuICAgICAgICAgICAgICAgIGN1cnJlbnRFbGVtZW50ID0gY3VycmVudEVsZW1lbnQucGFyZW50RWxlbWVudDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnByb2Nlc3NSZXF1ZXN0T25FbGVtZW50KGN1cnJlbnRFbGVtZW50KTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhhbmRsZXMga2V5IHByZXNzZXMgb24gaW5wdXRzXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgICAqL1xuICAgIGtleURvd25IYW5kbGVyKGV2ZW50KSB7XG4gICAgICAgIC8vIENoZWNrIHRoYXQgd2UgYXJlIGlucHV0dGluZyBpbnRvIGEgdmFsaWQgZWxlbWVudFxuICAgICAgICBpZiAoIWV2ZW50LnRhcmdldC5tYXRjaGVzKFxuICAgICAgICAgICAgJ2lucHV0JyxcbiAgICAgICAgKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgdGhhdCB0aGUgaW5wdXQgdHlwZSBpcyB2YWxpZFxuICAgICAgICBjb25zdCB2YWxpZFR5cGVzID0gW1xuICAgICAgICAgICAgJ2NoZWNrYm94JyxcbiAgICAgICAgICAgICdjb2xvcicsXG4gICAgICAgICAgICAnZGF0ZScsXG4gICAgICAgICAgICAnZGF0ZXRpbWUnLFxuICAgICAgICAgICAgJ2RhdGV0aW1lLWxvY2FsJyxcbiAgICAgICAgICAgICdlbWFpbCcsXG4gICAgICAgICAgICAnaW1hZ2UnLFxuICAgICAgICAgICAgJ21vbnRoJyxcbiAgICAgICAgICAgICdudW1iZXInLFxuICAgICAgICAgICAgJ3Bhc3N3b3JkJyxcbiAgICAgICAgICAgICdyYWRpbycsXG4gICAgICAgICAgICAncmFuZ2UnLFxuICAgICAgICAgICAgJ3NlYXJjaCcsXG4gICAgICAgICAgICAndGVsJyxcbiAgICAgICAgICAgICd0ZXh0JyxcbiAgICAgICAgICAgICd0aW1lJyxcbiAgICAgICAgICAgICd1cmwnLFxuICAgICAgICAgICAgJ3dlZWsnLFxuICAgICAgICBdO1xuICAgICAgICBpZiAodmFsaWRUeXBlcy5pbmRleE9mKGV2ZW50LnRhcmdldC5nZXRBdHRyaWJ1dGUoJ3R5cGUnKSkgPT09IC0xKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoZXZlbnQua2V5ID09PSAnRW50ZXInICYmIGV2ZW50LnRhcmdldC5tYXRjaGVzKCcqW2RhdGEtcmVxdWVzdF0nKSkge1xuICAgICAgICAgICAgdGhpcy5wcm9jZXNzUmVxdWVzdE9uRWxlbWVudChldmVudC50YXJnZXQpO1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BJbW1lZGlhdGVQcm9wYWdhdGlvbigpO1xuICAgICAgICB9IGVsc2UgaWYgKGV2ZW50LnRhcmdldC5tYXRjaGVzKCcqW2RhdGEtdHJhY2staW5wdXRdJykpIHtcbiAgICAgICAgICAgIHRoaXMudHJhY2tJbnB1dChldmVudC50YXJnZXQpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyBmb3JtIHN1Ym1pc3Npb25zLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICAgKi9cbiAgICBzdWJtaXRIYW5kbGVyKGV2ZW50KSB7XG4gICAgICAgIC8vIENoZWNrIHRoYXQgd2UgYXJlIHN1Ym1pdHRpbmcgYSB2YWxpZCBmb3JtXG4gICAgICAgIGlmICghZXZlbnQudGFyZ2V0Lm1hdGNoZXMoXG4gICAgICAgICAgICAnZm9ybVtkYXRhLXJlcXVlc3RdJyxcbiAgICAgICAgKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB0aGlzLnByb2Nlc3NSZXF1ZXN0T25FbGVtZW50KGV2ZW50LnRhcmdldCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUHJvY2Vzc2VzIGEgcmVxdWVzdCBvbiBhIGdpdmVuIGVsZW1lbnQsIHVzaW5nIGl0cyBkYXRhIGF0dHJpYnV0ZXMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICovXG4gICAgcHJvY2Vzc1JlcXVlc3RPbkVsZW1lbnQoZWxlbWVudCkge1xuICAgICAgICBjb25zdCBkYXRhID0gZWxlbWVudC5kYXRhc2V0O1xuXG4gICAgICAgIGNvbnN0IGhhbmRsZXIgPSBTdHJpbmcoZGF0YS5yZXF1ZXN0KTtcbiAgICAgICAgY29uc3Qgb3B0aW9ucyA9IHtcbiAgICAgICAgICAgIGNvbmZpcm06ICgncmVxdWVzdENvbmZpcm0nIGluIGRhdGEpID8gU3RyaW5nKGRhdGEucmVxdWVzdENvbmZpcm0pIDogbnVsbCxcbiAgICAgICAgICAgIHJlZGlyZWN0OiAoJ3JlcXVlc3RSZWRpcmVjdCcgaW4gZGF0YSkgPyBTdHJpbmcoZGF0YS5yZXF1ZXN0UmVkaXJlY3QpIDogbnVsbCxcbiAgICAgICAgICAgIGxvYWRpbmc6ICgncmVxdWVzdExvYWRpbmcnIGluIGRhdGEpID8gU3RyaW5nKGRhdGEucmVxdWVzdExvYWRpbmcpIDogbnVsbCxcbiAgICAgICAgICAgIGZsYXNoOiAoJ3JlcXVlc3RGbGFzaCcgaW4gZGF0YSksXG4gICAgICAgICAgICBmaWxlczogKCdyZXF1ZXN0RmlsZXMnIGluIGRhdGEpLFxuICAgICAgICAgICAgYnJvd3NlclZhbGlkYXRlOiAoJ3JlcXVlc3RCcm93c2VyVmFsaWRhdGUnIGluIGRhdGEpLFxuICAgICAgICAgICAgZm9ybTogKCdyZXF1ZXN0Rm9ybScgaW4gZGF0YSkgPyBTdHJpbmcoZGF0YS5yZXF1ZXN0Rm9ybSkgOiBudWxsLFxuICAgICAgICAgICAgdXJsOiAoJ3JlcXVlc3RVcmwnIGluIGRhdGEpID8gU3RyaW5nKGRhdGEucmVxdWVzdFVybCkgOiBudWxsLFxuICAgICAgICAgICAgdXBkYXRlOiAoJ3JlcXVlc3RVcGRhdGUnIGluIGRhdGEpID8gdGhpcy5wYXJzZURhdGEoU3RyaW5nKGRhdGEucmVxdWVzdFVwZGF0ZSkpIDogW10sXG4gICAgICAgICAgICBkYXRhOiAoJ3JlcXVlc3REYXRhJyBpbiBkYXRhKSA/IHRoaXMucGFyc2VEYXRhKFN0cmluZyhkYXRhLnJlcXVlc3REYXRhKSkgOiBbXSxcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLnNub3dib2FyZC5yZXF1ZXN0KGVsZW1lbnQsIGhhbmRsZXIsIG9wdGlvbnMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNldHMgdXAgYW4gQUpBWCByZXF1ZXN0IHZpYSBIVE1MIGF0dHJpYnV0ZXMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1JlcXVlc3R9IHJlcXVlc3RcbiAgICAgKi9cbiAgICBvbkFqYXhTZXR1cChyZXF1ZXN0KSB7XG4gICAgICAgIGlmICghcmVxdWVzdC5lbGVtZW50KSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBmaWVsZE5hbWUgPSByZXF1ZXN0LmVsZW1lbnQuZ2V0QXR0cmlidXRlKCduYW1lJyk7XG5cbiAgICAgICAgY29uc3QgZGF0YSA9IHtcbiAgICAgICAgICAgIC4uLnRoaXMuZ2V0UGFyZW50UmVxdWVzdERhdGEocmVxdWVzdC5lbGVtZW50KSxcbiAgICAgICAgICAgIC4uLnJlcXVlc3Qub3B0aW9ucy5kYXRhLFxuICAgICAgICB9O1xuXG4gICAgICAgIGlmIChyZXF1ZXN0LmVsZW1lbnQgJiYgcmVxdWVzdC5lbGVtZW50Lm1hdGNoZXMoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0LCBidXR0b24nKSAmJiAhcmVxdWVzdC5mb3JtICYmIGZpZWxkTmFtZSAmJiAhcmVxdWVzdC5vcHRpb25zLmRhdGFbZmllbGROYW1lXSkge1xuICAgICAgICAgICAgZGF0YVtmaWVsZE5hbWVdID0gcmVxdWVzdC5lbGVtZW50LnZhbHVlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmVxdWVzdC5vcHRpb25zLmRhdGEgPSBkYXRhO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFBhcnNlcyBhbmQgY29sbGF0ZXMgYWxsIGRhdGEgZnJvbSBlbGVtZW50cyB1cCB0aGUgRE9NIGhpZXJhcmNoeS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7RWxlbWVudH0gdGFyZ2V0XG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBnZXRQYXJlbnRSZXF1ZXN0RGF0YSh0YXJnZXQpIHtcbiAgICAgICAgY29uc3QgZWxlbWVudHMgPSBbXTtcbiAgICAgICAgbGV0IGRhdGEgPSB7fTtcbiAgICAgICAgbGV0IGN1cnJlbnRFbGVtZW50ID0gdGFyZ2V0O1xuXG4gICAgICAgIHdoaWxlIChjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50ICYmIGN1cnJlbnRFbGVtZW50LnBhcmVudEVsZW1lbnQudGFnTmFtZSAhPT0gJ0hUTUwnKSB7XG4gICAgICAgICAgICBlbGVtZW50cy5wdXNoKGN1cnJlbnRFbGVtZW50LnBhcmVudEVsZW1lbnQpO1xuICAgICAgICAgICAgY3VycmVudEVsZW1lbnQgPSBjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50O1xuICAgICAgICB9XG5cbiAgICAgICAgZWxlbWVudHMucmV2ZXJzZSgpO1xuXG4gICAgICAgIGVsZW1lbnRzLmZvckVhY2goKGVsZW1lbnQpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGVsZW1lbnREYXRhID0gZWxlbWVudC5kYXRhc2V0O1xuXG4gICAgICAgICAgICBpZiAoJ3JlcXVlc3REYXRhJyBpbiBlbGVtZW50RGF0YSkge1xuICAgICAgICAgICAgICAgIGRhdGEgPSB7XG4gICAgICAgICAgICAgICAgICAgIC4uLmRhdGEsXG4gICAgICAgICAgICAgICAgICAgIC4uLnRoaXMucGFyc2VEYXRhKGVsZW1lbnREYXRhLnJlcXVlc3REYXRhKSxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZGF0YTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBQYXJzZXMgZGF0YSBpbiB0aGUgV2ludGVyL09jdG9iZXIgSlNPTiBmb3JtYXQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZGF0YVxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgcGFyc2VEYXRhKGRhdGEpIHtcbiAgICAgICAgbGV0IHZhbHVlO1xuXG4gICAgICAgIGlmIChkYXRhID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHZhbHVlID0gJyc7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcpIHtcbiAgICAgICAgICAgIHJldHVybiB2YWx1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5zbm93Ym9hcmQuanNvbnBhcnNlcigpLnBhcnNlKGB7JHtkYXRhfX1gKTtcbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKGBFcnJvciBwYXJzaW5nIHRoZSBkYXRhIGF0dHJpYnV0ZSBvbiBlbGVtZW50OiAke2UubWVzc2FnZX1gKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHRyYWNrSW5wdXQoZWxlbWVudCkge1xuICAgICAgICBjb25zdCB7IGxhc3RWYWx1ZSB9ID0gZWxlbWVudC5kYXRhc2V0O1xuICAgICAgICBjb25zdCBpbnRlcnZhbCA9IGVsZW1lbnQuZGF0YXNldC50cmFja0lucHV0IHx8IDMwMDtcblxuICAgICAgICBpZiAobGFzdFZhbHVlICE9PSB1bmRlZmluZWQgJiYgbGFzdFZhbHVlID09PSBlbGVtZW50LnZhbHVlKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnJlc2V0VHJhY2tJbnB1dFRpbWVyKGVsZW1lbnQpO1xuXG4gICAgICAgIGVsZW1lbnQuZGF0YXNldC50cmFja0lucHV0ID0gd2luZG93LnNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgaWYgKGVsZW1lbnQuZGF0YXNldC5yZXF1ZXN0KSB7XG4gICAgICAgICAgICAgICAgdGhpcy5wcm9jZXNzUmVxdWVzdE9uRWxlbWVudChlbGVtZW50KTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIFRyYXZlcnNlIHVwIHRoZSBoaWVyYXJjaHkgYW5kIGZpbmQgYSBmb3JtIHRoYXQgc2VuZHMgYW4gQUpBWCBxdWVyeVxuICAgICAgICAgICAgbGV0IGN1cnJlbnRFbGVtZW50ID0gZWxlbWVudDtcbiAgICAgICAgICAgIHdoaWxlIChjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50ICYmIGN1cnJlbnRFbGVtZW50LnBhcmVudEVsZW1lbnQudGFnTmFtZSAhPT0gJ0hUTUwnKSB7XG4gICAgICAgICAgICAgICAgY3VycmVudEVsZW1lbnQgPSBjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50O1xuXG4gICAgICAgICAgICAgICAgaWYgKGN1cnJlbnRFbGVtZW50LnRhZ05hbWUgPT09ICdGT1JNJyAmJiBjdXJyZW50RWxlbWVudC5kYXRhc2V0LnJlcXVlc3QpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wcm9jZXNzUmVxdWVzdE9uRWxlbWVudChjdXJyZW50RWxlbWVudCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSwgaW50ZXJ2YWwpO1xuICAgIH1cblxuICAgIHJlc2V0VHJhY2tJbnB1dFRpbWVyKGVsZW1lbnQpIHtcbiAgICAgICAgaWYgKGVsZW1lbnQuZGF0YXNldC50cmFja0lucHV0KSB7XG4gICAgICAgICAgICB3aW5kb3cuY2xlYXJUaW1lb3V0KGVsZW1lbnQuZGF0YXNldC50cmFja0lucHV0KTtcbiAgICAgICAgICAgIGVsZW1lbnQuZGF0YXNldC50cmFja0lucHV0ID0gbnVsbDtcbiAgICAgICAgfVxuICAgIH1cbn1cblxuU25vd2JvYXJkLmFkZFBsdWdpbignYXR0cmlidXRlUmVxdWVzdCcsIEF0dHJpYnV0ZVJlcXVlc3QpO1xuIl0sIm5hbWVzIjpbIkF0dHJpYnV0ZVJlcXVlc3QiLCJyZWFkeSIsImFqYXhTZXR1cCIsImF0dGFjaEhhbmRsZXJzIiwiZGlzYWJsZURlZmF1bHRGb3JtVmFsaWRhdGlvbiIsImRldGFjaEhhbmRsZXJzIiwid2luZG93IiwiYWRkRXZlbnRMaXN0ZW5lciIsImV2ZW50IiwiY2hhbmdlSGFuZGxlciIsImNsaWNrSGFuZGxlciIsImtleURvd25IYW5kbGVyIiwic3VibWl0SGFuZGxlciIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvckFsbCIsImZvckVhY2giLCJmb3JtIiwic2V0QXR0cmlidXRlIiwicmVtb3ZlRXZlbnRMaXN0ZW5lciIsInRhcmdldCIsIm1hdGNoZXMiLCJwcm9jZXNzUmVxdWVzdE9uRWxlbWVudCIsImN1cnJlbnRFbGVtZW50IiwidGFnTmFtZSIsInBhcmVudEVsZW1lbnQiLCJwcmV2ZW50RGVmYXVsdCIsInZhbGlkVHlwZXMiLCJpbmRleE9mIiwiZ2V0QXR0cmlidXRlIiwia2V5Iiwic3RvcEltbWVkaWF0ZVByb3BhZ2F0aW9uIiwidHJhY2tJbnB1dCIsImVsZW1lbnQiLCJkYXRhIiwiZGF0YXNldCIsImhhbmRsZXIiLCJTdHJpbmciLCJyZXF1ZXN0Iiwib3B0aW9ucyIsImNvbmZpcm0iLCJyZXF1ZXN0Q29uZmlybSIsInJlZGlyZWN0IiwicmVxdWVzdFJlZGlyZWN0IiwibG9hZGluZyIsInJlcXVlc3RMb2FkaW5nIiwiZmxhc2giLCJmaWxlcyIsImJyb3dzZXJWYWxpZGF0ZSIsInJlcXVlc3RGb3JtIiwidXJsIiwicmVxdWVzdFVybCIsInVwZGF0ZSIsInBhcnNlRGF0YSIsInJlcXVlc3RVcGRhdGUiLCJyZXF1ZXN0RGF0YSIsInNub3dib2FyZCIsImZpZWxkTmFtZSIsImdldFBhcmVudFJlcXVlc3REYXRhIiwidmFsdWUiLCJlbGVtZW50cyIsInB1c2giLCJyZXZlcnNlIiwiZWxlbWVudERhdGEiLCJ1bmRlZmluZWQiLCJqc29ucGFyc2VyIiwicGFyc2UiLCJlIiwiRXJyb3IiLCJtZXNzYWdlIiwibGFzdFZhbHVlIiwiaW50ZXJ2YWwiLCJyZXNldFRyYWNrSW5wdXRUaW1lciIsInNldFRpbWVvdXQiLCJjbGVhclRpbWVvdXQiLCJTbm93Ym9hcmQiLCJTaW5nbGV0b24iLCJhZGRQbHVnaW4iXSwic291cmNlUm9vdCI6IiJ9