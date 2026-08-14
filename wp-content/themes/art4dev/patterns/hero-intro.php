<?php
/**
 * Title: Hero Intro — Our Purpose
 * Slug: art4dev/hero-intro
 * Categories: art4dev
 * Description: The "Our Purpose" statement that sits directly beneath the hero.
 *
 * @package art4dev
 */
?>
<!-- wp:group {"tagName":"section","className":"hero-intro","layout":{"type":"default"}} -->
<section class="wp-block-group hero-intro">
	<!-- wp:html -->
	<div class="container">
		<div class="hero-intro__grid">
			<h2 class="hero-intro__label reveal">Our Purpose</h2>
			<div class="reveal">
				<p class="hero-intro__body">
					At Arts for Global Development we bring a multi-disciplinary, cross-sectoral and participatory approach to development — engaging individuals and organizations worldwide in creating and maintaining arts-infused positive impact. A registered 501(c)3 educational nonprofit, we champion the use and application of the arts to challenge societal inequities and environmental degradation.
				</p>
				<div class="hero-intro__meta">
					<a href="<?php echo esc_url( a4gd_page_url( 'who-we-are' ) ); ?>" class="btn btn--ink">Our story <span class="arrow" aria-hidden="true">→</span></a>
					<a href="<?php echo esc_url( a4gd_page_url( 'initiatives' ) ); ?>" class="link-arrow">See current work <span class="arrow" aria-hidden="true">→</span></a>
				</div>
			</div>
		</div>
		<div class="hero-intro__rule"></div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
