<?php
/**
 * Visual Composer Divider
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Get and extract shortcode attributes
extract( vcex_vc_map_get_attributes( 'vcex_divider', $atts, $this ) );

// Define output var
$output = '';

// Sanitize data
$icon          = vcex_get_icon_class( $atts, 'icon' );
$height        = $height ? vcex_validate_px( $height, 'px' ) : '';
$icon_padding  = ( $icon_height || $icon_width ) ? '' : $icon_padding;
$dotted_height = ( 'dotted' == $style ) ? $dotted_height : '';
$dotted_height = ( $icon ) ? '' : $dotted_height;

// Wrapper classes
$wrap_classes = array( 'vcex-module', 'vcex-divider' );
if ( $style ) {
	$wrap_classes[] = 'vcex-divider-'. $style;
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}
if ( $align ) {
	$wrap_classes[] = 'vcex-divider-' . $align;
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $icon_height ) {
	$wrap_classes[] = 'vcex-divider-custom-icon-height';
}
if ( $icon_width ) {
	$wrap_classes[] = 'vcex-divider-custom-icon-width';
}
if ( $icon_bg ) {
	$wrap_classes[] = 'vcex-divider-icon-has-bg';
}
if ( 'dotted' == $style & ! $icon ) {
	$wrap_classes[] = 'repeat-bg';
}

// If icon exists
if ( $icon ) {

	// Add special class to wrapper
	$wrap_classes[] = 'vcex-divider-w-icon';

	// Load icon font family
	vcex_enqueue_icon_font( $icon_type, $icon );

	// Icon style
	$icon_style = vcex_inline_style( array(
		'font_size'     => $icon_size,
		'border_radius' => $icon_border_radius,
		'color'         => $icon_color,
		'background'    => $icon_bg,
		'padding'       => $icon_padding,
		'height'        => $icon_height,
		'line_height'   => vcex_validate_px( $icon_height, 'px' ),
		'width'         => $icon_width,
	) );

	// Border style
	$vcex_inner_border_style = '';

	if ( 'dotted' != $style ) {

		$border_top_margin = '';

		if ( $int_height = intval( $height ) ) {

			if ( 'double' == $style ) {
				$border_top_margin = ( ( $int_height * 2 ) + 4 ) / 2;
			} else {
				$border_top_margin = $int_height / 2;
			}
			$border_top_margin = intval( $border_top_margin );
			$border_top_margin = $border_top_margin ? - $border_top_margin : '';

		}

		$vcex_inner_border_style = vcex_inline_style( array(
			'border_color'        => $color,
			'border_bottom_width' => $height,
			'border_top_width'    => ( 'double' == $style ) ? $height : '',
			'margin_top'          => $border_top_margin,
		) );

	}

	// Reset vars if icon is defined so styles aren't duplicated in main wrapper
	$height = $color = '';

}

// Main style
$botttom_height = ( 'double' == $style ) ? $height : '';
$wrap_style = vcex_inline_style( array(
	'height'              => $dotted_height,
	'width'               => $width,
	'margin'              => $margin,
	'border_top_width'    => $height,
	'border_bottom_width' => $botttom_height,
	'border_color'        => $color,
) );

// Add custom classes last
if ( $el_class ) {
	$wrap_classes[] = vcex_get_extra_class( $el_class );
}

// Turn wrap classes into a string
$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_divider', $atts );

// Open divider wrapper
$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . $wrap_style . '>';

	// Display icon if defined
	if ( $icon ) {

		$output .= '<div class="vcex-divider-icon">';

			// Icon before span
			if ( 'dotted' != $style ) {
				$output .= '<span class="vcex-divider-icon-before"' . $vcex_inner_border_style . '></span>';
			}

			// Icon output
			$output .= '<span class="vcex-icon-wrap"' . $icon_style . '>';

				$output .= '<span class="' . esc_attr( $icon ) . '"></span>';

			$output .= '</span>';

			// Icon after span
			if ( 'dotted' != $style ) {
				$output .= '<span class="vcex-divider-icon-after"' . $vcex_inner_border_style . '></span>';
			}

		// Close icon wrap
		$output .= '</div>';

	}

// Close main wrapper
$output .= '</div>';

// Clear floats if needed
if ( 'left' == $align || 'right' == $align ) {
	$output .= '<div class="wpex-clear"></div>';
}

// @codingStandardsIgnoreLine
echo $output;
