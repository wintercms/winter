"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/ui/widgets/base/build/base"],{

/***/ "./assets/ui/widgets/base/base.js":
/*!****************************************!*\
  !*** ./assets/ui/widgets/base/base.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elements_Overlay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./elements/Overlay */ "./assets/ui/widgets/base/elements/Overlay.js");


if (window.Snowboard === undefined) {
  throw new Error('The Snowboard library must be loaded in order to use the Inspector widget');
}

(function (Snowboard) {
  Snowboard.addPlugin('overlay', _elements_Overlay__WEBPACK_IMPORTED_MODULE_0__["default"]);
})(window.Snowboard);

/***/ }),

/***/ "./assets/ui/widgets/base/elements/Overlay.js":
/*!****************************************************!*\
  !*** ./assets/ui/widgets/base/elements/Overlay.js ***!
  \****************************************************/
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
    _this.opacity = 0.42;
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
      var _this2 = this;

      this.overlay = document.createElement('div');
      this.overlay.id = 'overlay';
      this.setStyle();
      this.overlay.addEventListener('click', function (event) {
        _this2.snowboard.globalEvent('overlay.clicked', _this2.overlay, event);
      });
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
      var _this3 = this;

      this.overlay.style.backgroundColor = this.color;
      this.overlay.style.position = 'fixed';
      this.overlay.style.top = 0;
      this.overlay.style.left = 0;
      this.overlay.style.zIndex = 1000;
      this.overlay.style.transitionProperty = 'opacity';
      this.overlay.style.transitionTimingFunction = 'ease-out';
      this.overlay.style.transitionDuration = "".concat(this.speed, "ms");
      window.requestAnimationFrame(function () {
        if (_this3.shown) {
          _this3.overlay.style.width = '100%';
          _this3.overlay.style.height = '100%';
          _this3.overlay.style.opacity = _this3.opacity;
        } else {
          _this3.overlay.style.width = '0px';
          _this3.overlay.style.height = '0px';
          _this3.overlay.style.opacity = 0;
        }
      });
    }
    /**
     * Shows the overlay.
     *
     * Fires an "overlay.show" event, and follows up with an "overlay.shown" when the transition completes.
     */

  }, {
    key: "show",
    value: function show() {
      var _this4 = this;

      this.snowboard.globalEvent('overlay.show', this.overlay);
      this.shown = true;
      this.overlay.style.width = '100%';
      this.overlay.style.height = '100%';
      window.requestAnimationFrame(function () {
        _this4.overlay.style.opacity = _this4.opacity;

        _this4.overlay.addEventListener('transitionend', function () {
          _this4.snowboard.globalEvent('overlay.shown', _this4.overlay);
        }, {
          once: true
        });
      });
    }
    /**
     * Hides the overlay.
     *
     * Fires an "overlay.hide" event, and follows up with an "overlay.hidden" when the transition completes.
     */

  }, {
    key: "hide",
    value: function hide() {
      var _this5 = this;

      this.snowboard.globalEvent('overlay.hide', this.overlay);
      this.overlay.style.opacity = 0;
      this.overlay.addEventListener('transitionend', function () {
        _this5.shown = false;
        _this5.overlay.style.width = '0px';
        _this5.overlay.style.height = '0px';

        _this5.snowboard.globalEvent('overlay.hidden', _this5.overlay);
      }, {
        once: true
      });
    }
    /**
     * Toggles the overlay.
     */

  }, {
    key: "toggle",
    value: function toggle() {
      if (this.shown) {
        this.hide();
        return;
      }

      this.show();
    }
    /**
     * Sets the color of the overlay.
     *
     * Fluent method.
     *
     * @param {string} color
     * @returns {Overlay}
     */

  }, {
    key: "setColor",
    value: function setColor(color) {
      this.color = String(color);
      this.setStyle();
      return this;
    }
    /**
     * Sets the opacity of the overlay.
     *
     * Fluent method.
     *
     * @param {Number} opacity
     * @returns {Overlay}
     */

  }, {
    key: "setOpacity",
    value: function setOpacity(opacity) {
      this.opacity = Number(opacity);
      this.setStyle();
      return this;
    }
    /**
     * Sets the speed of the transition for the overlay.
     *
     * Fluent method.
     *
     * @param {Number} speed
     * @returns {Overlay}
     */

  }, {
    key: "setSpeed",
    value: function setSpeed(speed) {
      this.speed = Number(speed);
      this.setStyle();
      return this;
    }
  }]);

  return Overlay;
}(Snowboard.Singleton);



/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/ui/widgets/base/base.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy91aS93aWRnZXRzL2Jhc2UvYnVpbGQvYmFzZS5qcyIsIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7OztBQUFBOztBQUVBLElBQUlDLE1BQU0sQ0FBQ0MsU0FBUCxLQUFxQkMsU0FBekIsRUFBb0M7QUFDaEMsUUFBTSxJQUFJQyxLQUFKLENBQVUsMkVBQVYsQ0FBTjtBQUNIOztBQUVELENBQUMsVUFBQ0YsU0FBRCxFQUFlO0FBQ1pBLEVBQUFBLFNBQVMsQ0FBQ0csU0FBVixDQUFvQixTQUFwQixFQUErQkwseURBQS9CO0FBQ0gsQ0FGRCxFQUVHQyxNQUFNLENBQUNDLFNBRlY7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNOQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0lBQ3FCRjs7Ozs7QUFDakI7QUFDSjtBQUNBO0FBQ0E7QUFDQTtBQUNJLG1CQUFZTSxTQUFaLEVBQXVCO0FBQUE7O0FBQUE7O0FBQ25CLDhCQUFNQSxTQUFOO0FBRUEsVUFBS0MsT0FBTCxHQUFlLElBQWY7QUFDQSxVQUFLQyxLQUFMLEdBQWEsS0FBYjtBQUNBLFVBQUtDLEtBQUwsR0FBYSxTQUFiO0FBQ0EsVUFBS0MsT0FBTCxHQUFlLElBQWY7QUFDQSxVQUFLQyxLQUFMLEdBQWEsR0FBYjtBQVBtQjtBQVF0QjtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7O1dBQ0ksd0JBQWU7QUFDWCxhQUFPLENBQUMsWUFBRCxDQUFQO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksbUJBQVU7QUFDTixhQUFPO0FBQ0hDLFFBQUFBLEtBQUssRUFBRTtBQURKLE9BQVA7QUFHSDtBQUVEO0FBQ0o7QUFDQTs7OztXQUNJLGlCQUFRO0FBQ0osV0FBS0MsYUFBTDtBQUNIO0FBRUQ7QUFDSjtBQUNBO0FBQ0E7QUFDQTs7OztXQUNJLHNCQUFhO0FBQ1QsV0FBS0MsY0FBTDs7QUFDQTtBQUNIO0FBRUQ7QUFDSjtBQUNBOzs7O1dBQ0kseUJB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Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksb0JBQVdSLE9BQVgsRUFBb0I7QUFDaEIsV0FBS0EsT0FBTCxHQUFlK0IsTUFBTSxDQUFDL0IsT0FBRCxDQUFyQjtBQUNBLFdBQUtRLFFBQUw7QUFDQSxhQUFPLElBQVA7QUFDSDtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7V0FDSSxrQkFBU1AsS0FBVCxFQUFnQjtBQUNaLFdBQUtBLEtBQUwsR0FBYThCLE1BQU0sQ0FBQzlCLEtBQUQsQ0FBbkI7QUFDQSxXQUFLTyxRQUFMO0FBQ0EsYUFBTyxJQUFQO0FBQ0g7Ozs7RUFoTWdDaEIsU0FBUyxDQUFDd0MiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2Jhc2UvYmFzZS5qcyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2Jhc2UvZWxlbWVudHMvT3ZlcmxheS5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgT3ZlcmxheSBmcm9tICcuL2VsZW1lbnRzL092ZXJsYXknO1xuXG5pZiAod2luZG93LlNub3dib2FyZCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCdUaGUgU25vd2JvYXJkIGxpYnJhcnkgbXVzdCBiZSBsb2FkZWQgaW4gb3JkZXIgdG8gdXNlIHRoZSBJbnNwZWN0b3Igd2lkZ2V0Jylcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdvdmVybGF5JywgT3ZlcmxheSk7XG59KSh3aW5kb3cuU25vd2JvYXJkKTtcbiIsIi8qKlxuICogT3ZlcmxheSBlbGVtZW50LlxuICpcbiAqIENvbnRyb2xzIHRoZSBvdmVybGF5IGVsZW1lbnQgdGhhdCBzaHJvdWRzIHRoZSBwYWdlIHdoZW4gYSBtb2RhbCwgcG9wdXAgb3IgcG9wb3ZlciBpcyBvcGVuLlxuICpcbiAqIEBjb3B5cmlnaHQgMjAyMSBXaW50ZXIuXG4gKiBAYXV0aG9yIEJlbiBUaG9tc29uIDxnaXRAYWxmcmVpZG8uY29tPlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPdmVybGF5IGV4dGVuZHMgU25vd2JvYXJkLlNpbmdsZXRvbiB7XG4gICAgLyoqXG4gICAgICogQ29uc3RydWN0b3IuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1Nub3dib2FyZH0gc25vd2JvYXJkXG4gICAgICovXG4gICAgY29uc3RydWN0b3Ioc25vd2JvYXJkKSB7XG4gICAgICAgIHN1cGVyKHNub3dib2FyZCk7XG5cbiAgICAgICAgdGhpcy5vdmVybGF5ID0gbnVsbDtcbiAgICAgICAgdGhpcy5zaG93biA9IGZhbHNlO1xuICAgICAgICB0aGlzLmNvbG9yID0gJyMwMDAwMDAnO1xuICAgICAgICB0aGlzLm9wYWNpdHkgPSAwLjQyO1xuICAgICAgICB0aGlzLnNwZWVkID0gMTc1O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgdGhlIGRlcGVuZGVuY2llcy5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtBcnJheX1cbiAgICAgKi9cbiAgICBkZXBlbmRlbmNpZXMoKSB7XG4gICAgICAgIHJldHVybiBbJ3RyYW5zaXRpb24nXTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIGxpc3RlbmVycyBmb3IgZXZlbnRzLlxuICAgICAqXG4gICAgICogQHJldHVybnMge09iamVjdH1cbiAgICAgKi9cbiAgICBsaXN0ZW5zKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgcmVhZHk6ICdyZWFkeScsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVhZHkgZXZlbnQgaGFuZGxlci5cbiAgICAgKi9cbiAgICByZWFkeSgpIHtcbiAgICAgICAgdGhpcy5jcmVhdGVPdmVybGF5KCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRGVzdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIERlc3Ryb3lzIHRoZSBvdmVybGF5LlxuICAgICAqL1xuICAgIGRlc3RydWN0b3IoKSB7XG4gICAgICAgIHRoaXMuZGVzdHJveU92ZXJsYXkoKTtcbiAgICAgICAgc3VwZXIuZGVzdHJ1Y3RvcigpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENyZWF0ZXMgYW4gb3ZlcmxheSBlbGVtZW50IGFuZCBpbnNlcnRzIGl0IGludG8gdGhlIERPTS5cbiAgICAgKi9cbiAgICBjcmVhdGVPdmVybGF5KCkge1xuICAgICAgICB0aGlzLm92ZXJsYXkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgICAgICAgdGhpcy5vdmVybGF5LmlkID0gJ292ZXJsYXknO1xuICAgICAgICB0aGlzLnNldFN0eWxlKCk7XG4gICAgICAgIHRoaXMub3ZlcmxheS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ292ZXJsYXkuY2xpY2tlZCcsIHRoaXMub3ZlcmxheSwgZXZlbnQpO1xuICAgICAgICB9KTtcblxuICAgICAgICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHRoaXMub3ZlcmxheSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVtb3ZlcyBhbmQgZGVzdHJveXMgdGhlIG92ZXJsYXkgZWxlbWVudCBmcm9tIHRoZSBET00uXG4gICAgICovXG4gICAgZGVzdHJveU92ZXJsYXkoKSB7XG4gICAgICAgIGRvY3VtZW50LmJvZHkucmVtb3ZlQ2hpbGQodGhpcy5vdmVybGF5KTtcbiAgICAgICAgdGhpcy5vdmVybGF5ID0gbnVsbDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZXRzIHRoZSBzdHlsaW5nIG9uIHRoZSBvdmVybGF5LlxuICAgICAqL1xuICAgIHNldFN0eWxlKCkge1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUuYmFja2dyb3VuZENvbG9yID0gdGhpcy5jb2xvcjtcbiAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLnBvc2l0aW9uID0gJ2ZpeGVkJztcbiAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLnRvcCA9IDA7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5sZWZ0ID0gMDtcbiAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLnpJbmRleCA9IDEwMDA7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS50cmFuc2l0aW9uUHJvcGVydHkgPSAnb3BhY2l0eSc7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS50cmFuc2l0aW9uVGltaW5nRnVuY3Rpb24gPSAnZWFzZS1vdXQnO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUudHJhbnNpdGlvbkR1cmF0aW9uID0gYCR7dGhpcy5zcGVlZH1tc2A7XG5cbiAgICAgICAgd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiB7XG4gICAgICAgICAgICBpZiAodGhpcy5zaG93bikge1xuICAgICAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS53aWR0aCA9ICcxMDAlJztcbiAgICAgICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUuaGVpZ2h0ID0gJzEwMCUnO1xuICAgICAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5vcGFjaXR5ID0gdGhpcy5vcGFjaXR5O1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUud2lkdGggPSAnMHB4JztcbiAgICAgICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUuaGVpZ2h0ID0gJzBweCc7XG4gICAgICAgICAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLm9wYWNpdHkgPSAwO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTaG93cyB0aGUgb3ZlcmxheS5cbiAgICAgKlxuICAgICAqIEZpcmVzIGFuIFwib3ZlcmxheS5zaG93XCIgZXZlbnQsIGFuZCBmb2xsb3dzIHVwIHdpdGggYW4gXCJvdmVybGF5LnNob3duXCIgd2hlbiB0aGUgdHJhbnNpdGlvbiBjb21wbGV0ZXMuXG4gICAgICovXG4gICAgc2hvdygpIHtcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ292ZXJsYXkuc2hvdycsIHRoaXMub3ZlcmxheSk7XG4gICAgICAgIHRoaXMuc2hvd24gPSB0cnVlO1xuICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUud2lkdGggPSAnMTAwJSc7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5oZWlnaHQgPSAnMTAwJSc7XG4gICAgICAgIHdpbmRvdy5yZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5vdmVybGF5LnN0eWxlLm9wYWNpdHkgPSB0aGlzLm9wYWNpdHk7XG5cbiAgICAgICAgICAgIHRoaXMub3ZlcmxheS5hZGRFdmVudExpc3RlbmVyKCd0cmFuc2l0aW9uZW5kJywgKCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KCdvdmVybGF5LnNob3duJywgdGhpcy5vdmVybGF5KTtcbiAgICAgICAgICAgIH0sIHtcbiAgICAgICAgICAgICAgICBvbmNlOiB0cnVlLFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhpZGVzIHRoZSBvdmVybGF5LlxuICAgICAqXG4gICAgICogRmlyZXMgYW4gXCJvdmVybGF5LmhpZGVcIiBldmVudCwgYW5kIGZvbGxvd3MgdXAgd2l0aCBhbiBcIm92ZXJsYXkuaGlkZGVuXCIgd2hlbiB0aGUgdHJhbnNpdGlvbiBjb21wbGV0ZXMuXG4gICAgICovXG4gICAgaGlkZSgpIHtcbiAgICAgICAgdGhpcy5zbm93Ym9hcmQuZ2xvYmFsRXZlbnQoJ292ZXJsYXkuaGlkZScsIHRoaXMub3ZlcmxheSk7XG4gICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5vcGFjaXR5ID0gMDtcbiAgICAgICAgdGhpcy5vdmVybGF5LmFkZEV2ZW50TGlzdGVuZXIoJ3RyYW5zaXRpb25lbmQnLCAoKSA9PiB7XG4gICAgICAgICAgICB0aGlzLnNob3duID0gZmFsc2U7XG4gICAgICAgICAgICB0aGlzLm92ZXJsYXkuc3R5bGUud2lkdGggPSAnMHB4JztcbiAgICAgICAgICAgIHRoaXMub3ZlcmxheS5zdHlsZS5oZWlnaHQgPSAnMHB4JztcbiAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLmdsb2JhbEV2ZW50KCdvdmVybGF5LmhpZGRlbicsIHRoaXMub3ZlcmxheSk7XG4gICAgICAgIH0sIHtcbiAgICAgICAgICAgIG9uY2U6IHRydWUsXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFRvZ2dsZXMgdGhlIG92ZXJsYXkuXG4gICAgICovXG4gICAgdG9nZ2xlKCkge1xuICAgICAgICBpZiAodGhpcy5zaG93bikge1xuICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnNob3coKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZXRzIHRoZSBjb2xvciBvZiB0aGUgb3ZlcmxheS5cbiAgICAgKlxuICAgICAqIEZsdWVudCBtZXRob2QuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gY29sb3JcbiAgICAgKiBAcmV0dXJucyB7T3ZlcmxheX1cbiAgICAgKi9cbiAgICBzZXRDb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gU3RyaW5nKGNvbG9yKTtcbiAgICAgICAgdGhpcy5zZXRTdHlsZSgpO1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZXRzIHRoZSBvcGFjaXR5IG9mIHRoZSBvdmVybGF5LlxuICAgICAqXG4gICAgICogRmx1ZW50IG1ldGhvZC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBvcGFjaXR5XG4gICAgICogQHJldHVybnMge092ZXJsYXl9XG4gICAgICovXG4gICAgc2V0T3BhY2l0eShvcGFjaXR5KSB7XG4gICAgICAgIHRoaXMub3BhY2l0eSA9IE51bWJlcihvcGFjaXR5KTtcbiAgICAgICAgdGhpcy5zZXRTdHlsZSgpO1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZXRzIHRoZSBzcGVlZCBvZiB0aGUgdHJhbnNpdGlvbiBmb3IgdGhlIG92ZXJsYXkuXG4gICAgICpcbiAgICAgKiBGbHVlbnQgbWV0aG9kLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtOdW1iZXJ9IHNwZWVkXG4gICAgICogQHJldHVybnMge092ZXJsYXl9XG4gICAgICovXG4gICAgc2V0U3BlZWQoc3BlZWQpIHtcbiAgICAgICAgdGhpcy5zcGVlZCA9IE51bWJlcihzcGVlZCk7XG4gICAgICAgIHRoaXMuc2V0U3R5bGUoKTtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxufVxuIl0sIm5hbWVzIjpbIk92ZXJsYXkiLCJ3aW5kb3ciLCJTbm93Ym9hcmQiLCJ1bmRlZmluZWQiLCJFcnJvciIsImFkZFBsdWdpbiIsInNub3dib2FyZCIsIm92ZXJsYXkiLCJzaG93biIsImNvbG9yIiwib3BhY2l0eSIsInNwZWVkIiwicmVhZHkiLCJjcmVhdGVPdmVybGF5IiwiZGVzdHJveU92ZXJsYXkiLCJkb2N1bWVudCIsImNyZWF0ZUVsZW1lbnQiLCJpZCIsInNldFN0eWxlIiwiYWRkRXZlbnRMaXN0ZW5lciIsImV2ZW50IiwiZ2xvYmFsRXZlbnQiLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJyZW1vdmVDaGlsZCIsInN0eWxlIiwiYmFja2dyb3VuZENvbG9yIiwicG9zaXRpb24iLCJ0b3AiLCJsZWZ0IiwiekluZGV4IiwidHJhbnNpdGlvblByb3BlcnR5IiwidHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uIiwidHJhbnNpdGlvbkR1cmF0aW9uIiwicmVxdWVzdEFuaW1hdGlvbkZyYW1lIiwid2lkdGgiLCJoZWlnaHQiLCJvbmNlIiwiaGlkZSIsInNob3ciLCJTdHJpbmciLCJOdW1iZXIiLCJTaW5nbGV0b24iXSwic291cmNlUm9vdCI6IiJ9