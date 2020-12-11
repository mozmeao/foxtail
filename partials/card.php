<section class="mzp-c-card mzp-has-aspect-1-1">
  <a class="mzp-c-card-block-link" href="<?php the_permalink(); ?>">
    <div class="mzp-c-card-media-wrapper">
      <?php
      if ( has_post_thumbnail() ) {
        the_post_thumbnail('1x1', array('class' => 'mzp-c-card-image'));
      }
      else {
      get_template_part('partials/fallback-image'); 
      }
      ?>
    </div>
    <div class="mzp-c-card-content">
      <div class="mzp-c-card-content">
        <?php $post->post_parent; ?>
        <?php if (!is_search()) { ?>
        <div class="ft-c-pill__wrap">
          <div class="ft-c-pill"><?php echo foxtail_get_cat($post) ?></div>
        </div>
        <?php } ?>
        <h2 class="mzp-c-card-title"><?php the_title(); ?></h2>
        <span class="ft-c-more">Read More</span>

      </div>
    </div>
  </a>
</section>