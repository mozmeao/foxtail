<?php
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
?>