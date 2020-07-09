<?php
/**
 * Functions specific to advanced custom fields
 *
 */


// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', get_stylesheet_directory() . '/inc/acf/' );
define( 'MY_ACF_URL', get_stylesheet_directory_uri() . '/inc/acf/' );

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

// Add option pages
if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Content Settings',
		'menu_title'	=> 'Content Settings',
		'menu_slug' 	=> 'foxtail-content-settings',
		'redirect'		=> 'foxtail-content-settings'
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Front Page Settings (EN)',
		'menu_title'	=> 'Front Page (EN)',
		'parent_slug'	=> 'foxtail-content-settings',
    ));
    
    acf_add_options_sub_page(array(
		'page_title' 	=> 'Front Page Settings (DE)',
		'menu_title'	=> 'Front Page (DE)',
		'parent_slug'	=> 'foxtail-content-settings',
	));
	
}

?>