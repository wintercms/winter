/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./elements/Overlay.js":
/*!*****************************!*\
  !*** ./elements/Overlay.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Overlay)
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
 * Overlay element.
 *
 * Controls the overlay element that shrouds the page when a modal, popup or popover is open.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Overlay = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(Overlay, _Snowboard$Singleton);

  var _super = _createSuper(Overlay);

  /**
   * Constructor.
   *
   * @param {Snowboard} snowboard
   */
  function Overlay(snowboard) {
    var _this;

    _classCallCheck(this, Overlay);

    _this = _super.call(this, snowboard);
    _this.overlay = null;
    _this.shown = false;
    _this.color = '#000000';
    _this.opacity = 0.5;
    _this.speed = 175;
    return _this;
  }
  /**
   * Defines the dependencies.
   *
   * @returns {Array}
   */


  _createClass(Overlay, [{
    key: "dependencies",
    value: function dependencies() {
      return ['transition'];
    }
    /**
     * Defines listeners for events.
     *
     * @returns {Object}
     */

  }, {
    key: "listens",
    value: function listens() {
      return {
        ready: 'ready'
      };
    }
    /**
     * Ready event handler.
     */

  }, {
    key: "ready",
    value: function ready() {
      this.createOverlay();
    }
    /**
     * Destructor.
     *
     * Destroys the overlay.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      this.destroyOverlay();

      _get(_getPrototypeOf(Overlay.prototype), "destructor", this).call(this);
    }
    /**
     * Creates an overlay element and inserts it into the DOM.
     */

  }, {
    key: "createOverlay",
    value: function createOverlay() {
      this.overlay = document.createElement('div');
      this.overlay.id = 'overlay';
      this.setStyle();
      document.body.appendChild(this.overlay);
    }
    /**
     * Removes and destroys the overlay element from the DOM.
     */

  }, {
    key: "destroyOverlay",
    value: function destroyOverlay() {
      document.body.removeChild(this.overlay);
      this.overlay = null;
    }
    /**
     * Sets the styling on the overlay.
     */

  }, {
    key: "setStyle",
    value: function setStyle() {
      var _this2 = this;

      this.overlay.style.backgroundColor = this.color;
      this.overlay.style.position = 'fixed';
      this.overlay.style.top = 0;
      this.overlay.style.left = 0;
      this.overlay.style.width = '100%';
      this.overlay.style.height = '100%';

      if (this.shown) {
        this.overlay.style.display = 'block';
        this.overlay.style.opacity = this.opacity;
      } else {
        this.overlay.style.display = 'none';
        this.overlay.style.opacity = 0;
      }

      window.requestAnimationFrame(function () {
        _this2.overlay.style.transitionProperty = 'opacity';
        _this2.overlay.style.transitionTimingFunction = 'ease-in';
        _this2.overlay.style.transitionDuration = "".concat(_this2.speed, "ms");
      });
    }
  }, {
    key: "show",
    value: function show() {
      this.shown = true;
      this.overlay.style.display = 'block';
      this.overlay.style.opacity = this.opacity;
    }
  }, {
    key: "hide",
    value: function hide() {
      var _this3 = this;

      this.overlay.style.opacity = 0;
      this.overlay.addEventListener('transitionend', function () {
        _this3.shown = false;
        _this3.overlay.style.display = 'none';
      }, {
        once: true
      });
    }
  }, {
    key: "toggle",
    value: function toggle() {
      if (this.shown) {
        this.hide();
        return;
      }

      this.show();
    }
  }]);

  return Overlay;
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
/*!*****************!*\
  !*** ./base.js ***!
  \*****************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elements_Overlay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./elements/Overlay */ "./elements/Overlay.js");


if (window.Snowboard === undefined) {
  throw new Error('The Snowboard library must be loaded in order to use the Inspector widget');
}

(function (Snowboard) {
  Snowboard.addPlugin('system.ui.overlay', _elements_Overlay__WEBPACK_IMPORTED_MODULE_0__["default"]);
})(window.Snowboard);
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2J1aWxkL2Jhc2UuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7SUFDcUJBOzs7OztBQUVqQjtBQUNKO0FBQ0E7QUFDQTtBQUNBO0FBQ0ksbUJBQVlDLFNBQVosRUFBdUI7QUFBQTs7QUFBQTs7QUFDbkIsOEJBQU1BLFNBQU47QUFFQSxVQUFLQyxPQUFMLEdBQWUsSUFBZjtBQUNBLFVBQUtDLEtBQUwsR0FBYSxLQUFiO0FBQ0EsVUFBS0MsS0FBTCxHQUFhLFNBQWI7QUFDQSxVQUFLQyxPQUFMLEdBQWUsR0FBZjtBQUNBLFVBQUtDLEtBQUwsR0FBYSxHQUFiO0FBUG1CO0FBUXRCO0FBRUQ7QUFDSjtBQUNBO0FBQ0E7QUFDQTs7Ozs7V0FDSSx3QkFBZTtBQUNYLGFBQU8sQ0FBQyxZQUFELENBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSxtQkFBVTtBQUNOLGFBQU87QUFDSEMsUUFBQUEsS0FBSyxFQUFFO0FBREosT0FBUDtBQUdIO0FBRUQ7QUFDSjtBQUNBOzs7O1dBQ0ksaUJBQVE7QUFDSixXQUFLQyxhQUFMO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksc0JBQWE7QUFDVCxXQUFLQyxjQUFMOztBQUNBO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7Ozs7V0FDSSx5QkFBZ0I7QUFDWixXQUFLUCxPQUFMLEdBQWVRLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1QixLQUF2QixDQUFmO0FBQ0EsV0FBS1QsT0FBTCxDQUFhVSxFQUFiLEdBQWtCLFNBQWxCO0FBQ0EsV0FBS0MsUUFBTDtBQUVBSCxNQUFBQSxRQUFRLENBQUNJLElBQVQsQ0FBY0MsV0FBZCxDQUEwQixLQUFLYixPQUEvQjtBQUNIO0FBRUQ7QUFDSjtBQUNBOzs7O1dBQ0ksMEJBQWlCO0FBQ2JRLE1BQUFBLFFBQVEsQ0FBQ0ksSUFBVCxDQUFjRSxXQUFkLENBQTBCLEtBQUtkLE9BQS9CO0FBQ0EsV0FBS0EsT0FBTCxHQUFlLElBQWY7QUFDSDtBQUVEO0FBQ0o7QUFDQTs7OztXQUNJLG9CQUFXO0FBQUE7O0FBQ1AsV0FBS0EsT0FBTCxDQUFhZSxLQUFiLENBQW1CQyxlQUFuQixHQUFxQyxLQUFLZCxLQUExQztBQUNBLFdBQUtGLE9BQUwsQ0FBYWUsS0FBYixDQUFtQkUsUUFBbkIsR0FBOEIsT0FBOUI7QUFDQSxXQUFLakIsT0FBTCxDQUFhZSxLQUFiLENBQW1CRyxHQUFuQixHQUF5QixDQUF6QjtBQUNBLFdBQUtsQixPQUFMLENBQWFlLEtBQWIsQ0FBbUJJLElBQW5CLEdBQTBCLENBQTFCO0FBQ0EsV0FBS25CLE9BQUwsQ0FBYWUsS0FBYixDQUFtQkssS0FBbkIsR0FBMkIsTUFBM0I7QUFDQSxXQUFLcEIsT0FBTCxDQUFhZSxLQUFiLENBQW1CTSxNQUFuQixHQUE0QixNQUE1Qjs7QUFFQSxVQUFJLEtBQUtwQixLQUFULEVBQWdCO0FBQ1osYUFBS0QsT0FBTCxDQUFhZSxLQUFiLENBQW1CTyxPQUFuQixHQUE2QixPQUE3QjtBQUNBLGFBQUt0QixPQUFMLENBQWFlLEtBQWIsQ0FBbUJaLE9BQW5CLEdBQTZCLEtBQUtBLE9BQWxDO0FBQ0gsT0FIRCxNQUdPO0FBQ0gsYUFBS0gsT0FBTCxDQUFhZSxLQUFiLENBQW1CTyxPQUFuQixHQUE2QixNQUE3QjtBQUNBLGFBQUt0QixPQUFMLENBQWFlLEtBQWIsQ0FBbUJaLE9BQW5CLEdBQTZCLENBQTdCO0FBQ0g7O0FBRURvQixNQUFBQSxNQUFNLENBQUNDLHFCQUFQLENBQTZCLFlBQU07QUFDL0IsY0FBSSxDQUFDeEIsT0FBTCxDQUFhZSxLQUFiLENBQW1CVSxrQkFBbkIsR0FBd0MsU0FBeEM7QUFDQSxjQUFJLENBQUN6QixPQUFMLENBQWFlLEtBQWIsQ0FBbUJXLHdCQUFuQixHQUE4QyxTQUE5QztBQUNBLGNBQUksQ0FBQzFCLE9BQUwsQ0FBYWUsS0FBYixDQUFtQlksa0JBQW5CLGFBQTJDLE1BQUksQ0FBQ3ZCLEtBQWhEO0FBQ0gsT0FKRDtBQUtIOzs7V0FFRCxnQkFBTztBQUNILFdBQUtILEtBQUwsR0FBYSxJQUFiO0FBQ0EsV0FBS0QsT0FBTCxDQUFhZSxLQUFiLENBQW1CTyxPQUFuQixHQUE2QixPQUE3QjtBQUNBLFdBQUt0QixPQUFMLENBQWFlLEtBQWIsQ0FBbUJaLE9BQW5CLEdBQTZCLEtBQUtBLE9BQWxDO0FBQ0g7OztXQUVELGdCQUFPO0FBQUE7O0FBQ0gsV0FBS0gsT0FBTCxDQUFhZSxLQUFiLENBQW1CWixPQUFuQixHQUE2QixDQUE3QjtBQUNBLFdBQUtILE9BQUwsQ0FBYTRCLGdCQUFiLENBQThCLGVBQTlCLEVBQStDLFlBQU07QUFDakQsY0FBSSxDQUFDM0IsS0FBTCxHQUFhLEtBQWI7QUFDQSxjQUFJLENBQUNELE9BQUwsQ0FBYWUsS0FBYixDQUFtQk8sT0FBbkIsR0FBNkIsTUFBN0I7QUFDSCxPQUhELEVBR0c7QUFDQ08sUUFBQUEsSUFBSSxFQUFFO0FBRFAsT0FISDtBQU1IOzs7V0FFRCxrQkFBUztBQUNMLFVBQUksS0FBSzVCLEtBQVQsRUFBZ0I7QUFDWixhQUFLNkIsSUFBTDtBQUNBO0FBQ0g7O0FBRUQsV0FBS0MsSUFBTDtBQUNIOzs7O0VBMUhnQ0MsU0FBUyxDQUFDQzs7Ozs7Ozs7VUNSL0M7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7Ozs7V0N0QkE7V0FDQTtXQUNBO1dBQ0E7V0FDQSx5Q0FBeUMsd0NBQXdDO1dBQ2pGO1dBQ0E7V0FDQTs7Ozs7V0NQQTs7Ozs7V0NBQTtXQUNBO1dBQ0E7V0FDQSx1REFBdUQsaUJBQWlCO1dBQ3hFO1dBQ0EsZ0RBQWdELGFBQWE7V0FDN0Q7Ozs7Ozs7Ozs7OztBQ05BOztBQUVBLElBQUlWLE1BQU0sQ0FBQ1MsU0FBUCxLQUFxQkUsU0FBekIsRUFBb0M7QUFDaEMsUUFBTSxJQUFJQyxLQUFKLENBQVUsMkVBQVYsQ0FBTjtBQUNIOztBQUVELENBQUMsVUFBQ0gsU0FBRCxFQUFlO0FBQ1pBLEVBQUFBLFNBQVMsQ0FBQ0ksU0FBVixDQUFvQixtQkFBcEIsRUFBeUN0Qyx5REFBekM7QUFDSCxDQUZELEVBRUd5QixNQUFNLENBQUNTLFNBRlYsRSIsInNvdXJjZXMiOlsid2VicGFjazovL0B3aW50ZXJjbXMvdWktYmFzZS8uL2VsZW1lbnRzL092ZXJsYXkuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy91aS1iYXNlL3dlYnBhY2svYm9vdHN0cmFwIiwid2VicGFjazovL0B3aW50ZXJjbXMvdWktYmFzZS93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy91aS1iYXNlL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy91aS1iYXNlL3dlYnBhY2svcnVudGltZS9tYWtlIG5hbWVzcGFjZSBvYmplY3QiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy91aS1iYXNlLy4vYmFzZS5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIE92ZXJsYXkgZWxlbWVudC5cbiAqXG4gKiBDb250cm9scyB0aGUgb3ZlcmxheSBlbGVtZW50IHRoYXQgc2hyb3VkcyB0aGUgcGFnZSB3aGVuIGEgbW9kYWwsIHBvcHVwIG9yIHBvcG92ZXIgaXMgb3Blbi5cbiAqXG4gKiBAY29weXJpZ2h0IDIwMjEgV2ludGVyLlxuICogQGF1dGhvciBCZW4gVGhvbXNvbiA8Z2l0QGFsZnJlaWRvLmNvbT5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3ZlcmxheSBleHRlbmRzIFNub3dib2FyZC5TaW5nbGV0b25cbntcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U25vd2JvYXJkfSBzbm93Ym9hcmRcbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICB0aGlzLm92ZXJsYXkgPSBudWxsO1xuICAgICAgICB0aGlzLnNob3duID0gZmFsc2U7XG4gICAgICAgIHRoaXMuY29sb3IgPSAnIzAwMDAwMCc7XG4gICAgICAgIHRoaXMub3BhY2l0eSA9IDAuNTtcbiAgICAgICAgdGhpcy5zcGVlZCA9IDE3NTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIHRoZSBkZXBlbmRlbmNpZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWyd0cmFuc2l0aW9uJ107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBsaXN0ZW5lcnMgZm9yIGV2ZW50cy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlYWR5IGV2ZW50IGhhbmRsZXIuXG4gICAgICovXG4gICAgcmVhZHkoKSB7XG4gICAgICAgIHRoaXMuY3JlYXRlT3ZlcmxheSgpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlc3RydWN0b3IuXG4gICAgICpcbiAgICAgKiBEZXN0cm95cyB0aGUgb3ZlcmxheS5cbiAgICAgKi9cbiAgICBkZXN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLmRlc3Ryb3lPdmVybGF5KCk7XG4gICAgICAgIHN1cGVyLmRlc3RydWN0b3IoKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDcmVhdGVzIGFuIG92ZXJsYXkgZWxlbWVudCBhbmQgaW5zZXJ0cyBpdCBpbnRvIHRoZSBET00uXG4gICAgICovXG4gICAgY3JlYXRlT3ZlcmxheSgpIHtcbiAgICAgICAgdGhpcy5vdmVybGF5ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgIHRoaXMub3ZlcmxheS5pZCA9ICdvdmVybGF5JztcbiAgICAgICAgdGhpcy5zZXRTdHlsZSgpO1xuXG4gICAgICAgIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGhpcy5vdmVybGF5KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZW1vdmVzIGFuZCBkZXN0cm95cyB0aGUgb3ZlcmxheSBlbGVtZW50IGZyb20gdGhlIERPTS5cbiAgICAgKi9cbiAgICBkZXN0cm95T3ZlcmxheSgpIHtcbiAgICAgICAgZG9jdW1lbnQuYm9keS5yZW1vdmVDaGlsZCh0aGlzLm92ZXJsYXkpO1xuICAgICAgICB0aGlzLm92ZXJsYXkgPSBudWxsO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNldHMgdGhlIHN0eWxpbmcgb24gdGhlIG92ZXJsYXkuXG4gICAgICovXG4gICAgc2V0U3R5bGUoKSB7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5iYWNrZ3JvdW5kQ29sb3IgPSB0aGlzLmNvbG9yO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUucG9zaXRpb24gPSAnZml4ZWQnO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUudG9wID0gMDtcbiAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLmxlZnQgPSAwO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUud2lkdGggPSAnMTAwJSc7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5oZWlnaHQgPSAnMTAwJSc7XG5cbiAgICAgICAgaWYgKHRoaXMuc2hvd24pIHtcbiAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcbiAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5vcGFjaXR5ID0gdGhpcy5vcGFjaXR5O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUub3BhY2l0eSA9IDA7XG4gICAgICAgIH1cblxuICAgICAgICB3aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHtcbiAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS50cmFuc2l0aW9uUHJvcGVydHkgPSAnb3BhY2l0eSc7XG4gICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUudHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uID0gJ2Vhc2UtaW4nO1xuICAgICAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IGAke3RoaXMuc3BlZWR9bXNgO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBzaG93KCkge1xuICAgICAgICB0aGlzLnNob3duID0gdHJ1ZTtcbiAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUub3BhY2l0eSA9IHRoaXMub3BhY2l0eTtcbiAgICB9XG5cbiAgICBoaWRlKCkge1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUub3BhY2l0eSA9IDA7XG4gICAgICAgIHRoaXMub3ZlcmxheS5hZGRFdmVudExpc3RlbmVyKCd0cmFuc2l0aW9uZW5kJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5zaG93biA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIH0sIHtcbiAgICAgICAgICAgIG9uY2U6IHRydWUsXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIHRvZ2dsZSgpIHtcbiAgICAgICAgaWYgKHRoaXMuc2hvd24pIHtcbiAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zaG93KCk7XG4gICAgfVxufVxuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQgT3ZlcmxheSBmcm9tICcuL2VsZW1lbnRzL092ZXJsYXknO1xuXG5pZiAod2luZG93LlNub3dib2FyZCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCdUaGUgU25vd2JvYXJkIGxpYnJhcnkgbXVzdCBiZSBsb2FkZWQgaW4gb3JkZXIgdG8gdXNlIHRoZSBJbnNwZWN0b3Igd2lkZ2V0Jylcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdzeXN0ZW0udWkub3ZlcmxheScsIE92ZXJsYXkpO1xufSkod2luZG93LlNub3dib2FyZCk7XG4iXSwibmFtZXMiOlsiT3ZlcmxheSIsInNub3dib2FyZCIsIm92ZXJsYXkiLCJzaG93biIsImNvbG9yIiwib3BhY2l0eSIsInNwZWVkIiwicmVhZHkiLCJjcmVhdGVPdmVybGF5IiwiZGVzdHJveU92ZXJsYXkiLCJkb2N1bWVudCIsImNyZWF0ZUVsZW1lbnQiLCJpZCIsInNldFN0eWxlIiwiYm9keSIsImFwcGVuZENoaWxkIiwicmVtb3ZlQ2hpbGQiLCJzdHlsZSIsImJhY2tncm91bmRDb2xvciIsInBvc2l0aW9uIiwidG9wIiwibGVmdCIsIndpZHRoIiwiaGVpZ2h0IiwiZGlzcGxheSIsIndpbmRvdyIsInJlcXVlc3RBbmltYXRpb25GcmFtZSIsInRyYW5zaXRpb25Qcm9wZXJ0eSIsInRyYW5zaXRpb25UaW1pbmdGdW5jdGlvbiIsInRyYW5zaXRpb25EdXJhdGlvbiIsImFkZEV2ZW50TGlzdGVuZXIiLCJvbmNlIiwiaGlkZSIsInNob3ciLCJTbm93Ym9hcmQiLCJTaW5nbGV0b24iLCJ1bmRlZmluZWQiLCJFcnJvciIsImFkZFBsdWdpbiJdLCJzb3VyY2VSb290IjoiIn0=