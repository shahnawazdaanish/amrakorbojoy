<?php
/**
 * Visual Composer Bullets
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Content required
if ( empty( $content ) ) {
	return;
}

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_shortcode', $atts, $this );

// Define classes
$classes = 'vcex-shortcode clr';
if ( $atts['visibility'] ) {
	$classes .= ' ' . $atts['visibility'];
}
if ( $css_animation = vcex_get_css_animation( $atts['css_animation'] ) ) {
	$classes .= ' ' . $css_animation;
}
if ( $el_class = vcex_get_extra_class( $atts['el_class'] ) ) {
	$classes .= ' ' . $el_class;
}
$classes = vcex_parse_shortcode_classes( $classes, 'vcex_shortcode', $atts );

// Echo shortcode
echo '<div class="' . esc_attr( $classes ) . '">' . do_shortcode( wp_kses_post( $content ) ) . '</div>';