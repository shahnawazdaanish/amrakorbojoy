<?php
/**
 * Custom Post type related posts
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_type = get_post_type();
$columns   = apply_filters( 'wpex_cpt_single_related_columns', 3 );

// Query args
$args = array(
	'post_type'      => $post_type,
	'posts_per_page' => $columns,
	'orderby'        => 'rand',
	'post__not_in'   => array( get_the_ID() ),
	'no_found_rows'  => true,
);

// Query items fom same category
if ( apply_filters( 'wpex_cpt_single_related_in_same_term', true ) && $tax = wpex_get_post_type_cat_tax() ) {
	$terms = wp_get_post_terms( get_the_ID(), $tax, array(
		'fields' => 'ids',
	) );
	if ( $terms ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => $tax,
				'field'    => 'term_id',
				'terms'    => $terms,
			)
		);
	}
}

// Apply filters to arguments for child theme editing.
$args = apply_filters( 'wpex_cpt_single_related_query_args', $args, $post_type );

// Related query arguments
$wpex_related_query = new wp_query( $args );

// If the custom query returns post display related posts section
if ( $wpex_related_query->have_posts() ) :

	// Check if embeds are enabled
	$show_embeds = apply_filters( 'wpex_cpt_single_related_embeds', false );

	// Check if excerpts are enabled
	$has_excerpts = apply_filters( 'wpex_cpt_single_related_excerpts', true );

	// Wrapper classes
	$classes = 'related-posts clr';
	if ( 'full-screen' == wpex_content_area_layout() ) {
		$classes .= ' container';
	} ?>

	<div class="<?php echo esc_attr( $classes ); ?>">

		<?php
		wpex_heading( array(
			'content'		=> esc_html__( 'Related Items', 'total' ),
			'classes'		=> array( 'related-posts-title' ),
			'apply_filters'	=> 'cpt_single_related',
		) ); ?>

		<div class="wpex-row clr">
			<?php
			// Loop through items
			$wpex_count = 0;
			foreach( $wpex_related_query->posts as $post ) : setup_postdata( $post );

				$wpex_count++;

				// Add classes
				$classes   = array( 'related-post', 'clr', 'nr-col' );
				$classes[] = wpex_grid_class( $columns );
				$classes[] = 'col-' . $wpex_count; ?>

				<article <?php post_class( $classes ); ?>>

					<?php
					// Display post video
					if ( $show_embeds && 'video' == $format && $video = wpex_get_post_video() ) : ?>

						<div class="related-post-video"><?php echo wpex_get_post_video_html( $video ); ?></div>

					<?php
					// Display post audio
					elseif ( $show_embeds && 'audio' == $format && $audio = wpex_get_post_audio() ) : ?>

						<div class="related-post-video"><?php echo wpex_get_post_audio_html( $audio ); ?></div>

					<?php
					// Display post thumbnail
					elseif ( has_post_thumbnail() && apply_filters( 'wpex_cpt_single_related_has_thumbnails', true ) ) :

						// Overlay style
						$overlay = apply_filters( 'wpex_cpt_single_related_overlay', 'none' ); ?>

						<figure class="related-post-figure clr <?php echo wpex_overlay_classes( $overlay ); ?>">
							<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" rel="bookmark" class="related-post-thumb<?php wpex_entry_image_animation_classes(); ?>">
								<?php echo wpex_get_post_thumbnail( array(
									'size' => $post_type . '_single_related',
								) ); ?>
								<?php wpex_entry_media_after( 'cpt_single_related' ); ?>
								<?php wpex_overlay( 'inside_link', $overlay ); ?>
							</a>
							<?php wpex_overlay( 'outside_link', $overlay ); ?>
						</figure>

					<?php endif; ?>

					<?php
					// Display post excerpt
					if ( $has_excerpts ) : ?>

						<div class="related-post-content clr">
							<h4 class="related-post-title entry-title">
								<a href="<?php wpex_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
							</h4><!-- .related-post-title -->
							<div class="related-post-excerpt clr">
								<?php wpex_excerpt( array(
									'length' => apply_filters( 'wpex_cpt_single_related_excerpt_length', '15' ),
								) ); ?>
							</div><!-- related-post-excerpt -->
						</div><!-- .related-post-content -->

					<?php endif; ?>

				</article><!-- .related-post -->

				<?php if ( $columns == $wpex_count ) $wpex_count=0; ?>
			<?php endforeach; ?>
		</div><!-- .wpex-row -->

	</div><!-- .related-posts -->

<?php endif; ?>

<?php wp_reset_postdata(); ?>