<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

get_header();


?>
<div class="ft-l-content">
  <main class="ft-c-post-list">
    <div class="ft-l-container">

      <?php if ( have_posts() ) : ?>

      <?php

				echo '<div class="ft-c-archive-header">';
				echo get_avatar( get_the_author_meta('ID'), 100);
				echo '<div>';
				echo '<h2>' .  get_the_author() . '</h2>';
				the_archive_description( '<div class="archive-description">', '</div>' );
				echo '</div>';


			?>
    </div><!-- .ft-c-archive-header -->
    <div class="ft-l-space-related"></div>
    <div class="ft-c-post-list__wrap--three-column">

      <?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				get_template_part('partials/card-excerpt');

			endwhile;


		else :

			get_template_part( 'partials/content', 'none' );

		endif;
		?>

    </div>
    <?php foxtail_pagination(); ?>
</div>
</main>
</div>

<?php
get_footer();
