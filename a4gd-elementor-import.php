<?php
/**
 * Apply the Elementor brand kit + "A4GD —" section library to the LIVE site from the baked
 * bundle. Runs on boot via `wp eval-file` (WordPress is bootstrapped by wp-cli).
 *
 * CONTENT-SAFE BY DESIGN:
 *  - Provision-once: gated on the `a4gd_elementor_provision` option matching the bundle version,
 *    so the client's own later tweaks to globals/templates are never overwritten on redeploy.
 *  - Only ever writes Elementor's own records (the kit's settings meta + elementor_library
 *    section posts). Never touches pages, posts, or any client content.
 *  - Idempotent: templates are replaced by slug, so a forced re-run can't duplicate them.
 *
 * To re-provision after updating the bundle, bump its "version" — the flag then differs and it
 * applies once more.
 */

$bundle_path = '/seed/elementor-bundle.json';
if ( ! file_exists( $bundle_path ) ) {
	echo "[elementor] no bundle at {$bundle_path}\n";
	return;
}
$bundle = json_decode( file_get_contents( $bundle_path ), true );
if ( ! is_array( $bundle ) ) {
	echo "[elementor] bundle unreadable\n";
	return;
}

$version = (string) ( $bundle['version'] ?? '1' );
if ( get_option( 'a4gd_elementor_provision' ) === $version ) {
	echo "[elementor] already provisioned (v{$version}) — preserving current state.\n";
	return;
}

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	echo "[elementor] Elementor not active yet — skipping (will retry next boot).\n";
	return;
}

// --- Brand kit globals --------------------------------------------------------------------
if ( ! empty( $bundle['kit_settings'] ) ) {
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( $kit_id ) {
		update_post_meta( $kit_id, '_elementor_page_settings', $bundle['kit_settings'] );
		echo "[elementor] kit {$kit_id} globals applied.\n";
	}
}

// --- Section library ----------------------------------------------------------------------
$count = 0;
foreach ( (array) ( $bundle['templates'] ?? array() ) as $t ) {
	$slug  = $t['slug'] ?? '';
	$title = $t['title'] ?? $slug;
	$type  = $t['type'] ?? 'container';
	$data  = $t['data'] ?? array();
	if ( ! $slug ) {
		continue;
	}

	$existing = get_posts( array(
		'post_type'   => 'elementor_library',
		'name'        => $slug,
		'post_status' => 'any',
		'numberposts' => 1,
		'fields'      => 'ids',
	) );
	$postarr = array(
		'post_title'  => $title,
		'post_name'   => $slug,
		'post_status' => 'publish',
		'post_type'   => 'elementor_library',
	);
	if ( $existing ) {
		$postarr['ID'] = $existing[0];
		$id = wp_update_post( $postarr );
	} else {
		$id = wp_insert_post( $postarr );
	}
	if ( ! $id || is_wp_error( $id ) ) {
		continue;
	}

	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', $type );
	update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '4.0.0' );
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	wp_set_object_terms( $id, $type, 'elementor_library_type' );
	$count++;
}
echo "[elementor] section library applied: {$count} templates.\n";

if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

update_option( 'a4gd_elementor_provision', $version );
echo "[elementor] provisioned v{$version}.\n";
