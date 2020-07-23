<?php
if ( get_field('select_mega_cta_type') === 'Custom' ) {
  $megatitle = get_field('mega_cta_title_custom');
  $megalabel = get_field('mega_cta_label_custom');
  $megalink = get_field('mega_cta_link_custom');
  $megaimage = get_field('mega_cta_image_custom');
}
?>
<div class="ft-c-mega-cta">
  <div class="ft-l-container">
    <div class="ft-c-mega-cta__wrap">
      <div class="ft-c-mega-cta__content">
        <span class="ft-c-label ft-c-label--white ft-c-single-post__category">
          <?php if ($megalabel) echo $megalabel  ?>
        </span>
        <h3><?php echo $megatitle ?></h3>
        <a href="<?php echo esc_url($megalink['url']) ?>" class="ft-button-secondary--dark"
          target="<?php echo esc_attr( $megalink['target'] ? $link['target'] : '_self' ) ?>">
          <?php echo esc_html($megalink['title']) ?>
        </a>
      </div>
      <div class=" ft-c-mega-cta__image">
        <?php echo wp_get_attachment_image( $megaimage, '3x2' ); ?>
      </div>
    </div>
  </div>
</div>