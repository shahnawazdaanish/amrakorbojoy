<?php
/**
 * Alter form fields when clicking edit on various modules
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

/**
 * Parses icon parameter to make sure the icon & icon_type is set properly
 */
function vcex_parse_icon_param( $atts, $icon_param = 'icon', $icon_type_param = 'icon_type' ) {
	$icon = ! empty( $atts[$icon_param] ) ? $atts[$icon_param] : '';
	if ( $icon && empty( $atts[$icon_type_param] ) ) {
		$get_icon_type = vcex_get_icon_type_from_class( $icon );
		$atts[$icon_type_param] = ( 'ticons' == $get_icon_type ) ? '' : $get_icon_type;
		if ( 'fontawesome' == $get_icon_type ) {
			$atts[$icon_param . '_fontawesome'] = $icon;
		} elseif ( 'ticons' == $get_icon_type ) {
			$atts[$icon_param] = str_replace( 'fa fa-', 'ticon ticon-', $icon );
		} elseif ( ! $get_icon_type ) {
			$atts[$icon_param] = vcex_add_default_icon_prefix( $icon );
		}
	}
	return $atts;
}

/**
 * Sets the default image size to "full" if it's set to custom but img height and width are empty.
 *
 * @deprecated 1.1
 */
function vcex_parse_image_size( $atts ) {
	$img_size = ( isset( $atts['img_size'] ) && 'wpex_custom' == $atts['img_size'] ) ? 'wpex_custom' : '';
	$img_size = empty( $atts['img_size'] ) ? 'wpex_custom' : '';
	if ( 'wpex_custom' == $img_size && empty( $atts['img_height'] ) && empty( $atts['img_width'] ) ) {
		$atts['img_size'] = 'full';
	}
	return $atts;
}

/**
 * Combines multiple top/right/bottom/left fields.
 */
function vcex_combine_trbl_fields( $top = '', $right = '', $bottom = '', $left = '' ) {

	$margins = array();

	if ( $top ) {
		$margins['top'] = 'top:' . $top;
	}

	if ( $right ) {
		$margins['right'] = 'right:' . $right;
	}

	if ( $bottom ) {
		$margins['bottom'] = 'bottom:' . $bottom;
	}

	if ( $left ) {
		$margins['left'] = 'left:' . $left;
	}

	if ( $margins ) {
		return implode( '|', $margins );
	}

}

/**
 * Parses old content CSS params
 */
function vcex_parse_deprecated_grid_entry_content_css( $atts ) {

	// Disable border
	$content_border = ! empty( $atts['content_border'] ) ? $atts['content_border'] : '';
	if ( '0px' == $content_border || 'none' == $content_border ) {
		$atts['content_border'] = 'false';
	}

	// Parse css
	if ( empty( $atts['content_css'] ) ) {

		// Define css var
		$css = '';

		// Background Color - No Image
		$bg = ! empty( $atts['content_background'] ) ? $atts['content_background'] : '';
		if ( $bg ) {
			$css .= 'background-color: ' . $bg . ';';
		}

		// Border
		$border = ! empty( $atts['content_border'] ) ? $atts['content_border'] : '';
		if ( $border ) {
			if ( '0px' == $border || 'none' == $border ) {
				$css .= 'border: 0px none rgba(255,255,255,0.01);'; // reset border
			} else {
				$css .= 'border: ' . $border . ';';
			}
		}

		// Padding
		$padding = ! empty( $atts['content_padding'] ) ? $atts['content_padding'] : '';
		if ( $padding ) {
			$css .= 'padding: ' . $padding . ';';
		}

		// Margin
		$margin = ! empty( $atts['content_margin'] ) ? $atts['content_margin'] : '';
		if ( $margin ) {
			$css .= 'margin: ' . $margin . ';';
		}

		// Update css var
		if ( $css ) {
			$css = '.temp{' . $css . '}';
		}

		// Add css to attributes
		$atts['content_css'] = $css;

		// Unset old vars
		unset( $atts['content_background'] );
		unset( $atts['content_padding'] );
		unset( $atts['content_margin'] );
		unset( $atts['content_border'] );

	}

	// Return $atts
	return $atts;

}