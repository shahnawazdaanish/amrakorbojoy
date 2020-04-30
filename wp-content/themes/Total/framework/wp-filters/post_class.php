<?php
/**
 * Adds custom classes to the posts class
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_post_class( $classes, $class = '', $post_id = '' ) {

	if ( ! $post_id ) {
		return $classes;
	}

	// Get post type
	$type = get_post_type( $post_id );

	// Not needed here
	if ( 'forum' == $type || 'topic' == $type ) {
		return $classes;
	}

	// Add entry class
	$classes[] = 'entry';

	// Conditional to check for gallery output
	$check_gallery = ( 'post' == $type && wpex_get_mod( 'blog_entry_gallery_output', true ) ) ? true : false;

	// Add media class
	if ( wpex_post_has_media( $post_id, $check_gallery ) ) {
		$classes[] = 'has-media';
	} else {
		$classes[] = 'no-media';
	}

	// Custom link class
	if ( wpex_get_post_redirect_link( $post_id ) ) {
		$classes[] = 'has-redirect';
	}

	// Sticky
	if ( is_sticky( $post_id ) ) {
		$classes[] = 'sticky';
	}

	// Return classes
	return $classes;
}
add_filter( 'post_class', 'wpex_post_class', 10, 3 );