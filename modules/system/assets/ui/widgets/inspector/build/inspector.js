"use strict";
(self["webpackChunk_wintercms_system"] = self["webpackChunk_wintercms_system"] || []).push([["/assets/ui/widgets/inspector/build/inspector"],{

/***/ "./assets/ui/widgets/inspector/inspector.js":
/*!**************************************************!*\
  !*** ./assets/ui/widgets/inspector/inspector.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_Manager__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main/Manager */ "./assets/ui/widgets/inspector/main/Manager.js");


if (window.Snowboard === undefined) {
  throw new Error('The Snowboard library must be loaded in order to use the Inspector widget');
}

(function (Snowboard) {
  Snowboard.addPlugin('system.widgets.inspector', _main_Manager__WEBPACK_IMPORTED_MODULE_0__["default"]);
})(window.Snowboard);

/***/ }),

/***/ "./assets/ui/widgets/inspector/main/Manager.js":
/*!*****************************************************!*\
  !*** ./assets/ui/widgets/inspector/main/Manager.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Manager)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "../../node_modules/vue/dist/vue.esm-bundler.js");
/* harmony import */ var _Inspector_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Inspector.vue */ "./assets/ui/widgets/inspector/main/Inspector.vue");
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
    _this.currentInspector = null;
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
        ready: 'ready',
        'overlay.clicked': 'hideInspector'
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
          form: _this2.findForm(element),
          inspectorElement: null,
          inspectorVue: null,
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
      var _this3 = this;

      // Create a new inspector <div> to house the Vue instance
      inspector.inspectorElement = document.createElement('div');
      document.body.appendChild(inspector.inspectorElement);
      this.currentInspector = inspector; // Create Vue instance and mount it to the above <div>

      inspector.inspectorVue = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createApp)(_Inspector_vue__WEBPACK_IMPORTED_MODULE_1__["default"], {
        snowboard: this.snowboard,
        inspectedElement: inspector.element,
        form: inspector.form,
        title: inspector.title,
        description: inspector.description,
        placement: inspector.placement,
        fallbackPlacement: inspector.fallbackPlacement,
        offsetX: inspector.offset.x,
        offsetY: inspector.offset.y,
        hideFn: function hideFn() {
          return _this3.hideInspector();
        },
        config: inspector.config
      });
      inspector.inspectorVue.mount(inspector.inspectorElement);
    }
  }, {
    key: "hideInspector",
    value: function hideInspector() {
      if (!this.currentInspector) {
        return;
      }

      this.currentInspector.inspectorVue.unmount();
      document.body.removeChild(this.currentInspector.inspectorElement);
      this.currentInspector = null;
    }
  }, {
    key: "inspectableClick",
    value: function inspectableClick(event, inspector) {
      event.preventDefault();

      if (inspector === this.currentInspector) {
        return;
      }

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
    /**
     * Finds the form that the element belongs to.
     *
     * @param {HTMLElement} element
     * @returns {HTMLElement|undefined}
     */

  }, {
    key: "findForm",
    value: function findForm(element) {
      return element.closest('form');
    }
  }]);

  return Manager;
}(Snowboard.Singleton);



/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({});

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js ***!
  \***********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    label: {
      type: [String, Boolean],
      "default": false
    }
  }
});

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _popperjs_core_lib_popper_lite__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @popperjs/core/lib/popper-lite */ "../../node_modules/@popperjs/core/lib/popper-lite.js");
/* harmony import */ var _popperjs_core_lib_modifiers_arrow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @popperjs/core/lib/modifiers/arrow */ "../../node_modules/@popperjs/core/lib/modifiers/arrow.js");
/* harmony import */ var _popperjs_core_lib_modifiers_flip__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @popperjs/core/lib/modifiers/flip */ "../../node_modules/@popperjs/core/lib/modifiers/flip.js");
/* harmony import */ var _popperjs_core_lib_modifiers_preventOverflow__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @popperjs/core/lib/modifiers/preventOverflow */ "../../node_modules/@popperjs/core/lib/modifiers/preventOverflow.js");
/* harmony import */ var _popperjs_core_lib_modifiers_offset__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @popperjs/core/lib/modifiers/offset */ "../../node_modules/@popperjs/core/lib/modifiers/offset.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }






/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    shown: {
      type: Boolean,
      "default": false
    },
    snowboard: {
      type: Object,
      required: true
    },
    inspectedElement: {
      type: HTMLElement,
      required: true
    },
    hideFn: {
      type: Function,
      required: true
    },
    placement: {
      type: String,
      "default": 'auto',
      validate: function validate(value) {
        return ['auto', 'top', 'right', 'bottom', 'left'].indexOf(value) !== -1;
      }
    },
    fallbackPlacement: {
      type: String,
      "default": 'bottom',
      validate: function validate(value) {
        return ['top', 'right', 'bottom', 'left'].indexOf(value) !== -1;
      }
    },
    offsetX: {
      type: Number,
      "default": 0
    },
    offsetY: {
      type: Number,
      "default": 0
    }
  },
  data: function data() {
    return {
      top: 0,
      left: 0,
      inspectedElementStyle: {
        position: null,
        zIndex: null
      },
      popperInstance: null
    };
  },
  watch: {
    /**
     * Detects if the popover is shown. If shown, show the overlay and create the popover.
     *
     * @param {Boolean} isShown
     */
    shown: function shown(isShown) {
      var _this = this;

      if (isShown) {
        this.snowboard.overlay().show();
        this.highlightInspectedElement();
        this.$nextTick(function () {
          _this.createPopper();
        });
      } else {
        this.popperInstance.destroy();
        this.snowboard.overlay().hide();
      }
    }
  },
  unmounted: function unmounted() {
    this.snowboard.overlay().hide();
  },
  methods: {
    createPopper: function createPopper() {
      this.popperInstance = (0,_popperjs_core_lib_popper_lite__WEBPACK_IMPORTED_MODULE_0__.createPopper)(this.inspectedElement, this.$refs.popover, {
        modifiers: [_popperjs_core_lib_modifiers_arrow__WEBPACK_IMPORTED_MODULE_1__["default"], _popperjs_core_lib_modifiers_flip__WEBPACK_IMPORTED_MODULE_2__["default"], _popperjs_core_lib_modifiers_preventOverflow__WEBPACK_IMPORTED_MODULE_3__["default"], _objectSpread(_objectSpread({}, _popperjs_core_lib_modifiers_offset__WEBPACK_IMPORTED_MODULE_4__["default"]), {}, {
          options: {
            offset: [this.offsetX, this.offsetY + 10]
          }
        })]
      });
    },
    highlightInspectedElement: function highlightInspectedElement() {
      this.inspectedElementStyle.position = this.inspectedElement.style.position;
      this.inspectedElementStyle.zIndex = this.inspectedElement.style.zIndex;
      this.inspectedElement.style.position = 'relative';
      this.inspectedElement.style.zIndex = 1001;
    }
  }
});

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _layout_Popover_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../layout/Popover.vue */ "./assets/ui/widgets/inspector/layout/Popover.vue");
/* harmony import */ var _fields_Field_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../fields/Field.vue */ "./assets/ui/widgets/inspector/fields/Field.vue");
/* harmony import */ var _fields_FieldLabel_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../fields/FieldLabel.vue */ "./assets/ui/widgets/inspector/fields/FieldLabel.vue");
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }




/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {
    Field: _fields_Field_vue__WEBPACK_IMPORTED_MODULE_1__["default"],
    FieldLabel: _fields_FieldLabel_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  props: {
    snowboard: {
      type: Object,
      required: true
    },
    inspectedElement: {
      type: HTMLElement,
      required: true
    },
    form: {
      type: HTMLElement,
      "default": null
    },
    hideFn: {
      type: Function,
      required: true
    },
    config: {
      type: [String, Object],
      "default": null
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      "default": null
    },
    layout: {
      type: String,
      "default": 'popover',
      validate: function validate(value) {
        return ['popover', 'sidebar'].indexOf(value) !== -1;
      }
    },
    placement: {
      type: String,
      "default": 'auto',
      validate: function validate(value) {
        return ['auto', 'top', 'right', 'bottom', 'left'].indexOf(value) !== -1;
      }
    },
    fallbackPlacement: {
      type: String,
      "default": 'bottom',
      validate: function validate(value) {
        return ['top', 'right', 'bottom', 'left'].indexOf(value) !== -1;
      }
    },
    offsetX: {
      type: Number,
      "default": 0
    },
    offsetY: {
      type: Number,
      "default": 0
    }
  },
  data: function data() {
    return {
      showInspector: false,
      userConfig: {
        title: null,
        description: null,
        fields: null
      }
    };
  },
  computed: {
    inspectorTitle: function inspectorTitle() {
      if (this.userConfig.title) {
        return this.userConfig.title;
      }

      return this.title;
    },
    inspectorDescription: function inspectorDescription() {
      if (this.userConfig.description) {
        return this.userConfig.description;
      }

      return this.description;
    },
    inspectorFields: function inspectorFields() {
      return this.userConfig.fields;
    },
    layoutProps: function layoutProps() {
      return {
        shown: this.showInspector,
        snowboard: this.snowboard,
        inspectedElement: this.inspectedElement,
        hideFn: this.hideFn,
        placement: this.placement,
        fallbackPlacement: this.fallbackPlacement,
        offsetX: this.offsetX,
        offsetY: this.offsetY
      };
    },
    layoutComponent: function layoutComponent() {
      return _layout_Popover_vue__WEBPACK_IMPORTED_MODULE_0__["default"];
    }
  },
  mounted: function mounted() {
    this.getConfiguration();
  },
  methods: {
    /**
     * Gets the configuration of the Inspector.
     *
     * If a config is defined locally via [data-inspector-config], this is used as a default. The
     * Backend will always be queried for a configuration to determine if any overrides need to be
     * applied.
     */
    getConfiguration: function getConfiguration() {
      if (this.config) {
        var userConfig = typeof this.config === 'string' ? JSON.parse(this.config) : this.config;

        if (userConfig.title) {
          this.userConfig.title = userConfig.title;
        }

        if (userConfig.description) {
          this.userConfig.description = userConfig.description;
        }

        this.userConfig.fields = this.processFieldsConfig(userConfig.fields || userConfig.properties || {});
      }

      this.getConfigurationFromBackend();
    },

    /**
     * Queries the backend for the final Inspector configuration.
     */
    getConfigurationFromBackend: function getConfigurationFromBackend() {
      var _this = this;

      this.snowboard.request(this.form, 'onGetInspectorConfiguration', {
        success: function success(data) {
          if (data.configuration.title) {
            _this.userConfig.title = data.configuration.title;
          }

          if (data.configuration.description) {
            _this.userConfig.description = data.configuration.description;
          }

          if (data.configuration.fields) {
            _this.userConfig.fields = _this.processFieldsConfig(data.configuration.fields);
          } else if (data.configuration.properties) {
            _this.userConfig.fields = _this.processFieldsConfig(data.configuration.properties);
          }
        },
        complete: function complete() {
          _this.showInspector = true;
        }
      });
    },
    processFieldsConfig: function processFieldsConfig(config) {
      var fieldsConfig = Array.isArray(config) ? this.reformatProperties(config) : config; // Post-process the fields config

      Object.entries(fieldsConfig).forEach(function (entry) {
        var _entry = _slicedToArray(entry, 2),
            fieldConfig = _entry[1]; // Rename "title" property to "label"


        if (fieldConfig.title !== undefined) {
          if (fieldConfig.label === undefined) {
            fieldConfig.label = fieldConfig.title;
          }

          delete fieldConfig.title;
        }

        if (fieldConfig.type === 'set') {
          fieldConfig.type = 'checkboxlist';
        }
      });
      console.log(fieldsConfig);
      return fieldsConfig;
    },
    reformatProperties: function reformatProperties(properties) {
      var config = {};
      properties.forEach(function (property) {
        config[property.property] = property;
        delete config[property.property].property;
      });
      return config;
    }
  }
});

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "../../node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = {
  "class": "field"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default")]);
}

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "../../node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = {
  key: 0,
  "class": "field-label"
};
var _hoisted_2 = ["textContent"];
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return $props.label ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
    "class": "label",
    textContent: (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.label)
  }, null, 8
  /* PROPS */
  , _hoisted_2)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true);
}

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96 ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "../../node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = {
  ref: "popover",
  "class": "inspector-wrapper"
};
var _hoisted_2 = {
  key: 0,
  "class": "inspector popover-layout"
};

var _hoisted_3 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
  "class": "arrow",
  "data-popper-arrow": ""
}, null, -1
/* HOISTED */
);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(vue__WEBPACK_IMPORTED_MODULE_0__.Transition, {
    name: "popover-fade"
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [$props.shown ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_2, [_hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("header", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "title"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "description"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        "class": "inspector-hide wn-icon-remove",
        onClick: _cache[0] || (_cache[0] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
          return $props.hideFn && $props.hideFn.apply($props, arguments);
        }, ["stop"]))
      })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("main", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "fields")])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    _: 3
    /* FORWARDED */

  })], 512
  /* NEED_PATCH */
  );
}

/***/ }),

/***/ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4 ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "../../node_modules/vue/dist/vue.esm-bundler.js");

var _hoisted_1 = ["textContent"];
var _hoisted_2 = ["textContent"];

var _hoisted_3 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
  "class": "field-control"
}, null, -1
/* HOISTED */
);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_FieldLabel = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("FieldLabel");

  var _component_Field = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("Field");

  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)($options.layoutComponent), (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)($options.layoutProps)), {
    title: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [$options.inspectorTitle ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
        key: 0,
        "class": "inspector-title",
        textContent: (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($options.inspectorTitle)
      }, null, 8
      /* PROPS */
      , _hoisted_1)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    description: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [$options.inspectorDescription ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
        key: 0,
        "class": "inspector-description",
        textContent: (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($options.inspectorDescription)
      }, null, 8
      /* PROPS */
      , _hoisted_2)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)];
    }),
    fields: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($options.inspectorFields, function (field, i) {
        return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_Field, {
          key: i
        }, {
          "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
            return [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_FieldLabel, {
              label: field.label
            }, null, 8
            /* PROPS */
            , ["label"]), _hoisted_3];
          }),
          _: 2
          /* DYNAMIC */

        }, 1024
        /* DYNAMIC_SLOTS */
        );
      }), 128
      /* KEYED_FRAGMENT */
      ))];
    }),
    _: 1
    /* STABLE */

  }, 16
  /* FULL_PROPS */
  );
}

/***/ }),

/***/ "../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_cssWithMappingToString_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../../../node_modules/css-loader/dist/runtime/cssWithMappingToString.js */ "../../node_modules/css-loader/dist/runtime/cssWithMappingToString.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_cssWithMappingToString_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_cssWithMappingToString_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../../../../node_modules/css-loader/dist/runtime/api.js */ "../../node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_cssWithMappingToString_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, ".inspector-wrapper {\n  z-index: 1002;\n}\n.inspector.popover-layout {\n  position: relative;\n  width: 360px;\n  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.12), 0 1px 4px rgba(0, 0, 0, 0.24);\n  border-radius: 3px;\n  font-size: 12px;\n}\n.inspector.popover-layout .arrow,\n.inspector.popover-layout .arrow::before {\n  position: absolute;\n  width: 10px;\n  height: 10px;\n}\n.inspector.popover-layout .arrow {\n  visibility: hidden;\n  z-index: 1002;\n}\n.inspector.popover-layout .arrow::before {\n  visibility: visible;\n  content: '';\n  transform: rotate(45deg);\n  background-color: #3498db;\n}\n.inspector.popover-layout header {\n  position: relative;\n  padding: 10px 46px 10px 16px;\n  background: #3498db;\n  color: #fff;\n  border-bottom: 1px solid #2383c4;\n  border-top-left-radius: 3px;\n  border-top-right-radius: 3px;\n  text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.22);\n  z-index: 1003;\n}\n.inspector.popover-layout header .inspector-title {\n  font-weight: bold;\n  font-size: 14px;\n}\n.inspector.popover-layout header .inspector-description {\n  font-weight: normal;\n  font-size: 12px;\n  margin-top: -3px;\n}\n.inspector.popover-layout header .inspector-hide {\n  position: absolute;\n  top: 0;\n  right: 0;\n  width: 30px;\n  height: 30px;\n  line-height: 30px;\n  text-align: center;\n  background: #2383c4;\n  border-top-right-radius: 3px;\n  border-bottom-left-radius: 3px;\n  cursor: pointer;\n  transition: background-color 175ms ease;\n}\n.inspector.popover-layout header .inspector-hide::before {\n  transition: opacity 175ms ease;\n  opacity: 0.7;\n  margin-right: 0;\n}\n.inspector.popover-layout header .inspector-hide:hover {\n  background: #2077b2;\n}\n.inspector.popover-layout header .inspector-hide:hover::before {\n  opacity: 1;\n}\n.inspector.popover-layout main {\n  position: relative;\n  background: #f2f2f2;\n  border-bottom-left-radius: 3px;\n  border-bottom-right-radius: 3px;\n  z-index: 1003;\n}\n.inspector.popover-layout main .field {\n  display: flex;\n  flex-direction: row;\n  justify-content: stretch;\n}\n.inspector.popover-layout main .field .field-label {\n  padding: 5px 10px;\n  flex: 3 0;\n  background: #f2f2f2;\n  color: #405261;\n  border-right: 1px solid #cccccc;\n}\n.inspector.popover-layout main .field .field-control {\n  background: #fff;\n  flex: 5 0;\n}\n.inspector.popover-layout main .field + .field {\n  border-top: 1px solid #cccccc;\n}\n.inspector-wrapper[data-popper-placement^='top'] .inspector.popover-layout .arrow {\n  bottom: -5px;\n}\n.inspector-wrapper[data-popper-placement^='bottom'] .inspector.popover-layout .arrow {\n  top: -5px;\n}\n.inspector-wrapper[data-popper-placement^='left'] .inspector.popover-layout .arrow {\n  right: -5px;\n}\n.inspector-wrapper[data-popper-placement^='right'] .inspector.popover-layout .arrow {\n  left: -5px;\n}\n.popover-fade-enter-active,\n.popover-fade-leave-active {\n  transition: opacity 175ms ease-out, transform 175ms ease-out;\n}\n.popover-fade-enter-from,\n.popover-fade-leave-to {\n  opacity: 0;\n  transform: translateY(20px);\n}\n", "",{"version":3,"sources":["webpack://./assets/ui/widgets/inspector/layout/Popover.vue"],"names":[],"mappings":"AASA;EACI,aAAA;AARJ;AAWA;EACI,kBAAA;EACA,YAAA;EACA,wEAAA;EACA,kBAAA;EACA,eAAA;AATJ;AAIA;;EASQ,kBAAA;EACA,WAAA;EACA,YAAA;AATR;AAFA;EAeQ,kBAAA;EACA,aAAA;AAVR;AANA;EAoBQ,mBAAA;EACA,WAAA;EACA,wBAAA;EACA,yBAAA;AAXR;AAZA;EA2BQ,kBAAA;EACA,4BAAA;EACA,mBAAA;EACA,WAAA;EACA,gCAAA;EACA,2BAAA;EACA,4BAAA;EACA,4CAAA;EACA,aAAA;AAZR;AAvBA;EAsCY,iBAAA;EACA,eAAA;AAZZ;AA3BA;EA2CY,mBAAA;EACA,eAAA;EACA,gBAAA;AAbZ;AAhCA;EAiDY,kBAAA;EACA,MAAA;EACA,QAAA;EACA,WAAA;EACA,YAAA;EACA,iBAAA;EACA,kBAAA;EACA,mBAAA;EACA,4BAAA;EACA,8BAAA;EACA,eAAA;EACA,uCAAA;AAdZ;AAgBY;EACI,8BAAA;EACA,YAAA;EACA,eAAA;AAdhB;AAiBY;EACI,mBAAA;AAfhB;AAiBgB;EACI,UAAA;AAfpB;AAzDA;EA+EQ,kBAAA;EACA,mBAAA;EACA,8BAAA;EACA,+BAAA;EACA,aAAA;AAnBR;AAhEA;EAsFY,aAAA;EACA,mBAAA;EACA,wBAAA;AAnBZ;AArEA;EA2FgB,iBAAA;EACA,SAAA;EACA,mBAAA;EACA,cAAA;EACA,+BAAA;AAnBhB;AA5EA;EAmGgB,gBAAA;EACA,SAAA;AApBhB;AAhFA;EAyGY,6BAAA;AAtBZ;AA4BA;EACI,YAAA;AA1BJ;AA4BA;EACI,SAAA;AA1BJ;AA4BA;EACI,WAAA;AA1BJ;AA4BA;EACI,UAAA;AA1BJ;AA8BA;;EAEI,4DAAA;AA5BJ;AAgCA;;EAEI,UAAA;EACA,2BAAA;AA9BJ","sourcesContent":["\n@import (reference) '../../../less/global.less';\n@import (reference) '../style/variables.less';\n\n// VARIABLES\n@inpsector-popover-width: 360px;\n@inspector-border-radius: @border-radius-base;\n\n// STYLING\n.inspector-wrapper {\n    z-index: 1002;\n}\n\n.inspector.popover-layout {\n    position: relative;\n    width: @inpsector-popover-width;\n    box-shadow: @overlay-box-shadow;\n    border-radius: @inspector-border-radius;\n    font-size: @inspector-font-size;\n\n    .arrow,\n    .arrow::before {\n        position: absolute;\n        width: 10px;\n        height: 10px;\n    }\n\n    .arrow {\n        visibility: hidden;\n        z-index: 1002;\n    }\n\n    .arrow::before {\n        visibility: visible;\n        content: '';\n        transform: rotate(45deg);\n        background-color: @inspector-header-bg;\n    }\n\n    header {\n        position: relative;\n        padding: @padding-large-vertical (@padding-large-horizontal + 30px) @padding-large-vertical @padding-large-horizontal;\n        background: @inspector-header-bg;\n        color: @inspector-header-fg;\n        border-bottom: 1px solid darken(@inspector-header-bg, 8%);\n        border-top-left-radius: @inspector-border-radius;\n        border-top-right-radius: @inspector-border-radius;\n        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.22);\n        z-index: 1003;\n\n        .inspector-title {\n            font-weight: @inspector-header-title-weight;\n            font-size: @inspector-header-title-size;\n        }\n\n        .inspector-description {\n            font-weight: @inspector-header-description-weight;\n            font-size: @inspector-header-description-size;\n            margin-top: -3px;\n        }\n\n        .inspector-hide {\n            position: absolute;\n            top: 0;\n            right: 0;\n            width: 30px;\n            height: 30px;\n            line-height: 30px;\n            text-align: center;\n            background: darken(@inspector-header-bg, 8%);\n            border-top-right-radius: @inspector-border-radius;\n            border-bottom-left-radius: @inspector-border-radius;\n            cursor: pointer;\n            transition: background-color 175ms ease;\n\n            &::before {\n                transition: opacity 175ms ease;\n                opacity: 0.7;\n                margin-right: 0;\n            }\n\n            &:hover {\n                background: darken(@inspector-header-bg, 12%);\n\n                &::before {\n                    opacity: 1;\n                }\n            }\n        }\n    }\n\n    main {\n        position: relative;\n        background: @inspector-bg;\n        border-bottom-left-radius: @inspector-border-radius;\n        border-bottom-right-radius: @inspector-border-radius;\n        z-index: 1003;\n\n        .field {\n            display: flex;\n            flex-direction: row;\n            justify-content: stretch;\n\n            .field-label {\n                padding: @padding-small-vertical @padding-small-horizontal;\n                flex: 3 0;\n                background: @inspector-field-label-bg;\n                color: @inspector-field-label-fg;\n                border-right: 1px solid @inspector-field-border;\n            }\n\n            .field-control {\n                background: @inspector-field-bg;\n                flex: 5 0;\n            }\n        }\n\n        .field + .field {\n            border-top: 1px solid @inspector-field-border;\n        }\n    }\n}\n\n// ARROW PLACEMENT\n.inspector-wrapper[data-popper-placement^='top'] .inspector.popover-layout .arrow {\n    bottom: -5px;\n}\n.inspector-wrapper[data-popper-placement^='bottom'] .inspector.popover-layout .arrow {\n    top: -5px;\n}\n.inspector-wrapper[data-popper-placement^='left'] .inspector.popover-layout .arrow {\n    right: -5px;\n}\n.inspector-wrapper[data-popper-placement^='right'] .inspector.popover-layout .arrow {\n    left: -5px;\n}\n\n// TRANSITIONS\n.popover-fade-enter-active,\n.popover-fade-leave-active {\n    transition: opacity 175ms ease-out,\n                transform 175ms ease-out;\n}\n\n.popover-fade-enter-from,\n.popover-fade-leave-to {\n    opacity: 0;\n    transform: translateY(20px);\n}\n"],"sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "../../node_modules/style-loader/dist/cjs.js!../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ../../node_modules/style-loader/dist/cjs.js!../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_17_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_17_use_2_node_modules_less_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_style_index_0_id_96100b96_lang_less__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../../../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Popover.vue?vue&type=style&index=0&id=96100b96&lang=less */ "../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_17_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_17_use_2_node_modules_less_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_style_index_0_id_96100b96_lang_less__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_17_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_17_use_2_node_modules_less_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_style_index_0_id_96100b96_lang_less__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/Field.vue":
/*!******************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/Field.vue ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Field_vue_vue_type_template_id_00a3b64e__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Field.vue?vue&type=template&id=00a3b64e */ "./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e");
/* harmony import */ var _Field_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Field.vue?vue&type=script&lang=js */ "./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js");
/* harmony import */ var _home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../node_modules/vue-loader/dist/exportHelper.js */ "../../node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Field_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Field_vue_vue_type_template_id_00a3b64e__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"assets/ui/widgets/inspector/fields/Field.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/FieldLabel.vue":
/*!***********************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/FieldLabel.vue ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _FieldLabel_vue_vue_type_template_id_715285cb__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FieldLabel.vue?vue&type=template&id=715285cb */ "./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb");
/* harmony import */ var _FieldLabel_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FieldLabel.vue?vue&type=script&lang=js */ "./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js");
/* harmony import */ var _home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../node_modules/vue-loader/dist/exportHelper.js */ "../../node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_FieldLabel_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_FieldLabel_vue_vue_type_template_id_715285cb__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"assets/ui/widgets/inspector/fields/FieldLabel.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./assets/ui/widgets/inspector/layout/Popover.vue":
/*!********************************************************!*\
  !*** ./assets/ui/widgets/inspector/layout/Popover.vue ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Popover_vue_vue_type_template_id_96100b96__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Popover.vue?vue&type=template&id=96100b96 */ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96");
/* harmony import */ var _Popover_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Popover.vue?vue&type=script&lang=js */ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js");
/* harmony import */ var _Popover_vue_vue_type_style_index_0_id_96100b96_lang_less__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Popover.vue?vue&type=style&index=0&id=96100b96&lang=less */ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less");
/* harmony import */ var _home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../node_modules/vue-loader/dist/exportHelper.js */ "../../node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Popover_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Popover_vue_vue_type_template_id_96100b96__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"assets/ui/widgets/inspector/layout/Popover.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./assets/ui/widgets/inspector/main/Inspector.vue":
/*!********************************************************!*\
  !*** ./assets/ui/widgets/inspector/main/Inspector.vue ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Inspector_vue_vue_type_template_id_5c17e1d4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Inspector.vue?vue&type=template&id=5c17e1d4 */ "./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4");
/* harmony import */ var _Inspector_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Inspector.vue?vue&type=script&lang=js */ "./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js");
/* harmony import */ var _home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../node_modules/vue-loader/dist/exportHelper.js */ "../../node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_ben_Projects_Personal_wintercms_winter_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Inspector_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Inspector_vue_vue_type_template_id_5c17e1d4__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"assets/ui/widgets/inspector/main/Inspector.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js":
/*!******************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Field_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Field_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Field.vue?vue&type=script&lang=js */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js":
/*!***********************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_FieldLabel_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_FieldLabel_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./FieldLabel.vue?vue&type=script&lang=js */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js":
/*!********************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Popover.vue?vue&type=script&lang=js */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js":
/*!********************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Inspector_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Inspector_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Inspector.vue?vue&type=script&lang=js */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e":
/*!************************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Field_vue_vue_type_template_id_00a3b64e__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Field_vue_vue_type_template_id_00a3b64e__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Field.vue?vue&type=template&id=00a3b64e */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/Field.vue?vue&type=template&id=00a3b64e");


/***/ }),

/***/ "./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb":
/*!*****************************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_FieldLabel_vue_vue_type_template_id_715285cb__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_FieldLabel_vue_vue_type_template_id_715285cb__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./FieldLabel.vue?vue&type=template&id=715285cb */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/fields/FieldLabel.vue?vue&type=template&id=715285cb");


/***/ }),

/***/ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96":
/*!**************************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96 ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_template_id_96100b96__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_template_id_96100b96__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Popover.vue?vue&type=template&id=96100b96 */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=template&id=96100b96");


/***/ }),

/***/ "./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4":
/*!**************************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4 ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Inspector_vue_vue_type_template_id_5c17e1d4__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Inspector_vue_vue_type_template_id_5c17e1d4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Inspector.vue?vue&type=template&id=5c17e1d4 */ "../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/main/Inspector.vue?vue&type=template&id=5c17e1d4");


/***/ }),

/***/ "./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less":
/*!*****************************************************************************************************!*\
  !*** ./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_17_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_17_use_2_node_modules_less_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_Popover_vue_vue_type_style_index_0_id_96100b96_lang_less__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/style-loader/dist/cjs.js!../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../../../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./Popover.vue?vue&type=style&index=0&id=96100b96&lang=less */ "../../node_modules/style-loader/dist/cjs.js!../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-17.use[1]!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-17.use[2]!../../node_modules/less-loader/dist/cjs.js!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./assets/ui/widgets/inspector/layout/Popover.vue?vue&type=style&index=0&id=96100b96&lang=less");


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["assets/js/vendor/vendor"], () => (__webpack_exec__("./assets/ui/widgets/inspector/inspector.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9idWlsZC9pbnNwZWN0b3IuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7QUFBQTs7QUFFQSxJQUFJQyxNQUFNLENBQUNDLFNBQVAsS0FBcUJDLFNBQXpCLEVBQW9DO0FBQ2hDLFFBQU0sSUFBSUMsS0FBSixDQUFVLDJFQUFWLENBQU47QUFDSDs7QUFFRCxDQUFDLFVBQUNGLFNBQUQsRUFBZTtBQUNaQSxFQUFBQSxTQUFTLENBQUNHLFNBQVYsQ0FBb0IsMEJBQXBCLEVBQWdETCxxREFBaEQ7QUFDSCxDQUZELEVBRUdDLE1BQU0sQ0FBQ0MsU0FGVjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDTkE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0lBQ3FCRjs7Ozs7QUFDakI7QUFDSjtBQUNBO0FBQ0E7QUFDQTtBQUNJLG1CQUFZUSxTQUFaLEVBQXVCO0FBQUE7O0FBQUE7O0FBQ25CLDhCQUFNQSxTQUFOO0FBRUEsVUFBS0MsbUJBQUwsR0FBMkIsRUFBM0I7QUFDQSxVQUFLQyxnQkFBTCxHQUF3QixJQUF4QjtBQUptQjtBQUt0QjtBQUVEO0FBQ0o7QUFDQTtBQUNBO0FBQ0E7Ozs7O1dBQ0ksd0JBQWU7QUFDWCxhQUFPLENBQUMsbUJBQUQsQ0FBUDtBQUNIO0FBRUQ7QUFDSjtBQUNBO0FBQ0E7QUFDQTs7OztXQUNJLG1CQUFVO0FBQ04sYUFBTztBQUNIQyxRQUFBQSxLQUFLLEVBQUUsT0FESjtBQUVILDJCQUFtQjtBQUZoQixPQUFQO0FBSUg7QUFFRDtBQUNKO0FBQ0E7Ozs7V0FDSSxpQkFBUTtBQUNKLFdBQUtDLHVCQUFMO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7QUFDQTtBQUNBOzs7O1dBQ0ksc0JBQWE7QUFDVCxXQUFLQyx5QkFBTDtBQUNBLFdBQUtKLG1CQUFMLEdBQTJCLEVBQTNCOztBQUVBO0FBQ0g7QUFFRDtBQUNKO0FBQ0E7Ozs7V0FDSSxt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Q0EsSUFBSSxLQUFVLEVBQUUsRUFjZjs7O0FBR0QsaUVBQWU7Ozs7Ozs7Ozs7Ozs7OztBQ3hCbU47Ozs7Ozs7Ozs7Ozs7OztBQ0FLOzs7Ozs7Ozs7Ozs7Ozs7QUNBSDs7Ozs7Ozs7Ozs7Ozs7O0FDQUUiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9pbnNwZWN0b3IuanMiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbWFpbi9NYW5hZ2VyLmpzIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2ZpZWxkcy9GaWVsZC52dWUiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvZmllbGRzL0ZpZWxkTGFiZWwudnVlIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2xheW91dC9Qb3BvdmVyLnZ1ZSIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9tYWluL0luc3BlY3Rvci52dWUiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbGF5b3V0L1BvcG92ZXIudnVlPzVlNmYiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbGF5b3V0L1BvcG92ZXIudnVlP2Q0ZDAiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvZmllbGRzL0ZpZWxkLnZ1ZT8yYTlmIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2ZpZWxkcy9GaWVsZExhYmVsLnZ1ZT9jMDJmIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2xheW91dC9Qb3BvdmVyLnZ1ZT8zYmMyIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL21haW4vSW5zcGVjdG9yLnZ1ZT83OWVmIiwid2VicGFjazovL0B3aW50ZXJjbXMvc3lzdGVtLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2ZpZWxkcy9GaWVsZC52dWU/NzIxMCIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9maWVsZHMvRmllbGRMYWJlbC52dWU/NDgxZiIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9sYXlvdXQvUG9wb3Zlci52dWU/OTRiZSIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9tYWluL0luc3BlY3Rvci52dWU/YzM2MyIsIndlYnBhY2s6Ly9Ad2ludGVyY21zL3N5c3RlbS8uL2Fzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9maWVsZHMvRmllbGQudnVlPzc3NTkiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvZmllbGRzL0ZpZWxkTGFiZWwudnVlPzI1MDkiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbGF5b3V0L1BvcG92ZXIudnVlP2M5NjYiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbWFpbi9JbnNwZWN0b3IudnVlP2U0Y2QiLCJ3ZWJwYWNrOi8vQHdpbnRlcmNtcy9zeXN0ZW0vLi9hc3NldHMvdWkvd2lkZ2V0cy9pbnNwZWN0b3IvbGF5b3V0L1BvcG92ZXIudnVlP2RiYjAiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IE1hbmFnZXIgZnJvbSAnLi9tYWluL01hbmFnZXInO1xuXG5pZiAod2luZG93LlNub3dib2FyZCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCdUaGUgU25vd2JvYXJkIGxpYnJhcnkgbXVzdCBiZSBsb2FkZWQgaW4gb3JkZXIgdG8gdXNlIHRoZSBJbnNwZWN0b3Igd2lkZ2V0Jylcbn1cblxuKChTbm93Ym9hcmQpID0+IHtcbiAgICBTbm93Ym9hcmQuYWRkUGx1Z2luKCdzeXN0ZW0ud2lkZ2V0cy5pbnNwZWN0b3InLCBNYW5hZ2VyKTtcbn0pKHdpbmRvdy5Tbm93Ym9hcmQpO1xuIiwiaW1wb3J0IHsgY3JlYXRlQXBwIH0gZnJvbSAndnVlJztcbmltcG9ydCBJbnNwZWN0b3IgZnJvbSAnLi9JbnNwZWN0b3IudnVlJztcblxuLyoqXG4gKiBJbnNwZWN0b3IgbWFuYWdlci5cbiAqXG4gKiBUaGlzIGNsYXNzIHByb3ZpZGVzIHRoZSBtYW5hZ2VtZW50IGFuZCBpbml0aWFsaXphdGlvbiBvZiBJbnNwZWN0b3Igd2lkZ2V0cyBvbiBhIHBhZ2UuXG4gKlxuICogQGNvcHlyaWdodCAyMDIxIFdpbnRlci5cbiAqIEBhdXRob3IgQmVuIFRob21zb24gPGdpdEBhbGZyZWlkby5jb20+XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE1hbmFnZXIgZXh0ZW5kcyBTbm93Ym9hcmQuU2luZ2xldG9uIHtcbiAgICAvKipcbiAgICAgKiBDb25zdHJ1Y3Rvci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U25vd2JvYXJkfSBzbm93Ym9hcmRcbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihzbm93Ym9hcmQpIHtcbiAgICAgICAgc3VwZXIoc25vd2JvYXJkKTtcblxuICAgICAgICB0aGlzLmluc3BlY3RhYmxlRWxlbWVudHMgPSBbXTtcbiAgICAgICAgdGhpcy5jdXJyZW50SW5zcGVjdG9yID0gbnVsbDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBEZWZpbmVzIHRoZSBkZXBlbmRlbmNpZXMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAgICovXG4gICAgZGVwZW5kZW5jaWVzKCkge1xuICAgICAgICByZXR1cm4gWydzeXN0ZW0udWkub3ZlcmxheSddO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlZmluZXMgbGlzdGVuZXJzIGZvciBldmVudHMuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgICAqL1xuICAgIGxpc3RlbnMoKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICByZWFkeTogJ3JlYWR5JyxcbiAgICAgICAgICAgICdvdmVybGF5LmNsaWNrZWQnOiAnaGlkZUluc3BlY3RvcicsXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVhZHkgZXZlbnQgaGFuZGxlci5cbiAgICAgKi9cbiAgICByZWFkeSgpIHtcbiAgICAgICAgdGhpcy5iaW5kSW5zcGVjdGFibGVFbGVtZW50cygpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERlc3RydWN0b3IuXG4gICAgICpcbiAgICAgKiBGaXJlZCB3aGVuIHRoaXMgcGx1Z2luIGlzIHJlbW92ZWQuXG4gICAgICovXG4gICAgZGVzdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy51bmJpbmRJbnNwZWN0YWJsZUVsZW1lbnRzKCk7XG4gICAgICAgIHRoaXMuaW5zcGVjdGFibGVFbGVtZW50cyA9IFtdO1xuXG4gICAgICAgIHN1cGVyLmRlc3RydWN0b3IoKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZWFyY2hlcyBmb3IsIGFuZCBiaW5kcyBhbiBldmVudCB0bywgaW5zcGVjdGFibGUgZWxlbWVudHMuXG4gICAgICovXG4gICAgYmluZEluc3BlY3RhYmxlRWxlbWVudHMoKSB7XG4gICAgICAgIHdpbmRvdy5kb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCdbZGF0YS1pbnNwZWN0YWJsZV0nKS5mb3JFYWNoKChlbGVtZW50KSA9PiB7XG4gICAgICAgICAgICBjb25zdCBpbnNwZWN0b3JEYXRhID0ge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQsXG4gICAgICAgICAgICAgICAgZm9ybTogdGhpcy5maW5kRm9ybShlbGVtZW50KSxcbiAgICAgICAgICAgICAgICBpbnNwZWN0b3JFbGVtZW50OiBudWxsLFxuICAgICAgICAgICAgICAgIGluc3BlY3RvclZ1ZTogbnVsbCxcbiAgICAgICAgICAgICAgICBjb250YWluZXI6IHRoaXMuZmluZEluc3BlY3RhYmxlQ29udGFpbmVyKGVsZW1lbnQpLFxuICAgICAgICAgICAgICAgIGhhbmRsZXI6IChldmVudCkgPT4gdGhpcy5pbnNwZWN0YWJsZUNsaWNrLmNhbGwodGhpcywgZXZlbnQsIGluc3BlY3RvckRhdGEpLFxuICAgICAgICAgICAgICAgIHRpdGxlOiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yVGl0bGUgfHwgJ0luc3BlY3RvcicsXG4gICAgICAgICAgICAgICAgZGVzY3JpcHRpb246IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JEZXNjcmlwdGlvbiB8fCBudWxsLFxuICAgICAgICAgICAgICAgIGNvbmZpZzogZWxlbWVudC5kYXRhc2V0Lmluc3BlY3RvckNvbmZpZyB8fCBudWxsLFxuICAgICAgICAgICAgICAgIG9mZnNldDoge1xuICAgICAgICAgICAgICAgICAgICB4OiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yT2Zmc2V0WCB8fCBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yT2Zmc2V0IHx8IDAsXG4gICAgICAgICAgICAgICAgICAgIHk6IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JPZmZzZXRZIHx8IGVsZW1lbnQuZGF0YXNldC5pbnNwZWN0b3JPZmZzZXQgfHwgMCxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHBsYWNlbWVudDogZWxlbWVudC5kYXRhc2V0Lmluc3BlY3RvclBsYWNlbWVudCB8fCBudWxsLFxuICAgICAgICAgICAgICAgIGZhbGxiYWNrUGxhY2VtZW50OiBlbGVtZW50LmRhdGFzZXQuaW5zcGVjdG9yRmFsbGJhY2tQbGFjZW1lbnQgfHwgJ2JvdHRvbScsXG4gICAgICAgICAgICAgICAgY3NzQ2xhc3NlczogZWxlbWVudC5kYXRhc2V0Lmluc3BlY3RvckNzc0NsYXNzIHx8IG51bGwsXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICB0aGlzLmluc3BlY3RhYmxlRWxlbWVudHMucHVzaChpbnNwZWN0b3JEYXRhKTtcbiAgICAgICAgICAgIGVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBpbnNwZWN0b3JEYXRhLmhhbmRsZXIpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBVbmJpbmRzIGFsbCBpbnNwZWN0YWJsZSBlbGVtZW50cy5cbiAgICAgKi9cbiAgICB1bmJpbmRJbnNwZWN0YWJsZUVsZW1lbnRzKCkge1xuICAgIH1cblxuICAgIGNyZWF0ZUluc3BlY3RvcihpbnNwZWN0b3IpIHtcbiAgICAgICAgLy8gQ3JlYXRlIGEgbmV3IGluc3BlY3RvciA8ZGl2PiB0byBob3VzZSB0aGUgVnVlIGluc3RhbmNlXG4gICAgICAgIGluc3BlY3Rvci5pbnNwZWN0b3JFbGVtZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgICAgIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQoaW5zcGVjdG9yLmluc3BlY3RvckVsZW1lbnQpO1xuICAgICAgICB0aGlzLmN1cnJlbnRJbnNwZWN0b3IgPSBpbnNwZWN0b3I7XG5cbiAgICAgICAgLy8gQ3JlYXRlIFZ1ZSBpbnN0YW5jZSBhbmQgbW91bnQgaXQgdG8gdGhlIGFib3ZlIDxkaXY+XG4gICAgICAgIGluc3BlY3Rvci5pbnNwZWN0b3JWdWUgPSBjcmVhdGVBcHAoSW5zcGVjdG9yLCB7XG4gICAgICAgICAgICBzbm93Ym9hcmQ6IHRoaXMuc25vd2JvYXJkLFxuICAgICAgICAgICAgaW5zcGVjdGVkRWxlbWVudDogaW5zcGVjdG9yLmVsZW1lbnQsXG4gICAgICAgICAgICBmb3JtOiBpbnNwZWN0b3IuZm9ybSxcbiAgICAgICAgICAgIHRpdGxlOiBpbnNwZWN0b3IudGl0bGUsXG4gICAgICAgICAgICBkZXNjcmlwdGlvbjogaW5zcGVjdG9yLmRlc2NyaXB0aW9uLFxuICAgICAgICAgICAgcGxhY2VtZW50OiBpbnNwZWN0b3IucGxhY2VtZW50LFxuICAgICAgICAgICAgZmFsbGJhY2tQbGFjZW1lbnQ6IGluc3BlY3Rvci5mYWxsYmFja1BsYWNlbWVudCxcbiAgICAgICAgICAgIG9mZnNldFg6IGluc3BlY3Rvci5vZmZzZXQueCxcbiAgICAgICAgICAgIG9mZnNldFk6IGluc3BlY3Rvci5vZmZzZXQueSxcbiAgICAgICAgICAgIGhpZGVGbjogKCkgPT4gdGhpcy5oaWRlSW5zcGVjdG9yKCksXG4gICAgICAgICAgICBjb25maWc6IGluc3BlY3Rvci5jb25maWcsXG4gICAgICAgIH0pO1xuICAgICAgICBpbnNwZWN0b3IuaW5zcGVjdG9yVnVlLm1vdW50KGluc3BlY3Rvci5pbnNwZWN0b3JFbGVtZW50KTtcbiAgICB9XG5cbiAgICBoaWRlSW5zcGVjdG9yKCkge1xuICAgICAgICBpZiAoIXRoaXMuY3VycmVudEluc3BlY3Rvcikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5jdXJyZW50SW5zcGVjdG9yLmluc3BlY3RvclZ1ZS51bm1vdW50KCk7XG4gICAgICAgIGRvY3VtZW50LmJvZHkucmVtb3ZlQ2hpbGQodGhpcy5jdXJyZW50SW5zcGVjdG9yLmluc3BlY3RvckVsZW1lbnQpO1xuICAgICAgICB0aGlzLmN1cnJlbnRJbnNwZWN0b3IgPSBudWxsO1xuICAgIH1cblxuICAgIGluc3BlY3RhYmxlQ2xpY2soZXZlbnQsIGluc3BlY3Rvcikge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGlmIChpbnNwZWN0b3IgPT09IHRoaXMuY3VycmVudEluc3BlY3Rvcikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5jcmVhdGVJbnNwZWN0b3IoaW5zcGVjdG9yKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBTZWFyY2hlcyB1cCB0aGUgaGllcmFyY2h5IGZvciBhIGNvbnRhaW5lciBmb3IgSW5zcGVjdGFibGUgZWxlbWVudHMuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50XG4gICAgICogQHJldHVybnMge0hUTUxFbGVtZW50fG51bGx9XG4gICAgICovXG4gICAgZmluZEluc3BlY3RhYmxlQ29udGFpbmVyKGVsZW1lbnQpIHtcbiAgICAgICAgbGV0IGN1cnJlbnRFbGVtZW50ID0gZWxlbWVudDtcblxuICAgICAgICB3aGlsZSAoY3VycmVudEVsZW1lbnQucGFyZW50RWxlbWVudCAmJiBjdXJyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50LnRhZ05hbWUgIT09ICdIVE1MJykge1xuICAgICAgICAgICAgaWYgKGN1cnJlbnRFbGVtZW50Lm1hdGNoZXMoJ1tkYXRhLWluc3BlY3Rvci1jb250YWluZXJdJykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gY3VycmVudEVsZW1lbnQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGN1cnJlbnRFbGVtZW50ID0gY3VycmVudEVsZW1lbnQucGFyZW50RWxlbWVudDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEZpbmRzIHRoZSBmb3JtIHRoYXQgdGhlIGVsZW1lbnQgYmVsb25ncyB0by5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsZW1lbnRcbiAgICAgKiBAcmV0dXJucyB7SFRNTEVsZW1lbnR8dW5kZWZpbmVkfVxuICAgICAqL1xuICAgIGZpbmRGb3JtKGVsZW1lbnQpIHtcbiAgICAgICAgcmV0dXJuIGVsZW1lbnQuY2xvc2VzdCgnZm9ybScpO1xuICAgIH1cbn1cbiIsIjx0ZW1wbGF0ZT5cbiAgICA8ZGl2IGNsYXNzPVwiZmllbGRcIj5cbiAgICAgICAgPHNsb3Q+PC9zbG90PlxuICAgIDwvZGl2PlxuPC90ZW1wbGF0ZT5cblxuPHNjcmlwdD5cbmV4cG9ydCBkZWZhdWx0IHtcbn07XG48L3NjcmlwdD5cbiIsIjx0ZW1wbGF0ZT5cbiAgICA8ZGl2XG4gICAgICAgIHYtaWY9XCJsYWJlbFwiXG4gICAgICAgIGNsYXNzPVwiZmllbGQtbGFiZWxcIlxuICAgID5cbiAgICAgICAgPHNwYW5cbiAgICAgICAgICAgIGNsYXNzPVwibGFiZWxcIlxuICAgICAgICAgICAgdi10ZXh0PVwibGFiZWxcIlxuICAgICAgICAvPlxuICAgIDwvZGl2PlxuPC90ZW1wbGF0ZT5cblxuPHNjcmlwdD5cbmV4cG9ydCBkZWZhdWx0IHtcbiAgICBwcm9wczoge1xuICAgICAgICBsYWJlbDoge1xuICAgICAgICAgICAgdHlwZTogW1N0cmluZywgQm9vbGVhbl0sXG4gICAgICAgICAgICBkZWZhdWx0OiBmYWxzZSxcbiAgICAgICAgfSxcbiAgICB9LFxufTtcbjwvc2NyaXB0PlxuIiwiPHRlbXBsYXRlPlxuICAgIDxkaXZcbiAgICAgICAgcmVmPVwicG9wb3ZlclwiXG4gICAgICAgIGNsYXNzPVwiaW5zcGVjdG9yLXdyYXBwZXJcIlxuICAgID5cbiAgICAgICAgPHRyYW5zaXRpb24gbmFtZT1cInBvcG92ZXItZmFkZVwiPlxuICAgICAgICAgICAgPGRpdlxuICAgICAgICAgICAgICAgIHYtaWY9XCJzaG93blwiXG4gICAgICAgICAgICAgICAgY2xhc3M9XCJpbnNwZWN0b3IgcG9wb3Zlci1sYXlvdXRcIlxuICAgICAgICAgICAgPlxuICAgICAgICAgICAgICAgIDxkaXZcbiAgICAgICAgICAgICAgICAgICAgY2xhc3M9XCJhcnJvd1wiXG4gICAgICAgICAgICAgICAgICAgIGRhdGEtcG9wcGVyLWFycm93XG4gICAgICAgICAgICAgICAgPjwvZGl2PlxuICAgICAgICAgICAgICAgIDxoZWFkZXI+XG4gICAgICAgICAgICAgICAgICAgIDxzbG90IG5hbWU9XCJ0aXRsZVwiPjwvc2xvdD5cbiAgICAgICAgICAgICAgICAgICAgPHNsb3QgbmFtZT1cImRlc2NyaXB0aW9uXCI+PC9zbG90PlxuICAgICAgICAgICAgICAgICAgICA8ZGl2XG4gICAgICAgICAgICAgICAgICAgICAgICBjbGFzcz1cImluc3BlY3Rvci1oaWRlIHduLWljb24tcmVtb3ZlXCJcbiAgICAgICAgICAgICAgICAgICAgICAgIEBjbGljay5zdG9wPVwiaGlkZUZuXCJcbiAgICAgICAgICAgICAgICAgICAgPjwvZGl2PlxuICAgICAgICAgICAgICAgIDwvaGVhZGVyPlxuICAgICAgICAgICAgICAgIDxtYWluPlxuICAgICAgICAgICAgICAgICAgICA8c2xvdCBuYW1lPVwiZmllbGRzXCI+PC9zbG90PlxuICAgICAgICAgICAgICAgIDwvbWFpbj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICA8L3RyYW5zaXRpb24+XG4gICAgPC9kaXY+XG48L3RlbXBsYXRlPlxuXG48c2NyaXB0PlxuaW1wb3J0IHsgY3JlYXRlUG9wcGVyIH0gZnJvbSAnQHBvcHBlcmpzL2NvcmUvbGliL3BvcHBlci1saXRlJztcbmltcG9ydCBhcnJvdyBmcm9tICdAcG9wcGVyanMvY29yZS9saWIvbW9kaWZpZXJzL2Fycm93JztcbmltcG9ydCBmbGlwIGZyb20gJ0Bwb3BwZXJqcy9jb3JlL2xpYi9tb2RpZmllcnMvZmxpcCc7XG5pbXBvcnQgcHJldmVudE92ZXJmbG93IGZyb20gJ0Bwb3BwZXJqcy9jb3JlL2xpYi9tb2RpZmllcnMvcHJldmVudE92ZXJmbG93JztcbmltcG9ydCBvZmZzZXQgZnJvbSAnQHBvcHBlcmpzL2NvcmUvbGliL21vZGlmaWVycy9vZmZzZXQnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gICAgcHJvcHM6IHtcbiAgICAgICAgc2hvd246IHtcbiAgICAgICAgICAgIHR5cGU6IEJvb2xlYW4sXG4gICAgICAgICAgICBkZWZhdWx0OiBmYWxzZSxcbiAgICAgICAgfSxcbiAgICAgICAgc25vd2JvYXJkOiB7XG4gICAgICAgICAgICB0eXBlOiBPYmplY3QsXG4gICAgICAgICAgICByZXF1aXJlZDogdHJ1ZSxcbiAgICAgICAgfSxcbiAgICAgICAgaW5zcGVjdGVkRWxlbWVudDoge1xuICAgICAgICAgICAgdHlwZTogSFRNTEVsZW1lbnQsXG4gICAgICAgICAgICByZXF1aXJlZDogdHJ1ZSxcbiAgICAgICAgfSxcbiAgICAgICAgaGlkZUZuOiB7XG4gICAgICAgICAgICB0eXBlOiBGdW5jdGlvbixcbiAgICAgICAgICAgIHJlcXVpcmVkOiB0cnVlLFxuICAgICAgICB9LFxuICAgICAgICBwbGFjZW1lbnQ6IHtcbiAgICAgICAgICAgIHR5cGU6IFN0cmluZyxcbiAgICAgICAgICAgIGRlZmF1bHQ6ICdhdXRvJyxcbiAgICAgICAgICAgIHZhbGlkYXRlKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICAgICAgJ2F1dG8nLFxuICAgICAgICAgICAgICAgICAgICAndG9wJyxcbiAgICAgICAgICAgICAgICAgICAgJ3JpZ2h0JyxcbiAgICAgICAgICAgICAgICAgICAgJ2JvdHRvbScsXG4gICAgICAgICAgICAgICAgICAgICdsZWZ0JyxcbiAgICAgICAgICAgICAgICBdLmluZGV4T2YodmFsdWUpICE9PSAtMTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0sXG4gICAgICAgIGZhbGxiYWNrUGxhY2VtZW50OiB7XG4gICAgICAgICAgICB0eXBlOiBTdHJpbmcsXG4gICAgICAgICAgICBkZWZhdWx0OiAnYm90dG9tJyxcbiAgICAgICAgICAgIHZhbGlkYXRlKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICAgICAgJ3RvcCcsXG4gICAgICAgICAgICAgICAgICAgICdyaWdodCcsXG4gICAgICAgICAgICAgICAgICAgICdib3R0b20nLFxuICAgICAgICAgICAgICAgICAgICAnbGVmdCcsXG4gICAgICAgICAgICAgICAgXS5pbmRleE9mKHZhbHVlKSAhPT0gLTE7XG4gICAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgICBvZmZzZXRYOiB7XG4gICAgICAgICAgICB0eXBlOiBOdW1iZXIsXG4gICAgICAgICAgICBkZWZhdWx0OiAwLFxuICAgICAgICB9LFxuICAgICAgICBvZmZzZXRZOiB7XG4gICAgICAgICAgICB0eXBlOiBOdW1iZXIsXG4gICAgICAgICAgICBkZWZhdWx0OiAwLFxuICAgICAgICB9LFxuICAgIH0sXG4gICAgZGF0YSgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHRvcDogMCxcbiAgICAgICAgICAgIGxlZnQ6IDAsXG4gICAgICAgICAgICBpbnNwZWN0ZWRFbGVtZW50U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBwb3NpdGlvbjogbnVsbCxcbiAgICAgICAgICAgICAgICB6SW5kZXg6IG51bGwsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgcG9wcGVySW5zdGFuY2U6IG51bGwsXG4gICAgICAgIH07XG4gICAgfSxcbiAgICB3YXRjaDoge1xuICAgICAgICAvKipcbiAgICAgICAgICogRGV0ZWN0cyBpZiB0aGUgcG9wb3ZlciBpcyBzaG93bi4gSWYgc2hvd24sIHNob3cgdGhlIG92ZXJsYXkgYW5kIGNyZWF0ZSB0aGUgcG9wb3Zlci5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtCb29sZWFufSBpc1Nob3duXG4gICAgICAgICAqL1xuICAgICAgICBzaG93bihpc1Nob3duKSB7XG4gICAgICAgICAgICBpZiAoaXNTaG93bikge1xuICAgICAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLm92ZXJsYXkoKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWdobGlnaHRJbnNwZWN0ZWRFbGVtZW50KCk7XG4gICAgICAgICAgICAgICAgdGhpcy4kbmV4dFRpY2soKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmNyZWF0ZVBvcHBlcigpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLnBvcHBlckluc3RhbmNlLmRlc3Ryb3koKTtcbiAgICAgICAgICAgICAgICB0aGlzLnNub3dib2FyZC5vdmVybGF5KCkuaGlkZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgIH0sXG4gICAgdW5tb3VudGVkKCkge1xuICAgICAgICB0aGlzLnNub3dib2FyZC5vdmVybGF5KCkuaGlkZSgpO1xuICAgIH0sXG4gICAgbWV0aG9kczoge1xuICAgICAgICBjcmVhdGVQb3BwZXIoKSB7XG4gICAgICAgICAgICB0aGlzLnBvcHBlckluc3RhbmNlID0gY3JlYXRlUG9wcGVyKHRoaXMuaW5zcGVjdGVkRWxlbWVudCwgdGhpcy4kcmVmcy5wb3BvdmVyLCB7XG4gICAgICAgICAgICAgICAgbW9kaWZpZXJzOiBbXG4gICAgICAgICAgICAgICAgICAgIGFycm93LFxuICAgICAgICAgICAgICAgICAgICBmbGlwLFxuICAgICAgICAgICAgICAgICAgICBwcmV2ZW50T3ZlcmZsb3csXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC4uLm9mZnNldCxcbiAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvZmZzZXQ6IFt0aGlzLm9mZnNldFgsIHRoaXMub2Zmc2V0WSArIDEwXSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9LFxuICAgICAgICBoaWdobGlnaHRJbnNwZWN0ZWRFbGVtZW50KCkge1xuICAgICAgICAgICAgdGhpcy5pbnNwZWN0ZWRFbGVtZW50U3R5bGUucG9zaXRpb24gPSB0aGlzLmluc3BlY3RlZEVsZW1lbnQuc3R5bGUucG9zaXRpb247XG4gICAgICAgICAgICB0aGlzLmluc3BlY3RlZEVsZW1lbnRTdHlsZS56SW5kZXggPSB0aGlzLmluc3BlY3RlZEVsZW1lbnQuc3R5bGUuekluZGV4O1xuXG4gICAgICAgICAgICB0aGlzLmluc3BlY3RlZEVsZW1lbnQuc3R5bGUucG9zaXRpb24gPSAncmVsYXRpdmUnO1xuICAgICAgICAgICAgdGhpcy5pbnNwZWN0ZWRFbGVtZW50LnN0eWxlLnpJbmRleCA9IDEwMDE7XG4gICAgICAgIH0sXG4gICAgfSxcbn07XG48L3NjcmlwdD5cblxuPHN0eWxlIGxhbmc9XCJsZXNzXCI+XG5AaW1wb3J0IChyZWZlcmVuY2UpICcuLi8uLi8uLi9sZXNzL2dsb2JhbC5sZXNzJztcbkBpbXBvcnQgKHJlZmVyZW5jZSkgJy4uL3N0eWxlL3ZhcmlhYmxlcy5sZXNzJztcblxuLy8gVkFSSUFCTEVTXG5AaW5wc2VjdG9yLXBvcG92ZXItd2lkdGg6IDM2MHB4O1xuQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzOiBAYm9yZGVyLXJhZGl1cy1iYXNlO1xuXG4vLyBTVFlMSU5HXG4uaW5zcGVjdG9yLXdyYXBwZXIge1xuICAgIHotaW5kZXg6IDEwMDI7XG59XG5cbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQge1xuICAgIHBvc2l0aW9uOiByZWxhdGl2ZTtcbiAgICB3aWR0aDogQGlucHNlY3Rvci1wb3BvdmVyLXdpZHRoO1xuICAgIGJveC1zaGFkb3c6IEBvdmVybGF5LWJveC1zaGFkb3c7XG4gICAgYm9yZGVyLXJhZGl1czogQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzO1xuICAgIGZvbnQtc2l6ZTogQGluc3BlY3Rvci1mb250LXNpemU7XG5cbiAgICAuYXJyb3csXG4gICAgLmFycm93OjpiZWZvcmUge1xuICAgICAgICBwb3NpdGlvbjogYWJzb2x1dGU7XG4gICAgICAgIHdpZHRoOiAxMHB4O1xuICAgICAgICBoZWlnaHQ6IDEwcHg7XG4gICAgfVxuXG4gICAgLmFycm93IHtcbiAgICAgICAgdmlzaWJpbGl0eTogaGlkZGVuO1xuICAgICAgICB6LWluZGV4OiAxMDAyO1xuICAgIH1cblxuICAgIC5hcnJvdzo6YmVmb3JlIHtcbiAgICAgICAgdmlzaWJpbGl0eTogdmlzaWJsZTtcbiAgICAgICAgY29udGVudDogJyc7XG4gICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDQ1ZGVnKTtcbiAgICAgICAgYmFja2dyb3VuZC1jb2xvcjogQGluc3BlY3Rvci1oZWFkZXItYmc7XG4gICAgfVxuXG4gICAgaGVhZGVyIHtcbiAgICAgICAgcG9zaXRpb246IHJlbGF0aXZlO1xuICAgICAgICBwYWRkaW5nOiBAcGFkZGluZy1sYXJnZS12ZXJ0aWNhbCAoQHBhZGRpbmctbGFyZ2UtaG9yaXpvbnRhbCArIDMwcHgpIEBwYWRkaW5nLWxhcmdlLXZlcnRpY2FsIEBwYWRkaW5nLWxhcmdlLWhvcml6b250YWw7XG4gICAgICAgIGJhY2tncm91bmQ6IEBpbnNwZWN0b3ItaGVhZGVyLWJnO1xuICAgICAgICBjb2xvcjogQGluc3BlY3Rvci1oZWFkZXItZmc7XG4gICAgICAgIGJvcmRlci1ib3R0b206IDFweCBzb2xpZCBkYXJrZW4oQGluc3BlY3Rvci1oZWFkZXItYmcsIDglKTtcbiAgICAgICAgYm9yZGVyLXRvcC1sZWZ0LXJhZGl1czogQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzO1xuICAgICAgICBib3JkZXItdG9wLXJpZ2h0LXJhZGl1czogQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzO1xuICAgICAgICB0ZXh0LXNoYWRvdzogMHB4IDFweCAxcHggcmdiYSgwLCAwLCAwLCAwLjIyKTtcbiAgICAgICAgei1pbmRleDogMTAwMztcblxuICAgICAgICAuaW5zcGVjdG9yLXRpdGxlIHtcbiAgICAgICAgICAgIGZvbnQtd2VpZ2h0OiBAaW5zcGVjdG9yLWhlYWRlci10aXRsZS13ZWlnaHQ7XG4gICAgICAgICAgICBmb250LXNpemU6IEBpbnNwZWN0b3ItaGVhZGVyLXRpdGxlLXNpemU7XG4gICAgICAgIH1cblxuICAgICAgICAuaW5zcGVjdG9yLWRlc2NyaXB0aW9uIHtcbiAgICAgICAgICAgIGZvbnQtd2VpZ2h0OiBAaW5zcGVjdG9yLWhlYWRlci1kZXNjcmlwdGlvbi13ZWlnaHQ7XG4gICAgICAgICAgICBmb250LXNpemU6IEBpbnNwZWN0b3ItaGVhZGVyLWRlc2NyaXB0aW9uLXNpemU7XG4gICAgICAgICAgICBtYXJnaW4tdG9wOiAtM3B4O1xuICAgICAgICB9XG5cbiAgICAgICAgLmluc3BlY3Rvci1oaWRlIHtcbiAgICAgICAgICAgIHBvc2l0aW9uOiBhYnNvbHV0ZTtcbiAgICAgICAgICAgIHRvcDogMDtcbiAgICAgICAgICAgIHJpZ2h0OiAwO1xuICAgICAgICAgICAgd2lkdGg6IDMwcHg7XG4gICAgICAgICAgICBoZWlnaHQ6IDMwcHg7XG4gICAgICAgICAgICBsaW5lLWhlaWdodDogMzBweDtcbiAgICAgICAgICAgIHRleHQtYWxpZ246IGNlbnRlcjtcbiAgICAgICAgICAgIGJhY2tncm91bmQ6IGRhcmtlbihAaW5zcGVjdG9yLWhlYWRlci1iZywgOCUpO1xuICAgICAgICAgICAgYm9yZGVyLXRvcC1yaWdodC1yYWRpdXM6IEBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1cztcbiAgICAgICAgICAgIGJvcmRlci1ib3R0b20tbGVmdC1yYWRpdXM6IEBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1cztcbiAgICAgICAgICAgIGN1cnNvcjogcG9pbnRlcjtcbiAgICAgICAgICAgIHRyYW5zaXRpb246IGJhY2tncm91bmQtY29sb3IgMTc1bXMgZWFzZTtcblxuICAgICAgICAgICAgJjo6YmVmb3JlIHtcbiAgICAgICAgICAgICAgICB0cmFuc2l0aW9uOiBvcGFjaXR5IDE3NW1zIGVhc2U7XG4gICAgICAgICAgICAgICAgb3BhY2l0eTogMC43O1xuICAgICAgICAgICAgICAgIG1hcmdpbi1yaWdodDogMDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJjpob3ZlciB7XG4gICAgICAgICAgICAgICAgYmFja2dyb3VuZDogZGFya2VuKEBpbnNwZWN0b3ItaGVhZGVyLWJnLCAxMiUpO1xuXG4gICAgICAgICAgICAgICAgJjo6YmVmb3JlIHtcbiAgICAgICAgICAgICAgICAgICAgb3BhY2l0eTogMTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBtYWluIHtcbiAgICAgICAgcG9zaXRpb246IHJlbGF0aXZlO1xuICAgICAgICBiYWNrZ3JvdW5kOiBAaW5zcGVjdG9yLWJnO1xuICAgICAgICBib3JkZXItYm90dG9tLWxlZnQtcmFkaXVzOiBAaW5zcGVjdG9yLWJvcmRlci1yYWRpdXM7XG4gICAgICAgIGJvcmRlci1ib3R0b20tcmlnaHQtcmFkaXVzOiBAaW5zcGVjdG9yLWJvcmRlci1yYWRpdXM7XG4gICAgICAgIHotaW5kZXg6IDEwMDM7XG5cbiAgICAgICAgLmZpZWxkIHtcbiAgICAgICAgICAgIGRpc3BsYXk6IGZsZXg7XG4gICAgICAgICAgICBmbGV4LWRpcmVjdGlvbjogcm93O1xuICAgICAgICAgICAganVzdGlmeS1jb250ZW50OiBzdHJldGNoO1xuXG4gICAgICAgICAgICAuZmllbGQtbGFiZWwge1xuICAgICAgICAgICAgICAgIHBhZGRpbmc6IEBwYWRkaW5nLXNtYWxsLXZlcnRpY2FsIEBwYWRkaW5nLXNtYWxsLWhvcml6b250YWw7XG4gICAgICAgICAgICAgICAgZmxleDogMyAwO1xuICAgICAgICAgICAgICAgIGJhY2tncm91bmQ6IEBpbnNwZWN0b3ItZmllbGQtbGFiZWwtYmc7XG4gICAgICAgICAgICAgICAgY29sb3I6IEBpbnNwZWN0b3ItZmllbGQtbGFiZWwtZmc7XG4gICAgICAgICAgICAgICAgYm9yZGVyLXJpZ2h0OiAxcHggc29saWQgQGluc3BlY3Rvci1maWVsZC1ib3JkZXI7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC5maWVsZC1jb250cm9sIHtcbiAgICAgICAgICAgICAgICBiYWNrZ3JvdW5kOiBAaW5zcGVjdG9yLWZpZWxkLWJnO1xuICAgICAgICAgICAgICAgIGZsZXg6IDUgMDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC5maWVsZCArIC5maWVsZCB7XG4gICAgICAgICAgICBib3JkZXItdG9wOiAxcHggc29saWQgQGluc3BlY3Rvci1maWVsZC1ib3JkZXI7XG4gICAgICAgIH1cbiAgICB9XG59XG5cbi8vIEFSUk9XIFBMQUNFTUVOVFxuLmluc3BlY3Rvci13cmFwcGVyW2RhdGEtcG9wcGVyLXBsYWNlbWVudF49J3RvcCddIC5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgLmFycm93IHtcbiAgICBib3R0b206IC01cHg7XG59XG4uaW5zcGVjdG9yLXdyYXBwZXJbZGF0YS1wb3BwZXItcGxhY2VtZW50Xj0nYm90dG9tJ10gLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCAuYXJyb3cge1xuICAgIHRvcDogLTVweDtcbn1cbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSdsZWZ0J10gLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCAuYXJyb3cge1xuICAgIHJpZ2h0OiAtNXB4O1xufVxuLmluc3BlY3Rvci13cmFwcGVyW2RhdGEtcG9wcGVyLXBsYWNlbWVudF49J3JpZ2h0J10gLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCAuYXJyb3cge1xuICAgIGxlZnQ6IC01cHg7XG59XG5cbi8vIFRSQU5TSVRJT05TXG4ucG9wb3Zlci1mYWRlLWVudGVyLWFjdGl2ZSxcbi5wb3BvdmVyLWZhZGUtbGVhdmUtYWN0aXZlIHtcbiAgICB0cmFuc2l0aW9uOiBvcGFjaXR5IDE3NW1zIGVhc2Utb3V0LFxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybSAxNzVtcyBlYXNlLW91dDtcbn1cblxuLnBvcG92ZXItZmFkZS1lbnRlci1mcm9tLFxuLnBvcG92ZXItZmFkZS1sZWF2ZS10byB7XG4gICAgb3BhY2l0eTogMDtcbiAgICB0cmFuc2Zvcm06IHRyYW5zbGF0ZVkoMjBweCk7XG59XG48L3N0eWxlPlxuIiwiPHRlbXBsYXRlPlxuICAgIDxjb21wb25lbnRcbiAgICAgICAgOmlzPVwibGF5b3V0Q29tcG9uZW50XCJcbiAgICAgICAgdi1iaW5kPVwibGF5b3V0UHJvcHNcIlxuICAgID5cbiAgICAgICAgPHRlbXBsYXRlICN0aXRsZT5cbiAgICAgICAgICAgIDxkaXZcbiAgICAgICAgICAgICAgICB2LWlmPVwiaW5zcGVjdG9yVGl0bGVcIlxuICAgICAgICAgICAgICAgIGNsYXNzPVwiaW5zcGVjdG9yLXRpdGxlXCJcbiAgICAgICAgICAgICAgICB2LXRleHQ9XCJpbnNwZWN0b3JUaXRsZVwiXG4gICAgICAgICAgICA+PC9kaXY+XG4gICAgICAgIDwvdGVtcGxhdGU+XG4gICAgICAgIDx0ZW1wbGF0ZSAjZGVzY3JpcHRpb24+XG4gICAgICAgICAgICA8ZGl2XG4gICAgICAgICAgICAgICAgdi1pZj1cImluc3BlY3RvckRlc2NyaXB0aW9uXCJcbiAgICAgICAgICAgICAgICBjbGFzcz1cImluc3BlY3Rvci1kZXNjcmlwdGlvblwiXG4gICAgICAgICAgICAgICAgdi10ZXh0PVwiaW5zcGVjdG9yRGVzY3JpcHRpb25cIlxuICAgICAgICAgICAgPjwvZGl2PlxuICAgICAgICA8L3RlbXBsYXRlPlxuICAgICAgICA8dGVtcGxhdGUgI2ZpZWxkcz5cbiAgICAgICAgICAgIDxGaWVsZFxuICAgICAgICAgICAgICAgIHYtZm9yPVwiKGZpZWxkLCBpKSBpbiBpbnNwZWN0b3JGaWVsZHNcIlxuICAgICAgICAgICAgICAgIDprZXk9XCJpXCJcbiAgICAgICAgICAgID5cbiAgICAgICAgICAgICAgICA8RmllbGRMYWJlbFxuICAgICAgICAgICAgICAgICAgICA6bGFiZWw9XCJmaWVsZC5sYWJlbFwiXG4gICAgICAgICAgICAgICAgLz5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZmllbGQtY29udHJvbFwiIC8+XG4gICAgICAgICAgICA8L0ZpZWxkPlxuICAgICAgICA8L3RlbXBsYXRlPlxuICAgIDwvY29tcG9uZW50PlxuPC90ZW1wbGF0ZT5cblxuPHNjcmlwdD5cbmltcG9ydCBQb3BvdmVyTGF5b3V0IGZyb20gJy4uL2xheW91dC9Qb3BvdmVyLnZ1ZSc7XG5pbXBvcnQgRmllbGQgZnJvbSAnLi4vZmllbGRzL0ZpZWxkLnZ1ZSc7XG5pbXBvcnQgRmllbGRMYWJlbCBmcm9tICcuLi9maWVsZHMvRmllbGRMYWJlbC52dWUnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gICAgY29tcG9uZW50czoge1xuICAgICAgICBGaWVsZCxcbiAgICAgICAgRmllbGRMYWJlbCxcbiAgICB9LFxuICAgIHByb3BzOiB7XG4gICAgICAgIHNub3dib2FyZDoge1xuICAgICAgICAgICAgdHlwZTogT2JqZWN0LFxuICAgICAgICAgICAgcmVxdWlyZWQ6IHRydWUsXG4gICAgICAgIH0sXG4gICAgICAgIGluc3BlY3RlZEVsZW1lbnQ6IHtcbiAgICAgICAgICAgIHR5cGU6IEhUTUxFbGVtZW50LFxuICAgICAgICAgICAgcmVxdWlyZWQ6IHRydWUsXG4gICAgICAgIH0sXG4gICAgICAgIGZvcm06IHtcbiAgICAgICAgICAgIHR5cGU6IEhUTUxFbGVtZW50LFxuICAgICAgICAgICAgZGVmYXVsdDogbnVsbCxcbiAgICAgICAgfSxcbiAgICAgICAgaGlkZUZuOiB7XG4gICAgICAgICAgICB0eXBlOiBGdW5jdGlvbixcbiAgICAgICAgICAgIHJlcXVpcmVkOiB0cnVlLFxuICAgICAgICB9LFxuICAgICAgICBjb25maWc6IHtcbiAgICAgICAgICAgIHR5cGU6IFtTdHJpbmcsIE9iamVjdF0sXG4gICAgICAgICAgICBkZWZhdWx0OiBudWxsLFxuICAgICAgICB9LFxuICAgICAgICB0aXRsZToge1xuICAgICAgICAgICAgdHlwZTogU3RyaW5nLFxuICAgICAgICAgICAgcmVxdWlyZWQ6IHRydWUsXG4gICAgICAgIH0sXG4gICAgICAgIGRlc2NyaXB0aW9uOiB7XG4gICAgICAgICAgICB0eXBlOiBTdHJpbmcsXG4gICAgICAgICAgICBkZWZhdWx0OiBudWxsLFxuICAgICAgICB9LFxuICAgICAgICBsYXlvdXQ6IHtcbiAgICAgICAgICAgIHR5cGU6IFN0cmluZyxcbiAgICAgICAgICAgIGRlZmF1bHQ6ICdwb3BvdmVyJyxcbiAgICAgICAgICAgIHZhbGlkYXRlKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICAgICAgJ3BvcG92ZXInLFxuICAgICAgICAgICAgICAgICAgICAnc2lkZWJhcicsXG4gICAgICAgICAgICAgICAgXS5pbmRleE9mKHZhbHVlKSAhPT0gLTE7XG4gICAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgICBwbGFjZW1lbnQ6IHtcbiAgICAgICAgICAgIHR5cGU6IFN0cmluZyxcbiAgICAgICAgICAgIGRlZmF1bHQ6ICdhdXRvJyxcbiAgICAgICAgICAgIHZhbGlkYXRlKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICAgICAgJ2F1dG8nLFxuICAgICAgICAgICAgICAgICAgICAndG9wJyxcbiAgICAgICAgICAgICAgICAgICAgJ3JpZ2h0JyxcbiAgICAgICAgICAgICAgICAgICAgJ2JvdHRvbScsXG4gICAgICAgICAgICAgICAgICAgICdsZWZ0JyxcbiAgICAgICAgICAgICAgICBdLmluZGV4T2YodmFsdWUpICE9PSAtMTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0sXG4gICAgICAgIGZhbGxiYWNrUGxhY2VtZW50OiB7XG4gICAgICAgICAgICB0eXBlOiBTdHJpbmcsXG4gICAgICAgICAgICBkZWZhdWx0OiAnYm90dG9tJyxcbiAgICAgICAgICAgIHZhbGlkYXRlKHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICAgICAgJ3RvcCcsXG4gICAgICAgICAgICAgICAgICAgICdyaWdodCcsXG4gICAgICAgICAgICAgICAgICAgICdib3R0b20nLFxuICAgICAgICAgICAgICAgICAgICAnbGVmdCcsXG4gICAgICAgICAgICAgICAgXS5pbmRleE9mKHZhbHVlKSAhPT0gLTE7XG4gICAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgICBvZmZzZXRYOiB7XG4gICAgICAgICAgICB0eXBlOiBOdW1iZXIsXG4gICAgICAgICAgICBkZWZhdWx0OiAwLFxuICAgICAgICB9LFxuICAgICAgICBvZmZzZXRZOiB7XG4gICAgICAgICAgICB0eXBlOiBOdW1iZXIsXG4gICAgICAgICAgICBkZWZhdWx0OiAwLFxuICAgICAgICB9LFxuICAgIH0sXG4gICAgZGF0YSgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHNob3dJbnNwZWN0b3I6IGZhbHNlLFxuICAgICAgICAgICAgdXNlckNvbmZpZzoge1xuICAgICAgICAgICAgICAgIHRpdGxlOiBudWxsLFxuICAgICAgICAgICAgICAgIGRlc2NyaXB0aW9uOiBudWxsLFxuICAgICAgICAgICAgICAgIGZpZWxkczogbnVsbCxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH07XG4gICAgfSxcbiAgICBjb21wdXRlZDoge1xuICAgICAgICBpbnNwZWN0b3JUaXRsZSgpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLnVzZXJDb25maWcudGl0bGUpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy51c2VyQ29uZmlnLnRpdGxlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gdGhpcy50aXRsZTtcbiAgICAgICAgfSxcbiAgICAgICAgaW5zcGVjdG9yRGVzY3JpcHRpb24oKSB7XG4gICAgICAgICAgICBpZiAodGhpcy51c2VyQ29uZmlnLmRlc2NyaXB0aW9uKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMudXNlckNvbmZpZy5kZXNjcmlwdGlvbjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHRoaXMuZGVzY3JpcHRpb247XG4gICAgICAgIH0sXG4gICAgICAgIGluc3BlY3RvckZpZWxkcygpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnVzZXJDb25maWcuZmllbGRzO1xuICAgICAgICB9LFxuICAgICAgICBsYXlvdXRQcm9wcygpIHtcbiAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgc2hvd246IHRoaXMuc2hvd0luc3BlY3RvcixcbiAgICAgICAgICAgICAgICBzbm93Ym9hcmQ6IHRoaXMuc25vd2JvYXJkLFxuICAgICAgICAgICAgICAgIGluc3BlY3RlZEVsZW1lbnQ6IHRoaXMuaW5zcGVjdGVkRWxlbWVudCxcbiAgICAgICAgICAgICAgICBoaWRlRm46IHRoaXMuaGlkZUZuLFxuICAgICAgICAgICAgICAgIHBsYWNlbWVudDogdGhpcy5wbGFjZW1lbnQsXG4gICAgICAgICAgICAgICAgZmFsbGJhY2tQbGFjZW1lbnQ6IHRoaXMuZmFsbGJhY2tQbGFjZW1lbnQsXG4gICAgICAgICAgICAgICAgb2Zmc2V0WDogdGhpcy5vZmZzZXRYLFxuICAgICAgICAgICAgICAgIG9mZnNldFk6IHRoaXMub2Zmc2V0WSxcbiAgICAgICAgICAgIH07XG4gICAgICAgIH0sXG4gICAgICAgIGxheW91dENvbXBvbmVudCgpIHtcbiAgICAgICAgICAgIHJldHVybiBQb3BvdmVyTGF5b3V0O1xuICAgICAgICB9LFxuICAgIH0sXG4gICAgbW91bnRlZCgpIHtcbiAgICAgICAgdGhpcy5nZXRDb25maWd1cmF0aW9uKCk7XG4gICAgfSxcbiAgICBtZXRob2RzOiB7XG4gICAgICAgIC8qKlxuICAgICAgICAgKiBHZXRzIHRoZSBjb25maWd1cmF0aW9uIG9mIHRoZSBJbnNwZWN0b3IuXG4gICAgICAgICAqXG4gICAgICAgICAqIElmIGEgY29uZmlnIGlzIGRlZmluZWQgbG9jYWxseSB2aWEgW2RhdGEtaW5zcGVjdG9yLWNvbmZpZ10sIHRoaXMgaXMgdXNlZCBhcyBhIGRlZmF1bHQuIFRoZVxuICAgICAgICAgKiBCYWNrZW5kIHdpbGwgYWx3YXlzIGJlIHF1ZXJpZWQgZm9yIGEgY29uZmlndXJhdGlvbiB0byBkZXRlcm1pbmUgaWYgYW55IG92ZXJyaWRlcyBuZWVkIHRvIGJlXG4gICAgICAgICAqIGFwcGxpZWQuXG4gICAgICAgICAqL1xuICAgICAgICBnZXRDb25maWd1cmF0aW9uKCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuY29uZmlnKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgdXNlckNvbmZpZyA9ICh0eXBlb2YgdGhpcy5jb25maWcgPT09ICdzdHJpbmcnKVxuICAgICAgICAgICAgICAgICAgICA/IEpTT04ucGFyc2UodGhpcy5jb25maWcpXG4gICAgICAgICAgICAgICAgICAgIDogdGhpcy5jb25maWc7XG5cbiAgICAgICAgICAgICAgICBpZiAodXNlckNvbmZpZy50aXRsZSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnVzZXJDb25maWcudGl0bGUgPSB1c2VyQ29uZmlnLnRpdGxlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiAodXNlckNvbmZpZy5kZXNjcmlwdGlvbikge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnVzZXJDb25maWcuZGVzY3JpcHRpb24gPSB1c2VyQ29uZmlnLmRlc2NyaXB0aW9uO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB0aGlzLnVzZXJDb25maWcuZmllbGRzID0gdGhpcy5wcm9jZXNzRmllbGRzQ29uZmlnKFxuICAgICAgICAgICAgICAgICAgICB1c2VyQ29uZmlnLmZpZWxkc1xuICAgICAgICAgICAgICAgICAgICB8fCB1c2VyQ29uZmlnLnByb3BlcnRpZXNcbiAgICAgICAgICAgICAgICAgICAgfHwge30sXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5nZXRDb25maWd1cmF0aW9uRnJvbUJhY2tlbmQoKTtcbiAgICAgICAgfSxcbiAgICAgICAgLyoqXG4gICAgICAgICAqIFF1ZXJpZXMgdGhlIGJhY2tlbmQgZm9yIHRoZSBmaW5hbCBJbnNwZWN0b3IgY29uZmlndXJhdGlvbi5cbiAgICAgICAgICovXG4gICAgICAgIGdldENvbmZpZ3VyYXRpb25Gcm9tQmFja2VuZCgpIHtcbiAgICAgICAgICAgIHRoaXMuc25vd2JvYXJkLnJlcXVlc3QodGhpcy5mb3JtLCAnb25HZXRJbnNwZWN0b3JDb25maWd1cmF0aW9uJywge1xuICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IChkYXRhKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkYXRhLmNvbmZpZ3VyYXRpb24udGl0bGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudXNlckNvbmZpZy50aXRsZSA9IGRhdGEuY29uZmlndXJhdGlvbi50aXRsZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAoZGF0YS5jb25maWd1cmF0aW9uLmRlc2NyaXB0aW9uKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnVzZXJDb25maWcuZGVzY3JpcHRpb24gPSBkYXRhLmNvbmZpZ3VyYXRpb24uZGVzY3JpcHRpb247XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaWYgKGRhdGEuY29uZmlndXJhdGlvbi5maWVsZHMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudXNlckNvbmZpZy5maWVsZHMgPSB0aGlzLnByb2Nlc3NGaWVsZHNDb25maWcoZGF0YS5jb25maWd1cmF0aW9uLmZpZWxkcyk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoZGF0YS5jb25maWd1cmF0aW9uLnByb3BlcnRpZXMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudXNlckNvbmZpZy5maWVsZHMgPSB0aGlzLnByb2Nlc3NGaWVsZHNDb25maWcoZGF0YS5jb25maWd1cmF0aW9uLnByb3BlcnRpZXMpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBjb21wbGV0ZTogKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3dJbnNwZWN0b3IgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSxcbiAgICAgICAgcHJvY2Vzc0ZpZWxkc0NvbmZpZyhjb25maWcpIHtcbiAgICAgICAgICAgIGNvbnN0IGZpZWxkc0NvbmZpZyA9IChBcnJheS5pc0FycmF5KGNvbmZpZykpXG4gICAgICAgICAgICAgICAgPyB0aGlzLnJlZm9ybWF0UHJvcGVydGllcyhjb25maWcpXG4gICAgICAgICAgICAgICAgOiBjb25maWc7XG5cbiAgICAgICAgICAgIC8vIFBvc3QtcHJvY2VzcyB0aGUgZmllbGRzIGNvbmZpZ1xuICAgICAgICAgICAgT2JqZWN0LmVudHJpZXMoZmllbGRzQ29uZmlnKS5mb3JFYWNoKChlbnRyeSkgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IFssIGZpZWxkQ29uZmlnXSA9IGVudHJ5O1xuXG4gICAgICAgICAgICAgICAgLy8gUmVuYW1lIFwidGl0bGVcIiBwcm9wZXJ0eSB0byBcImxhYmVsXCJcbiAgICAgICAgICAgICAgICBpZiAoZmllbGRDb25maWcudGl0bGUgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoZmllbGRDb25maWcubGFiZWwgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZmllbGRDb25maWcubGFiZWwgPSBmaWVsZENvbmZpZy50aXRsZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBkZWxldGUgZmllbGRDb25maWcudGl0bGU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGZpZWxkQ29uZmlnLnR5cGUgPT09ICdzZXQnKSB7XG4gICAgICAgICAgICAgICAgICAgIGZpZWxkQ29uZmlnLnR5cGUgPSAnY2hlY2tib3hsaXN0JztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgY29uc29sZS5sb2coZmllbGRzQ29uZmlnKTtcblxuICAgICAgICAgICAgcmV0dXJuIGZpZWxkc0NvbmZpZztcbiAgICAgICAgfSxcbiAgICAgICAgcmVmb3JtYXRQcm9wZXJ0aWVzKHByb3BlcnRpZXMpIHtcbiAgICAgICAgICAgIGNvbnN0IGNvbmZpZyA9IHt9O1xuXG4gICAgICAgICAgICBwcm9wZXJ0aWVzLmZvckVhY2goKHByb3BlcnR5KSA9PiB7XG4gICAgICAgICAgICAgICAgY29uZmlnW3Byb3BlcnR5LnByb3BlcnR5XSA9IHByb3BlcnR5O1xuICAgICAgICAgICAgICAgIGRlbGV0ZSBjb25maWdbcHJvcGVydHkucHJvcGVydHldLnByb3BlcnR5O1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBjb25maWc7XG4gICAgICAgIH0sXG4gICAgfSxcbn07XG48L3NjcmlwdD5cbiIsIi8vIEltcG9ydHNcbmltcG9ydCBfX19DU1NfTE9BREVSX0FQSV9TT1VSQ0VNQVBfSU1QT1JUX19fIGZyb20gXCIuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvY3NzLWxvYWRlci9kaXN0L3J1bnRpbWUvY3NzV2l0aE1hcHBpbmdUb1N0cmluZy5qc1wiO1xuaW1wb3J0IF9fX0NTU19MT0FERVJfQVBJX0lNUE9SVF9fXyBmcm9tIFwiLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2Nzcy1sb2FkZXIvZGlzdC9ydW50aW1lL2FwaS5qc1wiO1xudmFyIF9fX0NTU19MT0FERVJfRVhQT1JUX19fID0gX19fQ1NTX0xPQURFUl9BUElfSU1QT1JUX19fKF9fX0NTU19MT0FERVJfQVBJX1NPVVJDRU1BUF9JTVBPUlRfX18pO1xuLy8gTW9kdWxlXG5fX19DU1NfTE9BREVSX0VYUE9SVF9fXy5wdXNoKFttb2R1bGUuaWQsIFwiLmluc3BlY3Rvci13cmFwcGVyIHtcXG4gIHotaW5kZXg6IDEwMDI7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQge1xcbiAgcG9zaXRpb246IHJlbGF0aXZlO1xcbiAgd2lkdGg6IDM2MHB4O1xcbiAgYm94LXNoYWRvdzogMCAxcHggNnB4IHJnYmEoMCwgMCwgMCwgMC4xMiksIDAgMXB4IDRweCByZ2JhKDAsIDAsIDAsIDAuMjQpO1xcbiAgYm9yZGVyLXJhZGl1czogM3B4O1xcbiAgZm9udC1zaXplOiAxMnB4O1xcbn1cXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyxcXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdzo6YmVmb3JlIHtcXG4gIHBvc2l0aW9uOiBhYnNvbHV0ZTtcXG4gIHdpZHRoOiAxMHB4O1xcbiAgaGVpZ2h0OiAxMHB4O1xcbn1cXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICB2aXNpYmlsaXR5OiBoaWRkZW47XFxuICB6LWluZGV4OiAxMDAyO1xcbn1cXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdzo6YmVmb3JlIHtcXG4gIHZpc2liaWxpdHk6IHZpc2libGU7XFxuICBjb250ZW50OiAnJztcXG4gIHRyYW5zZm9ybTogcm90YXRlKDQ1ZGVnKTtcXG4gIGJhY2tncm91bmQtY29sb3I6ICMzNDk4ZGI7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgaGVhZGVyIHtcXG4gIHBvc2l0aW9uOiByZWxhdGl2ZTtcXG4gIHBhZGRpbmc6IDEwcHggNDZweCAxMHB4IDE2cHg7XFxuICBiYWNrZ3JvdW5kOiAjMzQ5OGRiO1xcbiAgY29sb3I6ICNmZmY7XFxuICBib3JkZXItYm90dG9tOiAxcHggc29saWQgIzIzODNjNDtcXG4gIGJvcmRlci10b3AtbGVmdC1yYWRpdXM6IDNweDtcXG4gIGJvcmRlci10b3AtcmlnaHQtcmFkaXVzOiAzcHg7XFxuICB0ZXh0LXNoYWRvdzogMHB4IDFweCAxcHggcmdiYSgwLCAwLCAwLCAwLjIyKTtcXG4gIHotaW5kZXg6IDEwMDM7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgaGVhZGVyIC5pbnNwZWN0b3ItdGl0bGUge1xcbiAgZm9udC13ZWlnaHQ6IGJvbGQ7XFxuICBmb250LXNpemU6IDE0cHg7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgaGVhZGVyIC5pbnNwZWN0b3ItZGVzY3JpcHRpb24ge1xcbiAgZm9udC13ZWlnaHQ6IG5vcm1hbDtcXG4gIGZvbnQtc2l6ZTogMTJweDtcXG4gIG1hcmdpbi10b3A6IC0zcHg7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgaGVhZGVyIC5pbnNwZWN0b3ItaGlkZSB7XFxuICBwb3NpdGlvbjogYWJzb2x1dGU7XFxuICB0b3A6IDA7XFxuICByaWdodDogMDtcXG4gIHdpZHRoOiAzMHB4O1xcbiAgaGVpZ2h0OiAzMHB4O1xcbiAgbGluZS1oZWlnaHQ6IDMwcHg7XFxuICB0ZXh0LWFsaWduOiBjZW50ZXI7XFxuICBiYWNrZ3JvdW5kOiAjMjM4M2M0O1xcbiAgYm9yZGVyLXRvcC1yaWdodC1yYWRpdXM6IDNweDtcXG4gIGJvcmRlci1ib3R0b20tbGVmdC1yYWRpdXM6IDNweDtcXG4gIGN1cnNvcjogcG9pbnRlcjtcXG4gIHRyYW5zaXRpb246IGJhY2tncm91bmQtY29sb3IgMTc1bXMgZWFzZTtcXG59XFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCBoZWFkZXIgLmluc3BlY3Rvci1oaWRlOjpiZWZvcmUge1xcbiAgdHJhbnNpdGlvbjogb3BhY2l0eSAxNzVtcyBlYXNlO1xcbiAgb3BhY2l0eTogMC43O1xcbiAgbWFyZ2luLXJpZ2h0OiAwO1xcbn1cXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IGhlYWRlciAuaW5zcGVjdG9yLWhpZGU6aG92ZXIge1xcbiAgYmFja2dyb3VuZDogIzIwNzdiMjtcXG59XFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCBoZWFkZXIgLmluc3BlY3Rvci1oaWRlOmhvdmVyOjpiZWZvcmUge1xcbiAgb3BhY2l0eTogMTtcXG59XFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCBtYWluIHtcXG4gIHBvc2l0aW9uOiByZWxhdGl2ZTtcXG4gIGJhY2tncm91bmQ6ICNmMmYyZjI7XFxuICBib3JkZXItYm90dG9tLWxlZnQtcmFkaXVzOiAzcHg7XFxuICBib3JkZXItYm90dG9tLXJpZ2h0LXJhZGl1czogM3B4O1xcbiAgei1pbmRleDogMTAwMztcXG59XFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCBtYWluIC5maWVsZCB7XFxuICBkaXNwbGF5OiBmbGV4O1xcbiAgZmxleC1kaXJlY3Rpb246IHJvdztcXG4gIGp1c3RpZnktY29udGVudDogc3RyZXRjaDtcXG59XFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCBtYWluIC5maWVsZCAuZmllbGQtbGFiZWwge1xcbiAgcGFkZGluZzogNXB4IDEwcHg7XFxuICBmbGV4OiAzIDA7XFxuICBiYWNrZ3JvdW5kOiAjZjJmMmYyO1xcbiAgY29sb3I6ICM0MDUyNjE7XFxuICBib3JkZXItcmlnaHQ6IDFweCBzb2xpZCAjY2NjY2NjO1xcbn1cXG4uaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IG1haW4gLmZpZWxkIC5maWVsZC1jb250cm9sIHtcXG4gIGJhY2tncm91bmQ6ICNmZmY7XFxuICBmbGV4OiA1IDA7XFxufVxcbi5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgbWFpbiAuZmllbGQgKyAuZmllbGQge1xcbiAgYm9yZGVyLXRvcDogMXB4IHNvbGlkICNjY2NjY2M7XFxufVxcbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSd0b3AnXSAuaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICBib3R0b206IC01cHg7XFxufVxcbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSdib3R0b20nXSAuaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICB0b3A6IC01cHg7XFxufVxcbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSdsZWZ0J10gLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCAuYXJyb3cge1xcbiAgcmlnaHQ6IC01cHg7XFxufVxcbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSdyaWdodCddIC5pbnNwZWN0b3IucG9wb3Zlci1sYXlvdXQgLmFycm93IHtcXG4gIGxlZnQ6IC01cHg7XFxufVxcbi5wb3BvdmVyLWZhZGUtZW50ZXItYWN0aXZlLFxcbi5wb3BvdmVyLWZhZGUtbGVhdmUtYWN0aXZlIHtcXG4gIHRyYW5zaXRpb246IG9wYWNpdHkgMTc1bXMgZWFzZS1vdXQsIHRyYW5zZm9ybSAxNzVtcyBlYXNlLW91dDtcXG59XFxuLnBvcG92ZXItZmFkZS1lbnRlci1mcm9tLFxcbi5wb3BvdmVyLWZhZGUtbGVhdmUtdG8ge1xcbiAgb3BhY2l0eTogMDtcXG4gIHRyYW5zZm9ybTogdHJhbnNsYXRlWSgyMHB4KTtcXG59XFxuXCIsIFwiXCIse1widmVyc2lvblwiOjMsXCJzb3VyY2VzXCI6W1wid2VicGFjazovLy4vYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2xheW91dC9Qb3BvdmVyLnZ1ZVwiXSxcIm5hbWVzXCI6W10sXCJtYXBwaW5nc1wiOlwiQUFTQTtFQUNJLGFBQUE7QUFSSjtBQVdBO0VBQ0ksa0JBQUE7RUFDQSxZQUFBO0VBQ0Esd0VBQUE7RUFDQSxrQkFBQTtFQUNBLGVBQUE7QUFUSjtBQUlBOztFQVNRLGtCQUFBO0VBQ0EsV0FBQTtFQUNBLFlBQUE7QUFUUjtBQUZBO0VBZVEsa0JBQUE7RUFDQSxhQUFBO0FBVlI7QUFOQTtFQW9CUSxtQkFBQTtFQUNBLFdBQUE7RUFDQSx3QkFBQTtFQUNBLHlCQUFBO0FBWFI7QUFaQTtFQTJCUSxrQkFBQTtFQUNBLDRCQUFBO0VBQ0EsbUJBQUE7RUFDQSxXQUFBO0VBQ0EsZ0NBQUE7RUFDQSwyQkFBQTtFQUNBLDRCQUFBO0VBQ0EsNENBQUE7RUFDQSxhQUFBO0FBWlI7QUF2QkE7RUFzQ1ksaUJBQUE7RUFDQSxlQUFBO0FBWlo7QUEzQkE7RUEyQ1ksbUJBQUE7RUFDQSxlQUFBO0VBQ0EsZ0JBQUE7QUFiWjtBQWhDQTtFQWlEWSxrQkFBQTtFQUNBLE1BQUE7RUFDQSxRQUFBO0VBQ0EsV0FBQTtFQUNBLFlBQUE7RUFDQSxpQkFBQTtFQUNBLGtCQUFBO0VBQ0EsbUJBQUE7RUFDQSw0QkFBQTtFQUNBLDhCQUFBO0VBQ0EsZUFBQTtFQUNBLHVDQUFBO0FBZFo7QUFnQlk7RUFDSSw4QkFBQTtFQUNBLFlBQUE7RUFDQSxlQUFBO0FBZGhCO0FBaUJZO0VBQ0ksbUJBQUE7QUFmaEI7QUFpQmdCO0VBQ0ksVUFBQTtBQWZwQjtBQXpEQTtFQStFUSxrQkFBQTtFQUNBLG1CQUFBO0VBQ0EsOEJBQUE7RUFDQSwrQkFBQTtFQUNBLGFBQUE7QUFuQlI7QUFoRUE7RUFzRlksYUFBQTtFQUNBLG1CQUFBO0VBQ0Esd0JBQUE7QUFuQlo7QUFyRUE7RUEyRmdCLGlCQUFBO0VBQ0EsU0FBQTtFQUNBLG1CQUFBO0VBQ0EsY0FBQTtFQUNBLCtCQUFBO0FBbkJoQjtBQTVFQTtFQW1HZ0IsZ0JBQUE7RUFDQSxTQUFBO0FBcEJoQjtBQWhGQTtFQXlHWSw2QkFBQTtBQXRCWjtBQTRCQTtFQUNJLFlBQUE7QUExQko7QUE0QkE7RUFDSSxTQUFBO0FBMUJKO0FBNEJBO0VBQ0ksV0FBQTtBQTFCSjtBQTRCQTtFQUNJLFVBQUE7QUExQko7QUE4QkE7O0VBRUksNERBQUE7QUE1Qko7QUFnQ0E7O0VBRUksVUFBQTtFQUNBLDJCQUFBO0FBOUJKXCIsXCJzb3VyY2VzQ29udGVudFwiOltcIlxcbkBpbXBvcnQgKHJlZmVyZW5jZSkgJy4uLy4uLy4uL2xlc3MvZ2xvYmFsLmxlc3MnO1xcbkBpbXBvcnQgKHJlZmVyZW5jZSkgJy4uL3N0eWxlL3ZhcmlhYmxlcy5sZXNzJztcXG5cXG4vLyBWQVJJQUJMRVNcXG5AaW5wc2VjdG9yLXBvcG92ZXItd2lkdGg6IDM2MHB4O1xcbkBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1czogQGJvcmRlci1yYWRpdXMtYmFzZTtcXG5cXG4vLyBTVFlMSU5HXFxuLmluc3BlY3Rvci13cmFwcGVyIHtcXG4gICAgei1pbmRleDogMTAwMjtcXG59XFxuXFxuLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCB7XFxuICAgIHBvc2l0aW9uOiByZWxhdGl2ZTtcXG4gICAgd2lkdGg6IEBpbnBzZWN0b3ItcG9wb3Zlci13aWR0aDtcXG4gICAgYm94LXNoYWRvdzogQG92ZXJsYXktYm94LXNoYWRvdztcXG4gICAgYm9yZGVyLXJhZGl1czogQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzO1xcbiAgICBmb250LXNpemU6IEBpbnNwZWN0b3ItZm9udC1zaXplO1xcblxcbiAgICAuYXJyb3csXFxuICAgIC5hcnJvdzo6YmVmb3JlIHtcXG4gICAgICAgIHBvc2l0aW9uOiBhYnNvbHV0ZTtcXG4gICAgICAgIHdpZHRoOiAxMHB4O1xcbiAgICAgICAgaGVpZ2h0OiAxMHB4O1xcbiAgICB9XFxuXFxuICAgIC5hcnJvdyB7XFxuICAgICAgICB2aXNpYmlsaXR5OiBoaWRkZW47XFxuICAgICAgICB6LWluZGV4OiAxMDAyO1xcbiAgICB9XFxuXFxuICAgIC5hcnJvdzo6YmVmb3JlIHtcXG4gICAgICAgIHZpc2liaWxpdHk6IHZpc2libGU7XFxuICAgICAgICBjb250ZW50OiAnJztcXG4gICAgICAgIHRyYW5zZm9ybTogcm90YXRlKDQ1ZGVnKTtcXG4gICAgICAgIGJhY2tncm91bmQtY29sb3I6IEBpbnNwZWN0b3ItaGVhZGVyLWJnO1xcbiAgICB9XFxuXFxuICAgIGhlYWRlciB7XFxuICAgICAgICBwb3NpdGlvbjogcmVsYXRpdmU7XFxuICAgICAgICBwYWRkaW5nOiBAcGFkZGluZy1sYXJnZS12ZXJ0aWNhbCAoQHBhZGRpbmctbGFyZ2UtaG9yaXpvbnRhbCArIDMwcHgpIEBwYWRkaW5nLWxhcmdlLXZlcnRpY2FsIEBwYWRkaW5nLWxhcmdlLWhvcml6b250YWw7XFxuICAgICAgICBiYWNrZ3JvdW5kOiBAaW5zcGVjdG9yLWhlYWRlci1iZztcXG4gICAgICAgIGNvbG9yOiBAaW5zcGVjdG9yLWhlYWRlci1mZztcXG4gICAgICAgIGJvcmRlci1ib3R0b206IDFweCBzb2xpZCBkYXJrZW4oQGluc3BlY3Rvci1oZWFkZXItYmcsIDglKTtcXG4gICAgICAgIGJvcmRlci10b3AtbGVmdC1yYWRpdXM6IEBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1cztcXG4gICAgICAgIGJvcmRlci10b3AtcmlnaHQtcmFkaXVzOiBAaW5zcGVjdG9yLWJvcmRlci1yYWRpdXM7XFxuICAgICAgICB0ZXh0LXNoYWRvdzogMHB4IDFweCAxcHggcmdiYSgwLCAwLCAwLCAwLjIyKTtcXG4gICAgICAgIHotaW5kZXg6IDEwMDM7XFxuXFxuICAgICAgICAuaW5zcGVjdG9yLXRpdGxlIHtcXG4gICAgICAgICAgICBmb250LXdlaWdodDogQGluc3BlY3Rvci1oZWFkZXItdGl0bGUtd2VpZ2h0O1xcbiAgICAgICAgICAgIGZvbnQtc2l6ZTogQGluc3BlY3Rvci1oZWFkZXItdGl0bGUtc2l6ZTtcXG4gICAgICAgIH1cXG5cXG4gICAgICAgIC5pbnNwZWN0b3ItZGVzY3JpcHRpb24ge1xcbiAgICAgICAgICAgIGZvbnQtd2VpZ2h0OiBAaW5zcGVjdG9yLWhlYWRlci1kZXNjcmlwdGlvbi13ZWlnaHQ7XFxuICAgICAgICAgICAgZm9udC1zaXplOiBAaW5zcGVjdG9yLWhlYWRlci1kZXNjcmlwdGlvbi1zaXplO1xcbiAgICAgICAgICAgIG1hcmdpbi10b3A6IC0zcHg7XFxuICAgICAgICB9XFxuXFxuICAgICAgICAuaW5zcGVjdG9yLWhpZGUge1xcbiAgICAgICAgICAgIHBvc2l0aW9uOiBhYnNvbHV0ZTtcXG4gICAgICAgICAgICB0b3A6IDA7XFxuICAgICAgICAgICAgcmlnaHQ6IDA7XFxuICAgICAgICAgICAgd2lkdGg6IDMwcHg7XFxuICAgICAgICAgICAgaGVpZ2h0OiAzMHB4O1xcbiAgICAgICAgICAgIGxpbmUtaGVpZ2h0OiAzMHB4O1xcbiAgICAgICAgICAgIHRleHQtYWxpZ246IGNlbnRlcjtcXG4gICAgICAgICAgICBiYWNrZ3JvdW5kOiBkYXJrZW4oQGluc3BlY3Rvci1oZWFkZXItYmcsIDglKTtcXG4gICAgICAgICAgICBib3JkZXItdG9wLXJpZ2h0LXJhZGl1czogQGluc3BlY3Rvci1ib3JkZXItcmFkaXVzO1xcbiAgICAgICAgICAgIGJvcmRlci1ib3R0b20tbGVmdC1yYWRpdXM6IEBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1cztcXG4gICAgICAgICAgICBjdXJzb3I6IHBvaW50ZXI7XFxuICAgICAgICAgICAgdHJhbnNpdGlvbjogYmFja2dyb3VuZC1jb2xvciAxNzVtcyBlYXNlO1xcblxcbiAgICAgICAgICAgICY6OmJlZm9yZSB7XFxuICAgICAgICAgICAgICAgIHRyYW5zaXRpb246IG9wYWNpdHkgMTc1bXMgZWFzZTtcXG4gICAgICAgICAgICAgICAgb3BhY2l0eTogMC43O1xcbiAgICAgICAgICAgICAgICBtYXJnaW4tcmlnaHQ6IDA7XFxuICAgICAgICAgICAgfVxcblxcbiAgICAgICAgICAgICY6aG92ZXIge1xcbiAgICAgICAgICAgICAgICBiYWNrZ3JvdW5kOiBkYXJrZW4oQGluc3BlY3Rvci1oZWFkZXItYmcsIDEyJSk7XFxuXFxuICAgICAgICAgICAgICAgICY6OmJlZm9yZSB7XFxuICAgICAgICAgICAgICAgICAgICBvcGFjaXR5OiAxO1xcbiAgICAgICAgICAgICAgICB9XFxuICAgICAgICAgICAgfVxcbiAgICAgICAgfVxcbiAgICB9XFxuXFxuICAgIG1haW4ge1xcbiAgICAgICAgcG9zaXRpb246IHJlbGF0aXZlO1xcbiAgICAgICAgYmFja2dyb3VuZDogQGluc3BlY3Rvci1iZztcXG4gICAgICAgIGJvcmRlci1ib3R0b20tbGVmdC1yYWRpdXM6IEBpbnNwZWN0b3ItYm9yZGVyLXJhZGl1cztcXG4gICAgICAgIGJvcmRlci1ib3R0b20tcmlnaHQtcmFkaXVzOiBAaW5zcGVjdG9yLWJvcmRlci1yYWRpdXM7XFxuICAgICAgICB6LWluZGV4OiAxMDAzO1xcblxcbiAgICAgICAgLmZpZWxkIHtcXG4gICAgICAgICAgICBkaXNwbGF5OiBmbGV4O1xcbiAgICAgICAgICAgIGZsZXgtZGlyZWN0aW9uOiByb3c7XFxuICAgICAgICAgICAganVzdGlmeS1jb250ZW50OiBzdHJldGNoO1xcblxcbiAgICAgICAgICAgIC5maWVsZC1sYWJlbCB7XFxuICAgICAgICAgICAgICAgIHBhZGRpbmc6IEBwYWRkaW5nLXNtYWxsLXZlcnRpY2FsIEBwYWRkaW5nLXNtYWxsLWhvcml6b250YWw7XFxuICAgICAgICAgICAgICAgIGZsZXg6IDMgMDtcXG4gICAgICAgICAgICAgICAgYmFja2dyb3VuZDogQGluc3BlY3Rvci1maWVsZC1sYWJlbC1iZztcXG4gICAgICAgICAgICAgICAgY29sb3I6IEBpbnNwZWN0b3ItZmllbGQtbGFiZWwtZmc7XFxuICAgICAgICAgICAgICAgIGJvcmRlci1yaWdodDogMXB4IHNvbGlkIEBpbnNwZWN0b3ItZmllbGQtYm9yZGVyO1xcbiAgICAgICAgICAgIH1cXG5cXG4gICAgICAgICAgICAuZmllbGQtY29udHJvbCB7XFxuICAgICAgICAgICAgICAgIGJhY2tncm91bmQ6IEBpbnNwZWN0b3ItZmllbGQtYmc7XFxuICAgICAgICAgICAgICAgIGZsZXg6IDUgMDtcXG4gICAgICAgICAgICB9XFxuICAgICAgICB9XFxuXFxuICAgICAgICAuZmllbGQgKyAuZmllbGQge1xcbiAgICAgICAgICAgIGJvcmRlci10b3A6IDFweCBzb2xpZCBAaW5zcGVjdG9yLWZpZWxkLWJvcmRlcjtcXG4gICAgICAgIH1cXG4gICAgfVxcbn1cXG5cXG4vLyBBUlJPVyBQTEFDRU1FTlRcXG4uaW5zcGVjdG9yLXdyYXBwZXJbZGF0YS1wb3BwZXItcGxhY2VtZW50Xj0ndG9wJ10gLmluc3BlY3Rvci5wb3BvdmVyLWxheW91dCAuYXJyb3cge1xcbiAgICBib3R0b206IC01cHg7XFxufVxcbi5pbnNwZWN0b3Itd3JhcHBlcltkYXRhLXBvcHBlci1wbGFjZW1lbnRePSdib3R0b20nXSAuaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICAgIHRvcDogLTVweDtcXG59XFxuLmluc3BlY3Rvci13cmFwcGVyW2RhdGEtcG9wcGVyLXBsYWNlbWVudF49J2xlZnQnXSAuaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICAgIHJpZ2h0OiAtNXB4O1xcbn1cXG4uaW5zcGVjdG9yLXdyYXBwZXJbZGF0YS1wb3BwZXItcGxhY2VtZW50Xj0ncmlnaHQnXSAuaW5zcGVjdG9yLnBvcG92ZXItbGF5b3V0IC5hcnJvdyB7XFxuICAgIGxlZnQ6IC01cHg7XFxufVxcblxcbi8vIFRSQU5TSVRJT05TXFxuLnBvcG92ZXItZmFkZS1lbnRlci1hY3RpdmUsXFxuLnBvcG92ZXItZmFkZS1sZWF2ZS1hY3RpdmUge1xcbiAgICB0cmFuc2l0aW9uOiBvcGFjaXR5IDE3NW1zIGVhc2Utb3V0LFxcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm0gMTc1bXMgZWFzZS1vdXQ7XFxufVxcblxcbi5wb3BvdmVyLWZhZGUtZW50ZXItZnJvbSxcXG4ucG9wb3Zlci1mYWRlLWxlYXZlLXRvIHtcXG4gICAgb3BhY2l0eTogMDtcXG4gICAgdHJhbnNmb3JtOiB0cmFuc2xhdGVZKDIwcHgpO1xcbn1cXG5cIl0sXCJzb3VyY2VSb290XCI6XCJcIn1dKTtcbi8vIEV4cG9ydHNcbmV4cG9ydCBkZWZhdWx0IF9fX0NTU19MT0FERVJfRVhQT1JUX19fO1xuIiwiaW1wb3J0IGFwaSBmcm9tIFwiIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9zdHlsZS1sb2FkZXIvZGlzdC9ydW50aW1lL2luamVjdFN0eWxlc0ludG9TdHlsZVRhZy5qc1wiO1xuICAgICAgICAgICAgaW1wb3J0IGNvbnRlbnQgZnJvbSBcIiEhLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2Nzcy1sb2FkZXIvZGlzdC9janMuanM/P2Nsb25lZFJ1bGVTZXQtMTcudXNlWzFdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3Qvc3R5bGVQb3N0TG9hZGVyLmpzIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9wb3N0Y3NzLWxvYWRlci9kaXN0L2Nqcy5qcz8/Y2xvbmVkUnVsZVNldC0xNy51c2VbMl0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2xlc3MtbG9hZGVyL2Rpc3QvY2pzLmpzIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vUG9wb3Zlci52dWU/dnVlJnR5cGU9c3R5bGUmaW5kZXg9MCZpZD05NjEwMGI5NiZsYW5nPWxlc3NcIjtcblxudmFyIG9wdGlvbnMgPSB7fTtcblxub3B0aW9ucy5pbnNlcnQgPSBcImhlYWRcIjtcbm9wdGlvbnMuc2luZ2xldG9uID0gZmFsc2U7XG5cbnZhciB1cGRhdGUgPSBhcGkoY29udGVudCwgb3B0aW9ucyk7XG5cblxuXG5leHBvcnQgZGVmYXVsdCBjb250ZW50LmxvY2FscyB8fCB7fTsiLCJpbXBvcnQgeyByZW5kZXIgfSBmcm9tIFwiLi9GaWVsZC52dWU/dnVlJnR5cGU9dGVtcGxhdGUmaWQ9MDBhM2I2NGVcIlxuaW1wb3J0IHNjcmlwdCBmcm9tIFwiLi9GaWVsZC52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIlxuZXhwb3J0ICogZnJvbSBcIi4vRmllbGQudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCJcblxuaW1wb3J0IGV4cG9ydENvbXBvbmVudCBmcm9tIFwiL2hvbWUvYmVuL1Byb2plY3RzL1BlcnNvbmFsL3dpbnRlcmNtcy93aW50ZXIvbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9leHBvcnRIZWxwZXIuanNcIlxuY29uc3QgX19leHBvcnRzX18gPSAvKiNfX1BVUkVfXyovZXhwb3J0Q29tcG9uZW50KHNjcmlwdCwgW1sncmVuZGVyJyxyZW5kZXJdLFsnX19maWxlJyxcImFzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9maWVsZHMvRmllbGQudnVlXCJdXSlcbi8qIGhvdCByZWxvYWQgKi9cbmlmIChtb2R1bGUuaG90KSB7XG4gIF9fZXhwb3J0c19fLl9faG1ySWQgPSBcIjAwYTNiNjRlXCJcbiAgY29uc3QgYXBpID0gX19WVUVfSE1SX1JVTlRJTUVfX1xuICBtb2R1bGUuaG90LmFjY2VwdCgpXG4gIGlmICghYXBpLmNyZWF0ZVJlY29yZCgnMDBhM2I2NGUnLCBfX2V4cG9ydHNfXykpIHtcbiAgICBjb25zb2xlLmxvZygncmVsb2FkJylcbiAgICBhcGkucmVsb2FkKCcwMGEzYjY0ZScsIF9fZXhwb3J0c19fKVxuICB9XG4gIFxuICBtb2R1bGUuaG90LmFjY2VwdChcIi4vRmllbGQudnVlP3Z1ZSZ0eXBlPXRlbXBsYXRlJmlkPTAwYTNiNjRlXCIsICgpID0+IHtcbiAgICBjb25zb2xlLmxvZygncmUtcmVuZGVyJylcbiAgICBhcGkucmVyZW5kZXIoJzAwYTNiNjRlJywgcmVuZGVyKVxuICB9KVxuXG59XG5cblxuZXhwb3J0IGRlZmF1bHQgX19leHBvcnRzX18iLCJpbXBvcnQgeyByZW5kZXIgfSBmcm9tIFwiLi9GaWVsZExhYmVsLnZ1ZT92dWUmdHlwZT10ZW1wbGF0ZSZpZD03MTUyODVjYlwiXG5pbXBvcnQgc2NyaXB0IGZyb20gXCIuL0ZpZWxkTGFiZWwudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCJcbmV4cG9ydCAqIGZyb20gXCIuL0ZpZWxkTGFiZWwudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCJcblxuaW1wb3J0IGV4cG9ydENvbXBvbmVudCBmcm9tIFwiL2hvbWUvYmVuL1Byb2plY3RzL1BlcnNvbmFsL3dpbnRlcmNtcy93aW50ZXIvbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9leHBvcnRIZWxwZXIuanNcIlxuY29uc3QgX19leHBvcnRzX18gPSAvKiNfX1BVUkVfXyovZXhwb3J0Q29tcG9uZW50KHNjcmlwdCwgW1sncmVuZGVyJyxyZW5kZXJdLFsnX19maWxlJyxcImFzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9maWVsZHMvRmllbGRMYWJlbC52dWVcIl1dKVxuLyogaG90IHJlbG9hZCAqL1xuaWYgKG1vZHVsZS5ob3QpIHtcbiAgX19leHBvcnRzX18uX19obXJJZCA9IFwiNzE1Mjg1Y2JcIlxuICBjb25zdCBhcGkgPSBfX1ZVRV9ITVJfUlVOVElNRV9fXG4gIG1vZHVsZS5ob3QuYWNjZXB0KClcbiAgaWYgKCFhcGkuY3JlYXRlUmVjb3JkKCc3MTUyODVjYicsIF9fZXhwb3J0c19fKSkge1xuICAgIGNvbnNvbGUubG9nKCdyZWxvYWQnKVxuICAgIGFwaS5yZWxvYWQoJzcxNTI4NWNiJywgX19leHBvcnRzX18pXG4gIH1cbiAgXG4gIG1vZHVsZS5ob3QuYWNjZXB0KFwiLi9GaWVsZExhYmVsLnZ1ZT92dWUmdHlwZT10ZW1wbGF0ZSZpZD03MTUyODVjYlwiLCAoKSA9PiB7XG4gICAgY29uc29sZS5sb2coJ3JlLXJlbmRlcicpXG4gICAgYXBpLnJlcmVuZGVyKCc3MTUyODVjYicsIHJlbmRlcilcbiAgfSlcblxufVxuXG5cbmV4cG9ydCBkZWZhdWx0IF9fZXhwb3J0c19fIiwiaW1wb3J0IHsgcmVuZGVyIH0gZnJvbSBcIi4vUG9wb3Zlci52dWU/dnVlJnR5cGU9dGVtcGxhdGUmaWQ9OTYxMDBiOTZcIlxuaW1wb3J0IHNjcmlwdCBmcm9tIFwiLi9Qb3BvdmVyLnZ1ZT92dWUmdHlwZT1zY3JpcHQmbGFuZz1qc1wiXG5leHBvcnQgKiBmcm9tIFwiLi9Qb3BvdmVyLnZ1ZT92dWUmdHlwZT1zY3JpcHQmbGFuZz1qc1wiXG5cbmltcG9ydCBcIi4vUG9wb3Zlci52dWU/dnVlJnR5cGU9c3R5bGUmaW5kZXg9MCZpZD05NjEwMGI5NiZsYW5nPWxlc3NcIlxuXG5pbXBvcnQgZXhwb3J0Q29tcG9uZW50IGZyb20gXCIvaG9tZS9iZW4vUHJvamVjdHMvUGVyc29uYWwvd2ludGVyY21zL3dpbnRlci9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2V4cG9ydEhlbHBlci5qc1wiXG5jb25zdCBfX2V4cG9ydHNfXyA9IC8qI19fUFVSRV9fKi9leHBvcnRDb21wb25lbnQoc2NyaXB0LCBbWydyZW5kZXInLHJlbmRlcl0sWydfX2ZpbGUnLFwiYXNzZXRzL3VpL3dpZGdldHMvaW5zcGVjdG9yL2xheW91dC9Qb3BvdmVyLnZ1ZVwiXV0pXG4vKiBob3QgcmVsb2FkICovXG5pZiAobW9kdWxlLmhvdCkge1xuICBfX2V4cG9ydHNfXy5fX2htcklkID0gXCI5NjEwMGI5NlwiXG4gIGNvbnN0IGFwaSA9IF9fVlVFX0hNUl9SVU5USU1FX19cbiAgbW9kdWxlLmhvdC5hY2NlcHQoKVxuICBpZiAoIWFwaS5jcmVhdGVSZWNvcmQoJzk2MTAwYjk2JywgX19leHBvcnRzX18pKSB7XG4gICAgY29uc29sZS5sb2coJ3JlbG9hZCcpXG4gICAgYXBpLnJlbG9hZCgnOTYxMDBiOTYnLCBfX2V4cG9ydHNfXylcbiAgfVxuICBcbiAgbW9kdWxlLmhvdC5hY2NlcHQoXCIuL1BvcG92ZXIudnVlP3Z1ZSZ0eXBlPXRlbXBsYXRlJmlkPTk2MTAwYjk2XCIsICgpID0+IHtcbiAgICBjb25zb2xlLmxvZygncmUtcmVuZGVyJylcbiAgICBhcGkucmVyZW5kZXIoJzk2MTAwYjk2JywgcmVuZGVyKVxuICB9KVxuXG59XG5cblxuZXhwb3J0IGRlZmF1bHQgX19leHBvcnRzX18iLCJpbXBvcnQgeyByZW5kZXIgfSBmcm9tIFwiLi9JbnNwZWN0b3IudnVlP3Z1ZSZ0eXBlPXRlbXBsYXRlJmlkPTVjMTdlMWQ0XCJcbmltcG9ydCBzY3JpcHQgZnJvbSBcIi4vSW5zcGVjdG9yLnZ1ZT92dWUmdHlwZT1zY3JpcHQmbGFuZz1qc1wiXG5leHBvcnQgKiBmcm9tIFwiLi9JbnNwZWN0b3IudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCJcblxuaW1wb3J0IGV4cG9ydENvbXBvbmVudCBmcm9tIFwiL2hvbWUvYmVuL1Byb2plY3RzL1BlcnNvbmFsL3dpbnRlcmNtcy93aW50ZXIvbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9leHBvcnRIZWxwZXIuanNcIlxuY29uc3QgX19leHBvcnRzX18gPSAvKiNfX1BVUkVfXyovZXhwb3J0Q29tcG9uZW50KHNjcmlwdCwgW1sncmVuZGVyJyxyZW5kZXJdLFsnX19maWxlJyxcImFzc2V0cy91aS93aWRnZXRzL2luc3BlY3Rvci9tYWluL0luc3BlY3Rvci52dWVcIl1dKVxuLyogaG90IHJlbG9hZCAqL1xuaWYgKG1vZHVsZS5ob3QpIHtcbiAgX19leHBvcnRzX18uX19obXJJZCA9IFwiNWMxN2UxZDRcIlxuICBjb25zdCBhcGkgPSBfX1ZVRV9ITVJfUlVOVElNRV9fXG4gIG1vZHVsZS5ob3QuYWNjZXB0KClcbiAgaWYgKCFhcGkuY3JlYXRlUmVjb3JkKCc1YzE3ZTFkNCcsIF9fZXhwb3J0c19fKSkge1xuICAgIGNvbnNvbGUubG9nKCdyZWxvYWQnKVxuICAgIGFwaS5yZWxvYWQoJzVjMTdlMWQ0JywgX19leHBvcnRzX18pXG4gIH1cbiAgXG4gIG1vZHVsZS5ob3QuYWNjZXB0KFwiLi9JbnNwZWN0b3IudnVlP3Z1ZSZ0eXBlPXRlbXBsYXRlJmlkPTVjMTdlMWQ0XCIsICgpID0+IHtcbiAgICBjb25zb2xlLmxvZygncmUtcmVuZGVyJylcbiAgICBhcGkucmVyZW5kZXIoJzVjMTdlMWQ0JywgcmVuZGVyKVxuICB9KVxuXG59XG5cblxuZXhwb3J0IGRlZmF1bHQgX19leHBvcnRzX18iLCJleHBvcnQgeyBkZWZhdWx0IH0gZnJvbSBcIi0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2JhYmVsLWxvYWRlci9saWIvaW5kZXguanM/P2Nsb25lZFJ1bGVTZXQtNS51c2VbMF0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9pbmRleC5qcz8/cnVsZVNldFswXS51c2VbMF0hLi9GaWVsZC52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIjsgZXhwb3J0ICogZnJvbSBcIi0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2JhYmVsLWxvYWRlci9saWIvaW5kZXguanM/P2Nsb25lZFJ1bGVTZXQtNS51c2VbMF0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9pbmRleC5qcz8/cnVsZVNldFswXS51c2VbMF0hLi9GaWVsZC52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIiIsImV4cG9ydCB7IGRlZmF1bHQgfSBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL0ZpZWxkTGFiZWwudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCI7IGV4cG9ydCAqIGZyb20gXCItIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9iYWJlbC1sb2FkZXIvbGliL2luZGV4LmpzPz9jbG9uZWRSdWxlU2V0LTUudXNlWzBdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vRmllbGRMYWJlbC52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIiIsImV4cG9ydCB7IGRlZmF1bHQgfSBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL1BvcG92ZXIudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCI7IGV4cG9ydCAqIGZyb20gXCItIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9iYWJlbC1sb2FkZXIvbGliL2luZGV4LmpzPz9jbG9uZWRSdWxlU2V0LTUudXNlWzBdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vUG9wb3Zlci52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIiIsImV4cG9ydCB7IGRlZmF1bHQgfSBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL0luc3BlY3Rvci52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIjsgZXhwb3J0ICogZnJvbSBcIi0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2JhYmVsLWxvYWRlci9saWIvaW5kZXguanM/P2Nsb25lZFJ1bGVTZXQtNS51c2VbMF0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9pbmRleC5qcz8/cnVsZVNldFswXS51c2VbMF0hLi9JbnNwZWN0b3IudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCIiLCJleHBvcnQgKiBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L3RlbXBsYXRlTG9hZGVyLmpzPz9ydWxlU2V0WzFdLnJ1bGVzWzJdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vRmllbGQudnVlP3Z1ZSZ0eXBlPXRlbXBsYXRlJmlkPTAwYTNiNjRlXCIiLCJleHBvcnQgKiBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L3RlbXBsYXRlTG9hZGVyLmpzPz9ydWxlU2V0WzFdLnJ1bGVzWzJdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vRmllbGRMYWJlbC52dWU/dnVlJnR5cGU9dGVtcGxhdGUmaWQ9NzE1Mjg1Y2JcIiIsImV4cG9ydCAqIGZyb20gXCItIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9iYWJlbC1sb2FkZXIvbGliL2luZGV4LmpzPz9jbG9uZWRSdWxlU2V0LTUudXNlWzBdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvdGVtcGxhdGVMb2FkZXIuanM/P3J1bGVTZXRbMV0ucnVsZXNbMl0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9pbmRleC5qcz8/cnVsZVNldFswXS51c2VbMF0hLi9Qb3BvdmVyLnZ1ZT92dWUmdHlwZT10ZW1wbGF0ZSZpZD05NjEwMGI5NlwiIiwiZXhwb3J0ICogZnJvbSBcIi0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2JhYmVsLWxvYWRlci9saWIvaW5kZXguanM/P2Nsb25lZFJ1bGVTZXQtNS51c2VbMF0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC90ZW1wbGF0ZUxvYWRlci5qcz8/cnVsZVNldFsxXS5ydWxlc1syXSEuLi8uLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL0luc3BlY3Rvci52dWU/dnVlJnR5cGU9dGVtcGxhdGUmaWQ9NWMxN2UxZDRcIiIsImV4cG9ydCAqIGZyb20gXCItIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9zdHlsZS1sb2FkZXIvZGlzdC9janMuanMhLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2Nzcy1sb2FkZXIvZGlzdC9janMuanM/P2Nsb25lZFJ1bGVTZXQtMTcudXNlWzFdIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3Qvc3R5bGVQb3N0TG9hZGVyLmpzIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9wb3N0Y3NzLWxvYWRlci9kaXN0L2Nqcy5qcz8/Y2xvbmVkUnVsZVNldC0xNy51c2VbMl0hLi4vLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2xlc3MtbG9hZGVyL2Rpc3QvY2pzLmpzIS4uLy4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vUG9wb3Zlci52dWU/dnVlJnR5cGU9c3R5bGUmaW5kZXg9MCZpZD05NjEwMGI5NiZsYW5nPWxlc3NcIiJdLCJuYW1lcyI6WyJNYW5hZ2VyIiwid2luZG93IiwiU25vd2JvYXJkIiwidW5kZWZpbmVkIiwiRXJyb3IiLCJhZGRQbHVnaW4iLCJjcmVhdGVBcHAiLCJJbnNwZWN0b3IiLCJzbm93Ym9hcmQiLCJpbnNwZWN0YWJsZUVsZW1lbnRzIiwiY3VycmVudEluc3BlY3RvciIsInJlYWR5IiwiYmluZEluc3BlY3RhYmxlRWxlbWVudHMiLCJ1bmJpbmRJbnNwZWN0YWJsZUVsZW1lbnRzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZm9yRWFjaCIsImVsZW1lbnQiLCJpbnNwZWN0b3JEYXRhIiwiZm9ybSIsImZpbmRGb3JtIiwiaW5zcGVjdG9yRWxlbWVudCIsImluc3BlY3RvclZ1ZSIsImNvbnRhaW5lciIsImZpbmRJbnNwZWN0YWJsZUNvbnRhaW5lciIsImhhbmRsZXIiLCJldmVudCIsImluc3BlY3RhYmxlQ2xpY2siLCJjYWxsIiwidGl0bGUiLCJkYXRhc2V0IiwiaW5zcGVjdG9yVGl0bGUiLCJkZXNjcmlwdGlvbiIsImluc3BlY3RvckRlc2NyaXB0aW9uIiwiY29uZmlnIiwiaW5zcGVjdG9yQ29uZmlnIiwib2Zmc2V0IiwieCIsImluc3BlY3Rvck9mZnNldFgiLCJpbnNwZWN0b3JPZmZzZXQiLCJ5IiwiaW5zcGVjdG9yT2Zmc2V0WSIsInBsYWNlbWVudCIsImluc3BlY3RvclBsYWNlbWVudCIsImZhbGxiYWNrUGxhY2VtZW50IiwiaW5zcGVjdG9yRmFsbGJhY2tQbGFjZW1lbnQiLCJjc3NDbGFzc2VzIiwiaW5zcGVjdG9yQ3NzQ2xhc3MiLCJwdXNoIiwiYWRkRXZlbnRMaXN0ZW5lciIsImluc3BlY3RvciIsImNyZWF0ZUVsZW1lbnQiLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJpbnNwZWN0ZWRFbGVtZW50Iiwib2Zmc2V0WCIsIm9mZnNldFkiLCJoaWRlRm4iLCJoaWRlSW5zcGVjdG9yIiwibW91bnQiLCJ1bm1vdW50IiwicmVtb3ZlQ2hpbGQiLCJwcmV2ZW50RGVmYXVsdCIsImNyZWF0ZUluc3BlY3RvciIsImN1cnJlbnRFbGVtZW50IiwicGFyZW50RWxlbWVudCIsInRhZ05hbWUiLCJtYXRjaGVzIiwiY2xvc2VzdCIsIlNpbmdsZXRvbiIsInByb3BzIiwibGFiZWwiLCJ0eXBlIiwiU3RyaW5nIiwiQm9vbGVhbiIsImNyZWF0ZVBvcHBlciIsImFycm93IiwiZmxpcCIsInByZXZlbnRPdmVyZmxvdyIsInNob3duIiwiT2JqZWN0IiwicmVxdWlyZWQiLCJIVE1MRWxlbWVudCIsIkZ1bmN0aW9uIiwidmFsaWRhdGUiLCJ2YWx1ZSIsImluZGV4T2YiLCJOdW1iZXIiLCJkYXRhIiwidG9wIiwibGVmdCIsImluc3BlY3RlZEVsZW1lbnRTdHlsZSIsInBvc2l0aW9uIiwiekluZGV4IiwicG9wcGVySW5zdGFuY2UiLCJ3YXRjaCIsImlzU2hvd24iLCJvdmVybGF5Iiwic2hvdyIsImhpZ2hsaWdodEluc3BlY3RlZEVsZW1lbnQiLCIkbmV4dFRpY2siLCJkZXN0cm95IiwiaGlkZSIsInVubW91bnRlZCIsIm1ldGhvZHMiLCIkcmVmcyIsInBvcG92ZXIiLCJtb2RpZmllcnMiLCJvcHRpb25zIiwic3R5bGUiLCJQb3BvdmVyTGF5b3V0IiwiRmllbGQiLCJGaWVsZExhYmVsIiwiY29tcG9uZW50cyIsImxheW91dCIsInNob3dJbnNwZWN0b3IiLCJ1c2VyQ29uZmlnIiwiZmllbGRzIiwiY29tcHV0ZWQiLCJpbnNwZWN0b3JGaWVsZHMiLCJsYXlvdXRQcm9wcyIsImxheW91dENvbXBvbmVudCIsIm1vdW50ZWQiLCJnZXRDb25maWd1cmF0aW9uIiwiSlNPTiIsInBhcnNlIiwicHJvY2Vzc0ZpZWxkc0NvbmZpZyIsInByb3BlcnRpZXMiLCJnZXRDb25maWd1cmF0aW9uRnJvbUJhY2tlbmQiLCJyZXF1ZXN0Iiwic3VjY2VzcyIsImNvbmZpZ3VyYXRpb24iLCJjb21wbGV0ZSIsImZpZWxkc0NvbmZpZyIsIkFycmF5IiwiaXNBcnJheSIsInJlZm9ybWF0UHJvcGVydGllcyIsImVudHJpZXMiLCJlbnRyeSIsImZpZWxkQ29uZmlnIiwiY29uc29sZSIsImxvZyIsInByb3BlcnR5IiwiX2NyZWF0ZUVsZW1lbnRCbG9jayIsIl9yZW5kZXJTbG90IiwiX2N0eCIsIiRwcm9wcyIsIl9jcmVhdGVFbGVtZW50Vk5vZGUiLCJfdG9EaXNwbGF5U3RyaW5nIiwicmVmIiwiX2NyZWF0ZVZOb2RlIiwiX1RyYW5zaXRpb24iLCJuYW1lIiwiX2hvaXN0ZWRfMyIsIm9uQ2xpY2siLCJfY3JlYXRlQmxvY2siLCJfcmVzb2x2ZUR5bmFtaWNDb21wb25lbnQiLCIkb3B0aW9ucyIsIl9ub3JtYWxpemVQcm9wcyIsIl9GcmFnbWVudCIsIl9yZW5kZXJMaXN0IiwiZmllbGQiLCJpIiwiX2NvbXBvbmVudF9GaWVsZCIsImtleSIsIl9jb21wb25lbnRfRmllbGRMYWJlbCJdLCJzb3VyY2VSb290IjoiIn0=