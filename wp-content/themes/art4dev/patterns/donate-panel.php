<?php
/**
 * Title: Donate Panel
 * Slug: art4dev/donate-panel
 * Categories: art4dev
 * Description: Compact donate prompt.
 *
 * The PayPal/Venmo/card buttons deliberately link through to the Donate & Sponsor page rather
 * than embedding a processor: which processor Art4Dev uses is still open (schema §9), and
 * re-embedding it is Dan's post-migration task.
 *
 * @package art4dev
 */
$a4gd_donate = a4gd_page_url( 'donate-sponsor' );
?>
<!-- wp:group {"tagName":"section","className":"section--cream","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<section class="wp-block-group section--cream" style="padding-top:5rem;padding-bottom:5rem">
	<!-- wp:html -->
	<div class="container">
		<div class="donate-panel">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/logos/logo-mark.svg' ) ); ?>" alt="" class="donate-panel__mark" />
			<span class="eyebrow eyebrow--center eyebrow--clay" style="display:block;text-align:center;">Donate to</span>
			<h3>Arts for Global Development, Inc.</h3>
			<p>Support programs that apply the arts and culture to challenge and address socio-ecological inequities.</p>
			<div class="donate-panel__btns">
				<a href="<?php echo esc_url( $a4gd_donate ); ?>" class="donate-panel__btn-pp">PayPal</a>
				<a href="<?php echo esc_url( $a4gd_donate ); ?>" class="donate-panel__btn-venmo">Venmo</a>
				<a href="<?php echo esc_url( $a4gd_donate ); ?>" class="btn btn--outline" style="margin-top:0.5rem;">Donate with debit or credit card</a>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
