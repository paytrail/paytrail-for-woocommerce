!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t(require("jQuery")):"function"==typeof define&&define.amd?define("paytrail",["jQuery"],t):"object"==typeof exports?exports.paytrail=t(require("jQuery")):e.paytrail=t(e.jQuery)}(self,(e=>(()=>{"use strict";var t={1145:t=>{t.exports=e}},n={};return function e(o){var r=n[o];if(void 0!==r)return r.exports;var a=n[o]={exports:{}};return t[o](a,a.exports,e),a.exports}(1145)(document).ready((function(e){var t,n,o,r=function(){console.log("Closing overlay");var e=document.getElementById("paytrail-overlay-container");if(e){var t=window.getComputedStyle(e).getPropertyValue("display");e.style.display="none"===t?"block":"none"}};t={autoOpen:!1,modal:!0,width:500,open:function(t,n){e(this).parent().css("z-index",9998)},close:function(t,n){e(this).parent().css("z-index","")}},n=document.getElementById("user-data-form"),o=document.getElementById("open-lightbox"),n&&o&&o.addEventListener("click",(function(){e(n).dialog(t).dialog("open")})),function(){var e=document.getElementById("user-data-form");e&&e.addEventListener("submit",(function(e){e.preventDefault();var t=new FormData(this),n=new URLSearchParams(t).toString();window.open("https://www.paytrail.com/tilaa-palvelu/lisatiedot?"+n+"&tekninen_alusta=WooCommerce","_blank").focus()}))}();var a=document.getElementById("credentials"),i=document.getElementById("test-mode-button"),d=document.getElementById("woocommerce_paytrail_enable_test_mode");document.getElementById("mainform"),(a||i)&&(a.addEventListener("click",(function(){r()})),i.addEventListener("click",(function(e){d.checked=!d.checked;var t=new Event("change",{bubbles:!0});d.dispatchEvent(t);var n=document.getElementById("mainform").querySelector('[name="save"]');n&&(n.click(),r()),e.preventDefault()})))})),{}})()));