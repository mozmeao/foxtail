<?php
			$next_post = get_next_post();
			$prev_post = get_previous_post();
			?>
<div class="ft-c-post-nav">
  <div class="ft-l-container ft-c-post__wrap">
    <?php if ( ! empty( $prev_post ) ): ?>
    <a id="previous-post" href="<?php echo get_permalink( $prev_post->ID ); ?>"
      class="ft-c-post-nav__item ft-c-post-nav__item--left">
      <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/previous.svg' ?>" alt="previous" />
      <div class="ft-c-post-nav__item-content">
        <span>Previous Post</span>
        <p>
          <?php echo apply_filters( 'the_title', $prev_post->post_title ); ?>
        </p>
      </div>
    </a>
    <?php else: ?>
    <div class="ft-c-post-nav__item"></div>
    <?php endif ?>
    <?php if ( ! empty( $next_post ) ): ?>
    <a id="next-post" href="<?php echo get_permalink( $next_post->ID ); ?>" class="ft-c-post-nav__item">
      <div class="ft-c-post-nav__item-content">
        <span>Next Post</span>
        <p>
          <?php echo get_the_title( $next_post->ID ); ?>
        </p>
      </div>
      <img src="<?php echo get_template_directory_uri() . '/assets/images/icons/next.svg' ?>" alt="next" />
    </a>
    <?php else: ?>
    <div class="ft-c-post-nav__item"></div>
    <?php endif ?>
  </div>
</div>