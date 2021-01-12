<?php

// Get cat, tag, and set variables
$orig_post = $post;
global $post;
$tags = wp_get_post_tags($post->ID);
$categories = get_the_category($post->ID);

// Its best to use tags, so set up args using tags
if ($tags) {
  $tag_ids = array();
  foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;

  $args=array(
    'tag__in' => $tag_ids,
    'post__not_in' => array($post->ID),
    'posts_per_page'=>3, // Number of related posts that will be shown.
    'ignore_sticky_posts'=>1
  );
}

// If no tag exists, use categories
elseif ($categories) {
  $category_ids = array();
  foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

  $args=array(
    'category__in' => $category_ids,
    'post__not_in' => array($post->ID),
    'posts_per_page'=> 3, // Number of related posts that will be shown.
    'ignore_sticky_posts'=>1
  );
}

if ($args) {

  $my_query = new wp_query( $args );

  if( $my_query->have_posts() ) {
    ?>
<div class="ft-l-space-related"></div>
<section id="related-articles" class="ft-c-post-list">
  <div class="ft-l-container">
    <h2 class="ft-c-post-list__title"><?php _e('Related Articles', 'foxtail'); ?></h2>
    <div class="ft-c-post-list__wrap--three-column">
    <?php
      while( $my_query->have_posts() ) :
        $my_query->the_post(); ?>
      <?php get_template_part('partials/card'); ?>
    <?php endwhile; ?>
    </div>
  </div>
</section>
<div class="ft-l-space-related"></div>

<?php
  }
}

$post = $orig_post;

wp_reset_query();

?>
