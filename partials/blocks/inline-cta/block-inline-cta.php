<?php
 // set up variables
  $image = get_field( 'block-inlinecta-image' );
  $text = get_field( 'block-inlinecta-text' );
  $cta = get_field( 'block-inlinecta-cta' );

  if (!empty($cta)) {
    $cta_url = $cta['url'];
    $cta_title = $cta['title'];
  }
?>

<a class="ft-c-inline-cta" href=" <?php if ( !empty($cta_url)) { echo esc_url( $cta_url ); } ?>">
  <div class="ft-c-inline-cta__media">
  <?php if ( !empty($image)) {
    echo wp_get_attachment_image( $image, '1x1' );
  } ?>
  </div>
  <div class="ft-c-inline-cta__content">
    <?php if ( !empty($text)) { ?> <h6><?php echo $text ?></h6> <?php } ?>
    <?php if ( !empty($cta_title)) { ?> <span><?php echo $cta_title ?></span> <?php } ?>
  </div>
</a>
