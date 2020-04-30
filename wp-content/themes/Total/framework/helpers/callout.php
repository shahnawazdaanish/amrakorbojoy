<?php
/**
 * Callout functions
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
 * Check if callout is enabled
 *
 * @since 4.0
 */
function wpex_has_callout( $post_id = '' ) {

	// Check if enabled by default
	if ( wpex_elementor_location_exists( 'footer_callout' ) ) {
		$bool = true;
	} else {
		$bool = get_theme_mod( 'callout', true );
	}

	// Get current post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Apply filters before meta check so meta always overrides filters
	$bool = apply_filters( 'wpex_callout_enabled', $bool );

	// Check page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_footer_callout', true ) ) {
		if ( 'on' == $meta ) {
			$bool = false;
		} elseif ( 'enable' == $meta ) {
			$bool = true;
		}
	}

	// Return bool value
	return $bool;

}

/**
 * Returns callout content
 *
 * @since 4.0
 */
function wpex_callout_content( $post_id = '' ) {

	// Default content var
	$content = '';

	// Get post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Get post meta content
	$meta = $post_id ? get_post_meta( $post_id, 'wpex_callout_text', true ) : '';

	// Return content defined in meta
	if ( $meta
		&& '<p><br data-mce-bogus="1"></p>' != $meta
		&& '<p>&nbsp;<br></p>' != $meta
	) {
		$content = $meta;
	}

	// Return Customzier content
	else {

		// Get content from theme mod
		$content = wpex_get_translated_theme_mod( 'callout_text', 'I am the footer call-to-action block, here you can add some relevant/important information about your company or product. I can be disabled in the theme options.' );

		// Apply filters if meta is not defined since meta should always override
		$content = apply_filters( 'wpex_get_footer_callout_content', $content );

	}

	// If page content is numeric and it's a post return the post content
	if ( $content && is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, get_post_type( $content ) );
		$post    = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = wpex_parse_vc_content( $post->post_content );
		}
	}

	// Return content
	return $content;

}