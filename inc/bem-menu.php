<?php

/**
 * Walker Texas Ranger
 * Inserts some BEM naming sensibility into Wordpress menus
 */

class walker_texas_ranger extends Walker_Nav_Menu {

    function __construct($css_class_prefix) {
      $this->css_class_prefix = $css_class_prefix;

      // Define menu item names appropriately
      $this->item_css_class_suffixes = array(
        'item'                      => '__item',
        'parent_item'               => '__item--parent',
        'active_item'               => '__item--active',
        'parent_of_active_item'     => '__item--parent--active',
        'ancestor_of_active_item'   => '__item--ancestor--active',
        'sub_menu'                  => '__sub-menu',
        'sub_menu_item'             => '__sub-menu__item',
        'link'                      => '__link',
        'external'                  => '--external',
      );
    }

    // Return the host of a link that leaves this site, or false for internal links.
    // Relative URLs and links to the blog's own host (with or without www.) are internal.
    function external_host($url) {
      $host = wp_parse_url($url, PHP_URL_HOST);

      if (empty($host)) {
        return false;
      }

      $host = preg_replace('/^www\./', '', strtolower($host));
      $own_host = preg_replace('/^www\./', '', strtolower(wp_parse_url(home_url(), PHP_URL_HOST)));

      return $host === $own_host ? false : $host;
    }

    // Check for children

    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ){
      $id_field = $this->db_fields['id'];

      if ( is_object( $args[0] ) ) {
        $args[0]->has_children = !empty( $children_elements[$element->$id_field] );
      }

      return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function start_lvl(&$output, $depth = 1, $args=array()) {
      $real_depth = $depth + 1;

      $indent = str_repeat("\t", $real_depth);

      $prefix = $this->css_class_prefix;
      $suffix = $this->item_css_class_suffixes;

      $classes = array(
        $prefix . $suffix['sub_menu'],
        $prefix . $suffix['sub_menu']. '--' . $real_depth
      );

      $class_names = implode( ' ', $classes );

      // Add a ul wrapper to sub nav

      $output .= "\n" . $indent . '<ul class="'. $class_names .'">' ."\n";
    }


    // Add main/sub classes to li's and links
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      global $wp_query;

      $indent = ( $depth > 0 ? str_repeat( "    ", $depth ) : '' ); // code indent

      $prefix = $this->css_class_prefix;
      $suffix = $this->item_css_class_suffixes;

      // Links to other sites get a modifier class, rel="external" and a screen reader label
      $external_host = $this->external_host($item->url);

      // Item classes
      $item_classes =  array(
        'item_class'            => $depth == 0 ? $prefix . $suffix['item'] : '',
        'parent_class'          => $args->has_children ? $parent_class = $prefix . $suffix['parent_item'] : '',
        'active_page_class'     => in_array("current-menu-item",$item->classes) ? $prefix . $suffix['active_item'] : '',
        'active_parent_class'   => in_array("current-menu-parent",$item->classes) ? $prefix . $suffix['parent_of_active_item'] : '',
        'active_ancestor_class' => in_array("current-menu-ancestor",$item->classes) ? $prefix . $suffix['ancestor_of_active_item'] : '',
        'depth_class'           => $depth >=1 ? $prefix . $suffix['sub_menu_item'] . ' ' . $prefix . $suffix['sub_menu'] . '--' . $depth . '__item' : '',
        'item_id_class'         => $prefix . '__item--'. $item->object_id,
        'user_class'            => $item->classes[0] !== '' ? $prefix . '__item--'. $item->classes[0] : ''
      );

      // convert array to string excluding any empty values
      $class_string = implode("  ", array_filter($item_classes));

      // Add the classes to the wrapping <li>
      $output .= $indent . '<li class="' . $class_string . '">';

      // Link classes
      $link_classes = array(
        'item_link'             => $depth == 0 ? $prefix . $suffix['link'] : '',
        'depth_class'           => $depth >=1 ? $prefix . $suffix['sub_menu'] . $suffix['link'] . '  ' . $prefix . $suffix['sub_menu'] . '--' . $depth . $suffix['link'] : '',
      );

      $rel = preg_split('/\s+/', trim((string) $item->xfn), -1, PREG_SPLIT_NO_EMPTY);

      if ($external_host) {
        $link_classes['external_class'] = $depth == 0 ? $prefix . $suffix['link'] . $suffix['external'] : $prefix . $suffix['sub_menu'] . $suffix['link'] . $suffix['external'];
        $rel[] = 'external';
      }

      $link_class_string = implode("  ", array_filter($link_classes));
      $link_class_output = 'class="' . $link_class_string . '"';
      $rel_string = implode(' ', array_unique($rel));

      // link attributes
      $attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
      $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
      $attributes .= ! empty($rel_string)       ? ' rel="'    . esc_attr($rel_string      ) .'"' : '';
      $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"' : '';

      // Create link markup
      $item_output = $args->before;
      $item_output .= '<a' . $attributes . ' ' . $link_class_output . '>';
      $item_output .=     $args->link_before;
      $item_output .=     apply_filters('the_title', $item->title, $item->ID);
      if ($external_host) {
        $item_output .= '<span class="' . $prefix . '__external-icon" aria-hidden="true"></span>';
        /* translators: %s: the external site's domain, e.g. firefox.com */
        $item_output .= '<span class="screen-reader-text"> ' . esc_html(sprintf(__('(opens %s)', 'foxtail'), $external_host)) . '</span>';
      }
      $item_output .=     $args->link_after;
      $item_output .=     $args->after;
      $item_output .= '</a>';

      // Filter <li>
      $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * bem_menu returns an instance of the walker_texas_ranger class with the following arguments
 * @param  string $location This must be the same as what is set in wp-admin/settings/menus for menu location.
 * @param  string $css_class_prefix This string will prefix all of the menu's classes, BEM syntax friendly
 * @param  arr/string $css_class_modifiers Provide either a string or array of values to apply extra classes to the <ul> but not the <li's>
 * @return [type]
 */

function bem_menu($location = "main_menu", $css_class_prefix = 'main-menu', $css_class_modifiers = null){

    // Check to see if any css modifiers were supplied
    if($css_class_modifiers){
      if(is_array($css_class_modifiers)){
        $modifiers = implode(" ", $css_class_modifiers);
      } elseif (is_string($css_class_modifiers)) {
        $modifiers = $css_class_modifiers;
      }
    } else {
      $modifiers = '';
    }

    $args = array(
      'theme_location'    => $location,
      'container'         => false,
      'items_wrap'        => '<ul class="' . $css_class_prefix . ' ' . $modifiers . '">%3$s</ul>',
      'walker'            => new walker_texas_ranger($css_class_prefix, true)
    );

    if (has_nav_menu($location)){
      return wp_nav_menu($args);
    } else {
      echo "<p>You need to first define a menu in WP-admin<p>";
    }
}
