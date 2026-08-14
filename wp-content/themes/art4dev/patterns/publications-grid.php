<?php
/**
 * Title: Publications Grid — From the Field
 * Slug: art4dev/publications-grid
 * Categories: art4dev
 * Description: Homepage grid of publications flagged is_featured.
 *
 * Driven by the `is_featured` field rather than a hardcoded list, per schema §1.4 ("Show on
 * homepage 'Recent Publications' grid"). A core query loop can't filter on meta without a
 * pre_get_posts hook, so this queries directly.
 *
 * Note: only 2 of the 11 imported publications are flagged is_featured, so this renders 2
 * cards where the static prototype mocked up 3. That is the imported data, not a bug — flag a
 * third publication as featured and it appears here automatically.
 *
 * Publications with an external_url link out to the source; the rest link to their own page.
 *
 * @package art4dev
 */

$a4gd_featured = new WP_Query(
	array(
		'post_type'           => 'publication',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array(
			array(
				'key'   => 'is_featured',
				'value' => '1',
			),
		),
	)
);

if ( ! $a4gd_featured->have_posts() ) {
	return;
}
?>
<!-- wp:group {"tagName":"section","className":"section","layout":{"type":"default"}} -->
<section class="wp-block-group section">
	<!-- wp:html -->
	<div class="container">
		<div class="promises__head in-view">
			<div>
				<span class="eyebrow eyebrow--clay">Recent Publications &amp; Stories</span>
				<h2 style="margin-top:1.25rem;">From the field.</h2>
			</div>
			<a href="<?php echo esc_url( a4gd_page_url( 'library' ) ); ?>" class="link-arrow">View all <span class="arrow" aria-hidden="true">→</span></a>
		</div>

		<div class="pub-grid">
			<?php
			while ( $a4gd_featured->have_posts() ) :
				$a4gd_featured->the_post();

				$a4gd_url   = get_field( 'external_url' ) ? get_field( 'external_url' ) : get_permalink();
				$a4gd_ext   = (bool) get_field( 'external_url' );
				$a4gd_types = wp_get_post_terms( get_the_ID(), 'publication_type', array( 'fields' => 'names' ) );
				?>
				<a href="<?php echo esc_url( $a4gd_url ); ?>" class="pub-card in-view"<?php echo $a4gd_ext ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<div class="pub-card__media">
						<?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?>
					</div>
					<?php if ( $a4gd_types ) : ?>
						<span class="pub-card__tag"><?php echo esc_html( $a4gd_types[0] ); ?></span>
					<?php endif; ?>
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
			<?php endwhile; ?>
		</div>

		<div style="text-align:center;margin-top:3rem;">
			<a href="<?php echo esc_url( a4gd_page_url( 'library' ) ); ?>" class="btn btn--outline">All publications <span class="arrow" aria-hidden="true">→</span></a>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
<?php
wp_reset_postdata();
