<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Foxtail
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">

  <?php wp_head(); ?>
</head>

<body <?php body_class('mzp-t-firefox'); ?>>
  <div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'foxtail' ); ?></a>

    <header class="ft-c-header">
      <div class="ft-c-header__toggle">
        <div class="ft-c-hamburger"></div>
      </div>
      <div class="ft-c-header__logo">
        <a href="<?php echo home_url() ?>">Firefox Blog</a>
      </div>
      <div class="ft-c-header__search ft-c-header__search--mobile">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/search.svg' ?>" alt="search" />
      </div>
      <div class="ft-c-header__nav">
        <?php bem_menu('primary_menu', 'ft-c-primary-nav',); ?>
      </div>
      <div class="ft-c-header__cta-wrap">
        <a href="https://www.mozilla.org/firefox/new/?utm_source=blog.mozilla.org&amp;utm_medium=referral&amp;utm_campaign=blog-nav"
          rel="external" class="ft-c-header__cta">Download Firefox</a>
      </div>
      <div class="ft-c-header__search">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/search.svg' ?>" alt="search" />
      </div>


    </header><!-- #masthead -->

    <div id="content" class="site-content">