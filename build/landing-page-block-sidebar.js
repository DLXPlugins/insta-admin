/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/plugins/landing/sidebar.js":
/*!****************************************!*\
  !*** ./src/plugins/landing/sidebar.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_4__);
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }





var Sidebar = function Sidebar(props) {
  var _useState = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(false),
    _useState2 = _slicedToArray(_useState, 2),
    isFullScreen = _useState2[0],
    setIsFullScreen = _useState2[1];
  var _useState3 = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)('insta-admin'),
    _useState4 = _slicedToArray(_useState3, 2),
    adminSlug = _useState4[0],
    setAdminSlug = _useState4[1];
  var _useState5 = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Site Features', 'insta-admin-landing-page')),
    _useState6 = _slicedToArray(_useState5, 2),
    adminMenuTitle = _useState6[0],
    setAdminMenuTitle = _useState6[1];

  /* Subscribe to post updates and update the slug */
  (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.subscribe)(function () {
    var currentPostId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.select)('core/editor').getCurrentPostId();
    var currentPost = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.select)('core').getEntityRecord('postType', 'insta_admin_landing', currentPostId);
    var isSaving = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.select)('core/editor').isSavingPost();
    // Update slug in the text control to match the saved slug in meta.
    if (isSaving && currentPost && currentPost.status === 'private' && currentPost.modified_gmt !== currentPost.date_gmt) {
      var meta = wp.data.select('core/editor').getEditedPostAttribute('meta');
      if (typeof meta === 'undefined') {
        return;
      }

      // Find and sanitize slug.
      if (meta._ialp_slug !== null && typeof meta._ialp_slug !== 'undefined') {
        setAdminSlug((0,_wordpress_url__WEBPACK_IMPORTED_MODULE_4__.cleanForSlug)(meta._ialp_slug));
      }
    }
  });

  /* Initialize the initial state */
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(function () {
    var meta = wp.data.select('core/editor').getEditedPostAttribute('meta');
    if (typeof meta === 'undefined') {
      props.setMetaFieldValue('_ialp_full_screen', false);
      setIsFullScreen(false);
      return;
    }
    // Check for meta key _ialp_full_screen
    if (meta._ialp_full_screen === null || typeof meta._ialp_full_screen === 'undefined') {
      props.setMetaFieldValue('_ialp_full_screen', false);
      setIsFullScreen(false);
    } else {
      setIsFullScreen(meta._ialp_full_screen);
    }

    // Set admin slug.
    if (meta._ialp_slug === null || typeof meta._ialp_slug === 'undefined') {
      props.setMetaFieldValue('_ialp_slug', 'insta-admin');
      setAdminSlug('insta-admin');
    } else {
      setAdminSlug(meta._ialp_slug);
    }

    // Set admin title.
    if (meta._ialp_menu_title === null || typeof meta._ialp_menu_title === 'undefined') {
      props.setMetaFieldValue('_ialp_menu_title', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Site Features', 'insta-admin-landing-page'));
      setAdminMenuTitle((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Site Features', 'insta-admin-landing-page'));
    } else {
      setAdminMenuTitle(meta._ialp_menu_title);
    }
  }, []);
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    initialOpen: true,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Appearance', 'quotes-dlx')
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Full Screen Admin', 'insta-admin-landing-page'),
    checked: isFullScreen,
    onChange: function onChange(value) {
      setIsFullScreen(value);
      props.setMetaFieldValue('_ialp_full_screen', value);
    },
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Make the admin panel full screen.', 'insta-admin-landing-page')
  }))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    initialOpen: true,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Settings', 'quotes-dlx')
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Landing Page Slug', 'insta-admin-landing-page'),
    value: adminSlug,
    onChange: function onChange(value) {
      setAdminSlug(value);
      props.setMetaFieldValue('_ialp_slug', (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_4__.cleanForSlug)(value));
    },
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set the slug for the landing page.', 'insta-admin-landing-page')
  })), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu Title', 'insta-admin-landing-page'),
    value: adminMenuTitle,
    onChange: function onChange(value) {
      setAdminMenuTitle(value);
      props.setMetaFieldValue('_ialp_menu_title', value);
    },
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set the menu title used in the admin sidebar.', 'insta-admin-landing-page')
  }))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.withDispatch)(function (dispatch) {
  return {
    setMetaFieldValue: function setMetaFieldValue(key, value) {
      dispatch('core/editor').editPost({
        meta: _defineProperty({}, key, value)
      });
    }
  };
})(Sidebar));

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["editPost"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["plugins"];

/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/***/ ((module) => {

module.exports = window["wp"]["url"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
/*!**************************************!*\
  !*** ./src/plugins/landing/index.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _sidebar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./sidebar */ "./src/plugins/landing/sidebar.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__);




(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__.registerPlugin)('insta-admin-landing-page-options', {
  icon: /*#__PURE__*/React.createElement("svg", {
    viewBox: "0 0 2134 2134",
    xmlns: "http://www.w3.org/2000/svg",
    xmlSpace: "preserve",
    width: 24,
    height: 24
  }, /*#__PURE__*/React.createElement("path", {
    d: "M1655.89 830.429c-12.267-19.066-33.334-30.4-55.867-30.4h-466.667V66.696c0-31.467-22-58.667-52.8-65.2-31.333-6.667-62 9.466-74.8 38.133l-533.333 1200c-9.2 20.534-7.2 44.534 5.066 63.333 12.267 18.934 33.334 30.4 55.867 30.4h466.667v733.334c0 31.466 22 58.666 52.8 65.2 4.666.933 9.333 1.467 13.866 1.467 25.867 0 50-15.067 60.934-39.601l533.333-1200c9.067-20.667 7.333-44.401-5.066-63.334Z",
    style: {
      fill: '#ffc107',
      fillRule: 'nonzero'
    }
  })),
  render: function render() {
    return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__.PluginSidebarMoreMenuItem, {
      target: "insta-admin-landing-sidebar"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('InstaAdmin Options', 'insta-admin-landing-page')), /*#__PURE__*/React.createElement(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__.PluginSidebar, {
      name: "insta-admin-landing-sidebar",
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('InstaAdmin Options', 'insta-admin-landing-page')
    }, /*#__PURE__*/React.createElement(_sidebar__WEBPACK_IMPORTED_MODULE_3__["default"], null)));
  }
});
})();

/******/ })()
;
//# sourceMappingURL=landing-page-block-sidebar.js.map