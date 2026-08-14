<?php
/**
 * Title: Footer Brand
 * Slug: art4dev/site-footer-brand
 * Inserter: no
 *
 * Theme-hosted logo + newsletter + social. PHP (not a plain part) so the logo URI
 * resolves at runtime — see patterns/site-brand.php for the reasoning.
 *
 * The newsletter form has no handler: the static prototype stubs it with
 * preventDefault(), and wiring it up is a post-migration task for Dan (Fluent Forms).
 *
 * @package art4dev
 */
?>
<!-- wp:html -->
<div class="site-footer__brand">
	<img src="<?php echo esc_url( get_theme_file_uri( 'assets/logos/logo-horizontal-dark.svg' ) ); ?>" alt="Arts for Global Development" />
	<h4>Let’s together reimagine the power of arts and creativity for our planet and people.</h4>
	<div class="site-footer__newsletter">
		<form onsubmit="event.preventDefault();">
			<input type="email" placeholder="Subscribe to our newsletter — email" aria-label="Email address" />
			<button type="submit" class="link-arrow">Subscribe <span class="arrow" aria-hidden="true">→</span></button>
		</form>
	</div>
	<div class="site-footer__social">
		<a href="#" aria-label="Instagram">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
		</a>
		<a href="#" aria-label="LinkedIn">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM0 8h5v16H0V8zm7.5 0H12v2.2h.07c.62-1.16 2.12-2.4 4.36-2.4 4.66 0 5.52 3.06 5.52 7.05V24h-5v-7.1c0-1.7-.03-3.87-2.36-3.87-2.36 0-2.72 1.84-2.72 3.74V24h-5V8z"/></svg>
		</a>
	</div>
</div>
<!-- /wp:html -->
