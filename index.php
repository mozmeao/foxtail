<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

get_header();
?>
<div class="ft-l-space-related"></div>
<main class="ft-c-post-list">
  <div class="ft-l-container">

    <?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) :
				?>
    <header>
      <h2>Latest Posts</h2>
      <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
      <?php if ($paged && $paged > 1) { ?>

      <span>page: <?php echo $paged ?></span>
      <?php } ?>
    </header>
    <div class="ft-l-space-related"></div>
    <div class="ft-c-post-list__wrap--three-column">
      <?php
			endif;

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				get_template_part('partials/card'); 

			endwhile;

		else :

			get_template_part( 'partials/content', 'none' );

		endif;
		?>

    </div>
    <?php foxtail_pagination(); ?>
  </div>
</main>
<div class="ft-l-space-related"></div>

<?php
get_footer();