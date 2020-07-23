<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Foxtail
 */

get_header();
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main ft-l-container">

    <?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'partials/content', get_post_type() );

			the_post_navigation();

		endwhile; // End of the loop.
		?>

  </main><!-- #main -->
  <?php
	if( get_field('display_mega_cta') == 'enable_sidebar' ) {
    get_template_part( 'partials/mega-cta' );
	}
	?>



</div><!-- #primary -->

<?php
get_footer();