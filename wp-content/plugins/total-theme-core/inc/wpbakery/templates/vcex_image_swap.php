<?php
/**
 * Visual Composer Image Swap
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Fallbacks (old atts)
$link_title  = isset( $atts['link_title'] ) ? $atts['link_title'] : '';
$link_target = isset( $atts['link_target'] ) ? $atts['link_target'] : '';

// Get and extract shortcode attributes
extract( vcex_vc_map_get_attributes( 'vcex_image_swap', $atts, $this ) );

// Output var
$output = '';

// Primary and secondary imags required
if ( ! $primary_image || ! $secondary_image ) {
	return;
}

// Add styles
$wrapper_inline_style = vcex_inline_style( array(
	'width' => $container_width,
) );
$image_style = vcex_inline_style( array(
	'border_radius' => $border_radius,
), false );

// Add classes
$wrap_classes = array( 'vcex-module', 'vcex-image-swap', 'clr' );
if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}
$wrap_classes = implode( ' ', $wrap_classes );
$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_image_swap', $atts );

if ( $css ) {
	$output .='<div class="' . vcex_vc_shortcode_custom_css_class( $css ) . '">';
}

$output .='<figure class="' . $wrap_classes . '"' . $wrapper_inline_style . vcex_get_unique_id( $unique_id ) . '>';

	// Get link data
	$link_data = vcex_build_link( $link );

	// Output link
	if ( ! empty( $link_data['url'] ) ) {

		// Define link attributes
		$link_attrs = array(
			'href'  => '',
			'class' => 'vcex-image-swap-link',
		);

		// Link classes
		if ( in_array( $link_tooltip, array( 'yes', 'true' ) ) ) {
			$link_attrs['class'] .= ' tooltip-up';
		}

		// Link attributes
		$link_attrs['href']   = isset( $link_data['url'] ) ? esc_url( $link_data['url'] ) : $link;
		$link_attrs['title']  = isset( $link_data['title'] ) ? esc_attr( $link_data['title'] ) : '';
		$link_attrs['rel']    = isset( $link_data['rel'] ) ? $link_data['rel'] : '';
		$link_attrs['target'] = isset( $link_data['target'] ) ? $link_data['target'] : '';

		$output .='<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

	}

	// Primary image
	$output .= vcex_get_post_thumbnail( array(
		'attachment' => $primary_image,
		'size'       => $img_size,
		'crop'       => $img_crop,
		'width'      => $img_width,
		'height'     => $img_height,
		'class'      => 'vcex-image-swap-primary',
		'style'      => $image_style,
	) );

	// Secondary image
	$output .= vcex_get_post_thumbnail( array(
		'attachment' => $secondary_image,
		'size'       => $img_size,
		'crop'       => $img_crop,
		'width'      => $img_width,
		'height'     => $img_height,
		'class'      => 'vcex-image-swap-secondary',
		'style'      => $image_style,
	) );

	// Close link wrapper
	if ( ! empty( $link_data['url'] ) ) {
		$output .='</a>';
	}

$output .='</figure>'; // Close main wrap

// Close CSS wrapper
if ( $css ) {
	$output .='</div>';
}

// @codingStandardsIgnoreLine
echo $output;
