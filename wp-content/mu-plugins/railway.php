<?php
/**
 * Railway environment adapter (must-use plugin).
 *
 * Loaded automatically (mu-plugins need no activation). Makes a stock WordPress
 * container behave correctly behind Railway's TLS-terminating edge proxy, without
 * baking the Railway hostname into the database — so the same image works no matter
 * what domain Railway assigns, across every redeploy.
 *
 * @package art4dev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * 1. HTTPS awareness.
 * Railway terminates TLS at the edge and forwards plain HTTP to the container with
 * X-Forwarded-Proto: https. Without this, WordPress thinks the request is HTTP,
 * builds http:// URLs, and bounces into a redirect loop against its https siteurl.
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

/*
 * 2. Canonical URL.
 * Force home/siteurl to the Railway-assigned public domain at runtime via filters,
 * rather than writing it into wp_options. This keeps the URL correct even if the
 * domain changes between deploys, and means the seed database's original
 * art4development.local values can never surface. Filters win over the stored option.
 */
$a4gd_railway_domain = getenv( 'RAILWAY_PUBLIC_DOMAIN' );
if ( $a4gd_railway_domain ) {
	$a4gd_railway_url = 'https://' . $a4gd_railway_domain;
	add_filter( 'option_home', function () use ( $a4gd_railway_url ) {
		return $a4gd_railway_url;
	} );
	add_filter( 'option_siteurl', function () use ( $a4gd_railway_url ) {
		return $a4gd_railway_url;
	} );
}
