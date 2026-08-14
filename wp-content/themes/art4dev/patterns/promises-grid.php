<?php
/**
 * Title: Promises Grid
 * Slug: art4dev/promises-grid
 * Categories: art4dev
 * Description: Three numbered engagement cards.
 *
 * @package art4dev
 */
$a4gd_cards = array(
	array( 'i.',   'Explore what we do.',  'Consulting, placemaking, research, and design programs that put arts and culture to work.', 'What we do', a4gd_page_url( 'what-we-do' ) ),
	array( 'ii.',  'Take action.',         'Join an initiative, attend an event, or contribute to our global learning community.',      'Take action', a4gd_page_url( 'take-action' ) ),
	array( 'iii.', 'Champion the work.',   'Partner with us, donate, or volunteer your time and expertise.',                            'Champion',    a4gd_page_url( 'champion' ) ),
);
?>
<!-- wp:group {"tagName":"section","className":"promises section","layout":{"type":"default"}} -->
<section class="wp-block-group promises section">
	<!-- wp:html -->
	<div class="container">
		<div class="promises__head in-view">
			<h2>Three ways to engage with the work.</h2>
			<p class="lead" style="margin:0;">From program design to community action to lasting partnership — find the door that fits.</p>
		</div>
		<div class="promises__grid">
			<?php foreach ( $a4gd_cards as $a4gd_c ) : ?>
			<a href="<?php echo esc_url( $a4gd_c[4] ); ?>" class="promise-card in-view">
				<div>
					<span class="promise-card__num"><?php echo esc_html( $a4gd_c[0] ); ?></span>
					<h3><?php echo esc_html( $a4gd_c[1] ); ?></h3>
					<p><?php echo esc_html( $a4gd_c[2] ); ?></p>
				</div>
				<span class="promise-card__arrow"><?php echo esc_html( $a4gd_c[3] ); ?> <span class="arrow" aria-hidden="true">→</span></span>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
