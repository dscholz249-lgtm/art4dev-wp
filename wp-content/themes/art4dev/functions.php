<?php
/**
 * Arts for Global Development — theme setup.
 *
 * @package art4dev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types and taxonomies.
 *
 * Slugs here are load-bearing: the WXR content import (wp-handoff/03-content-import.xml)
 * is built against these exact slugs, and any deviation imports content into the void.
 * See wp-handoff/01-schema.md §1–2. Registered on `init` at the default priority so
 * ACF (which loads field groups on init:5) sees the post types when attaching location rules.
 */
add_action( 'init', function () {
	// Initiative CPT
	register_post_type( 'initiative', [
		'labels' => [
			'name'          => 'Initiatives',
			'singular_name' => 'Initiative',
			'add_new_item'  => 'Add New Initiative',
		],
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-lightbulb',
		'menu_position' => 20,
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ],
		'rewrite'       => [ 'slug' => 'initiatives' ],
		'show_in_rest'  => true,
	] );

	// Team Member CPT
	register_post_type( 'team_member', [
		'labels' => [
			'name'          => 'Team Members',
			'singular_name' => 'Team Member',
		],
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 21,
		'supports'      => [ 'title', 'editor', 'thumbnail' ],
		'rewrite'       => [ 'slug' => 'team' ],
		'show_in_rest'  => true,
	] );

	// Publication CPT
	register_post_type( 'publication', [
		'labels' => [
			'name'          => 'Publications',
			'singular_name' => 'Publication',
		],
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-book',
		'menu_position' => 22,
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
		/*
		 * DEVIATION from 06-setup-checklist.md §4 / schema §1.4, which specify
		 * 'publications'. That collides with the page tree in schema §1.1
		 * (Publications → Library, art'ishake): the CPT rule `publications/([^/]+)`
		 * is registered ahead of the page fallback, so /publications/ served the CPT
		 * archive instead of the page, /publications/library/ 404'd, and
		 * /publications/artishake/ redirected to a publication — silently, since
		 * get_permalink() still reported the page URLs. The two specs cannot both
		 * own /publications/*.
		 *
		 * Singular 'publication' frees the page tree. Cheap because 9 of the 11
		 * imported publications carry an external_url and link off-site, and schema §4
		 * already treats the Library page as the human-facing index. Approved by Dan
		 * 2026-07-16. See WORKLOG.md.
		 */
		'rewrite'       => [ 'slug' => 'publication' ],
		'show_in_rest'  => true,
	] );

	// art'ishake Issue CPT
	register_post_type( 'artishake_issue', [
		'labels' => [
			'name'          => 'art\'ishake Issues',
			'singular_name' => 'art\'ishake Issue',
		],
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-media-document',
		'menu_position' => 23,
		'supports'      => [ 'title', 'editor', 'thumbnail' ],
		'rewrite'       => [ 'slug' => 'artishake' ],
		'show_in_rest'  => true,
	] );

	// Taxonomies
	register_taxonomy( 'team_role', [ 'team_member' ], [
		'labels'            => [ 'name' => 'Team Roles', 'singular_name' => 'Team Role' ],
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'team-role' ],
	] );

	register_taxonomy( 'program_pillar', [ 'initiative' ], [
		'labels'            => [ 'name' => 'Program Pillars', 'singular_name' => 'Program Pillar' ],
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'pillar' ],
	] );

	register_taxonomy( 'region', [ 'initiative' ], [
		'labels'            => [ 'name' => 'Regions', 'singular_name' => 'Region' ],
		'hierarchical'      => false,
		'show_admin_column' => true,
		'show_in_rest'      => true,
	] );

	register_taxonomy( 'publication_type', [ 'publication' ], [
		'labels'            => [ 'name' => 'Publication Types', 'singular_name' => 'Publication Type' ],
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'publication-type' ],
	] );
} );

/**
 * Resolve a page's URL from its slug.
 *
 * The patterns link to a fixed set of pages, and hardcoding paths gets these wrong in ways
 * that hide behind WordPress's canonical redirect: "Champion the Work" has the slug
 * `champion`, so /champion-the-work/donate-sponsor/ 301s to /champion/donate-sponsor/ rather
 * than 404ing. Resolving by slug removes the redirect hop and survives the client re-nesting
 * or re-slugging a page later.
 *
 * @param string $slug Page slug (unique across pages in this content set).
 * @return string Permalink, or the site root if the page is gone.
 */
function a4gd_page_url( string $slug ): string {
	static $cache = [];

	if ( ! isset( $cache[ $slug ] ) ) {
		$ids = get_posts( [
			'post_type'        => 'page',
			'name'             => $slug,
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		] );

		$cache[ $slug ] = $ids ? get_permalink( $ids[0] ) : home_url( '/' );
	}

	return $cache[ $slug ];
}

/**
 * Theme supports and menu locations.
 */
add_action( 'after_setup_theme', function () {
	// Block themes get post-thumbnails automatically, but the schema calls for featured
	// images on Pages specifically (hero_image falls back to the featured image), and
	// every imported page carries a _thumbnail_id. Being explicit keeps that contract.
	add_theme_support( 'post-thumbnails' );
	add_post_type_support( 'page', 'thumbnail' );

	register_nav_menus( [
		'primary'       => 'Primary Navigation',
		'footer_org'    => 'Footer — Organization',
		'footer_engage' => 'Footer — Engage',
	] );
} );

/**
 * Block binding source: an ACF field's *unformatted* value.
 *
 * Block templates are .html, so ACF values reach them through the Block Bindings API.
 * ACF's own `acf/field` source returns the formatted value, and the handoff defines
 * hero_intro / short_bio as textareas with new_lines=wpautop — so `acf/field` hands back
 * "<p>text</p>", which bound into a paragraph block yields `<p><p>text</p></p>`: invalid
 * markup the browser then re-nests. Bindings only attach to paragraph/heading/image/button,
 * so there is no HTML-accepting block to bind instead.
 *
 * Passing $format_value=false returns the raw stored string and lets the block supply its
 * own tag. The field config stays exactly as the handoff specifies, so anywhere ACF renders
 * through PHP still gets its wpautop.
 */
add_action( 'init', function () {
	if ( ! function_exists( 'register_block_bindings_source' ) || ! function_exists( 'get_field' ) ) {
		return;
	}

	register_block_bindings_source( 'art4dev/field', [
		'label'              => 'ACF field (unformatted)',
		'uses_context'       => [ 'postId' ],
		'get_value_callback' => function ( array $source_args, $block_instance ) {
			if ( empty( $source_args['key'] ) ) {
				return null;
			}
			$post_id = $block_instance->context['postId'] ?? get_the_ID();
			if ( ! $post_id ) {
				return null;
			}
			$value = get_field( $source_args['key'], $post_id, false );

			// hero_title is empty on every imported page; schema §1.1 says fall back to the title.
			if ( '' === $value || null === $value || false === $value ) {
				return ! empty( $source_args['fallback'] ) && 'title' === $source_args['fallback']
					? get_the_title( $post_id )
					: null;
			}

			return is_scalar( $value ) ? (string) $value : null;
		},
	] );
} );

/**
 * Front-end and editor styles.
 *
 * theme.json covers the design tokens; main.css carries the component styles ported
 * from the static prototype that theme.json has no vocabulary for (nav dropdowns,
 * editorial grids, the collage strip).
 */
add_action( 'wp_enqueue_scripts', function () {
	$css = get_stylesheet_directory() . '/assets/css/main.css';

	wp_enqueue_style(
		'art4dev-main',
		get_stylesheet_directory_uri() . '/assets/css/main.css',
		[],
		file_exists( $css ) ? (string) filemtime( $css ) : '1.0.0'
	);

	// Not optional: .in-view starts at opacity:0 and only this script adds .is-visible,
	// so without it most of the page is invisible rather than merely un-animated.
	$js = get_stylesheet_directory() . '/assets/js/main.js';

	wp_enqueue_script(
		'art4dev-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		[],
		file_exists( $js ) ? (string) filemtime( $js ) : '1.0.0',
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);
} );

/**
 * Pattern category for the theme's own sections, so they group together in the inserter
 * instead of scattering through core's categories.
 */
add_action( 'init', function () {
	register_block_pattern_category( 'art4dev', [ 'label' => 'Arts for Global Development' ] );
} );
