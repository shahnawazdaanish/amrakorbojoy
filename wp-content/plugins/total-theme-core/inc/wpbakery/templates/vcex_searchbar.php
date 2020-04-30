<?php
/**
 * Visual Composer Searchbar
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
extract( vcex_vc_map_get_attributes( 'vcex_searchbar', $atts, $this ) );

// Define output var
$output = '';

// Sanitize
$placeholder = $placeholder ? $placeholder : esc_html__( 'Keywords...', 'total' );
$button_text = $button_text ? $button_text : esc_html__( 'Search', 'total' );

// Autofocus
$autofocus = 'true' == $autofocus ? 'autofocus' : '';

// Wrap Classes
$wrap_classes = array( 'vcex-module', 'vcex-searchbar clr' );
if ( 'true' == $fullwidth_mobile ) {
	$wrap_classes[] = 'vcex-fullwidth-mobile';
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}

// Form classes
$input_classes = 'vcex-searchbar-input';
$input_classes .= ' '. vcex_vc_shortcode_custom_css_class( $css );

// Wrap style
$wrap_style = vcex_inline_style( array(
	'width' => $wrap_width,
	'float' => $wrap_float,
) );

// Input style
$input_style = vcex_inline_style( array(
	'color'          => $input_color,
	'font_size'      => $input_font_size,
	'text_transform' => $input_text_transform,
	'letter_spacing' => $input_letter_spacing,
	'font_weight'    => $input_font_weight,
) );

// Implode classes and apply filters
$wrap_classes = implode( ' ', $wrap_classes );
$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_searchbar', $atts );

// Begin output
$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . $wrap_style . '>';

	$output .= '<form method="get" class="vcex-searchbar-form" action="'. esc_url( home_url( '/' ) ) .'"'. $input_style .'>';

		$output .= '<input type="search" class="' . $input_classes . '" name="s" placeholder="' . $placeholder . '"' . vcex_inline_style( array( 'width' => $input_width ) ) . $autofocus . ' />';

		if ( $advanced_query ) :

			// Sanitize
			$advanced_query = trim( $advanced_query );
			$advanced_query = html_entity_decode( $advanced_query );

			// Convert to array
			$advanced_query = parse_str( $advanced_query, $advanced_query_array );

			// If array is valid loop through params
			if ( $advanced_query_array ) :

				foreach( $advanced_query_array as $key => $val ) :

					$output .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';

				endforeach;

			endif;

		endif;

		/*
		 * Button
		 */
		$button_attrs = array(
			'class' => 'vcex-searchbar-button',
		);

		// Button hover data
		$hover_data = array();
		if ( $button_bg_hover ) {
			$hover_data['background'] = esc_attr( $button_bg_hover );
		}
		if ( $button_color_hover ) {
			$hover_data['color'] = esc_attr( $button_color_hover );
		}
		if ( $hover_data ) {
			$button_attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( $hover_data ) );
		}

		// Button style
		$button_attrs['style'] = vcex_inline_style( array(
			'width'          => $button_width,
			'background'     => $button_bg,
			'color'          => $button_color,
			'font_size'      => $button_font_size,
			'text_transform' => $button_text_transform,
			'letter_spacing' => $button_letter_spacing,
			'font_weight'    => $button_font_weight,
			'border_radius'  => $button_border_radius,
		), false );

		$output .= '<button' . vcex_parse_html_attributes( $button_attrs ) . '>';

			$output .= do_shortcode( wp_kses_post( str_replace( '``', '"', $button_text ) ) );

		$output .= '</button>';

	$output .= '</form>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
