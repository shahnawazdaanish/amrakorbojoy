<?php
/**
 * Sanitization functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.6
 */

/**
 * Check if an attachment exists.
 *
 * @since 4.4.1
 */
function wpex_attachment_exists( $attachment = '' ) {
	if ( 'attachment' == get_post_type( $attachment ) ) {
		return $attachment;
	}
}

/**
 * Applies correct functions to the content to render p tags and shortcodes,
 * much like the_content filter but without causing conflicts with 3rd party plugins.
 *
 * @since 4.1
 */
function wpex_the_content( $raw_string = '', $context = '' ) {
	if ( ! $raw_string ) {
		return;
	}
	return apply_filters( 'wpex_the_content', $raw_string, $context );
}

/**
 * Echo escaped post title
 *
 * @since 2.0.0
 */
function wpex_esc_title( $post = '' ) {
	echo wpex_get_esc_title( $post );
}

/**
 * Return escaped post title
 *
 * @since 1.5.4
 */
function wpex_get_esc_title( $post = '' ) {
	return the_title_attribute( array(
		'echo' => false,
		'post' => $post,
	) );
}

/**
 * Escape attribute with fallback
 *
 * @since 3.3.5
 */
function wpex_esc_attr( $val = null, $fallback = null ) {
	if ( $val = esc_attr( $val ) ) {
		return $val;
	}
	return $fallback;
}

/**
 * Escape html with fallback
 *
 * @since 3.3.5
 */
function wpex_esc_html( $val = null, $fallback = null ) {
	if ( $val = esc_html( $val ) ) {
		return $val;
	}
	return $fallback;
}

/**
 * Sanitize numbers with fallback
 *
 * @since 3.3.5
 */
function wpex_intval( $val = null, $fallback = null ) {
	if ( 0 == $val ) {
		return 0; // Some settings may need this
	} elseif ( $val = intval( $val ) ) {
		return $val;
	} else {
		return $fallback;
	}
}

/**
 * Sanitize numbers with fallback
 *
 * @since 4.3
 */
function wpex_sanitize_checkbox( $input ) {
	return ! empty( $input ) ? true : false;
}

/**
 * Sanitize Font Family
 *
 * @since 4.4
 */
function wpex_sanitize_font_family( $font_family ) {
	if ( 'system-ui' == $font_family ) {
		$font_family = wpex_get_system_ui_font_stack();
	}
	$font_family = str_replace( "``", "'", $font_family ); // Fix issue with fonts saved in shortcodes
	$font_family = wpex_get_font_family_stack( $font_family );
	return wp_specialchars_decode( $font_family );
}

/**
 * Sanitize Font Size Mod
 *
 * @since 4.9.5
 */
function wpex_sanitize_font_size_mod( $input ) {
	// Sanitize array values
	if ( is_array( $input ) ) {
		return array_map( 'wpex_sanitize_font_size', $input );
	}
	// Convert jSON to array and sanitize each value while doing so
	if ( strpos( $input, '{' ) !== false ) {
		$input = json_decode( $input, true );
		return array_map( 'wpex_sanitize_font_size', $input );
	}
	return wpex_sanitize_font_size( $input ); // Most likely a string.
}

/**
 * Sanitize Font Size
 *
 * @since 4.3
 */
function wpex_sanitize_font_size( $input ) {
	if ( strpos( $input, 'px' ) || strpos( $input, 'em' ) || strpos( $input, 'vw' ) || strpos( $input, 'vmin' ) || strpos( $input, 'vmax' ) ) {
		$input = esc_html( $input );
	} else {
		$input = absint( $input ) . 'px';
	}
	if ( $input != '0px' && $input != '0em' ) {
		return esc_html( $input );
	}
	return '';
}

/**
 * Sanitize Input to return px or em value
 *
 * @since 4.3
 */
function wpex_sanitize_letter_spacing( $input ) {
	if ( strpos( $input, 'px' ) || strpos( $input, 'em' ) || strpos( $input, 'vmin' ) || strpos( $input, 'vmax' ) ) {
		$input = esc_attr( $input );
	} else {
		$input = absint( $input ) . 'px';
	}
	return $input;
}

/**
 * Sanitize line-height
 *
 * @since 4.3
 */
function wpex_sanitize_line_height( $input ) {
	return esc_html( $input );
}

/**
 * Sanitize customizer select
 *
 * @since 4.3
 */
function wpex_sanitize_customizer_select( $input, $setting ) {

	// Input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
	$input = sanitize_key( $input );

	// Get the list of possible select options
	$choices = $setting->manager->get_control( $setting->id )->choices;

	// Return input if valid or return default option
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

}

/**
 * Sanitize customizer columns control
 *
 * @since 4.9.9
 */
function wpex_sanitize_customizer_columns( $input, $setting ) {

	if ( is_numeric( $input ) ) {
		return absint( $input );
	}

	$input = (array) $input;

	return array_map( 'absint', $input );

}

/**
 * Parse visual composer content to remove extra p tags
 *
 * @since 4.9
 */
function wpex_parse_vc_content( $shortcode ) {
	$shortcode = str_replace( '<p>[vc_row', '[vc_row', $shortcode );
	$shortcode = str_replace( '[/vc_row]</p>', '[/vc_row]', $shortcode );
	return $shortcode;
}

/**
 * Clean up shortcodes
 *
 * @since 4.9.6
 */
function wpex_clean_up_shortcodes( $content = '' ) {
	if ( $content ) {
		return strtr( $content, array (
			'<p>['    => '[',
			']</p>'   => ']',
			']<br />' => ']'
		) );
	}
}