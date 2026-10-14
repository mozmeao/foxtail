<?php
/**
 * Homepage items that come from either a WordPress post or an External item.
 *
 * External items are entered in the Front Page options pages. Their fields
 * (url, title, label, image, excerpt) live in the "External item (template)"
 * ACF field group and are cloned into the hero, the secondary slots and the
 * curated collections.
 *
 * An item is an array:
 *   source        'post' or 'external'
 *   url           link target (escape with esc_url() when printing)
 *   title         ready to echo
 *   label         ready to echo, may be ''
 *   excerpt       ready to echo, may be '' (only filled when asked for)
 *   image_id      attachment ID, 0 for none
 *   post_id       post ID, 0 for external items
 *   external_host the linked site's host (e.g. firefox.com) when the URL leaves this site, else false
 */

// Return the host of a link that leaves this site, or false for internal links.
// Relative URLs and links to the blog's own host (with or without www.) are internal.
function foxtail_external_host( $url ) {
  $host = wp_parse_url( $url, PHP_URL_HOST );

  if ( empty( $host ) ) {
    return false;
  }

  $host = preg_replace( '/^www\./', '', strtolower( $host ) );
  $own_host = preg_replace( '/^www\./', '', strtolower( wp_parse_url( home_url(), PHP_URL_HOST ) ) );

  return $host === $own_host ? false : $host;
}

// Is this an absolute http:// or https:// URL?
function foxtail_is_http_url( $url ) {
  $scheme = wp_parse_url( (string) $url, PHP_URL_SCHEME );

  return in_array( strtolower( (string) $scheme ), array( 'http', 'https' ), true ) && wp_parse_url( $url, PHP_URL_HOST );
}

/**
 * Build an item from a post.
 *
 * $options:
 *   raw_title  print the unfiltered post title, as the hero and secondary slots always have (default false: get_the_title())
 *   excerpt    fill the excerpt, trimmed to 55 words (default false)
 *   label      fill the label with the primary category (default true)
 */
function foxtail_post_item( $post, $options = array() ) {
  $post = get_post( $post );

  if ( ! $post ) {
    return null;
  }

  $options = wp_parse_args( $options, array( 'raw_title' => false, 'excerpt' => false, 'label' => true ) );

  return array(
    'source'        => 'post',
    'url'           => get_permalink( $post ),
    'title'         => $options['raw_title'] ? esc_html( $post->post_title ) : get_the_title( $post ),
    'label'         => $options['label'] ? foxtail_primary_cat( $post ) : '',
    'excerpt'       => $options['excerpt'] ? wp_trim_words( get_the_excerpt( $post ), 55, ' ...' ) : '',
    'image_id'      => (int) get_post_thumbnail_id( $post ),
    'post_id'       => $post->ID,
    'external_host' => false,
  );
}

// Build an item from External item fields. Returns null unless it has a title and an http(s) URL.
function foxtail_external_item( $fields ) {
  if ( ! is_array( $fields ) || empty( $fields['title'] ) || ! foxtail_is_http_url( $fields['url'] ?? '' ) ) {
    return null;
  }

  $excerpt = trim( (string) ( $fields['excerpt'] ?? '' ) );

  return array(
    'source'        => 'external',
    'url'           => $fields['url'],
    'title'         => esc_html( $fields['title'] ),
    'label'         => esc_html( trim( (string) ( $fields['label'] ?? '' ) ) ),
    'excerpt'       => $excerpt === '' ? '' : esc_html( wp_trim_words( $excerpt, 55, ' ...' ) ),
    'image_id'      => (int) ( $fields['image'] ?? 0 ),
    'post_id'       => 0,
    'external_host' => foxtail_external_host( $fields['url'] ),
  );
}

/**
 * The item in a hero or secondary slot, or null if the slot is empty.
 *
 * $group is the slot's ACF group value. The slot's fields are $post_field (post object),
 * $source_field ('post' or 'external'; anything else, including never saved, means post)
 * and $external_field (the cloned External item fields).
 * $options are passed to foxtail_post_item().
 */
function foxtail_featured_item( $group, $post_field, $source_field, $external_field, $options = array() ) {
  if ( ! is_array( $group ) ) {
    return null;
  }

  if ( ( $group[ $source_field ] ?? '' ) === 'external' ) {
    return foxtail_external_item( $group[ $external_field ] ?? null );
  }

  return empty( $group[ $post_field ] ) ? null : foxtail_post_item( $group[ $post_field ], $options );
}

// The curated items of a Featured Collection set to "External (curated)", at most 6, in order.
function foxtail_collection_items( $group ) {
  $items = array();

  foreach ( (array) ( $group['external_items'] ?? array() ) as $row ) {
    $item = foxtail_external_item( $row );

    if ( $item ) {
      $items[] = $item;
    }
  }

  return array_slice( $items, 0, 6 );
}

// Should a Featured Collection render? Collections with a Display toggle (#2-#5) can be switched off;
// $display_default is used when the toggle has never been saved. Curated collections need at least one item.
function foxtail_collection_visible( $group, $display_default = true ) {
  if ( ! is_array( $group ) ) {
    return false;
  }

  $display = array_key_exists( 'display', $group ) && $group['display'] !== null ? (bool) $group['display'] : $display_default;

  if ( ! $display ) {
    return false;
  }

  return ( $group['category_or_tag'] ?? '' ) !== 'external' || count( foxtail_collection_items( $group ) ) > 0;
}

// Add a post-sourced item to the posts the homepage collections skip. External items aren't posts.
function foxtail_mark_displayed( $item ) {
  global $_displayed_posts;

  if ( $item && $item['post_id'] ) {
    $_displayed_posts[] = $item['post_id'];
  }
}

/**
 * The item's image at $size ('1x1' or '3x2'). Posts use their featured image, exactly as
 * get_the_post_thumbnail() prints it. When there is no image (or the attachment was deleted):
 * $fallback 'div' prints partials/fallback-image.php (made for cards), 'img' prints a fallback
 * <img> cropped to $size, for layouts that style an <img> (hero, secondary slots).
 */
function foxtail_item_image( $item, $size, $attr = array(), $fallback = 'div' ) {
  if ( $item['post_id'] ) {
    $html = get_the_post_thumbnail( $item['post_id'], $size, $attr );
  } else {
    $html = $item['image_id'] ? wp_get_attachment_image( $item['image_id'], $size, false, $attr ) : '';
  }

  if ( $html !== '' ) {
    return $html;
  }

  if ( $fallback === 'img' ) {
    $dimensions = $size === '3x2' ? array( 1080, 720 ) : array( 800, 800 );
    $class = trim( ( $attr['class'] ?? '' ) . ' ft-c-fallback-img ft-c-fallback-img--' . $size );
    $src = get_template_directory_uri() . '/assets/images/fallback/bg-' . rand( 1, 15 ) . '.jpg';

    return sprintf( '<img width="%d" height="%d" src="%s" class="%s" alt="" decoding="async" />', $dimensions[0], $dimensions[1], esc_url( $src ), esc_attr( $class ) );
  }

  ob_start();
  get_template_part( 'partials/fallback-image' );
  return ob_get_clean();
}

// rel="external" for items that link to another site, else ''.
function foxtail_item_rel( $item ) {
  return $item['external_host'] ? ' rel="external"' : '';
}

// The external-link icon and a screen reader "(opens firefox.com)" label, for links that leave the site.
function foxtail_external_marker( $host ) {
  if ( ! $host ) {
    return '';
  }

  /* translators: %s: the external site's domain, e.g. firefox.com */
  return '<span class="ft-c-external-icon" aria-hidden="true"></span><span class="screen-reader-text"> ' . esc_html( sprintf( __( '(opens %s)', 'foxtail' ), $host ) ) . '</span>';
}
