<?php
/**
 * php This file is the template file for the homepage of foxtail
 */

get_header();
set_query_var('lang', 'en');
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


<?php
get_footer();