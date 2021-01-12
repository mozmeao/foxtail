<!-- First, some JS that handles the mouseover hover -->
<script type="text/javascript" async>
function secondaryFeatured() {
  document.querySelector(".ft-c-featured-secondary__image--1").classList.add("is-active");
  document.getElementById("ft-js-first").addEventListener("mouseover", function() {
    document.querySelector(".ft-c-featured-secondary__image--1").classList.add("is-active");
    document.querySelector(".ft-c-featured-secondary__image--2").classList.remove("is-active");
    document.querySelector(".ft-c-featured-secondary__image--3").classList.remove("is-active");
  })
  document.getElementById("ft-js-second").addEventListener("mouseover", function() {
    document.querySelector(".ft-c-featured-secondary__image--1").classList.remove("is-active");
    document.querySelector(".ft-c-featured-secondary__image--2").classList.add("is-active");
    document.querySelector(".ft-c-featured-secondary__image--3").classList.remove("is-active");
  })
  document.getElementById("ft-js-third").addEventListener("mouseover", function() {
    document.querySelector(".ft-c-featured-secondary__image--1").classList.remove("is-active");
    document.querySelector(".ft-c-featured-secondary__image--2").classList.remove("is-active");
    document.querySelector(".ft-c-featured-secondary__image--3").classList.add("is-active");
  })
}
window.addEventListener("load", secondaryFeatured, false);
</script>

<?php

// Get the language for the page this is being used on
$lang = get_query_var('lang');

// set the post object according to what langauge is set
if ($lang):
  $featured_secondary_1 = get_field('featured_secondary_1_' . $lang, 'option');
  $featured_secondary_2 = get_field('featured_secondary_2_' . $lang, 'option');
  $featured_secondary_3 = get_field('featured_secondary_3_' . $lang, 'option');

  // Add these posts to this array so that they aren't duplicated on front page
  global $_displayed_posts;
  $_displayed_posts[] = $featured_secondary_1->ID;
  $_displayed_posts[] = $featured_secondary_2->ID;
  $_displayed_posts[] = $featured_secondary_3->ID;
endif;

// set up variables
if ($featured_secondary_1):
  $featured_secondary_1_image = get_the_post_thumbnail($featured_secondary_1, '1x1', array('class' => 'ft-c-featured-secondary__image--1'));
  $featured_secondary_1_link = get_permalink($featured_secondary_1->ID);
  $featured_secondary_1_title = esc_html($featured_secondary_1->post_title);
endif;
if ($featured_secondary_2):
  $featured_secondary_2_image = get_the_post_thumbnail($featured_secondary_2, '1x1', array('class' => 'ft-c-featured-secondary__image--2'));
  $featured_secondary_2_link = get_permalink($featured_secondary_2->ID);
  $featured_secondary_2_title = esc_html($featured_secondary_2->post_title);
endif;
if ($featured_secondary_3):
  $featured_secondary_3_image = get_the_post_thumbnail($featured_secondary_3, '1x1', array('class' => 'ft-c-featured-secondary__image--3'));
  $featured_secondary_3_link = get_permalink( $featured_secondary_3->ID );
  $featured_secondary_3_title = esc_html($featured_secondary_3->post_title);
endif;
?>

<section class="ft-c-featured-secondary">
  <div class="ft-c-featured-secondary__wrap ft-l-container">
    <div class="ft-c-featured-secondary__media">
      <?php echo $featured_secondary_1_image ?>
      <?php echo $featured_secondary_2_image ?>
      <?php echo $featured_secondary_3_image ?>
    </div>
    <div class="ft-c-featured-secondary__item-list">

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-first">
        <?php echo $featured_secondary_1_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label"><?php echo foxtail_get_cat($featured_secondary_1) ?></span>
          <a href="<?php echo $featured_secondary_1_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $featured_secondary_1_title ?></h2>
            <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?></span>
          </a>
        </div>
      </div>

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-second">
        <?php echo $featured_secondary_2_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label"><?php echo foxtail_get_cat($featured_secondary_2) ?></span>
          <a href="<?php echo $featured_secondary_2_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $featured_secondary_2_title ?></h2>
            <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?></span>
          </a>
        </div>
      </div>

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-third">
        <?php echo $featured_secondary_3_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label"><?php echo foxtail_get_cat($featured_secondary_3) ?></span>
          <a href="<?php echo $featured_secondary_3_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $featured_secondary_3_title ?></h2>
            <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?></span>
          </a>
        </div>
      </div>

    </div>
  </div>
  <div class="ft-c-post-list__cta">
    <?php $url = site_url( '/latest/') ?>
    <a href="<?php echo $url ?>" class="mzp-c-cta-link">
      <?php _e('View All Posts', 'foxtail'); ?>
    </a>
  </div>
</section>
