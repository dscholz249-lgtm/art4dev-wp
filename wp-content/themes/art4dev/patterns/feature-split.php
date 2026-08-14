<?php
/**
 * Title: Feature Split — Make a Difference
 * Slug: art4dev/feature-split
 * Categories: art4dev
 * Description: Image beside an intro and a list of ways to engage.
 *
 * @package art4dev
 */
$a4gd_links = array(
	array( 'Become a creative partner', 'Collaborate &amp; Partner →', a4gd_page_url( 'invest-partner' ) ),
	array( 'Show your commitment',      'Speak Up &amp; Share →',      a4gd_page_url( 'artishake' ) ),
	array( 'Nurture the art power',     'Donate &amp; Support →',      a4gd_page_url( 'donate-sponsor' ) ),
	array( 'Join the team',             'Volunteer &amp; Intern →',    a4gd_page_url( 'volunteer-intern' ) ),
);
?>
<!-- wp:group {"tagName":"section","className":"feature-split section--cream section","layout":{"type":"default"}} -->
<section class="wp-block-group feature-split section--cream section">
	<!-- wp:html -->
	<div class="container">
		<div class="feature-split__grid">
			<div class="feature-split__image in-view">
				<img src="<?php echo esc_url( wp_get_attachment_image_url( 1, 'large' ) ); ?>" alt="A gallery opening with people in conversation, viewed from above." loading="lazy" />
			</div>
			<div class="in-view">
				<span class="eyebrow eyebrow--clay">Make a Difference</span>
				<h2 style="margin-top:1.5rem;">A creative force, for over two decades.</h2>
				<p class="lead">Since 2002 we have been facilitating the creative sector and stakeholders of development to collectively tackle socio-cultural, economic, and ecological challenges — and support underrepresented individuals and communities worldwide.</p>
				<ul class="feature-split__list">
					<?php foreach ( $a4gd_links as $a4gd_l ) : ?>
					<li>
						<a href="<?php echo esc_url( $a4gd_l[2] ); ?>">
							<span><?php echo esc_html( $a4gd_l[0] ); ?></span>
							<small><?php echo wp_kses_post( $a4gd_l[1] ); ?></small>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
