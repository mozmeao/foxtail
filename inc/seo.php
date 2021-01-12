<?php

/* Foxtail DIY WP SEO
  Usage: This function is called in functions.php and is then added to
  the header with <?php echo foxtail_seo(); ?>

	Optional: add custom description, and/or title
	to any post or page using these custom field keys:

		ft_seo_desc
		ft_seo_title

	To migrate from any SEO plugin, replace its custom field 
	keys with those listed above. More information:

*/
function foxtail_seo() {

  // SET UP VARIABLES
	global $page, $paged, $post;
  $description = ''; // description
  $robots = ''; // robot preferences
  $page_title = ''; // page title
  $image = ''; // page image
  $lang = 'en'; // some future-proofing

  // SET THE DESCRIPTION

  // use the field set in our option page, but fall back to wordpress desc if needed
  $site_description = ''; // temporary variable
  $description_wp = get_bloginfo('description', 'display'); // set in wordpress options
  if (class_exists('ACF')) {
    $description_ft = get_field('site_description_' . $lang, 'option'); // set in custom option page
  }
  $description_ft ? $site_description = $description_ft : $site_description = $description_wp;

  if ( get_post() ) {
    $seo_desc = get_post_meta($post->ID, 'ft_seo_desc', true);
    $pagedata = get_post($post->ID);
  }
  

  if (is_home() || is_front_page()) {
    $description = $site_description;
  }
	elseif (is_singular()) {

		if (!empty($seo_desc)) {
			$description = $seo_desc;
    } 
    elseif (!empty($pagedata)) {
      $description = get_the_excerpt($pagedata);
			$description = substr(trim(strip_tags($description)), 0, 155);
			$description = preg_replace('#\n#', ' ', $description);
			$description = preg_replace('#\s{2,}#', ' ', $description);
			$description = trim($description);
		} 
  } 
  elseif ((is_category() || is_tag()) && get_the_archive_description()) {
    $description = get_the_archive_description();
    $description = substr(trim(strip_tags($description)), 0, 155);
    $description = preg_replace('#\n#', ' ', $description);
    $description = preg_replace('#\s{2,}#', ' ', $description);
    $description = trim($description);
  }  
  else {
		$description = $site_description;
	}

  // SET THE ROBOT META PROPERTY
	if (is_category() || is_tag() || is_post_type_archive( 'collection' )) {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ($paged > 1) {
      $robots = "noindex,follow";
		} else {
      $robots = "index,follow";
		}
	} else if (is_home() || is_singular()) {
    $robots = "index,follow";
	} else {
    $robots = "noindex,follow";
	}

  // SET THE TITLE

  // use the field set in our option page, but fall back to wordpress desc if needed
  $name = '';
  $name_wp = get_bloginfo('name', 'display'); // set in wordpress options
  if (class_exists('ACF')) {
    $name_ft = get_field('site_name_' . $lang, 'option'); // set in custom panel
  }
  
  $name = $name_ft ? $name_ft : $name_wp;

  if ( get_post() ) {
    $title_custom = get_post_meta($post->ID, 'ft_seo_title', true);
  }
	$url = ltrim(esc_url($_SERVER['REQUEST_URI']), '/');
	$title = trim(wp_title('', false));
	$cat = single_cat_title('', false);
	$tag = single_tag_title('', false);
	$search = get_search_query();

	if (!empty($title_custom)) $title = $title_custom;
	if ($paged >= 2 || $page >= 2) $page_number = ' | ' . sprintf('Page %s', max($paged, $page));
	else $page_number = '';

	if (is_home() || is_front_page()) $seo_title = $name;
	elseif (is_singular())            $seo_title = $title . ' | ' . $name;
	elseif (is_tag())                 $seo_title = $tag . ' | ' . $name;
	elseif (is_category())            $seo_title = $cat . ' | ' . $name;
	elseif (is_archive())             $seo_title = $title . ' | ' . $name;
	elseif (is_search())              $seo_title = 'Search: ' . $search . ' | ' . $name;
	elseif (is_404())                 $seo_title = '404 - Not Found: ' . $url . ' | ' . $name;
	else                              $seo_title = $name . ' | ' . $description;

  $page_title = $seo_title . $page_number;

  // SET THE IMAGE

  if (class_exists('ACF')) {
    $site_image = get_field('site_image_' . $lang, 'option');
  }
  $site_image = wp_get_attachment_url($site_image, '3x2');

  if (is_singular()) {
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $pagedata ), '3x2' );
    $image = $thumbnail_src ? esc_attr( $thumbnail_src[0]) : $site_image;
  }
  elseif (is_category() || is_tag()) {
    $term = get_queried_object();
    if (class_exists('ACF')) {
      $image_display = get_field('tax_featured_image', $term);
    }
    $image = $image_display ? wp_get_attachment_image_url($image_display, '3x2') : $site_image;
  }
  else {
    $image = $site_image;
  }

  ob_start(); ?>
  <!-- Basic meta & SEO -->
  <title><?php echo $page_title ?></title>
  <meta name="description" content="<?php echo esc_attr($description) ?>" />
  <meta name="robots" content="<? echo esc_attr($robots)?>">

   <!-- OpenGraph -->
  <meta property="og:title" content="<?php echo esc_attr($page_title) ?>" />
  <meta property="og:description" content="<?php echo esc_attr($description) ?>" />
  <meta property="og:image" content="<?php echo esc_attr($image) ?>" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:creator" content="@firefox" />
  <meta name="twitter:title" content="<?php echo esc_attr($page_title) ?>" />
  <meta name="twitter:description" content="<?php echo esc_attr($description) ?>" />
  <meta name="twitter:image" content="<?php echo esc_attr($image) ?>" />

  <!-- Favicons & Icons -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/apple-touch-icon.png' ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/favicon-32x32.png' ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/favicon-16x16.png' ?>">
  <link rel="manifest" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/site.webmanifest' ?>">
  <link rel="mask-icon" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/safari-pinned-tab.svg' ?>" color="#20123a">
  <link rel="shortcut icon" href="<?php echo get_template_directory_uri() . '/assets/images/favicons/favicon.ico' ?>">
  <meta name="msapplication-TileColor" content="#20123a">
  <meta name="msapplication-config" content="<?php echo get_template_directory_uri() . '/assets/images/favicons/browserconfig.xml'  ?>">
  <meta name="theme-color" content="#7542E5">

  <?php $output = ob_get_clean();

 

	return $output;
}

?>