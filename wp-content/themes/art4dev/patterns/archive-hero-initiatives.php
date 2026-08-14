<?php
/**
 * Title: Initiatives Archive Hero
 * Slug: art4dev/archive-hero-initiatives
 * Categories: art4dev
 * Inserter: no
 *
 * Static hero for the initiatives archive (/initiatives/). Uses the prototype's
 * section > .container structure so it aligns with the rest of the site rather than getting
 * WP's constrained-layout centering. The Take Action → Initiatives *page* has its own hero
 * (from ACF via page-hero); this covers the CPT archive route.
 *
 * @package art4dev
 */
?>
<!-- wp:html -->
<section class="page-hero">
	<div class="container">
		<span class="eyebrow eyebrow--clay reveal">Take Action</span>
		<h1 class="reveal" style="margin-top:1.5rem;">Initiatives</h1>
		<p class="page-hero__lede reveal">Arts-led programs built with communities — exhibitions, workshops, and long-running collaborations putting creativity to work on real problems.</p>
	</div>
</section>
<!-- /wp:html -->
