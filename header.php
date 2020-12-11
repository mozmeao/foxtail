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
<html class="no-js" <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-QSYYYSCH2V"></script>
  <script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  // REPLACE WITH THE FINAL GA TAG
  // THIS TAG FOR TESTING PURPOSES ONLY
  gtag('config', 'G-QSYYYSCH2V');
  </script>

  <?php wp_head(); ?>
  <?php echo foxtail_seo(); ?>
</head>

<body <?php body_class(''); ?> data-blogname="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
  <div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'foxtail'); ?></a>

    <header class="ft-c-header">
      <div class="ft-c-header__toggle">
        <div class="ft-c-hamburger"></div>
      </div>
      <div class="ft-c-header__logo">
        <a href="<?php echo home_url() ?>">Mozilla</a>
      </div>
      <div class="ft-c-header__search ft-c-header__search--mobile">
        <img class="ft-c-header__search-icon" src="<?php echo get_template_directory_uri() . '/assets/images/icons/search.svg' ?>" alt="search" />
      </div>
      <div id="nav" class="ft-c-header__nav">
        <?php bem_menu('primary_menu', 'ft-c-primary-nav'); ?>
      </div>
      <div class="ft-c-header__cta-wrap">
        <a id="nav-download"
          href="https://www.mozilla.org/firefox/new/?utm_source=blog.mozilla.org&amp;utm_medium=referral&amp;utm_campaign=blog-nav"
          rel="external" class="mzp-c-button mzp-t-product mzp-t-lg"><?php _e('Download Firefox', 'foxtail'); ?></a>
      </div>
      <div class="ft-c-header__search">
        <img class="ft-c-header__search-icon" src="<?php echo get_template_directory_uri() . '/assets/images/icons/search.svg' ?>" alt="search" />
      </div>


    </header><!-- #masthead -->
    <div class="ft-c-search">
      <h4><?php _e('Search', 'foxtail'); ?></h4>
      <?php get_search_form(); ?>
    </div>

    <div id="content" class="site-content">
