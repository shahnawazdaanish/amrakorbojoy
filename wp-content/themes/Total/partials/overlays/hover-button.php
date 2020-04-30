<?php
/**
 * Hover Button Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for outside_link position
if ( 'outside_link' != $position ) {
	return;
}

// Define vars
$link = $target = $text = '';

// Lightbox
$lightbox_link  = ! empty( $args['lightbox_link'] ) ? $args['lightbox_link'] : '';
$lightbox_data  = ! empty( $args['lightbox_data'] ) ? $args['lightbox_data'] : '';
$lightbox_data  = ( is_array( $lightbox_data ) ) ? ' ' . implode( ' ', $lightbox_data ) : $lightbox_data;
$lightbox_class = ! empty( $args['lightbox_class'] ) ? $args['lightbox_class'] : 'wpex-lightbox';

if ( 'wpex-lightbox-group-item' == $lightbox_class ) {
	$lightbox_class = 'wpex-lightbox';
}

// Link
if ( ! $lightbox_link ) {

	// Post link
	$link = isset( $args['post_permalink'] ) ? $args['post_permalink'] : wpex_get_permalink();

	// Target
	if ( isset( $args['link_target'] ) && ( 'blank' == $args['link_target'] || '_blank' == $args['link_target'] ) ) {
		$target = 'blank';
	} else {
		$target = '';
	}

} else {
	$link = $lightbox_link;
}

// Custom link
$link = ! empty( $args['overlay_link'] ) ? $args['overlay_link'] : $link;

// Text
$text = ! empty( $args['overlay_button_text'] ) ? $args['overlay_button_text'] : esc_html__( 'View Post', 'total' );
$text = ( 'post_title' == $text ) ? get_the_title() : $text;

// Link classes
$link_classes = 'overlay-hover-button-link theme-button minimal-border white';
if ( $lightbox_link ) {
	$link_classes .= ' '. $lightbox_class;
}

// Apply filters
$link   = apply_filters( 'wpex_hover_button_overlay_link', $link );
$target = apply_filters( 'wpex_button_overlay_target', $target );
$text   = apply_filters( 'wpex_hover_button_overlay_text', $text );

// Santize
$text         = esc_attr( $text );
$link         = esc_url( $link );
$link_classes = esc_attr( $link_classes ); ?>

<div class="overlay-hover-button overlay-hide theme-overlay textcenter">

	<div class="overlay-hover-button-inner overlay-table clr">

		<div class="overlay-hover-button-text overlay-table-cell clr">

			<?php
			$link_attrs = array(
				'href'   => esc_url( $link ),
				'class'  => esc_attr( $link_classes ),
				'target' => $target,
				'data'   => $lightbox_data,
			); ?>

			<a <?php echo wpex_parse_attrs( $link_attrs ); ?>><?php echo do_shortcode( wp_kses_post( $text ) ); ?></a>

		</div>

	</div>

</div>