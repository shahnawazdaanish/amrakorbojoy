<?php
/**
 * Visual Composer Spacing
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.9
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
extract( vcex_vc_map_get_attributes( 'vcex_spacing', $atts, $this ) );

// Core class
$classes = 'vcex-spacing';

// Custom Class
if ( $class ) {
    $classes .= ' ' . vcex_get_extra_class( $class );
}

// Visiblity Class
if ( $visibility ) {
    $classes .= ' ' . $visibility;
}

// Front-end composer class
if ( vcex_vc_is_inline() ) {
    $classes .= ' vc-spacing-shortcode';
}

// Apply filters
$classes = vcex_parse_shortcode_classes( $classes, 'vcex_spacing', $atts );

// Sanitize size - supports %, em, vh and px
if ( ( strpos( $size, '%' ) !== false )
	|| ( strpos( $size, 'em' ) !== false )
	|| ( strpos( $size, 'vh' ) !== false )
) {
	$size = wp_strip_all_tags( $size );
} elseif ( $size = floatval( $size ) ) {
	$size = wp_strip_all_tags( $size ) . 'px';
}

// Echo output
echo '<div class="' . esc_attr( $classes ) . '" style="height:' . esc_attr( $size ) . '"></div>';