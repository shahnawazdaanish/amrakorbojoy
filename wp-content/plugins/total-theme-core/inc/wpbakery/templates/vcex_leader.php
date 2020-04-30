<?php
/**
 * Visual Composer Leader
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
extract( vcex_vc_map_get_attributes( 'vcex_leader', $atts, $this ) );

$leaders = (array) vcex_vc_param_group_parse_atts( $leaders );

if ( ! $leaders ) {
	return;
}

$wrap_atrrs = array(
	'class' => array( 'vcex-module', 'vcex-leader', 'vcex-leader-' . $style, 'clr' ),
);

if ( 'true' == $responsive && vcex_is_layout_responsive() ) {
	$wrap_atrrs['class'][] = 'vcex-responsive';
}
if ( $el_class ) {
	$wrap_atrrs['class'][] = vcex_get_extra_class( $el_class );
}

if ( $responsive_data = vcex_get_module_responsive_data( $atts ) ) {
	$wrap_atrrs['data-wpex-rcss'] = $responsive_data;
}

$wrap_atrrs['style'] = vcex_inline_style( array(
	'color'     => $color,
	'font_size' => $font_size,
), false );

// Add filters to the module classes
$wrap_atrrs['class'] = vcex_parse_shortcode_classes( implode( ' ', $wrap_atrrs['class'] ), 'vcex_leader', $atts );

// Begin output
$output = '<ul' . vcex_parse_html_attributes( $wrap_atrrs ) . '>';

// Label typography
$label_typo = vcex_inline_style( array(
	'color'       => $label_color,
	'font_weight' => $label_font_weight,
	'font_style'  => $label_font_style,
	'font_family' => $label_font_family,
	'background'  => $background,
) );

if ( $label_font_family ) {
	vcex_enqueue_google_font( $label_font_family );
}

// value typography
$value_typo = vcex_inline_style( array(
	'color'       => $value_color,
	'font_weight' => $value_font_weight,
	'font_style'  => $value_font_style,
	'font_family' => $value_font_family,
	'background'  => $background,
) );

if ( $value_font_family ) {
	vcex_enqueue_google_font( $value_font_family );
}

// Individual item classes
$leader_classes = 'clr';
if ( $css_animation && 'none' != $css_animation ) {
	$leader_classes .= ' ' . vcex_get_css_animation( $css_animation );
}

// Loop through leaders and output it's content
foreach ( $leaders as $leader ) {

	$label = isset( $leader['label'] ) ? $leader['label'] : esc_html__( 'Label', 'total' );
	$value = isset( $leader['value'] ) ? $leader['value'] : esc_html__( 'Value', 'total' );

	$output .= '<li class="' . esc_attr( $leader_classes ) . '">';

		$output .= '<span class="vcex-first"' . $label_typo . '>' . do_shortcode( wp_kses_post( $label ) ) . '</span>';

		if ( $responsive && 'minimal' != $style ) {

			$output .= '<span class="vcex-inner">...</span>';

		}

		if ( 'Value' != $value ) {

			$output .= '<span class="vcex-last"' . $value_typo . '>' . do_shortcode( wp_kses_post( $value ) ) . '</span>';

		}

	$output .= '</li>';

}

$output .= '</ul>';

// @codingStandardsIgnoreLine
echo $output;
