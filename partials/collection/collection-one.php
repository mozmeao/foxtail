<section class="ft-c-post-list">
  <div class="ft-l-container">
    <span class="ft-c-label">Featured collection</span>
    <h2 class="ft-c-post-list__title"><?php the_field('feature_collection_1_title_en', 'option') ?></h2>
    <div class="ft-c-post-list__wrap--two-column">



      <?php
      global $_displayed_posts;
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'post__not_in' => $_displayed_posts,
);

// Select posts based on either a category or a tag
$categoryortag = get_field('feature_category_or_tag_1_en', 'option');
if ( $categoryortag === 'category' ):
  $args['cat'] = get_field('featured_collection_1_category_en', 'option');
elseif ($categoryortag && $categoryortag === 'tag'):
  $args['tag_id'] = get_field('featured_collection_1_tag_en', 'option');
endif;



$arr_posts = new WP_Query( $args );
 
if ( $arr_posts->have_posts() ) :
  while ( $arr_posts->have_posts() ) :
    $arr_posts->the_post();
    ?>
      <?php get_template_part('partials/card', 'small'); ?>

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