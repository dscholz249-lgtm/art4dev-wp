<?php
/**
 * Title: Site Brand (header logo)
 * Slug: art4dev/site-brand
 * Inserter: no
 *
 * The logo lives in the theme (assets/logos/), not the media library, so it needs a
 * runtime-resolved URI — hardcoding the URL into a template part would break when Dan
 * migrates to SiteGround. Patterns are PHP, template parts are not, which is why the
 * brand mark is a pattern rather than inline markup in parts/header.html.
 *
 * @package art4dev
 */
?>
<!-- wp:html -->
<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Arts for Global Development — Home">
	<img class="site-logo__mark" src="<?php echo esc_url( get_theme_file_uri( 'assets/logos/logo-horizontal.svg' ) ); ?>" alt="Arts for Global Development" />
</a>
<!-- /wp:html -->
