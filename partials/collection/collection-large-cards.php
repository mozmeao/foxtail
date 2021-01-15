<?php
  $lang = get_query_var('lang');
  $collectionNum = ''; // this should have collectionNum passwed to it with a number as a string

  if ( $args['collectionNum'] ) {
    $collectionNum = $args['collectionNum'];
  }

  global $_displayed_posts;

  if ($lang):
    $group = get_field($lang . '_featured_collection_' . $collectionNum, 'option');
    $categoryortag = $group['category_or_tag'];
    $category = $group['category'];
    $tag = $group['tag'];
    $collection_link = $group['link'];
    $title = $group['title'];
    $limit = $group['limit_posts'];
  endif;

  $args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'post__not_in' => $_displayed_posts,
  );

  // Select posts based on either a category or a tag
  if ( $categoryortag === 'category' ):
    $args['cat'] = $category;
  elseif ($categoryortag && $categoryortag === 'tag'):
    $args['tag_id'] = $tag;
  endif;

  $arr_posts = new WP_Query( $args );

  // add a class if there are fewer than 6 posts
  $num = $arr_posts->post_count;
  $numclass = '';
  if ($num < 6 || $limit) {
    $numclass= 'ft-p-lownum';
  }
?>

<section class="ft-c-post-list">
  <div class="ft-l-container">
    <span class="ft-c-label"><?php _e('Featured Collection', 'foxtail'); ?></span>
    <h2 class="ft-c-post-list__title"><?php echo $title ?></h2>
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
