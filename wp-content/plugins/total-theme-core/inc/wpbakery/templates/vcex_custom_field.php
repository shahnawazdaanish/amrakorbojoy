<?php
/**
 * Custom Field Shortcode
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Get shortcode attributes
extract( vcex_vc_map_get_attributes( 'vcex_custom_field', $atts, $this ) );

// name required
if ( empty( $name ) ) {
	return;
}

$cf_value = ( $cf_value = get_post_meta( vcex_get_the_ID(), $name, true ) ) ? $cf_value : $fallback;

if ( empty( $cf_value ) ) {
	return;
}

// Define classes
$classes = array( 'vcex-custom-field', 'clr' );
if ( $visibility ) {
	$classes[] = sanitize_html_class( $visibility );
}
if ( $css_animation = vcex_get_css_animation( $css_animation ) ) {
	$classes[] = $css_animation;
}
if ( $align ) {
	$classes[] = sanitize_html_class( 'text' . $align );
}
if ( $el_class = vcex_get_extra_class( $el_class ) ) {
	$classes[] = $el_class;
}
$classes = vcex_parse_shortcode_classes( implode( ' ', $classes ), 'vcex_custom_field', $atts );

$output = '';

// Wrap attributes
$wrap_attrs = array(
	'class' => esc_attr( $classes ),
);

// Shortcode style
$wrap_attrs['style'] = vcex_inline_style( array(
	'color'       => $color,
	'font_family' => $font_family,
	'font_size'   => $font_size,
), false );

// Get responsive data
if ( $responsive_data = vcex_get_module_responsive_data( $atts ) ) {
	$wrap_attrs['data-wpex-rcss'] = $responsive_data;
}

// Shortcode Output
$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	if ( $icon = vcex_get_icon_class( $atts, 'icon' ) ) {

		vcex_enqueue_icon_font( $icon_type, $icon );

		$output .= '<span class="' . esc_attr( $icon ) . '" aria-hidden="true"></span> ';

	}

	if ( $before ) {
		$output .= '<strong class="vcex-custom-field-before">' . esc_html( $before ) . '</strong> ';
	}

	$output .= apply_filters( 'vcex_custom_field_value_output', wp_kses_post( $cf_value ), $atts );

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;