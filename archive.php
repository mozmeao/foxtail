<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foxtail
 */

get_header();


$term = get_queried_object();
$image_display = get_field('tax_featured_image_display', $term);

?>
<div class="ft-l-content">
  <main class="ft-c-post-list">
    <div class="ft-l-container">

      <?php if ( have_posts() ) : ?>

      <?php
      if ($image_display == true) {
        echo '<div class="ft-c-archive-header ftc-archive-header--has-image">';
        $image = get_field('tax_featured_image', $term);
        echo wp_get_attachment_image($image, '16x9');
        echo '<div class="ft-u-center-text">';
        echo '<h2>';
        echo single_term_title();
        echo '</h2>';
        the_archive_description( '<div class="archive-description">', '</div>' );
        echo '</div>';
      }

      else {
        echo '<div class="ft-c-archive-header">';
        the_archive_title( '<h2>', '</h2>' );
        the_archive_description( '<div class="archive-description">', '</div>' );
      }
      ?>
    </div><!-- .ft-c-archive-header -->
    <div class="ft-l-space-related"></div>
    <div class="ft-c-post-list__wrap--three-column">

      <?php
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
</div>

<?php
get_footer();
