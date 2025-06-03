(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["MzpNavigation"] = factory();
	else
		root["MzpNavigation"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 546:
/***/ (function(module) {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var MzpNavigation = {};
var _navElem;
var _navItemsLists;
var _navMenuButtons;
var _options = {
  onNavOpen: null,
  onNavClose: null
};
var _ticking = false;
var _lastKnownScrollPosition = 0;
var _animationFrameID = null;
var _stickyScrollOffset = 300;
var _wideBreakpoint = '768px';
var _tallBreakpoint = '600px';
var _mqLargeNav;
var _viewport = document.getElementsByTagName('html')[0];

/**
 * Does the viewport meet the minimum width and height
 * requirements for sticky behavior?
 * @returns {Boolean}
 */
MzpNavigation.isLargeViewport = function () {
  return _mqLargeNav.matches;
};

/**
 * Feature detect for sticky navigation
 * @returns {Boolean}
 */
MzpNavigation.supportsSticky = function () {
  if (typeof window.MzpSupports !== 'undefined') {
    return window.MzpSupports.matchMedia && window.MzpSupports.classList && window.MzpSupports.requestAnimationFrame && window.MzpSupports.cssFeatureQueries && CSS.supports('position', 'sticky');
  } else {
    return false;
  }
};

/**
 * Scroll event listener. No computationally expensive
 * operations such as DOM modifications should happen
 * here. Instead we throttle using `requestAnimationFrame`.
 */
MzpNavigation.onScroll = function () {
  if (!_ticking) {
    _animationFrameID = window.requestAnimationFrame(MzpNavigation.checkScrollPosition);
    _ticking = true;
  }
};

/**
 * Create sticky state for the navigation.
 */
MzpNavigation.createSticky = function () {
  _viewport.classList.add('mzp-has-sticky-navigation');
  _animationFrameID = window.requestAnimationFrame(MzpNavigation.checkScrollPosition);
  window.addEventListener('scroll', MzpNavigation.onScroll, false);
};

/**
 * Destroy sticky state for the navigation.
 */
MzpNavigation.destroySticky = function () {
  _viewport.classList.remove('mzp-has-sticky-navigation');
  _navElem.classList.remove('mzp-is-scrolling');
  _navElem.classList.remove('mzp-is-hidden');
  _lastKnownScrollPosition = 0;
  if (_animationFrameID) {
    window.cancelAnimationFrame(_animationFrameID);
  }
  window.removeEventListener('scroll', MzpNavigation.onScroll, false);
};

/**
 * Initialize sticky state for the navigation.
 * Uses `matchMedia` to determine if conditions
 * for sticky navigation are satisfied.
 */
MzpNavigation.initSticky = function () {
  _mqLargeNav = matchMedia("(min-width: ".concat(_wideBreakpoint, ") and (min-height: ").concat(_tallBreakpoint, ")"));
  function makeStickyNav(mq) {
    if (mq.matches) {
      MzpNavigation.createSticky();
    } else {
      MzpNavigation.destroySticky();
    }
  }
  if (window.matchMedia('all').addEventListener) {
    _mqLargeNav.addEventListener('change', makeStickyNav, false);
  } else if (window.matchMedia('all').addListener) {
    _mqLargeNav.addListener(makeStickyNav);
  }
  if (MzpNavigation.isLargeViewport()) {
    MzpNavigation.createSticky();
  }
};

/**
 * Implements sticky navigation behavior as
 * user scrolls up and down the viewport.
 */
MzpNavigation.checkScrollPosition = function () {
  // add styling for when scrolling the viewport
  if (window.scrollY > 0) {
    _navElem.classList.add('mzp-is-scrolling');
  } else {
    _navElem.classList.remove('mzp-is-scrolling');
  }

  // scrolling down
  if (window.scrollY > _lastKnownScrollPosition) {
    // hide the sticky nav shortly after scrolling down the viewport.
    if (window.scrollY > _stickyScrollOffset) {
      // if there's a menu currently open, close it.
      if (typeof window.MzpMenu !== 'undefined') {
        window.MzpMenu.close();
      }
      _navElem.classList.add('mzp-is-hidden');
    }
  }
  // scrolling up
  else {
    _navElem.classList.remove('mzp-is-hidden');
  }
  _lastKnownScrollPosition = window.scrollY;
  _ticking = false;
};

/**
 * Event handler for navigation menu button `click` events.
 */
MzpNavigation.onClick = function (e) {
  var thisNavItemList = e.target.parentNode.querySelector('.mzp-c-navigation-items');
  e.preventDefault();

  // Update button state
  e.target.classList.toggle('mzp-is-active');

  // Update menu state
  thisNavItemList.classList.toggle('mzp-is-open');

  // Update aria-expended state on menu.
  var expanded = thisNavItemList.classList.contains('mzp-is-open') ? true : false;
  e.target.setAttribute('aria-expanded', expanded);
  if (expanded) {
    if (typeof _options.onNavOpen === 'function') {
      _options.onNavOpen(thisNavItemList);
    }
  } else {
    if (typeof _options.onNavClose === 'function') {
      _options.onNavClose(thisNavItemList);
    }
  }
};

/**
 * use Intersection Observer API to keep track of when the mobile
 * nav menu is displayed to handle aria roles better
 */
MzpNavigation.menuButtonVisible = function (callback) {
  // check if Intersection observer is supported
  if (window.MzpSupports !== 'undefined' && window.MzpSupports.intersectionObserver) {
    var observer = new IntersectionObserver(function (entries) {
      for (var index = 0; index < entries.length; index++) {
        var entry = entries[index];
        callback(entry.intersectionRatio > 0, entry.target);
      }
    }, {
      root: document.documentElement
    });
    for (var index = 0; index < _navMenuButtons.length; index++) {
      var button = _navMenuButtons[index];
      observer.observe(button);
    }
  }
},
/**
 * Set initial ARIA navigation states.
 */
MzpNavigation.setAria = function () {
  if (window.MzpSupports !== 'undefined' && window.MzpSupports.intersectionObserver) {
    MzpNavigation.menuButtonVisible(function (isVisible, menuButton) {
      if (isVisible) {
        // if the menu button is visible -  set the 'aria-expanded' role based on whether the menu is open or not
        var isActive = !!menuButton.classList.contains('mzp-is-active');
        menuButton.setAttribute('aria-expanded', isActive);
      } else {
        // if the menu is not visible - remove the aria role, since elements
        // with `display: none` are not read to screen readers
        menuButton.removeAttribute('aria-expanded');
      }
    });
  } else {
    for (var index = 0; index < _navMenuButtons.length; index++) {
      var menuButton = _navMenuButtons[index];
      var isActive = menuButton.classList.contains('mzp-is-active') && getComputedStyle(menuButton).display !== 'none';
      menuButton.setAttribute('aria-expanded', isActive);
    }
  }
};

/**
 * Bind navigation event handlers.
 */
MzpNavigation.bindEvents = function () {
  _navItemsLists = document.querySelectorAll('.mzp-c-navigation-items');
  if (_navItemsLists.length > 0) {
    _navMenuButtons = document.querySelectorAll('.mzp-c-navigation-menu-button');
    for (var index = 0; index < _navMenuButtons.length; index++) {
      var menuButton = _navMenuButtons[index];
      menuButton.addEventListener('click', MzpNavigation.onClick, false);
    }
    MzpNavigation.setAria();
  }
};

/**
 * Initialize menu.
 * @param {Object} options - configurable options.
 */
MzpNavigation.init = function (options) {
  if (_typeof(options) === 'object') {
    for (var i in options) {
      if (Object.prototype.hasOwnProperty.call(options, i)) {
        _options[i] = options[i];
      }
    }
  }
  MzpNavigation.bindEvents();

  /**
   * Init (optional) sticky navigation.
   * If there are multiple navigation organisms on a single page,
   * assume only the first (and hence top-most) instance can and
   * will be sticky.
   *
   * Do not init sticky navigation if user prefers reduced motion
   */

  _navElem = document.querySelector('.mzp-c-navigation');
  var _navIsSticky = _navElem && _navElem.classList.contains('mzp-is-sticky') && MzpNavigation.supportsSticky();
  if (_navIsSticky && matchMedia('(prefers-reduced-motion)').matches) {
    _navElem.classList.remove('mzp-is-sticky');
  } else if (_navIsSticky) {
    MzpNavigation.initSticky();
  }
};
module.exports = MzpNavigation;

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
/******/ 	var __webpack_exports__ = __webpack_require__(546);
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});