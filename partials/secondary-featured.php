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
if ($lang && $lang === 'en'):
  $secondary_featured_1 = get_field('secondary_featured_1_en', 'option');
  $secondary_featured_2 = get_field('secondary_featured_2_en', 'option');
  $secondary_featured_3 = get_field('secondary_featured_3_en', 'option');
elseif ($lang && $lang === 'de'):
  $secondary_featured_1 = get_field('secondary_featured_1_de', 'option');
  $secondary_featured_2 = get_field('secondary_featured_2_de', 'option');
  $secondary_featured_3 = get_field('secondary_featured_3_de', 'option');
endif;

// set up variables
if ($secondary_featured_1):
  $secondary_featured_1_image = get_the_post_thumbnail($secondary_featured_1, '1x1', array('class' => 'ft-c-featured-secondary__image--1'));
  $secondary_featured_1_link = get_permalink($secondary_featured_1->ID);
  $secondary_featured_1_title = esc_html($secondary_featured_1->post_title);
endif;
if ($secondary_featured_2):
  $secondary_featured_2_image = get_the_post_thumbnail($secondary_featured_2, '1x1', array('class' => 'ft-c-featured-secondary__image--2'));
  $secondary_featured_2_link = get_permalink($secondary_featured_2->ID);
  $secondary_featured_2_title = esc_html($secondary_featured_2->post_title);
endif;
if ($secondary_featured_3):
  $secondary_featured_3_image = get_the_post_thumbnail($secondary_featured_3, '1x1', array('class' => 'ft-c-featured-secondary__image--3'));
  $secondary_featured_3_link = get_permalink( $secondary_featured_3->ID );
  $secondary_featured_3_title = esc_html($secondary_featured_3->post_title);
endif;
?>

<section class="ft-c-featured-secondary">
  <div class="ft-c-featured-secondary__wrap ft-l-container">
    <div class="ft-c-featured-secondary__media">
      <?php echo $secondary_featured_1_image ?>
      <?php echo $secondary_featured_2_image ?>
      <?php echo $secondary_featured_3_image ?>
    </div>
    <div class="ft-c-featured-secondary__item-list">

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-first">
        <?php echo $secondary_featured_1_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label">Firefox</span>
          <a href="<?php echo $secondary_featured_1_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $secondary_featured_1_title ?></h2>
            <p class="ft-c-featured-secondary__readmore">Read More</p>
          </a>
        </div>
      </div>

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-second">
        <?php echo $secondary_featured_2_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label">Firefox</span>
          <a href="<?php echo $secondary_featured_2_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $secondary_featured_2_title ?></h2>
            <p class="ft-c-featured-secondary__readmore">Read More</p>
          </a>
        </div>
      </div>

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-third">
        <?php echo $secondary_featured_3_image ?>
        <div class="ft-c-featured-secondary__item">
          <span class="ft-c-label">Firefox</span>
          <a href="<?php echo $secondary_featured_3_link ?>">
            <h2 class="ft-c-featured-secondary__title"><?php echo $secondary_featured_3_title ?></h2>
            <p class="ft-c-featured-secondary__readmore">Read More</p>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>