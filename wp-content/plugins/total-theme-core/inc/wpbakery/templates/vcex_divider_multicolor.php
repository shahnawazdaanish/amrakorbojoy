<?php
/**
 * Visual Composer Divider MultiColor
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.9
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
$atts = vcex_vc_map_get_attributes( 'vcex_divider_multicolor', $atts, $this );

$colors = (array) vcex_vc_param_group_parse_atts( $atts['colors'] );

if ( ! $colors ) {
	return;
}

$count = count( $colors );

// Define default wrap attributes
$wrap_attrs = array(
	'class' => 'vcex-module vcex-divider-multicolor clr',
);

if ( $atts['el_class'] ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_extra_class( $atts['el_class'] );
}

if ( $atts['visibility'] ) {
	$wrap_attrs['class'] .= ' ' . $atts['visibility'];
}

if ( $atts['align'] && 'center' != $atts['align'] ) {
	$wrap_attrs['class'] .= ' float-' . $atts['align'];
}

$wrap_attrs['style'] = vcex_inline_style( array(
	'width'         => ( $atts['width'] && '100%' != $atts['width'] ) ? $atts['width'] : '',
	'margin_bottom' => $atts['margin_bottom'],
), false );

$wrap_attrs['class'] = esc_attr( vcex_parse_shortcode_classes( $wrap_attrs['class'] ) );

// Output
$output = '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	foreach ( $colors as $color ) {

		$inline_style = vcex_inline_style( array(
			'background' => isset( $color['value'] ) ? $color['value'] : '',
			'width'      => ( 100 / $count ) . '%',
			'height'     => ( $atts['height'] && '8px' !== $atts['height'] ) ? intval( $atts['height'] ) : '',
		), true );

		$output .= '<span' . $inline_style . '></span>';

	}

$output .= '</div>';

if ( $atts['align'] && 'center' != $atts['align'] ) {
	$output .= '<div style="clear:both;"></div>'; // Clear floats
}

// @codingStandardsIgnoreLine
echo $output;
