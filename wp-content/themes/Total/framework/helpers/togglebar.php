<?php
/**
 * Togglebar functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get togglebar content ID
 *
 * @since 4.0
 */
function wpex_togglebar_content_id() {
	$id = apply_filters( 'wpex_toggle_bar_content_id', wpex_get_mod( 'toggle_bar_page', null ) );
	return $id ? wpex_parse_obj_id( intval( $id ) ) : null;
}

/**
 * Returns togglebar content
 *
 * @since 4.0
 */
function wpex_togglebar_content() {
	if ( $togglebar_id = wpex_togglebar_content_id() ) {
		return wpex_parse_vc_content( get_post_field( 'post_content', $togglebar_id ) );
	}
}

/**
 * Check if togglebar is enabled
 *
 * @since 4.0
 */
function wpex_has_togglebar( $post_id = '' ) {

	// Return false if toggle bar page is not defined
	if ( ! wpex_togglebar_content_id() && ! wpex_elementor_location_exists( 'togglebar' ) ) {
		return false;
	}

	// Check if enabled in Customizer
	$return = wpex_get_mod( 'toggle_bar', true );

	// Get post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check meta
	if ( $post_id ) {

		// Return true if enabled via the page settings
		if ( 'enable' == get_post_meta( $post_id, 'wpex_disable_toggle_bar', true ) ) {
			$return = true;
		}

		// Return false if disabled via the page settings
		if ( 'on' == get_post_meta( $post_id, 'wpex_disable_toggle_bar', true ) ) {
			$return = false;
		}

	}

	// Apply filters and return
	// @todo Rename to "wpex_has_togglebar" for consistancy
	return apply_filters( 'wpex_toggle_bar_active', $return );

}

/**
 * Get correct togglebar style
 *
 * @since 4.0
 */
function wpex_togglebar_style() {
	return apply_filters( 'wpex_togglebar_style', wpex_get_mod( 'toggle_bar_display', 'overlay' ) );
}

/**
 * Returns correct togglebar classes
 *
 * @since Total 1.0.0
 */
function wpex_togglebar_classes() {

	// Add default classes
	$classes = array();

	// Display
	$display = wpex_togglebar_style();
	$classes[] = 'toggle-bar-' . sanitize_html_class( $display );

	// Default state
	if ( 'visible' == wpex_get_mod( 'toggle_bar_default_state', 'hidden' ) ) {
		$classes[] = 'active-bar';
	} else {
		$classes[] = 'close-on-doc-click';
	}

	// Add animation classes
	if ( 'overlay' == $display && $animation = wpex_get_mod( 'toggle_bar_animation', 'fade' ) ) {
		$classes[] = 'toggle-bar-' . sanitize_html_class( $animation );
	}

	// Add visibility classes
	if ( $visibility = wpex_get_mod( 'toggle_bar_visibility', 'always-visible' ) ) {
		$classes[] = sanitize_html_class( $visibility );
	}

	// Remove padding
	if ( wpex_elementor_location_exists( 'togglebar' ) ) {
		$classes[] = 'toggle-bar-npad';
	}

	// Add clearfix
	$classes[] = 'wpex-clr';

	// Sanitize
	$classes = array_map( 'esc_attr', $classes );

	// Apply filters for child theming
	$classes = apply_filters_deprecated( 'wpex_toggle_bar_active', array( $classes ), '4.9', 'wpex_togglebar_classes' );
	$classes = apply_filters( 'wpex_togglebar_classes', $classes );

	// Turn classes into space seperated string
	$classes = is_array( $classes ) ? implode( ' ', $classes ) : $classes;

	// Return classes
	return $classes;

}