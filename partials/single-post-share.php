<div class="ft-c-post-share">
  <hr class="ft-c-post-share__divider" />
  <div class="ft-c-post-share__icons">
    <a class="ft-js-share-link"
      href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>+<?php echo get_permalink(); ?>+<?php echo urlencode(' via @firefox')?>"
      target="_blank">
      <img class="ft-c-post-share__icon" src="<?php echo get_template_directory_uri() . '/assets/images/icons/twitter.svg' ?>" alt="<?php _e('Share on Twitter', 'foxtail'); ?>" />
    </a>
    <a data-pocket-label="pocket" data-pocket-count="none" class="pocket-btn" data-lang="en"></a>
    <script type="text/javascript">
    ! function(d, i) {
      if (!d.getElementById(i)) {
        var j = d.createElement("script");
        j.id = i;
        j.src = "https://widgets.getpocket.com/v1/j/btn.js?v=1";
        var w = d.getElementById(i);
        d.body.appendChild(j);
      }
    }(document, "pocket-btn-js");
    </script>
  </div>
</div>
