<?php
/**
 * Title Excerpt Hover Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
}

// Get excerpt length
$excerpt_length = isset( $args['overlay_excerpt_length'] ) ? $args['overlay_excerpt_length'] : 15;

// Get title
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title(); ?>

<div class="overlay-title-excerpt-hover overlay-hide theme-overlay textcenter">
	<div class="overlay-table clr">
		<div class="overlay-table-cell clr">
			<div class="overlay-title"><?php echo esc_html( $title ); ?></div>
			<?php
			if ( isset( $args['overlay_excerpt'] ) ) {
				echo wp_kses_post( $args['overlay_excerpt'] );
			} else {
				wpex_excerpt( array(
					'length'               => $excerpt_length,
					'trim_custom_excerpts' => apply_filters( 'wpex_title_excerpt_hover_overlay_trim_custom_excerpts', true ), // trims custom excerpts
					'before'               => '<div class="overlay-excerpt">',
					'after'                => '</div>',
					'context'              => 'overlay_title_excerpt_hover',
				) );
			} ?>
		</div>
	</div>
</div>