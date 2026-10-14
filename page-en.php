<?php
/**
 * Fallback homepage at /en/ for when WPML is unavailable.
 *
 * WordPress uses this template for a Page with the slug "en". See
 * "WPML outage fallback" in README.md. Loads front-page.php so the
 * fallback always matches the real homepage.
 */
require get_template_directory() . '/front-page.php';
