<div class="ft-c-post-share">
  <hr class="ft-c-post-share__divider" />
  <div class="ft-c-post-share__icons">
    <a class="ft-js-share-link"
      href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>+<?php echo get_permalink(); ?>+<?php echo urlencode(' via @firefox')?>"
      target="_blank">
      <img class="ft-c-post-share__icon" src="<?php echo get_template_directory_uri() . '/assets/images/icons/twitter.svg' ?>" alt="<?php _e('Share on Twitter', 'foxtail'); ?>" />
    </a>
  </div>
</div>
