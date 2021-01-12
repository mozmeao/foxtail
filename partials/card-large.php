<article class="ft-c-card ft-c-card--large">
  <a class="ft-c-card__link" href="<?php the_permalink(); ?>">
    <div class="ft-c-card__image">
      <?php
      if ( has_post_thumbnail() ) :
        the_post_thumbnail('16x9');
      endif;
      ?>
    </div>
    <div class="ft-c-card__content">
      <!-- <span class="ft-c-label">Mozilla Comunity</span> -->
      <h2 class="ft-c-card__title"><?php the_title(); ?></h2>
      <?php
        $item_excerpt = wp_trim_words( get_the_excerpt(), 55, ' ...' );
        echo '<p>' . $item_excerpt . '</p>'
      ?>
      <span class="ft-c-more"><?php _e('View Collection', 'foxtail'); ?></span>
    </div>
  </a>
</article>
