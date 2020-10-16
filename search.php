<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Foxtail
 */

get_header();
?>

<div class="ft-l-content">
  <main class="ft-c-post-list">
    <div class="ft-l-container">

      <?php if ( have_posts() ) : ?>

      <header>
        <h2>
          <?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'foxtail' ), '<span>' . get_search_query() . '</span>' );
					?>
        </h2>
      </header><!-- .page-header -->

      <div class="ft-l-space-related"></div>
      <div class="ft-c-post-list__wrap--three-column">

        <?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				
				get_template_part('partials/card-search'); 

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