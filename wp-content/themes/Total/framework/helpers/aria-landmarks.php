<?php
/**
 * Helper function for adding aria landmarks
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.6.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_aria_landmark( $location ) {
	echo wpex_get_aria_landmark( $location );
}

function wpex_get_aria_landmark( $location ) {

	// Return if disabled
	if ( ! wpex_get_mod( 'aria_landmarks_enable', false ) ) {
		return;
	}

	$landmark = '';

	if ( $location == 'header' ) {
		$landmark = 'role="banner"';
	}

	elseif ( $location == 'skip_to_content' || $location == 'breadcrumbs' ) {
		$landmark = 'role="navigation"';
	}

	elseif ( $location == 'site_navigation' ) {
		$landmark = 'role="navigation"';
	}

	elseif ( $location == 'searchform' ) {
		$landmark = 'role="search"';
	}

	elseif ( $location == 'main' ) {
		$landmark = 'role="main"';
	}

	elseif ( $location == 'main' ) {
		$landmark = 'role="main"';
	}

	elseif ( $location == 'sidebar' ) {
		$landmark = 'role="complementary"';
	}

	elseif ( $location == 'copyright' ) {
		$landmark = 'role="contentinfo"';
	}

	elseif ( $location == 'footer_callout' ) {
		$landmark = 'role="navigation"';
	}

	elseif ( $location == 'footer_bottom_menu' ) {
		$landmark = 'role="navigation"';
	}

	elseif( $location == 'scroll_top' ) {
		$landmark = 'role="navigation"';
	}

	elseif( $location == 'mobile_menu_alt' ) {
		$landmark = 'role="navigation"';
	}

	$landmark = apply_filters( 'wpex_get_aria_landmark', $landmark, $location );

	if ( $landmark ) {
		return ' ' . $landmark;
	}

}