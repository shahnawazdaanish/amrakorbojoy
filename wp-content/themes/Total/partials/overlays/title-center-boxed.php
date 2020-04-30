<?php
/**
 * Image Overlay: Title Center Boxed
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
} ?>

<div class="overlay-title-center-boxed theme-overlay"><div class="overlay-table"><div class="overlay-table-cell"><span class="title"><?php echo apply_filters( 'wpex_overlay_content_title-center-boxed', esc_html( $title ) ); ?></span></div></div></div>