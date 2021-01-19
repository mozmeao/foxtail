<?php

// return first category
function foxtail_get_cat($the_page) {
  $category = get_the_category($the_page);
  $category_parent_id = $category[0]->category_parent;
  if ( $category_parent_id != 0 ) {
    $category_parent = get_term( $category_parent_id, 'category' );
    $cat_name = $category_parent->name;
  } else {
    $cat_name = $category[0]->name;
  }
  return $cat_name;
}

/*
* Print the current page URL.
*/
function foxtail_current_url() {
  global $wp;
  $current_url = esc_url(home_url(add_query_arg(array(),$wp->request)));

  echo $current_url;
}



// This will return the primary category which is set by Yoast.
// if Yoast isn't available, it will return the top category for the post.
// $categories should be an array of categories
function foxtail_primary_cat($post_id) {
  $categories = get_the_category($post_id);
  if ( class_exists('WPSEO_Primary_Term') ) {

        // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
    $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
    $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
    $term = get_term( $wpseo_primary_term );

      if ( is_wp_error( $term ) ) {

        // Default to first category (not Yoast) if an error is returned
        $category_display = $category[0]->name;
        $category_slug = $category[0]->slug;

      } else {

        // Check if category has parent
        $category_id = $term->term_id;
        $category_term = get_category($category_id);

        // if primary category is a child
        if( $category_term->category_parent > 0 ) {

          // Get parent category object
          $parent = $category_term->parent;
          $parent_object = get_category($parent);

          // Set parent category variables
          $category_display = $parent_object->name;
          $category_slug = $parent_object->slug;

          // if primary cateogry is a parent
          } else {

            // Yoast Primary category
            $category_display = $term->name;
            $category_slug = $term->slug;
        }
      }
  } else {
    // Default, display the first category in WP's list of assigned categories
    $category_display = $categories[0]->name;
    $category_slug = $categories[0]->slug;
  }
  return $category_display;
}
?>
