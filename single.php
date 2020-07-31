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

<div class="ft-l-content">
  <main id="main" class="site-main ft-l-container">

    <?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'partials/content', get_post_type() );

			get_template_part( 'partials/single-post-share');
		?>	
  </main><!-- #main -->
  <?php

	
			if( get_field('display_mega_cta') == true ) {
    		get_template_part( 'partials/single-mega-cta' );
			}
			
			get_template_part('partials/single-navigation');

			get_template_part('partials/single-related-posts');
			
		endwhile; // End of the loop.
		?>

</div><!-- #primary -->

<?php
get_footer();