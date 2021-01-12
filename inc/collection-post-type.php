<?php

/**
* Function for adding custom post type
*/

function create_collection_type() {
  $labels = array(
    'name'               => _x( 'Collections', 'post type general name' ),
    'singular_name'      => _x( 'Collection', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'book' ),
    'add_new_item'       => __( 'Add New Collection' ),
    'edit_item'          => __( 'Edit Collection' ),
    'new_item'           => __( 'New Collection' ),
    'all_items'          => __( 'All Collections' ),
    'view_item'          => __( 'View Collection' ),
    'search_items'       => __( 'Search Collections' ),
    'not_found'          => __( 'No collection found' ),
    'not_found_in_trash' => __( 'No collections found in the Trash' ),
    'menu_name'          => 'Collections'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Curated collections of posts, defined by a tag or category',
    'public'        => true,
    'rewrite' => array('slug' => 'collections'),
    'menu_position' => 5,
    'supports'      => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
    'has_archive'   => true,
  );
  register_post_type( 'collection', $args );
}
add_action( 'init', 'create_collection_type' );

?>
