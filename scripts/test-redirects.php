<?php
/**
 * Tests for the redirect matcher in inc/redirects.php, without WordPress.
 * Run: php scripts/test-redirects.php (CI runs it on PHP 7.4 and 8.2).
 */

require __DIR__ . '/../inc/redirects.php';

$map = array(
	'posts'      => array(
		'en' => array(
			'crlite'        => array( 81467, 'https://www.firefox.com/en-US/blog/crlite/', true ),
			'441'           => array( 50001, 'https://www.firefox.com/en-US/blog/441/', true ),
			'not-live-yet'  => array( 50002, 'https://www.firefox.com/en-US/blog/not-live-yet/', false ),
			'caf%c3%a9'     => array( 50003, 'https://www.firefox.com/en-US/blog/cafe/', true ),
		),
		'de' => array(
			'profile-management' => array( 60001, 'https://www.firefox.com/de/blog/profile-management/', true ),
		),
	),
	'categories' => array(
		'en' => array(
			'firefox'                           => array( 'https://www.firefox.com/en-US/blog/', true ),
			'category/firefox'                  => array( 'https://www.firefox.com/en-US/blog/', true ),
			'category/firefox/privacy-security' => array( 'https://www.firefox.com/en-US/blog/topics/privacy-security/', false ),
		),
	),
);
$site = array(
	'languages' => array( 'en', 'de', 'fr' ),
	'default'   => 'en',
);

$post     = function ( $id ) {
	return array( 'post_id' => $id, 'is_404' => false, 'is_category' => false );
};
$notfound = array( 'post_id' => 0, 'is_404' => true, 'is_category' => false );
$category = array( 'post_id' => 0, 'is_404' => false, 'is_category' => true );
$other    = array( 'post_id' => 0, 'is_404' => false, 'is_category' => false );

$crlite = 'https://www.firefox.com/en-US/blog/crlite/';
$home   = 'https://www.firefox.com/en-US/blog/';

// array( path, context, expected URL or null, expected active )
$cases = array(
	// Every path form of a post, resolved by WordPress to the post itself.
	array( '/en/mozilla/crlite/', $post( 81467 ), $crlite ),
	array( '/en/firefox/crlite/', $post( 81467 ), $crlite ),
	array( '/firefox/crlite/', $post( 81467 ), $crlite ),
	array( '/en/firefox/privacy-security/crlite/', $post( 81467 ), $crlite ),
	array( '/EN/Mozilla/CRLite/', $post( 81467 ), $crlite ),
	array( '/en/mozilla/crlite', $post( 81467 ), $crlite ),
	array( '/en/mozilla/crlite/2/', $post( 81467 ), $crlite ),
	array( '/en/mozilla/crlite/amp/', $post( 81467 ), $crlite ),
	array( '/en/mozilla/crlite/comment-page-1/', $post( 81467 ), $crlite ),
	// Per-post comment feeds.
	array( '/en/mozilla/crlite/feed/', $post( 81467 ), $crlite ),
	array( '/en/mozilla/crlite/feed/atom/', $post( 81467 ), $crlite ),
	// Legacy forms that WordPress would otherwise 404 or guess.
	array( '/en/products/firefox/crlite/', $notfound, $crlite ),
	array( '/blog/2025/08/19/crlite/', $notfound, $crlite ),
	array( '/crlite/', $notfound, $crlite ),
	// An unpublished post (404 at its old permalink).
	array( '/en/mozilla/crlite/', $notfound, $crlite ),
	// Numeric slug, and its pagination.
	array( '/en/firefox/privacy-security/441/', $post( 50001 ), 'https://www.firefox.com/en-US/blog/441/' ),
	array( '/en/firefox/privacy-security/441/2/', $post( 50001 ), 'https://www.firefox.com/en-US/blog/441/' ),
	// Percent-encoded slugs match case-insensitively.
	array( '/en/firefox/caf%C3%A9/', $post( 50003 ), 'https://www.firefox.com/en-US/blog/cafe/' ),
	// Inactive rows match but don't redirect.
	array( '/en/firefox/not-live-yet/', $post( 50002 ), 'https://www.firefox.com/en-US/blog/not-live-yet/', false ),
	// Locale matters: the en row doesn't apply to other locales and vice versa.
	array( '/de/firefox-de/crlite/', $notfound, null ),
	array( '/fr/firefox-fr/profile-management/', $post( 70001 ), null ),
	array( '/de/firefox-de/profile-management/', $post( 60001 ), 'https://www.firefox.com/de/blog/profile-management/' ),
	array( '/en/firefox/profile-management/', $post( 78000 ), null ),
	// Same slug, different post (e.g. a later post reused it): no redirect.
	array( '/en/mozilla/crlite/', $post( 99999 ), null ),
	// Same slug on something that isn't a single post: no redirect.
	array( '/en/crlite/', $other, null ),
	array( '/en/tag/crlite/', $notfound, null ),
	array( '/en/category/crlite/', $notfound, null ),
	array( '/en/author/crlite/', $notfound, null ),
	array( '/wp-content/uploads/2025/08/crlite/', $notfound, null ),
	array( '/en/wp-json/wp/v2/posts/crlite/', $notfound, null ),
	// Category archives and their pages.
	array( '/en/firefox/', $category, $home ),
	array( '/en/category/firefox/', $category, $home ),
	array( '/en/category/firefox/page/3/', $category, $home ),
	array( '/firefox/', $category, $home ),
	array( '/en/category/firefox/privacy-security/', $category, 'https://www.firefox.com/en-US/blog/topics/privacy-security/', false ),
	// Category feeds stay.
	array( '/en/category/firefox/feed/', $category, null ),
	array( '/en/firefox/feed/', $category, null ),
	// Only listed categories, only on category archives, only their locale.
	array( '/en/category/mozilla/news/', $category, null ),
	array( '/en/category/firefox/mozilla-vpn/', $category, null ),
	array( '/de/category/firefox/', $category, null ),
	array( '/en/firefox/', $notfound, null ),
	// Everything else stays.
	array( '/', $other, null ),
	array( '/en/', $other, null ),
	array( '/en/feed/', $other, null ),
	array( '/feed/', $other, null ),
	array( '/en/tag/firefox/', $other, null ),
	array( '/en/2025/08/', $other, null ),
	array( '/en/latest/', $other, null ),
	array( '/en/some-mozilla-post/', $post( 12345 ), null ),
	array( '/en/does-not-exist/', $notfound, null ),
);

$failed = 0;
foreach ( $cases as $case ) {
	list( $path, $context, $want_url ) = $case;
	$want_active = isset( $case[3] ) ? $case[3] : true;
	$got         = foxtail_redirect_find( $map, $path, $site, $context );
	$got_url     = $got ? $got['url'] : null;
	$ok          = $got_url === $want_url && ( ! $got || $got['active'] === $want_active );
	if ( ! $ok ) {
		$failed++;
		printf(
			"FAIL %s: want %s, got %s\n",
			$path,
			$want_url ? $want_url . ( $want_active ? '' : ' (inactive)' ) : 'no match',
			$got ? $got_url . ( $got['active'] ? '' : ' (inactive)' ) : 'no match'
		);
	}
}

// UTM parameters are kept, everything else is dropped.
$utm_cases = array(
	array( array(), $crlite ),
	array( array( 'utm_source' => 'newsletter', 'utm_campaign' => 'a b' ), $crlite . '?utm_source=newsletter&utm_campaign=a%20b' ),
	array( array( 'p' => '1', 'utm_medium' => 'email', 'fbclid' => 'x' ), $crlite . '?utm_medium=email' ),
	array( array( 'utm_source' => array( 'x' ) ), $crlite ),
);
foreach ( $utm_cases as $case ) {
	$got = foxtail_redirect_with_utm( $crlite, $case[0] );
	if ( $got !== $case[1] ) {
		$failed++;
		printf( "FAIL utm %s: want %s, got %s\n", json_encode( $case[0] ), $case[1], $got );
	}
}

$total = count( $cases ) + count( $utm_cases );
if ( $failed ) {
	printf( "%d of %d redirect tests failed.\n", $failed, $total );
	exit( 1 );
}
printf( "All %d redirect tests passed.\n", $total );
