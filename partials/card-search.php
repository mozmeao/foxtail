<article class="ft-c-card">
  <a class="ft-c-card__link" href="<?php the_permalink(); ?>">
    <div class="ft-c-card__image">
      <?php
      if ( has_post_thumbnail() ) :
        the_post_thumbnail('1x1');
      endif;
      ?>
    </div>
    <div class="ft-c-card__content">
      <?php $post->post_parent; ?>
      <h2 class="ft-c-card__title"><?php the_title(); ?></h2>
      <span class="ft-c-card__cta">Read More</span>
    </div>
  </a>
</article>