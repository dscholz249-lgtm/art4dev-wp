<?php
/**
 * Title: Pillars — four numbered editorial rows
 * Slug: art4dev/pillars
 * Categories: art4dev
 * Description: The four What We Do areas as numbered editorial rows with capability lists.
 * Inserter: yes
 *
 * Static copy, taken from the static prototype (pages/what-we-do.html), which is the visual
 * and editorial spec. The imported What We Do page carries the same four areas as plain prose
 * headings with one-line summaries; this pattern is the designed version an editor swaps in,
 * and it adds the capability lists the prose doesn't have.
 *
 * The titles double as the four `program_pillar` taxonomy terms and the four child pages of
 * What We Do — CTAs resolve by slug so they survive re-nesting.
 *
 * @package art4dev
 */

$a4gd_pillars = array(
	array(
		'num'   => 'i.',
		'title' => 'Consulting',
		'slug'  => 'consulting',
		'body'  => 'We partner with arts organizations, development agencies, academic institutions, and purpose-driven businesses to design and implement creative strategies that create meaningful, measurable impact.',
		'items' => array(
			'Arts integration into SDG-aligned programs and ESG strategies',
			'Community engagement and participatory program design',
			'Creative communications and storytelling for social change',
			'Arts-based research and evaluation frameworks',
		),
	),
	array(
		'num'   => 'ii.',
		'title' => 'Creative Placemaking &amp; Experience',
		'slug'  => 'placemaking',
		'body'  => 'We transform physical and virtual spaces into sites of inquiry, dialogue, and collective creation — from exhibitions and workshops to conference experiences and virtual safe places.',
		'items' => array(
			'Long-term and pop-up exhibitions on identity, climate, water, health',
			'Arts program design for corporate, nonprofit, and public spaces',
			'Interactive workshops for diverse audiences',
			'Gallery talks, panel discussions, community gatherings',
		),
	),
	array(
		'num'   => 'iii.',
		'title' => 'Creative Intel',
		'slug'  => 'creative-intel',
		'body'  => 'At our core, we are a learning organization. We research, experiment, curate, and share knowledge at the intersection of arts, culture, and development — building the evidence base that helps practitioners, policymakers, and educators make the case for creativity as a driver of change.',
		'items' => array(
			'Original research and published opinion',
			'Commissioned reports and interdisciplinary writing',
			'Curation of <em>art’ishake</em> e-magazine',
			'Speaking engagements and public lectures',
		),
	),
	array(
		'num'   => 'iv.',
		'title' => 'Design Lab',
		'slug'  => 'design-lab',
		'body'  => 'The Design Lab is where we prototype, make, and celebrate. It is the creative studio of Art4Development.Net — a space for designing tangible, arts-infused tools and products that educate, inspire, and generate impact.',
		'items' => array(
			'Lili’s Alphabet Book &amp; Game — bilingual literacy tool for children 4–12',
			'The Spoon Puppet Kit — eco-friendly arts &amp; crafts kit on soil health',
			'Upcycled Promotional Goods (with The Revival, Ghana)',
		),
	),
);
?>
<!-- wp:group {"tagName":"section","className":"section","layout":{"type":"default"}} -->
<section class="wp-block-group section">
	<!-- wp:html -->
	<div class="container">
		<div class="pillars">
			<?php foreach ( $a4gd_pillars as $a4gd_p ) : ?>
			<article class="pillar in-view">
				<div class="pillar__num"><?php echo esc_html( $a4gd_p['num'] ); ?></div>
				<h2 class="pillar__title"><?php echo wp_kses_post( $a4gd_p['title'] ); ?></h2>
				<div class="pillar__body">
					<p><?php echo wp_kses_post( $a4gd_p['body'] ); ?></p>
					<ul>
						<?php foreach ( $a4gd_p['items'] as $a4gd_item ) : ?>
						<li><?php echo wp_kses_post( $a4gd_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="pillar__cta">
					<a href="<?php echo esc_url( a4gd_page_url( $a4gd_p['slug'] ) ); ?>">Learn more <span class="arrow" aria-hidden="true">→</span></a>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
