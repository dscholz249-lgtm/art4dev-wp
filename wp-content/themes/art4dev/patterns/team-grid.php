<?php
/**
 * Title: Team Grid — grouped by role
 * Slug: art4dev/team-grid
 * Categories: art4dev
 * Description: Team members grouped by team_role, ordered by sort_order within each group.
 * Inserter: yes
 *
 * This is a pattern, not archive-team_member.html, because team_member is registered
 * has_archive=false (06-setup-checklist.md §4) — /team/ 404s, so an archive template would
 * never render. Schema §4 is explicit that team members "feed the Our Team page via a query
 * loop grouped by team_role", which is what this is. Drop it into the Our Team page.
 *
 * Two things a core query loop can't do, hence the direct queries:
 *   - group by taxonomy term (one loop per role)
 *   - order by sort_order within each group (schema §5 spaces these by 10s)
 *
 * Role order is term_id ASC — i.e. WXR import order, which puts Board of Directors before
 * Advisory Board per schema §5. The default alphabetical sort inverts that, and hardcoding
 * the names would break the moment the client renames a role.
 *
 * @package art4dev
 */

$a4gd_roles = get_terms(
	array(
		'taxonomy'   => 'team_role',
		'hide_empty' => true,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	)
);

if ( empty( $a4gd_roles ) || is_wp_error( $a4gd_roles ) ) {
	return;
}
?>
<!-- wp:group {"tagName":"section","className":"section","layout":{"type":"default"}} -->
<section class="wp-block-group section">
	<!-- wp:html -->
	<div class="container">
		<?php foreach ( $a4gd_roles as $a4gd_role ) : ?>
			<?php
			$a4gd_members = new WP_Query(
				array(
					'post_type'      => 'team_member',
					'posts_per_page' => -1,
					'no_found_rows'  => true,
					'meta_key'       => 'sort_order',
					'orderby'        => 'meta_value_num',
					'order'          => 'ASC',
					'tax_query'      => array(
						array(
							'taxonomy' => 'team_role',
							'field'    => 'term_id',
							'terms'    => $a4gd_role->term_id,
						),
					),
				)
			);

			if ( ! $a4gd_members->have_posts() ) {
				continue;
			}
			?>
			<h2 class="eyebrow eyebrow--clay" style="margin-bottom:2rem;"><?php echo esc_html( $a4gd_role->name ); ?></h2>
			<div class="team-grid" style="margin-bottom:5rem;">
				<?php
				while ( $a4gd_members->have_posts() ) :
					$a4gd_members->the_post();

					// full_name is the display name; the post title is the same string, but the
					// field is what schema §1.3 designates for display.
					$a4gd_name = get_field( 'full_name' ) ? get_field( 'full_name' ) : get_the_title();
					$a4gd_bio  = get_field( 'short_bio' );
					?>
					<article class="team-card in-view">
						<div class="role"><?php echo esc_html( get_field( 'role_title' ) ); ?></div>
						<h3><?php echo esc_html( $a4gd_name ); ?></h3>
						<?php if ( $a4gd_bio ) : ?>
							<p><?php echo esc_html( $a4gd_bio ); ?></p>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endforeach; ?>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
