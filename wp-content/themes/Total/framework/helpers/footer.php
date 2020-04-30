<?php
/**
 * Site Footer Helper Functions
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
 * Check if footer is enabled
 *
 * @since 4.0
 */
function wpex_has_footer() {

	// Return true by default
	$bool = true;

	// Disabled on landing page
	if ( is_page_template( 'templates/landing-page.php' ) ) {
		$bool = false;
	}

	// Get current post id
	$post_id = wpex_get_current_post_id();

	// Check page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_footer', true ) ) {
		if ( 'on' == $meta ) {
			$bool = false;
		} elseif ( 'enable' == $meta ) {
			$bool = true;
		}
	}

	// Apply filters and return bool
	return apply_filters( 'wpex_display_footer', $bool );

}

/**
 * Check if footer has widgets
 *
 * @since 4.0
 */
function wpex_footer_has_widgets() {
	if ( wpex_has_custom_footer() || ! empty( $_GET[ 'wpex_inline_footer_template_editor' ] ) ) {
		$bool = wpex_get_mod( 'footer_builder_footer_widgets', false ); //@todo make the option same value as Customizer?
	} else {
		$bool = get_theme_mod( 'footer_widgets', true );
	}
	$post_id = wpex_get_current_post_id();
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_footer_widgets', true ) ) {
		if ( 'on' == $meta ) {
			$bool = false;
		} elseif ( 'enable' == $meta ) {
			$bool = true;
		}
	}
	return apply_filters( 'wpex_display_footer_widgets', $bool );
}

/**
 * Returns footer class
 *
 * @since 4.9.8
 */
function wpex_footer_class() {
	$class = array( 'site-footer' );
	if ( wpex_get_mod( 'footer_bg_img' ) && $bg_style = get_theme_mod( 'footer_bg_img_style' ) ) {
		$class[] = 'bg-' . $bg_style;
	}
	return implode( ' ', apply_filters( 'wpex_footer_class', $class ) );
}

/**
 * Returns footer widgets class
 *
 * @since 4.9.8
 */
function wpex_footer_widgets_class() {
	$columns = wpex_get_mod( 'footer_widgets_columns', '4' );
	$gap     = wpex_get_mod( 'footer_widgets_gap', '30' );

	$class = array( 'wpex-row', 'clr' );

	if ( '1' == $columns ) {
		$class[] = 'single-col-footer';
	}
	if ( $gap ) {
		$class[] = 'gap-' . $gap;
	}
	$class = apply_filters( 'wpex_footer_widgets_class', $class ); // added in 4.9.8
	return apply_filters( 'wpex_footer_widget_row_classes', implode( ' ', $class ) ); // @todo deprecate filter
}

/**
 * Get footer builder ID
 *
 * @since 4.0
 */
function wpex_footer_builder_id() {
	if ( ! wpex_get_mod( 'footer_builder_enable', true ) ) {
		return;
	}
	$id = intval( apply_filters( 'wpex_footer_builder_page_id', wpex_get_mod( 'footer_builder_page_id' ) ) );
	if ( $id ) {
		$translated_id = wpex_parse_obj_id( $id, 'page' ); // translate
		$id = $translated_id ? $translated_id : $id; // if not translated return original ID
		if ( 'publish' == get_post_status( $id ) ) {
			return $id;
		}
	}
}

/**
 * Check if footer builder is enabled
 *
 * @since 4.6.5
 */
function wpex_has_custom_footer() {
	return wpex_footer_builder_id();
}

/**
 * Check if footer reveal is enabled
 *
 * @since 4.0
 */
function wpex_footer_has_reveal( $post_id = '' ) {

	// Disable here always
	if ( ! wpex_has_footer()
		|| 'boxed' == wpex_site_layout()
		|| 'six' == wpex_header_style()
		|| wpex_vc_is_inline()
	) {
		return false;
	}

	// Check customizer setting
	$bool = wpex_get_mod( 'footer_reveal', false );

	// Get current post id if not set
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_footer_reveal', true ) ) {
		if ( 'on' == $meta ) {
			$bool = true;
		} elseif ( 'off' == $meta ) {
			$bool = false;
		}
	}

	// Apply filters and return
	return apply_filters( 'wpex_has_footer_reveal', $bool );
}

/**
 * Check if footer bottom is enabled
 *
 * @since 4.1
 */
function wpex_has_footer_bottom( $post_id = '' ) {

	if ( wpex_elementor_location_exists( 'footer_bottom' ) ) {
		$bool = true;
	} elseif ( wpex_has_custom_footer() || ! empty( $_GET[ 'wpex_inline_footer_template_editor' ] ) ) {
		$bool = wpex_get_mod( 'footer_builder_footer_bottom', false ); //@todo rename to be same as default.
	} else {
		$bool = get_theme_mod( 'footer_bottom', true );
	}

	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_footer_bottom', true ) ) {
		if ( 'on' == $meta ) {
			$bool = true;
		} elseif ( 'off' == $meta ) {
			$bool = false;
		}
	}

	return apply_filters( 'wpex_has_footer_bottom', $bool );
}