<?php
/**
 * Tests for the feed note in inc/feed-note.php, without WordPress: the few
 * WordPress functions it uses are stubbed below.
 * Run: php scripts/test-feed-note.php (CI runs it on PHP 7.4 and 8.2).
 */

// --- Stubs -----------------------------------------------------------------

$GLOBALS['t'] = array();

function foxtail_test_reset() {
	$GLOBALS['t'] = array(
		'options' => array(),
		'feed'    => true,     // is_feed()
		'archive' => false,    // is_archive()
		'single'  => false,    // is_singular()
		'search'  => false,    // is_search()
		'comment' => false,    // is_comment_feed()
		'format'  => 'feed',   // get_query_var( 'feed' )
		'lang'    => 'en',     // WPML's current language
		'map'     => array(
			'posts'      => array(),
			'categories' => array(
				'en' => array(
					'firefox'                       => array( 'https://www.firefox.com/en-US/blog/', false ),
					'category/firefox'              => array( 'https://www.firefox.com/en-US/blog/', false ),
					'category/firefox/firefox-tips' => array( 'https://www.firefox.com/en-US/blog/topics/tips-and-tricks/', false ),
				),
			),
		),
		'lastpost' => '2026-10-15 00:28:05',
	);
}
foxtail_test_reset();

function add_action() {}
function add_filter() {}
function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['t']['options'] ) ? $GLOBALS['t']['options'][ $name ] : $default;
}
function update_option( $name, $value ) {
	$GLOBALS['t']['options'][ $name ] = $value;
	return true;
}
function is_feed() { return $GLOBALS['t']['feed']; }
function is_archive() { return $GLOBALS['t']['archive']; }
function is_singular() { return $GLOBALS['t']['single']; }
function is_search() { return $GLOBALS['t']['search']; }
function is_comment_feed() { return $GLOBALS['t']['comment']; }
function get_query_var( $name ) { return 'feed' === $name ? $GLOBALS['t']['format'] : ''; }
function get_default_feed() { return 'rss2'; }
function apply_filters( $hook, $value ) {
	return 'wpml_current_language' === $hook ? $GLOBALS['t']['lang'] : $value;
}
function get_lastpostmodified() { return $GLOBALS['t']['lastpost']; }
function get_bloginfo_rss() { return 'The Mozilla Blog'; }
function esc_xml( $s ) { return htmlspecialchars( (string) $s, ENT_XML1 | ENT_QUOTES, 'UTF-8' ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function foxtail_redirect_map() { return $GLOBALS['t']['map']; }

require __DIR__ . '/../inc/feed-note.php';

// --- Tests -----------------------------------------------------------------

$failed = 0;
$check  = function ( $label, $got, $want ) use ( &$failed ) {
	if ( $got !== $want ) {
		$failed++;
		printf( "FAIL %s: want %s, got %s\n", $label, var_export( $want, true ), var_export( $got, true ) );
	}
};
$on = function () {
	foxtail_test_reset();
	$GLOBALS['t']['options']['foxtail_feed_note_enabled'] = '1';
	$GLOBALS['t']['options']['foxtail_feed_note_since']   = gmmktime( 12, 0, 0, 12, 1, 2026 ); // Dec 1, 12:00 UTC
};
$en = array( 'https://www.firefox.com/en-US/blog/', 'https://www.firefox.com/en-US/blog/feed/' );

// Targets come from the category/firefox rows of the redirect map.
$check( 'targets: en row', foxtail_feed_note_targets( $GLOBALS['t']['map'] ), array( 'en' => $en ) );
$check( 'targets: no map', foxtail_feed_note_targets( null ), array() );
$check( 'targets: no category/firefox row', foxtail_feed_note_targets( array( 'categories' => array( 'de' => array( 'category/firefox-de' => array( 'https://www.firefox.com/de/blog/', false ) ) ) ) ), array() );

// Which feeds get the note.
foxtail_test_reset();
$check( 'off: no note', foxtail_feed_note_target(), null );
$cases = array(
	array( 'site-wide feed', array(), $en ),
	array( 'site-wide feed, /feed/rss2/', array( 'format' => 'rss2' ), $en ),
	array( 'site-wide feed, Atom', array( 'format' => 'atom' ), $en ),
	array( 'RDF', array( 'format' => 'rdf' ), null ),
	array( 'RSS 0.92', array( 'format' => 'rss' ), null ),
	array( 'not a feed', array( 'feed' => false ), null ),
	array( 'category/tag/author/date feed', array( 'archive' => true ), null ),
	array( 'comment feed of a post', array( 'single' => true ), null ),
	array( 'search feed', array( 'search' => true ), null ),
	array( 'site-wide comments feed', array( 'comment' => true ), null ),
	array( 'German feed (no de row)', array( 'lang' => 'de' ), null ),
	array( 'no WPML', array( 'lang' => null ), $en ),
	array( 'no redirect map', array( 'map' => null ), null ),
);
foreach ( $cases as $case ) {
	$on();
	$GLOBALS['t'] = array_merge( $GLOBALS['t'], $case[1] );
	$check( "target: {$case[0]}", foxtail_feed_note_target(), $case[2] );
}

// Feed description.
$on();
$check( 'description: line added', foxtail_feed_note_description( 'News and Updates about Mozilla', 'description' ), 'News and Updates about Mozilla. Firefox posts now publish on firefox.com: firefox.com/blog/feed' );
$check( 'description: keeps its own full stop', foxtail_feed_note_description( 'News.', 'description' ), 'News. Firefox posts now publish on firefox.com: firefox.com/blog/feed' );
$check( 'description: other fields untouched', foxtail_feed_note_description( 'The Mozilla Blog', 'name' ), 'The Mozilla Blog' );
foxtail_test_reset();
$check( 'description: off', foxtail_feed_note_description( 'News', 'description' ), 'News' );

// Feed dates move forward to the switch-on time, never back.
$on();
$check( 'build date: older is bumped', foxtail_feed_note_build_date( 'Tue, 13 Oct 2026 13:31:38 +0000', 'r' ), 'Tue, 01 Dec 2026 12:00:00 +0000' );
$check( 'build date: newer is kept', foxtail_feed_note_build_date( 'Wed, 02 Dec 2026 09:00:00 +0000', 'r' ), 'Wed, 02 Dec 2026 09:00:00 +0000' );
$check( 'build date: none', foxtail_feed_note_build_date( false, 'Y-m-d\TH:i:s\Z' ), '2026-12-01T12:00:00Z' );
$check( 'last modified: older is bumped', foxtail_feed_note_last_modified( '2026-10-15 00:28:05', 'GMT' ), '2026-12-01 12:00:00' );
$check( 'last modified: newer is kept', foxtail_feed_note_last_modified( '2026-12-02 09:00:00', 'gmt' ), '2026-12-02 09:00:00' );
$check( 'last modified: server time zone untouched', foxtail_feed_note_last_modified( '2026-10-15 00:28:05', 'server' ), '2026-10-15 00:28:05' );
$GLOBALS['t']['archive'] = true;
$check( 'last modified: other feeds untouched', foxtail_feed_note_last_modified( '2026-10-15 00:28:05', 'GMT' ), '2026-10-15 00:28:05' );
foxtail_test_reset();
$GLOBALS['t']['options']['foxtail_feed_note_enabled'] = '1';
$check( 'build date: no switch-on time yet', foxtail_feed_note_build_date( 'Tue, 13 Oct 2026 13:31:38 +0000', 'r' ), 'Tue, 13 Oct 2026 13:31:38 +0000' );
$check( 'since: falls back to the newest post', foxtail_feed_note_since(), gmmktime( 0, 28, 5, 10, 15, 2026 ) );

// Switching on records the time; saving again while on doesn't change it.
foxtail_test_reset();
foxtail_feed_note_switched( '0', '1' );
$since = get_option( 'foxtail_feed_note_since' );
$check( 'switched on: time recorded', is_int( $since ) && abs( $since - time() ) < 5, true );
$GLOBALS['t']['options']['foxtail_feed_note_since'] = 123;
foxtail_feed_note_switched( '1', '1' );
$check( 'still on: time kept', get_option( 'foxtail_feed_note_since' ), 123 );
foxtail_feed_note_switched( '1', '0' );
$check( 'switched off: time kept', get_option( 'foxtail_feed_note_since' ), 123 );

// CDATA can't be broken out of.
$check( 'cdata', foxtail_feed_note_cdata( 'a]]>b' ), '<![CDATA[a]]]]><![CDATA[>b]]>' );

// The RSS item and Atom entry are well-formed XML with the fixed ID.
$on();
ob_start();
foxtail_feed_note_rss2();
$rss = ob_get_clean();
$xml = @simplexml_load_string( '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/"><channel>' . $rss . '</channel></rss>' );
$check( 'rss: well-formed', false !== $xml, true );
$check( 'rss: guid', $xml ? (string) $xml->channel->item->guid : null, FOXTAIL_FEED_NOTE_ID );
$check( 'rss: date', $xml ? (string) $xml->channel->item->pubDate : null, 'Tue, 01 Dec 2026 12:00:00 +0000' );
$check( 'rss: links the full feed URL', false !== strpos( $rss, 'href="https://www.firefox.com/en-US/blog/feed/"' ), true );
$GLOBALS['t']['format'] = 'atom';
ob_start();
foxtail_feed_note_atom();
$atom = ob_get_clean();
$xml  = @simplexml_load_string( '<feed xmlns="http://www.w3.org/2005/Atom">' . $atom . '</feed>' );
$check( 'atom: well-formed', false !== $xml, true );
$check( 'atom: id', $xml ? (string) $xml->entry->id : null, FOXTAIL_FEED_NOTE_ID );
$GLOBALS['t']['format'] = 'rdf';
ob_start();
foxtail_feed_note_rss2();
$check( 'rdf: no item', ob_get_clean(), '' );

if ( $failed ) {
	printf( "%d feed note test(s) failed.\n", $failed );
	exit( 1 );
}
echo "All feed note tests passed.\n";
