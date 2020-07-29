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

			get_template_part( 'partials/content-collection', get_post_type() );

			// BEGIN POST LOOP
			echo '<div class="ft-c-post-list__wrap--three-column">';

			$args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
			);

			$categoryortag = get_field(' collection_filter', 'option');
			if ( $categoryortag === 'category' ):
				$args['cat'] = get_field('collection_filter', 'option');
			elseif ($categoryortag && $categoryortag === 'tag'):
				$args['tag_id'] = get_field('collection_tag', 'option');
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