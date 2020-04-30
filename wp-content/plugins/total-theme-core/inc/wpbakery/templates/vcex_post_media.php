<?php
/**
 * Visual Composer Post Media
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
$atts = vcex_vc_map_get_attributes( 'vcex_post_media', $atts, $this );
extract( $atts );

// Get correct post ID
$post_id = vcex_get_the_ID();

// Thumbnail args
$thumbnail_args = array(
	'attachment' => get_post_thumbnail_id( $post_id ),
	'size'       => $img_size,
	'crop'       => $img_crop,
	'width'      => $img_width,
	'height'     => $img_height,
);

// Define wrap classes
$wrap_class = 'vcex-post-media clr';

if ( $css ) {
	$wrap_class .= ' ' . vcex_vc_shortcode_custom_css_class( $css );
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_class .= ' ' . vcex_get_css_animation( $css_animation );
}
if ( $classes ) {
	$wrap_class .= ' ' . vcex_get_extra_class( $classes );
}
if ( $visibility ) {
	$wrap_class .= ' ' . $visibility;
}

// Module output
$output = '<div class="' . esc_attr( $wrap_class ) . '">';

	if ( function_exists( 'wpex_get_post_media' ) ) {

		$output .= wpex_get_post_media( $post_id, array(
			'thumbnail_args' => $thumbnail_args,
			'lightbox'       => ( $lightbox == 'true' ) ? true : false,
		) );

	} else {

		$output .= get_the_post_thumbnail();

	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
