<?php
/**
 * Image Overlay: Title Bottom See Through
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

// Get post data
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title();

// Title is required
if ( ! $title ) {
	return;
}

// Define overlay content
$content = '<span class="title">' . esc_html( $title ) . '</span>'; ?>

<div class="overlay-title-bottom-see-through theme-overlay textcenter"><?php echo apply_filters( 'wpex_overlay_content_title-bottom-see-through', $content ); ?></div>