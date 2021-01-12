<?php
/**
 * php This file is the template file for the homepage of foxtail
 */

get_header();
set_query_var('lang', 'en');

global $_displayed_posts;
$_displayed_posts = array();

if (class_exists('ACF')) {
  $featured_post = get_field('post_to_feature_en', 'option');
}

set_query_var('featured_post', $featured_post);


// if there is no ACF, then just show a basic loop
if (!class_exists('ACF')) {
  get_template_part('partials/basic-loop'); 

// otherwise show a custom static homepage thta is set by ACF
} else { ?>
  <!-- This is the large featured post section -->
  <?php get_template_part('partials/featured-post'); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- The 2nd featured section which has 3 items -->
  <?php get_template_part('partials/featured-secondary'); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- The Featured Video section -->
  <?php get_template_part('partials/featured-video'); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- Collection #1 -->
  <?php get_template_part('partials/collection/collection-small-cards', null, array('collectionNum' => '1')); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- Collection #2 -->
  <?php get_template_part('partials/collection/collection-large-cards', null, array('collectionNum' => '2')); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- Collection #3 -->
  <?php get_template_part('partials/collection/collection-large-cards', null, array('collectionNum' => '3')); ?>

  <div class="ft-l-space-unrelated"></div>

  <!-- Collection #4 -->
  <?php
    if( get_field('display_collection_4_en', 'option') ) {
      get_template_part('partials/collection/collection-small-cards', null, array('collectionNum' => '4'));
      echo '<div class="ft-l-space-unrelated"></div>';
    }
    ?>


  <!-- Collection #5 -->
  <?php
    if( get_field('display_collection_5_en', 'option') ) {
      get_template_part('partials/collection/collection-large-cards', null, array('collectionNum' => '5'));
      echo '<div class="ft-l-space-unrelated"></div>';
    }
    ?>



  <!-- Featured CTA -->
  <?php get_template_part('partials/featured-extra'); ?>

<!-- endif -->
<?php } ?>




<?php
get_footer();
