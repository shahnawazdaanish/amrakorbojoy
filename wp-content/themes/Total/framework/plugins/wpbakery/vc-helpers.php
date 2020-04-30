<?php
/**
 * Visual Composer helper functions
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.9.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns row overlay span.
 */
function vcex_row_overlay( $atts ) {

	$overlay = isset( $atts['wpex_bg_overlay'] ) ? $atts['wpex_bg_overlay'] : '';

	if ( $overlay && 'none' != $overlay ) {

		$style = '';

		if ( 'custom' == $overlay && ! empty( $atts['wpex_bg_overlay_image'] ) ) {
			if ( $custom_img = wp_get_attachment_url( $atts['wpex_bg_overlay_image'] ) ) {
				$style .= 'background-image:url(' . esc_url( $custom_img ) . ');';
			}
		}

		if ( ! empty( $atts['wpex_bg_overlay_color'] ) ) {
			$style .= 'background-color:' . $atts['wpex_bg_overlay_color'] . ';';
		}

		if ( ! empty( $atts['wpex_bg_overlay_opacity'] ) ) {
			$style .= 'opacity:' . $atts['wpex_bg_overlay_opacity'] . ';';
		}

		$overlay = wpex_parse_html( 'span', array(
			'class'      => 'wpex-bg-overlay ' . $overlay,
			'style'      => $style,
			'data-style' => $style,
		) );

		return '<div class="wpex-bg-overlay-wrap"> ' . $overlay . '</div>';

	}

}

/**
 * Check if advanced parallax is enabled.
 */
function vcex_supports_advanced_parallax() {
	return apply_filters( 'vcex_supports_advanced_parallax', true );
}

/**
 * Check if shape dividers is supported
 */
function vcex_supports_shape_dividers() {
	return apply_filters( 'vcex_supports_shape_dividers', true );
}

/**
 * Adds inner row margin to compensate for the VC negative margins.
 */
function vcex_offset_vc( $atts ) {

	// No offset added here
	if ( ! empty( $atts['full_width'] ) || ! empty( $atts['max_width'] ) ) {
		return;
	}

	// Get column spacing
	$spacing = ! empty( $atts['column_spacing'] ) ? $atts['column_spacing'] : '30';

	// Return if spacing set to 0px
	if ( '0px' == $spacing ) {
		return;
	}

	// Define offset class
	$classes = 'wpex-offset-vc-' . $spacing/2;

	// Parallax check
	if ( vcex_supports_advanced_parallax() ) {
		if ( ! empty( $atts['vcex_parallax'] ) && ! empty( $atts['parallax_image'] ) ) {
			return $classes;
		}
	}

	// Self hosted video
	if ( 'self_hosted' == $atts['video_bg'] && ! empty( $atts['video_bg_mp4'] ) ) {
		return $classes;
	}

	// Youtube videos
	if ( 'youtube' == $atts['video_bg'] && ! empty( $atts['video_bg_url'] ) ) {
		return $classes;
	}

	// Overlays
	$overlay = isset( $atts['wpex_bg_overlay'] ) ? $atts['wpex_bg_overlay'] : '';
	if ( $overlay ) {
		return $classes;
	}

	// Check for custom CSS
	if ( ! empty( $atts['css'] ) ) {
		if ( strpos( $atts['css'], 'background' )
			|| strpos( $atts['css'], 'border' )
		) {
			return $classes;
		}
	} elseif ( ! empty( $atts['center_row'] )
		|| ! empty( $atts['bg_image'] )
		|| ! empty( $atts['bg_color'] )
		|| ! empty( $atts['border_width'] )
	) {
		return $classes;
	}

}


/**
 * Returns video row background.
 */
if ( ! function_exists( 'vcex_row_video' ) ) {
	function vcex_row_video( $atts ) {

		// Define output
		$output = '';

		// Return if disabled
		if ( empty( $atts['wpex_self_hosted_video_bg'] ) ) {
			return;
		}

		// Make sure at least one video is defined
		if ( empty( $atts['video_bg_webm'] ) && empty( $atts['video_bg_ogv'] ) && empty( $atts['video_bg_mp4'] ) ) {
			return;
		}

		// Check sound
		$sound = apply_filters( 'vcex_self_hosted_row_video_sound', false );
		$sound = $sound ? '' : ' muted volume="0"';

		// Center video?
		$center = ( isset( $atts['video_bg_center'] ) && 'true' == $atts['video_bg_center'] ) ? ' wpex-video-bg-center' : '';

		// Video output
		$output .= '<div class="wpex-video-bg-wrap">';

			$output .= '<video aria-hidden="true" class="wpex-video-bg' . $center . '" preload="auto" autoplay="true" loop="loop"' . $sound . '>';

				if ( ! empty( $atts['video_bg_webm'] ) ) {
					$output .= '<source src="' . esc_url( $atts['video_bg_webm'] ) . '" type="video/webm" />';
				}

				if ( ! empty( $atts['video_bg_ogv'] ) ) {
					$output .= '<source src="' . esc_url( $atts['video_bg_ogv'] ) . '" type="video/ogg ogv" />';
				}

				if ( ! empty( $atts['video_bg_mp4'] ) ) {
					$output .= '<source src="' . esc_url( $atts['video_bg_mp4'] ) . '" type="video/mp4" />';
				}

			$output .= '</video>';

		$output .= '</div>';

		// Video overlay fallack
		// @deprecated in 3.6.0
		if ( ! empty( $atts['video_bg_overlay'] ) && 'none' != $atts['video_bg_overlay'] ) {

			$output .= '<span class="wpex-video-bg-overlay ' . esc_attr( $atts['video_bg_overlay'] ) . '"></span>';

		}

		return $output;

	}
}

/**
 * Returns row parallax background.
 */
if ( ! function_exists( 'vcex_parallax_bg' ) ) {

	function vcex_parallax_bg( $atts ) {

		if ( ! vcex_supports_advanced_parallax() ) {
			return;
		}

		// Make sure parallax is enabled
		if ( empty( $atts['vcex_parallax'] ) ) {
			return;
		}

		// Return if a video is defined
		if ( ! empty( $atts['wpex_self_hosted_video_bg'] ) ) {
			return;
		}

		// Sanitize $bg_image
		if ( ! empty( $atts['parallax_image'] ) ) {
			$bg_image = wp_get_attachment_url( $atts['parallax_image'] );
		} elseif ( ! empty( $atts['bg_image'] ) ) {
			$bg_image = $atts['bg_image']; // Old deprecated setting
		} else {
			return;
		}

		// Sanitize data
		$parallax_style     = ! empty( $atts['parallax_style'] ) ? $atts['parallax_style'] : ''; // Default should be cover
		$parallax_speed     = ! empty( $atts['parallax_speed'] ) ? $atts['parallax_speed'] : '0.2';
		$parallax_direction = ! empty( $atts['parallax_direction'] ) ? $atts['parallax_direction'] : 'top';

		// Classes
		$classes = array( 'wpex-parallax-bg' );
		$classes[] = $parallax_style;
		if ( isset( $atts['parallax_mobile'] ) && 'no' == $atts['parallax_mobile'] ) {
			$classes[] = 'not-mobile';
		}
		$classes = apply_filters( 'wpex_parallax_classes', $classes );
		$classes = implode( ' ', array_filter( $classes, 'trim' ) );

		return wpex_parse_html( 'div', array(
			'class'          => esc_attr( $classes ),
			'data-direction' => $parallax_direction,
			'data-velocity'  => '-'. abs( $parallax_speed ),
			'style'          => 'background-image:url('. esc_url( $bg_image ) .' );',
		) );

	}
}