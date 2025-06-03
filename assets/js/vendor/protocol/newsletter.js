(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["MzpNewsletter"] = factory();
	else
		root["MzpNewsletter"] = factory();
})(self, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 195:
/***/ (function(module) {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var form;
var ERROR_LIST = {
  COUNTRY_ERROR: 'Country not selected',
  EMAIL_INVALID_ERROR: 'Invalid email address',
  EMAIL_UNKNOWN_ERROR: 'Email address not known',
  LANGUAGE_ERROR: 'Language not selected',
  LEGAL_TERMS_ERROR: 'Terms not checked',
  NEWSLETTER_ERROR: 'Newsletter not selected',
  NOT_FOUND: 'Not Found',
  PRIVACY_POLICY_ERROR: 'Privacy policy not checked',
  REASON_ERROR: 'Reason not selected'
};
var MzpNewsletter = {
  /**
   * Really primitive validation (e.g a@a)
   * matches built-in validation in Firefox
   * @param {String} email
   * @returns {Boolean}
   */
  checkEmailValidity: function checkEmailValidity(email) {
    return /\S+@\S+/.test(email);
  },
  /**
   * Add disabled property to all form fields.
   * @param {HTMLFormElement} form
   */
  disableFormFields: function disableFormFields(form) {
    var formFields = form.querySelectorAll('input, button, select');
    for (var i = 0; i < formFields.length; i++) {
      formFields[i].disabled = true;
    }
  },
  /**
   * Remove disabled property to all form fields.
   * @param {HTMLFormElement} form
   */
  enableFormFields: function enableFormFields(form) {
    var formFields = form.querySelectorAll('input, button, select');
    for (var i = 0; i < formFields.length; i++) {
      formFields[i].disabled = false;
    }
  },
  /**
   * Hide all visible form error labels.
   * @param {HTMLFormElement} form
   */
  clearFormErrors: function clearFormErrors(form) {
    var errorMsgs = form.querySelectorAll('.mzp-c-form-errors li');
    form.querySelector('.mzp-c-form-errors').classList.add('hidden');
    for (var i = 0; i < errorMsgs.length; i++) {
      errorMsgs[i].classList.add('hidden');
    }
  },
  handleFormError: function handleFormError(msg) {
    var error;
    MzpNewsletter.enableFormFields(form);
    form.querySelector('.mzp-c-form-errors').classList.remove('hidden');
    switch (msg) {
      case ERROR_LIST.EMAIL_INVALID_ERROR:
        error = form.querySelector('.error-email-invalid');
        break;
      case ERROR_LIST.NEWSLETTER_ERROR:
        form.querySelector('.error-newsletter-checkbox').classList.remove('hidden');
        break;
      case ERROR_LIST.COUNTRY_ERROR:
        error = form.querySelector('.error-select-country');
        break;
      case ERROR_LIST.LANGUAGE_ERROR:
        error = form.querySelector('.error-select-language');
        break;
      case ERROR_LIST.PRIVACY_POLICY_ERROR:
        error = form.querySelector('.error-privacy-policy');
        break;
      case ERROR_LIST.LEGAL_TERMS_ERROR:
        error = form.querySelector('.error-terms');
        break;
      default:
        error = form.querySelector('.error-try-again-later');
    }
    if (error) {
      error.classList.remove('hidden');
    }
    if (typeof MzpNewsletter.customErrorCallback === 'function') {
      MzpNewsletter.customErrorCallback(msg);
    }
  },
  handleFormSuccess: function handleFormSuccess() {
    form.classList.add('hidden');
    document.getElementById('newsletter-thanks').classList.remove('hidden');
    if (typeof MzpNewsletter.customSuccessCallback === 'function') {
      MzpNewsletter.customSuccessCallback();
    }
  },
  /**
   * Perform an AJAX POST to Basket
   * @param {String} email
   * @param {String} params (URI encoded query string)
   * @param {String} url (Basket API endpoint)
   * @param {Function} successCallback
   * @param {Function} errorCallback
   */
  postToBasket: function postToBasket(email, params, url, successCallback, errorCallback) {
    var xhr = new XMLHttpRequest();

    // Emails used in automation for page-level integration tests
    // should avoid hitting basket directly.
    if (email === 'success@example.com') {
      successCallback();
      return;
    } else if (email === 'failure@example.com') {
      errorCallback();
      return;
    }
    xhr.onload = function (e) {
      var response = e.target.response || e.target.responseText;
      if (_typeof(response) !== 'object') {
        response = JSON.parse(response);
      }
      if (response) {
        if (response.status === 'ok' && e.target.status >= 200 && e.target.status < 300) {
          successCallback();
        } else if (response.status === 'error' && response.desc) {
          errorCallback(response.desc);
        } else {
          errorCallback();
        }
      } else {
        errorCallback();
      }
    };
    xhr.onerror = errorCallback;
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.timeout = 5000;
    xhr.ontimeout = errorCallback;
    xhr.responseType = 'json';
    xhr.send(params);
  },
  serialize: function serialize() {
    // Email address
    var email = encodeURIComponent(form.querySelector('input[type="email"]').value);

    // Country (optional form <select>)
    var countrySelect = form.querySelector('select[name="country"]');
    var country = countrySelect ? "&country=".concat(countrySelect.value) : '';

    // Language (get by DOM ID as field can be <input> or <select>)
    var lang = form.querySelector('#id_lang').value;

    // Selected newsletter(s)
    var newsletters = Array.from(form.querySelectorAll('input[name="newsletters"]:checked')).map(function (newsletter) {
      return "".concat(newsletter.value);
    }).join(',');
    newsletters = encodeURIComponent(newsletters);

    // Source URL (hidden field)
    var sourceUrl = encodeURIComponent(form.querySelector('input[name="source_url"]').value);
    return "email=".concat(email).concat(country, "&lang=").concat(lang, "&source_url=").concat(sourceUrl, "&newsletters=").concat(newsletters);
  },
  validateFields: function validateFields() {
    var email = form.querySelector('input[type="email"]').value;
    var privacy = form.querySelector('input[name="privacy"]:checked') ? true : false;
    var newsletters = form.querySelectorAll('input[name="newsletters"]:checked');
    var countrySelect = form.querySelector('select[name="country"]');
    var lang = form.querySelector('#id_lang').value;
    var terms = form.querySelector('input[name="terms"]');

    // Really basic client side email validity check.
    if (!MzpNewsletter.checkEmailValidity(email)) {
      MzpNewsletter.handleFormError(ERROR_LIST.EMAIL_INVALID_ERROR);
      return false;
    }

    // Check for country selection value.
    if (countrySelect && !countrySelect.value) {
      MzpNewsletter.handleFormError(ERROR_LIST.COUNTRY_ERROR);
      return false;
    }

    // Check for language selection value.
    if (!lang) {
      MzpNewsletter.handleFormError(ERROR_LIST.LANGUAGE_ERROR);
      return false;
    }

    // Confirm at least one newsletter is checked
    if (newsletters.length === 0) {
      MzpNewsletter.handleFormError(ERROR_LIST.NEWSLETTER_ERROR);
      return false;
    }

    // Confirm privacy policy is checked
    if (!privacy) {
      MzpNewsletter.handleFormError(ERROR_LIST.PRIVACY_POLICY_ERROR);
      return false;
    }
    if (terms && !terms.checked) {
      MzpNewsletter.handleFormError(ERROR_LIST.LEGAL_TERMS_ERROR);
      return false;
    }
    return true;
  },
  subscribe: function subscribe(e) {
    var url = form.getAttribute('action');
    var email = form.querySelector('input[type="email"]').value;
    e.preventDefault();
    e.stopPropagation();

    // Disable form fields until POST has completed.
    MzpNewsletter.disableFormFields(form);

    // Clear any prior messages that might have been displayed.
    MzpNewsletter.clearFormErrors(form);

    // Perform client side form field validation.
    if (!MzpNewsletter.validateFields()) {
      return;
    }
    var params = MzpNewsletter.serialize();
    MzpNewsletter.postToBasket(email, params, url, MzpNewsletter.handleFormSuccess, MzpNewsletter.handleFormError);
  },
  init: function init(customSuccessCallback, customErrorCallback) {
    form = document.getElementById('newsletter-form');
    var submitButton;
    var formDetails;
    var emailField;
    var formExpanded;
    function emailFormShowDetails() {
      if (!formExpanded) {
        formDetails.style.display = 'block';
        formExpanded = true;
      }
    }
    if (form) {
      submitButton = document.getElementById('newsletter-submit');
      formDetails = document.getElementById('newsletter-details');
      emailField = document.querySelector('.mzp-js-email-field');
      formExpanded = window.getComputedStyle(formDetails).display === 'none' ? false : true;

      // Expand email form on input focus or submit if details aren't visible
      emailField.addEventListener('focus', function () {
        emailFormShowDetails();
      }, false);
      submitButton.addEventListener('click', function (e) {
        if (!formExpanded) {
          e.preventDefault();
          emailFormShowDetails();
        }
      }, false);
      form.addEventListener('submit', function (e) {
        if (!formExpanded) {
          e.preventDefault();
          emailFormShowDetails();
        }
      }, false);
      form.addEventListener('submit', MzpNewsletter.subscribe, false);
      if (typeof customSuccessCallback === 'function') {
        MzpNewsletter.customSuccessCallback = customSuccessCallback;
      }
      if (typeof customErrorCallback === 'function') {
        MzpNewsletter.customErrorCallback = customErrorCallback;
      }
    }
  }
};
module.exports = MzpNewsletter;

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
/******/ 	var __webpack_exports__ = __webpack_require__(195);
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});