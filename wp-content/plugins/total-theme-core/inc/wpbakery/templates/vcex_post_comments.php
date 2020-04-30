<?php
/**
 * Visual Composer Comments
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

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_comments', $atts, $this );

// Define wrapper attributes
$wrap_attrs = array(
	'class' => 'vcex-comments clr',
);

// Extra classname
if ( $atts['el_class'] ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_extra_class( $atts['el_class'] );
}

// Visibility
if ( $atts['visibility'] ) {
	$wrap_attrs['class'] .= ' ' . esc_attr( $atts['visibility'] );
}

// Show/hide heading
if ( empty( $atts['show_heading'] ) || 'false' == $atts['show_heading'] ) {
	$wrap_attrs['class'] .= ' vcex-comments-hide-heading';
}

$wrap_attrs['class'] = esc_attr( vcex_parse_shortcode_classes( $wrap_attrs['class'] ) );

echo '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	echo comments_template();

echo '</div>';