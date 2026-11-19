<?php
/**
 * The "Firefox posts have moved" note in the site-wide feed (/en/feed/). See
 * "Feed note" in README.md.
 *
 * When switched on (Tools -> Firefox redirects), the site-wide feed gets one
 * extra item at the top that points subscribers to the firefox.com feed, and
 * the feed's description gets a short line. The Firefox category feeds don't
 * need it: they forward to firefox.com with the redirects (inc/redirects.php).
 *
 * The item has a fixed ID, so feed readers show it once. Its date is when the
 * note was switched on, so it doesn't jump back to the top on every fetch.
 *
 * Tests: scripts/test-feed-note.php (runs without WordPress).
 *
 * @package foxtail
 */

/** A fixed ID, the same on every environment, so readers show the item once. */
const FOXTAIL_FEED_NOTE_ID = 'tag:blog.mozilla.org,2026-12-01:firefox-posts-moved';

/**
 * The firefox.com blog for each language whose Firefox category has a
 * redirect row (redirects/categories.csv, path category/firefox), active or
 * not. Languages without one get no note: their Firefox posts are still
 * published here.
 *
 * @param array|null $map The lookup from inc/redirects-map.php.
 * @return array language => array( blog URL, feed URL )
 */
function foxtail_feed_note_targets( $map ) {
	$targets = array();
	if ( ! is_array( $map ) || empty( $map['categories'] ) ) {
		return $targets;
	}
	foreach ( $map['categories'] as $lang => $rows ) {
		if ( ! empty( $rows['category/firefox'][0] ) ) {
			$blog             = $rows['category/firefox'][0];
			$targets[ $lang ] = array( $blog, $blog . 'feed/' );
		}
	}
	return $targets;
}

/**
 * The feed address as shown to people: short, without the language
 * (firefox.com redirects it to the visitor's language). Links use the full URL.
 *
 * @return string
 */
function foxtail_feed_note_display() {
	return 'firefox.com/blog/feed';
}

/**
 * Whether the switch (Tools -> Firefox redirects) is on.
 *
 * @return bool
 */
function foxtail_feed_note_enabled() {
	return '1' === (string) get_option( 'foxtail_feed_note_enabled', '0' );
}

/**
 * The note's target for this request: the site-wide posts feed (not a
 * category, tag, author, search, date or comments feed) in RSS 2.0 or Atom,
 * in a language with a firefox.com feed, with the switch on.
 *
 * @return array|null array( blog URL, feed URL )
 */
function foxtail_feed_note_target() {
	// WordPress doesn't set is_home() for feeds: the site-wide feed is the one
	// that isn't for an archive, a post, a search or comments.
	if ( ! foxtail_feed_note_enabled() || ! is_feed() || is_archive() || is_singular() || is_search() || is_comment_feed() ) {
		return null;
	}
	// Only the formats that get the item (RDF and RSS 0.92 are left alone).
	$format = (string) get_query_var( 'feed' );
	if ( '' === $format || 'feed' === $format ) {
		$format = get_default_feed();
	}
	if ( ! in_array( $format, array( 'rss2', 'atom' ), true ) ) {
		return null;
	}
	$lang    = apply_filters( 'wpml_current_language', null );
	$lang    = $lang ? $lang : 'en';
	$targets = foxtail_feed_note_targets( function_exists( 'foxtail_redirect_map' ) ? foxtail_redirect_map() : null );
	return isset( $targets[ $lang ] ) ? $targets[ $lang ] : null;
}

/**
 * When the note was switched on (Unix time). Falls back to the newest post,
 * so the date is stable even if the option is missing.
 *
 * @return int
 */
function foxtail_feed_note_since() {
	$since = (int) get_option( 'foxtail_feed_note_since', 0 );
	return $since ? $since : (int) strtotime( get_lastpostmodified( 'GMT' ) . ' UTC' );
}

/**
 * The note's copy.
 *
 * @param string $feed The firefox.com feed URL.
 * @return array { title, text, html }
 */
function foxtail_feed_note_copy( $feed ) {
	$title = 'Firefox posts have moved to firefox.com';
	$short = foxtail_feed_note_display();
	$text  = 'New posts about Firefox now publish on the Firefox blog at firefox.com. Mozilla news stays here, in this feed. To keep getting Firefox posts, subscribe to the new feed: ' . $short;
	$html  = '<p>New posts about Firefox now publish on the Firefox blog at firefox.com. Mozilla news stays here, in this feed.</p>'
		. '<p>To keep getting Firefox posts, subscribe to the new feed: <a href="' . esc_url( $feed ) . '">' . esc_html( $short ) . '</a></p>'
		. '<p>Each topic on the Firefox blog has its own feed too, linked from its topic page.</p>';
	return compact( 'title', 'text', 'html' );
}

/**
 * Text for a CDATA section.
 *
 * @param string $s Text.
 * @return string
 */
function foxtail_feed_note_cdata( $s ) {
	return '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $s ) . ']]>';
}

/**
 * The item, at the top of the RSS 2.0 feed. rss2_head runs right before the
 * posts; the late priority keeps other channel-level output (site icon,
 * plugins) ahead of it.
 */
function foxtail_feed_note_rss2() {
	$target = foxtail_feed_note_target();
	if ( ! $target ) {
		return;
	}
	list( $blog, $feed ) = $target;
	$copy                = foxtail_feed_note_copy( $feed );
	echo "\n\t<item>\n";
	echo "\t\t<title>" . esc_xml( $copy['title'] ) . "</title>\n";
	echo "\t\t<link>" . esc_url( $blog ) . "</link>\n";
	echo "\t\t<pubDate>" . esc_xml( gmdate( 'D, d M Y H:i:s +0000', foxtail_feed_note_since() ) ) . "</pubDate>\n";
	echo "\t\t<guid isPermaLink=\"false\">" . esc_xml( FOXTAIL_FEED_NOTE_ID ) . "</guid>\n";
	echo "\t\t<description>" . foxtail_feed_note_cdata( $copy['text'] ) . "</description>\n";
	echo "\t\t<content:encoded>" . foxtail_feed_note_cdata( $copy['html'] ) . "</content:encoded>\n";
	echo "\t</item>\n";
}
add_action( 'rss2_head', 'foxtail_feed_note_rss2', PHP_INT_MAX );

/**
 * The same as an Atom entry (atom_head runs right before the posts).
 */
function foxtail_feed_note_atom() {
	$target = foxtail_feed_note_target();
	if ( ! $target ) {
		return;
	}
	list( $blog, $feed ) = $target;
	$copy                = foxtail_feed_note_copy( $feed );
	$date                = gmdate( 'Y-m-d\TH:i:s\Z', foxtail_feed_note_since() );
	echo "\n\t<entry>\n";
	echo "\t\t<author><name>" . esc_xml( get_bloginfo_rss( 'name' ) ) . "</name></author>\n";
	echo "\t\t<title type=\"text\">" . esc_xml( $copy['title'] ) . "</title>\n";
	echo "\t\t<link rel=\"alternate\" type=\"text/html\" href=\"" . esc_url( $blog ) . "\" />\n";
	echo "\t\t<id>" . esc_xml( FOXTAIL_FEED_NOTE_ID ) . "</id>\n";
	echo "\t\t<updated>" . esc_xml( $date ) . "</updated>\n";
	echo "\t\t<published>" . esc_xml( $date ) . "</published>\n";
	echo "\t\t<summary type=\"text\">" . esc_xml( $copy['text'] ) . "</summary>\n";
	echo "\t\t<content type=\"html\">" . foxtail_feed_note_cdata( $copy['html'] ) . "</content>\n";
	echo "\t</entry>\n";
}
add_action( 'atom_head', 'foxtail_feed_note_atom', PHP_INT_MAX );

/**
 * A short line after the feed's description (RSS description, Atom
 * subtitle), for readers that show it.
 *
 * @param string $value The escaped value.
 * @param string $show  Which bloginfo field.
 * @return string
 */
function foxtail_feed_note_description( $value, $show ) {
	if ( 'description' !== $show || ! foxtail_feed_note_target() ) {
		return $value;
	}
	$line  = 'Firefox posts now publish on firefox.com: ' . foxtail_feed_note_display();
	$value = rtrim( $value );
	if ( '' !== $value && ! preg_match( '/[.!?]$/', $value ) ) {
		$value .= '.';
	}
	return trim( $value . ' ' . esc_xml( $line ) );
}
add_filter( 'bloginfo_rss', 'foxtail_feed_note_description', 10, 2 );

/**
 * A feed date moved forward to when the note was switched on, if it's older.
 * Otherwise readers asking "changed since?" would get 304 Not Modified (no
 * new post since their last fetch) and never see the note.
 *
 * @param string|false $date   The date WordPress worked out (false if none).
 * @param int|false    $parsed $date as a Unix time (false if none).
 * @param string       $format The format to return.
 * @return string|false
 */
function foxtail_feed_note_bump_date( $date, $parsed, $format ) {
	$since = (int) get_option( 'foxtail_feed_note_since', 0 );
	if ( ! $since || ! foxtail_feed_note_target() ) {
		return $date;
	}
	return false !== $parsed && $parsed >= $since ? $date : gmdate( $format, $since );
}

/**
 * The lastBuildDate/updated element.
 *
 * @param string|false $date   The date, in $format.
 * @param string       $format The format asked for.
 * @return string|false
 */
function foxtail_feed_note_build_date( $date, $format ) {
	return foxtail_feed_note_bump_date( $date, false === $date ? false : strtotime( $date ), $format );
}
add_filter( 'get_feed_build_date', 'foxtail_feed_note_build_date', 10, 2 );

/**
 * The newest post's date (GMT, MySQL format), which WordPress uses for a
 * feed's Last-Modified and ETag headers.
 *
 * @param string|false $date     The date.
 * @param string       $timezone Which time zone it's in.
 * @return string|false
 */
function foxtail_feed_note_last_modified( $date, $timezone ) {
	if ( 'gmt' !== strtolower( (string) $timezone ) ) {
		return $date;
	}
	return foxtail_feed_note_bump_date( $date, false === $date ? false : strtotime( $date . ' UTC' ), 'Y-m-d H:i:s' );
}
add_filter( 'get_lastpostmodified', 'foxtail_feed_note_last_modified', 10, 2 );

/**
 * Remembers when the note was switched on (the item's date). Here rather than
 * in the admin page, so WP-CLI changes set it too.
 *
 * @param mixed $old Old value.
 * @param mixed $new New value.
 */
function foxtail_feed_note_switched( $old, $new ) {
	if ( '1' === (string) $new && '1' !== (string) $old ) {
		update_option( 'foxtail_feed_note_since', time(), false );
	}
}
add_action( 'update_option_foxtail_feed_note_enabled', 'foxtail_feed_note_switched', 10, 2 );
add_action(
	'add_option_foxtail_feed_note_enabled',
	function ( $name, $value ) {
		foxtail_feed_note_switched( '0', $value );
	},
	10,
	2
);
