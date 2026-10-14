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

  // "External (curated)": up to 3 hand-entered items, no query (see inc/featured-items.php)
  $curated = $categoryortag === 'external';

  if ( $curated ):
    $items = foxtail_collection_items($group);
    $num = count($items);
  else:
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
    $num = $arr_posts->post_count;
  endif;

  // add a class if there are fewer than 6 posts
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
      if ( $curated ) :
        foreach ( $items as $item ) :
          get_template_part('partials/card', null, array('item' => $item));
        endforeach;
      elseif ( $arr_posts->have_posts() ) :
        while ( $arr_posts->have_posts() ) :
          $arr_posts->the_post();
          get_template_part('partials/card');
        endwhile;
      endif;
    ?>
    </div>
<?php if ( ! empty( $collection_link['url'] ) ) : $link_host = foxtail_external_host( $collection_link['url'] ); ?>
    <div class="ft-c-post-list__cta">
      <a href="<?php echo esc_url($collection_link['url']) ?>" class="mzp-c-cta-link"<?php if ( $link_host ) echo ' rel="external"' ?>>
        <?php echo $collection_link['title'] ?><?php echo foxtail_external_marker( $link_host ) ?>
      </a>
    </div>
<?php endif; ?>
  </div>
</section>
