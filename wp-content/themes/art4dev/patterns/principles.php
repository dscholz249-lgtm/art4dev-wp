<?php
/**
 * Title: Principles — People / Planet / Performance
 * Slug: art4dev/principles
 * Categories: art4dev
 * Description: The three commitments as a card row.
 * Inserter: yes
 *
 * Copy from the static prototype (pages/our-promise.html). The imported Our Promise page
 * carries the same three commitments as plain headings; this is the designed version.
 *
 * The roman numerals are decorative counters, not content — hence aria-hidden, so a screen
 * reader hears "People", not "i People".
 *
 * @package art4dev
 */

$a4gd_principles = array(
	array(
		'icon'  => 'i',
		'title' => 'People',
		'body'  => 'We center children, youth, and women — particularly those from underrepresented and underserved communities — in everything we do. We believe in the dignity of all voices, the value of lived experience, and the power of co-creation. Mutual respect, active listening, and authenticity are not aspirations for us; they are how we work.',
	),
	array(
		'icon'  => 'ii',
		'title' => 'Planet',
		'body'  => 'We are committed to ‘one health’: the understanding that human well-being and ecological health are inseparable. We actively promote eco-arts and nature-positive creative practices as tools for building environmental consciousness and driving sustainability at every scale.',
	),
	array(
		'icon'  => 'iii',
		'title' => 'Performance &amp; Prosperity',
		'body'  => 'We are creative social entrepreneurs. We value the process of change as much as the outcomes it produces. By integrating arts into development strategies, we cultivate the curiosity, empathy, and collaborative spirit that make lasting transformation possible — and we build pathways to economic well-being for artists and communities along the way.',
	),
);
?>
<!-- wp:group {"tagName":"section","className":"section section--cream","layout":{"type":"default"}} -->
<section class="wp-block-group section section--cream">
	<!-- wp:html -->
	<div class="container">
		<div class="principles">
			<?php foreach ( $a4gd_principles as $a4gd_p ) : ?>
			<div class="principle in-view">
				<span class="principle__icon" aria-hidden="true"><?php echo esc_html( $a4gd_p['icon'] ); ?></span>
				<h3><?php echo wp_kses_post( $a4gd_p['title'] ); ?></h3>
				<p><?php echo wp_kses_post( $a4gd_p['body'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
