<?php
/**
 * First-boot database seeder (mysqli — no mysql client binary needed).
 *
 * Waits for the database, then imports seed/database.sql only if the database is
 * still empty. Kept CLI-free on purpose: installing the mysql client pulls Debian's
 * apache2 meta-package, which re-enables a second MPM and makes Apache refuse to boot.
 *
 * Exit codes (read by railway-entrypoint.sh):
 *   0  seed freshly imported  -> caller runs the URL search-replace
 *   5  already seeded         -> caller skips the rewrite
 *   2  database never reachable
 *   3  seed file unreadable
 *   4  import error
 *
 * @package art4dev
 */

// PHP 8.1+ makes mysqli THROW on error by default. This script is written around
// return-false / check-errno semantics (a failed "is it already seeded?" probe on an empty
// database is expected, not fatal), so turn exceptions off and handle errors explicitly.
mysqli_report( MYSQLI_REPORT_OFF );

$host = getenv( 'WORDPRESS_DB_HOST' ) ?: 'localhost';
$port = 3306;
$colon = strpos( $host, ':' );
if ( false !== $colon ) {
	$port = (int) substr( $host, $colon + 1 );
	$host = substr( $host, 0, $colon );
}
$user   = getenv( 'WORDPRESS_DB_USER' ) ?: 'root';
$pass   = getenv( 'WORDPRESS_DB_PASSWORD' ) ?: '';
$name   = getenv( 'WORDPRESS_DB_NAME' ) ?: 'wordpress';
$prefix = getenv( 'WORDPRESS_TABLE_PREFIX' ) ?: 'wp_';

// Connect, retrying while the MySQL service comes up (up to ~2 min).
$db = null;
for ( $i = 0; $i < 60; $i++ ) {
	$m = mysqli_init();
	mysqli_options( $m, MYSQLI_OPT_CONNECT_TIMEOUT, 5 );
	if ( @mysqli_real_connect( $m, $host, $user, $pass, $name, $port ) ) {
		$db = $m;
		break;
	}
	fwrite( STDERR, "[import] waiting for database ({$host}:{$port})… " . mysqli_connect_error() . "\n" );
	sleep( 2 );
}
if ( ! $db ) {
	fwrite( STDERR, "[import] database never became reachable\n" );
	exit( 2 );
}
$db->set_charset( 'utf8mb4' );

// Idempotency: if the options table already has rows, this DB is already seeded.
$res = @$db->query( "SELECT 1 FROM `{$prefix}options` LIMIT 1" );
if ( $res && $res->num_rows > 0 ) {
	echo "[import] database already seeded — skipping import\n";
	exit( 5 );
}

$sql = @file_get_contents( '/seed/database.sql' );
if ( false === $sql ) {
	fwrite( STDERR, "[import] cannot read /seed/database.sql\n" );
	exit( 3 );
}

// The dump is plain DDL/INSERT (no routines), so multi_query runs it in one shot.
if ( $db->multi_query( $sql ) ) {
	do {
		if ( $r = $db->store_result() ) {
			$r->free();
		}
	} while ( $db->more_results() && $db->next_result() );
}
if ( $db->errno ) {
	fwrite( STDERR, '[import] SQL error: ' . $db->error . "\n" );
	exit( 4 );
}

echo "[import] seed imported successfully\n";
exit( 0 );
