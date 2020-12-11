<article class="ft-c-card">
  <a class="ft-c-card__link" href="<?php the_permalink(); ?>">
    <div class="ft-c-card__image">
      <?php
      if ( has_post_thumbnail() ) :
        the_post_thumbnail('1x1');
      else:
        get_template_part('partials/fallback-image');
      endif;
      ?>
    </div>
    <div class="ft-c-card__content">
      <h2 class="ft-c-card__title"><?php the_title(); ?></h2>
      <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?></span>
    </div>
  </a>
</article>
