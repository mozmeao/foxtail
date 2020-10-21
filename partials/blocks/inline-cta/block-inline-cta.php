<?php
 // set up variables 
  $image = get_field( 'block-inlinecta-image' );
  $text = get_field( 'block-inlinecta-text' );
  $cta = get_field( 'block-inlinecta-cta' );
  $cta_url = $cta['url'];
  $cta_title = $cta['title'];
?>

<?php if ( !empty( $image) && !empty( $cta) && !empty( $text) ) { ?>
<a class="ft-c-inline-cta" href=" <?php echo esc_url( $cta_url ); ?>">
  <div class="ft-c-inline-cta__media">

    <?php echo wp_get_attachment_image( $image, '1x1' ); ?>
  </div>
  <div class="ft-c-inline-cta__content">
    <h6><?php echo $text ?></h6>
    <span><?php echo $cta_title ?></span>
  </div>
</a>
<?php } ?>