<section class="ft-c-post-list">
  <div class="ft-l-container">
    <span class="ft-c-label">Featured collection</span>
    <h2 class="ft-c-post-list__title">Be smart. Shop Safe.</h2>
    <div class="ft-c-post-list__wrap--three-column">



      <?php
      global $_displayed_posts;
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'post__not_in' => $_displayed_posts,
);

// Select posts based on either a category or a tag
$categoryortag = get_field('feature_category_or_tag_2_en', 'option');
if ( $categoryortag === 'category' ):
  $args['cat'] = get_field('featured_collection_2_category_en', 'option');
elseif ($categoryortag && $categoryortag === 'tag'):
  $args['tag_id'] = get_field('featured_collection_2_tag_en', 'option');
endif;



$arr_posts = new WP_Query( $args );
 
if ( $arr_posts->have_posts() ) :
  while ( $arr_posts->have_posts() ) :
    $arr_posts->the_post();
    ?>
      <?php get_template_part('partials/card'); ?>

      <?php
    endwhile;
endif;
?>
    </div>
    <div class="ft-c-post-list__cta">
      <a href="#" class="ft-button-secondary">
        View Collection
      </a>
    </div>
  </div>
</section>