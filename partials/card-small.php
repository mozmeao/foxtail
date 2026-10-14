<?php
// The current post, or an External item passed as $args['item'] (see inc/featured-items.php)
$item = isset($args['item']) ? $args['item'] : foxtail_post_item(get_post(), array('label' => false));
?>
<article class="ft-c-card">
  <a class="ft-c-card__link" href="<?php echo esc_url($item['url']); ?>"<?php echo foxtail_item_rel($item) ?>>
    <div class="ft-c-card__image">
      <?php
      echo foxtail_item_image($item, '1x1');
      ?>
    </div>
    <div class="ft-c-card__content">
      <h2 class="ft-c-card__title"><?php echo $item['title']; ?></h2>
      <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?><?php echo foxtail_external_marker($item['external_host']) ?></span>
    </div>
  </a>
</article>
