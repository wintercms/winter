/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./main/Manager.js":
/*!*************************!*\
  !*** ./main/Manager.js ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Manager)
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
 * Inspector manager.
 *
 * This class provides the management and initialization of Inspector widgets on a page.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
var Manager = /*#__PURE__*/function (_Snowboard$Singleton) {
  _inherits(Manager, _Snowboard$Singleton);

  var _super = _createSuper(Manager);

  /**
   * Constructor.
   *
   * @param {Snowboard} snowboard
   */
  function Manager(snowboard) {
    var _this;

    _classCallCheck(this, Manager);

    _this = _super.call(this, snowboard);
    _this.inspectableElements = [];
    return _this;
  }
  /**
   * Defines the dependencies.
   *
   * @returns {Array}
   */


  _createClass(Manager, [{
    key: "dependencies",
    value: function dependencies() {
      return ['system.ui.overlay'];
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
      this.bindInspectableElements();
    }
    /**
     * Destructor.
     *
     * Fired when this plugin is removed.
     */

  }, {
    key: "destructor",
    value: function destructor() {
      this.unbindInspectableElements();
      this.inspectableElements = [];

      _get(_getPrototypeOf(Manager.prototype), "destructor", this).call(this);
    }
    /**
     * Searches for, and binds an event to, inspectable elements.
     */

  }, {
    key: "bindInspectableElements",
    value: function bindInspectableElements() {
      var _this2 = this;

      window.document.querySelectorAll('[data-inspectable]').forEach(function (element) {
        var inspectorData = {
          element: element,
          container: _this2.findInspectableContainer(element),
          handler: function handler(event) {
            return _this2.inspectableClick.call(_this2, event, inspectorData);
          },
          title: element.dataset.inspectorTitle || 'Inspector',
          description: element.dataset.inspectorDescription || null,
          config: element.dataset.inspectorConfig || null,
          offset: {
            x: element.dataset.inspectorOffsetX || element.dataset.inspectorOffset || 0,
            y: element.dataset.inspectorOffsetY || element.dataset.inspectorOffset || 0
          },
          placement: element.dataset.inspectorPlacement || null,
          fallbackPlacement: element.dataset.inspectorFallbackPlacement || 'bottom',
          cssClasses: element.dataset.inspectorCssClass || null
        };

        _this2.inspectableElements.push(inspectorData);

        element.addEventListener('click', inspectorData.handler);
      });
    }
    /**
     * Unbinds all inspectable elements.
     */

  }, {
    key: "unbindInspectableElements",
    value: function unbindInspectableElements() {}
  }, {
    key: "createInspector",
    value: function createInspector(inspector) {
      console.log(this.snowboard);
      this.snowboard['system.ui.overlay']().toggle();
    }
  }, {
    key: "inspectableClick",
    value: function inspectableClick(event, inspector) {
      event.preventDefault();
      this.createInspector(inspector);
    }
    /**
     * Searches up the hierarchy for a container for Inspectable elements.
     *
     * @param {HTMLElement} element
     * @returns {HTMLElement|null}
     */

  }, {
    key: "findInspectableContainer",
    value: function findInspectableContainer(element) {
      var currentElement = element;

      while (currentElement.parentElement && currentElement.parentElement.tagName !== 'HTML') {
        if (currentElement.matches('[data-inspector-container]')) {
          return currentElement;
        }

        currentElement = currentElement.parentElement;
      }

      return null;
    }
  }]);

  return Manager;
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
/*!**********************!*\
  !*** ./inspector.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_Manager__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main/Manager */ "./main/Manager.js");


if (window.Snowboard === undefined) {
  throw new Error('The Snowboard library must be loaded in order to use the Inspector widget');
}

(function (Snowboard) {
  Snowboard.addPlugin('system.widgets.inspector', _main_Manager__WEBPACK_IMPORTED_MODULE_0__["default"]);
})(window.Snowboard);
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QSxFQUFBQSxTQUFTLENBQUNJLFNBQVYsQ0FBb0IsMEJBQXBCLEVBQWdEckQscURBQWhEO0FBQ0gsQ0FGRCxFQUVHTSxNQUFNLENBQUMyQyxTQUZWLEUiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3VpLWluc3BlY3Rvci8uL21haW4vTWFuYWdlci5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3VpLWluc3BlY3Rvci93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3VpLWluc3BlY3Rvci93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy91aS1pbnNwZWN0b3Ivd2VicGFjay9ydW50aW1lL2hhc093blByb3BlcnR5IHNob3J0aGFuZCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3VpLWluc3BlY3Rvci93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL0B3aW50ZXJjbXMvdWktaW5zcGVjdG9yLy4vaW5zcGVjdG9yLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogSW5zcGVjdG9yIG1hbmFnZXIuXG4gKlxuICogVGhpcyBjbGFzcyBwcm92aWRlcyB0aGUgbWFuYWdlbWVudCBhbmQgaW5pdGlhbGl6YXRpb24gb2YgSW5zcGVjdG9yIHdpZGdldHMgb24gYSBwYWdlLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBNYW5hZ2VyIGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvblxue1xuICAgIC8qKlxuICAgICAqIENvbnN0cnVjdG9yLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTbm93Ym9hcmR9IHNub3dib2FyZFxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yKHNub3dib2FyZCkge1xuICAgICAgICBzdXBlcihzbm93Ym9hcmQpO1xuXG4gICAgICAgIHRoaXMuaW5zcGVjdGFibGVFbGVtZW50cyA9IFtdO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgdGhlIGRlcGVuZGVuY2llcy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKi9cbiAgICBkZXBlbmRlbmNpZXMoKSB7XG4gICAgICAgIHJldHVybiBbJ3N5c3RlbS51aS5vdmVybGF5J107XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVmaW5lcyBsaXN0ZW5lcnMgZm9yIGV2ZW50cy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtPYmplY3R9XG4gICAgICovXG4gICAgbGlzdGVucygpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlYWR5OiAncmVhZHknLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJlYWR5IGV2ZW50IGhhbmRsZXIuXG4gICAgICovXG4gICAgcmVhZHkoKSB7XG4gICAgICAgIHRoaXMuYmluZEluc3BlY3RhYmxlRWxlbWVudHMoKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZXN0cnVjdG9yLlxuICAgICAqXG4gICAgICogRmlyZWQgd2hlbiB0aGlzIHBsdWdpbiBpcyByZW1vdmVkLlxuICAgICAqL1xuICAgIGRlc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMudW5iaW5kSW5zcGVjdGFibGVFbGVtZW50cygpO1xuICAgICAgICB0aGlzLmluc3BlY3RhYmxlRWxlbWVudHMgPSBbXTtcblxuICAgICAgICBzdXBlci5kZXN0cnVjdG9yKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogU2VhcmNoZXMgZm9yLCBhbmQgYmluZHMgYW4gZXZlbnQgdG8sIGluc3BlY3RhYmxlIGVsZW1lbnRzLlxuICAgICAqL1xuICAgIGJpbmRJbnNwZWN0YWJsZUVsZW1lbnRzKCkge1xuICAgICAgICB3aW5kb3cuZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW2RhdGEtaW5zcGVjdGFibGVdJykuZm9yRWFjaCgoZWxlbWVudCkgPT4ge1xuICAgICAgICAgICAgY29uc3QgaW5zcGVjdG9yRGF0YSA9IHtcbiAgICAgICAgICAgICAgICBlbGVtZW50LFxuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogdGhpcy5maW5kSW5zcGVjdGFibGVDb250YWluZXIoZWxlbWVudCksXG4gICAgICAgICAgICAgICAgaGFuZGxlcjogKGV2ZW50KSA9PiB0aGlzLmluc3BlY3RhYmxlQ2xpY2suY2FsbCh0aGlzLCBldmVudCwgaW5zcGVjdG9yRGF0YSksXG4gICAgICAgICAgICAgICAgdGl0bGU6IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JUaXRsZSB8fCAnSW5zcGVjdG9yJyxcbiAgICAgICAgICAgICAgICBkZXNjcmlwdGlvbjogZWxlbWVudC5kYXRhc2V0Lmluc3BlY3RvckRlc2NyaXB0aW9uIHx8IG51bGwsXG4gICAgICAgICAgICAgICAgY29uZmlnOiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yQ29uZmlnIHx8IG51bGwsXG4gICAgICAgICAgICAgICAgb2Zmc2V0OiB7XG4gICAgICAgICAgICAgICAgICAgIHg6IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JPZmZzZXRYIHx8IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JPZmZzZXQgfHwgMCxcbiAgICAgICAgICAgICAgICAgICAgeTogZWxlbWVudC5kYXRhc2V0Lmluc3BlY3Rvck9mZnNldFkgfHwgZWxlbWVudC5kYXRhc2V0Lmluc3BlY3Rvck9mZnNldCB8fCAwLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgcGxhY2VtZW50OiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yUGxhY2VtZW50IHx8IG51bGwsXG4gICAgICAgICAgICAgICAgZmFsbGJhY2tQbGFjZW1lbnQ6IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JGYWxsYmFja1BsYWNlbWVudCB8fCAnYm90dG9tJyxcbiAgICAgICAgICAgICAgICBjc3NDbGFzc2VzOiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yQ3NzQ2xhc3MgfHwgbnVsbCxcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIHRoaXMuaW5zcGVjdGFibGVFbGVtZW50cy5wdXNoKGluc3BlY3RvckRhdGEpO1xuICAgICAgICAgICAgZWxlbWVudC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGluc3BlY3RvckRhdGEuaGFuZGxlcik7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFVuYmluZHMgYWxsIGluc3BlY3RhYmxlIGVsZW1lbnRzLlxuICAgICAqL1xuICAgIHVuYmluZEluc3BlY3RhYmxlRWxlbWVudHMoKSB7XG4gICAgfVxuXG4gICAgY3JlYXRlSW5zcGVjdG9yKGluc3BlY3Rvcikge1xuICAgICAgICBjb25zb2xlLmxvZyh0aGlzLnNub3dib2FyZCk7XG4gICAgICAgIHRoaXMuc25vd2JvYXJkWydzeXN0ZW0udWkub3ZlcmxheSddKCkudG9nZ2xlKCk7XG4gICAgfVxuXG4gICAgaW5zcGVjdGFibGVDbGljayhldmVudCwgaW5zcGVjdG9yKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgdGhpcy5jcmVhdGVJbnNwZWN0b3IoaW5zcGVjdG9yKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZWFyY2hlcyB1cCB0aGUgaGllcmFyY2h5IGZvciBhIGNvbnRhaW5lciBmb3IgSW5zcGVjdGFibGUgZWxlbWVudHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICogQHJldHVybnMge0hUTUxFbGVtZW50fG51bGx9XG4gICAgICovXG4gICAgZmluZEluc3BlY3RhYmxlQ29udGFpbmVyKGVsZW1lbnQpIHtcbiAgICAgICAgbGV0IGN1cnJlbnRFbGVtZW50ID0gZWxlbWVudDtcblxuICAgICAgICB3aGlsZSAoY3VycmVudEVsZW1lbnQucGFyZW50RWxlbWVudCAmJiBjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50LnRhZ05hbWUgIT09ICdIVE1MJykge1xuICAgICAgICAgICAgaWYgKGN1cnJlbnRFbGVtZW50Lm1hdGNoZXMoJ1tkYXRhLWluc3BlY3Rvci1jb250YWluZXJdJykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gY3VycmVudEVsZW1lbnQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGN1cnJlbnRFbGVtZW50ID0gY3VycmVudEVsZW1lbnQucGFyZW50RWxlbWVudDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbn1cbiIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9ucyBmb3IgaGFybW9ueSBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSAoZXhwb3J0cywgZGVmaW5pdGlvbikgPT4ge1xuXHRmb3IodmFyIGtleSBpbiBkZWZpbml0aW9uKSB7XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGRlZmluaXRpb24sIGtleSkgJiYgIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBrZXkpKSB7XG5cdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuXHRcdH1cblx0fVxufTsiLCJfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSAob2JqLCBwcm9wKSA9PiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgcHJvcCkpIiwiLy8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5yID0gKGV4cG9ydHMpID0+IHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiaW1wb3J0IE1hbmFnZXIgZnJvbSAnLi9tYWluL01hbmFnZXInO1xuXG5pZiAod2luZG93LlNub3dib2FyZCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCdUaGUgU25vd2JvYXJkIGxpYnJhcnkgbXVzdCBiZSBsb2FkZWQgaW4gb3JkZXIgdG8gdXNlIHRoZSBJbnNwZWN0b3Igd2lkZ2V0Jylcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdzeXN0ZW0ud2lkZ2V0cy5pbnNwZWN0b3InLCBNYW5hZ2VyKTtcbn0pKHdpbmRvdy5Tbm93Ym9hcmQpO1xuIl0sIm5hbWVzIjpbIk1hbmFnZXIiLCJzbm93Ym9hcmQiLCJpbnNwZWN0YWJsZUVsZW1lbnRzIiwicmVhZHkiLCJiaW5kSW5zcGVjdGFibGVFbGVtZW50cyIsInVuYmluZEluc3BlY3RhYmxlRWxlbWVudHMiLCJ3aW5kb3ciLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJmb3JFYWNoIiwiZWxlbWVudCIsImluc3BlY3RvckRhdGEiLCJjb250YWluZXIiLCJmaW5kSW5zcGVjdGFibGVDb250YWluZXIiLCJoYW5kbGVyIiwiZXZlbnQiLCJpbnNwZWN0YWJsZUNsaWNrIiwiY2FsbCIsInRpdGxlIiwiZGF0YXNldCIsImluc3BlY3RvclRpdGxlIiwiZGVzY3JpcHRpb24iLCJpbnNwZWN0b3JEZXNjcmlwdGlvbiIsImNvbmZpZyIsImluc3BlY3RvckNvbmZpZyIsIm9mZnNldCIsIngiLCJpbnNwZWN0b3JPZmZzZXRYIiwiaW5zcGVjdG9yT2Zmc2V0IiwieSIsImluc3BlY3Rvck9mZnNldFkiLCJwbGFjZW1lbnQiLCJpbnNwZWN0b3JQbGFjZW1lbnQiLCJmYWxsYmFja1BsYWNlbWVudCIsImluc3BlY3RvckZhbGxiYWNrUGxhY2VtZW50IiwiY3NzQ2xhc3NlcyIsImluc3BlY3RvckNzc0NsYXNzIiwicHVzaCIsImFkZEV2ZW50TGlzdGVuZXIiLCJpbnNwZWN0b3IiLCJjb25zb2xlIiwibG9nIiwidG9nZ2xlIiwicHJldmVudERlZmF1bHQiLCJjcmVhdGVJbnNwZWN0b3IiLCJjdXJyZW50RWxlbWVudCIsInBhcmVudEVsZW1lbnQiLCJ0YWdOYW1lIiwibWF0Y2hlcyIsIlNub3dib2FyZCIsIlNpbmdsZXRvbiIsInVuZGVmaW5lZCIsIkVycm9yIiwiYWRkUGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==