<?php
/**
 * Image Overlay: Slide Up Title Black Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
}

if ( 'staff' == get_post_type() ) {
	$content = get_post_meta( get_the_ID(), 'wpex_staff_position', true );
} else {
	$content = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title();
}

// Define overlay content
$content = '<span class="title">' . wp_kses_post( $content ) . '</span>'; ?>

<div class="overlay-slideup-title overlay-hide black clr theme-overlay"><?php echo apply_filters( 'wpex_overlay_content_slideup-title-black', $content ); ?></div>