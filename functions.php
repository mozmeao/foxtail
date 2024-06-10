<?php
/**
 * Foxtail functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Foxtail
 */

if ( ! function_exists( 'foxtail_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function foxtail_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Foxtail, use a find and replace
         * to change 'foxtail' to the name of your theme in all the template files.
         */
        // load_theme_textdomain( 'foxtail', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        /*
         * Custom Image Sizes + calculations
         */
        require get_template_directory() . '/inc/image-sizes.php';


        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        // add_theme_support( 'custom-logo', array(
        //  'height'      => 250,
        //  'width'       => 250,
        //  'flex-width'  => true,
        //  'flex-height' => true,
        // ) );
    }
endif;
add_action( 'after_setup_theme', 'foxtail_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function foxtail_content_width() {
    // This variable is intended to be overruled from themes.
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS['content_width'] = apply_filters( 'foxtail_content_width', 640 );
}
add_action( 'after_setup_theme', 'foxtail_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
// function foxtail_widgets_init() {
//  register_sidebar( array(
//      'name'          => esc_html__( 'Sidebar', 'foxtail' ),
//      'id'            => 'sidebar-1',
//      'description'   => esc_html__( 'Add widgets here.', 'foxtail' ),
//      'before_widget' => '<section id="%1$s" class="widget %2$s">',
//      'after_widget'  => '</section>',
//      'before_title'  => '<h2 class="widget-title">',
//      'after_title'   => '</h2>',
//  ) );
// }
// add_action( 'widgets_init', 'foxtail_widgets_init' );

/**
 * Enqueue scripts and styles.
 */

function foxtail_scripts() {
    $theme_data = wp_get_theme();
    $themeVersion = $theme_data->get( 'Version' );

    // Frontend scripts.
    if ( ! is_admin() ) {
        // Enqueue vendors first.
        wp_enqueue_script( 'foxtail-vendorjs', get_template_directory_uri() . '/assets/js/vendor.min.js', array(), $themeVersion, true );

        // Enqueue custom JS after vendors.
        wp_enqueue_script( 'foxtail-customjs', get_template_directory_uri() . '/assets/js/custom.min.js', array(), $themeVersion, true );

        // Minified and Concatenated styles.
        wp_enqueue_style( 'foxtail-style', get_template_directory_uri() . '/assets/css/main.min.css', array(), $themeVersion, 'all' );
    }
}
add_action( 'wp_enqueue_scripts', 'foxtail_scripts' );


// Enqueue WordPress theme styles within Gutenberg.
add_theme_support( 'editor-styles' );
add_editor_style( 'assets/css/editor.min.css' );


/**
 * Implement the Custom Header feature.
 */
// require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
// require get_template_directory() . '/inc/customizer.php';

/**
 * Advanced Custom Fields
 */

require get_template_directory() . '/inc/advanced-custom-fields.php';


/**
 * Function for disiplaying pagination
 */

require get_template_directory() . '/inc/pagination.php';


/**
 * Function for adding custom post type
 */

require get_template_directory() . '/inc/collection-post-type.php';

/**
 * Function for adding Menus and allowing for BEM menus
 */

 require get_template_directory() . '/inc/bem-menu.php';

 register_nav_menu('primary_menu', 'primary site menu');


 /**
 * Function for header SEO & Meta tags
 */

    require get_template_directory() . '/inc/seo.php';

    /**
 * Helper functions
 */

require get_template_directory() . '/inc/helpers.php';

/**
 * Halt the main query in the case of an empty search
 */
add_filter( 'posts_search', function( $search, \WP_Query $q )
{
    if( ! is_admin() && empty( $search ) && $q->is_search() && $q->is_main_query() )
        $search .=" AND 0=1 ";

    return $search;
}, 10, 2 );

/**
 * Add Gutenberg block category
 */
add_filter( 'block_categories', function( $categories, $post ) {
    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'custom',
                'title' => 'Mozilla Custom',
            ),
        )
    );
}, 10, 2 );

/**
 * Add custom Gutenberg blocks (using ACF)
 */

function acf_inline_cta_block() {

    // check function exists
    if( function_exists('acf_register_block') ) {

        // register a portfolio item block
        acf_register_block(array(
            'name'              => 'inline-cta',
            'title'             => __('Inline CTA'),
            'description'       => __('A custom block for displaying a CTA inside a blog post'),
            'render_template'   => '/partials/blocks/inline-cta/block-inline-cta.php',
            'category'          => 'custom',
            'icon'              => 'external',
            'keywords'          => array( 'custom, mozilla, cta, inline' ),
        ));
    }
}

add_action('acf/init', 'acf_inline_cta_block');

/**
 * Add Co-authors in RSS and other feeds
 * /wp-includes/feed-rss2.php uses the_author(), so we selectively filter the_author value
 */

 function moz_coauthors_in_rss($the_author) {

    if (!is_feed() || !function_exists('coauthors')){
        // if it's not a feed or coauthors is disabled, return regular author
        return $the_author;
    }
    else{
        // return coauthors
        return coauthors(null, null, null, null, false);
    }
}

add_filter('the_author', 'moz_coauthors_in_rss');
