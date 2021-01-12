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

			get_template_part( 'partials/content-collection', get_post_type() );

			// BEGIN POST LOOP
			echo '<div class="ft-c-post-list__wrap--three-column">';

			$args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
			);

			
			if (class_exists('ACF')) {
				$categoryortag = get_field('collection_filter');
			}
			if ( $categoryortag === 'Category' ):
				$args['cat'] = get_field('collection_filter');
			elseif ($categoryortag && $categoryortag === 'Tag'):
				$args['tag_id'] = get_field('collection_tag');
			endif;

			$arr_posts = new WP_Query( $args );

			if ( $arr_posts->have_posts() ) :
				while ( $arr_posts->have_posts() ) :
					$arr_posts->the_post();	
	      	get_template_part('partials/card');
				endwhile;
			endif;
			echo '</div>';
			// END POST LOOP
		?>
  </main><!-- #main -->
  <?php			
		endwhile; // End of the loop.
	?>

</div><!-- #primary -->

<?php
get_footer();