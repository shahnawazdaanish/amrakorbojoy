<?php
/**
 * Site topbar functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Social

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if topbar is enabled
 *
 * @since 4.0
 */
function wpex_has_topbar( $post_id = '' ) {

	// Check if enabled by default
	if ( wpex_elementor_location_exists( 'topbar' ) ) {
		$bool = true;
	} else {
		$bool = get_theme_mod( 'top_bar', true );
	}

	// Get current post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check meta
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_top_bar', true ) ) {

		// Return false if disabled via post meta
		if ( 'on' == $meta ) {
			$bool = false;
		}

		// Return true if enabled via post meta
		elseif ( 'enable' == $meta ) {
			$bool = true;
		}

	}

	// Apply filers and return bool value
	return apply_filters( 'wpex_is_top_bar_enabled', $bool );

}

/**
 * Topbar style
 *
 * @since 2.0.0
 */
function wpex_topbar_style() {
	$style = wpex_get_mod( 'top_bar_style' );
	$style = $style ? $style : 'one';
	return apply_filters( 'wpex_top_bar_style', $style );
}

/**
 * Topbar style
 *
 * @since 2.0.0
 */
function wpex_topbar_classes() {
	$classes = 'clr';
	if ( wpex_get_mod( 'top_bar_sticky' ) && ! wpex_vc_is_inline() ) {
		$classes .= ' wpex-top-bar-sticky';
	}
	if ( ! wpex_get_mod( 'top_bar_bottom_border', true ) ) {
		$classes .= ' no-border';
	}
	if ( $visibility = wpex_get_mod( 'top_bar_visibility' ) ) {
		$classes .= ' ' . $visibility;
	}
	if ( 'full-width' == wpex_site_layout() && wpex_get_mod( 'top_bar_fullwidth' ) ) {
		$classes .= ' wpex-full-width';
	}
	if ( 'three' == wpex_get_mod( 'top_bar_style', 'one' ) ) {
		$classes .= ' textcenter';
	}
	return apply_filters( 'wpex_get_topbar_classes', $classes );
}

/**
 * Get topbar aside content
 *
 * @since 4.0
 */
function wpex_topbar_content( $post_id = '' ) {

	// Get topbar content from Customizer
	$content = wpex_get_translated_theme_mod( 'top_bar_content', '<span class="wpex-inline">[font_awesome icon="phone"] 1-800-987-654</span><span class="wpex-inline">[font_awesome icon="envelope"] admin@totalwptheme.com</span><span class="wpex-inline">[font_awesome icon="user"][wp_login_url text="User Login" logout_text="Logout"]</span>' );

	// Apply filters before converting to text
	$content = apply_filters( 'wpex_top_bar_content', $content );

	// Check if content is a page ID and get page content
	if ( is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, 'page' );
		$post    = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = wpex_parse_vc_content( $post->post_content );
		}
	}

	// Apply filters and return content
	return $content;

}

/**
 * Topbar content classes
 *
 * @since 2.0.0
 */
function wpex_topbar_content_classes() {

	// Define classes
	$classes = array( 'wpex-clr' );

	// Check for content
	if ( wpex_topbar_content() ) {
		$classes[] = 'has-content';
	}

	// Get topbar style
	$style = wpex_topbar_style();

	// Add classes based on top bar style only if social is enabled
	if ( 'one' == $style ) {
		$classes[] = 'top-bar-left';
	} elseif ( 'two' == $style ) {
		$classes[] = 'top-bar-right';
	} elseif ( 'three' == $style ) {
		$classes[] = 'top-bar-centered';
	}

	// Apply filters for child theming
	$classes = apply_filters( 'wpex_top_bar_classes', $classes );

	// Turn classes array into space seperated string
	$classes = implode( ' ', $classes );

	// Return classes
	return $classes;

}

/*-------------------------------------------------------------------------------*/
/* [ Social ]
/*-------------------------------------------------------------------------------*/

/**
 * Get topbar aside content
 *
 * @since 4.0
 */
function wpex_topbar_social_alt_content( $post_id = '' ) {

	// Check customizer setting
	$content = wpex_get_translated_theme_mod( 'top_bar_social_alt' );

	// Content can't be empty
	$content = trim( $content );

	// Check if social_alt is a page ID and get page content
	if ( is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, 'page' );
		$post    = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = trim( $post->post_content );
		}
	}

	// Return content
	return apply_filters( 'wpex_topbar_social_alt_content', $content );

}