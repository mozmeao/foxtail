/**
 * Protocol Module Initialization
 * 
 * Initialize Protocol JavaScript modules after they've been loaded.
 * This script should be loaded after the Protocol vendor files.
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    function domReady(fn) {
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(fn, 1);
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // Initialize Protocol modules when DOM is ready
    domReady(function() {
        // Initialize Base module (adds 'js' class, removes 'no-js')
        if (typeof window.MzpBase !== 'undefined' && window.MzpBase.init) {
            window.MzpBase.init();
        }

        // Initialize Navigation module
        if (typeof window.MzpNavigation !== 'undefined' && window.MzpNavigation.init) {
            window.MzpNavigation.init();
        }

        // Initialize Newsletter module
        if (typeof window.MzpNewsletter !== 'undefined' && window.MzpNewsletter.init) {
            window.MzpNewsletter.init();
        }

        // Initialize Footer module
        if (typeof window.MzpFooter !== 'undefined' && window.MzpFooter.init) {
            window.MzpFooter.init();
        }

        console.log('Protocol modules initialized');
    });
})();