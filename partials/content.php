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
			if (get_post_meta( get_the_ID(), 'hide_featured_image', true ) || !class_exists('ACF') || get_field('featured_at_top_of_post') == "Nothing") {
			echo '';
      } else if (get_field('featured_at_top_of_post') == "Video") {
        echo '<div class="ft-c-single-post__featured-video video-responsive">';
        // get the youtube ID from the URL and place it in the iframe youtube embed
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", get_field('post_featured_video'), $match);
        echo '<iframe id="player" width="560" height="315" frameborder="2" src="http://www.youtube.com/embed/' . $match[0] . '" ></iframe>';
        echo '</div>';
      } else {
      the_post_thumbnail('featured-image', array( 'class' => 'ft-c-single-post__featured-image' ));
      }
    ?>
    <div class="ft-c-single-post__category">
      <?php

        // Get current ID
        $category_current = foxtail_primary_cat($post);

        // Get the ID of a given category
        $category_id = get_cat_ID( $category_current );
 
        // Get the URL of this category
        $category_link = get_category_link( $category_id );

        echo '<a href="' . esc_url( $category_link ) . '" >';
      ?>
        <span class="ft-c-label ft-c-pill">
          <?php $post->post_parent; ?>
          <?php echo $category_current ?>
        </span>
      </a>
    </div>
    <?php the_title( '<h1 class="ft-c-single-post__title">', '</h1>' ); ?>

    <div class="ft-c-single-post__meta">

      <?php // show or hide date ?>
      <?php if (!get_post_meta( get_the_ID(), 'hide_date', true )) { ?>
      <div class="ft-c-post-meta">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/calendar.svg' ?>" alt="calendar" />
        <span><?php echo get_the_date(); ?></span>
      </div>
      <?php } ?>

      <?php // show or hide author ?>
      <?php if (!get_post_meta( get_the_ID(), 'hide_author', true )) { ?>
      <div class="ft-c-post-meta">
        <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/author.svg' ?>" alt="author" />
        <span>
        <?php
          if ( function_exists( 'coauthors_posts_links' ) ) {
            coauthors_posts_links(null, ' & ');
          } else {
            the_author_posts_link();
          }
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
        __('Continue reading<span class="screen-reader-text"> “%s”</span>', 'foxtail'),
        array(
          'span' => array(
            'class' => array(),
          ),
        )
      ),
      get_the_title()
    ) );

    wp_link_pages( array(
      'before' => '<div class="page-links">' . esc_html__('Pages:', 'foxtail'),
      'after'  => '</div>',
    ) );
    ?>
  </div>

</article><!-- #post-<?php the_ID(); ?> -->
