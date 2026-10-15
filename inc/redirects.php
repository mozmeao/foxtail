<?php
/**
 * Redirects migrated posts and Firefox category archives to firefox.com/blog.
 * See "Redirects" in README.md.
 *
 * The list lives in redirects/posts.csv and redirects/categories.csv;
 * `npm run build` validates it and writes inc/redirects-map.php.
 *
 * Posts are matched on locale + final slug, so every path WordPress accepts
 * for a post (any category path, /firefox/<slug>/, /blog/YYYY/MM/DD/<slug>/,
 * /<permalink>/feed/, ...) goes to the same place. A match only redirects
 * when the request resolves to that very post ID, or is a 404 (a legacy path
 * form, or the post was unpublished after migrating). Category rows only
 * apply to category archives (and their /page/N/), never to their feeds.
 * Tags, authors, dates, search and site-wide feeds are never touched.
 *
 * Rows with active=no don't redirect. They add an X-Foxtail-Redirect response
 * header instead, so scripts/redirects-check.mjs can confirm they match.
 *
 * Tools -> Firefox redirects (inc/redirects-admin.php) switches the whole
 * feature on or off for the site, and picks the status code. Off (the
 * default) means nothing redirects; matches only get an
 * "X-Foxtail-Redirect: off <url>" header. The status is 302 (temporary, the
 * default) or 301 (permanent, cached by browsers).
 *
 * @package foxtail
 */


/**
 * Splits a request path into locale and segments, without the trailing
 * feed, pagination, comment-page, amp, embed or trackback parts.
 *
 * @param string   $path      URL path, e.g. /en/firefox/crlite/feed/.
 * @param string[] $languages Language directories (WPML), e.g. array( 'en', 'de' ).
 * @param string   $default   Language of paths without a language directory.
 * @return array { locale: string, segments: string[], feed: bool }
 */
function foxtail_redirect_parse_path( $path, $languages, $default ) {
	$segments = array_values( array_filter( explode( '/', strtolower( $path ) ), 'strlen' ) );

	$locale = $default;
	if ( $segments && in_array( $segments[0], $languages, true ) ) {
		$locale = array_shift( $segments );
	}

	$feed = false;
	$n    = count( $segments );
	if ( $n >= 2 && 'feed' === $segments[ $n - 2 ] && in_array( $segments[ $n - 1 ], array( 'rss', 'rss2', 'atom', 'rdf' ), true ) ) {
		array_splice( $segments, -2 );
		$feed = true;
	} elseif ( $n && 'feed' === $segments[ $n - 1 ] ) {
		array_pop( $segments );
		$feed = true;
	}

	$n = count( $segments );
	if ( $n >= 2 && 'page' === $segments[ $n - 2 ] && ctype_digit( $segments[ $n - 1 ] ) ) {
		array_splice( $segments, -2 );
	} elseif ( $n && preg_match( '/^comment-page-[0-9]+$/', $segments[ $n - 1 ] ) ) {
		array_pop( $segments );
	}

	$n = count( $segments );
	if ( $n && in_array( $segments[ $n - 1 ], array( 'amp', 'embed', 'trackback' ), true ) ) {
		array_pop( $segments );
	}

	return array(
		'locale'   => $locale,
		'segments' => $segments,
		'feed'     => $feed,
	);
}

/**
 * Finds the redirect for a request, if any.
 *
 * @param array  $map     The lookup from inc/redirects-map.php.
 * @param string $path    URL path.
 * @param array  $site    languages (string[]) and default (string).
 * @param array  $context What WordPress resolved the request to:
 *                        post_id (int, the queried post when it's a single post, else 0),
 *                        is_404, is_category (bool).
 * @return array|null { url: string, active: bool, type: 'post'|'category' }
 */
function foxtail_redirect_find( $map, $path, $site, $context ) {
	$parsed   = foxtail_redirect_parse_path( $path, $site['languages'], $site['default'] );
	$segments = $parsed['segments'];
	$locale   = $parsed['locale'];
	if ( ! $segments ) {
		return null;
	}

	// Posts: the final slug. A numeric last segment can be post pagination
	// (/<slug>/2/), so the segment before it is tried too.
	$posts = isset( $map['posts'][ $locale ] ) ? $map['posts'][ $locale ] : array();
	$bases = array( 'category', 'tag', 'author', 'search', 'comments', 'collections', 'wp-content', 'wp-includes', 'wp-json', 'wp-admin' );
	if ( $posts && ! in_array( $segments[0], $bases, true ) ) {
		$candidates = array( end( $segments ) );
		if ( count( $segments ) > 1 && ctype_digit( end( $segments ) ) ) {
			$candidates[] = $segments[ count( $segments ) - 2 ];
		}
		foreach ( $candidates as $slug ) {
			if ( ! isset( $posts[ $slug ] ) ) {
				continue;
			}
			list( $id, $url, $active ) = $posts[ $slug ];
			if ( (int) $context['post_id'] === (int) $id || ( ! $context['post_id'] && $context['is_404'] ) ) {
				return array(
					'url'    => $url,
					'active' => (bool) $active,
					'type'   => 'post',
				);
			}
		}
	}

	// Category archives (and their pages), never their feeds.
	$categories = isset( $map['categories'][ $locale ] ) ? $map['categories'][ $locale ] : array();
	$key        = implode( '/', $segments );
	if ( $context['is_category'] && ! $parsed['feed'] && isset( $categories[ $key ] ) ) {
		list( $url, $active ) = $categories[ $key ];
		return array(
			'url'    => $url,
			'active' => (bool) $active,
			'type'   => 'category',
		);
	}

	return null;
}

/**
 * Adds the request's utm_* parameters to a URL, so campaign links keep
 * their attribution. Other parameters are dropped.
 *
 * @param string $url   Target URL.
 * @param array  $query The request's query parameters (unslashed).
 * @return string
 */
function foxtail_redirect_with_utm( $url, $query ) {
	$utm = array();
	foreach ( $query as $key => $value ) {
		if ( is_string( $key ) && 0 === strpos( $key, 'utm_' ) && is_string( $value ) ) {
			$utm[ $key ] = $value;
		}
	}
	return $utm ? $url . '?' . http_build_query( $utm, '', '&', PHP_QUERY_RFC3986 ) : $url;
}

/**
 * The lookup, or null when the build hasn't generated it (redirects off).
 *
 * @return array|null
 */
function foxtail_redirect_map() {
	static $map = false;
	if ( false === $map ) {
		$file = get_template_directory() . '/inc/redirects-map.php';
		$map  = is_readable( $file ) ? include $file : null;
	}
	return is_array( $map ) ? $map : null;
}

/**
 * Whether the site-wide switch (Tools -> Firefox redirects) is on.
 *
 * @return bool
 */
function foxtail_redirects_enabled() {
	return '1' === (string) get_option( 'foxtail_redirects_enabled', '0' );
}

/**
 * The redirect status chosen in Tools -> Firefox redirects: 302 or 301.
 *
 * @return int
 */
function foxtail_redirect_status() {
	return '301' === (string) get_option( 'foxtail_redirects_status', '302' ) ? 301 : 302;
}

/**
 * Runs before WordPress' own canonical redirects (priority 10), so legacy
 * URLs go to firefox.com in one hop.
 */
function foxtail_redirect() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : 'GET';
	if ( ! in_array( $method, array( 'GET', 'HEAD' ), true ) || is_preview() || is_customize_preview() ) {
		return;
	}
	$map = foxtail_redirect_map();
	if ( ! $map ) {
		return;
	}

	$path      = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', PHP_URL_PATH );
	// The home option, not home_url(): WPML adds the language directory to home_url().
	$home_path = rtrim( (string) wp_parse_url( get_option( 'home' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = substr( $path, strlen( $home_path ) );
	}

	// WPML has already read the language directory (or picked the default
	// language for paths without one). Without WPML, any locale in the list counts.
	$current = apply_filters( 'wpml_current_language', null );
	$site    = $current
		? array( 'languages' => array( $current ), 'default' => $current )
		: array( 'languages' => array_keys( $map['posts'] + $map['categories'] ), 'default' => 'en' );
	$context = array(
		'post_id'     => is_singular( 'post' ) ? get_queried_object_id() : 0,
		'is_404'      => is_404(),
		'is_category' => is_category(),
	);

	$match = foxtail_redirect_find( $map, $path, $site, $context );
	if ( ! $match ) {
		return;
	}
	if ( ! $match['active'] ) {
		header( 'X-Foxtail-Redirect: inactive ' . $match['url'] );
		return;
	}
	if ( ! foxtail_redirects_enabled() ) {
		header( 'X-Foxtail-Redirect: off ' . $match['url'] );
		return;
	}
	if ( wp_redirect( foxtail_redirect_with_utm( $match['url'], wp_unslash( $_GET ) ), foxtail_redirect_status(), 'Foxtail' ) ) {
		exit;
	}
}

if ( function_exists( 'add_action' ) ) {
	add_action( 'template_redirect', 'foxtail_redirect', 1 );
	if ( is_admin() ) {
		require __DIR__ . '/redirects-admin.php';
	}
}
