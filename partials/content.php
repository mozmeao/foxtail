<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ft-c-single-post'); ?>>
  <div class="ft-c-single-post__header">
    <?php
			
			// show or hide featured image
			if (!get_post_meta( get_the_ID(), 'show_featured_image', true )) {
			echo '';
			} else {
			the_post_thumbnail('featured-image', array( 'class' => 'ft-c-single-post__featured-image' ));
			}
		?>
    <div class="ft-c-single-post__category">
      <span class="ft-c-label ft-c-pill">
        <?php $post->post_parent; ?>
        <?php echo foxtail_get_cat($post) ?>
      </span>
    </div>
    <?php the_title( '<h1 class="ft-c-single-post__title">', '</h1>' ); ?>
    <div class="ft-c-single-post__meta">
      <div class="ft-c-post-meta">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/calendar.svg' ?>" alt="calendar" />
        <span><?php echo get_the_date(); ?></span>
      </div>

      <?php // show or hide author ?>
      <?php if (get_post_meta( get_the_ID(), 'show_author', true )) { ?>
      <div class="ft-c-post-meta">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/author.svg' ?>" alt="author" />
        <span>
          <?php
					echo sprintf(
						'<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>');
					?>
        </span>
      </div>
      <?php } ?>

    </div>
  </div>

  <div class="ft-c-single-post__body">


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
  </div>

</article><!-- #post-<?php the_ID(); ?> -->