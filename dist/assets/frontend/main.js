/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/scss/introStyles.scss":
/*!**************************************!*\
  !*** ./assets/scss/introStyles.scss ***!
  \**************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/sourceMaps.js */ "./node_modules/css-loader/dist/runtime/sourceMaps.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, ``, "",{"version":3,"sources":[],"names":[],"mappings":"","sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./assets/scss/payment_fields.scss":
/*!*****************************************!*\
  !*** ./assets/scss/payment_fields.scss ***!
  \*****************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/sourceMaps.js */ "./node_modules/css-loader/dist/runtime/sourceMaps.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, ``, "",{"version":3,"sources":[],"names":[],"mappings":"","sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/api.js":
/*!*****************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/api.js ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
module.exports = function (cssWithMappingToString) {
  var list = [];

  // return the list of modules as css string
  list.toString = function toString() {
    return this.map(function (item) {
      var content = "";
      var needLayer = typeof item[5] !== "undefined";
      if (item[4]) {
        content += "@supports (".concat(item[4], ") {");
      }
      if (item[2]) {
        content += "@media ".concat(item[2], " {");
      }
      if (needLayer) {
        content += "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {");
      }
      content += cssWithMappingToString(item);
      if (needLayer) {
        content += "}";
      }
      if (item[2]) {
        content += "}";
      }
      if (item[4]) {
        content += "}";
      }
      return content;
    }).join("");
  };

  // import a list of modules into the list
  list.i = function i(modules, media, dedupe, supports, layer) {
    if (typeof modules === "string") {
      modules = [[null, modules, undefined]];
    }
    var alreadyImportedModules = {};
    if (dedupe) {
      for (var k = 0; k < this.length; k++) {
        var id = this[k][0];
        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }
    for (var _k = 0; _k < modules.length; _k++) {
      var item = [].concat(modules[_k]);
      if (dedupe && alreadyImportedModules[item[0]]) {
        continue;
      }
      if (typeof layer !== "undefined") {
        if (typeof item[5] === "undefined") {
          item[5] = layer;
        } else {
          item[1] = "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {").concat(item[1], "}");
          item[5] = layer;
        }
      }
      if (media) {
        if (!item[2]) {
          item[2] = media;
        } else {
          item[1] = "@media ".concat(item[2], " {").concat(item[1], "}");
          item[2] = media;
        }
      }
      if (supports) {
        if (!item[4]) {
          item[4] = "".concat(supports);
        } else {
          item[1] = "@supports (".concat(item[4], ") {").concat(item[1], "}");
          item[4] = supports;
        }
      }
      list.push(item);
    }
  };
  return list;
};

/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/sourceMaps.js":
/*!************************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/sourceMaps.js ***!
  \************************************************************/
/***/ ((module) => {

"use strict";


module.exports = function (item) {
  var content = item[1];
  var cssMapping = item[3];
  if (!cssMapping) {
    return content;
  }
  if (typeof btoa === "function") {
    var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(cssMapping))));
    var data = "sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(base64);
    var sourceMapping = "/*# ".concat(data, " */");
    return [content].concat([sourceMapping]).join("\n");
  }
  return [content].join("\n");
};

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
/******/ 			id: moduleId,
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
/*!***************************!*\
  !*** ./assets/js/main.js ***!
  \***************************/
// Require main style file here for concatenation.
__webpack_require__(/*! ./assets/scss/payment_fields.scss */ "./assets/scss/payment_fields.scss");
__webpack_require__(/*! ./assets/scss/introStyles.scss */ "./assets/scss/introStyles.scss");
const handleSize = function (elem, size) {
  if (size < 600) {
    elem.classList.remove('col-wide');
    elem.classList.add('col-narrow');
  } else {
    elem.classList.remove('col-narrow');
    elem.classList.add('col-wide');
  }
};
// handleSize for resize event
const delta = 300;
let startTime;
let timeout = false;
const handleResize = function () {
  const container = document.getElementById('payment');
  const paytrailContainer = document.getElementsByClassName('payment_method_paytrail');
  if (new Date() - startTime < delta) {
    setTimeout(handleResize, delta);
  } else {
    timeout = false;
    handleSize(paytrailContainer[0], Math.round(container.offsetWidth));
  }
};
//Handle Apple Pay button and mobile group visibility
const handleApplePay = function () {
  if (typeof paytrail !== 'undefined') {
    const applePay = paytrail.applePayButton;
    const button = document.getElementsByClassName('paytrail-woocommerce-payment-fields--list-item apple-pay');
    // Return if there is no Apple Pay button (for blocks order-pay page)
    if (button.length === 0) {
      return;
    }
    const groupDiv = button[0].parentNode.previousElementSibling;
    // Select the provider list within the mobile group
    const providerList = groupDiv.getElementsByClassName('provider-list')[0];
    const providerText = providerList.textContent.trim();
    // Check if there are other providers besides Apple Pay
    const providers = providerList.textContent.split(',').map(provider => provider.trim());
    const hasOtherProviders = providers.some(provider => provider !== 'Apple Pay');

    // Hide manually created mobile group if there are no other mobile providers and apple pay is not available
    if (!hasOtherProviders && !applePay?.canMakePayment()) {
      groupDiv.style.display = 'none';
    } else if (hasOtherProviders && !applePay?.canMakePayment() && providerText.includes('Apple Pay')) {
      // If there are other mobile providers and Apple Pay is not available, remove Apple Pay title from the list
      providerList.textContent = providerText.replace(/,\s*Apple Pay|Apple Pay,\s*|Apple Pay/g, '').trim();
    }
    //Display Apple Pay button if it is available
    if (button.length > 0 && applePay?.canMakePayment()) {
      button[0].classList.remove('apple-pay');
    }
  }
};
window.initPaytrail = () => {
  const paytrailProviderGroups = document.getElementsByClassName('paytrail-provider-group');
  if (paytrailProviderGroups.length === 1) {
    Array.from(paytrailProviderGroups).map(providerGroup => {
      providerGroup.style = 'display: none;';
      const fields = document.getElementsByClassName('paytrail-woocommerce-payment-fields');
      Array.from(fields).map(field => field.classList.remove('hidden'));
    });
  } else if (paytrailProviderGroups.length > 1) {
    Array.from(paytrailProviderGroups).map(providerGroup => {
      if (providerGroup && !providerGroup.hasAttribute('listenerOnClick')) {
        providerGroup.addEventListener('click', function (e) {
          e.preventDefault();
          if (this.classList.contains('selected')) {
            this.classList.remove('selected');
            this.nextSibling.classList.add('hidden');
            return;
          }
          // Clear active state
          const active = document.getElementsByClassName('paytrail-provider-group selected');
          if (active.length !== 0) {
            active[0].classList.remove('selected');
          }
          // Hide payment fields
          const fields = document.getElementsByClassName('paytrail-woocommerce-payment-fields');
          Array.from(fields).map(field => field.classList.add('hidden'));
          // Show current group
          this.classList.add('selected');
          this.nextSibling.classList.remove('hidden');
          // Use scrolIntoView(alignTo) method
          const closestUl = this.nextSibling.closest('ul');
          closestUl.scrollIntoView(false); // align to the bottom of the scrollable element
        });
        providerGroup.setAttribute('listenerOnClick', 'true');
      }
    });
  }
  const methods = document.getElementsByClassName('paytrail-woocommerce-payment-fields--list-item paytrail-for-woocommerce-tokenized-payment-method');
  if (methods.length > 0) {
    Array.from(methods).map(method => {
      method.addEventListener('click', function (e) {
        e.preventDefault();
        Array.from(methods).map(method => method.classList.remove('selected'));
        let radio = this.childNodes[0].childNodes[0];
        if (typeof radio !== 'undefined') {
          radio.checked = true;
          this.classList.add('selected');
        }
      });
    });
  }
  handleResize();
  handleApplePay();
};
document.addEventListener('DOMContentLoaded', function (event) {
  initPaytrail();
});
window.addEventListener('resize', function () {
  startTime = new Date();
  if (timeout === false) {
    timeout = true;
    setTimeout(handleResize, delta);
  }
});
})();

/******/ })()
;
//# sourceMappingURL=main.js.map