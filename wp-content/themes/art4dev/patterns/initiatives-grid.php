<?php
/**
 * Title: Initiatives Grid
 * Slug: art4dev/initiatives-grid
 * Categories: art4dev
 * Description: All initiatives as cards — flagship first, then newest.
 * Inserter: yes
 *
 * Serves two places, which is why it's a pattern and not inline template markup:
 * archive-initiative.html (/initiatives/), and the Take Action → Initiatives page, where
 * schema §4 calls for "a query loop on that page".
 *
 * Ordering: is_flagship first, then year_started descending — the flagship (CreativeChange+,
 * 2005) would otherwise sink to the bottom on a date sort. A core query loop can't express
 * "meta flag first, then another meta key", hence the direct query.
 *
 * The card tag shows the initiative's program_pillar term, falling back to `status` — 2 of the
 * 5 initiatives have no pillar term assigned (Consulting and Design Lab have 0 initiatives),
 * so without the fallback those cards would show an empty tag.
 *
 * @package art4dev
 */

$a4gd_inits = new WP_Query(
	array(
		'post_type'      => 'initiative',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'meta_key'       => 'is_flagship',
		'orderby'        => array(
			'meta_value_num' => 'DESC', // flagship (1) before the rest (0)
			'date'           => 'DESC',
		),
	)
);

if ( ! $a4gd_inits->have_posts() ) {
	return;
}
?>
<!-- wp:group {"tagName":"section","className":"section","layout":{"type":"default"}} -->
<section class="wp-block-group section">
	<!-- wp:html -->
	<div class="container">
		<div class="init-grid">
			<?php
			while ( $a4gd_inits->have_posts() ) :
				$a4gd_inits->the_post();

				$a4gd_pillars = wp_get_post_terms( get_the_ID(), 'program_pillar', array( 'fields' => 'names' ) );
				$a4gd_tag     = $a4gd_pillars ? $a4gd_pillars[0] : get_field( 'status' );
				$a4gd_more    = get_field( 'learn_more_url' );
				?>
				<article class="init-card in-view">
					<div class="init-card__media">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?></a>
					</div>
					<?php if ( $a4gd_tag ) : ?>
						<span class="init-card__tag"><?php echo esc_html( ucfirst( $a4gd_tag ) ); ?></span>
					<?php endif; ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p><?php echo esc_html( get_field( 'tagline' ) ? get_field( 'tagline' ) : get_the_excerpt() ); ?></p>
					<?php if ( $a4gd_more ) : ?>
						<a href="<?php echo esc_url( $a4gd_more ); ?>" class="link-arrow" target="_blank" rel="noopener noreferrer">Learn more <span class="arrow" aria-hidden="true">→</span></a>
					<?php else : ?>
						<a href="<?php the_permalink(); ?>" class="link-arrow">Read more <span class="arrow" aria-hidden="true">→</span></a>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
<?php
wp_reset_postdata();
