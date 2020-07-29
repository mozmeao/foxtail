<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ft-c-collection-content'); ?>>
  <div class="ft-c-collection-content__header">
    <?php 
			if ( has_post_thumbnail() ) {
    		the_post_thumbnail('featured-image', array( 'class' => 'ft-c-collection-content__featured-image' ));
			}
		?>
    <?php the_title( '<h2 class="ft-c-collection-content__title">', '</h2>' ); ?>
  </div>

  <div class="ft-c-collection-content__body">

    <?php
		the_content( sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'foxtail' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'foxtail' ),
			'after'  => '</div>',
		) );
		?>
    <hr />
  </div>

</article><!-- #post-<?php the_ID(); ?> -->