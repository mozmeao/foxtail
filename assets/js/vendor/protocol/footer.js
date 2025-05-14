(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["MzpFooter"] = factory();
	else
		root["MzpFooter"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 715:
/***/ (function(module) {

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var MzpFooter = {};
MzpFooter.init = function () {
  var footerHeadings = '.mzp-c-footer-sections .mzp-c-footer-heading';

  // removes Details component if screen size is big
  function screenChange(mq) {
    if (mq.matches) {
      window.MzpDetails.init(footerHeadings);
    } else {
      window.MzpDetails.destroy(footerHeadings);
    }
  }

  // check we have global Supports and Details library
  if (typeof window.MzpSupports !== 'undefined' && typeof window.MzpDetails !== 'undefined') {
    // check browser supports matchMedia
    if (window.MzpSupports.matchMedia) {
      var _mqWide = matchMedia('(max-width: 479px)');

      // initialize details if screen is small
      if (_mqWide.matches) {
        window.MzpDetails.init(footerHeadings);
      }
      if (window.matchMedia('all').addEventListener) {
        _mqWide.addEventListener('change', screenChange, false);
      } else if (window.matchMedia('all').addListener) {
        _mqWide.addListener(screenChange);
      }
    }
  }
};
module.exports = MzpFooter;

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
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__(715);
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});