<?php 
$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
$args = array(
  'post_type'         => 'post',
  'posts_per_page'    => 6,
  'paged'             => $paged,
);
$wp_query = new WP_Query( $args );
?>
<div class="ft-l-content">
  <main class="ft-c-post-list">
    <div class="ft-l-container">

      <?php
		if ( $wp_query->have_posts() ) :

				?>
      <header>
        <h2>Latest Posts</h2>
        <?php if ($paged && $paged > 1) { ?>

        <span>page: <?php echo $paged ?></span>
        <?php } ?>
      </header>
      <div class="ft-c-post-list__wrap--three-column">
        <?php

			/* Start the Loop */
			while ( $wp_query->have_posts() ):
        $wp_query->the_post();

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
