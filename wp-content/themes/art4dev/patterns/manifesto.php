<?php
/**
 * Title: Manifesto
 * Slug: art4dev/manifesto
 * Categories: art4dev
 * Description: Dark full-width belief statement.
 *
 * Dark section, so it takes the white logo variant.
 *
 * @package art4dev
 */
?>
<!-- wp:group {"tagName":"section","className":"manifesto","layout":{"type":"default"}} -->
<section class="wp-block-group manifesto">
	<!-- wp:html -->
	<div class="container">
		<img class="manifesto__mark" src="<?php echo esc_url( get_theme_file_uri( 'assets/logos/logo-mark-dark.svg' ) ); ?>" alt="" />
		<h2>We are <em>Arts</em> for Global Development.</h2>
		<p>We believe art is a catalyst for learning, healing, and meaningful, positive change.</p>
		<p>We believe local stories matter globally, and that global perspectives enrich local action.</p>
		<p>We believe inclusion, curiosity, and collaboration are essential to lasting impact.</p>
		<div class="manifesto__sig">— Since 2002 —</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
