(()=>{var e={840:(e,t,n)=>{"use strict";n.r(t),n.d(t,{default:()=>r});var a=n(601),s=n.n(a),o=n(314),i=n.n(o)()(s());i.push([e.id,"",""]);const r=i},706:(e,t,n)=>{"use strict";n.r(t),n.d(t,{default:()=>r});var a=n(601),s=n.n(a),o=n(314),i=n.n(o)()(s());i.push([e.id,"",""]);const r=i},314:e=>{"use strict";e.exports=function(e){var t=[];return t.toString=function(){return this.map((function(t){var n="",a=void 0!==t[5];return t[4]&&(n+="@supports (".concat(t[4],") {")),t[2]&&(n+="@media ".concat(t[2]," {")),a&&(n+="@layer".concat(t[5].length>0?" ".concat(t[5]):""," {")),n+=e(t),a&&(n+="}"),t[2]&&(n+="}"),t[4]&&(n+="}"),n})).join("")},t.i=function(e,n,a,s,o){"string"==typeof e&&(e=[[null,e,void 0]]);var i={};if(a)for(var r=0;r<this.length;r++){var l=this[r][0];null!=l&&(i[l]=!0)}for(var c=0;c<e.length;c++){var d=[].concat(e[c]);a&&i[d[0]]||(void 0!==o&&(void 0===d[5]||(d[1]="@layer".concat(d[5].length>0?" ".concat(d[5]):""," {").concat(d[1],"}")),d[5]=o),n&&(d[2]?(d[1]="@media ".concat(d[2]," {").concat(d[1],"}"),d[2]=n):d[2]=n),s&&(d[4]?(d[1]="@supports (".concat(d[4],") {").concat(d[1],"}"),d[4]=s):d[4]="".concat(s)),t.push(d))}},t}},601:e=>{"use strict";e.exports=function(e){return e[1]}}},t={};function n(a){var s=t[a];if(void 0!==s)return s.exports;var o=t[a]={id:a,exports:{}};return e[a](o,o.exports,n),o.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var a in t)n.o(t,a)&&!n.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n(706),n(840);let a,s=!1;const o=function(){const e=document.getElementById("payment"),t=document.getElementsByClassName("payment_method_paytrail");var n;new Date-a<300?setTimeout(o,300):(s=!1,n=t[0],Math.round(e.offsetWidth)<600?(n.classList.remove("col-wide"),n.classList.add("col-narrow")):(n.classList.remove("col-narrow"),n.classList.add("col-wide")))};window.initPaytrail=()=>{const e=document.getElementsByClassName("paytrail-provider-group");1===e.length?Array.from(e).map((e=>{e.style="display: none;";const t=document.getElementsByClassName("paytrail-woocommerce-payment-fields");Array.from(t).map((e=>e.classList.remove("hidden")))})):e.length>1&&Array.from(e).map((e=>{e&&!e.hasAttribute("listenerOnClick")&&(e.addEventListener("click",(function(e){if(e.preventDefault(),this.classList.contains("selected"))return this.classList.remove("selected"),void this.nextSibling.classList.add("hidden");const t=document.getElementsByClassName("paytrail-provider-group selected");0!==t.length&&t[0].classList.remove("selected");const n=document.getElementsByClassName("paytrail-woocommerce-payment-fields");Array.from(n).map((e=>e.classList.add("hidden"))),this.classList.add("selected"),this.nextSibling.classList.remove("hidden"),this.nextSibling.closest("ul").scrollIntoView(!1)})),e.setAttribute("listenerOnClick","true"))}));const t=document.getElementsByClassName("paytrail-woocommerce-payment-fields--list-item paytrail-for-woocommerce-tokenized-payment-method");t.length>0&&Array.from(t).map((e=>{e.addEventListener("click",(function(e){e.preventDefault(),Array.from(t).map((e=>e.classList.remove("selected")));let n=this.childNodes[0].childNodes[0];void 0!==n&&(n.checked=!0,this.classList.add("selected"))}))})),o(),function(){if("undefined"!=typeof paytrail){const e=paytrail.applePayButton,t=document.getElementsByClassName("paytrail-woocommerce-payment-fields--list-item apple-pay");if(0===t.length)return;const n=t[0].parentNode.previousElementSibling,a=n.getElementsByClassName("provider-list")[0],s=a.textContent.trim(),o=a.textContent.split(",").map((e=>e.trim())).some((e=>"Apple Pay"!==e));o||e?.canMakePayment()?o&&!e?.canMakePayment()&&s.includes("Apple Pay")&&(a.textContent=s.replace(/,\s*Apple Pay|Apple Pay,\s*|Apple Pay/g,"").trim()):n.style.display="none",t.length>0&&e?.canMakePayment()&&t[0].classList.remove("apple-pay")}}()},document.addEventListener("DOMContentLoaded",(function(e){initPaytrail()})),window.addEventListener("resize",(function(){a=new Date,!1===s&&(s=!0,setTimeout(o,300))}))})();