<?php
/**
 * php This file is the template file for the homepage of foxtail
 */

get_header();
set_query_var('lang', 'en');

global $_displayed_posts_en;
$_displayed_posts_en = array();

?>

<!-- This is the large featured post section -->
<?php get_template_part('partials/featured-post'); ?>

<div class="ft-l-space-unrelated"></div>

<!-- The 2nd featured section which has 3 items -->
<?php get_template_part('partials/secondary-featured'); ?>

<div class="ft-l-space-unrelated"></div>

<!-- The Featured Video section -->
<?php get_template_part('partials/featured-video'); ?>

<div class="ft-l-space-unrelated"></div>

<!-- Collection #1 -->

<?php get_template_part('partials/collection/collection-one'); ?>

<div class="ft-l-space-unrelated"></div>

<!-- Collection #2 -->

<?php get_template_part('partials/collection/collection-two'); ?>

<div class="ft-l-space-unrelated"></div>

<!-- Collection #3 -->

<?php get_template_part('partials/collection/collection-three'); ?>

<div class="ft-l-space-unrelated"></div>




<?php
get_footer();