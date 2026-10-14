<section class="ft-c-featured-hero">
  <?php

  // get the lang variable, which will help us know which options to read
  $lang = get_query_var('lang');
  $featured_item = null;

  if ($lang):
    // a WordPress post or an External item, see inc/featured-items.php
    $group = get_field($lang . '_top_featured_post', 'option');
    $featured_item = foxtail_featured_item($group, 'post', 'source', 'external', array('raw_title' => true, 'excerpt' => true, 'label' => false));

    // add to array that tracks displayed posts on homepage (posts only)
    foxtail_mark_displayed($featured_item);
  endif;


  // set up variables
  if( $featured_item ):
    $featured_permalink = esc_url( $featured_item['url'] );
    $featured_image = foxtail_item_image($featured_item, '3x2', array(), 'img');
    $featured_excerpt = $featured_item['excerpt'];
  ?>

  <div class="ft-l-container ft-c-featured-hero__wrap">
    <div class="ft-c-featured-hero__image">
      <?php echo $featured_image ?>
    </div>
    <div class="ft-c-featured-hero__body">
      <span class="ft-c-label ft-c-label--dark"><?php _e('Featured Article', 'foxtail'); ?></span>
      <h1 class="ft-c-featured-hero__title">
        <?php echo $featured_item['title']; ?>
      </h1>

<?php if ( $featured_excerpt !== '' ) : ?>
      <div class="ft-c-featured-hero__desc">
        <p>
          <?php echo $featured_excerpt ?>
        </p>
      </div>
<?php endif; ?>

      <p class="ft-c-featured-hero__cta">
        <a class="mzp-c-button mzp-t-dark mzp-t-xl" href="<?php echo $featured_permalink ?>"<?php echo foxtail_item_rel($featured_item) ?>>
          <?php _e('Read More', 'foxtail'); ?><?php echo foxtail_external_marker($featured_item['external_host']) ?>
        </a>
      </p>
    </div>
  </div>
  <?php endif; ?>
</section>
