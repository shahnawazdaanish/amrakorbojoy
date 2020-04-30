<?php
/**
 * Visual Composer Bullets
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

// Return if no content
if ( empty( $content ) ) {
	return;
}

// Define output
$output = '';

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_bullets', $atts, $this );

// Check if icon is enabled
$has_icon = isset( $atts['has_icon'] ) && 'true' == $atts['has_icon'] ? true : false;

// Define wrap attributes
$wrap_attrs = array(
	'id'   => vcex_get_unique_id( $atts['unique_id'] ),
	'data' => '',
);

// Wrap classes
$wrap_classes = 'vcex-module vcex-bullets';
if ( $atts['css_animation'] && 'none' != $atts['css_animation'] ) {
	$wrap_classes .= ' ' . vcex_get_css_animation( $atts['css_animation'] );
}

if ( $has_icon ) {

	// Pre-defined bullet styles
	if ( $atts['style'] && ! $atts['icon_type'] ) {
		$wrap_classes .= ' ' . sanitize_html_class( 'vcex-bullets-' . $atts['style'] );
	}

	// Custom Icon
	elseif ( $icon = vcex_get_icon_class( $atts, 'icon' )  ) {
		vcex_enqueue_icon_font( $atts['icon_type'], $icon );
		$icon_style = vcex_inline_style( array(
			'color' => $atts['icon_color']
		) );
		$add_icon = '<div class="vcex-bullets-ci-wrap"><span class="vcex-icon-wrap"><span class="vcex-icon '. $icon .'" '. $icon_style .' aria-hidden="true"></span></span><div class="vcex-content">';
		$content = str_replace( '<li>', '<li>' . $add_icon, $content );
		$content = str_replace( '<li style="text-align: center;">', '<li style="text-align: center;">' . $add_icon, $content ); // Alot of users seem to center things.
		$content = str_replace( '</li>', '</div></div></li>', $content );
		$wrap_classes .= ' custom-icon';
	}

} else {
	$wrap_classes .= ' vcex-bullets-ni';
}

// Wrap Style
$wrap_attrs['style'] = vcex_inline_style( array(
	'color'          => $atts[ 'color' ],
	'font_family'    => $atts[ 'font_family' ],
	'font_size'      => $atts[ 'font_size' ],
	'letter_spacing' => $atts[ 'letter_spacing' ],
	'font_weight'    => $atts[ 'font_weight' ],
	'line_height'    => $atts[ 'line_height' ],
) );

// Load custom font
if ( $atts['font_family'] ) {
	vcex_enqueue_google_font( $atts['font_family'] );
}

// Responsive settings
if ( $responsive_data = vcex_get_module_responsive_data( $atts['font_size'], 'font_size' ) ) {
	$wrap_attrs['data-wpex-rcss'] = $responsive_data;
}

// Add custom classes last
if ( $atts['classes'] ) {
	$wrap_classes .= ' ' . $atts['classes'];
}

// Add filters to wrap classes and add to attributes
$wrap_attrs['class'] = esc_attr( vcex_parse_shortcode_classes( $wrap_classes, 'vcex_bullets', $atts ) );

// Output
$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	$output .= do_shortcode( wp_kses_post( $content ) );

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
