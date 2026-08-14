<?php
/**
 * Title: Hero — Full Bleed
 * Slug: art4dev/hero-full-bleed
 * Categories: art4dev
 * Description: Edge-to-edge hero image with the display headline overlaid.
 *
 * The image is the page's featured image (the placeholder, until real photography lands),
 * not a hardcoded file — swapping it is then just the Featured Image control, which is the
 * handoff's stated plan for image swaps (schema §6).
 *
 * @package art4dev
 */
?>
<!-- wp:group {"tagName":"section","className":"hero","layout":{"type":"default"}} -->
<section class="wp-block-group hero">
	<!-- wp:post-featured-image {"className":"hero__image","sizeSlug":"full"} /-->

	<!-- wp:group {"className":"container hero__content","layout":{"type":"constrained","contentSize":"1320px"}} -->
	<div class="wp-block-group container hero__content">
		<!-- wp:heading {"level":1,"className":"hero__title reveal"} -->
		<h1 class="wp-block-heading hero__title reveal">Reimagine the<br>power of <span class="word-italic">arts</span>.</h1>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<span class="hero__credit">Photograph — Lerin Mutlu</span>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
