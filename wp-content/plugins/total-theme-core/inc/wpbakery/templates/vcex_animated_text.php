<?php
/**
 * Visual Composer Animated Text
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

// Get attributes
$atts = vcex_vc_map_get_attributes( 'vcex_animated_text', $atts, $this );

// Extract attributes
extract( $atts );

// Convert strings
$strings = (array) vcex_vc_param_group_parse_atts( $strings );

// Display shortcode
if ( ! $strings ) {
	return;
}

// Enqueue scripts
$this->enqueue_scripts();

$css_classes = 'vcex-typed-text-wrap clr vcex-module';
$data_attr = '';

$data = array();
foreach ( $strings as $string ) {
	if ( isset( $string['text'] ) ) {
		$data[] = esc_html( $string['text'] );
	}
}

$settings = array(
	'typeSpeed'  => $speed ? intval( $speed ) : '40',
	'loop'       => vcex_validate_boolean( $loop ),
	'showCursor' => vcex_validate_boolean( $type_cursor ),
	'backDelay'  => $back_delay ? intval( $back_delay ) : '0',
	'backSpeed'  => $back_speed ? intval( $back_speed ) : '0',
	'startDelay' => $start_delay ? intval( $start_delay ) : '0',
);

$inline_style = vcex_inline_style( array(
	'color'       => $color,
	'font_size'   => $font_size,
	'font_weight' => $font_weight,
	'font_style'  => $font_style,
	'font_family' => $font_family,
	'text_align'  => $text_align,
) );

if ( 'true' == $static_text ) {
	$typed_inline_style = vcex_inline_style( array(
		'color'           => $animated_color,
		'font_weight'     => $animated_font_weight,
		'font_style'      => $animated_font_style,
		'font_family'     => $animated_font_family,
		'text_decoration' => $animated_text_decoration,
		'width'           => $animated_span_width,
		'text_align'      => $animated_text_align,
	) );
} else {
	$typed_inline_style = null;
}

// Get responsive data
if ( $responsive_data = vcex_get_module_responsive_data( $atts ) ) {
	$data_attr .= ' ' . $responsive_data;
}

// Add classes and apply filters to comply with VC standards
if ( $el_class = vcex_get_extra_class( $el_class ) ) {
	$css_classes .= ' ' . $el_class;
}
if ( $visibility ) {
	$css_classes .= ' ' . $visibility;
}
if ( $css_animation && 'none' != $css_animation ) {
	$css_classes .= ' ' . vcex_get_css_animation( $css_animation );
}
if ( $css ) {
	$css_classes .= ' ' . vcex_vc_shortcode_custom_css_class( $css );
}

$css_classes = vcex_parse_shortcode_classes( $css_classes, 'vcex_animated_text', $atts );

$tag_escaped = $tag ? esc_attr( $tag ) : 'div';

// Output Shortcode
$output = '<' . $tag_escaped . ' class="' . esc_attr( $css_classes ) . '"' . $inline_style . $data_attr . '>';

	if ( 'true' == $static_text && $static_before ) {
		$output .= '<span class="vcex-before">' . do_shortcode( wp_kses_post( $static_before ) ) . '</span> ';
	}

	if ( ( $animated_css || $typed_inline_style ) ) {
		$animated_css = $animated_css ? ' ' . vcex_vc_shortcode_custom_css_class( $animated_css ) : '';
		$output .= '<span class="vcex-typed-text-css' . $animated_css . '"' . $typed_inline_style . '>';
	}

	$tmp_data = array();
	foreach ( $data as $val ) {
		$tmp_data[] = do_shortcode( $val );
	}
	$data = $tmp_data;

	$output .= '<span class="screen-reader-text">';

		foreach ( $data as $string ) {
			$output .= '<span>' . esc_html( do_shortcode( $string ) ) . '</span>';
		}

	$output .= '</span>';

	$output .= '<span class="vcex-ph"></span>'; // Add empty span 1px wide to prevent bouce

	$output .= '<span class="vcex-typed-text" data-settings="' . htmlspecialchars( wp_json_encode( $settings ) ) . '" data-strings="' . htmlspecialchars( wp_json_encode( $data ) ) . '"></span>';

	if ( ( $animated_css || $typed_inline_style ) ) {
		$output .= '</span>';
	}

	if ( 'true' == $static_text && $static_after ) {
		$output .= ' <span class="vcex-after">' . do_shortcode( wp_kses_post( $static_after ) ) . '</span>';
	}

$output .= '</' . $tag_escaped . '>';

// @codingStandardsIgnoreLine
echo $output;

