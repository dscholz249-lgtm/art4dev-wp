<?php
/**
 * Title: Collage Strip
 * Slug: art4dev/collage-strip
 * Categories: art4dev
 * Description: Four-image visual rhythm break.
 *
 * Every tile is the placeholder (attachment 1) until real photography is swapped in —
 * deliberately obvious, per schema §6. Alt text carries the *intended* subject from the
 * prototype so whoever swaps the imagery knows what each slot is meant to hold.
 *
 * @package art4dev
 */
$a4gd_placeholder = wp_get_attachment_image_url( 1, 'large' );
$a4gd_tiles       = array(
	'A painter with paint-streaked face concentrating on a canvas.',
	'Large sculptural letters spelling ARTS in a lit interior.',
	'Hands cupping water in a documentary-style portrait.',
	'A vibrant mural-style street artwork.',
);
?>
<!-- wp:html -->
<div class="collage in-view">
	<?php foreach ( $a4gd_tiles as $a4gd_alt ) : ?>
	<figure>
		<img src="<?php echo esc_url( $a4gd_placeholder ); ?>" alt="<?php echo esc_attr( $a4gd_alt ); ?>" loading="lazy" />
	</figure>
	<?php endforeach; ?>
</div>
<!-- /wp:html -->
