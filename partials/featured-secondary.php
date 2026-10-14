<!-- First, some JS that handles the mouseover hover -->
<script type="text/javascript" async>
function secondaryFeatured() {
  var images = [1, 2, 3].map(function(n) {
    return document.querySelector(".ft-c-featured-secondary__image--" + n);
  });
  function activate(n) {
    images.forEach(function(image, i) {
      if (image) {
        image.classList.toggle("is-active", i === n - 1);
      }
    });
  }
  activate(1);
  ["first", "second", "third"].forEach(function(id, i) {
    var item = document.getElementById("ft-js-" + id);
    if (item) {
      item.addEventListener("mouseover", function() {
        activate(i + 1);
      });
    }
  });
}
window.addEventListener("load", secondaryFeatured, false);
</script>

<?php

// Get the language for the page this is being used on
$lang = get_query_var('lang');

// Each slot is a WordPress post or an External item, see inc/featured-items.php
$slots = array(1 => 'first', 2 => 'second', 3 => 'third');
$items = array();
$images = array(1 => '', 2 => '', 3 => '');

if ($lang):
  $group = get_field($lang . '_secondary_featured_group', 'option');

  foreach ($slots as $n => $slot) {
    $items[$n] = foxtail_featured_item($group, $slot, $slot . '_source', $slot . '_external', array('raw_title' => true));

    // Add posts to this array so that they aren't duplicated on front page
    foxtail_mark_displayed($items[$n]);

    if ($items[$n]) {
      $image_class = 'ft-c-featured-secondary__image--' . $n;
      $images[$n] = foxtail_item_image($items[$n], '1x1', array('class' => $image_class), 'img');
    }
  }
endif;
?>

<section class="ft-c-featured-secondary">
  <div class="ft-c-featured-secondary__wrap ft-l-container">
    <div class="ft-c-featured-secondary__media">
      <?php echo $images[1] ?>
      <?php echo $images[2] ?>
      <?php echo $images[3] ?>
    </div>
    <div class="ft-c-featured-secondary__item-list">
<?php foreach ($slots as $n => $slot) : if (empty($items[$n])) continue; $item = $items[$n]; ?>

      <div class="ft-c-featured-secondary__item-wrap" id="ft-js-<?php echo $slot ?>">
        <?php echo $images[$n] ?>
        <div class="ft-c-featured-secondary__item">
<?php if ($item['label'] !== '') : ?>
          <span class="ft-c-label"><?php echo $item['label'] ?></span>
<?php endif; ?>
          <a href="<?php echo esc_url($item['url']) ?>"<?php echo foxtail_item_rel($item) ?>>
            <h2 class="ft-c-featured-secondary__title"><?php echo $item['title'] ?></h2>
            <span class="ft-c-more"><?php _e('Read More', 'foxtail'); ?><?php echo foxtail_external_marker($item['external_host']) ?></span>
          </a>
        </div>
      </div>
<?php endforeach; ?>

    </div>
  </div>
  <div class="ft-c-post-list__cta">
    <?php $url = site_url( '/latest/') ?>
    <a href="<?php echo $url ?>" class="mzp-c-cta-link">
      <?php _e('View All Posts', 'foxtail'); ?>
    </a>
  </div>
</section>
