<section class="ft-c-featured-hero">
  <?php

  // get the lang variable, which will help us know which post object to grab
  $lang = get_query_var('lang');

  if ($lang && $lang === 'en'):
    $featured_post = get_field('post_to_feature_en', 'option');
  elseif ($lang && $lang === 'de'):
    $featured_post = get_field('post_to_feature_de', 'option');
  endif;

  // set up variables
  if( $featured_post ):
    $featured_permalink = get_permalink( $featured_post->ID );
    $featured_image = get_the_post_thumbnail($featured_post, '3x2');
    $featured_title = esc_html( $featured_post->post_title);
    $featured_excerpt = wp_trim_words( get_the_excerpt($featured_post), 55, ' ...' );
  ?>

  <div class="ft-l-container ft-c-featured-hero__wrap">
    <div class="ft-c-featured-hero__image">
      <?php echo $featured_image ?>
    </div>
    <div class="ft-c-featured-hero__body">
      <span class="ft-c-label ft-c-label--dark">Featured Article</span>
      <h1 class="ft-c-featured-hero__title">
        <?php echo esc_html( $featured_post->post_title ); ?>
      </h1>

      <div class="ft-c-featured-hero__desc">
        <p>
          <?php echo $featured_excerpt ?>
        </p>
      </div>

      <p class="ft-c-featured-hero__cta">
        <a class="ft-button--dark" href="<?php echo $featured_permalink ?>">
          Read more
        </a>
      </p>
    </div>
  </div>
  <?php endif; ?>
</section>