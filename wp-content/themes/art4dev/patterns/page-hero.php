<?php
/**
 * Title: Page Hero
 * Slug: art4dev/page-hero
 * Categories: art4dev
 * Description: Interior page hero — breadcrumbs, eyebrow, title and intro from ACF.
 * Inserter: yes
 *
 * Rebuilt to the static prototype's exact structure: a plain <section class="page-hero"> with
 * a <div class="container"> inside. The earlier version used a WP constrained-layout group,
 * whose `.is-layout-constrained > *` rule forces every child to max-width:680px with
 * margin:auto — which centered the H1/lede into a narrow indented column instead of the
 * prototype's full-width, left-aligned `.container`. Matching the prototype markup lets the
 * ported component CSS govern layout directly.
 *
 * Values come from ACF via get_field() rather than block bindings: the pattern is already PHP,
 * and rendering here means the markup matches the prototype exactly (bindings can only target a
 * paragraph/heading block, which re-introduces the constrained wrapper). hero_title is empty on
 * every imported page, so it falls back to the post title (schema §1.1).
 *
 * @package art4dev
 */

$a4gd_id      = get_the_ID();
$a4gd_eyebrow = get_field( 'hero_eyebrow', $a4gd_id );
$a4gd_title   = get_field( 'hero_title', $a4gd_id );
$a4gd_intro   = get_field( 'hero_intro', $a4gd_id, false ); // unformatted: it's a plain lede line
$a4gd_title   = $a4gd_title ? $a4gd_title : get_the_title( $a4gd_id );

// Breadcrumb trail from page ancestors (Home / Parent / … / current).
$a4gd_crumbs = array( array( 'Home', home_url( '/' ) ) );
if ( $a4gd_id ) {
	$a4gd_ancestors = array_reverse( get_post_ancestors( $a4gd_id ) );
	foreach ( $a4gd_ancestors as $a4gd_anc ) {
		$a4gd_crumbs[] = array( get_the_title( $a4gd_anc ), get_permalink( $a4gd_anc ) );
	}
	$a4gd_crumbs[] = array( get_the_title( $a4gd_id ), '' ); // current page: no link
}
?>
<!-- wp:html -->
<section class="page-hero">
	<div class="container">
		<nav class="page-hero__crumbs" aria-label="Breadcrumb">
			<?php
			$a4gd_last = count( $a4gd_crumbs ) - 1;
			foreach ( $a4gd_crumbs as $a4gd_i => $a4gd_crumb ) :
				list( $a4gd_label, $a4gd_url ) = $a4gd_crumb;
				if ( $a4gd_i > 0 ) {
					echo '<span aria-hidden="true">/</span>';
				}
				if ( $a4gd_url && $a4gd_i !== $a4gd_last ) {
					printf( '<a href="%s">%s</a>', esc_url( $a4gd_url ), esc_html( $a4gd_label ) );
				} else {
					echo '<span>' . esc_html( $a4gd_label ) . '</span>';
				}
			endforeach;
			?>
		</nav>

		<?php if ( $a4gd_eyebrow ) : ?>
			<span class="eyebrow eyebrow--clay reveal"><?php echo esc_html( $a4gd_eyebrow ); ?></span>
		<?php endif; ?>

		<h1 class="reveal" style="margin-top:1.5rem;"><?php echo esc_html( $a4gd_title ); ?></h1>

		<?php if ( $a4gd_intro ) : ?>
			<p class="page-hero__lede reveal"><?php echo esc_html( wp_strip_all_tags( $a4gd_intro ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<!-- /wp:html -->
