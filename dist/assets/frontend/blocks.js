/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/components/payment-methods.jsx":
/*!**************************************************!*\
  !*** ./assets/js/components/payment-methods.jsx ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PaymentMethods: () => (/* binding */ PaymentMethods)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _provider_group__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./provider-group */ "./assets/js/components/provider-group.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");




const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paytrail_data', {});
const applePayActive = settings.apple_pay_active;
//Hide manually created mobile group if Apple Pay is not supported and there are no other mobile providers
const hasOtherProviders = settings.groups.some(group => group.id === 'mobile' && group.providers.some(provider => provider.id !== 'apple-pay'));
const hideMobileGroup = providerGroup => applePayActive && providerGroup.id === 'mobile' && !hasOtherProviders && !paytrail?.applePayButton?.canMakePayment();
const PaymentMethods = () => {
  // Check if no providers should be displayed
  if (!settings.groups || settings.groups.length === 0) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
      className: "payment_box payment_method_paytrail"
    });
  }

  //Renders terms and conditions HTML.
  const PaytrailTerms = () => {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
      className: "checkout-terms-link",
      dangerouslySetInnerHTML: {
        __html: settings.terms
      }
    });
  };

  // Display provider groups if providers are available
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "payment_box payment_method_paytrail",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(PaytrailTerms, {}), settings.groups.map(providerGroup => hideMobileGroup(providerGroup) ? null : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_provider_group__WEBPACK_IMPORTED_MODULE_2__.ProviderGroup, {
      group: providerGroup
    }, providerGroup.id))]
  });
};

/***/ }),

/***/ "./assets/js/components/paytrail-label.jsx":
/*!*************************************************!*\
  !*** ./assets/js/components/paytrail-label.jsx ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PaytrailLabel: () => (/* binding */ PaytrailLabel),
/* harmony export */   label: () => (/* binding */ label)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");





const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__.getSetting)('paytrail_data', {});
const defaultLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Paytrail Payments', 'woo-gutenberg-products-block');
const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__.decodeEntities)(settings.title) || defaultLabel;

/**
 * Label component
 *
 * @param {*} props Props from payment API.
 */
const PaytrailLabel = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(PaymentMethodLabel, {
    text: label
  });
};

/***/ }),

/***/ "./assets/js/components/provider-group.jsx":
/*!*************************************************!*\
  !*** ./assets/js/components/provider-group.jsx ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ProviderGroup: () => (/* binding */ ProviderGroup)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _provider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./provider */ "./assets/js/components/provider.jsx");
/* harmony import */ var _context_paytrail_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../context/paytrail-context */ "./assets/js/context/paytrail-context.jsx");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");





const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__.getSetting)('paytrail_data', {});
const ProviderGroup = ({
  group
}) => {
  if (!settings.groups || settings.groups === 0) {
    return null;
  }
  const {
    activeProvider
  } = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_paytrail_context__WEBPACK_IMPORTED_MODULE_2__.PaytrailContext);
  const getProviderUniqueId = (provider, index) => `${provider.id}-${index}`;
  const [isOpen, setIsOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(group.providers.some((provider, index) => activeProvider === getProviderUniqueId(provider, index)));
  const toggle = () => setIsOpen(!isOpen);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      className: "paytrail-provider-group",
      onClick: toggle,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("img", {
        src: group.icon,
        className: "provider-group-icon",
        height: 28,
        width: 28
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
        className: "paytrail-provider-group-title",
        children: group.name
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
      className: "provider-list",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("ul", {
        className: "paytrail-woocommerce-payment-fields",
        children: isOpen && group.providers.map((provider, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_provider__WEBPACK_IMPORTED_MODULE_1__.Provider, {
          provider: provider,
          index: index
        }, provider.id))
      })
    })]
  });
};

/***/ }),

/***/ "./assets/js/components/provider.jsx":
/*!*******************************************!*\
  !*** ./assets/js/components/provider.jsx ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Provider: () => (/* binding */ Provider)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_paytrail_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../context/paytrail-context */ "./assets/js/context/paytrail-context.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");


// Provider component to render individual providers

const Provider = ({
  provider,
  index
}) => {
  const {
    activeProvider,
    setActiveProvider
  } = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_paytrail_context__WEBPACK_IMPORTED_MODULE_1__.PaytrailContext);
  const providerUniqueId = `${provider.id}-${index}`;
  const hideApplePay = provider.id === 'apple-pay' && !paytrail?.applePayButton?.canMakePayment();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("li", {
    className: `paytrail-woocommerce-payment-fields--list-item ${hideApplePay ? 'apple-pay' : ''} ${activeProvider === providerUniqueId ? "selected" : ""}`,
    onClick: () => setActiveProvider(providerUniqueId),
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("label", {
      htmlFor: `provider-${provider.id}-${index}`,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("input", {
        type: "radio",
        className: "paytrail-woocommerce-payment-fields--list-item--input",
        name: "payment_provider",
        value: provider.id,
        checked: activeProvider === providerUniqueId,
        onChange: () => setActiveProvider(providerUniqueId)
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("div", {
        className: "paytrail-woocommerce-payment-fields--list-item--wrapper",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("img", {
          src: provider.svg,
          alt: provider.name,
          className: "paytrail-woocommerce-payment-fields--list-item--img"
        })
      })]
    })
  });
};

/***/ }),

/***/ "./assets/js/containers/paytrail-container.jsx":
/*!*****************************************************!*\
  !*** ./assets/js/containers/paytrail-container.jsx ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PaytrailContainer: () => (/* binding */ PaytrailContainer)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_payment_methods__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/payment-methods */ "./assets/js/components/payment-methods.jsx");
/* harmony import */ var _context_paytrail_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/paytrail-context */ "./assets/js/context/paytrail-context.jsx");
/* harmony import */ var _context_with_paytrail__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../context/with-paytrail */ "./assets/js/context/with-paytrail.jsx");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");







const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paytrail_data', {});

/**
 * Content component
 */
const PaytrailContainer = (0,_context_with_paytrail__WEBPACK_IMPORTED_MODULE_4__.withPaytrail)(props => {
  const {
    eventRegistration,
    emitResponse
  } = props;
  const {
    onPaymentSetup
  } = eventRegistration;
  const {
    activeProvider
  } = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(_context_paytrail_context__WEBPACK_IMPORTED_MODULE_3__.PaytrailContext);
  let cleanProvider = activeProvider.replace(/-\d+$/, '');
  if (!settings.groups || settings.groups.length === 0) {
    const defaultProvider = 'paytrail';
    if (!cleanProvider) {
      cleanProvider = defaultProvider;
    }
  }
  const PaytrailStaticComponent = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
    className: "paytrail-static-container",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "paytrail-description",
      children: settings.description || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)("payment providers will open in a new window", "paytrail-for-woocommerce")
    })
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const paymentSetup = onPaymentSetup(async () => {
      return {
        type: emitResponse.responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            payment_provider: cleanProvider || defaultProvider
          }
        }
      };
    });
    return () => {
      paymentSetup();
    };
  }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, activeProvider]);
  if (!settings.groups || settings.groups.length === 0) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(PaytrailStaticComponent, {});
  } else {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_components_payment_methods__WEBPACK_IMPORTED_MODULE_2__.PaymentMethods, {});
  }
});

/***/ }),

/***/ "./assets/js/context/paytrail-context.jsx":
/*!************************************************!*\
  !*** ./assets/js/context/paytrail-context.jsx ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PaytrailContext: () => (/* binding */ PaytrailContext),
/* harmony export */   PaytrailContextProvider: () => (/* binding */ PaytrailContextProvider)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var usehooks_ts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! usehooks-ts */ "./node_modules/usehooks-ts/dist/index.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");



const PaytrailContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)({
  activeProvider: '',
  setActiveProvider: () => {
    throw new Error('No set active provider defined');
  }
});
const PaytrailContextProvider = ({
  children
}) => {
  const [activeProvider, setActiveProvider] = (0,usehooks_ts__WEBPACK_IMPORTED_MODULE_2__.useLocalStorage)('activePaytrailProvider', '');
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(PaytrailContext.Provider, {
    value: {
      activeProvider,
      setActiveProvider
    },
    children: children
  });
};

/***/ }),

/***/ "./assets/js/context/with-paytrail.jsx":
/*!*********************************************!*\
  !*** ./assets/js/context/with-paytrail.jsx ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   withPaytrail: () => (/* binding */ withPaytrail)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _paytrail_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./paytrail-context */ "./assets/js/context/paytrail-context.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");



const withPaytrail = WrappedComponent => {
  return class extends react__WEBPACK_IMPORTED_MODULE_0__.Component {
    render() {
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_paytrail_context__WEBPACK_IMPORTED_MODULE_1__.PaytrailContextProvider, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(WrappedComponent, {
          ...this.props
        })
      });
    }
  };
};

/***/ }),

/***/ "./assets/js/paytrail.js":
/*!*******************************!*\
  !*** ./assets/js/paytrail.js ***!
  \*******************************/
/***/ (() => {

(() => {
  var t = {
      1924: (t, e, r) => {
        "use strict";

        var o = r(210),
          n = r(5559),
          i = n(o("String.prototype.indexOf"));
        t.exports = function (t, e) {
          var r = o(t, !!e);
          return "function" == typeof r && i(t, ".prototype.") > -1 ? n(r) : r;
        };
      },
      5559: (t, e, r) => {
        "use strict";

        var o = r(8612),
          n = r(210),
          i = n("%Function.prototype.apply%"),
          a = n("%Function.prototype.call%"),
          s = n("%Reflect.apply%", !0) || o.call(a, i),
          c = n("%Object.getOwnPropertyDescriptor%", !0),
          u = n("%Object.defineProperty%", !0),
          l = n("%Math.max%");
        if (u) try {
          u({}, "a", {
            value: 1
          });
        } catch (t) {
          u = null;
        }
        t.exports = function (t) {
          var e = s(o, a, arguments);
          if (c && u) {
            var r = c(e, "length");
            r.configurable && u(e, "length", {
              value: 1 + l(0, t.length - (arguments.length - 1))
            });
          }
          return e;
        };
        var p = function () {
          return s(o, i, arguments);
        };
        u ? u(t.exports, "apply", {
          value: p
        }) : t.exports.apply = p;
      },
      4445: t => {
        t.exports = a, a.default = a, a.stable = l, a.stableStringify = l;
        var e = "[...]",
          r = "[Circular]",
          o = [],
          n = [];
        function i() {
          return {
            depthLimit: Number.MAX_SAFE_INTEGER,
            edgesLimit: Number.MAX_SAFE_INTEGER
          };
        }
        function a(t, e, r, a) {
          var s;
          void 0 === a && (a = i()), c(t, "", 0, [], void 0, 0, a);
          try {
            s = 0 === n.length ? JSON.stringify(t, e, r) : JSON.stringify(t, f(e), r);
          } catch (t) {
            return JSON.stringify("[unable to serialize, circular reference is too complex to analyze]");
          } finally {
            for (; 0 !== o.length;) {
              var u = o.pop();
              4 === u.length ? Object.defineProperty(u[0], u[1], u[3]) : u[0][u[1]] = u[2];
            }
          }
          return s;
        }
        function s(t, e, r, i) {
          var a = Object.getOwnPropertyDescriptor(i, r);
          void 0 !== a.get ? a.configurable ? (Object.defineProperty(i, r, {
            value: t
          }), o.push([i, r, e, a])) : n.push([e, r, t]) : (i[r] = t, o.push([i, r, e]));
        }
        function c(t, o, n, i, a, u, l) {
          var p;
          if (u += 1, "object" == typeof t && null !== t) {
            for (p = 0; p < i.length; p++) if (i[p] === t) return void s(r, t, o, a);
            if (void 0 !== l.depthLimit && u > l.depthLimit) return void s(e, t, o, a);
            if (void 0 !== l.edgesLimit && n + 1 > l.edgesLimit) return void s(e, t, o, a);
            if (i.push(t), Array.isArray(t)) for (p = 0; p < t.length; p++) c(t[p], p, p, i, t, u, l);else {
              var f = Object.keys(t);
              for (p = 0; p < f.length; p++) {
                var y = f[p];
                c(t[y], y, p, i, t, u, l);
              }
            }
            i.pop();
          }
        }
        function u(t, e) {
          return t < e ? -1 : t > e ? 1 : 0;
        }
        function l(t, e, r, a) {
          void 0 === a && (a = i());
          var s,
            c = p(t, "", 0, [], void 0, 0, a) || t;
          try {
            s = 0 === n.length ? JSON.stringify(c, e, r) : JSON.stringify(c, f(e), r);
          } catch (t) {
            return JSON.stringify("[unable to serialize, circular reference is too complex to analyze]");
          } finally {
            for (; 0 !== o.length;) {
              var u = o.pop();
              4 === u.length ? Object.defineProperty(u[0], u[1], u[3]) : u[0][u[1]] = u[2];
            }
          }
          return s;
        }
        function p(t, n, i, a, c, l, f) {
          var y;
          if (l += 1, "object" == typeof t && null !== t) {
            for (y = 0; y < a.length; y++) if (a[y] === t) return void s(r, t, n, c);
            try {
              if ("function" == typeof t.toJSON) return;
            } catch (t) {
              return;
            }
            if (void 0 !== f.depthLimit && l > f.depthLimit) return void s(e, t, n, c);
            if (void 0 !== f.edgesLimit && i + 1 > f.edgesLimit) return void s(e, t, n, c);
            if (a.push(t), Array.isArray(t)) for (y = 0; y < t.length; y++) p(t[y], y, y, a, t, l, f);else {
              var h = {},
                d = Object.keys(t).sort(u);
              for (y = 0; y < d.length; y++) {
                var m = d[y];
                p(t[m], m, y, a, t, l, f), h[m] = t[m];
              }
              if (void 0 === c) return h;
              o.push([c, n, t]), c[n] = h;
            }
            a.pop();
          }
        }
        function f(t) {
          return t = void 0 !== t ? t : function (t, e) {
            return e;
          }, function (e, r) {
            if (n.length > 0) for (var o = 0; o < n.length; o++) {
              var i = n[o];
              if (i[1] === e && i[0] === r) {
                r = i[2], n.splice(o, 1);
                break;
              }
            }
            return t.call(this, e, r);
          };
        }
      },
      7648: t => {
        "use strict";

        var e = "Function.prototype.bind called on incompatible ",
          r = Array.prototype.slice,
          o = Object.prototype.toString,
          n = "[object Function]";
        t.exports = function (t) {
          var i = this;
          if ("function" != typeof i || o.call(i) !== n) throw new TypeError(e + i);
          for (var a, s = r.call(arguments, 1), c = function () {
              if (this instanceof a) {
                var e = i.apply(this, s.concat(r.call(arguments)));
                return Object(e) === e ? e : this;
              }
              return i.apply(t, s.concat(r.call(arguments)));
            }, u = Math.max(0, i.length - s.length), l = [], p = 0; p < u; p++) l.push("$" + p);
          if (a = Function("binder", "return function (" + l.join(",") + "){ return binder.apply(this,arguments); }")(c), i.prototype) {
            var f = function () {};
            f.prototype = i.prototype, a.prototype = new f(), f.prototype = null;
          }
          return a;
        };
      },
      8612: (t, e, r) => {
        "use strict";

        var o = r(7648);
        t.exports = Function.prototype.bind || o;
      },
      210: (t, e, r) => {
        "use strict";

        var o,
          n = SyntaxError,
          i = Function,
          a = TypeError,
          s = function (t) {
            try {
              return i('"use strict"; return (' + t + ").constructor;")();
            } catch (t) {}
          },
          c = Object.getOwnPropertyDescriptor;
        if (c) try {
          c({}, "");
        } catch (t) {
          c = null;
        }
        var u = function () {
            throw new a();
          },
          l = c ? function () {
            try {
              return u;
            } catch (t) {
              try {
                return c(arguments, "callee").get;
              } catch (t) {
                return u;
              }
            }
          }() : u,
          p = r(1405)(),
          f = Object.getPrototypeOf || function (t) {
            return t.__proto__;
          },
          y = {},
          h = "undefined" == typeof Uint8Array ? o : f(Uint8Array),
          d = {
            "%AggregateError%": "undefined" == typeof AggregateError ? o : AggregateError,
            "%Array%": Array,
            "%ArrayBuffer%": "undefined" == typeof ArrayBuffer ? o : ArrayBuffer,
            "%ArrayIteratorPrototype%": p ? f([][Symbol.iterator]()) : o,
            "%AsyncFromSyncIteratorPrototype%": o,
            "%AsyncFunction%": y,
            "%AsyncGenerator%": y,
            "%AsyncGeneratorFunction%": y,
            "%AsyncIteratorPrototype%": y,
            "%Atomics%": "undefined" == typeof Atomics ? o : Atomics,
            "%BigInt%": "undefined" == typeof BigInt ? o : BigInt,
            "%Boolean%": Boolean,
            "%DataView%": "undefined" == typeof DataView ? o : DataView,
            "%Date%": Date,
            "%decodeURI%": decodeURI,
            "%decodeURIComponent%": decodeURIComponent,
            "%encodeURI%": encodeURI,
            "%encodeURIComponent%": encodeURIComponent,
            "%Error%": Error,
            "%eval%": eval,
            "%EvalError%": EvalError,
            "%Float32Array%": "undefined" == typeof Float32Array ? o : Float32Array,
            "%Float64Array%": "undefined" == typeof Float64Array ? o : Float64Array,
            "%FinalizationRegistry%": "undefined" == typeof FinalizationRegistry ? o : FinalizationRegistry,
            "%Function%": i,
            "%GeneratorFunction%": y,
            "%Int8Array%": "undefined" == typeof Int8Array ? o : Int8Array,
            "%Int16Array%": "undefined" == typeof Int16Array ? o : Int16Array,
            "%Int32Array%": "undefined" == typeof Int32Array ? o : Int32Array,
            "%isFinite%": isFinite,
            "%isNaN%": isNaN,
            "%IteratorPrototype%": p ? f(f([][Symbol.iterator]())) : o,
            "%JSON%": "object" == typeof JSON ? JSON : o,
            "%Map%": "undefined" == typeof Map ? o : Map,
            "%MapIteratorPrototype%": "undefined" != typeof Map && p ? f(new Map()[Symbol.iterator]()) : o,
            "%Math%": Math,
            "%Number%": Number,
            "%Object%": Object,
            "%parseFloat%": parseFloat,
            "%parseInt%": parseInt,
            "%Promise%": "undefined" == typeof Promise ? o : Promise,
            "%Proxy%": "undefined" == typeof Proxy ? o : Proxy,
            "%RangeError%": RangeError,
            "%ReferenceError%": ReferenceError,
            "%Reflect%": "undefined" == typeof Reflect ? o : Reflect,
            "%RegExp%": RegExp,
            "%Set%": "undefined" == typeof Set ? o : Set,
            "%SetIteratorPrototype%": "undefined" != typeof Set && p ? f(new Set()[Symbol.iterator]()) : o,
            "%SharedArrayBuffer%": "undefined" == typeof SharedArrayBuffer ? o : SharedArrayBuffer,
            "%String%": String,
            "%StringIteratorPrototype%": p ? f(""[Symbol.iterator]()) : o,
            "%Symbol%": p ? Symbol : o,
            "%SyntaxError%": n,
            "%ThrowTypeError%": l,
            "%TypedArray%": h,
            "%TypeError%": a,
            "%Uint8Array%": "undefined" == typeof Uint8Array ? o : Uint8Array,
            "%Uint8ClampedArray%": "undefined" == typeof Uint8ClampedArray ? o : Uint8ClampedArray,
            "%Uint16Array%": "undefined" == typeof Uint16Array ? o : Uint16Array,
            "%Uint32Array%": "undefined" == typeof Uint32Array ? o : Uint32Array,
            "%URIError%": URIError,
            "%WeakMap%": "undefined" == typeof WeakMap ? o : WeakMap,
            "%WeakRef%": "undefined" == typeof WeakRef ? o : WeakRef,
            "%WeakSet%": "undefined" == typeof WeakSet ? o : WeakSet
          },
          m = function t(e) {
            var r;
            if ("%AsyncFunction%" === e) r = s("async function () {}");else if ("%GeneratorFunction%" === e) r = s("function* () {}");else if ("%AsyncGeneratorFunction%" === e) r = s("async function* () {}");else if ("%AsyncGenerator%" === e) {
              var o = t("%AsyncGeneratorFunction%");
              o && (r = o.prototype);
            } else if ("%AsyncIteratorPrototype%" === e) {
              var n = t("%AsyncGenerator%");
              n && (r = f(n.prototype));
            }
            return d[e] = r, r;
          },
          b = {
            "%ArrayBufferPrototype%": ["ArrayBuffer", "prototype"],
            "%ArrayPrototype%": ["Array", "prototype"],
            "%ArrayProto_entries%": ["Array", "prototype", "entries"],
            "%ArrayProto_forEach%": ["Array", "prototype", "forEach"],
            "%ArrayProto_keys%": ["Array", "prototype", "keys"],
            "%ArrayProto_values%": ["Array", "prototype", "values"],
            "%AsyncFunctionPrototype%": ["AsyncFunction", "prototype"],
            "%AsyncGenerator%": ["AsyncGeneratorFunction", "prototype"],
            "%AsyncGeneratorPrototype%": ["AsyncGeneratorFunction", "prototype", "prototype"],
            "%BooleanPrototype%": ["Boolean", "prototype"],
            "%DataViewPrototype%": ["DataView", "prototype"],
            "%DatePrototype%": ["Date", "prototype"],
            "%ErrorPrototype%": ["Error", "prototype"],
            "%EvalErrorPrototype%": ["EvalError", "prototype"],
            "%Float32ArrayPrototype%": ["Float32Array", "prototype"],
            "%Float64ArrayPrototype%": ["Float64Array", "prototype"],
            "%FunctionPrototype%": ["Function", "prototype"],
            "%Generator%": ["GeneratorFunction", "prototype"],
            "%GeneratorPrototype%": ["GeneratorFunction", "prototype", "prototype"],
            "%Int8ArrayPrototype%": ["Int8Array", "prototype"],
            "%Int16ArrayPrototype%": ["Int16Array", "prototype"],
            "%Int32ArrayPrototype%": ["Int32Array", "prototype"],
            "%JSONParse%": ["JSON", "parse"],
            "%JSONStringify%": ["JSON", "stringify"],
            "%MapPrototype%": ["Map", "prototype"],
            "%NumberPrototype%": ["Number", "prototype"],
            "%ObjectPrototype%": ["Object", "prototype"],
            "%ObjProto_toString%": ["Object", "prototype", "toString"],
            "%ObjProto_valueOf%": ["Object", "prototype", "valueOf"],
            "%PromisePrototype%": ["Promise", "prototype"],
            "%PromiseProto_then%": ["Promise", "prototype", "then"],
            "%Promise_all%": ["Promise", "all"],
            "%Promise_reject%": ["Promise", "reject"],
            "%Promise_resolve%": ["Promise", "resolve"],
            "%RangeErrorPrototype%": ["RangeError", "prototype"],
            "%ReferenceErrorPrototype%": ["ReferenceError", "prototype"],
            "%RegExpPrototype%": ["RegExp", "prototype"],
            "%SetPrototype%": ["Set", "prototype"],
            "%SharedArrayBufferPrototype%": ["SharedArrayBuffer", "prototype"],
            "%StringPrototype%": ["String", "prototype"],
            "%SymbolPrototype%": ["Symbol", "prototype"],
            "%SyntaxErrorPrototype%": ["SyntaxError", "prototype"],
            "%TypedArrayPrototype%": ["TypedArray", "prototype"],
            "%TypeErrorPrototype%": ["TypeError", "prototype"],
            "%Uint8ArrayPrototype%": ["Uint8Array", "prototype"],
            "%Uint8ClampedArrayPrototype%": ["Uint8ClampedArray", "prototype"],
            "%Uint16ArrayPrototype%": ["Uint16Array", "prototype"],
            "%Uint32ArrayPrototype%": ["Uint32Array", "prototype"],
            "%URIErrorPrototype%": ["URIError", "prototype"],
            "%WeakMapPrototype%": ["WeakMap", "prototype"],
            "%WeakSetPrototype%": ["WeakSet", "prototype"]
          },
          g = r(8612),
          v = r(7642),
          w = g.call(Function.call, Array.prototype.concat),
          _ = g.call(Function.apply, Array.prototype.splice),
          S = g.call(Function.call, String.prototype.replace),
          A = g.call(Function.call, String.prototype.slice),
          O = g.call(Function.call, RegExp.prototype.exec),
          E = /[^%.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|%$))/g,
          P = /\\(\\)?/g,
          j = function (t) {
            var e = A(t, 0, 1),
              r = A(t, -1);
            if ("%" === e && "%" !== r) throw new n("invalid intrinsic syntax, expected closing `%`");
            if ("%" === r && "%" !== e) throw new n("invalid intrinsic syntax, expected opening `%`");
            var o = [];
            return S(t, E, function (t, e, r, n) {
              o[o.length] = r ? S(n, P, "$1") : e || t;
            }), o;
          },
          x = function (t, e) {
            var r,
              o = t;
            if (v(b, o) && (o = "%" + (r = b[o])[0] + "%"), v(d, o)) {
              var i = d[o];
              if (i === y && (i = m(o)), void 0 === i && !e) throw new a("intrinsic " + t + " exists, but is not available. Please file an issue!");
              return {
                alias: r,
                name: o,
                value: i
              };
            }
            throw new n("intrinsic " + t + " does not exist!");
          };
        t.exports = function (t, e) {
          if ("string" != typeof t || 0 === t.length) throw new a("intrinsic name must be a non-empty string");
          if (arguments.length > 1 && "boolean" != typeof e) throw new a('"allowMissing" argument must be a boolean');
          if (null === O(/^%?[^%]*%?$/g, t)) throw new n("`%` may not be present anywhere but at the beginning and end of the intrinsic name");
          var r = j(t),
            o = r.length > 0 ? r[0] : "",
            i = x("%" + o + "%", e),
            s = i.name,
            u = i.value,
            l = !1,
            p = i.alias;
          p && (o = p[0], _(r, w([0, 1], p)));
          for (var f = 1, y = !0; f < r.length; f += 1) {
            var h = r[f],
              m = A(h, 0, 1),
              b = A(h, -1);
            if (('"' === m || "'" === m || "`" === m || '"' === b || "'" === b || "`" === b) && m !== b) throw new n("property names with quotes must have matching quotes");
            if ("constructor" !== h && y || (l = !0), v(d, s = "%" + (o += "." + h) + "%")) u = d[s];else if (null != u) {
              if (!(h in u)) {
                if (!e) throw new a("base intrinsic for " + t + " exists, but the property is not available.");
                return;
              }
              if (c && f + 1 >= r.length) {
                var g = c(u, h);
                u = (y = !!g) && "get" in g && !("originalValue" in g.get) ? g.get : u[h];
              } else y = v(u, h), u = u[h];
              y && !l && (d[s] = u);
            }
          }
          return u;
        };
      },
      1405: (t, e, r) => {
        "use strict";

        var o = "undefined" != typeof Symbol && Symbol,
          n = r(5419);
        t.exports = function () {
          return "function" == typeof o && "function" == typeof Symbol && "symbol" == typeof o("foo") && "symbol" == typeof Symbol("bar") && n();
        };
      },
      5419: t => {
        "use strict";

        t.exports = function () {
          if ("function" != typeof Symbol || "function" != typeof Object.getOwnPropertySymbols) return !1;
          if ("symbol" == typeof Symbol.iterator) return !0;
          var t = {},
            e = Symbol("test"),
            r = Object(e);
          if ("string" == typeof e) return !1;
          if ("[object Symbol]" !== Object.prototype.toString.call(e)) return !1;
          if ("[object Symbol]" !== Object.prototype.toString.call(r)) return !1;
          for (e in t[e] = 42, t) return !1;
          if ("function" == typeof Object.keys && 0 !== Object.keys(t).length) return !1;
          if ("function" == typeof Object.getOwnPropertyNames && 0 !== Object.getOwnPropertyNames(t).length) return !1;
          var o = Object.getOwnPropertySymbols(t);
          if (1 !== o.length || o[0] !== e) return !1;
          if (!Object.prototype.propertyIsEnumerable.call(t, e)) return !1;
          if ("function" == typeof Object.getOwnPropertyDescriptor) {
            var n = Object.getOwnPropertyDescriptor(t, e);
            if (42 !== n.value || !0 !== n.enumerable) return !1;
          }
          return !0;
        };
      },
      7642: (t, e, r) => {
        "use strict";

        var o = r(8612);
        t.exports = o.call(Function.call, Object.prototype.hasOwnProperty);
      },
      631: (t, e, r) => {
        var o = "function" == typeof Map && Map.prototype,
          n = Object.getOwnPropertyDescriptor && o ? Object.getOwnPropertyDescriptor(Map.prototype, "size") : null,
          i = o && n && "function" == typeof n.get ? n.get : null,
          a = o && Map.prototype.forEach,
          s = "function" == typeof Set && Set.prototype,
          c = Object.getOwnPropertyDescriptor && s ? Object.getOwnPropertyDescriptor(Set.prototype, "size") : null,
          u = s && c && "function" == typeof c.get ? c.get : null,
          l = s && Set.prototype.forEach,
          p = "function" == typeof WeakMap && WeakMap.prototype ? WeakMap.prototype.has : null,
          f = "function" == typeof WeakSet && WeakSet.prototype ? WeakSet.prototype.has : null,
          y = "function" == typeof WeakRef && WeakRef.prototype ? WeakRef.prototype.deref : null,
          h = Boolean.prototype.valueOf,
          d = Object.prototype.toString,
          m = Function.prototype.toString,
          b = String.prototype.match,
          g = String.prototype.slice,
          v = String.prototype.replace,
          w = String.prototype.toUpperCase,
          _ = String.prototype.toLowerCase,
          S = RegExp.prototype.test,
          A = Array.prototype.concat,
          O = Array.prototype.join,
          E = Array.prototype.slice,
          P = Math.floor,
          j = "function" == typeof BigInt ? BigInt.prototype.valueOf : null,
          x = Object.getOwnPropertySymbols,
          T = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? Symbol.prototype.toString : null,
          k = "function" == typeof Symbol && "object" == typeof Symbol.iterator,
          R = "function" == typeof Symbol && Symbol.toStringTag && (Symbol.toStringTag, 1) ? Symbol.toStringTag : null,
          C = Object.prototype.propertyIsEnumerable,
          N = ("function" == typeof Reflect ? Reflect.getPrototypeOf : Object.getPrototypeOf) || ([].__proto__ === Array.prototype ? function (t) {
            return t.__proto__;
          } : null);
        function I(t, e) {
          if (t === 1 / 0 || t === -1 / 0 || t != t || t && t > -1e3 && t < 1e3 || S.call(/e/, e)) return e;
          var r = /[0-9](?=(?:[0-9]{3})+(?![0-9]))/g;
          if ("number" == typeof t) {
            var o = t < 0 ? -P(-t) : P(t);
            if (o !== t) {
              var n = String(o),
                i = g.call(e, n.length + 1);
              return v.call(n, r, "$&_") + "." + v.call(v.call(i, /([0-9]{3})/g, "$&_"), /_$/, "");
            }
          }
          return v.call(e, r, "$&_");
        }
        var L = r(4654),
          D = L.custom,
          U = G(D) ? D : null;
        function M(t, e, r) {
          var o = "double" === (r.quoteStyle || e) ? '"' : "'";
          return o + t + o;
        }
        function F(t) {
          return v.call(String(t), /"/g, "&quot;");
        }
        function q(t) {
          return !("[object Array]" !== z(t) || R && "object" == typeof t && R in t);
        }
        function B(t) {
          return !("[object RegExp]" !== z(t) || R && "object" == typeof t && R in t);
        }
        function G(t) {
          if (k) return t && "object" == typeof t && t instanceof Symbol;
          if ("symbol" == typeof t) return !0;
          if (!t || "object" != typeof t || !T) return !1;
          try {
            return T.call(t), !0;
          } catch (t) {}
          return !1;
        }
        t.exports = function t(e, r, o, n) {
          var s = r || {};
          if (W(s, "quoteStyle") && "single" !== s.quoteStyle && "double" !== s.quoteStyle) throw new TypeError('option "quoteStyle" must be "single" or "double"');
          if (W(s, "maxStringLength") && ("number" == typeof s.maxStringLength ? s.maxStringLength < 0 && s.maxStringLength !== 1 / 0 : null !== s.maxStringLength)) throw new TypeError('option "maxStringLength", if provided, must be a positive integer, Infinity, or `null`');
          var c = !W(s, "customInspect") || s.customInspect;
          if ("boolean" != typeof c && "symbol" !== c) throw new TypeError("option \"customInspect\", if provided, must be `true`, `false`, or `'symbol'`");
          if (W(s, "indent") && null !== s.indent && "\t" !== s.indent && !(parseInt(s.indent, 10) === s.indent && s.indent > 0)) throw new TypeError('option "indent" must be "\\t", an integer > 0, or `null`');
          if (W(s, "numericSeparator") && "boolean" != typeof s.numericSeparator) throw new TypeError('option "numericSeparator", if provided, must be `true` or `false`');
          var d = s.numericSeparator;
          if (void 0 === e) return "undefined";
          if (null === e) return "null";
          if ("boolean" == typeof e) return e ? "true" : "false";
          if ("string" == typeof e) return V(e, s);
          if ("number" == typeof e) {
            if (0 === e) return 1 / 0 / e > 0 ? "0" : "-0";
            var w = String(e);
            return d ? I(e, w) : w;
          }
          if ("bigint" == typeof e) {
            var S = String(e) + "n";
            return d ? I(e, S) : S;
          }
          var P = void 0 === s.depth ? 5 : s.depth;
          if (void 0 === o && (o = 0), o >= P && P > 0 && "object" == typeof e) return q(e) ? "[Array]" : "[Object]";
          var x,
            D = function (t, e) {
              var r;
              if ("\t" === t.indent) r = "\t";else {
                if (!("number" == typeof t.indent && t.indent > 0)) return null;
                r = O.call(Array(t.indent + 1), " ");
              }
              return {
                base: r,
                prev: O.call(Array(e + 1), r)
              };
            }(s, o);
          if (void 0 === n) n = [];else if ($(n, e) >= 0) return "[Circular]";
          function H(e, r, i) {
            if (r && (n = E.call(n)).push(r), i) {
              var a = {
                depth: s.depth
              };
              return W(s, "quoteStyle") && (a.quoteStyle = s.quoteStyle), t(e, a, o + 1, n);
            }
            return t(e, s, o + 1, n);
          }
          if ("function" == typeof e && !B(e)) {
            var J = function (t) {
                if (t.name) return t.name;
                var e = b.call(m.call(t), /^function\s*([\w$]+)/);
                return e ? e[1] : null;
              }(e),
              tt = Z(e, H);
            return "[Function" + (J ? ": " + J : " (anonymous)") + "]" + (tt.length > 0 ? " { " + O.call(tt, ", ") + " }" : "");
          }
          if (G(e)) {
            var et = k ? v.call(String(e), /^(Symbol\(.*\))_[^)]*$/, "$1") : T.call(e);
            return "object" != typeof e || k ? et : Q(et);
          }
          if ((x = e) && "object" == typeof x && ("undefined" != typeof HTMLElement && x instanceof HTMLElement || "string" == typeof x.nodeName && "function" == typeof x.getAttribute)) {
            for (var rt = "<" + _.call(String(e.nodeName)), ot = e.attributes || [], nt = 0; nt < ot.length; nt++) rt += " " + ot[nt].name + "=" + M(F(ot[nt].value), "double", s);
            return rt += ">", e.childNodes && e.childNodes.length && (rt += "..."), rt + "</" + _.call(String(e.nodeName)) + ">";
          }
          if (q(e)) {
            if (0 === e.length) return "[]";
            var it = Z(e, H);
            return D && !function (t) {
              for (var e = 0; e < t.length; e++) if ($(t[e], "\n") >= 0) return !1;
              return !0;
            }(it) ? "[" + X(it, D) + "]" : "[ " + O.call(it, ", ") + " ]";
          }
          if (function (t) {
            return !("[object Error]" !== z(t) || R && "object" == typeof t && R in t);
          }(e)) {
            var at = Z(e, H);
            return "cause" in Error.prototype || !("cause" in e) || C.call(e, "cause") ? 0 === at.length ? "[" + String(e) + "]" : "{ [" + String(e) + "] " + O.call(at, ", ") + " }" : "{ [" + String(e) + "] " + O.call(A.call("[cause]: " + H(e.cause), at), ", ") + " }";
          }
          if ("object" == typeof e && c) {
            if (U && "function" == typeof e[U] && L) return L(e, {
              depth: P - o
            });
            if ("symbol" !== c && "function" == typeof e.inspect) return e.inspect();
          }
          if (function (t) {
            if (!i || !t || "object" != typeof t) return !1;
            try {
              i.call(t);
              try {
                u.call(t);
              } catch (t) {
                return !0;
              }
              return t instanceof Map;
            } catch (t) {}
            return !1;
          }(e)) {
            var st = [];
            return a.call(e, function (t, r) {
              st.push(H(r, e, !0) + " => " + H(t, e));
            }), K("Map", i.call(e), st, D);
          }
          if (function (t) {
            if (!u || !t || "object" != typeof t) return !1;
            try {
              u.call(t);
              try {
                i.call(t);
              } catch (t) {
                return !0;
              }
              return t instanceof Set;
            } catch (t) {}
            return !1;
          }(e)) {
            var ct = [];
            return l.call(e, function (t) {
              ct.push(H(t, e));
            }), K("Set", u.call(e), ct, D);
          }
          if (function (t) {
            if (!p || !t || "object" != typeof t) return !1;
            try {
              p.call(t, p);
              try {
                f.call(t, f);
              } catch (t) {
                return !0;
              }
              return t instanceof WeakMap;
            } catch (t) {}
            return !1;
          }(e)) return Y("WeakMap");
          if (function (t) {
            if (!f || !t || "object" != typeof t) return !1;
            try {
              f.call(t, f);
              try {
                p.call(t, p);
              } catch (t) {
                return !0;
              }
              return t instanceof WeakSet;
            } catch (t) {}
            return !1;
          }(e)) return Y("WeakSet");
          if (function (t) {
            if (!y || !t || "object" != typeof t) return !1;
            try {
              return y.call(t), !0;
            } catch (t) {}
            return !1;
          }(e)) return Y("WeakRef");
          if (function (t) {
            return !("[object Number]" !== z(t) || R && "object" == typeof t && R in t);
          }(e)) return Q(H(Number(e)));
          if (function (t) {
            if (!t || "object" != typeof t || !j) return !1;
            try {
              return j.call(t), !0;
            } catch (t) {}
            return !1;
          }(e)) return Q(H(j.call(e)));
          if (function (t) {
            return !("[object Boolean]" !== z(t) || R && "object" == typeof t && R in t);
          }(e)) return Q(h.call(e));
          if (function (t) {
            return !("[object String]" !== z(t) || R && "object" == typeof t && R in t);
          }(e)) return Q(H(String(e)));
          if (!function (t) {
            return !("[object Date]" !== z(t) || R && "object" == typeof t && R in t);
          }(e) && !B(e)) {
            var ut = Z(e, H),
              lt = N ? N(e) === Object.prototype : e instanceof Object || e.constructor === Object,
              pt = e instanceof Object ? "" : "null prototype",
              ft = !lt && R && Object(e) === e && R in e ? g.call(z(e), 8, -1) : pt ? "Object" : "",
              yt = (lt || "function" != typeof e.constructor ? "" : e.constructor.name ? e.constructor.name + " " : "") + (ft || pt ? "[" + O.call(A.call([], ft || [], pt || []), ": ") + "] " : "");
            return 0 === ut.length ? yt + "{}" : D ? yt + "{" + X(ut, D) + "}" : yt + "{ " + O.call(ut, ", ") + " }";
          }
          return String(e);
        };
        var H = Object.prototype.hasOwnProperty || function (t) {
          return t in this;
        };
        function W(t, e) {
          return H.call(t, e);
        }
        function z(t) {
          return d.call(t);
        }
        function $(t, e) {
          if (t.indexOf) return t.indexOf(e);
          for (var r = 0, o = t.length; r < o; r++) if (t[r] === e) return r;
          return -1;
        }
        function V(t, e) {
          if (t.length > e.maxStringLength) {
            var r = t.length - e.maxStringLength,
              o = "... " + r + " more character" + (r > 1 ? "s" : "");
            return V(g.call(t, 0, e.maxStringLength), e) + o;
          }
          return M(v.call(v.call(t, /(['\\])/g, "\\$1"), /[\x00-\x1f]/g, J), "single", e);
        }
        function J(t) {
          var e = t.charCodeAt(0),
            r = {
              8: "b",
              9: "t",
              10: "n",
              12: "f",
              13: "r"
            }[e];
          return r ? "\\" + r : "\\x" + (e < 16 ? "0" : "") + w.call(e.toString(16));
        }
        function Q(t) {
          return "Object(" + t + ")";
        }
        function Y(t) {
          return t + " { ? }";
        }
        function K(t, e, r, o) {
          return t + " (" + e + ") {" + (o ? X(r, o) : O.call(r, ", ")) + "}";
        }
        function X(t, e) {
          if (0 === t.length) return "";
          var r = "\n" + e.prev + e.base;
          return r + O.call(t, "," + r) + "\n" + e.prev;
        }
        function Z(t, e) {
          var r = q(t),
            o = [];
          if (r) {
            o.length = t.length;
            for (var n = 0; n < t.length; n++) o[n] = W(t, n) ? e(t[n], t) : "";
          }
          var i,
            a = "function" == typeof x ? x(t) : [];
          if (k) {
            i = {};
            for (var s = 0; s < a.length; s++) i["$" + a[s]] = a[s];
          }
          for (var c in t) W(t, c) && (r && String(Number(c)) === c && c < t.length || k && i["$" + c] instanceof Symbol || (S.call(/[^\w$]/, c) ? o.push(e(c, t) + ": " + e(t[c], t)) : o.push(c + ": " + e(t[c], t))));
          if ("function" == typeof x) for (var u = 0; u < a.length; u++) C.call(t, a[u]) && o.push("[" + e(a[u]) + "]: " + e(t[a[u]], t));
          return o;
        }
      },
      5798: t => {
        "use strict";

        var e = String.prototype.replace,
          r = /%20/g,
          o = "RFC3986";
        t.exports = {
          default: o,
          formatters: {
            RFC1738: function (t) {
              return e.call(t, r, "+");
            },
            RFC3986: function (t) {
              return String(t);
            }
          },
          RFC1738: "RFC1738",
          RFC3986: o
        };
      },
      129: (t, e, r) => {
        "use strict";

        var o = r(8261),
          n = r(5235),
          i = r(5798);
        t.exports = {
          formats: i,
          parse: n,
          stringify: o
        };
      },
      5235: (t, e, r) => {
        "use strict";

        var o = r(2769),
          n = Object.prototype.hasOwnProperty,
          i = Array.isArray,
          a = {
            allowDots: !1,
            allowPrototypes: !1,
            allowSparse: !1,
            arrayLimit: 20,
            charset: "utf-8",
            charsetSentinel: !1,
            comma: !1,
            decoder: o.decode,
            delimiter: "&",
            depth: 5,
            ignoreQueryPrefix: !1,
            interpretNumericEntities: !1,
            parameterLimit: 1e3,
            parseArrays: !0,
            plainObjects: !1,
            strictNullHandling: !1
          },
          s = function (t) {
            return t.replace(/&#(\d+);/g, function (t, e) {
              return String.fromCharCode(parseInt(e, 10));
            });
          },
          c = function (t, e) {
            return t && "string" == typeof t && e.comma && t.indexOf(",") > -1 ? t.split(",") : t;
          },
          u = function (t, e, r, o) {
            if (t) {
              var i = r.allowDots ? t.replace(/\.([^.[]+)/g, "[$1]") : t,
                a = /(\[[^[\]]*])/g,
                s = r.depth > 0 && /(\[[^[\]]*])/.exec(i),
                u = s ? i.slice(0, s.index) : i,
                l = [];
              if (u) {
                if (!r.plainObjects && n.call(Object.prototype, u) && !r.allowPrototypes) return;
                l.push(u);
              }
              for (var p = 0; r.depth > 0 && null !== (s = a.exec(i)) && p < r.depth;) {
                if (p += 1, !r.plainObjects && n.call(Object.prototype, s[1].slice(1, -1)) && !r.allowPrototypes) return;
                l.push(s[1]);
              }
              return s && l.push("[" + i.slice(s.index) + "]"), function (t, e, r, o) {
                for (var n = o ? e : c(e, r), i = t.length - 1; i >= 0; --i) {
                  var a,
                    s = t[i];
                  if ("[]" === s && r.parseArrays) a = [].concat(n);else {
                    a = r.plainObjects ? Object.create(null) : {};
                    var u = "[" === s.charAt(0) && "]" === s.charAt(s.length - 1) ? s.slice(1, -1) : s,
                      l = parseInt(u, 10);
                    r.parseArrays || "" !== u ? !isNaN(l) && s !== u && String(l) === u && l >= 0 && r.parseArrays && l <= r.arrayLimit ? (a = [])[l] = n : "__proto__" !== u && (a[u] = n) : a = {
                      0: n
                    };
                  }
                  n = a;
                }
                return n;
              }(l, e, r, o);
            }
          };
        t.exports = function (t, e) {
          var r = function (t) {
            if (!t) return a;
            if (null !== t.decoder && void 0 !== t.decoder && "function" != typeof t.decoder) throw new TypeError("Decoder has to be a function.");
            if (void 0 !== t.charset && "utf-8" !== t.charset && "iso-8859-1" !== t.charset) throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
            var e = void 0 === t.charset ? a.charset : t.charset;
            return {
              allowDots: void 0 === t.allowDots ? a.allowDots : !!t.allowDots,
              allowPrototypes: "boolean" == typeof t.allowPrototypes ? t.allowPrototypes : a.allowPrototypes,
              allowSparse: "boolean" == typeof t.allowSparse ? t.allowSparse : a.allowSparse,
              arrayLimit: "number" == typeof t.arrayLimit ? t.arrayLimit : a.arrayLimit,
              charset: e,
              charsetSentinel: "boolean" == typeof t.charsetSentinel ? t.charsetSentinel : a.charsetSentinel,
              comma: "boolean" == typeof t.comma ? t.comma : a.comma,
              decoder: "function" == typeof t.decoder ? t.decoder : a.decoder,
              delimiter: "string" == typeof t.delimiter || o.isRegExp(t.delimiter) ? t.delimiter : a.delimiter,
              depth: "number" == typeof t.depth || !1 === t.depth ? +t.depth : a.depth,
              ignoreQueryPrefix: !0 === t.ignoreQueryPrefix,
              interpretNumericEntities: "boolean" == typeof t.interpretNumericEntities ? t.interpretNumericEntities : a.interpretNumericEntities,
              parameterLimit: "number" == typeof t.parameterLimit ? t.parameterLimit : a.parameterLimit,
              parseArrays: !1 !== t.parseArrays,
              plainObjects: "boolean" == typeof t.plainObjects ? t.plainObjects : a.plainObjects,
              strictNullHandling: "boolean" == typeof t.strictNullHandling ? t.strictNullHandling : a.strictNullHandling
            };
          }(e);
          if ("" === t || null == t) return r.plainObjects ? Object.create(null) : {};
          for (var l = "string" == typeof t ? function (t, e) {
              var r,
                u = {
                  __proto__: null
                },
                l = e.ignoreQueryPrefix ? t.replace(/^\?/, "") : t,
                p = e.parameterLimit === 1 / 0 ? void 0 : e.parameterLimit,
                f = l.split(e.delimiter, p),
                y = -1,
                h = e.charset;
              if (e.charsetSentinel) for (r = 0; r < f.length; ++r) 0 === f[r].indexOf("utf8=") && ("utf8=%E2%9C%93" === f[r] ? h = "utf-8" : "utf8=%26%2310003%3B" === f[r] && (h = "iso-8859-1"), y = r, r = f.length);
              for (r = 0; r < f.length; ++r) if (r !== y) {
                var d,
                  m,
                  b = f[r],
                  g = b.indexOf("]="),
                  v = -1 === g ? b.indexOf("=") : g + 1;
                -1 === v ? (d = e.decoder(b, a.decoder, h, "key"), m = e.strictNullHandling ? null : "") : (d = e.decoder(b.slice(0, v), a.decoder, h, "key"), m = o.maybeMap(c(b.slice(v + 1), e), function (t) {
                  return e.decoder(t, a.decoder, h, "value");
                })), m && e.interpretNumericEntities && "iso-8859-1" === h && (m = s(m)), b.indexOf("[]=") > -1 && (m = i(m) ? [m] : m), n.call(u, d) ? u[d] = o.combine(u[d], m) : u[d] = m;
              }
              return u;
            }(t, r) : t, p = r.plainObjects ? Object.create(null) : {}, f = Object.keys(l), y = 0; y < f.length; ++y) {
            var h = f[y],
              d = u(h, l[h], r, "string" == typeof t);
            p = o.merge(p, d, r);
          }
          return !0 === r.allowSparse ? p : o.compact(p);
        };
      },
      8261: (t, e, r) => {
        "use strict";

        var o = r(7478),
          n = r(2769),
          i = r(5798),
          a = Object.prototype.hasOwnProperty,
          s = {
            brackets: function (t) {
              return t + "[]";
            },
            comma: "comma",
            indices: function (t, e) {
              return t + "[" + e + "]";
            },
            repeat: function (t) {
              return t;
            }
          },
          c = Array.isArray,
          u = Array.prototype.push,
          l = function (t, e) {
            u.apply(t, c(e) ? e : [e]);
          },
          p = Date.prototype.toISOString,
          f = i.default,
          y = {
            addQueryPrefix: !1,
            allowDots: !1,
            charset: "utf-8",
            charsetSentinel: !1,
            delimiter: "&",
            encode: !0,
            encoder: n.encode,
            encodeValuesOnly: !1,
            format: f,
            formatter: i.formatters[f],
            indices: !1,
            serializeDate: function (t) {
              return p.call(t);
            },
            skipNulls: !1,
            strictNullHandling: !1
          },
          h = {},
          d = function t(e, r, i, a, s, u, p, f, d, m, b, g, v, w, _, S) {
            for (var A, O = e, E = S, P = 0, j = !1; void 0 !== (E = E.get(h)) && !j;) {
              var x = E.get(e);
              if (P += 1, void 0 !== x) {
                if (x === P) throw new RangeError("Cyclic object value");
                j = !0;
              }
              void 0 === E.get(h) && (P = 0);
            }
            if ("function" == typeof f ? O = f(r, O) : O instanceof Date ? O = b(O) : "comma" === i && c(O) && (O = n.maybeMap(O, function (t) {
              return t instanceof Date ? b(t) : t;
            })), null === O) {
              if (s) return p && !w ? p(r, y.encoder, _, "key", g) : r;
              O = "";
            }
            if ("string" == typeof (A = O) || "number" == typeof A || "boolean" == typeof A || "symbol" == typeof A || "bigint" == typeof A || n.isBuffer(O)) return p ? [v(w ? r : p(r, y.encoder, _, "key", g)) + "=" + v(p(O, y.encoder, _, "value", g))] : [v(r) + "=" + v(String(O))];
            var T,
              k = [];
            if (void 0 === O) return k;
            if ("comma" === i && c(O)) w && p && (O = n.maybeMap(O, p)), T = [{
              value: O.length > 0 ? O.join(",") || null : void 0
            }];else if (c(f)) T = f;else {
              var R = Object.keys(O);
              T = d ? R.sort(d) : R;
            }
            for (var C = a && c(O) && 1 === O.length ? r + "[]" : r, N = 0; N < T.length; ++N) {
              var I = T[N],
                L = "object" == typeof I && void 0 !== I.value ? I.value : O[I];
              if (!u || null !== L) {
                var D = c(O) ? "function" == typeof i ? i(C, I) : C : C + (m ? "." + I : "[" + I + "]");
                S.set(e, P);
                var U = o();
                U.set(h, S), l(k, t(L, D, i, a, s, u, "comma" === i && w && c(O) ? null : p, f, d, m, b, g, v, w, _, U));
              }
            }
            return k;
          };
        t.exports = function (t, e) {
          var r,
            n = t,
            u = function (t) {
              if (!t) return y;
              if (null !== t.encoder && void 0 !== t.encoder && "function" != typeof t.encoder) throw new TypeError("Encoder has to be a function.");
              var e = t.charset || y.charset;
              if (void 0 !== t.charset && "utf-8" !== t.charset && "iso-8859-1" !== t.charset) throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
              var r = i.default;
              if (void 0 !== t.format) {
                if (!a.call(i.formatters, t.format)) throw new TypeError("Unknown format option provided.");
                r = t.format;
              }
              var o = i.formatters[r],
                n = y.filter;
              return ("function" == typeof t.filter || c(t.filter)) && (n = t.filter), {
                addQueryPrefix: "boolean" == typeof t.addQueryPrefix ? t.addQueryPrefix : y.addQueryPrefix,
                allowDots: void 0 === t.allowDots ? y.allowDots : !!t.allowDots,
                charset: e,
                charsetSentinel: "boolean" == typeof t.charsetSentinel ? t.charsetSentinel : y.charsetSentinel,
                delimiter: void 0 === t.delimiter ? y.delimiter : t.delimiter,
                encode: "boolean" == typeof t.encode ? t.encode : y.encode,
                encoder: "function" == typeof t.encoder ? t.encoder : y.encoder,
                encodeValuesOnly: "boolean" == typeof t.encodeValuesOnly ? t.encodeValuesOnly : y.encodeValuesOnly,
                filter: n,
                format: r,
                formatter: o,
                serializeDate: "function" == typeof t.serializeDate ? t.serializeDate : y.serializeDate,
                skipNulls: "boolean" == typeof t.skipNulls ? t.skipNulls : y.skipNulls,
                sort: "function" == typeof t.sort ? t.sort : null,
                strictNullHandling: "boolean" == typeof t.strictNullHandling ? t.strictNullHandling : y.strictNullHandling
              };
            }(e);
          "function" == typeof u.filter ? n = (0, u.filter)("", n) : c(u.filter) && (r = u.filter);
          var p,
            f = [];
          if ("object" != typeof n || null === n) return "";
          p = e && e.arrayFormat in s ? e.arrayFormat : e && "indices" in e ? e.indices ? "indices" : "repeat" : "indices";
          var h = s[p];
          if (e && "commaRoundTrip" in e && "boolean" != typeof e.commaRoundTrip) throw new TypeError("`commaRoundTrip` must be a boolean, or absent");
          var m = "comma" === h && e && e.commaRoundTrip;
          r || (r = Object.keys(n)), u.sort && r.sort(u.sort);
          for (var b = o(), g = 0; g < r.length; ++g) {
            var v = r[g];
            u.skipNulls && null === n[v] || l(f, d(n[v], v, h, m, u.strictNullHandling, u.skipNulls, u.encode ? u.encoder : null, u.filter, u.sort, u.allowDots, u.serializeDate, u.format, u.formatter, u.encodeValuesOnly, u.charset, b));
          }
          var w = f.join(u.delimiter),
            _ = !0 === u.addQueryPrefix ? "?" : "";
          return u.charsetSentinel && ("iso-8859-1" === u.charset ? _ += "utf8=%26%2310003%3B&" : _ += "utf8=%E2%9C%93&"), w.length > 0 ? _ + w : "";
        };
      },
      2769: (t, e, r) => {
        "use strict";

        var o = r(5798),
          n = Object.prototype.hasOwnProperty,
          i = Array.isArray,
          a = function () {
            for (var t = [], e = 0; e < 256; ++e) t.push("%" + ((e < 16 ? "0" : "") + e.toString(16)).toUpperCase());
            return t;
          }(),
          s = function (t, e) {
            for (var r = e && e.plainObjects ? Object.create(null) : {}, o = 0; o < t.length; ++o) void 0 !== t[o] && (r[o] = t[o]);
            return r;
          };
        t.exports = {
          arrayToObject: s,
          assign: function (t, e) {
            return Object.keys(e).reduce(function (t, r) {
              return t[r] = e[r], t;
            }, t);
          },
          combine: function (t, e) {
            return [].concat(t, e);
          },
          compact: function (t) {
            for (var e = [{
                obj: {
                  o: t
                },
                prop: "o"
              }], r = [], o = 0; o < e.length; ++o) for (var n = e[o], a = n.obj[n.prop], s = Object.keys(a), c = 0; c < s.length; ++c) {
              var u = s[c],
                l = a[u];
              "object" == typeof l && null !== l && -1 === r.indexOf(l) && (e.push({
                obj: a,
                prop: u
              }), r.push(l));
            }
            return function (t) {
              for (; t.length > 1;) {
                var e = t.pop(),
                  r = e.obj[e.prop];
                if (i(r)) {
                  for (var o = [], n = 0; n < r.length; ++n) void 0 !== r[n] && o.push(r[n]);
                  e.obj[e.prop] = o;
                }
              }
            }(e), t;
          },
          decode: function (t, e, r) {
            var o = t.replace(/\+/g, " ");
            if ("iso-8859-1" === r) return o.replace(/%[0-9a-f]{2}/gi, unescape);
            try {
              return decodeURIComponent(o);
            } catch (t) {
              return o;
            }
          },
          encode: function (t, e, r, n, i) {
            if (0 === t.length) return t;
            var s = t;
            if ("symbol" == typeof t ? s = Symbol.prototype.toString.call(t) : "string" != typeof t && (s = String(t)), "iso-8859-1" === r) return escape(s).replace(/%u[0-9a-f]{4}/gi, function (t) {
              return "%26%23" + parseInt(t.slice(2), 16) + "%3B";
            });
            for (var c = "", u = 0; u < s.length; ++u) {
              var l = s.charCodeAt(u);
              45 === l || 46 === l || 95 === l || 126 === l || l >= 48 && l <= 57 || l >= 65 && l <= 90 || l >= 97 && l <= 122 || i === o.RFC1738 && (40 === l || 41 === l) ? c += s.charAt(u) : l < 128 ? c += a[l] : l < 2048 ? c += a[192 | l >> 6] + a[128 | 63 & l] : l < 55296 || l >= 57344 ? c += a[224 | l >> 12] + a[128 | l >> 6 & 63] + a[128 | 63 & l] : (u += 1, l = 65536 + ((1023 & l) << 10 | 1023 & s.charCodeAt(u)), c += a[240 | l >> 18] + a[128 | l >> 12 & 63] + a[128 | l >> 6 & 63] + a[128 | 63 & l]);
            }
            return c;
          },
          isBuffer: function (t) {
            return !(!t || "object" != typeof t || !(t.constructor && t.constructor.isBuffer && t.constructor.isBuffer(t)));
          },
          isRegExp: function (t) {
            return "[object RegExp]" === Object.prototype.toString.call(t);
          },
          maybeMap: function (t, e) {
            if (i(t)) {
              for (var r = [], o = 0; o < t.length; o += 1) r.push(e(t[o]));
              return r;
            }
            return e(t);
          },
          merge: function t(e, r, o) {
            if (!r) return e;
            if ("object" != typeof r) {
              if (i(e)) e.push(r);else {
                if (!e || "object" != typeof e) return [e, r];
                (o && (o.plainObjects || o.allowPrototypes) || !n.call(Object.prototype, r)) && (e[r] = !0);
              }
              return e;
            }
            if (!e || "object" != typeof e) return [e].concat(r);
            var a = e;
            return i(e) && !i(r) && (a = s(e, o)), i(e) && i(r) ? (r.forEach(function (r, i) {
              if (n.call(e, i)) {
                var a = e[i];
                a && "object" == typeof a && r && "object" == typeof r ? e[i] = t(a, r, o) : e.push(r);
              } else e[i] = r;
            }), e) : Object.keys(r).reduce(function (e, i) {
              var a = r[i];
              return n.call(e, i) ? e[i] = t(e[i], a, o) : e[i] = a, e;
            }, a);
          }
        };
      },
      7478: (t, e, r) => {
        "use strict";

        var o = r(210),
          n = r(1924),
          i = r(631),
          a = o("%TypeError%"),
          s = o("%WeakMap%", !0),
          c = o("%Map%", !0),
          u = n("WeakMap.prototype.get", !0),
          l = n("WeakMap.prototype.set", !0),
          p = n("WeakMap.prototype.has", !0),
          f = n("Map.prototype.get", !0),
          y = n("Map.prototype.set", !0),
          h = n("Map.prototype.has", !0),
          d = function (t, e) {
            for (var r, o = t; null !== (r = o.next); o = r) if (r.key === e) return o.next = r.next, r.next = t.next, t.next = r, r;
          };
        t.exports = function () {
          var t,
            e,
            r,
            o = {
              assert: function (t) {
                if (!o.has(t)) throw new a("Side channel does not contain " + i(t));
              },
              get: function (o) {
                if (s && o && ("object" == typeof o || "function" == typeof o)) {
                  if (t) return u(t, o);
                } else if (c) {
                  if (e) return f(e, o);
                } else if (r) return function (t, e) {
                  var r = d(t, e);
                  return r && r.value;
                }(r, o);
              },
              has: function (o) {
                if (s && o && ("object" == typeof o || "function" == typeof o)) {
                  if (t) return p(t, o);
                } else if (c) {
                  if (e) return h(e, o);
                } else if (r) return function (t, e) {
                  return !!d(t, e);
                }(r, o);
                return !1;
              },
              set: function (o, n) {
                s && o && ("object" == typeof o || "function" == typeof o) ? (t || (t = new s()), l(t, o, n)) : c ? (e || (e = new c()), y(e, o, n)) : (r || (r = {
                  key: {},
                  next: null
                }), function (t, e, r) {
                  var o = d(t, e);
                  o ? o.value = r : t.next = {
                    key: e,
                    next: t.next,
                    value: r
                  };
                }(r, o, n));
              }
            };
          return o;
        };
      },
      7903: t => {
        "use strict";

        function e(t, e) {
          (null == e || e > t.length) && (e = t.length);
          for (var r = 0, o = new Array(e); r < e; r++) o[r] = t[r];
          return o;
        }
        function r() {
          this._defaults = [];
        }
        for (var o = 0, n = ["use", "on", "once", "set", "query", "type", "accept", "auth", "withCredentials", "sortQuery", "retry", "ok", "redirects", "timeout", "buffer", "serialize", "parse", "ca", "key", "pfx", "cert", "disableTLSCerts"]; o < n.length; o++) {
          const t = n[o];
          r.prototype[t] = function () {
            for (var e = arguments.length, r = new Array(e), o = 0; o < e; o++) r[o] = arguments[o];
            return this._defaults.push({
              fn: t,
              args: r
            }), this;
          };
        }
        r.prototype._setDefaults = function (t) {
          var r,
            o = function (t, r) {
              var o = "undefined" != typeof Symbol && t[Symbol.iterator] || t["@@iterator"];
              if (!o) {
                if (Array.isArray(t) || (o = function (t, r) {
                  if (t) {
                    if ("string" == typeof t) return e(t, r);
                    var o = Object.prototype.toString.call(t).slice(8, -1);
                    return "Object" === o && t.constructor && (o = t.constructor.name), "Map" === o || "Set" === o ? Array.from(t) : "Arguments" === o || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o) ? e(t, r) : void 0;
                  }
                }(t)) || r && t && "number" == typeof t.length) {
                  o && (t = o);
                  var n = 0,
                    i = function () {};
                  return {
                    s: i,
                    n: function () {
                      return n >= t.length ? {
                        done: !0
                      } : {
                        done: !1,
                        value: t[n++]
                      };
                    },
                    e: function (t) {
                      throw t;
                    },
                    f: i
                  };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
              }
              var a,
                s = !0,
                c = !1;
              return {
                s: function () {
                  o = o.call(t);
                },
                n: function () {
                  var t = o.next();
                  return s = t.done, t;
                },
                e: function (t) {
                  c = !0, a = t;
                },
                f: function () {
                  try {
                    s || null == o.return || o.return();
                  } finally {
                    if (c) throw a;
                  }
                }
              };
            }(this._defaults);
          try {
            for (o.s(); !(r = o.n()).done;) {
              const e = r.value;
              t[e.fn](...e.args);
            }
          } catch (t) {
            o.e(t);
          } finally {
            o.f();
          }
        }, t.exports = r;
      },
      569: (t, e, r) => {
        "use strict";

        function o(t, e) {
          (null == e || e > t.length) && (e = t.length);
          for (var r = 0, o = new Array(e); r < e; r++) o[r] = t[r];
          return o;
        }
        let n;
        "undefined" != typeof window ? n = window : "undefined" == typeof self ? (console.warn("Using browser-only version of superagent in non-browser environment"), n = void 0) : n = self;
        const i = r(4607),
          a = r(4445),
          s = r(129),
          c = r(8899),
          u = r(4506),
          l = u.isObject,
          p = u.mixin,
          f = u.hasOwn,
          y = r(1097),
          h = r(7903);
        function d() {}
        t.exports = function (t, r) {
          return "function" == typeof r ? new e.Request("GET", t).end(r) : 1 === arguments.length ? new e.Request("GET", t) : new e.Request(t, r);
        };
        const m = e = t.exports;
        e.Request = A, m.getXHR = () => {
          if (n.XMLHttpRequest) return new n.XMLHttpRequest();
          throw new Error("Browser-only version of superagent could not find XHR");
        };
        const b = "".trim ? t => t.trim() : t => t.replace(/(^\s*|\s*$)/g, "");
        function g(t) {
          if (!l(t)) return t;
          const e = [];
          for (const r in t) f(t, r) && v(e, r, t[r]);
          return e.join("&");
        }
        function v(t, e, r) {
          if (void 0 !== r) if (null !== r) {
            if (Array.isArray(r)) {
              var n,
                i = function (t, e) {
                  var r = "undefined" != typeof Symbol && t[Symbol.iterator] || t["@@iterator"];
                  if (!r) {
                    if (Array.isArray(t) || (r = function (t, e) {
                      if (t) {
                        if ("string" == typeof t) return o(t, e);
                        var r = Object.prototype.toString.call(t).slice(8, -1);
                        return "Object" === r && t.constructor && (r = t.constructor.name), "Map" === r || "Set" === r ? Array.from(t) : "Arguments" === r || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r) ? o(t, e) : void 0;
                      }
                    }(t)) || e && t && "number" == typeof t.length) {
                      r && (t = r);
                      var n = 0,
                        i = function () {};
                      return {
                        s: i,
                        n: function () {
                          return n >= t.length ? {
                            done: !0
                          } : {
                            done: !1,
                            value: t[n++]
                          };
                        },
                        e: function (t) {
                          throw t;
                        },
                        f: i
                      };
                    }
                    throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                  }
                  var a,
                    s = !0,
                    c = !1;
                  return {
                    s: function () {
                      r = r.call(t);
                    },
                    n: function () {
                      var t = r.next();
                      return s = t.done, t;
                    },
                    e: function (t) {
                      c = !0, a = t;
                    },
                    f: function () {
                      try {
                        s || null == r.return || r.return();
                      } finally {
                        if (c) throw a;
                      }
                    }
                  };
                }(r);
              try {
                for (i.s(); !(n = i.n()).done;) v(t, e, n.value);
              } catch (t) {
                i.e(t);
              } finally {
                i.f();
              }
            } else if (l(r)) for (const o in r) f(r, o) && v(t, "".concat(e, "[").concat(o, "]"), r[o]);else t.push(encodeURI(e) + "=" + encodeURIComponent(r));
          } else t.push(encodeURI(e));
        }
        function w(t) {
          const e = {},
            r = t.split("&");
          let o, n;
          for (let t = 0, i = r.length; t < i; ++t) o = r[t], n = o.indexOf("="), -1 === n ? e[decodeURIComponent(o)] = "" : e[decodeURIComponent(o.slice(0, n))] = decodeURIComponent(o.slice(n + 1));
          return e;
        }
        function _(t) {
          return /[/+]json($|[^-\w])/i.test(t);
        }
        function S(t) {
          this.req = t, this.xhr = this.req.xhr, this.text = "HEAD" !== this.req.method && ("" === this.xhr.responseType || "text" === this.xhr.responseType) || void 0 === this.xhr.responseType ? this.xhr.responseText : null, this.statusText = this.req.xhr.statusText;
          let e = this.xhr.status;
          1223 === e && (e = 204), this._setStatusProperties(e), this.headers = function (t) {
            const e = t.split(/\r?\n/),
              r = {};
            let o, n, i, a;
            for (let t = 0, s = e.length; t < s; ++t) n = e[t], o = n.indexOf(":"), -1 !== o && (i = n.slice(0, o).toLowerCase(), a = b(n.slice(o + 1)), r[i] = a);
            return r;
          }(this.xhr.getAllResponseHeaders()), this.header = this.headers, this.header["content-type"] = this.xhr.getResponseHeader("content-type"), this._setHeaderProperties(this.header), null === this.text && t._responseType ? this.body = this.xhr.response : this.body = "HEAD" === this.req.method ? null : this._parseBody(this.text ? this.text : this.xhr.response);
        }
        function A(t, e) {
          const r = this;
          this._query = this._query || [], this.method = t, this.url = e, this.header = {}, this._header = {}, this.on("end", () => {
            let t,
              e = null,
              o = null;
            try {
              o = new S(r);
            } catch (t) {
              return e = new Error("Parser is unable to parse the response"), e.parse = !0, e.original = t, r.xhr ? (e.rawResponse = void 0 === r.xhr.responseType ? r.xhr.responseText : r.xhr.response, e.status = r.xhr.status ? r.xhr.status : null, e.statusCode = e.status) : (e.rawResponse = null, e.status = null), r.callback(e);
            }
            r.emit("response", o);
            try {
              r._isResponseOK(o) || (t = new Error(o.statusText || o.text || "Unsuccessful HTTP response"));
            } catch (e) {
              t = e;
            }
            t ? (t.original = e, t.response = o, t.status = t.status || o.status, r.callback(t, o)) : r.callback(null, o);
          });
        }
        m.serializeObject = g, m.parseString = w, m.types = {
          html: "text/html",
          json: "application/json",
          xml: "text/xml",
          urlencoded: "application/x-www-form-urlencoded",
          form: "application/x-www-form-urlencoded",
          "form-data": "application/x-www-form-urlencoded"
        }, m.serialize = {
          "application/x-www-form-urlencoded": s.stringify,
          "application/json": a
        }, m.parse = {
          "application/x-www-form-urlencoded": w,
          "application/json": JSON.parse
        }, p(S.prototype, y.prototype), S.prototype._parseBody = function (t) {
          let e = m.parse[this.type];
          return this.req._parser ? this.req._parser(this, t) : (!e && _(this.type) && (e = m.parse["application/json"]), e && t && (t.length > 0 || t instanceof Object) ? e(t) : null);
        }, S.prototype.toError = function () {
          const t = this.req,
            e = t.method,
            r = t.url,
            o = "cannot ".concat(e, " ").concat(r, " (").concat(this.status, ")"),
            n = new Error(o);
          return n.status = this.status, n.method = e, n.url = r, n;
        }, m.Response = S, i(A.prototype), p(A.prototype, c.prototype), A.prototype.type = function (t) {
          return this.set("Content-Type", m.types[t] || t), this;
        }, A.prototype.accept = function (t) {
          return this.set("Accept", m.types[t] || t), this;
        }, A.prototype.auth = function (t, e, r) {
          1 === arguments.length && (e = ""), "object" == typeof e && null !== e && (r = e, e = ""), r || (r = {
            type: "function" == typeof btoa ? "basic" : "auto"
          });
          const o = r.encoder ? r.encoder : t => {
            if ("function" == typeof btoa) return btoa(t);
            throw new Error("Cannot use basic auth, btoa is not a function");
          };
          return this._auth(t, e, r, o);
        }, A.prototype.query = function (t) {
          return "string" != typeof t && (t = g(t)), t && this._query.push(t), this;
        }, A.prototype.attach = function (t, e, r) {
          if (e) {
            if (this._data) throw new Error("superagent can't mix .send() and .attach()");
            this._getFormData().append(t, e, r || e.name);
          }
          return this;
        }, A.prototype._getFormData = function () {
          return this._formData || (this._formData = new n.FormData()), this._formData;
        }, A.prototype.callback = function (t, e) {
          if (this._shouldRetry(t, e)) return this._retry();
          const r = this._callback;
          this.clearTimeout(), t && (this._maxRetries && (t.retries = this._retries - 1), this.emit("error", t)), r(t, e);
        }, A.prototype.crossDomainError = function () {
          const t = new Error("Request has been terminated\nPossible causes: the network is offline, Origin is not allowed by Access-Control-Allow-Origin, the page is being unloaded, etc.");
          t.crossDomain = !0, t.status = this.status, t.method = this.method, t.url = this.url, this.callback(t);
        }, A.prototype.agent = function () {
          return console.warn("This is not supported in browser version of superagent"), this;
        }, A.prototype.ca = A.prototype.agent, A.prototype.buffer = A.prototype.ca, A.prototype.write = () => {
          throw new Error("Streaming is not supported in browser version of superagent");
        }, A.prototype.pipe = A.prototype.write, A.prototype._isHost = function (t) {
          return t && "object" == typeof t && !Array.isArray(t) && "[object Object]" !== Object.prototype.toString.call(t);
        }, A.prototype.end = function (t) {
          this._endCalled && console.warn("Warning: .end() was called twice. This is not supported in superagent"), this._endCalled = !0, this._callback = t || d, this._finalizeQueryString(), this._end();
        }, A.prototype._setUploadTimeout = function () {
          const t = this;
          this._uploadTimeout && !this._uploadTimeoutTimer && (this._uploadTimeoutTimer = setTimeout(() => {
            t._timeoutError("Upload timeout of ", t._uploadTimeout, "ETIMEDOUT");
          }, this._uploadTimeout));
        }, A.prototype._end = function () {
          if (this._aborted) return this.callback(new Error("The request has been aborted even before .end() was called"));
          const t = this;
          this.xhr = m.getXHR();
          const e = this.xhr;
          let r = this._formData || this._data;
          this._setTimeouts(), e.addEventListener("readystatechange", () => {
            const r = e.readyState;
            if (r >= 2 && t._responseTimeoutTimer && clearTimeout(t._responseTimeoutTimer), 4 !== r) return;
            let o;
            try {
              o = e.status;
            } catch (t) {
              o = 0;
            }
            if (!o) {
              if (t.timedout || t._aborted) return;
              return t.crossDomainError();
            }
            t.emit("end");
          });
          const o = (e, r) => {
            r.total > 0 && (r.percent = r.loaded / r.total * 100, 100 === r.percent && clearTimeout(t._uploadTimeoutTimer)), r.direction = e, t.emit("progress", r);
          };
          if (this.hasListeners("progress")) try {
            e.addEventListener("progress", o.bind(null, "download")), e.upload && e.upload.addEventListener("progress", o.bind(null, "upload"));
          } catch (t) {}
          e.upload && this._setUploadTimeout();
          try {
            this.username && this.password ? e.open(this.method, this.url, !0, this.username, this.password) : e.open(this.method, this.url, !0);
          } catch (t) {
            return this.callback(t);
          }
          if (this._withCredentials && (e.withCredentials = !0), !this._formData && "GET" !== this.method && "HEAD" !== this.method && "string" != typeof r && !this._isHost(r)) {
            const t = this._header["content-type"];
            let e = this._serializer || m.serialize[t ? t.split(";")[0] : ""];
            !e && _(t) && (e = m.serialize["application/json"]), e && (r = e(r));
          }
          for (const t in this.header) null !== this.header[t] && f(this.header, t) && e.setRequestHeader(t, this.header[t]);
          this._responseType && (e.responseType = this._responseType), this.emit("request", this), e.send(void 0 === r ? null : r);
        }, m.agent = () => new h();
        for (var O = 0, E = ["GET", "POST", "OPTIONS", "PATCH", "PUT", "DELETE"]; O < E.length; O++) {
          const t = E[O];
          h.prototype[t.toLowerCase()] = function (e, r) {
            const o = new m.Request(t, e);
            return this._setDefaults(o), r && o.end(r), o;
          };
        }
        function P(t, e, r) {
          const o = m("DELETE", t);
          return "function" == typeof e && (r = e, e = null), e && o.send(e), r && o.end(r), o;
        }
        h.prototype.del = h.prototype.delete, m.get = (t, e, r) => {
          const o = m("GET", t);
          return "function" == typeof e && (r = e, e = null), e && o.query(e), r && o.end(r), o;
        }, m.head = (t, e, r) => {
          const o = m("HEAD", t);
          return "function" == typeof e && (r = e, e = null), e && o.query(e), r && o.end(r), o;
        }, m.options = (t, e, r) => {
          const o = m("OPTIONS", t);
          return "function" == typeof e && (r = e, e = null), e && o.send(e), r && o.end(r), o;
        }, m.del = P, m.delete = P, m.patch = (t, e, r) => {
          const o = m("PATCH", t);
          return "function" == typeof e && (r = e, e = null), e && o.send(e), r && o.end(r), o;
        }, m.post = (t, e, r) => {
          const o = m("POST", t);
          return "function" == typeof e && (r = e, e = null), e && o.send(e), r && o.end(r), o;
        }, m.put = (t, e, r) => {
          const o = m("PUT", t);
          return "function" == typeof e && (r = e, e = null), e && o.send(e), r && o.end(r), o;
        };
      },
      8899: (t, e, r) => {
        "use strict";

        const o = r(3695),
          n = r(4506),
          i = n.isObject,
          a = n.hasOwn;
        function s() {}
        t.exports = s, s.prototype.clearTimeout = function () {
          return clearTimeout(this._timer), clearTimeout(this._responseTimeoutTimer), clearTimeout(this._uploadTimeoutTimer), delete this._timer, delete this._responseTimeoutTimer, delete this._uploadTimeoutTimer, this;
        }, s.prototype.parse = function (t) {
          return this._parser = t, this;
        }, s.prototype.responseType = function (t) {
          return this._responseType = t, this;
        }, s.prototype.serialize = function (t) {
          return this._serializer = t, this;
        }, s.prototype.timeout = function (t) {
          if (!t || "object" != typeof t) return this._timeout = t, this._responseTimeout = 0, this._uploadTimeout = 0, this;
          for (const e in t) if (a(t, e)) switch (e) {
            case "deadline":
              this._timeout = t.deadline;
              break;
            case "response":
              this._responseTimeout = t.response;
              break;
            case "upload":
              this._uploadTimeout = t.upload;
              break;
            default:
              console.warn("Unknown timeout option", e);
          }
          return this;
        }, s.prototype.retry = function (t, e) {
          return 0 !== arguments.length && !0 !== t || (t = 1), t <= 0 && (t = 0), this._maxRetries = t, this._retries = 0, this._retryCallback = e, this;
        };
        const c = new Set(["ETIMEDOUT", "ECONNRESET", "EADDRINUSE", "ECONNREFUSED", "EPIPE", "ENOTFOUND", "ENETUNREACH", "EAI_AGAIN"]),
          u = new Set([408, 413, 429, 500, 502, 503, 504, 521, 522, 524]);
        s.prototype._shouldRetry = function (t, e) {
          if (!this._maxRetries || this._retries++ >= this._maxRetries) return !1;
          if (this._retryCallback) try {
            const r = this._retryCallback(t, e);
            if (!0 === r) return !0;
            if (!1 === r) return !1;
          } catch (t) {
            console.error(t);
          }
          if (e && e.status && u.has(e.status)) return !0;
          if (t) {
            if (t.code && c.has(t.code)) return !0;
            if (t.timeout && "ECONNABORTED" === t.code) return !0;
            if (t.crossDomain) return !0;
          }
          return !1;
        }, s.prototype._retry = function () {
          return this.clearTimeout(), this.req && (this.req = null, this.req = this.request()), this._aborted = !1, this.timedout = !1, this.timedoutError = null, this._end();
        }, s.prototype.then = function (t, e) {
          if (!this._fullfilledPromise) {
            const t = this;
            this._endCalled && console.warn("Warning: superagent request was sent twice, because both .end() and .then() were called. Never call .end() if you use promises"), this._fullfilledPromise = new Promise((e, r) => {
              t.on("abort", () => {
                if (this._maxRetries && this._maxRetries > this._retries) return;
                if (this.timedout && this.timedoutError) return void r(this.timedoutError);
                const t = new Error("Aborted");
                t.code = "ABORTED", t.status = this.status, t.method = this.method, t.url = this.url, r(t);
              }), t.end((t, o) => {
                t ? r(t) : e(o);
              });
            });
          }
          return this._fullfilledPromise.then(t, e);
        }, s.prototype.catch = function (t) {
          return this.then(void 0, t);
        }, s.prototype.use = function (t) {
          return t(this), this;
        }, s.prototype.ok = function (t) {
          if ("function" != typeof t) throw new Error("Callback required");
          return this._okCallback = t, this;
        }, s.prototype._isResponseOK = function (t) {
          return !!t && (this._okCallback ? this._okCallback(t) : t.status >= 200 && t.status < 300);
        }, s.prototype.get = function (t) {
          return this._header[t.toLowerCase()];
        }, s.prototype.getHeader = s.prototype.get, s.prototype.set = function (t, e) {
          if (i(t)) {
            for (const e in t) a(t, e) && this.set(e, t[e]);
            return this;
          }
          return this._header[t.toLowerCase()] = e, this.header[t] = e, this;
        }, s.prototype.unset = function (t) {
          return delete this._header[t.toLowerCase()], delete this.header[t], this;
        }, s.prototype.field = function (t, e, r) {
          if (null == t) throw new Error(".field(name, val) name can not be empty");
          if (this._data) throw new Error(".field() can't be used if .send() is used. Please use only .send() or only .field() & .attach()");
          if (i(t)) {
            for (const e in t) a(t, e) && this.field(e, t[e]);
            return this;
          }
          if (Array.isArray(e)) {
            for (const r in e) a(e, r) && this.field(t, e[r]);
            return this;
          }
          if (null == e) throw new Error(".field(name, val) val can not be empty");
          return "boolean" == typeof e && (e = String(e)), r ? this._getFormData().append(t, e, r) : this._getFormData().append(t, e), this;
        }, s.prototype.abort = function () {
          if (this._aborted) return this;
          if (this._aborted = !0, this.xhr && this.xhr.abort(), this.req) {
            if (o.gte(process.version, "v13.0.0") && o.lt(process.version, "v14.0.0")) throw new Error("Superagent does not work in v13 properly with abort() due to Node.js core changes");
            o.gte(process.version, "v14.0.0") && (this.req.destroyed = !0), this.req.abort();
          }
          return this.clearTimeout(), this.emit("abort"), this;
        }, s.prototype._auth = function (t, e, r, o) {
          switch (r.type) {
            case "basic":
              this.set("Authorization", "Basic ".concat(o("".concat(t, ":").concat(e))));
              break;
            case "auto":
              this.username = t, this.password = e;
              break;
            case "bearer":
              this.set("Authorization", "Bearer ".concat(t));
          }
          return this;
        }, s.prototype.withCredentials = function (t) {
          return void 0 === t && (t = !0), this._withCredentials = t, this;
        }, s.prototype.redirects = function (t) {
          return this._maxRedirects = t, this;
        }, s.prototype.maxResponseSize = function (t) {
          if ("number" != typeof t) throw new TypeError("Invalid argument");
          return this._maxResponseSize = t, this;
        }, s.prototype.toJSON = function () {
          return {
            method: this.method,
            url: this.url,
            data: this._data,
            headers: this._header
          };
        }, s.prototype.send = function (t) {
          const e = i(t);
          let r = this._header["content-type"];
          if (this._formData) throw new Error(".send() can't be used if .attach() or .field() is used. Please use only .send() or only .field() & .attach()");
          if (e && !this._data) Array.isArray(t) ? this._data = [] : this._isHost(t) || (this._data = {});else if (t && this._data && this._isHost(this._data)) throw new Error("Can't merge these send calls");
          if (e && i(this._data)) for (const e in t) a(t, e) && (this._data[e] = t[e]);else "string" == typeof t ? (r || this.type("form"), r = this._header["content-type"], r && (r = r.toLowerCase().trim()), this._data = "application/x-www-form-urlencoded" === r ? this._data ? "".concat(this._data, "&").concat(t) : t : (this._data || "") + t) : this._data = t;
          return !e || this._isHost(t) || r || this.type("json"), this;
        }, s.prototype.sortQuery = function (t) {
          return this._sort = void 0 === t || t, this;
        }, s.prototype._finalizeQueryString = function () {
          const t = this._query.join("&");
          if (t && (this.url += (this.url.includes("?") ? "&" : "?") + t), this._query.length = 0, this._sort) {
            const t = this.url.indexOf("?");
            if (t >= 0) {
              const e = this.url.slice(t + 1).split("&");
              "function" == typeof this._sort ? e.sort(this._sort) : e.sort(), this.url = this.url.slice(0, t) + "?" + e.join("&");
            }
          }
        }, s.prototype._appendQueryString = () => {
          console.warn("Unsupported");
        }, s.prototype._timeoutError = function (t, e, r) {
          if (this._aborted) return;
          const o = new Error("".concat(t + e, "ms exceeded"));
          o.timeout = e, o.code = "ECONNABORTED", o.errno = r, this.timedout = !0, this.timedoutError = o, this.abort(), this.callback(o);
        }, s.prototype._setTimeouts = function () {
          const t = this;
          this._timeout && !this._timer && (this._timer = setTimeout(() => {
            t._timeoutError("Timeout of ", t._timeout, "ETIME");
          }, this._timeout)), this._responseTimeout && !this._responseTimeoutTimer && (this._responseTimeoutTimer = setTimeout(() => {
            t._timeoutError("Response timeout of ", t._responseTimeout, "ETIMEDOUT");
          }, this._responseTimeout));
        };
      },
      1097: (t, e, r) => {
        "use strict";

        const o = r(4506);
        function n() {}
        t.exports = n, n.prototype.get = function (t) {
          return this.header[t.toLowerCase()];
        }, n.prototype._setHeaderProperties = function (t) {
          const e = t["content-type"] || "";
          this.type = o.type(e);
          const r = o.params(e);
          for (const t in r) Object.prototype.hasOwnProperty.call(r, t) && (this[t] = r[t]);
          this.links = {};
          try {
            t.link && (this.links = o.parseLinks(t.link));
          } catch (t) {}
        }, n.prototype._setStatusProperties = function (t) {
          const e = Math.trunc(t / 100);
          this.statusCode = t, this.status = this.statusCode, this.statusType = e, this.info = 1 === e, this.ok = 2 === e, this.redirect = 3 === e, this.clientError = 4 === e, this.serverError = 5 === e, this.error = (4 === e || 5 === e) && this.toError(), this.created = 201 === t, this.accepted = 202 === t, this.noContent = 204 === t, this.badRequest = 400 === t, this.unauthorized = 401 === t, this.notAcceptable = 406 === t, this.forbidden = 403 === t, this.notFound = 404 === t, this.unprocessableEntity = 422 === t;
        };
      },
      4506: (t, e) => {
        "use strict";

        function r(t, e) {
          var r = "undefined" != typeof Symbol && t[Symbol.iterator] || t["@@iterator"];
          if (!r) {
            if (Array.isArray(t) || (r = function (t, e) {
              if (t) {
                if ("string" == typeof t) return o(t, e);
                var r = Object.prototype.toString.call(t).slice(8, -1);
                return "Object" === r && t.constructor && (r = t.constructor.name), "Map" === r || "Set" === r ? Array.from(t) : "Arguments" === r || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r) ? o(t, e) : void 0;
              }
            }(t)) || e && t && "number" == typeof t.length) {
              r && (t = r);
              var n = 0,
                i = function () {};
              return {
                s: i,
                n: function () {
                  return n >= t.length ? {
                    done: !0
                  } : {
                    done: !1,
                    value: t[n++]
                  };
                },
                e: function (t) {
                  throw t;
                },
                f: i
              };
            }
            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
          }
          var a,
            s = !0,
            c = !1;
          return {
            s: function () {
              r = r.call(t);
            },
            n: function () {
              var t = r.next();
              return s = t.done, t;
            },
            e: function (t) {
              c = !0, a = t;
            },
            f: function () {
              try {
                s || null == r.return || r.return();
              } finally {
                if (c) throw a;
              }
            }
          };
        }
        function o(t, e) {
          (null == e || e > t.length) && (e = t.length);
          for (var r = 0, o = new Array(e); r < e; r++) o[r] = t[r];
          return o;
        }
        e.type = t => t.split(/ *; */).shift(), e.params = t => {
          const e = {};
          var o,
            n = r(t.split(/ *; */));
          try {
            for (n.s(); !(o = n.n()).done;) {
              const t = o.value.split(/ *= */),
                r = t.shift(),
                n = t.shift();
              r && n && (e[r] = n);
            }
          } catch (t) {
            n.e(t);
          } finally {
            n.f();
          }
          return e;
        }, e.parseLinks = t => {
          const e = {};
          var o,
            n = r(t.split(/ *, */));
          try {
            for (n.s(); !(o = n.n()).done;) {
              const t = o.value.split(/ *; */),
                r = t[0].slice(1, -1);
              e[t[1].split(/ *= */)[1].slice(1, -1)] = r;
            }
          } catch (t) {
            n.e(t);
          } finally {
            n.f();
          }
          return e;
        }, e.cleanHeader = (t, e) => (delete t["content-type"], delete t["content-length"], delete t["transfer-encoding"], delete t.host, e && (delete t.authorization, delete t.cookie), t), e.isObject = t => null !== t && "object" == typeof t, e.hasOwn = Object.hasOwn || function (t, e) {
          if (null == t) throw new TypeError("Cannot convert undefined or null to object");
          return Object.prototype.hasOwnProperty.call(new Object(t), e);
        }, e.mixin = (t, r) => {
          for (const o in r) e.hasOwn(r, o) && (t[o] = r[o]);
        };
      },
      4607: t => {
        function e(t) {
          if (t) return function (t) {
            for (var r in e.prototype) t[r] = e.prototype[r];
            return t;
          }(t);
        }
        t.exports = e, e.prototype.on = e.prototype.addEventListener = function (t, e) {
          return this._callbacks = this._callbacks || {}, (this._callbacks["$" + t] = this._callbacks["$" + t] || []).push(e), this;
        }, e.prototype.once = function (t, e) {
          function r() {
            this.off(t, r), e.apply(this, arguments);
          }
          return r.fn = e, this.on(t, r), this;
        }, e.prototype.off = e.prototype.removeListener = e.prototype.removeAllListeners = e.prototype.removeEventListener = function (t, e) {
          if (this._callbacks = this._callbacks || {}, 0 == arguments.length) return this._callbacks = {}, this;
          var r,
            o = this._callbacks["$" + t];
          if (!o) return this;
          if (1 == arguments.length) return delete this._callbacks["$" + t], this;
          for (var n = 0; n < o.length; n++) if ((r = o[n]) === e || r.fn === e) {
            o.splice(n, 1);
            break;
          }
          return 0 === o.length && delete this._callbacks["$" + t], this;
        }, e.prototype.emit = function (t) {
          this._callbacks = this._callbacks || {};
          for (var e = new Array(arguments.length - 1), r = this._callbacks["$" + t], o = 1; o < arguments.length; o++) e[o - 1] = arguments[o];
          if (r) {
            o = 0;
            for (var n = (r = r.slice(0)).length; o < n; ++o) r[o].apply(this, e);
          }
          return this;
        }, e.prototype.listeners = function (t) {
          return this._callbacks = this._callbacks || {}, this._callbacks["$" + t] || [];
        }, e.prototype.hasListeners = function (t) {
          return !!this.listeners(t).length;
        };
      },
      4793: function (t, e, r) {
        "use strict";

        var o = this && this.__awaiter || function (t, e, r, o) {
            return new (r || (r = Promise))(function (n, i) {
              function a(t) {
                try {
                  c(o.next(t));
                } catch (t) {
                  i(t);
                }
              }
              function s(t) {
                try {
                  c(o.throw(t));
                } catch (t) {
                  i(t);
                }
              }
              function c(t) {
                var e;
                t.done ? n(t.value) : (e = t.value, e instanceof r ? e : new r(function (t) {
                  t(e);
                })).then(a, s);
              }
              c((o = o.apply(t, e || [])).next());
            });
          },
          n = this && this.__generator || function (t, e) {
            var r,
              o,
              n,
              i,
              a = {
                label: 0,
                sent: function () {
                  if (1 & n[0]) throw n[1];
                  return n[1];
                },
                trys: [],
                ops: []
              };
            return i = {
              next: s(0),
              throw: s(1),
              return: s(2)
            }, "function" == typeof Symbol && (i[Symbol.iterator] = function () {
              return this;
            }), i;
            function s(i) {
              return function (s) {
                return function (i) {
                  if (r) throw new TypeError("Generator is already executing.");
                  for (; a;) try {
                    if (r = 1, o && (n = 2 & i[0] ? o.return : i[0] ? o.throw || ((n = o.return) && n.call(o), 0) : o.next) && !(n = n.call(o, i[1])).done) return n;
                    switch (o = 0, n && (i = [2 & i[0], n.value]), i[0]) {
                      case 0:
                      case 1:
                        n = i;
                        break;
                      case 4:
                        return a.label++, {
                          value: i[1],
                          done: !1
                        };
                      case 5:
                        a.label++, o = i[1], i = [0];
                        continue;
                      case 7:
                        i = a.ops.pop(), a.trys.pop();
                        continue;
                      default:
                        if (!((n = (n = a.trys).length > 0 && n[n.length - 1]) || 6 !== i[0] && 2 !== i[0])) {
                          a = 0;
                          continue;
                        }
                        if (3 === i[0] && (!n || i[1] > n[0] && i[1] < n[3])) {
                          a.label = i[1];
                          break;
                        }
                        if (6 === i[0] && a.label < n[1]) {
                          a.label = n[1], n = i;
                          break;
                        }
                        if (n && a.label < n[2]) {
                          a.label = n[2], a.ops.push(i);
                          break;
                        }
                        n[2] && a.ops.pop(), a.trys.pop();
                        continue;
                    }
                    i = e.call(t, a);
                  } catch (t) {
                    i = [6, t], o = 0;
                  } finally {
                    r = n = 0;
                  }
                  if (5 & i[0]) throw i[1];
                  return {
                    value: i[0] ? i[1] : void 0,
                    done: !0
                  };
                }([i, s]);
              };
            }
          };
        Object.defineProperty(e, "__esModule", {
          value: !0
        }), e.applePayButton = void 0;
        var i = r(569),
          a = r(7644),
          s = function (t, e) {
            var r = t.querySelector('[name="'.concat(e, '"]'));
            return r && r.getAttribute("value") || "";
          };
        e.applePayButton = {
          canMakePayment: function () {
            var t = window.ApplePaySession && ApplePaySession.canMakePayments();
            return t || console.log("Current browser does not support Apple Pay."), t;
          },
          mount: function (t, e) {
            var r = document.querySelector(t);
            if (!r) throw "No element found with selector: '".concat(t, "'. Make sure element exists and JavaScript is ran after the page has loaded.");
            r.style.display = "block";
            var c,
              u = s(r, "currency"),
              l = {
                label: s(r, "label"),
                type: "final",
                amount: s(r, "amount")
              },
              p = {
                "checkout-transaction-id": s(c = r, "checkout-transaction-id"),
                "checkout-account": s(c, "checkout-account"),
                "checkout-algorithm": s(c, "checkout-algorithm"),
                "checkout-method": s(c, "checkout-method"),
                "checkout-timestamp": s(c, "checkout-timestamp"),
                "checkout-nonce": s(c, "checkout-nonce"),
                signature: s(c, "signature")
              };
            r.onclick = function () {
              var t = new ApplePaySession(function () {
                for (var t = 14; t >= 4; t--) if (ApplePaySession.supportsVersion(t)) return t;
                return 4;
              }(), {
                countryCode: "FI",
                currencyCode: u,
                merchantCapabilities: ["supports3DS", "supportsDebit", "supportsCredit"],
                supportedNetworks: ["visa", "masterCard"],
                total: l
              });
              !function (t, e, r, s) {
                e.onvalidatemerchant = function (r) {
                  return o(void 0, void 0, void 0, function () {
                    var o, s;
                    return n(this, function (n) {
                      switch (n.label) {
                        case 0:
                          return o = r.validationURL, [4, i.post("".concat(a.API_ROOT_URL, "/payments/").concat(t["checkout-transaction-id"], "/apple-pay/validate-session")).set(t).accept("json").send({
                            validationUrl: o
                          })];
                        case 1:
                          return s = n.sent(), e.completeMerchantValidation(s.body), [2];
                      }
                    });
                  });
                }, e.onpaymentauthorized = function (r) {
                  return o(void 0, void 0, void 0, function () {
                    var o, c;
                    return n(this, function (n) {
                      switch (n.label) {
                        case 0:
                          return n.trys.push([0, 2,, 3]), [4, i.post("".concat(a.API_ROOT_URL, "/payments/").concat(t["checkout-transaction-id"], "/apple-pay/charge-token")).set(t).accept("json").send({
                            paymentData: r.payment.token.paymentData
                          })];
                        case 1:
                          return o = n.sent(), c = o.body.redirectUrl, e.completePayment({
                            status: ApplePaySession.STATUS_SUCCESS
                          }), s(c), [3, 3];
                        case 2:
                          return n.sent(), e.completePayment({
                            status: ApplePaySession.STATUS_FAILURE
                          }), [3, 3];
                        case 3:
                          return [2];
                      }
                    });
                  });
                }, e.onpaymentmethodselected = function () {
                  e.completePaymentMethodSelection(r, []);
                };
              }(p, t, l, e), t.begin();
            };
          }
        };
      },
      7644: (t, e) => {
        "use strict";

        Object.defineProperty(e, "__esModule", {
          value: !0
        }), e.API_ROOT_URL = void 0, e.API_ROOT_URL = ("https://services.paytrail.com/", "https://services.paytrail.com/").slice(0, -1);
      },
      7145: function (t, e, r) {
        "use strict";

        var o = this && this.__assign || function () {
            return o = Object.assign || function (t) {
              for (var e, r = 1, o = arguments.length; r < o; r++) for (var n in e = arguments[r]) Object.prototype.hasOwnProperty.call(e, n) && (t[n] = e[n]);
              return t;
            }, o.apply(this, arguments);
          },
          n = this && this.__awaiter || function (t, e, r, o) {
            return new (r || (r = Promise))(function (n, i) {
              function a(t) {
                try {
                  c(o.next(t));
                } catch (t) {
                  i(t);
                }
              }
              function s(t) {
                try {
                  c(o.throw(t));
                } catch (t) {
                  i(t);
                }
              }
              function c(t) {
                var e;
                t.done ? n(t.value) : (e = t.value, e instanceof r ? e : new r(function (t) {
                  t(e);
                })).then(a, s);
              }
              c((o = o.apply(t, e || [])).next());
            });
          },
          i = this && this.__generator || function (t, e) {
            var r,
              o,
              n,
              i,
              a = {
                label: 0,
                sent: function () {
                  if (1 & n[0]) throw n[1];
                  return n[1];
                },
                trys: [],
                ops: []
              };
            return i = {
              next: s(0),
              throw: s(1),
              return: s(2)
            }, "function" == typeof Symbol && (i[Symbol.iterator] = function () {
              return this;
            }), i;
            function s(i) {
              return function (s) {
                return function (i) {
                  if (r) throw new TypeError("Generator is already executing.");
                  for (; a;) try {
                    if (r = 1, o && (n = 2 & i[0] ? o.return : i[0] ? o.throw || ((n = o.return) && n.call(o), 0) : o.next) && !(n = n.call(o, i[1])).done) return n;
                    switch (o = 0, n && (i = [2 & i[0], n.value]), i[0]) {
                      case 0:
                      case 1:
                        n = i;
                        break;
                      case 4:
                        return a.label++, {
                          value: i[1],
                          done: !1
                        };
                      case 5:
                        a.label++, o = i[1], i = [0];
                        continue;
                      case 7:
                        i = a.ops.pop(), a.trys.pop();
                        continue;
                      default:
                        if (!((n = (n = a.trys).length > 0 && n[n.length - 1]) || 6 !== i[0] && 2 !== i[0])) {
                          a = 0;
                          continue;
                        }
                        if (3 === i[0] && (!n || i[1] > n[0] && i[1] < n[3])) {
                          a.label = i[1];
                          break;
                        }
                        if (6 === i[0] && a.label < n[1]) {
                          a.label = n[1], n = i;
                          break;
                        }
                        if (n && a.label < n[2]) {
                          a.label = n[2], a.ops.push(i);
                          break;
                        }
                        n[2] && a.ops.pop(), a.trys.pop();
                        continue;
                    }
                    i = e.call(t, a);
                  } catch (t) {
                    i = [6, t], o = 0;
                  } finally {
                    r = n = 0;
                  }
                  if (5 & i[0]) throw i[1];
                  return {
                    value: i[0] ? i[1] : void 0,
                    done: !0
                  };
                }([i, s]);
              };
            }
          };
        Object.defineProperty(e, "__esModule", {
          value: !0
        }), e.googlePayButton = e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW = void 0;
        var a = r(569),
          s = r(7644);
        e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW = !0;
        var c = function (t, e) {
            if (!t) return "";
            var r = t.querySelector('[name="'.concat(e, '"]'));
            return r && r.getAttribute("value") || "";
          },
          u = function (t) {
            var r = document.querySelector(t);
            return o(o({
              apiVersion: 2,
              apiVersionMinor: 0
            }, e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW ? {
              callbackIntents: ["PAYMENT_AUTHORIZATION"]
            } : {}), {
              allowedPaymentMethods: [{
                type: "CARD",
                parameters: {
                  allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                  allowedCardNetworks: ["MASTERCARD", "VISA"]
                },
                tokenizationSpecification: {
                  type: "PAYMENT_GATEWAY",
                  parameters: {
                    gateway: "nexirelay",
                    gatewayMerchantId: c(r, "relay-merchant-id")
                  }
                }
              }],
              merchantInfo: {
                merchantId: c(r, "google-merchant-id"),
                merchantName: c(r, "merchant-name"),
                merchantOrigin: window.location.hostname
              },
              transactionInfo: {
                totalPriceStatus: "FINAL",
                totalPriceLabel: c(r, "label"),
                totalPrice: c(r, "amount"),
                transactionId: c(r, "checkout-transaction-id"),
                currencyCode: c(r, "currency"),
                countryCode: "FI"
              }
            });
          },
          l = function (t, r) {
            return function (o) {
              return n(void 0, void 0, void 0, function () {
                var n, u, l, p, f, y, h;
                return i(this, function (i) {
                  switch (i.label) {
                    case 0:
                      return i.trys.push([0, 2,, 3]), n = function (t) {
                        var e = document.querySelector(t);
                        return {
                          "checkout-transaction-id": c(e, "checkout-transaction-id"),
                          "checkout-account": c(e, "checkout-account"),
                          "checkout-algorithm": c(e, "checkout-algorithm"),
                          "checkout-method": c(e, "checkout-method"),
                          "checkout-timestamp": c(e, "checkout-timestamp"),
                          "checkout-nonce": c(e, "checkout-nonce"),
                          signature: c(e, "signature")
                        };
                      }(t), [4, a.post("".concat(s.API_ROOT_URL, "/payments/").concat(n["checkout-transaction-id"], "/google-pay/charge-token")).set(n).accept("json").send({
                        tokenizationData: o.paymentMethodData.tokenizationData
                      })];
                    case 1:
                      return (u = i.sent()).body.redirectUrl ? (r(u.body.redirectUrl), [2, {
                        transactionState: "SUCCESS"
                      }]) : [3, 3];
                    case 2:
                      if (l = i.sent(), e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW) return [2, {
                        transactionState: "ERROR",
                        error: {
                          reason: "PAYMENT_DATA_INVALID",
                          intent: "PAYMENT_AUTHORIZATION",
                          message: (null === (f = null === (p = l.response) || void 0 === p ? void 0 : p.body) || void 0 === f ? void 0 : f.message) || "Error"
                        }
                      }];
                      if (null == (null === (h = null === (y = l.response) || void 0 === y ? void 0 : y.body) || void 0 === h ? void 0 : h.redirectUrl)) throw console.error("Unknown error with Google Pay", l), l;
                      return r(l.response.body.redirectUrl), [2, {
                        transactionState: "ERROR"
                      }];
                    case 3:
                      return [2, {
                        transactionState: "ERROR"
                      }];
                  }
                });
              });
            };
          },
          p = function (t, r) {
            var n = document.querySelector(t);
            return new google.payments.api.PaymentsClient(o(o({}, e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW ? {
              paymentDataCallbacks: {
                onPaymentAuthorized: l(t, r)
              }
            } : {}), {
              environment: c(n, "environment")
            }));
          },
          f = function (t, r) {
            return function () {
              var o = p(t, r),
                n = u(t);
              o.loadPaymentData(n).then(e.USE_GOOGLE_PAY_CALLBACK_INTENTS_FLOW ? function () {
                return null;
              } : l(t, r)).catch(function (t) {
                if ("CANCELED" !== t.statusCode) throw console.error("Unknown error with Google Pay", t), t;
              });
            };
          };
        e.googlePayButton = {
          mount: function (t, e, r) {
            if (!document.querySelector(t)) throw "No element found with selector: '".concat(t, "'. Make sure element exists and JavaScript is ran after the page has loaded.");
            !function (t, e, r) {
              var o = document.createElement("script");
              o.src = "https://pay.google.com/gp/p/js/pay.js", o.addEventListener("load", function () {
                return function (t, e, r) {
                  var o = p(t, e);
                  o.isReadyToPay({
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: [{
                      type: "CARD",
                      parameters: {
                        allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                        allowedCardNetworks: ["MASTERCARD", "VISA"]
                      }
                    }]
                  }).then(function (n) {
                    if (n.result) {
                      var i = o.createButton({
                          onClick: f(t, e),
                          allowedPaymentMethods: u(t).allowedPaymentMethods,
                          buttonColor: (null == r ? void 0 : r.color) || "default",
                          buttonType: (null == r ? void 0 : r.type) || "short",
                          buttonRadius: (null == r ? void 0 : r.radius) || 8,
                          buttonSizeMode: "fill"
                        }),
                        a = document.querySelector(t);
                      if (a) {
                        a.style.display = "flex";
                        var s = document.createElement("div");
                        s.style.height = "100%", s.style.width = "100%", s.style.display = "flex", s.style.justifyContent = "center", s.style.alignItems = "center", a.appendChild(s), s.appendChild(i);
                      }
                    }
                  }).catch(function (t) {
                    throw console.error("Unable to load Google Pay", t), t;
                  });
                }(t, e, r);
              }), document.body.appendChild(o);
            }(t, e, r);
          }
        };
      },
      4654: () => {},
      3695: () => {}
    },
    e = {};
  function r(o) {
    var n = e[o];
    if (void 0 !== n) return n.exports;
    var i = e[o] = {
      exports: {}
    };
    return t[o].call(i.exports, i, i.exports, r), i.exports;
  }
  (() => {
    "use strict";

    var t = r(4793),
      e = r(7145);
    window.checkoutFinland = {
      applePayButton: t.applePayButton,
      googlePayButton: e.googlePayButton
    }, window.paytrail = {
      applePayButton: t.applePayButton,
      googlePayButton: e.googlePayButton
    };
  })();
})();

/***/ }),

/***/ "./assets/scss/payment_blocks.scss":
/*!*****************************************!*\
  !*** ./assets/scss/payment_blocks.scss ***!
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

/***/ }),

/***/ "./node_modules/lodash.debounce/index.js":
/*!***********************************************!*\
  !*** ./node_modules/lodash.debounce/index.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright jQuery Foundation and other contributors <https://jquery.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */

/** Used as the `TypeError` message for "Functions" methods. */
var FUNC_ERROR_TEXT = 'Expected a function';

/** Used as references for various `Number` constants. */
var NAN = 0 / 0;

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/** Used to match leading and trailing whitespace. */
var reTrim = /^\s+|\s+$/g;

/** Used to detect bad signed hexadecimal string values. */
var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

/** Used to detect binary string values. */
var reIsBinary = /^0b[01]+$/i;

/** Used to detect octal string values. */
var reIsOctal = /^0o[0-7]+$/i;

/** Built-in method references without a dependency on `root`. */
var freeParseInt = parseInt;

/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof __webpack_require__.g == 'object' && __webpack_require__.g && __webpack_require__.g.Object === Object && __webpack_require__.g;

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var objectToString = objectProto.toString;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max,
    nativeMin = Math.min;

/**
 * Gets the timestamp of the number of milliseconds that have elapsed since
 * the Unix epoch (1 January 1970 00:00:00 UTC).
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Date
 * @returns {number} Returns the timestamp.
 * @example
 *
 * _.defer(function(stamp) {
 *   console.log(_.now() - stamp);
 * }, _.now());
 * // => Logs the number of milliseconds it took for the deferred invocation.
 */
var now = function() {
  return root.Date.now();
};

/**
 * Creates a debounced function that delays invoking `func` until after `wait`
 * milliseconds have elapsed since the last time the debounced function was
 * invoked. The debounced function comes with a `cancel` method to cancel
 * delayed `func` invocations and a `flush` method to immediately invoke them.
 * Provide `options` to indicate whether `func` should be invoked on the
 * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
 * with the last arguments provided to the debounced function. Subsequent
 * calls to the debounced function return the result of the last `func`
 * invocation.
 *
 * **Note:** If `leading` and `trailing` options are `true`, `func` is
 * invoked on the trailing edge of the timeout only if the debounced function
 * is invoked more than once during the `wait` timeout.
 *
 * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
 * until to the next tick, similar to `setTimeout` with a timeout of `0`.
 *
 * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
 * for details over the differences between `_.debounce` and `_.throttle`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to debounce.
 * @param {number} [wait=0] The number of milliseconds to delay.
 * @param {Object} [options={}] The options object.
 * @param {boolean} [options.leading=false]
 *  Specify invoking on the leading edge of the timeout.
 * @param {number} [options.maxWait]
 *  The maximum time `func` is allowed to be delayed before it's invoked.
 * @param {boolean} [options.trailing=true]
 *  Specify invoking on the trailing edge of the timeout.
 * @returns {Function} Returns the new debounced function.
 * @example
 *
 * // Avoid costly calculations while the window size is in flux.
 * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
 *
 * // Invoke `sendMail` when clicked, debouncing subsequent calls.
 * jQuery(element).on('click', _.debounce(sendMail, 300, {
 *   'leading': true,
 *   'trailing': false
 * }));
 *
 * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
 * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
 * var source = new EventSource('/stream');
 * jQuery(source).on('message', debounced);
 *
 * // Cancel the trailing debounced invocation.
 * jQuery(window).on('popstate', debounced.cancel);
 */
function debounce(func, wait, options) {
  var lastArgs,
      lastThis,
      maxWait,
      result,
      timerId,
      lastCallTime,
      lastInvokeTime = 0,
      leading = false,
      maxing = false,
      trailing = true;

  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  wait = toNumber(wait) || 0;
  if (isObject(options)) {
    leading = !!options.leading;
    maxing = 'maxWait' in options;
    maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
    trailing = 'trailing' in options ? !!options.trailing : trailing;
  }

  function invokeFunc(time) {
    var args = lastArgs,
        thisArg = lastThis;

    lastArgs = lastThis = undefined;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }

  function leadingEdge(time) {
    // Reset any `maxWait` timer.
    lastInvokeTime = time;
    // Start the timer for the trailing edge.
    timerId = setTimeout(timerExpired, wait);
    // Invoke the leading edge.
    return leading ? invokeFunc(time) : result;
  }

  function remainingWait(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime,
        result = wait - timeSinceLastCall;

    return maxing ? nativeMin(result, maxWait - timeSinceLastInvoke) : result;
  }

  function shouldInvoke(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime;

    // Either this is the first call, activity has stopped and we're at the
    // trailing edge, the system time has gone backwards and we're treating
    // it as the trailing edge, or we've hit the `maxWait` limit.
    return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
      (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
  }

  function timerExpired() {
    var time = now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    // Restart the timer.
    timerId = setTimeout(timerExpired, remainingWait(time));
  }

  function trailingEdge(time) {
    timerId = undefined;

    // Only invoke if we have `lastArgs` which means `func` has been
    // debounced at least once.
    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = undefined;
    return result;
  }

  function cancel() {
    if (timerId !== undefined) {
      clearTimeout(timerId);
    }
    lastInvokeTime = 0;
    lastArgs = lastCallTime = lastThis = timerId = undefined;
  }

  function flush() {
    return timerId === undefined ? result : trailingEdge(now());
  }

  function debounced() {
    var time = now(),
        isInvoking = shouldInvoke(time);

    lastArgs = arguments;
    lastThis = this;
    lastCallTime = time;

    if (isInvoking) {
      if (timerId === undefined) {
        return leadingEdge(lastCallTime);
      }
      if (maxing) {
        // Handle invocations in a tight loop.
        timerId = setTimeout(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (timerId === undefined) {
      timerId = setTimeout(timerExpired, wait);
    }
    return result;
  }
  debounced.cancel = cancel;
  debounced.flush = flush;
  return debounced;
}

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && objectToString.call(value) == symbolTag);
}

/**
 * Converts `value` to a number.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {number} Returns the number.
 * @example
 *
 * _.toNumber(3.2);
 * // => 3.2
 *
 * _.toNumber(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toNumber(Infinity);
 * // => Infinity
 *
 * _.toNumber('3.2');
 * // => 3.2
 */
function toNumber(value) {
  if (typeof value == 'number') {
    return value;
  }
  if (isSymbol(value)) {
    return NAN;
  }
  if (isObject(value)) {
    var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
    value = isObject(other) ? (other + '') : other;
  }
  if (typeof value != 'string') {
    return value === 0 ? value : +value;
  }
  value = value.replace(reTrim, '');
  var isBinary = reIsBinary.test(value);
  return (isBinary || reIsOctal.test(value))
    ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
    : (reIsBadHex.test(value) ? NAN : +value);
}

module.exports = debounce;


/***/ }),

/***/ "./node_modules/react/cjs/react-jsx-runtime.development.js":
/*!*****************************************************************!*\
  !*** ./node_modules/react/cjs/react-jsx-runtime.development.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



if (true) {
  (function() {
'use strict';

var React = __webpack_require__(/*! react */ "react");

// ATTENTION
// When adding new symbols to this file,
// Please consider also adding to 'react-devtools-shared/src/backend/ReactSymbols'
// The Symbol used to tag the ReactElement-like types.
var REACT_ELEMENT_TYPE = Symbol.for('react.element');
var REACT_PORTAL_TYPE = Symbol.for('react.portal');
var REACT_FRAGMENT_TYPE = Symbol.for('react.fragment');
var REACT_STRICT_MODE_TYPE = Symbol.for('react.strict_mode');
var REACT_PROFILER_TYPE = Symbol.for('react.profiler');
var REACT_PROVIDER_TYPE = Symbol.for('react.provider');
var REACT_CONTEXT_TYPE = Symbol.for('react.context');
var REACT_FORWARD_REF_TYPE = Symbol.for('react.forward_ref');
var REACT_SUSPENSE_TYPE = Symbol.for('react.suspense');
var REACT_SUSPENSE_LIST_TYPE = Symbol.for('react.suspense_list');
var REACT_MEMO_TYPE = Symbol.for('react.memo');
var REACT_LAZY_TYPE = Symbol.for('react.lazy');
var REACT_OFFSCREEN_TYPE = Symbol.for('react.offscreen');
var MAYBE_ITERATOR_SYMBOL = Symbol.iterator;
var FAUX_ITERATOR_SYMBOL = '@@iterator';
function getIteratorFn(maybeIterable) {
  if (maybeIterable === null || typeof maybeIterable !== 'object') {
    return null;
  }

  var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];

  if (typeof maybeIterator === 'function') {
    return maybeIterator;
  }

  return null;
}

var ReactSharedInternals = React.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;

function error(format) {
  {
    {
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }

      printWarning('error', format, args);
    }
  }
}

function printWarning(level, format, args) {
  // When changing this logic, you might want to also
  // update consoleWithStackDev.www.js as well.
  {
    var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
    var stack = ReactDebugCurrentFrame.getStackAddendum();

    if (stack !== '') {
      format += '%s';
      args = args.concat([stack]);
    } // eslint-disable-next-line react-internal/safe-string-coercion


    var argsWithFormat = args.map(function (item) {
      return String(item);
    }); // Careful: RN currently depends on this prefix

    argsWithFormat.unshift('Warning: ' + format); // We intentionally don't use spread (or .apply) directly because it
    // breaks IE9: https://github.com/facebook/react/issues/13610
    // eslint-disable-next-line react-internal/no-production-logging

    Function.prototype.apply.call(console[level], console, argsWithFormat);
  }
}

// -----------------------------------------------------------------------------

var enableScopeAPI = false; // Experimental Create Event Handle API.
var enableCacheElement = false;
var enableTransitionTracing = false; // No known bugs, but needs performance testing

var enableLegacyHidden = false; // Enables unstable_avoidThisFallback feature in Fiber
// stuff. Intended to enable React core members to more easily debug scheduling
// issues in DEV builds.

var enableDebugTracing = false; // Track which Fiber(s) schedule render work.

var REACT_MODULE_REFERENCE;

{
  REACT_MODULE_REFERENCE = Symbol.for('react.module.reference');
}

function isValidElementType(type) {
  if (typeof type === 'string' || typeof type === 'function') {
    return true;
  } // Note: typeof might be other than 'symbol' or 'number' (e.g. if it's a polyfill).


  if (type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || enableDebugTracing  || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || enableLegacyHidden  || type === REACT_OFFSCREEN_TYPE || enableScopeAPI  || enableCacheElement  || enableTransitionTracing ) {
    return true;
  }

  if (typeof type === 'object' && type !== null) {
    if (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || // This needs to include all possible module reference object
    // types supported by any Flight configuration anywhere since
    // we don't know which Flight build this will end up being used
    // with.
    type.$$typeof === REACT_MODULE_REFERENCE || type.getModuleId !== undefined) {
      return true;
    }
  }

  return false;
}

function getWrappedName(outerType, innerType, wrapperName) {
  var displayName = outerType.displayName;

  if (displayName) {
    return displayName;
  }

  var functionName = innerType.displayName || innerType.name || '';
  return functionName !== '' ? wrapperName + "(" + functionName + ")" : wrapperName;
} // Keep in sync with react-reconciler/getComponentNameFromFiber


function getContextName(type) {
  return type.displayName || 'Context';
} // Note that the reconciler package should generally prefer to use getComponentNameFromFiber() instead.


function getComponentNameFromType(type) {
  if (type == null) {
    // Host root, text node or just invalid type.
    return null;
  }

  {
    if (typeof type.tag === 'number') {
      error('Received an unexpected object in getComponentNameFromType(). ' + 'This is likely a bug in React. Please file an issue.');
    }
  }

  if (typeof type === 'function') {
    return type.displayName || type.name || null;
  }

  if (typeof type === 'string') {
    return type;
  }

  switch (type) {
    case REACT_FRAGMENT_TYPE:
      return 'Fragment';

    case REACT_PORTAL_TYPE:
      return 'Portal';

    case REACT_PROFILER_TYPE:
      return 'Profiler';

    case REACT_STRICT_MODE_TYPE:
      return 'StrictMode';

    case REACT_SUSPENSE_TYPE:
      return 'Suspense';

    case REACT_SUSPENSE_LIST_TYPE:
      return 'SuspenseList';

  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_CONTEXT_TYPE:
        var context = type;
        return getContextName(context) + '.Consumer';

      case REACT_PROVIDER_TYPE:
        var provider = type;
        return getContextName(provider._context) + '.Provider';

      case REACT_FORWARD_REF_TYPE:
        return getWrappedName(type, type.render, 'ForwardRef');

      case REACT_MEMO_TYPE:
        var outerName = type.displayName || null;

        if (outerName !== null) {
          return outerName;
        }

        return getComponentNameFromType(type.type) || 'Memo';

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            return getComponentNameFromType(init(payload));
          } catch (x) {
            return null;
          }
        }

      // eslint-disable-next-line no-fallthrough
    }
  }

  return null;
}

var assign = Object.assign;

// Helpers to patch console.logs to avoid logging during side-effect free
// replaying on render function. This currently only patches the object
// lazily which won't cover if the log function was extracted eagerly.
// We could also eagerly patch the method.
var disabledDepth = 0;
var prevLog;
var prevInfo;
var prevWarn;
var prevError;
var prevGroup;
var prevGroupCollapsed;
var prevGroupEnd;

function disabledLog() {}

disabledLog.__reactDisabledLog = true;
function disableLogs() {
  {
    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      prevLog = console.log;
      prevInfo = console.info;
      prevWarn = console.warn;
      prevError = console.error;
      prevGroup = console.group;
      prevGroupCollapsed = console.groupCollapsed;
      prevGroupEnd = console.groupEnd; // https://github.com/facebook/react/issues/19099

      var props = {
        configurable: true,
        enumerable: true,
        value: disabledLog,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        info: props,
        log: props,
        warn: props,
        error: props,
        group: props,
        groupCollapsed: props,
        groupEnd: props
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    disabledDepth++;
  }
}
function reenableLogs() {
  {
    disabledDepth--;

    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      var props = {
        configurable: true,
        enumerable: true,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        log: assign({}, props, {
          value: prevLog
        }),
        info: assign({}, props, {
          value: prevInfo
        }),
        warn: assign({}, props, {
          value: prevWarn
        }),
        error: assign({}, props, {
          value: prevError
        }),
        group: assign({}, props, {
          value: prevGroup
        }),
        groupCollapsed: assign({}, props, {
          value: prevGroupCollapsed
        }),
        groupEnd: assign({}, props, {
          value: prevGroupEnd
        })
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    if (disabledDepth < 0) {
      error('disabledDepth fell below zero. ' + 'This is a bug in React. Please file an issue.');
    }
  }
}

var ReactCurrentDispatcher = ReactSharedInternals.ReactCurrentDispatcher;
var prefix;
function describeBuiltInComponentFrame(name, source, ownerFn) {
  {
    if (prefix === undefined) {
      // Extract the VM specific prefix used by each line.
      try {
        throw Error();
      } catch (x) {
        var match = x.stack.trim().match(/\n( *(at )?)/);
        prefix = match && match[1] || '';
      }
    } // We use the prefix to ensure our stacks line up with native stack frames.


    return '\n' + prefix + name;
  }
}
var reentry = false;
var componentFrameCache;

{
  var PossiblyWeakMap = typeof WeakMap === 'function' ? WeakMap : Map;
  componentFrameCache = new PossiblyWeakMap();
}

function describeNativeComponentFrame(fn, construct) {
  // If something asked for a stack inside a fake render, it should get ignored.
  if ( !fn || reentry) {
    return '';
  }

  {
    var frame = componentFrameCache.get(fn);

    if (frame !== undefined) {
      return frame;
    }
  }

  var control;
  reentry = true;
  var previousPrepareStackTrace = Error.prepareStackTrace; // $FlowFixMe It does accept undefined.

  Error.prepareStackTrace = undefined;
  var previousDispatcher;

  {
    previousDispatcher = ReactCurrentDispatcher.current; // Set the dispatcher in DEV because this might be call in the render function
    // for warnings.

    ReactCurrentDispatcher.current = null;
    disableLogs();
  }

  try {
    // This should throw.
    if (construct) {
      // Something should be setting the props in the constructor.
      var Fake = function () {
        throw Error();
      }; // $FlowFixMe


      Object.defineProperty(Fake.prototype, 'props', {
        set: function () {
          // We use a throwing setter instead of frozen or non-writable props
          // because that won't throw in a non-strict mode function.
          throw Error();
        }
      });

      if (typeof Reflect === 'object' && Reflect.construct) {
        // We construct a different control for this case to include any extra
        // frames added by the construct call.
        try {
          Reflect.construct(Fake, []);
        } catch (x) {
          control = x;
        }

        Reflect.construct(fn, [], Fake);
      } else {
        try {
          Fake.call();
        } catch (x) {
          control = x;
        }

        fn.call(Fake.prototype);
      }
    } else {
      try {
        throw Error();
      } catch (x) {
        control = x;
      }

      fn();
    }
  } catch (sample) {
    // This is inlined manually because closure doesn't do it for us.
    if (sample && control && typeof sample.stack === 'string') {
      // This extracts the first frame from the sample that isn't also in the control.
      // Skipping one frame that we assume is the frame that calls the two.
      var sampleLines = sample.stack.split('\n');
      var controlLines = control.stack.split('\n');
      var s = sampleLines.length - 1;
      var c = controlLines.length - 1;

      while (s >= 1 && c >= 0 && sampleLines[s] !== controlLines[c]) {
        // We expect at least one stack frame to be shared.
        // Typically this will be the root most one. However, stack frames may be
        // cut off due to maximum stack limits. In this case, one maybe cut off
        // earlier than the other. We assume that the sample is longer or the same
        // and there for cut off earlier. So we should find the root most frame in
        // the sample somewhere in the control.
        c--;
      }

      for (; s >= 1 && c >= 0; s--, c--) {
        // Next we find the first one that isn't the same which should be the
        // frame that called our sample function and the control.
        if (sampleLines[s] !== controlLines[c]) {
          // In V8, the first line is describing the message but other VMs don't.
          // If we're about to return the first line, and the control is also on the same
          // line, that's a pretty good indicator that our sample threw at same line as
          // the control. I.e. before we entered the sample frame. So we ignore this result.
          // This can happen if you passed a class to function component, or non-function.
          if (s !== 1 || c !== 1) {
            do {
              s--;
              c--; // We may still have similar intermediate frames from the construct call.
              // The next one that isn't the same should be our match though.

              if (c < 0 || sampleLines[s] !== controlLines[c]) {
                // V8 adds a "new" prefix for native classes. Let's remove it to make it prettier.
                var _frame = '\n' + sampleLines[s].replace(' at new ', ' at '); // If our component frame is labeled "<anonymous>"
                // but we have a user-provided "displayName"
                // splice it in to make the stack more readable.


                if (fn.displayName && _frame.includes('<anonymous>')) {
                  _frame = _frame.replace('<anonymous>', fn.displayName);
                }

                {
                  if (typeof fn === 'function') {
                    componentFrameCache.set(fn, _frame);
                  }
                } // Return the line we found.


                return _frame;
              }
            } while (s >= 1 && c >= 0);
          }

          break;
        }
      }
    }
  } finally {
    reentry = false;

    {
      ReactCurrentDispatcher.current = previousDispatcher;
      reenableLogs();
    }

    Error.prepareStackTrace = previousPrepareStackTrace;
  } // Fallback to just using the name if we couldn't make it throw.


  var name = fn ? fn.displayName || fn.name : '';
  var syntheticFrame = name ? describeBuiltInComponentFrame(name) : '';

  {
    if (typeof fn === 'function') {
      componentFrameCache.set(fn, syntheticFrame);
    }
  }

  return syntheticFrame;
}
function describeFunctionComponentFrame(fn, source, ownerFn) {
  {
    return describeNativeComponentFrame(fn, false);
  }
}

function shouldConstruct(Component) {
  var prototype = Component.prototype;
  return !!(prototype && prototype.isReactComponent);
}

function describeUnknownElementTypeFrameInDEV(type, source, ownerFn) {

  if (type == null) {
    return '';
  }

  if (typeof type === 'function') {
    {
      return describeNativeComponentFrame(type, shouldConstruct(type));
    }
  }

  if (typeof type === 'string') {
    return describeBuiltInComponentFrame(type);
  }

  switch (type) {
    case REACT_SUSPENSE_TYPE:
      return describeBuiltInComponentFrame('Suspense');

    case REACT_SUSPENSE_LIST_TYPE:
      return describeBuiltInComponentFrame('SuspenseList');
  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_FORWARD_REF_TYPE:
        return describeFunctionComponentFrame(type.render);

      case REACT_MEMO_TYPE:
        // Memo may contain any component type so we recursively resolve it.
        return describeUnknownElementTypeFrameInDEV(type.type, source, ownerFn);

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            // Lazy may contain any component type so we recursively resolve it.
            return describeUnknownElementTypeFrameInDEV(init(payload), source, ownerFn);
          } catch (x) {}
        }
    }
  }

  return '';
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

var loggedTypeFailures = {};
var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame.setExtraStackFrame(null);
    }
  }
}

function checkPropTypes(typeSpecs, values, location, componentName, element) {
  {
    // $FlowFixMe This is okay but Flow doesn't know it.
    var has = Function.call.bind(hasOwnProperty);

    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error$1 = void 0; // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.

        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            // eslint-disable-next-line react-internal/prod-error-codes
            var err = Error((componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' + 'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' + 'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.');
            err.name = 'Invariant Violation';
            throw err;
          }

          error$1 = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED');
        } catch (ex) {
          error$1 = ex;
        }

        if (error$1 && !(error$1 instanceof Error)) {
          setCurrentlyValidatingElement(element);

          error('%s: type specification of %s' + ' `%s` is invalid; the type checker ' + 'function must return `null` or an `Error` but returned a %s. ' + 'You may have forgotten to pass an argument to the type checker ' + 'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' + 'shape all require an argument).', componentName || 'React class', location, typeSpecName, typeof error$1);

          setCurrentlyValidatingElement(null);
        }

        if (error$1 instanceof Error && !(error$1.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error$1.message] = true;
          setCurrentlyValidatingElement(element);

          error('Failed %s type: %s', location, error$1.message);

          setCurrentlyValidatingElement(null);
        }
      }
    }
  }
}

var isArrayImpl = Array.isArray; // eslint-disable-next-line no-redeclare

function isArray(a) {
  return isArrayImpl(a);
}

/*
 * The `'' + value` pattern (used in in perf-sensitive code) throws for Symbol
 * and Temporal.* types. See https://github.com/facebook/react/pull/22064.
 *
 * The functions in this module will throw an easier-to-understand,
 * easier-to-debug exception with a clear errors message message explaining the
 * problem. (Instead of a confusing exception thrown inside the implementation
 * of the `value` object).
 */
// $FlowFixMe only called in DEV, so void return is not possible.
function typeName(value) {
  {
    // toStringTag is needed for namespaced types like Temporal.Instant
    var hasToStringTag = typeof Symbol === 'function' && Symbol.toStringTag;
    var type = hasToStringTag && value[Symbol.toStringTag] || value.constructor.name || 'Object';
    return type;
  }
} // $FlowFixMe only called in DEV, so void return is not possible.


function willCoercionThrow(value) {
  {
    try {
      testStringCoercion(value);
      return false;
    } catch (e) {
      return true;
    }
  }
}

function testStringCoercion(value) {
  // If you ended up here by following an exception call stack, here's what's
  // happened: you supplied an object or symbol value to React (as a prop, key,
  // DOM attribute, CSS property, string ref, etc.) and when React tried to
  // coerce it to a string using `'' + value`, an exception was thrown.
  //
  // The most common types that will cause this exception are `Symbol` instances
  // and Temporal objects like `Temporal.Instant`. But any object that has a
  // `valueOf` or `[Symbol.toPrimitive]` method that throws will also cause this
  // exception. (Library authors do this to prevent users from using built-in
  // numeric operators like `+` or comparison operators like `>=` because custom
  // methods are needed to perform accurate arithmetic or comparison.)
  //
  // To fix the problem, coerce this object or symbol value to a string before
  // passing it to React. The most reliable way is usually `String(value)`.
  //
  // To find which value is throwing, check the browser or debugger console.
  // Before this exception was thrown, there should be `console.error` output
  // that shows the type (Symbol, Temporal.PlainDate, etc.) that caused the
  // problem and how that type was used: key, atrribute, input value prop, etc.
  // In most cases, this console output also shows the component and its
  // ancestor components where the exception happened.
  //
  // eslint-disable-next-line react-internal/safe-string-coercion
  return '' + value;
}
function checkKeyStringCoercion(value) {
  {
    if (willCoercionThrow(value)) {
      error('The provided key is an unsupported type %s.' + ' This value must be coerced to a string before before using it here.', typeName(value));

      return testStringCoercion(value); // throw (to help callers find troubleshooting comments)
    }
  }
}

var ReactCurrentOwner = ReactSharedInternals.ReactCurrentOwner;
var RESERVED_PROPS = {
  key: true,
  ref: true,
  __self: true,
  __source: true
};
var specialPropKeyWarningShown;
var specialPropRefWarningShown;
var didWarnAboutStringRefs;

{
  didWarnAboutStringRefs = {};
}

function hasValidRef(config) {
  {
    if (hasOwnProperty.call(config, 'ref')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'ref').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.ref !== undefined;
}

function hasValidKey(config) {
  {
    if (hasOwnProperty.call(config, 'key')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'key').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.key !== undefined;
}

function warnIfStringRefCannotBeAutoConverted(config, self) {
  {
    if (typeof config.ref === 'string' && ReactCurrentOwner.current && self && ReactCurrentOwner.current.stateNode !== self) {
      var componentName = getComponentNameFromType(ReactCurrentOwner.current.type);

      if (!didWarnAboutStringRefs[componentName]) {
        error('Component "%s" contains the string ref "%s". ' + 'Support for string refs will be removed in a future major release. ' + 'This case cannot be automatically converted to an arrow function. ' + 'We ask you to manually fix this case by using useRef() or createRef() instead. ' + 'Learn more about using refs safely here: ' + 'https://reactjs.org/link/strict-mode-string-ref', getComponentNameFromType(ReactCurrentOwner.current.type), config.ref);

        didWarnAboutStringRefs[componentName] = true;
      }
    }
  }
}

function defineKeyPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingKey = function () {
      if (!specialPropKeyWarningShown) {
        specialPropKeyWarningShown = true;

        error('%s: `key` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingKey.isReactWarning = true;
    Object.defineProperty(props, 'key', {
      get: warnAboutAccessingKey,
      configurable: true
    });
  }
}

function defineRefPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingRef = function () {
      if (!specialPropRefWarningShown) {
        specialPropRefWarningShown = true;

        error('%s: `ref` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingRef.isReactWarning = true;
    Object.defineProperty(props, 'ref', {
      get: warnAboutAccessingRef,
      configurable: true
    });
  }
}
/**
 * Factory method to create a new React element. This no longer adheres to
 * the class pattern, so do not use new to call it. Also, instanceof check
 * will not work. Instead test $$typeof field against Symbol.for('react.element') to check
 * if something is a React Element.
 *
 * @param {*} type
 * @param {*} props
 * @param {*} key
 * @param {string|object} ref
 * @param {*} owner
 * @param {*} self A *temporary* helper to detect places where `this` is
 * different from the `owner` when React.createElement is called, so that we
 * can warn. We want to get rid of owner and replace string `ref`s with arrow
 * functions, and as long as `this` and owner are the same, there will be no
 * change in behavior.
 * @param {*} source An annotation object (added by a transpiler or otherwise)
 * indicating filename, line number, and/or other information.
 * @internal
 */


var ReactElement = function (type, key, ref, self, source, owner, props) {
  var element = {
    // This tag allows us to uniquely identify this as a React Element
    $$typeof: REACT_ELEMENT_TYPE,
    // Built-in properties that belong on the element
    type: type,
    key: key,
    ref: ref,
    props: props,
    // Record the component responsible for creating this element.
    _owner: owner
  };

  {
    // The validation flag is currently mutative. We put it on
    // an external backing store so that we can freeze the whole object.
    // This can be replaced with a WeakMap once they are implemented in
    // commonly used development environments.
    element._store = {}; // To make comparing ReactElements easier for testing purposes, we make
    // the validation flag non-enumerable (where possible, which should
    // include every environment we run tests in), so the test framework
    // ignores it.

    Object.defineProperty(element._store, 'validated', {
      configurable: false,
      enumerable: false,
      writable: true,
      value: false
    }); // self and source are DEV only properties.

    Object.defineProperty(element, '_self', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: self
    }); // Two elements created in two different places should be considered
    // equal for testing purposes and therefore we hide it from enumeration.

    Object.defineProperty(element, '_source', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: source
    });

    if (Object.freeze) {
      Object.freeze(element.props);
      Object.freeze(element);
    }
  }

  return element;
};
/**
 * https://github.com/reactjs/rfcs/pull/107
 * @param {*} type
 * @param {object} props
 * @param {string} key
 */

function jsxDEV(type, config, maybeKey, source, self) {
  {
    var propName; // Reserved names are extracted

    var props = {};
    var key = null;
    var ref = null; // Currently, key can be spread in as a prop. This causes a potential
    // issue if key is also explicitly declared (ie. <div {...props} key="Hi" />
    // or <div key="Hi" {...props} /> ). We want to deprecate key spread,
    // but as an intermediary step, we will use jsxDEV for everything except
    // <div {...props} key="Hi" />, because we aren't currently able to tell if
    // key is explicitly declared to be undefined or not.

    if (maybeKey !== undefined) {
      {
        checkKeyStringCoercion(maybeKey);
      }

      key = '' + maybeKey;
    }

    if (hasValidKey(config)) {
      {
        checkKeyStringCoercion(config.key);
      }

      key = '' + config.key;
    }

    if (hasValidRef(config)) {
      ref = config.ref;
      warnIfStringRefCannotBeAutoConverted(config, self);
    } // Remaining properties are added to a new props object


    for (propName in config) {
      if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
        props[propName] = config[propName];
      }
    } // Resolve default props


    if (type && type.defaultProps) {
      var defaultProps = type.defaultProps;

      for (propName in defaultProps) {
        if (props[propName] === undefined) {
          props[propName] = defaultProps[propName];
        }
      }
    }

    if (key || ref) {
      var displayName = typeof type === 'function' ? type.displayName || type.name || 'Unknown' : type;

      if (key) {
        defineKeyPropWarningGetter(props, displayName);
      }

      if (ref) {
        defineRefPropWarningGetter(props, displayName);
      }
    }

    return ReactElement(type, key, ref, self, source, ReactCurrentOwner.current, props);
  }
}

var ReactCurrentOwner$1 = ReactSharedInternals.ReactCurrentOwner;
var ReactDebugCurrentFrame$1 = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement$1(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame$1.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame$1.setExtraStackFrame(null);
    }
  }
}

var propTypesMisspellWarningShown;

{
  propTypesMisspellWarningShown = false;
}
/**
 * Verifies the object is a ReactElement.
 * See https://reactjs.org/docs/react-api.html#isvalidelement
 * @param {?object} object
 * @return {boolean} True if `object` is a ReactElement.
 * @final
 */


function isValidElement(object) {
  {
    return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
  }
}

function getDeclarationErrorAddendum() {
  {
    if (ReactCurrentOwner$1.current) {
      var name = getComponentNameFromType(ReactCurrentOwner$1.current.type);

      if (name) {
        return '\n\nCheck the render method of `' + name + '`.';
      }
    }

    return '';
  }
}

function getSourceInfoErrorAddendum(source) {
  {
    if (source !== undefined) {
      var fileName = source.fileName.replace(/^.*[\\\/]/, '');
      var lineNumber = source.lineNumber;
      return '\n\nCheck your code at ' + fileName + ':' + lineNumber + '.';
    }

    return '';
  }
}
/**
 * Warn if there's no key explicitly set on dynamic arrays of children or
 * object keys are not valid. This allows us to keep track of children between
 * updates.
 */


var ownerHasKeyUseWarning = {};

function getCurrentComponentErrorInfo(parentType) {
  {
    var info = getDeclarationErrorAddendum();

    if (!info) {
      var parentName = typeof parentType === 'string' ? parentType : parentType.displayName || parentType.name;

      if (parentName) {
        info = "\n\nCheck the top-level render call using <" + parentName + ">.";
      }
    }

    return info;
  }
}
/**
 * Warn if the element doesn't have an explicit key assigned to it.
 * This element is in an array. The array could grow and shrink or be
 * reordered. All children that haven't already been validated are required to
 * have a "key" property assigned to it. Error statuses are cached so a warning
 * will only be shown once.
 *
 * @internal
 * @param {ReactElement} element Element that requires a key.
 * @param {*} parentType element's parent's type.
 */


function validateExplicitKey(element, parentType) {
  {
    if (!element._store || element._store.validated || element.key != null) {
      return;
    }

    element._store.validated = true;
    var currentComponentErrorInfo = getCurrentComponentErrorInfo(parentType);

    if (ownerHasKeyUseWarning[currentComponentErrorInfo]) {
      return;
    }

    ownerHasKeyUseWarning[currentComponentErrorInfo] = true; // Usually the current owner is the offender, but if it accepts children as a
    // property, it may be the creator of the child that's responsible for
    // assigning it a key.

    var childOwner = '';

    if (element && element._owner && element._owner !== ReactCurrentOwner$1.current) {
      // Give the component that originally created this child.
      childOwner = " It was passed a child from " + getComponentNameFromType(element._owner.type) + ".";
    }

    setCurrentlyValidatingElement$1(element);

    error('Each child in a list should have a unique "key" prop.' + '%s%s See https://reactjs.org/link/warning-keys for more information.', currentComponentErrorInfo, childOwner);

    setCurrentlyValidatingElement$1(null);
  }
}
/**
 * Ensure that every element either is passed in a static location, in an
 * array with an explicit keys property defined, or in an object literal
 * with valid key property.
 *
 * @internal
 * @param {ReactNode} node Statically passed child of any type.
 * @param {*} parentType node's parent's type.
 */


function validateChildKeys(node, parentType) {
  {
    if (typeof node !== 'object') {
      return;
    }

    if (isArray(node)) {
      for (var i = 0; i < node.length; i++) {
        var child = node[i];

        if (isValidElement(child)) {
          validateExplicitKey(child, parentType);
        }
      }
    } else if (isValidElement(node)) {
      // This element was passed in a valid location.
      if (node._store) {
        node._store.validated = true;
      }
    } else if (node) {
      var iteratorFn = getIteratorFn(node);

      if (typeof iteratorFn === 'function') {
        // Entry iterators used to provide implicit keys,
        // but now we print a separate warning for them later.
        if (iteratorFn !== node.entries) {
          var iterator = iteratorFn.call(node);
          var step;

          while (!(step = iterator.next()).done) {
            if (isValidElement(step.value)) {
              validateExplicitKey(step.value, parentType);
            }
          }
        }
      }
    }
  }
}
/**
 * Given an element, validate that its props follow the propTypes definition,
 * provided by the type.
 *
 * @param {ReactElement} element
 */


function validatePropTypes(element) {
  {
    var type = element.type;

    if (type === null || type === undefined || typeof type === 'string') {
      return;
    }

    var propTypes;

    if (typeof type === 'function') {
      propTypes = type.propTypes;
    } else if (typeof type === 'object' && (type.$$typeof === REACT_FORWARD_REF_TYPE || // Note: Memo only checks outer props here.
    // Inner props are checked in the reconciler.
    type.$$typeof === REACT_MEMO_TYPE)) {
      propTypes = type.propTypes;
    } else {
      return;
    }

    if (propTypes) {
      // Intentionally inside to avoid triggering lazy initializers:
      var name = getComponentNameFromType(type);
      checkPropTypes(propTypes, element.props, 'prop', name, element);
    } else if (type.PropTypes !== undefined && !propTypesMisspellWarningShown) {
      propTypesMisspellWarningShown = true; // Intentionally inside to avoid triggering lazy initializers:

      var _name = getComponentNameFromType(type);

      error('Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?', _name || 'Unknown');
    }

    if (typeof type.getDefaultProps === 'function' && !type.getDefaultProps.isReactClassApproved) {
      error('getDefaultProps is only used on classic React.createClass ' + 'definitions. Use a static property named `defaultProps` instead.');
    }
  }
}
/**
 * Given a fragment, validate that it can only be provided with fragment props
 * @param {ReactElement} fragment
 */


function validateFragmentProps(fragment) {
  {
    var keys = Object.keys(fragment.props);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];

      if (key !== 'children' && key !== 'key') {
        setCurrentlyValidatingElement$1(fragment);

        error('Invalid prop `%s` supplied to `React.Fragment`. ' + 'React.Fragment can only have `key` and `children` props.', key);

        setCurrentlyValidatingElement$1(null);
        break;
      }
    }

    if (fragment.ref !== null) {
      setCurrentlyValidatingElement$1(fragment);

      error('Invalid attribute `ref` supplied to `React.Fragment`.');

      setCurrentlyValidatingElement$1(null);
    }
  }
}

var didWarnAboutKeySpread = {};
function jsxWithValidation(type, props, key, isStaticChildren, source, self) {
  {
    var validType = isValidElementType(type); // We warn in this case but don't throw. We expect the element creation to
    // succeed and there will likely be errors in render.

    if (!validType) {
      var info = '';

      if (type === undefined || typeof type === 'object' && type !== null && Object.keys(type).length === 0) {
        info += ' You likely forgot to export your component from the file ' + "it's defined in, or you might have mixed up default and named imports.";
      }

      var sourceInfo = getSourceInfoErrorAddendum(source);

      if (sourceInfo) {
        info += sourceInfo;
      } else {
        info += getDeclarationErrorAddendum();
      }

      var typeString;

      if (type === null) {
        typeString = 'null';
      } else if (isArray(type)) {
        typeString = 'array';
      } else if (type !== undefined && type.$$typeof === REACT_ELEMENT_TYPE) {
        typeString = "<" + (getComponentNameFromType(type.type) || 'Unknown') + " />";
        info = ' Did you accidentally export a JSX literal instead of a component?';
      } else {
        typeString = typeof type;
      }

      error('React.jsx: type is invalid -- expected a string (for ' + 'built-in components) or a class/function (for composite ' + 'components) but got: %s.%s', typeString, info);
    }

    var element = jsxDEV(type, props, key, source, self); // The result can be nullish if a mock or a custom function is used.
    // TODO: Drop this when these are no longer allowed as the type argument.

    if (element == null) {
      return element;
    } // Skip key warning if the type isn't valid since our key validation logic
    // doesn't expect a non-string/function type and can throw confusing errors.
    // We don't want exception behavior to differ between dev and prod.
    // (Rendering will throw with a helpful message and as soon as the type is
    // fixed, the key warnings will appear.)


    if (validType) {
      var children = props.children;

      if (children !== undefined) {
        if (isStaticChildren) {
          if (isArray(children)) {
            for (var i = 0; i < children.length; i++) {
              validateChildKeys(children[i], type);
            }

            if (Object.freeze) {
              Object.freeze(children);
            }
          } else {
            error('React.jsx: Static children should always be an array. ' + 'You are likely explicitly calling React.jsxs or React.jsxDEV. ' + 'Use the Babel transform instead.');
          }
        } else {
          validateChildKeys(children, type);
        }
      }
    }

    {
      if (hasOwnProperty.call(props, 'key')) {
        var componentName = getComponentNameFromType(type);
        var keys = Object.keys(props).filter(function (k) {
          return k !== 'key';
        });
        var beforeExample = keys.length > 0 ? '{key: someKey, ' + keys.join(': ..., ') + ': ...}' : '{key: someKey}';

        if (!didWarnAboutKeySpread[componentName + beforeExample]) {
          var afterExample = keys.length > 0 ? '{' + keys.join(': ..., ') + ': ...}' : '{}';

          error('A props object containing a "key" prop is being spread into JSX:\n' + '  let props = %s;\n' + '  <%s {...props} />\n' + 'React keys must be passed directly to JSX without using spread:\n' + '  let props = %s;\n' + '  <%s key={someKey} {...props} />', beforeExample, componentName, afterExample, componentName);

          didWarnAboutKeySpread[componentName + beforeExample] = true;
        }
      }
    }

    if (type === REACT_FRAGMENT_TYPE) {
      validateFragmentProps(element);
    } else {
      validatePropTypes(element);
    }

    return element;
  }
} // These two functions exist to still get child warnings in dev
// even with the prod transform. This means that jsxDEV is purely
// opt-in behavior for better messages but that we won't stop
// giving you warnings if you use production apis.

function jsxWithValidationStatic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, true);
  }
}
function jsxWithValidationDynamic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, false);
  }
}

var jsx =  jsxWithValidationDynamic ; // we may want to special case jsxs internally to take advantage of static children.
// for now we can ship identical prod functions

var jsxs =  jsxWithValidationStatic ;

exports.Fragment = REACT_FRAGMENT_TYPE;
exports.jsx = jsx;
exports.jsxs = jsxs;
  })();
}


/***/ }),

/***/ "./node_modules/react/jsx-runtime.js":
/*!*******************************************!*\
  !*** ./node_modules/react/jsx-runtime.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) {} else {
  module.exports = __webpack_require__(/*! ./cjs/react-jsx-runtime.development.js */ "./node_modules/react/cjs/react-jsx-runtime.development.js");
}


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = window["React"];

/***/ }),

/***/ "@woocommerce/blocks-registry":
/*!******************************************!*\
  !*** external ["wc","wcBlocksRegistry"] ***!
  \******************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wc"]["wcBlocksRegistry"];

/***/ }),

/***/ "@woocommerce/settings":
/*!************************************!*\
  !*** external ["wc","wcSettings"] ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wc"]["wcSettings"];

/***/ }),

/***/ "@wordpress/html-entities":
/*!**************************************!*\
  !*** external ["wp","htmlEntities"] ***!
  \**************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./node_modules/usehooks-ts/dist/index.js":
/*!************************************************!*\
  !*** ./node_modules/usehooks-ts/dist/index.js ***!
  \************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useBoolean: () => (/* binding */ useBoolean),
/* harmony export */   useClickAnyWhere: () => (/* binding */ useClickAnyWhere),
/* harmony export */   useCopyToClipboard: () => (/* binding */ useCopyToClipboard),
/* harmony export */   useCountdown: () => (/* binding */ useCountdown),
/* harmony export */   useCounter: () => (/* binding */ useCounter),
/* harmony export */   useDarkMode: () => (/* binding */ useDarkMode),
/* harmony export */   useDebounceCallback: () => (/* binding */ useDebounceCallback),
/* harmony export */   useDebounceValue: () => (/* binding */ useDebounceValue),
/* harmony export */   useDocumentTitle: () => (/* binding */ useDocumentTitle),
/* harmony export */   useEventCallback: () => (/* binding */ useEventCallback),
/* harmony export */   useEventListener: () => (/* binding */ useEventListener),
/* harmony export */   useHover: () => (/* binding */ useHover),
/* harmony export */   useIntersectionObserver: () => (/* binding */ useIntersectionObserver),
/* harmony export */   useInterval: () => (/* binding */ useInterval),
/* harmony export */   useIsClient: () => (/* binding */ useIsClient),
/* harmony export */   useIsMounted: () => (/* binding */ useIsMounted),
/* harmony export */   useIsomorphicLayoutEffect: () => (/* binding */ useIsomorphicLayoutEffect),
/* harmony export */   useLocalStorage: () => (/* binding */ useLocalStorage),
/* harmony export */   useMap: () => (/* binding */ useMap),
/* harmony export */   useMediaQuery: () => (/* binding */ useMediaQuery),
/* harmony export */   useOnClickOutside: () => (/* binding */ useOnClickOutside),
/* harmony export */   useReadLocalStorage: () => (/* binding */ useReadLocalStorage),
/* harmony export */   useResizeObserver: () => (/* binding */ useResizeObserver),
/* harmony export */   useScreen: () => (/* binding */ useScreen),
/* harmony export */   useScript: () => (/* binding */ useScript),
/* harmony export */   useScrollLock: () => (/* binding */ useScrollLock),
/* harmony export */   useSessionStorage: () => (/* binding */ useSessionStorage),
/* harmony export */   useStep: () => (/* binding */ useStep),
/* harmony export */   useTernaryDarkMode: () => (/* binding */ useTernaryDarkMode),
/* harmony export */   useTimeout: () => (/* binding */ useTimeout),
/* harmony export */   useToggle: () => (/* binding */ useToggle),
/* harmony export */   useUnmount: () => (/* binding */ useUnmount),
/* harmony export */   useWindowSize: () => (/* binding */ useWindowSize)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var lodash_debounce__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash.debounce */ "./node_modules/lodash.debounce/index.js");



// src/useBoolean/useBoolean.ts
function useBoolean(defaultValue = false) {
  if (typeof defaultValue !== "boolean") {
    throw new Error("defaultValue must be `true` or `false`");
  }
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(defaultValue);
  const setTrue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setValue(true);
  }, []);
  const setFalse = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setValue(false);
  }, []);
  const toggle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setValue((x) => !x);
  }, []);
  return { value, setValue, setTrue, setFalse, toggle };
}
var useIsomorphicLayoutEffect = typeof window !== "undefined" ? react__WEBPACK_IMPORTED_MODULE_0__.useLayoutEffect : react__WEBPACK_IMPORTED_MODULE_0__.useEffect;

// src/useEventListener/useEventListener.ts
function useEventListener(eventName, handler, element, options) {
  const savedHandler = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(handler);
  useIsomorphicLayoutEffect(() => {
    savedHandler.current = handler;
  }, [handler]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const targetElement = (element == null ? void 0 : element.current) ?? window;
    if (!(targetElement && targetElement.addEventListener))
      return;
    const listener = (event) => {
      savedHandler.current(event);
    };
    targetElement.addEventListener(eventName, listener, options);
    return () => {
      targetElement.removeEventListener(eventName, listener, options);
    };
  }, [eventName, element, options]);
}

// src/useClickAnyWhere/useClickAnyWhere.ts
function useClickAnyWhere(handler) {
  useEventListener("click", (event) => {
    handler(event);
  });
}
function useCopyToClipboard() {
  const [copiedText, setCopiedText] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const copy = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async (text) => {
    if (!(navigator == null ? void 0 : navigator.clipboard)) {
      console.warn("Clipboard not supported");
      return false;
    }
    try {
      await navigator.clipboard.writeText(text);
      setCopiedText(text);
      return true;
    } catch (error) {
      console.warn("Copy failed", error);
      setCopiedText(null);
      return false;
    }
  }, []);
  return [copiedText, copy];
}
function useCounter(initialValue) {
  const [count, setCount] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialValue ?? 0);
  const increment = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setCount((x) => x + 1);
  }, []);
  const decrement = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setCount((x) => x - 1);
  }, []);
  const reset = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setCount(initialValue ?? 0);
  }, [initialValue]);
  return {
    count,
    increment,
    decrement,
    reset,
    setCount
  };
}
function useInterval(callback, delay) {
  const savedCallback = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(callback);
  useIsomorphicLayoutEffect(() => {
    savedCallback.current = callback;
  }, [callback]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (delay === null) {
      return;
    }
    const id = setInterval(() => {
      savedCallback.current();
    }, delay);
    return () => {
      clearInterval(id);
    };
  }, [delay]);
}

// src/useCountdown/useCountdown.ts
function useCountdown({
  countStart,
  countStop = 0,
  intervalMs = 1e3,
  isIncrement = false
}) {
  const {
    count,
    increment,
    decrement,
    reset: resetCounter
  } = useCounter(countStart);
  const {
    value: isCountdownRunning,
    setTrue: startCountdown,
    setFalse: stopCountdown
  } = useBoolean(false);
  const resetCountdown = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    stopCountdown();
    resetCounter();
  }, [stopCountdown, resetCounter]);
  const countdownCallback = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (count === countStop) {
      stopCountdown();
      return;
    }
    if (isIncrement) {
      increment();
    } else {
      decrement();
    }
  }, [count, countStop, decrement, increment, isIncrement, stopCountdown]);
  useInterval(countdownCallback, isCountdownRunning ? intervalMs : null);
  return [count, { startCountdown, stopCountdown, resetCountdown }];
}
function useEventCallback(fn) {
  const ref = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(() => {
    throw new Error("Cannot call an event handler while rendering.");
  });
  useIsomorphicLayoutEffect(() => {
    ref.current = fn;
  }, [fn]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((...args) => {
    var _a;
    return (_a = ref.current) == null ? void 0 : _a.call(ref, ...args);
  }, [ref]);
}

// src/useLocalStorage/useLocalStorage.ts
var IS_SERVER = typeof window === "undefined";
function useLocalStorage(key, initialValue, options = {}) {
  const { initializeWithValue = true } = options;
  const serializer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (value) => {
      if (options.serializer) {
        return options.serializer(value);
      }
      return JSON.stringify(value);
    },
    [options]
  );
  const deserializer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (value) => {
      if (options.deserializer) {
        return options.deserializer(value);
      }
      if (value === "undefined") {
        return void 0;
      }
      const defaultValue = initialValue instanceof Function ? initialValue() : initialValue;
      let parsed;
      try {
        parsed = JSON.parse(value);
      } catch (error) {
        console.error("Error parsing JSON:", error);
        return defaultValue;
      }
      return parsed;
    },
    [options, initialValue]
  );
  const readValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const initialValueToUse = initialValue instanceof Function ? initialValue() : initialValue;
    if (IS_SERVER) {
      return initialValueToUse;
    }
    try {
      const raw = window.localStorage.getItem(key);
      return raw ? deserializer(raw) : initialValueToUse;
    } catch (error) {
      console.warn(`Error reading localStorage key \u201C${key}\u201D:`, error);
      return initialValueToUse;
    }
  }, [initialValue, key, deserializer]);
  const [storedValue, setStoredValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return readValue();
    }
    return initialValue instanceof Function ? initialValue() : initialValue;
  });
  const setValue = useEventCallback((value) => {
    if (IS_SERVER) {
      console.warn(
        `Tried setting localStorage key \u201C${key}\u201D even though environment is not a client`
      );
    }
    try {
      const newValue = value instanceof Function ? value(readValue()) : value;
      window.localStorage.setItem(key, serializer(newValue));
      setStoredValue(newValue);
      window.dispatchEvent(new StorageEvent("local-storage", { key }));
    } catch (error) {
      console.warn(`Error setting localStorage key \u201C${key}\u201D:`, error);
    }
  });
  const removeValue = useEventCallback(() => {
    if (IS_SERVER) {
      console.warn(
        `Tried removing localStorage key \u201C${key}\u201D even though environment is not a client`
      );
    }
    const defaultValue = initialValue instanceof Function ? initialValue() : initialValue;
    window.localStorage.removeItem(key);
    setStoredValue(defaultValue);
    window.dispatchEvent(new StorageEvent("local-storage", { key }));
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setStoredValue(readValue());
  }, [key]);
  const handleStorageChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (event) => {
      if (event.key && event.key !== key) {
        return;
      }
      setStoredValue(readValue());
    },
    [key, readValue]
  );
  useEventListener("storage", handleStorageChange);
  useEventListener("local-storage", handleStorageChange);
  return [storedValue, setValue, removeValue];
}
var IS_SERVER2 = typeof window === "undefined";
function useMediaQuery(query, {
  defaultValue = false,
  initializeWithValue = true
} = {}) {
  const getMatches = (query2) => {
    if (IS_SERVER2) {
      return defaultValue;
    }
    return window.matchMedia(query2).matches;
  };
  const [matches, setMatches] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return getMatches(query);
    }
    return defaultValue;
  });
  function handleChange() {
    setMatches(getMatches(query));
  }
  useIsomorphicLayoutEffect(() => {
    const matchMedia = window.matchMedia(query);
    handleChange();
    if (matchMedia.addListener) {
      matchMedia.addListener(handleChange);
    } else {
      matchMedia.addEventListener("change", handleChange);
    }
    return () => {
      if (matchMedia.removeListener) {
        matchMedia.removeListener(handleChange);
      } else {
        matchMedia.removeEventListener("change", handleChange);
      }
    };
  }, [query]);
  return matches;
}

// src/useDarkMode/useDarkMode.ts
var COLOR_SCHEME_QUERY = "(prefers-color-scheme: dark)";
var LOCAL_STORAGE_KEY = "usehooks-ts-dark-mode";
function useDarkMode(options = {}) {
  const {
    defaultValue,
    localStorageKey = LOCAL_STORAGE_KEY,
    initializeWithValue = true
  } = options;
  const isDarkOS = useMediaQuery(COLOR_SCHEME_QUERY, {
    initializeWithValue,
    defaultValue
  });
  const [isDarkMode, setDarkMode] = useLocalStorage(
    localStorageKey,
    defaultValue ?? isDarkOS ?? false,
    { initializeWithValue }
  );
  useIsomorphicLayoutEffect(() => {
    if (isDarkOS !== isDarkMode) {
      setDarkMode(isDarkOS);
    }
  }, [isDarkOS]);
  return {
    isDarkMode,
    toggle: () => {
      setDarkMode((prev) => !prev);
    },
    enable: () => {
      setDarkMode(true);
    },
    disable: () => {
      setDarkMode(false);
    },
    set: (value) => {
      setDarkMode(value);
    }
  };
}
function useUnmount(func) {
  const funcRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(func);
  funcRef.current = func;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(
    () => () => {
      funcRef.current();
    },
    []
  );
}

// src/useDebounceCallback/useDebounceCallback.ts
function useDebounceCallback(func, delay = 500, options) {
  const debouncedFunc = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  useUnmount(() => {
    if (debouncedFunc.current) {
      debouncedFunc.current.cancel();
    }
  });
  const debounced = (0,react__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const debouncedFuncInstance = lodash_debounce__WEBPACK_IMPORTED_MODULE_1__(func, delay, options);
    const wrappedFunc = (...args) => {
      return debouncedFuncInstance(...args);
    };
    wrappedFunc.cancel = () => {
      debouncedFuncInstance.cancel();
    };
    wrappedFunc.isPending = () => {
      return !!debouncedFunc.current;
    };
    wrappedFunc.flush = () => {
      return debouncedFuncInstance.flush();
    };
    return wrappedFunc;
  }, [func, delay, options]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    debouncedFunc.current = lodash_debounce__WEBPACK_IMPORTED_MODULE_1__(func, delay, options);
  }, [func, delay, options]);
  return debounced;
}
function useDebounceValue(initialValue, delay, options) {
  const eq = (options == null ? void 0 : options.equalityFn) ?? ((left, right) => left === right);
  const unwrappedInitialValue = initialValue instanceof Function ? initialValue() : initialValue;
  const [debouncedValue, setDebouncedValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(unwrappedInitialValue);
  const previousValueRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(unwrappedInitialValue);
  const updateDebouncedValue = useDebounceCallback(
    setDebouncedValue,
    delay,
    options
  );
  if (!eq(previousValueRef.current, unwrappedInitialValue)) {
    updateDebouncedValue(unwrappedInitialValue);
    previousValueRef.current = unwrappedInitialValue;
  }
  return [debouncedValue, updateDebouncedValue];
}
function useDocumentTitle(title, options = {}) {
  const { preserveTitleOnUnmount = true } = options;
  const defaultTitle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  useIsomorphicLayoutEffect(() => {
    defaultTitle.current = window.document.title;
  }, []);
  useIsomorphicLayoutEffect(() => {
    window.document.title = title;
  }, [title]);
  useUnmount(() => {
    if (!preserveTitleOnUnmount && defaultTitle.current) {
      window.document.title = defaultTitle.current;
    }
  });
}
function useHover(elementRef) {
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleMouseEnter = () => {
    setValue(true);
  };
  const handleMouseLeave = () => {
    setValue(false);
  };
  useEventListener("mouseenter", handleMouseEnter, elementRef);
  useEventListener("mouseleave", handleMouseLeave, elementRef);
  return value;
}
function useIntersectionObserver({
  threshold = 0,
  root = null,
  rootMargin = "0%",
  freezeOnceVisible = false,
  initialIsIntersecting = false,
  onChange
} = {}) {
  var _a;
  const [ref, setRef] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [state, setState] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => ({
    isIntersecting: initialIsIntersecting,
    entry: void 0
  }));
  const callbackRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)();
  callbackRef.current = onChange;
  const frozen = ((_a = state.entry) == null ? void 0 : _a.isIntersecting) && freezeOnceVisible;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!ref)
      return;
    if (!("IntersectionObserver" in window))
      return;
    if (frozen)
      return;
    let unobserve;
    const observer = new IntersectionObserver(
      (entries) => {
        const thresholds = Array.isArray(observer.thresholds) ? observer.thresholds : [observer.thresholds];
        entries.forEach((entry) => {
          const isIntersecting = entry.isIntersecting && thresholds.some((threshold2) => entry.intersectionRatio >= threshold2);
          setState({ isIntersecting, entry });
          if (callbackRef.current) {
            callbackRef.current(isIntersecting, entry);
          }
          if (isIntersecting && freezeOnceVisible && unobserve) {
            unobserve();
            unobserve = void 0;
          }
        });
      },
      { threshold, root, rootMargin }
    );
    observer.observe(ref);
    return () => {
      observer.disconnect();
    };
  }, [
    ref,
    // eslint-disable-next-line react-hooks/exhaustive-deps
    JSON.stringify(threshold),
    root,
    rootMargin,
    frozen,
    freezeOnceVisible
  ]);
  const prevRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    var _a2;
    if (!ref && ((_a2 = state.entry) == null ? void 0 : _a2.target) && !freezeOnceVisible && !frozen && prevRef.current !== state.entry.target) {
      prevRef.current = state.entry.target;
      setState({ isIntersecting: initialIsIntersecting, entry: void 0 });
    }
  }, [ref, state.entry, freezeOnceVisible, frozen, initialIsIntersecting]);
  const result = [
    setRef,
    !!state.isIntersecting,
    state.entry
  ];
  result.ref = result[0];
  result.isIntersecting = result[1];
  result.entry = result[2];
  return result;
}
function useIsClient() {
  const [isClient, setClient] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setClient(true);
  }, []);
  return isClient;
}
function useIsMounted() {
  const isMounted = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    isMounted.current = true;
    return () => {
      isMounted.current = false;
    };
  }, []);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => isMounted.current, []);
}
function useMap(initialState = /* @__PURE__ */ new Map()) {
  const [map, setMap] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(new Map(initialState));
  const actions = {
    set: (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((key, value) => {
      setMap((prev) => {
        const copy = new Map(prev);
        copy.set(key, value);
        return copy;
      });
    }, []),
    setAll: (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((entries) => {
      setMap(() => new Map(entries));
    }, []),
    remove: (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((key) => {
      setMap((prev) => {
        const copy = new Map(prev);
        copy.delete(key);
        return copy;
      });
    }, []),
    reset: (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
      setMap(() => /* @__PURE__ */ new Map());
    }, [])
  };
  return [map, actions];
}

// src/useOnClickOutside/useOnClickOutside.ts
function useOnClickOutside(ref, handler, eventType = "mousedown", eventListenerOptions = {}) {
  useEventListener(
    eventType,
    (event) => {
      const target = event.target;
      if (!target || !target.isConnected) {
        return;
      }
      const isOutside = Array.isArray(ref) ? ref.filter((r) => Boolean(r.current)).every((r) => r.current && !r.current.contains(target)) : ref.current && !ref.current.contains(target);
      if (isOutside) {
        handler(event);
      }
    },
    void 0,
    eventListenerOptions
  );
}
var IS_SERVER3 = typeof window === "undefined";
function useReadLocalStorage(key, options = {}) {
  let { initializeWithValue = true } = options;
  if (IS_SERVER3) {
    initializeWithValue = false;
  }
  const deserializer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (value) => {
      if (options.deserializer) {
        return options.deserializer(value);
      }
      if (value === "undefined") {
        return void 0;
      }
      let parsed;
      try {
        parsed = JSON.parse(value);
      } catch (error) {
        console.error("Error parsing JSON:", error);
        return null;
      }
      return parsed;
    },
    [options]
  );
  const readValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (IS_SERVER3) {
      return null;
    }
    try {
      const raw = window.localStorage.getItem(key);
      return raw ? deserializer(raw) : null;
    } catch (error) {
      console.warn(`Error reading localStorage key \u201C${key}\u201D:`, error);
      return null;
    }
  }, [key, deserializer]);
  const [storedValue, setStoredValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return readValue();
    }
    return void 0;
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setStoredValue(readValue());
  }, [key]);
  const handleStorageChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (event) => {
      if (event.key && event.key !== key) {
        return;
      }
      setStoredValue(readValue());
    },
    [key, readValue]
  );
  useEventListener("storage", handleStorageChange);
  useEventListener("local-storage", handleStorageChange);
  return storedValue;
}
var initialSize = {
  width: void 0,
  height: void 0
};
function useResizeObserver(options) {
  const { ref, box = "content-box" } = options;
  const [{ width, height }, setSize] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(initialSize);
  const isMounted = useIsMounted();
  const previousSize = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)({ ...initialSize });
  const onResize = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(void 0);
  onResize.current = options.onResize;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!ref.current)
      return;
    if (typeof window === "undefined" || !("ResizeObserver" in window))
      return;
    const observer = new ResizeObserver(([entry]) => {
      const boxProp = box === "border-box" ? "borderBoxSize" : box === "device-pixel-content-box" ? "devicePixelContentBoxSize" : "contentBoxSize";
      const newWidth = extractSize(entry, boxProp, "inlineSize");
      const newHeight = extractSize(entry, boxProp, "blockSize");
      const hasChanged = previousSize.current.width !== newWidth || previousSize.current.height !== newHeight;
      if (hasChanged) {
        const newSize = { width: newWidth, height: newHeight };
        previousSize.current.width = newWidth;
        previousSize.current.height = newHeight;
        if (onResize.current) {
          onResize.current(newSize);
        } else {
          if (isMounted()) {
            setSize(newSize);
          }
        }
      }
    });
    observer.observe(ref.current, { box });
    return () => {
      observer.disconnect();
    };
  }, [box, ref, isMounted]);
  return { width, height };
}
function extractSize(entry, box, sizeType) {
  if (!entry[box]) {
    if (box === "contentBoxSize") {
      return entry.contentRect[sizeType === "inlineSize" ? "width" : "height"];
    }
    return void 0;
  }
  return Array.isArray(entry[box]) ? entry[box][0][sizeType] : (
    // @ts-ignore Support Firefox's non-standard behavior
    entry[box][sizeType]
  );
}
var IS_SERVER4 = typeof window === "undefined";
function useScreen(options = {}) {
  let { initializeWithValue = true } = options;
  if (IS_SERVER4) {
    initializeWithValue = false;
  }
  const readScreen = () => {
    if (IS_SERVER4) {
      return void 0;
    }
    return window.screen;
  };
  const [screen, setScreen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return readScreen();
    }
    return void 0;
  });
  const debouncedSetScreen = useDebounceCallback(
    setScreen,
    options.debounceDelay
  );
  function handleSize() {
    const newScreen = readScreen();
    const setSize = options.debounceDelay ? debouncedSetScreen : setScreen;
    if (newScreen) {
      const {
        width,
        height,
        availHeight,
        availWidth,
        colorDepth,
        orientation,
        pixelDepth
      } = newScreen;
      setSize({
        width,
        height,
        availHeight,
        availWidth,
        colorDepth,
        orientation,
        pixelDepth
      });
    }
  }
  useEventListener("resize", handleSize);
  useIsomorphicLayoutEffect(() => {
    handleSize();
  }, []);
  return screen;
}
var cachedScriptStatuses = /* @__PURE__ */ new Map();
function getScriptNode(src) {
  const node = document.querySelector(
    `script[src="${src}"]`
  );
  const status = node == null ? void 0 : node.getAttribute("data-status");
  return {
    node,
    status
  };
}
function useScript(src, options) {
  const [status, setStatus] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (!src || (options == null ? void 0 : options.shouldPreventLoad)) {
      return "idle";
    }
    if (typeof window === "undefined") {
      return "loading";
    }
    return cachedScriptStatuses.get(src) ?? "loading";
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!src || (options == null ? void 0 : options.shouldPreventLoad)) {
      return;
    }
    const cachedScriptStatus = cachedScriptStatuses.get(src);
    if (cachedScriptStatus === "ready" || cachedScriptStatus === "error") {
      setStatus(cachedScriptStatus);
      return;
    }
    const script = getScriptNode(src);
    let scriptNode = script.node;
    if (!scriptNode) {
      scriptNode = document.createElement("script");
      scriptNode.src = src;
      scriptNode.async = true;
      if (options == null ? void 0 : options.id) {
        scriptNode.id = options.id;
      }
      scriptNode.setAttribute("data-status", "loading");
      document.body.appendChild(scriptNode);
      const setAttributeFromEvent = (event) => {
        const scriptStatus = event.type === "load" ? "ready" : "error";
        scriptNode == null ? void 0 : scriptNode.setAttribute("data-status", scriptStatus);
      };
      scriptNode.addEventListener("load", setAttributeFromEvent);
      scriptNode.addEventListener("error", setAttributeFromEvent);
    } else {
      setStatus(script.status ?? cachedScriptStatus ?? "loading");
    }
    const setStateFromEvent = (event) => {
      const newStatus = event.type === "load" ? "ready" : "error";
      setStatus(newStatus);
      cachedScriptStatuses.set(src, newStatus);
    };
    scriptNode.addEventListener("load", setStateFromEvent);
    scriptNode.addEventListener("error", setStateFromEvent);
    return () => {
      if (scriptNode) {
        scriptNode.removeEventListener("load", setStateFromEvent);
        scriptNode.removeEventListener("error", setStateFromEvent);
      }
      if (scriptNode && (options == null ? void 0 : options.removeOnUnmount)) {
        scriptNode.remove();
        cachedScriptStatuses.delete(src);
      }
    };
  }, [src, options == null ? void 0 : options.shouldPreventLoad, options == null ? void 0 : options.removeOnUnmount, options == null ? void 0 : options.id]);
  return status;
}
var IS_SERVER5 = typeof window === "undefined";
function useScrollLock(options = {}) {
  const { autoLock = true, lockTarget, widthReflow = true } = options;
  const [isLocked, setIsLocked] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const target = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const originalStyle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const lock = () => {
    if (target.current) {
      const { overflow, paddingRight } = target.current.style;
      originalStyle.current = { overflow, paddingRight };
      if (widthReflow) {
        const offsetWidth = target.current === document.body ? window.innerWidth : target.current.offsetWidth;
        const currentPaddingRight = parseInt(window.getComputedStyle(target.current).paddingRight, 10) || 0;
        const scrollbarWidth = offsetWidth - target.current.scrollWidth;
        target.current.style.paddingRight = `${scrollbarWidth + currentPaddingRight}px`;
      }
      target.current.style.overflow = "hidden";
      setIsLocked(true);
    }
  };
  const unlock = () => {
    if (target.current && originalStyle.current) {
      target.current.style.overflow = originalStyle.current.overflow;
      if (widthReflow) {
        target.current.style.paddingRight = originalStyle.current.paddingRight;
      }
    }
    setIsLocked(false);
  };
  useIsomorphicLayoutEffect(() => {
    if (IS_SERVER5)
      return;
    if (lockTarget) {
      target.current = typeof lockTarget === "string" ? document.querySelector(lockTarget) : lockTarget;
    }
    if (!target.current) {
      target.current = document.body;
    }
    if (autoLock) {
      lock();
    }
    return () => {
      unlock();
    };
  }, [autoLock, lockTarget, widthReflow]);
  return { isLocked, lock, unlock };
}
var IS_SERVER6 = typeof window === "undefined";
function useSessionStorage(key, initialValue, options = {}) {
  const { initializeWithValue = true } = options;
  const serializer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (value) => {
      if (options.serializer) {
        return options.serializer(value);
      }
      return JSON.stringify(value);
    },
    [options]
  );
  const deserializer = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (value) => {
      if (options.deserializer) {
        return options.deserializer(value);
      }
      if (value === "undefined") {
        return void 0;
      }
      const defaultValue = initialValue instanceof Function ? initialValue() : initialValue;
      let parsed;
      try {
        parsed = JSON.parse(value);
      } catch (error) {
        console.error("Error parsing JSON:", error);
        return defaultValue;
      }
      return parsed;
    },
    [options, initialValue]
  );
  const readValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const initialValueToUse = initialValue instanceof Function ? initialValue() : initialValue;
    if (IS_SERVER6) {
      return initialValueToUse;
    }
    try {
      const raw = window.sessionStorage.getItem(key);
      return raw ? deserializer(raw) : initialValueToUse;
    } catch (error) {
      console.warn(`Error reading sessionStorage key \u201C${key}\u201D:`, error);
      return initialValueToUse;
    }
  }, [initialValue, key, deserializer]);
  const [storedValue, setStoredValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return readValue();
    }
    return initialValue instanceof Function ? initialValue() : initialValue;
  });
  const setValue = useEventCallback((value) => {
    if (IS_SERVER6) {
      console.warn(
        `Tried setting sessionStorage key \u201C${key}\u201D even though environment is not a client`
      );
    }
    try {
      const newValue = value instanceof Function ? value(readValue()) : value;
      window.sessionStorage.setItem(key, serializer(newValue));
      setStoredValue(newValue);
      window.dispatchEvent(new StorageEvent("session-storage", { key }));
    } catch (error) {
      console.warn(`Error setting sessionStorage key \u201C${key}\u201D:`, error);
    }
  });
  const removeValue = useEventCallback(() => {
    if (IS_SERVER6) {
      console.warn(
        `Tried removing sessionStorage key \u201C${key}\u201D even though environment is not a client`
      );
    }
    const defaultValue = initialValue instanceof Function ? initialValue() : initialValue;
    window.sessionStorage.removeItem(key);
    setStoredValue(defaultValue);
    window.dispatchEvent(new StorageEvent("session-storage", { key }));
  });
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setStoredValue(readValue());
  }, [key]);
  const handleStorageChange = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (event) => {
      if (event.key && event.key !== key) {
        return;
      }
      setStoredValue(readValue());
    },
    [key, readValue]
  );
  useEventListener("storage", handleStorageChange);
  useEventListener("session-storage", handleStorageChange);
  return [storedValue, setValue, removeValue];
}
function useStep(maxStep) {
  const [currentStep, setCurrentStep] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(1);
  const canGoToNextStep = currentStep + 1 <= maxStep;
  const canGoToPrevStep = currentStep - 1 > 0;
  const setStep = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(
    (step) => {
      const newStep = step instanceof Function ? step(currentStep) : step;
      if (newStep >= 1 && newStep <= maxStep) {
        setCurrentStep(newStep);
        return;
      }
      throw new Error("Step not valid");
    },
    [maxStep, currentStep]
  );
  const goToNextStep = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (canGoToNextStep) {
      setCurrentStep((step) => step + 1);
    }
  }, [canGoToNextStep]);
  const goToPrevStep = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    if (canGoToPrevStep) {
      setCurrentStep((step) => step - 1);
    }
  }, [canGoToPrevStep]);
  const reset = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setCurrentStep(1);
  }, []);
  return [
    currentStep,
    {
      goToNextStep,
      goToPrevStep,
      canGoToNextStep,
      canGoToPrevStep,
      setStep,
      reset
    }
  ];
}

// src/useTernaryDarkMode/useTernaryDarkMode.ts
var COLOR_SCHEME_QUERY2 = "(prefers-color-scheme: dark)";
var LOCAL_STORAGE_KEY2 = "usehooks-ts-ternary-dark-mode";
function useTernaryDarkMode({
  defaultValue = "system",
  localStorageKey = LOCAL_STORAGE_KEY2,
  initializeWithValue = true
} = {}) {
  const isDarkOS = useMediaQuery(COLOR_SCHEME_QUERY2, { initializeWithValue });
  const [mode, setMode] = useLocalStorage(localStorageKey, defaultValue, {
    initializeWithValue
  });
  const isDarkMode = mode === "dark" || mode === "system" && isDarkOS;
  const toggleTernaryDarkMode = () => {
    const modes = ["light", "system", "dark"];
    setMode((prevMode) => {
      const nextIndex = (modes.indexOf(prevMode) + 1) % modes.length;
      return modes[nextIndex];
    });
  };
  return {
    isDarkMode,
    ternaryDarkMode: mode,
    setTernaryDarkMode: setMode,
    toggleTernaryDarkMode
  };
}
function useTimeout(callback, delay) {
  const savedCallback = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(callback);
  useIsomorphicLayoutEffect(() => {
    savedCallback.current = callback;
  }, [callback]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (!delay && delay !== 0) {
      return;
    }
    const id = setTimeout(() => {
      savedCallback.current();
    }, delay);
    return () => {
      clearTimeout(id);
    };
  }, [delay]);
}
function useToggle(defaultValue) {
  const [value, setValue] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(!!defaultValue);
  const toggle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setValue((x) => !x);
  }, []);
  return [value, toggle, setValue];
}
var IS_SERVER7 = typeof window === "undefined";
function useWindowSize(options = {}) {
  let { initializeWithValue = true } = options;
  if (IS_SERVER7) {
    initializeWithValue = false;
  }
  const [windowSize, setWindowSize] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(() => {
    if (initializeWithValue) {
      return {
        width: window.innerWidth,
        height: window.innerHeight
      };
    }
    return {
      width: void 0,
      height: void 0
    };
  });
  const debouncedSetWindowSize = useDebounceCallback(
    setWindowSize,
    options.debounceDelay
  );
  function handleSize() {
    const setSize = options.debounceDelay ? debouncedSetWindowSize : setWindowSize;
    setSize({
      width: window.innerWidth,
      height: window.innerHeight
    });
  }
  useEventListener("resize", handleSize);
  useIsomorphicLayoutEffect(() => {
    handleSize();
  }, []);
  return windowSize;
}




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
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!*********************************************!*\
  !*** ./assets/js/paytrail-block-payment.js ***!
  \*********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/blocks-registry */ "@woocommerce/blocks-registry");
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _containers_paytrail_container__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./containers/paytrail-container */ "./assets/js/containers/paytrail-container.jsx");
/* harmony import */ var _components_paytrail_label__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/paytrail-label */ "./assets/js/components/paytrail-label.jsx");
/* harmony import */ var _scss_payment_blocks_scss__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../scss/payment_blocks.scss */ "./assets/scss/payment_blocks.scss");
/* harmony import */ var _paytrail__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./paytrail */ "./assets/js/paytrail.js");
/* harmony import */ var _paytrail__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_paytrail__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/react/jsx-runtime.js");







const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paytrail_data', {});

/**
 * Paytrail payment method config object.
 */

(0,_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_0__.registerPaymentMethod)({
  paymentMethodId: 'paytrail',
  name: "paytrail",
  label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_components_paytrail_label__WEBPACK_IMPORTED_MODULE_3__.PaytrailLabel, {}),
  content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_containers_paytrail_container__WEBPACK_IMPORTED_MODULE_2__.PaytrailContainer, {}),
  edit: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_containers_paytrail_container__WEBPACK_IMPORTED_MODULE_2__.PaytrailContainer, {}),
  canMakePayment: () => true,
  ariaLabel: _components_paytrail_label__WEBPACK_IMPORTED_MODULE_3__.label,
  supports: {
    features: settings.supports
  }
});
})();

/******/ })()
;
//# sourceMappingURL=blocks.js.map