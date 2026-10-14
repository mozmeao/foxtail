<?php
// The current post, or an External item passed as $args['item'] (see inc/featured-items.php)
$item = isset($args['item']) ? $args['item'] : foxtail_post_item(get_post(), array('label' => !is_search()));
?>
<section class="mzp-c-card mzp-has-aspect-1-1">
  <a class="mzp-c-card-block-link" href="<?php echo esc_url($item['url']); ?>"<?php echo foxtail_item_rel($item) ?>>
    <div class="mzp-c-card-media-wrapper">
    <?php
      echo foxtail_item_image($item, '1x1', array('class' => 'mzp-c-card-image'));
    ?>
    </div>
    <div class="mzp-c-card-content">
      <div class="mzp-c-card-content">
        <?php // search results show no category pill ?>
        <?php if ($item['label'] !== '') { ?>
        <div class="ft-c-pill__wrap">
          <div class="ft-c-pill"><?php echo $item['label'] ?></div>
        </div>
        <?php } ?>
        <h2 class="mzp-c-card-title"><?php echo $item['title']; ?></h2>
        <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?><?php echo foxtail_external_marker($item['external_host']) ?></span>
      </div>
    </div>
  </a>
</section>
