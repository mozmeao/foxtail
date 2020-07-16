<?php

// Get the language for the page this is being used on
$lang = get_query_var('lang');

// set the post object according to what langauge is set
if ($lang):
  $featured_extra_headline = get_field('featured_extra_headline_' . $lang, 'option');
  $featured_extra_subhead = get_field('featured_extra_subhead_' . $lang, 'option');
  $featured_extra_image = get_field('featured_extra_image_' . $lang, 'option');
  $featured_extra_cta_1 = get_field('featured_extra_cta_1_' . $lang, 'option');
  $featured_extra_link_1 = get_field('featured_extra_link_1_' . $lang, 'option');
  $featured_extra_cta_2 = get_field('featured_extra_cta_2_' . $lang, 'option');
  $featured_extra_link_2 = get_field('featured_extra_link_2_' . $lang, 'option');
  $featured_extra_label = get_field('featured_extra_label_' . $lang, 'option');
endif

?>
<section class="ft-c-featured-extra">
  <div class="ft-c-featured-extra__wrap ft-l-container">
    <div class="ft-c-featured-extra__image">
      <?php if ($featured_extra_image) {
          echo wp_get_attachment_image($featured_extra_image, '1x1');
        };
        ?>
    </div>
    <div class="ft-c-featured-extra__content">
      <span class="ft-c-label ft-c-label--dark"><?php echo $featured_extra_label ?></span>
      <h3><?php echo $featured_extra_headline ?></h3>
      <?php echo $featured_extra_subhead ?>
      <div class="ft-c-featured-extra__ctas">
        <a class="ft-button--dark" href="<?php echo $featured_extra_link_1['url'] ?>" target="
          <?php $featured_extra_link_1['target'] ? $featured_extra_link_1['target'] : '_self';?>">
          <?php echo $featured_extra_cta_1 ?>
        </a>
        <?php if ($featured_extra_link_2 && $featured_extra_cta_2) { ?>
        <a class="ft-button-secondary--dark" href="<?php echo $featured_extra_link_2['url'] ?>" target="
          <?php $featured_extra_link_2['target'] ? $featured_extra_link_2['target'] : '_self';?>">
          <?php echo $featured_extra_cta_2 ?>
        </a>
        <?php } ?>

      </div>
    </div>
  </div>
</section>