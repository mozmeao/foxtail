<?php
/**
 * php This file is the template file for the homepage of foxtail
 */

get_header();
set_query_var('lang', 'en');
?>

<?php

// This is the large featured post section
get_template_part('partials/featured-post');
?>
<?php
get_footer();