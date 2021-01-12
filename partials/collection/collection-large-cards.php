<?php
  $lang = 'en'; // getting ready for future changes
  $collectionNum = ''; // this should have collectionNum passwed to it with a number as a string

  if ( $args['collectionNum'] ) {
    $collectionNum = $args['collectionNum'];
  }

  global $_displayed_posts;

  $args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'post__not_in' => $_displayed_posts,
  );

  // Select posts based on either a category or a tag
  $categoryortag = get_field('feature_category_or_tag_' . $collectionNum . '_' . $lang, 'option');
  if ( $categoryortag === 'category' ):
    $args['cat'] = get_field('featured_collection_' . $collectionNum . '_category_' . $lang, 'option');
  elseif ($categoryortag && $categoryortag === 'tag'):
    $args['tag_id'] = get_field('featured_collection_' . $collectionNum . '_tag_' . $lang, 'option');
  endif;

  $arr_posts = new WP_Query( $args );

  // add a class if there are fewer than 6 posts
  $num = $arr_posts->post_count;
  $numclass = '';
  if ($num < 6 || get_field('limit_posts_' . $collectionNum . '_en', 'option')) {
    $numclass= 'ft-p-lownum';
  }
?>

<section class="ft-c-post-list">
  <div class="ft-l-container">
    <span class="ft-c-label"><?php _e('Featured Collection', 'foxtail'); ?></span>
    <h2 class="ft-c-post-list__title">
      <?php the_field('feature_collection_' . $collectionNum . '_title_' . $lang, 'option') ?></h2>
    <div class="ft-c-post-list__wrap--three-column ft-c-post-list__wrap--frontpage <?php echo $numclass ?>">
    <?php
      if ( $arr_posts->have_posts() ) :
        while ( $arr_posts->have_posts() ) :
          $arr_posts->the_post();
          get_template_part('partials/card');
        endwhile;
      endif;
    ?>
    </div>
    <? $collection_link = get_field('featured_collection_' . $collectionNum . '_link_' . $lang, 'option') ?>
    <div class="ft-c-post-list__cta">
      <a href="<?php if ($collection_link) echo esc_url($collection_link['url']) ?>" class="mzp-c-cta-link">
        <?php echo $collection_link['title'] ?>
      </a>
    </div>
  </div>
</section>
