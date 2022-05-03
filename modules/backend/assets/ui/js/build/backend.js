/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/ui/js/ajax/AssetLoader.js":
/*!******************************************!*\
  !*** ./assets/ui/js/ajax/AssetLoader.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ AssetLoader)
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

var AssetLoader = /*#__PURE__*/function (_window$Snowboard$Sin) {
  _inherits(AssetLoader, _window$Snowboard$Sin);

  var _super = _createSuper(AssetLoader);

  function AssetLoader() {
    _classCallCheck(this, AssetLoader);

    return _super.apply(this, arguments);
  }

  _createClass(AssetLoader, [{
    key: "listens",
    value: function listens() {
      return {
        ajaxLoadAssets: 'processAssets'
      };
    }
  }, {
    key: "processAssets",
    value: function processAssets(assets) {
      console.log(assets);
    }
  }]);

  return AssetLoader;
}(window.Snowboard.Singleton);



/***/ }),

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

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

var Handler = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(Handler, _Snowboard$Singleton);

  var _super = _createSuper(Handler);

  function Handler() {
    _classCallCheck(this, Handler);

    return _super.apply(this, arguments);
  }

  _createClass(Handler, [{
    key: "listens",
    value: function listens() {
      return {
        ready: 'ready',
        ajaxFetchOptions: 'ajaxFetchOptions'
      };
    }
  }, {
    key: "ready",
    value: function ready() {
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
  }, {
    key: "ajaxFetchOptions",
    value: function ajaxFetchOptions(options) {
      if (this.hasToken()) {
        options.headers['X-CSRF-TOKEN'] = this.getToken();
      }
    }
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
/* harmony import */ var _ajax_AssetLoader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ajax/AssetLoader */ "./assets/ui/js/ajax/AssetLoader.js");



if (window.Snowboard === undefined) {
  throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

(function (Snowboard) {
  Snowboard.addPlugin('backend.ajax.handler', _ajax_Handler__WEBPACK_IMPORTED_MODULE_0__["default"]);
  Snowboard.addPlugin('backend.ajax.assetLoader', _ajax_AssetLoader__WEBPACK_IMPORTED_MODULE_1__["default"]);
})(window.Snowboard);
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy91aS9qcy9idWlsZC9iYWNrZW5kLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztJQUFxQkE7Ozs7Ozs7Ozs7Ozs7V0FDakIsbUJBQVU7QUFDTixhQUFPO0FBQ0hDLFFBQUFBLGNBQWMsRUFBRTtBQURiLE9BQVA7QUFHSDs7O1dBRUQsdUJBQWNDLE1BQWQsRUFBc0I7QUFDbEJDLE1BQUFBLE9BQU8sQ0FBQ0MsR0FBUixDQUFZRixNQUFaO0FBQ0g7Ozs7RUFUb0NHLE1BQU0sQ0FBQ0MsU0FBUCxDQUFpQkM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lDQXJDQzs7Ozs7Ozs7Ozs7OztXQUNqQixtQkFBVTtBQUNOLGFBQU87QUFDSEMsUUFBQUEsS0FBSyxFQUFFLE9BREo7QUFFSEMsUUFBQUEsZ0JBQWdCLEVBQUU7QUFGZixPQUFQO0FBSUg7OztXQUVELGlCQUFRO0FBQUE7O0FBQ0osVUFBSSxDQUFDTCxNQUFNLENBQUNNLE1BQVosRUFBb0I7QUFDaEI7QUFDSDs7QUFFRE4sTUFBQUEsTUFBTSxDQUFDTSxNQUFQLENBQWNDLGFBQWQsQ0FBNEIsVUFBQ0MsT0FBRCxFQUFhO0FBQ3JDLFlBQUksS0FBSSxDQUFDQyxRQUFMLEVBQUosRUFBcUI7QUFDakIsY0FBSSxDQUFDRCxPQUFPLENBQUNFLE9BQWIsRUFBc0I7QUFDbEJGLFlBQUFBLE9BQU8sQ0FBQ0UsT0FBUixHQUFrQixFQUFsQjtBQUNIOztBQUNERixVQUFBQSxPQUFPLENBQUNFLE9BQVIsQ0FBZ0IsY0FBaEIsSUFBa0MsS0FBSSxDQUFDQyxRQUFMLEVBQWxDO0FBQ0g7QUFDSixPQVBEO0FBUUg7OztXQUVELDBCQUFpQkgsT0FBakIsRUFBMEI7QUFDdEIsVUFBSSxLQUFLQyxRQUFMLEVBQUosRUFBcUI7QUFDakJELFFBQUFBLE9BQU8sQ0FBQ0UsT0FBUixDQUFnQixjQUFoQixJQUFrQyxLQUFLQyxRQUFMLEVBQWxDO0FBQ0g7QUFDSjs7O1dBRUQsb0JBQVc7QUFDUCxVQUFNQyxZQUFZLEdBQUdDLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1Qix5QkFBdkIsQ0FBckI7O0FBRUEsVUFBSSxDQUFDRixZQUFMLEVBQW1CO0FBQ2YsZUFBTyxLQUFQO0FBQ0g7O0FBQ0QsVUFBSSxDQUFDQSxZQUFZLENBQUNHLFlBQWIsQ0FBMEIsU0FBMUIsQ0FBTCxFQUEyQztBQUN2QyxlQUFPLEtBQVA7QUFDSDs7QUFFRCxhQUFPLElBQVA7QUFDSDs7O1dBRUQsb0JBQVc7QUFDUCxhQUFPRixRQUFRLENBQUNDLGFBQVQsQ0FBdUIseUJBQXZCLEVBQWtERSxZQUFsRCxDQUErRCxTQUEvRCxDQUFQO0FBQ0g7Ozs7RUE1Q2dDZixTQUFTLENBQUNDOzs7Ozs7OztVQ0EvQztVQUNBOztVQUVBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBOztVQUVBO1VBQ0E7O1VBRUE7VUFDQTtVQUNBOzs7OztXQ3RCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHlDQUF5Qyx3Q0FBd0M7V0FDakY7V0FDQTtXQUNBOzs7OztXQ1BBOzs7OztXQ0FBO1dBQ0E7V0FDQTtXQUNBLHVEQUF1RCxpQkFBaUI7V0FDeEU7V0FDQSxnREFBZ0QsYUFBYTtXQUM3RDs7Ozs7Ozs7Ozs7OztBQ05BO0FBQ0E7O0FBRUEsSUFBSUYsTUFBTSxDQUFDQyxTQUFQLEtBQXFCaUIsU0FBekIsRUFBb0M7QUFDaEMsUUFBTSxJQUFJQyxLQUFKLENBQVUsMERBQVYsQ0FBTjtBQUNIOztBQUVELENBQUMsVUFBQ2xCLFNBQUQsRUFBZTtBQUNaQSxFQUFBQSxTQUFTLENBQUNtQixTQUFWLENBQW9CLHNCQUFwQixFQUE0Q0gscURBQTVDO0FBQ0FoQixFQUFBQSxTQUFTLENBQUNtQixTQUFWLENBQW9CLDBCQUFwQixFQUFnRHpCLHlEQUFoRDtBQUNILENBSEQsRUFHR0ssTUFBTSxDQUFDQyxTQUhWLEUiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL2FqYXgvQXNzZXRMb2FkZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS8uL2Fzc2V0cy91aS9qcy9hamF4L0hhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9kZWZpbmUgcHJvcGVydHkgZ2V0dGVycyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvLi9hc3NldHMvdWkvanMvaW5kZXguanMiXSwic291cmNlc0NvbnRlbnQiOlsiZXhwb3J0IGRlZmF1bHQgY2xhc3MgQXNzZXRMb2FkZXIgZXh0ZW5kcyB3aW5kb3cuU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGFqYXhMb2FkQXNzZXRzOiAncHJvY2Vzc0Fzc2V0cycsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgcHJvY2Vzc0Fzc2V0cyhhc3NldHMpIHtcbiAgICAgICAgY29uc29sZS5sb2coYXNzZXRzKTtcbiAgICB9XG59XG4iLCJleHBvcnQgZGVmYXVsdCBjbGFzcyBIYW5kbGVyIGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICAgICAgYWpheEZldGNoT3B0aW9uczogJ2FqYXhGZXRjaE9wdGlvbnMnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIHJlYWR5KCkge1xuICAgICAgICBpZiAoIXdpbmRvdy5qUXVlcnkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHdpbmRvdy5qUXVlcnkuYWpheFByZWZpbHRlcigob3B0aW9ucykgPT4ge1xuICAgICAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgICAgIGlmICghb3B0aW9ucy5oZWFkZXJzKSB7XG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaGVhZGVycyA9IHt9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBvcHRpb25zLmhlYWRlcnNbJ1gtQ1NSRi1UT0tFTiddID0gdGhpcy5nZXRUb2tlbigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBhamF4RmV0Y2hPcHRpb25zKG9wdGlvbnMpIHtcbiAgICAgICAgaWYgKHRoaXMuaGFzVG9rZW4oKSkge1xuICAgICAgICAgICAgb3B0aW9ucy5oZWFkZXJzWydYLUNTUkYtVE9LRU4nXSA9IHRoaXMuZ2V0VG9rZW4oKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGhhc1Rva2VuKCkge1xuICAgICAgICBjb25zdCB0b2tlbkVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJyk7XG5cbiAgICAgICAgaWYgKCF0b2tlbkVsZW1lbnQpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIXRva2VuRWxlbWVudC5oYXNBdHRyaWJ1dGUoJ2NvbnRlbnQnKSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuXG4gICAgZ2V0VG9rZW4oKSB7XG4gICAgICAgIHJldHVybiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJykuZ2V0QXR0cmlidXRlKCdjb250ZW50Jyk7XG4gICAgfVxufVxuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQgQmFja2VuZEFqYXhIYW5kbGVyIGZyb20gJy4vYWpheC9IYW5kbGVyJztcbmltcG9ydCBBc3NldExvYWRlciBmcm9tICcuL2FqYXgvQXNzZXRMb2FkZXInO1xuXG5pZiAod2luZG93LlNub3dib2FyZCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCdTbm93Ym9hcmQgbXVzdCBiZSBsb2FkZWQgaW4gb3JkZXIgdG8gdXNlIHRoZSBCYWNrZW5kIFVJLicpO1xufVxuXG4oKFNub3dib2FyZCkgPT4ge1xuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2JhY2tlbmQuYWpheC5oYW5kbGVyJywgQmFja2VuZEFqYXhIYW5kbGVyKTtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdiYWNrZW5kLmFqYXguYXNzZXRMb2FkZXInLCBBc3NldExvYWRlcik7XG59KSh3aW5kb3cuU25vd2JvYXJkKTtcbiJdLCJuYW1lcyI6WyJBc3NldExvYWRlciIsImFqYXhMb2FkQXNzZXRzIiwiYXNzZXRzIiwiY29uc29sZSIsImxvZyIsIndpbmRvdyIsIlNub3dib2FyZCIsIlNpbmdsZXRvbiIsIkhhbmRsZXIiLCJyZWFkeSIsImFqYXhGZXRjaE9wdGlvbnMiLCJqUXVlcnkiLCJhamF4UHJlZmlsdGVyIiwib3B0aW9ucyIsImhhc1Rva2VuIiwiaGVhZGVycyIsImdldFRva2VuIiwidG9rZW5FbGVtZW50IiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiaGFzQXR0cmlidXRlIiwiZ2V0QXR0cmlidXRlIiwiQmFja2VuZEFqYXhIYW5kbGVyIiwidW5kZWZpbmVkIiwiRXJyb3IiLCJhZGRQbHVnaW4iXSwic291cmNlUm9vdCI6IiJ9