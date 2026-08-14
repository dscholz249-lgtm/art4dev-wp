<?php
/**
 * Title: Initiative Meta
 * Slug: art4dev/initiative-meta
 * Categories: art4dev
 * Description: Status / year / regions / partners row for an initiative.
 * Inserter: no
 *
 * Every row is conditional because the data is uneven: all 5 initiatives have status and
 * year_started, 4 have regions_text, 3 have partners, and none currently have learn_more_url.
 * Rendering empty labels would look broken, so absent fields drop out entirely.
 *
 * `partners` is a comma-separated string per schema §1.2, not a repeater — split for display.
 *
 * @package art4dev
 */

$a4gd_status   = get_field( 'status' );
$a4gd_year     = get_field( 'year_started' );
$a4gd_regions  = get_field( 'regions_text' );
$a4gd_partners = get_field( 'partners' );
$a4gd_flagship = (bool) get_field( 'is_flagship' );

$a4gd_rows = array();

if ( $a4gd_status ) {
	$a4gd_rows['Status'] = ucfirst( $a4gd_status ) . ( $a4gd_flagship ? ' · Flagship' : '' );
}
if ( $a4gd_year ) {
	$a4gd_rows['Since'] = $a4gd_year;
}
if ( $a4gd_regions ) {
	$a4gd_rows['Where'] = $a4gd_regions;
}
if ( $a4gd_partners ) {
	$a4gd_rows['Partners'] = implode( ' · ', array_map( 'trim', explode( ',', $a4gd_partners ) ) );
}

if ( ! $a4gd_rows ) {
	return;
}
?>
<!-- wp:html -->
<dl class="init-meta">
	<?php foreach ( $a4gd_rows as $a4gd_label => $a4gd_value ) : ?>
		<dt><?php echo esc_html( $a4gd_label ); ?></dt>
		<dd><?php echo esc_html( $a4gd_value ); ?></dd>
	<?php endforeach; ?>
</dl>
<!-- /wp:html -->
