<?php
/**
 * Column Side Border Output
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

// Let's show something custom in the front-end editor
if ( vcex_vc_is_inline() ) {
	echo '<div class="vcex-frontend-inline-placeholder">' . __( 'Column Side Border Placeholder', 'total-theme-code' ) . '</div>';
	return;
}

// Get and extract shortcode attributes
extract( vcex_vc_map_get_attributes( 'vcex_column_side_border', $atts, $this ) );

// Core class
$classes = 'vcex-column-side-border';

// Custom Class
if ( $class ) {
    $classes .= ' ' . vcex_get_extra_class( $class );
}

// Set position class
$position = $position ? $position : 'right';
$classes .= ' vcex-' . sanitize_html_class( $position );

// Visiblity Class
if ( $visibility ) {
    $classes .= ' ' . sanitize_html_class( $visibility );
}

// Apply filters to the classnames
$classes = vcex_parse_shortcode_classes( $classes, 'vcex_column_side_border', $atts );

// Inline style
$style = '';

if ( $height ) {
	$style .= 'height:' . wp_strip_all_tags( $height ) .';';
}

if ( $width ) {
	$style .= 'width:' . absint( $width ) .'px;';

	if ( 'right' == $position ) {
		$style .= 'right:-' . absint( $width ) / 2 . 'px;';
	}

	if ( 'left' == $position ) {
		$style .= 'left:-' . absint( $width ) / 2 . 'px;';
	}

}

if ( $background_color ) {
	$style .= 'background-color:' . wp_strip_all_tags( $background_color ) .';';
}

if ( $style ) {
	$style = ' style="' . esc_attr( $style ) . '"';
}

// Echo output
echo '<div class="' . esc_attr( $classes ) . '"' . $style . '></div>';