<section class="mzp-c-card ft-c-card--excerpt mzp-has-aspect-1-1">
  <a class="mzp-c-card-block-link" href="<?php the_permalink(); ?>">
    <div class="mzp-c-card-content">
      <div class="mzp-c-card-content">
        <?php $post->post_parent; ?>
        <?php if (!is_search()) { ?>
        <div class="ft-c-pill__wrap">
          <div class="ft-c-pill"><?php echo foxtail_primary_cat($post) ?></div>
        </div>
        <?php } ?>
        <h2 class="mzp-c-card-title"><?php the_title(); ?></h2>
        <p class="mzp-c-card-desc"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' );?></p>
        <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?></span>
      </div>
    </div>
  </a>
</section>
