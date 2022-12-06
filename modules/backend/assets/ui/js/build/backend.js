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
      if (!window.jQuery) {
        return;
      } // Add "render" event for backwards compatibility


      window.jQuery(document).trigger('render');
    }
    /**
     * Adds the jQuery AJAX prefilter that the old framework uses to inject the CSRF token in AJAX
     * calls.
     */

  }, {
    key: "addPrefilter",
    value: function addPrefilter() {
      var _this = this;

      if (!window.jQuery) {
        return;
      }

      window.jQuery.ajaxPrefilter(function (options) {
        if (_this.hasToken()) {
          if (!options.headers) {
            options.headers = {};
          }

          options.headers['X-CSRF-TOKEN'] = _this.getToken();
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
      } // Add "render" event for backwards compatibility


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
/************************************************************************/
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*******************************!*\
  !*** ./assets/ui/js/index.js ***!
  \*******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ajax_Handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ajax/Handler */ "./assets/ui/js/ajax/Handler.js");


if (window.Snowboard === undefined) {
  throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

(function (Snowboard) {
  Snowboard.addPlugin('backend.ajax.handler', _ajax_Handler__WEBPACK_IMPORTED_MODULE_0__["default"]); // Add the pre-filter immediately

  Snowboard['backend.ajax.handler']().addPrefilter(); // Add polyfill for AssetManager

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
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QTs7QUFFQSxJQUFJZCxNQUFNLENBQUNhLFNBQVAsS0FBcUJHLFNBQXpCLEVBQW9DO0VBQ2hDLE1BQU0sSUFBSUMsS0FBSixDQUFVLDBEQUFWLENBQU47QUFDSDs7QUFFRCxDQUFDLFVBQUNKLFNBQUQsRUFBZTtFQUNaQSxTQUFTLENBQUNLLFNBQVYsQ0FBb0Isc0JBQXBCLEVBQTRDSCxxREFBNUMsRUFEWSxDQUVaOztFQUNBRixTQUFTLENBQUMsc0JBQUQsQ0FBVCxHQUFvQ00sWUFBcEMsR0FIWSxDQUtaOztFQUNBbkIsTUFBTSxDQUFDb0IsWUFBUCxHQUFzQjtJQUNsQkMsSUFBSSxFQUFFLGNBQUNDLE1BQUQsRUFBU0MsUUFBVCxFQUFzQjtNQUN4QlYsU0FBUyxDQUFDVyxXQUFWLEdBQXdCSCxJQUF4QixDQUE2QkMsTUFBN0IsRUFBcUNHLElBQXJDLENBQ0ksWUFBTTtRQUNGLElBQUlGLFFBQVEsSUFBSSxPQUFPQSxRQUFQLEtBQW9CLFVBQXBDLEVBQWdEO1VBQzVDQSxRQUFRO1FBQ1g7TUFDSixDQUxMO0lBT0g7RUFUaUIsQ0FBdEI7RUFXQXZCLE1BQU0sQ0FBQzBCLFlBQVAsR0FBc0IxQixNQUFNLENBQUNvQixZQUE3QjtBQUNILENBbEJELEVBa0JHcEIsTUFBTSxDQUFDYSxTQWxCVixFIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9hamF4L0hhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9kZWZpbmUgcHJvcGVydHkgZ2V0dGVycyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvLi9hc3NldHMvdWkvanMvaW5kZXguanMiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBCYWNrZW5kIEFKQVggaGFuZGxlci5cbiAqXG4gKiBUaGlzIGlzIGEgdXRpbGl0eSBzY3JpcHQgdGhhdCByZXNvbHZlcyBzb21lIGJhY2t3YXJkcy1jb21wYXRpYmlsaXR5IGlzc3VlcyB3aXRoIHRoZSBmdW5jdGlvbmFsaXR5XG4gKiB0aGF0IHJlbGllcyBvbiB0aGUgb2xkIGZyYW1ld29yaywgYW5kIGVuc3VyZXMgdGhhdCBTbm93Ym9hcmQgd29ya3Mgd2VsbCB3aXRoaW4gdGhlIEJhY2tlbmRcbiAqIGVudmlyb25tZW50LlxuICpcbiAqIEZ1bmN0aW9uczpcbiAqICAtIEFkZHMgdGhlIFwicmVuZGVyXCIgalF1ZXJ5IGV2ZW50IHRvIFNub3dib2FyZCByZXF1ZXN0cyB0aGF0IHdpZGdldHMgdXNlIHRvIGluaXRpYWxpc2UuXG4gKiAgLSBFbnN1cmVzIHRoZSBDU1JGIHRva2VuIGlzIGluY2x1ZGVkIGluIHJlcXVlc3RzLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBIYW5kbGVyIGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgLyoqXG4gICAgICogRXZlbnQgbGlzdGVuZXJzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgcmVhZHk6ICdyZWFkeScsXG4gICAgICAgICAgICBhamF4RmV0Y2hPcHRpb25zOiAnYWpheEZldGNoT3B0aW9ucycsXG4gICAgICAgICAgICBhamF4VXBkYXRlQ29tcGxldGU6ICdhamF4VXBkYXRlQ29tcGxldGUnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlYWR5IGhhbmRsZXIuXG4gICAgICpcbiAgICAgKiBGaXJlcyBvZmYgYSBcInJlbmRlclwiIGV2ZW50LlxuICAgICAqL1xuICAgIHJlYWR5KCkge1xuICAgICAgICBpZiAoIXdpbmRvdy5qUXVlcnkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFkZCBcInJlbmRlclwiIGV2ZW50IGZvciBiYWNrd2FyZHMgY29tcGF0aWJpbGl0eVxuICAgICAgICB3aW5kb3cualF1ZXJ5KGRvY3VtZW50KS50cmlnZ2VyKCdyZW5kZXInKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBZGRzIHRoZSBqUXVlcnkgQUpBWCBwcmVmaWx0ZXIgdGhhdCB0aGUgb2xkIGZyYW1ld29yayB1c2VzIHRvIGluamVjdCB0aGUgQ1NSRiB0b2tlbiBpbiBBSkFYXG4gICAgICogY2FsbHMuXG4gICAgICovXG4gICAgYWRkUHJlZmlsdGVyKCkge1xuICAgICAgICBpZiAoIXdpbmRvdy5qUXVlcnkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHdpbmRvdy5qUXVlcnkuYWpheFByZWZpbHRlcigob3B0aW9ucykgPT4ge1xuICAgICAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgICAgIGlmICghb3B0aW9ucy5oZWFkZXJzKSB7XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaGVhZGVycyA9IHt9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBvcHRpb25zLmhlYWRlcnNbJ1gtQ1NSRi1UT0tFTiddID0gdGhpcy5nZXRUb2tlbigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBGZXRjaCBvcHRpb25zIGhhbmRsZXIuXG4gICAgICpcbiAgICAgKiBFbnN1cmVzIHRoYXQgdGhlIENTUkYgdG9rZW4gaXMgaW5jbHVkZWQgaW4gU25vd2JvYXJkIHJlcXVlc3RzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICAgKi9cbiAgICBhamF4RmV0Y2hPcHRpb25zKG9wdGlvbnMpIHtcbiAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgb3B0aW9ucy5oZWFkZXJzWydYLUNTUkYtVE9LRU4nXSA9IHRoaXMuZ2V0VG9rZW4oKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFVwZGF0ZSBjb21wbGV0ZSBoYW5kbGVyLlxuICAgICAqXG4gICAgICogRmlyZXMgb2ZmIGEgXCJyZW5kZXJcIiBldmVudCB3aGVuIHBhcnRpYWxzIGFyZSB1cGRhdGVkIHNvIHRoYXQgYW55IHdpZGdldHMgaW5jbHVkZWQgaW5cbiAgICAgKiByZXNwb25zZXMgYXJlIGNvcnJlY3RseSBpbml0aWFsaXNlZC5cbiAgICAgKi9cbiAgICBhamF4VXBkYXRlQ29tcGxldGUoKSB7XG4gICAgICAgIGlmICghd2luZG93LmpRdWVyeSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQWRkIFwicmVuZGVyXCIgZXZlbnQgZm9yIGJhY2t3YXJkcyBjb21wYXRpYmlsaXR5XG4gICAgICAgIHdpbmRvdy5qUXVlcnkoZG9jdW1lbnQpLnRyaWdnZXIoJ3JlbmRlcicpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERldGVybWluZXMgaWYgYSBDU1JGIHRva2VuIGlzIGF2YWlsYWJsZS5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtCb29sZWFufVxuICAgICAqL1xuICAgIGhhc1Rva2VuKCkge1xuICAgICAgICBjb25zdCB0b2tlbkVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJyk7XG5cbiAgICAgICAgaWYgKCF0b2tlbkVsZW1lbnQpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRva2VuRWxlbWVudC5oYXNBdHRyaWJ1dGUoJ2NvbnRlbnQnKSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogR2V0cyB0aGUgQ1NSRiB0b2tlbi5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAgICovXG4gICAgZ2V0VG9rZW4oKSB7XG4gICAgICAgIHJldHVybiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJykuZ2V0QXR0cmlidXRlKCdjb250ZW50Jyk7XG4gICAgfVxufVxuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQgQmFja2VuZEFqYXhIYW5kbGVyIGZyb20gJy4vYWpheC9IYW5kbGVyJztcblxuaWYgKHdpbmRvdy5Tbm93Ym9hcmQgPT09IHVuZGVmaW5lZCkge1xuICAgIHRocm93IG5ldyBFcnJvcignU25vd2JvYXJkIG11c3QgYmUgbG9hZGVkIGluIG9yZGVyIHRvIHVzZSB0aGUgQmFja2VuZCBVSS4nKTtcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdiYWNrZW5kLmFqYXguaGFuZGxlcicsIEJhY2tlbmRBamF4SGFuZGxlcik7XG4gICAgLy8gQWRkIHRoZSBwcmUtZmlsdGVyIGltbWVkaWF0ZWx5XG4gICAgU25vd2JvYXJkWydiYWNrZW5kLmFqYXguaGFuZGxlciddKCkuYWRkUHJlZmlsdGVyKCk7XG5cbiAgICAvLyBBZGQgcG9seWZpbGwgZm9yIEFzc2V0TWFuYWdlclxuICAgIHdpbmRvdy5Bc3NldE1hbmFnZXIgPSB7XG4gICAgICAgIGxvYWQ6IChhc3NldHMsIGNhbGxiYWNrKSA9PiB7XG4gICAgICAgICAgICBTbm93Ym9hcmQuYXNzZXRMb2FkZXIoKS5sb2FkKGFzc2V0cykudGhlbihcbiAgICAgICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChjYWxsYmFjayAmJiB0eXBlb2YgY2FsbGJhY2sgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgKTtcbiAgICAgICAgfSxcbiAgICB9O1xuICAgIHdpbmRvdy5hc3NldE1hbmFnZXIgPSB3aW5kb3cuQXNzZXRNYW5hZ2VyO1xufSkod2luZG93LlNub3dib2FyZCk7XG4iXSwibmFtZXMiOlsiSGFuZGxlciIsInJlYWR5IiwiYWpheEZldGNoT3B0aW9ucyIsImFqYXhVcGRhdGVDb21wbGV0ZSIsIndpbmRvdyIsImpRdWVyeSIsImRvY3VtZW50IiwidHJpZ2dlciIsImFqYXhQcmVmaWx0ZXIiLCJvcHRpb25zIiwiaGFzVG9rZW4iLCJoZWFkZXJzIiwiZ2V0VG9rZW4iLCJ0b2tlbkVsZW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiaGFzQXR0cmlidXRlIiwiZ2V0QXR0cmlidXRlIiwiU25vd2JvYXJkIiwiU2luZ2xldG9uIiwiQmFja2VuZEFqYXhIYW5kbGVyIiwidW5kZWZpbmVkIiwiRXJyb3IiLCJhZGRQbHVnaW4iLCJhZGRQcmVmaWx0ZXIiLCJBc3NldE1hbmFnZXIiLCJsb2FkIiwiYXNzZXRzIiwiY2FsbGJhY2siLCJhc3NldExvYWRlciIsInRoZW4iLCJhc3NldE1hbmFnZXIiXSwic291cmNlUm9vdCI6IiJ9