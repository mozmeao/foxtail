<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

?>

<section class="no-results not-found">
  <header class="page-header ft-u-center-text ">
    <h1 class="page-title"><?php _e('Nothing Found', 'foxtail'); ?></h1>
  </header><!-- .page-header -->

  <div class="page-content">
    <?php
    if ( is_home() && current_user_can( 'publish_posts' ) ) :

      printf(
        '<p>' . wp_kses(
          /* translators: 1: link to WP admin new post page. */
          esc_html_e('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'foxtail'),
          array(
            'a' => array(
              'href' => array(),
            ),
          )
        ) . '</p>',
        esc_url( admin_url( 'post-new.php' ) )
      );

    elseif ( is_search() ) :
      ?>
    <p>
      <?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'foxtail'); ?>
    </p>
    <?php
      get_search_form();
    else :
      ?>

    <p>
      <?php _e('It seems we can’t find what you’re looking for. Perhaps searching can help.', 'foxtail'); ?>
    </p>
    <?php
      get_search_form();
    endif;
    ?>
  </div><!-- .page-content -->
</section><!-- .no-results -->
