<?php
/**
 * Add meta viewport tag to header
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_get_meta_viewport() {

	$viewport = '';

	// Responsive viewport viewport
	if ( wpex_is_layout_responsive() ) {
		$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';
	}

	// Non responsive meta viewport
	else {
		$width = wpex_get_mod( 'main_container_width', '980' );
		if ( $width && false == strpos( $width, '%' ) ) {
			$width = $width ? intval( $width ) : '980';
			if ( 'boxed' == wpex_site_layout() ) {
				$outer_margin  = intval( wpex_get_mod( 'boxed_padding', 30 ) );
				$inner_padding = 30;
				$width = $width + ( $inner_padding * 2 ) + ( $outer_margin * 2 ); // Add inner + outer padding
			}
			$viewport = '<meta name="viewport" content="width=' . absint( apply_filters( 'wpex_viewport_width', $width ) ) . '">';
		} else {
			$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';
		}
	}

	// Apply filters to the meta viewport for child theme tweaking
	$viewport = apply_filters( 'wpex_meta_viewport', $viewport );

	// Return viewport
	if ( $viewport ) {
		return $viewport;
	}

}

function wpex_meta_viewport() {
	echo wpex_get_meta_viewport();
	echo "\r\n";
}
add_action( 'wp_head', 'wpex_meta_viewport', 1 );