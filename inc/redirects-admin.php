<?php
/**
 * Tools -> Firefox redirects: the on/off switch for inc/redirects.php and a
 * read-only view of the redirect list. The list itself is edited in git
 * (redirects/*.csv, see "Redirects" in README.md), not here.
 *
 * @package foxtail
 */

/**
 * Both settings are options of this site (wp_278_options), so they travel
 * with environment copies of the site's tables. Defaults: off, 302.
 */
function foxtail_redirects_register_setting() {
	register_setting(
		'foxtail_redirects',
		'foxtail_redirects_enabled',
		array(
			'type'              => 'string',
			'default'           => '0',
			'sanitize_callback' => function ( $value ) {
				return '1' === (string) $value ? '1' : '0';
			},
		)
	);
	register_setting(
		'foxtail_redirects',
		'foxtail_redirects_status',
		array(
			'type'              => 'string',
			'default'           => '302',
			'sanitize_callback' => function ( $value ) {
				return '301' === (string) $value ? '301' : '302';
			},
		)
	);
}
add_action( 'admin_init', 'foxtail_redirects_register_setting' );

/**
 * Records who changed a setting and when, and clears the WP Engine cache so
 * cached pages pick up the change.
 *
 * @param string $change What changed, e.g. "Turned on".
 */
function foxtail_redirects_record_change( $change ) {
	$user = wp_get_current_user();
	update_option(
		'foxtail_redirects_changed',
		array(
			'change' => $change,
			'user'   => $user->exists() ? $user->user_login : '',
			'time'   => time(),
		),
		false
	);
	// Once per request, even when both settings changed in one save.
	static $purge_scheduled = false;
	if ( ! $purge_scheduled && class_exists( 'WpeCommon' ) ) {
		$purge_scheduled = true;
		add_action(
			'shutdown',
			function () {
				WpeCommon::purge_memcached();
				WpeCommon::purge_varnish_cache();
			}
		);
	}
}

/**
 * Hooks for a setting being added (first save) or updated.
 *
 * @param string   $option   Option name.
 * @param string   $default  Value before the first save.
 * @param callable $describe Returns the change description for a new value.
 */
function foxtail_redirects_watch( $option, $default, $describe ) {
	$changed = function ( $old, $new ) use ( $describe ) {
		if ( (string) $old !== (string) $new ) {
			foxtail_redirects_record_change( $describe( (string) $new ) );
		}
	};
	add_action( "update_option_{$option}", $changed, 10, 2 );
	add_action(
		"add_option_{$option}",
		function ( $name, $value ) use ( $changed, $default ) {
			$changed( $default, $value );
		},
		10,
		2
	);
}
foxtail_redirects_watch(
	'foxtail_redirects_enabled',
	'0',
	function ( $value ) {
		return '1' === $value ? 'Turned on' : 'Turned off';
	}
);
foxtail_redirects_watch(
	'foxtail_redirects_status',
	'302',
	function ( $value ) {
		return "Status set to {$value}";
	}
);

function foxtail_redirects_admin_menu() {
	add_management_page( 'Firefox redirects', 'Firefox redirects', 'manage_options', 'foxtail-redirects', 'foxtail_redirects_admin_page' );
}
add_action( 'admin_menu', 'foxtail_redirects_admin_menu' );

/**
 * A yes/no badge.
 *
 * @param bool   $on    State.
 * @param string $yes   Label when on.
 * @param string $no    Label when off.
 * @return string HTML.
 */
function foxtail_redirects_badge( $on, $yes = 'Active', $no = 'Inactive' ) {
	$style = $on ? 'background:#d7f0dd;color:#0a5c1f' : 'background:#f0f0f1;color:#50575e';
	return '<span style="display:inline-block;padding:1px 8px;border-radius:10px;font-size:12px;' . $style . '">' . esc_html( $on ? $yes : $no ) . '</span>';
}

/**
 * The other language versions of a post (WPML) and what happens to each.
 *
 * @param int   $id     Post ID.
 * @param array $listed Listed post IDs => active.
 * @return string HTML.
 */
function foxtail_redirects_translations( $id, $listed ) {
	$trid = apply_filters( 'wpml_element_trid', null, $id, 'post_post' );
	if ( ! $trid ) {
		return '';
	}
	$out = array();
	foreach ( (array) apply_filters( 'wpml_get_element_translations', null, $trid, 'post_post' ) as $lang => $t ) {
		$tid = isset( $t->element_id ) ? (int) $t->element_id : 0;
		if ( ! $tid || $tid === (int) $id ) {
			continue;
		}
		if ( ! isset( $listed[ $tid ] ) ) {
			$what = 'stays here';
		} else {
			$what = $listed[ $tid ] ? 'redirects' : 'listed, inactive';
		}
		$link  = get_edit_post_link( $tid );
		$out[] = ( $link ? '<a href="' . esc_url( $link ) . '">' . esc_html( $lang ) . '</a>' : esc_html( $lang ) ) . ': ' . esc_html( $what );
	}
	sort( $out );
	return implode( '<br>', $out );
}

function foxtail_redirects_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$map      = foxtail_redirect_map();
	$enabled  = foxtail_redirects_enabled();
	$status   = foxtail_redirect_status();
	$changed  = get_option( 'foxtail_redirects_changed' );
	$posts    = $map ? $map['posts'] : array();
	$cats     = $map ? $map['categories'] : array();
	$n_posts  = 0;
	$n_active = 0;
	$ids      = array();
	foreach ( $posts as $rows ) {
		foreach ( $rows as $row ) {
			$n_posts++;
			$n_active += $row[2] ? 1 : 0;
			$ids[]     = (int) $row[0];
		}
	}
	$n_cats        = 0;
	$n_cats_active = 0;
	foreach ( $cats as $rows ) {
		foreach ( $rows as $row ) {
			$n_cats++;
			$n_cats_active += $row[1] ? 1 : 0;
		}
	}
	if ( $ids ) {
		_prime_post_caches( $ids, false, false );
	}
	// Listed post IDs => active, to show what happens to each translation.
	$listed = array();
	foreach ( $posts as $rows ) {
		foreach ( $rows as $row ) {
			$listed[ (int) $row[0] ] = (bool) $row[2];
		}
	}
	?>
	<div class="wrap">
		<h1>Firefox redirects</h1>
		<p>Redirects blog posts and Firefox category archives that moved to firefox.com/blog. The list is part of the Foxtail theme: change it in the theme's repository (<code>redirects/posts.csv</code>, <code>redirects/categories.csv</code>) and deploy. This page switches the whole feature on or off and sets the status code.</p>

		<?php if ( ! $map ) : ?>
			<div class="notice notice-error inline"><p>The redirect list (<code>inc/redirects-map.php</code>) is missing from the theme, so nothing redirects. Deploy a theme built with <code>npm run build</code>.</p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'foxtail_redirects' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Redirects</th>
					<td>
						<label><input type="checkbox" name="foxtail_redirects_enabled" value="1" <?php checked( $enabled ); ?>> Redirect the rows marked Active below</label>
						<p class="description">
							Currently <strong><?php echo $enabled ? 'on' : 'off'; ?></strong>.
							When off, nothing redirects, whatever the list says. Saving clears the WP Engine cache.
							This setting is stored in this site's database, so copying the site to another environment copies it too.
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Status code</th>
					<td>
						<fieldset>
							<label><input type="radio" name="foxtail_redirects_status" value="302" <?php checked( 302, $status ); ?>> 302 Found (temporary)</label><br>
							<label><input type="radio" name="foxtail_redirects_status" value="301" <?php checked( 301, $status ); ?>> 301 Moved Permanently</label>
						</fieldset>
						<p class="description">Use 302 while testing and until the list is final. Browsers and search engines remember a 301: switching back to 302 doesn't undo it for visitors who already followed it.</p>
					</td>
				</tr>
			</table>
			<?php if ( is_array( $changed ) && ! empty( $changed['time'] ) && ! empty( $changed['change'] ) ) : ?>
				<p>Last change: <?php echo esc_html( $changed['change'] ); ?> by <?php echo esc_html( $changed['user'] ?: 'unknown' ); ?> on <?php echo esc_html( wp_date( 'Y-m-d H:i', $changed['time'] ) ); ?>.</p>
			<?php endif; ?>
			<?php submit_button( 'Save' ); ?>
		</form>

		<h2>Posts (<?php echo (int) $n_active; ?> of <?php echo (int) $n_posts; ?> active)</h2>
		<p>Every URL of a listed post redirects (any category path, old URL forms, its comment feed), also after the post is unpublished here.</p>
		<table class="widefat striped">
			<thead><tr><th>Status</th><th>Lang</th><th>Post on this site</th><th>Redirects to</th><th>Translations</th></tr></thead>
			<tbody>
			<?php foreach ( $posts as $locale => $rows ) : ?>
				<?php foreach ( $rows as $slug => $row ) : ?>
					<?php
					list( $id, $url, $active ) = $row;
					$post                      = get_post( $id );
					?>
					<tr>
						<td><?php echo foxtail_redirects_badge( $active ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
						<td><?php echo esc_html( $locale ); ?></td>
						<td>
							<?php if ( $post ) : ?>
								<a href="<?php echo esc_url( get_edit_post_link( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ?: $slug ); ?></a>
								<br><code><?php echo esc_html( rawurldecode( $slug ) ); ?></code>
								<?php if ( 'publish' !== $post->post_status ) : ?>
									&middot; <?php echo esc_html( $post->post_status ); ?>
								<?php endif; ?>
								<?php if ( preg_replace( '/__trashed$/', '', $post->post_name ) !== $slug ) : ?>
									<br><strong>Slug is now <code><?php echo esc_html( rawurldecode( $post->post_name ) ); ?></code>:</strong> the list needs updating.
								<?php endif; ?>
							<?php else : ?>
								<code><?php echo esc_html( rawurldecode( $slug ) ); ?></code> &middot; post <?php echo (int) $id; ?> not found
							<?php endif; ?>
						</td>
						<td><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( preg_replace( '#^https://www\.firefox\.com#', '', $url ) ); ?></a></td>
						<td><?php echo foxtail_redirects_translations( $id, $listed ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description">Translations: each language version of a post needs its own row to redirect. Until then it stays on this site.</p>

		<h2>Category archives (<?php echo (int) $n_cats_active; ?> of <?php echo (int) $n_cats; ?> active)</h2>
		<p>The archive and its pages redirect. Category feeds, tags, authors, date archives and search never redirect.</p>
		<table class="widefat striped">
			<thead><tr><th>Status</th><th>Lang</th><th>Archive</th><th>Redirects to</th></tr></thead>
			<tbody>
			<?php foreach ( $cats as $locale => $rows ) : ?>
				<?php foreach ( $rows as $path => $row ) : ?>
					<?php list( $url, $active ) = $row; ?>
					<tr>
						<td><?php echo foxtail_redirects_badge( $active ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
						<td><?php echo esc_html( $locale ); ?></td>
						<td><code>/<?php echo esc_html( $locale . '/' . $path ); ?>/</code></td>
						<td><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( preg_replace( '#^https://www\.firefox\.com#', '', $url ) ); ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
