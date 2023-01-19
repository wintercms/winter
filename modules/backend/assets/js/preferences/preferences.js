/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "../../node_modules/jquery-events-to-dom-events/dist/index.m.js":
/*!**********************************************************************!*\
  !*** ../../node_modules/jquery-events-to-dom-events/dist/index.m.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "abnegate": () => (/* binding */ e),
/* harmony export */   "delegate": () => (/* binding */ t)
/* harmony export */ });
var t=function(t,e){void 0===e&&(e=["event"]);var n=t.startsWith("$")?function(){return window.$(document).trigger(t.slice(1),[].slice.call(arguments)[0].detail)}:function(){var n=arguments,i=e.reduce(function(t,e,i){return t[e]=[].slice.call(n)[i],t},{});i.event.target.dispatchEvent(new CustomEvent("$"+t,{detail:i,bubbles:!0,cancelable:!0}))};return t.startsWith("$")?document.addEventListener(t,n):window.$(document).on(t,n),n},e=function(t,e){t.startsWith("$")?document.removeEventListener(t,e):window.$(document).off(t,e)};
//# sourceMappingURL=index.m.js.map


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
/*!*******************************************!*\
  !*** ./assets/ui/js/pages/Preferences.js ***!
  \*******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery_events_to_dom_events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery-events-to-dom-events */ "../../node_modules/jquery-events-to-dom-events/dist/index.m.js");
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
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

(function (Snowboard) {
  var Preferences = /*#__PURE__*/function (_Snowboard$Singleton) {
    _inherits(Preferences, _Snowboard$Singleton);
    var _super = _createSuper(Preferences);
    function Preferences() {
      _classCallCheck(this, Preferences);
      return _super.apply(this, arguments);
    }
    _createClass(Preferences, [{
      key: "construct",
      value: function construct() {
        this.widget = null;
      }
    }, {
      key: "listens",
      value: function listens() {
        return {
          'backend.widget.initialized': 'onWidgetInitialized'
        };
      }
    }, {
      key: "onWidgetInitialized",
      value: function onWidgetInitialized(element, widget) {
        if (element === document.getElementById('CodeEditor-formEditorPreview-_editor_preview')) {
          this.widget = widget;
          this.enablePreferences();
        }
      }
    }, {
      key: "enablePreferences",
      value: function enablePreferences() {
        var _this = this;
        (0,jquery_events_to_dom_events__WEBPACK_IMPORTED_MODULE_0__.delegate)('change');
        var checkboxes = {
          show_gutter: 'showGutter',
          highlight_active_line: 'highlightActiveLine',
          use_hard_tabs: '!useSoftTabs',
          display_indent_guides: 'displayIndentGuides',
          show_invisibles: 'showInvisibles',
          show_print_margin: 'showPrintMargin',
          show_minimap: 'showMinimap',
          enable_folding: 'codeFolding',
          bracket_colors: 'bracketColors',
          show_colors: 'showColors'
        };
        Object.entries(checkboxes).forEach(function (_ref) {
          var _ref2 = _slicedToArray(_ref, 2),
            key = _ref2[0],
            value = _ref2[1];
          _this.element(key).addEventListener('change', function (event) {
            _this.widget.setConfig(value.replace(/^!/, ''), /^!/.test(value) ? !event.target.checked : event.target.checked);
          });
        });
        this.element('theme').addEventListener('$change', function (event) {
          _this.widget.loadTheme(event.target.value);
        });
        this.element('font_size').addEventListener('$change', function (event) {
          _this.widget.setConfig('fontSize', event.target.value);
        });
        this.element('tab_size').addEventListener('$change', function (event) {
          _this.widget.setConfig('tabSize', event.target.value);
        });
        this.element('word_wrap').addEventListener('$change', function (event) {
          var value = event.target.value;
          switch (value) {
            case 'off':
              _this.widget.setConfig('wordWrap', false);
              break;
            case 'fluid':
              _this.widget.setConfig('wordWrap', 'fluid');
              break;
            default:
              _this.widget.setConfig('wordWrap', parseInt(value, 10));
          }
        });
        document.querySelectorAll('[data-switch-lang]').forEach(function (element) {
          element.addEventListener('click', function (event) {
            event.preventDefault();
            var language = element.dataset.switchLang;
            var template = document.querySelector("[data-lang-snippet=\"".concat(language, "\"]"));
            if (!template) {
              return;
            }
            _this.widget.setValue(template.textContent.trim());
            _this.widget.setLanguage(language);
          });
        });
        this.widget.events.once('create', function () {
          var event = new MouseEvent('click');
          document.querySelector('[data-switch-lang="css"]').dispatchEvent(event);
        });
      }
    }, {
      key: "element",
      value: function element(key) {
        return document.getElementById("Form-field-Preference-editor_".concat(key));
      }
    }]);
    return Preferences;
  }(Snowboard.Singleton);
  Snowboard.addPlugin('backend.preferences', Preferences);
})(window.Snowboard);
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy9qcy9wcmVmZXJlbmNlcy9wcmVmZXJlbmNlcy5qcyIsIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7QUFBQSxvQkFBb0IsMEJBQTBCLG1DQUFtQyxpRkFBaUYsWUFBWSwyQ0FBMkMsa0NBQWtDLEdBQUcsRUFBRSxvREFBb0Qsa0NBQWtDLElBQUkscUZBQXFGLGlCQUFpQixpRkFBcUg7QUFDcmpCOzs7Ozs7O1VDREE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7Ozs7V0N0QkE7V0FDQTtXQUNBO1dBQ0E7V0FDQSx5Q0FBeUMsd0NBQXdDO1dBQ2pGO1dBQ0E7V0FDQTs7Ozs7V0NQQTs7Ozs7V0NBQTtXQUNBO1dBQ0E7V0FDQSx1REFBdUQsaUJBQWlCO1dBQ3hFO1dBQ0EsZ0RBQWdELGFBQWE7V0FDN0Q7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDTnVEO0FBRXZELENBQUMsVUFBQ0MsU0FBUyxFQUFLO0VBQUEsSUFDTkMsV0FBVztJQUFBO0lBQUE7SUFBQTtNQUFBO01BQUE7SUFBQTtJQUFBO01BQUE7TUFBQSxPQUNiLHFCQUFZO1FBQ1IsSUFBSSxDQUFDQyxNQUFNLEdBQUcsSUFBSTtNQUN0QjtJQUFDO01BQUE7TUFBQSxPQUVELG1CQUFVO1FBQ04sT0FBTztVQUNILDRCQUE0QixFQUFFO1FBQ2xDLENBQUM7TUFDTDtJQUFDO01BQUE7TUFBQSxPQUVELDZCQUFvQkMsT0FBTyxFQUFFRCxNQUFNLEVBQUU7UUFDakMsSUFBSUMsT0FBTyxLQUFLQyxRQUFRLENBQUNDLGNBQWMsQ0FBQyw4Q0FBOEMsQ0FBQyxFQUFFO1VBQ3JGLElBQUksQ0FBQ0gsTUFBTSxHQUFHQSxNQUFNO1VBQ3BCLElBQUksQ0FBQ0ksaUJBQWlCLEVBQUU7UUFDNUI7TUFDSjtJQUFDO01BQUE7TUFBQSxPQUVELDZCQUFvQjtRQUFBO1FBQ2hCUCxxRUFBUSxDQUFDLFFBQVEsQ0FBQztRQUVsQixJQUFNUSxVQUFVLEdBQUc7VUFDZkMsV0FBVyxFQUFFLFlBQVk7VUFDekJDLHFCQUFxQixFQUFFLHFCQUFxQjtVQUM1Q0MsYUFBYSxFQUFFLGNBQWM7VUFDN0JDLHFCQUFxQixFQUFFLHFCQUFxQjtVQUM1Q0MsZUFBZSxFQUFFLGdCQUFnQjtVQUNqQ0MsaUJBQWlCLEVBQUUsaUJBQWlCO1VBQ3BDQyxZQUFZLEVBQUUsYUFBYTtVQUMzQkMsY0FBYyxFQUFFLGFBQWE7VUFDN0JDLGNBQWMsRUFBRSxlQUFlO1VBQy9CQyxXQUFXLEVBQUU7UUFDakIsQ0FBQztRQUVEQyxNQUFNLENBQUNDLE9BQU8sQ0FBQ1osVUFBVSxDQUFDLENBQUNhLE9BQU8sQ0FBQyxnQkFBa0I7VUFBQTtZQUFoQkMsR0FBRztZQUFFQyxLQUFLO1VBQzNDLEtBQUksQ0FBQ25CLE9BQU8sQ0FBQ2tCLEdBQUcsQ0FBQyxDQUFDRSxnQkFBZ0IsQ0FBQyxRQUFRLEVBQUUsVUFBQ0MsS0FBSyxFQUFLO1lBQ3BELEtBQUksQ0FBQ3RCLE1BQU0sQ0FBQ3VCLFNBQVMsQ0FDakJILEtBQUssQ0FBQ0ksT0FBTyxDQUFDLElBQUksRUFBRSxFQUFFLENBQUMsRUFDdkIsSUFBSSxDQUFDQyxJQUFJLENBQUNMLEtBQUssQ0FBQyxHQUFHLENBQUNFLEtBQUssQ0FBQ0ksTUFBTSxDQUFDQyxPQUFPLEdBQUdMLEtBQUssQ0FBQ0ksTUFBTSxDQUFDQyxPQUFPLENBQ2xFO1VBQ0wsQ0FBQyxDQUFDO1FBQ04sQ0FBQyxDQUFDO1FBRUYsSUFBSSxDQUFDMUIsT0FBTyxDQUFDLE9BQU8sQ0FBQyxDQUFDb0IsZ0JBQWdCLENBQUMsU0FBUyxFQUFFLFVBQUNDLEtBQUssRUFBSztVQUN6RCxLQUFJLENBQUN0QixNQUFNLENBQUM0QixTQUFTLENBQUNOLEtBQUssQ0FBQ0ksTUFBTSxDQUFDTixLQUFLLENBQUM7UUFDN0MsQ0FBQyxDQUFDO1FBRUYsSUFBSSxDQUFDbkIsT0FBTyxDQUFDLFdBQVcsQ0FBQyxDQUFDb0IsZ0JBQWdCLENBQUMsU0FBUyxFQUFFLFVBQUNDLEtBQUssRUFBSztVQUM3RCxLQUFJLENBQUN0QixNQUFNLENBQUN1QixTQUFTLENBQUMsVUFBVSxFQUFFRCxLQUFLLENBQUNJLE1BQU0sQ0FBQ04sS0FBSyxDQUFDO1FBQ3pELENBQUMsQ0FBQztRQUVGLElBQUksQ0FBQ25CLE9BQU8sQ0FBQyxVQUFVLENBQUMsQ0FBQ29CLGdCQUFnQixDQUFDLFNBQVMsRUFBRSxVQUFDQyxLQUFLLEVBQUs7VUFDNUQsS0FBSSxDQUFDdEIsTUFBTSxDQUFDdUIsU0FBUyxDQUFDLFNBQVMsRUFBRUQsS0FBSyxDQUFDSSxNQUFNLENBQUNOLEtBQUssQ0FBQztRQUN4RCxDQUFDLENBQUM7UUFFRixJQUFJLENBQUNuQixPQUFPLENBQUMsV0FBVyxDQUFDLENBQUNvQixnQkFBZ0IsQ0FBQyxTQUFTLEVBQUUsVUFBQ0MsS0FBSyxFQUFLO1VBQzdELElBQVFGLEtBQUssR0FBS0UsS0FBSyxDQUFDSSxNQUFNLENBQXRCTixLQUFLO1VBQ2IsUUFBUUEsS0FBSztZQUNULEtBQUssS0FBSztjQUNOLEtBQUksQ0FBQ3BCLE1BQU0sQ0FBQ3VCLFNBQVMsQ0FBQyxVQUFVLEVBQUUsS0FBSyxDQUFDO2NBQ3hDO1lBQ0osS0FBSyxPQUFPO2NBQ1IsS0FBSSxDQUFDdkIsTUFBTSxDQUFDdUIsU0FBUyxDQUFDLFVBQVUsRUFBRSxPQUFPLENBQUM7Y0FDMUM7WUFDSjtjQUNJLEtBQUksQ0FBQ3ZCLE1BQU0sQ0FBQ3VCLFNBQVMsQ0FBQyxVQUFVLEVBQUVNLFFBQVEsQ0FBQ1QsS0FBSyxFQUFFLEVBQUUsQ0FBQyxDQUFDO1VBQUM7UUFFbkUsQ0FBQyxDQUFDO1FBRUZsQixRQUFRLENBQUM0QixnQkFBZ0IsQ0FBQyxvQkFBb0IsQ0FBQyxDQUFDWixPQUFPLENBQUMsVUFBQ2pCLE9BQU8sRUFBSztVQUNqRUEsT0FBTyxDQUFDb0IsZ0JBQWdCLENBQUMsT0FBTyxFQUFFLFVBQUNDLEtBQUssRUFBSztZQUN6Q0EsS0FBSyxDQUFDUyxjQUFjLEVBQUU7WUFDdEIsSUFBTUMsUUFBUSxHQUFHL0IsT0FBTyxDQUFDZ0MsT0FBTyxDQUFDQyxVQUFVO1lBQzNDLElBQU1DLFFBQVEsR0FBR2pDLFFBQVEsQ0FBQ2tDLGFBQWEsZ0NBQXdCSixRQUFRLFNBQUs7WUFFNUUsSUFBSSxDQUFDRyxRQUFRLEVBQUU7Y0FDWDtZQUNKO1lBRUEsS0FBSSxDQUFDbkMsTUFBTSxDQUFDcUMsUUFBUSxDQUFDRixRQUFRLENBQUNHLFdBQVcsQ0FBQ0MsSUFBSSxFQUFFLENBQUM7WUFDakQsS0FBSSxDQUFDdkMsTUFBTSxDQUFDd0MsV0FBVyxDQUFDUixRQUFRLENBQUM7VUFDckMsQ0FBQyxDQUFDO1FBQ04sQ0FBQyxDQUFDO1FBRUYsSUFBSSxDQUFDaEMsTUFBTSxDQUFDeUMsTUFBTSxDQUFDQyxJQUFJLENBQUMsUUFBUSxFQUFFLFlBQU07VUFDcEMsSUFBTXBCLEtBQUssR0FBRyxJQUFJcUIsVUFBVSxDQUFDLE9BQU8sQ0FBQztVQUNyQ3pDLFFBQVEsQ0FBQ2tDLGFBQWEsQ0FBQywwQkFBMEIsQ0FBQyxDQUFDUSxhQUFhLENBQUN0QixLQUFLLENBQUM7UUFDM0UsQ0FBQyxDQUFDO01BQ047SUFBQztNQUFBO01BQUEsT0FFRCxpQkFBUUgsR0FBRyxFQUFFO1FBQ1QsT0FBT2pCLFFBQVEsQ0FBQ0MsY0FBYyx3Q0FBaUNnQixHQUFHLEVBQUc7TUFDekU7SUFBQztJQUFBO0VBQUEsRUE1RnFCckIsU0FBUyxDQUFDK0MsU0FBUztFQStGN0MvQyxTQUFTLENBQUNnRCxTQUFTLENBQUMscUJBQXFCLEVBQUUvQyxXQUFXLENBQUM7QUFDM0QsQ0FBQyxFQUFFZ0QsTUFBTSxDQUFDakQsU0FBUyxDQUFDLEMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4uLy4uL25vZGVfbW9kdWxlcy9qcXVlcnktZXZlbnRzLXRvLWRvbS1ldmVudHMvZGlzdC9pbmRleC5tLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy93bi1iYWNrZW5kLW1vZHVsZS93ZWJwYWNrL3J1bnRpbWUvaGFzT3duUHJvcGVydHkgc2hvcnRoYW5kIiwid2VicGFjazovL0B3aW50ZXJjbXMvd24tYmFja2VuZC1tb2R1bGUvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3duLWJhY2tlbmQtbW9kdWxlLy4vYXNzZXRzL3VpL2pzL3BhZ2VzL1ByZWZlcmVuY2VzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbInZhciB0PWZ1bmN0aW9uKHQsZSl7dm9pZCAwPT09ZSYmKGU9W1wiZXZlbnRcIl0pO3ZhciBuPXQuc3RhcnRzV2l0aChcIiRcIik/ZnVuY3Rpb24oKXtyZXR1cm4gd2luZG93LiQoZG9jdW1lbnQpLnRyaWdnZXIodC5zbGljZSgxKSxbXS5zbGljZS5jYWxsKGFyZ3VtZW50cylbMF0uZGV0YWlsKX06ZnVuY3Rpb24oKXt2YXIgbj1hcmd1bWVudHMsaT1lLnJlZHVjZShmdW5jdGlvbih0LGUsaSl7cmV0dXJuIHRbZV09W10uc2xpY2UuY2FsbChuKVtpXSx0fSx7fSk7aS5ldmVudC50YXJnZXQuZGlzcGF0Y2hFdmVudChuZXcgQ3VzdG9tRXZlbnQoXCIkXCIrdCx7ZGV0YWlsOmksYnViYmxlczohMCxjYW5jZWxhYmxlOiEwfSkpfTtyZXR1cm4gdC5zdGFydHNXaXRoKFwiJFwiKT9kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKHQsbik6d2luZG93LiQoZG9jdW1lbnQpLm9uKHQsbiksbn0sZT1mdW5jdGlvbih0LGUpe3Quc3RhcnRzV2l0aChcIiRcIik/ZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcih0LGUpOndpbmRvdy4kKGRvY3VtZW50KS5vZmYodCxlKX07ZXhwb3J0e2UgYXMgYWJuZWdhdGUsdCBhcyBkZWxlZ2F0ZX07XG4vLyMgc291cmNlTWFwcGluZ1VSTD1pbmRleC5tLmpzLm1hcFxuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQgeyBkZWxlZ2F0ZSB9IGZyb20gJ2pxdWVyeS1ldmVudHMtdG8tZG9tLWV2ZW50cyc7XG5cbigoU25vd2JvYXJkKSA9PiB7XG4gICAgY2xhc3MgUHJlZmVyZW5jZXMgZXh0ZW5kcyBTbm93Ym9hcmQuU2luZ2xldG9uIHtcbiAgICAgICAgY29uc3RydWN0KCkge1xuICAgICAgICAgICAgdGhpcy53aWRnZXQgPSBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgbGlzdGVucygpIHtcbiAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgJ2JhY2tlbmQud2lkZ2V0LmluaXRpYWxpemVkJzogJ29uV2lkZ2V0SW5pdGlhbGl6ZWQnLFxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIG9uV2lkZ2V0SW5pdGlhbGl6ZWQoZWxlbWVudCwgd2lkZ2V0KSB7XG4gICAgICAgICAgICBpZiAoZWxlbWVudCA9PT0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ0NvZGVFZGl0b3ItZm9ybUVkaXRvclByZXZpZXctX2VkaXRvcl9wcmV2aWV3JykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLndpZGdldCA9IHdpZGdldDtcbiAgICAgICAgICAgICAgICB0aGlzLmVuYWJsZVByZWZlcmVuY2VzKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBlbmFibGVQcmVmZXJlbmNlcygpIHtcbiAgICAgICAgICAgIGRlbGVnYXRlKCdjaGFuZ2UnKTtcblxuICAgICAgICAgICAgY29uc3QgY2hlY2tib3hlcyA9IHtcbiAgICAgICAgICAgICAgICBzaG93X2d1dHRlcjogJ3Nob3dHdXR0ZXInLFxuICAgICAgICAgICAgICAgIGhpZ2hsaWdodF9hY3RpdmVfbGluZTogJ2hpZ2hsaWdodEFjdGl2ZUxpbmUnLFxuICAgICAgICAgICAgICAgIHVzZV9oYXJkX3RhYnM6ICchdXNlU29mdFRhYnMnLFxuICAgICAgICAgICAgICAgIGRpc3BsYXlfaW5kZW50X2d1aWRlczogJ2Rpc3BsYXlJbmRlbnRHdWlkZXMnLFxuICAgICAgICAgICAgICAgIHNob3dfaW52aXNpYmxlczogJ3Nob3dJbnZpc2libGVzJyxcbiAgICAgICAgICAgICAgICBzaG93X3ByaW50X21hcmdpbjogJ3Nob3dQcmludE1hcmdpbicsXG4gICAgICAgICAgICAgICAgc2hvd19taW5pbWFwOiAnc2hvd01pbmltYXAnLFxuICAgICAgICAgICAgICAgIGVuYWJsZV9mb2xkaW5nOiAnY29kZUZvbGRpbmcnLFxuICAgICAgICAgICAgICAgIGJyYWNrZXRfY29sb3JzOiAnYnJhY2tldENvbG9ycycsXG4gICAgICAgICAgICAgICAgc2hvd19jb2xvcnM6ICdzaG93Q29sb3JzJyxcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIE9iamVjdC5lbnRyaWVzKGNoZWNrYm94ZXMpLmZvckVhY2goKFtrZXksIHZhbHVlXSkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudChrZXkpLmFkZEV2ZW50TGlzdGVuZXIoJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLndpZGdldC5zZXRDb25maWcoXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZS5yZXBsYWNlKC9eIS8sICcnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIC9eIS8udGVzdCh2YWx1ZSkgPyAhZXZlbnQudGFyZ2V0LmNoZWNrZWQgOiBldmVudC50YXJnZXQuY2hlY2tlZCxcbiAgICAgICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQoJ3RoZW1lJykuYWRkRXZlbnRMaXN0ZW5lcignJGNoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMud2lkZ2V0LmxvYWRUaGVtZShldmVudC50YXJnZXQudmFsdWUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHRoaXMuZWxlbWVudCgnZm9udF9zaXplJykuYWRkRXZlbnRMaXN0ZW5lcignJGNoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMud2lkZ2V0LnNldENvbmZpZygnZm9udFNpemUnLCBldmVudC50YXJnZXQudmFsdWUpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHRoaXMuZWxlbWVudCgndGFiX3NpemUnKS5hZGRFdmVudExpc3RlbmVyKCckY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy53aWRnZXQuc2V0Q29uZmlnKCd0YWJTaXplJywgZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLmVsZW1lbnQoJ3dvcmRfd3JhcCcpLmFkZEV2ZW50TGlzdGVuZXIoJyRjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCB7IHZhbHVlIH0gPSBldmVudC50YXJnZXQ7XG4gICAgICAgICAgICAgICAgc3dpdGNoICh2YWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICBjYXNlICdvZmYnOlxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy53aWRnZXQuc2V0Q29uZmlnKCd3b3JkV3JhcCcsIGZhbHNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICBjYXNlICdmbHVpZCc6XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLndpZGdldC5zZXRDb25maWcoJ3dvcmRXcmFwJywgJ2ZsdWlkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMud2lkZ2V0LnNldENvbmZpZygnd29yZFdyYXAnLCBwYXJzZUludCh2YWx1ZSwgMTApKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW2RhdGEtc3dpdGNoLWxhbmddJykuZm9yRWFjaCgoZWxlbWVudCkgPT4ge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgbGFuZ3VhZ2UgPSBlbGVtZW50LmRhdGFzZXQuc3dpdGNoTGFuZztcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgdGVtcGxhdGUgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGBbZGF0YS1sYW5nLXNuaXBwZXQ9XCIke2xhbmd1YWdlfVwiXWApO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICghdGVtcGxhdGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMud2lkZ2V0LnNldFZhbHVlKHRlbXBsYXRlLnRleHRDb250ZW50LnRyaW0oKSk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMud2lkZ2V0LnNldExhbmd1YWdlKGxhbmd1YWdlKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLndpZGdldC5ldmVudHMub25jZSgnY3JlYXRlJywgKCkgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IGV2ZW50ID0gbmV3IE1vdXNlRXZlbnQoJ2NsaWNrJyk7XG4gICAgICAgICAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcignW2RhdGEtc3dpdGNoLWxhbmc9XCJjc3NcIl0nKS5kaXNwYXRjaEV2ZW50KGV2ZW50KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgZWxlbWVudChrZXkpIHtcbiAgICAgICAgICAgIHJldHVybiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChgRm9ybS1maWVsZC1QcmVmZXJlbmNlLWVkaXRvcl8ke2tleX1gKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIFNub3dib2FyZC5hZGRQbHVnaW4oJ2JhY2tlbmQucHJlZmVyZW5jZXMnLCBQcmVmZXJlbmNlcyk7XG59KSh3aW5kb3cuU25vd2JvYXJkKTtcbiJdLCJuYW1lcyI6WyJkZWxlZ2F0ZSIsIlNub3dib2FyZCIsIlByZWZlcmVuY2VzIiwid2lkZ2V0IiwiZWxlbWVudCIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJlbmFibGVQcmVmZXJlbmNlcyIsImNoZWNrYm94ZXMiLCJzaG93X2d1dHRlciIsImhpZ2hsaWdodF9hY3RpdmVfbGluZSIsInVzZV9oYXJkX3RhYnMiLCJkaXNwbGF5X2luZGVudF9ndWlkZXMiLCJzaG93X2ludmlzaWJsZXMiLCJzaG93X3ByaW50X21hcmdpbiIsInNob3dfbWluaW1hcCIsImVuYWJsZV9mb2xkaW5nIiwiYnJhY2tldF9jb2xvcnMiLCJzaG93X2NvbG9ycyIsIk9iamVjdCIsImVudHJpZXMiLCJmb3JFYWNoIiwia2V5IiwidmFsdWUiLCJhZGRFdmVudExpc3RlbmVyIiwiZXZlbnQiLCJzZXRDb25maWciLCJyZXBsYWNlIiwidGVzdCIsInRhcmdldCIsImNoZWNrZWQiLCJsb2FkVGhlbWUiLCJwYXJzZUludCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJwcmV2ZW50RGVmYXVsdCIsImxhbmd1YWdlIiwiZGF0YXNldCIsInN3aXRjaExhbmciLCJ0ZW1wbGF0ZSIsInF1ZXJ5U2VsZWN0b3IiLCJzZXRWYWx1ZSIsInRleHRDb250ZW50IiwidHJpbSIsInNldExhbmd1YWdlIiwiZXZlbnRzIiwib25jZSIsIk1vdXNlRXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwiU2luZ2xldG9uIiwiYWRkUGx1Z2luIiwid2luZG93Il0sInNvdXJjZVJvb3QiOiIifQ==