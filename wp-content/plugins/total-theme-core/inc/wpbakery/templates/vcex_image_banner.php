<?php
/**
 * Visual Composer Image Swap
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.1
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
extract( vcex_vc_map_get_attributes( 'vcex_image_banner', $atts, $this ) );

// Check links
$link       = vcex_build_link( $link );
$has_link   = isset( $link['url'] ) ? true : false;
$has_button = ( 'true' == $button && $button_text ) ? true : false;

// Wrap classes
$wrap_attrs = array(
	'class' => 'vcex-module vcex-image-banner',
);
if ( 'true' == $use_img_tag ) {
	$wrap_attrs['class'] .= ' vcex-has-img-tag';
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_attrs['class'] .= ' ' . trim( vcex_get_css_animation( $css_animation ) );
}
if ( $align ) {
	$wrap_attrs['class'] .= ' ' . sanitize_html_class( 'float' . $align );
}
if ( $content_align ) {
	$wrap_attrs['class'] .= ' ' . sanitize_html_class( 'text' . $content_align );
}
if ( $border_radius ) {
	$wrap_attrs['class'] .= ' ' . sanitize_html_class( 'vcex-border-radius-' . intval( $border_radius ) );
}
if ( 'true' == $show_on_hover ) {
	$wrap_attrs['class'] .= ' vcex-soh';
	if ( ! $has_link ) {
		$wrap_attrs['class'] .= ' overlay-parent';
	}
	$wrap_attrs['class'] .= ' ' . sanitize_html_class( 'vcex-anim-' . $show_on_hover_anim );
}
if ( 'true' == $image_zoom ) {
	$wrap_attrs['class'] .= ' vcex-h-zoom';
}
if ( $has_button ) {
	$wrap_attrs['class'] .= ' vcex-has-button';
}
if ( $el_class ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_extra_class( $el_class );
}

if ( $width ) {
	$wrap_attrs['style'] = vcex_inline_style( array(
		'width' => $width,
	), false );
}

// Output var
$output = '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	// Open link
	if ( $has_link ) {

		$link_attrs = array(
			'href'   => do_shortcode( $link['url'] ),
			'class'  => 'vcex-ib-link',
			'title'  => isset( $link['title'] ) ? do_shortcode( $link['title'] ) : '',
			'rel'    => isset( $link['rel'] ) ? $link['rel'] : '',
			'target' => isset( $link['target'] ) ? $link['target'] : '',
		);

		if ( 'true' == $show_on_hover ) {
			$link_attrs['class'] .= ' overlay-parent';
		}

		if ( 'true' == $link_local_scroll ) {
			$link_attrs['class'] .= ' local-scroll-link';
		}

		$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

	}

	// Image
	if ( 'featured' == $image_source ) {
		$image = get_post_thumbnail_id( vcex_get_the_ID() );
	} elseif ( 'custom_field' == $image_source ) {
		if ( $image_custom_field ) {
			$custom_field_val = get_post_meta( vcex_get_the_ID(), $image_custom_field, true );
			$image = intval( $custom_field_val );
		}
	}

	// Generate image URL
	if ( $image ) {
		$image_url = vcex_get_post_thumbnail( array(
			'attachment' => $image,
			'size'       => $img_size,
			'crop'       => $img_crop,
			'width'      => $img_width,
			'height'     => $img_height,
			'return'     => 'url',
		) );
	} else {
		$image_url = '';
	}

	if ( $image_url ) {

		if ( 'true' == $use_img_tag ) {

			$style = vcex_inline_style( array(
				'transition_speed' => ( $image_zoom_speed && '0.4' != $image_zoom_speed ) ? $image_zoom_speed : '',
			) );

			$output .= '<img src="' . esc_url( $image_url ) . '" class="vcex-ib-img"' . $style . ' />';

		} else {

			$style = vcex_inline_style( array(
				'background_image'    => esc_url( $image_url ),
				'background_position' => $image_position,
				'transition_speed'    => ( $image_zoom_speed && '0.4' != $image_zoom_speed ) ? $image_zoom_speed : '',
			) );

			$output .= '<span class="vcex-ib-img"' . $style . '></span>';

		}

	}

	// Overlay
	if ( 'true' == $overlay ) {

		$overlay_style = vcex_inline_style( array(
			'background' => $overlay_color,
			'opacity'    => $overlay_opacity,
		) );

		$output .= '<span class="vcex-ib-overlay"' . $overlay_style . '></span>';

	}

	// Inner border
	if ( 'true' == $inner_border ) {

		$border_style = vcex_inline_style( array(
			'border_color' => $inner_border_color,
			'border_width' => $inner_border_width,
		) );

		$output .= '<div class="vcex-inner-border"' . $border_style . '></div>';
	}

	// Content style
	$wrap_style = $padding ? ' ' . vcex_inline_style( array(
		'padding' => $padding,
	) ) : '';

	// Open content wrap
	$output .= '<div class="vcex-ib-content-wrap clr"' . $wrap_style . '>';

		// Inner style
		$inner_style = vcex_inline_style( array(
			'width' => $content_width,
		) );
		$inner_style = $inner_style ? ' ' . $inner_style : '';

		$output .= '<div class="vcex-ib-content clr"' . $inner_style . '>';

			// Heading
			if ( $heading ) {

				$heading_tag_escaped = $heading_tag ? esc_attr( $heading_tag ) : 'div';

				if ( $heading_font_family ) {
					vcex_enqueue_google_font( $heading_font_family );
				}

				$attrs = array(
					'class' => 'vcex-ib-title',
					'style' => vcex_inline_style( array(
						'font_family'    => $heading_font_family,
						'font_weight'    => $heading_font_weight,
						'font_size'      => $heading_font_size,
						'letter_spacing' => $heading_letter_spacing,
						'italic'         => $heading_italic,
						'line_height'    => $heading_line_height,
						'color'          => $heading_color,
						'padding_bottom' => $heading_bottom_padding,
					), false )
				);

				if ( $rfont_size = vcex_get_responsive_font_size_data( $heading_font_size ) ) {
					$attrs['data-wpex-rcss'] = "data-wpex-rcss='" . htmlspecialchars( wp_json_encode( array( 'font-size' => $rfont_size ) ) ) . "'";
				}

				$output .= '<' . $heading_tag_escaped . vcex_parse_html_attributes( $attrs ) . '>';

					$output .= wp_kses_post( do_shortcode( $heading ) );

				$output .= '</' . $heading_tag_escaped . '>';
			}

			// Caption
			if ( $caption ) {

				if ( $caption_font_family ) {
					vcex_enqueue_google_font( $caption_font_family );
				}

				$attrs = array(
					'class' => 'vcex-ib-caption',
					'style' => vcex_inline_style( array(
						'font_family'    => $caption_font_family,
						'font_weight'    => $caption_font_weight,
						'font_size'      => $caption_font_size,
						'letter_spacing' => $caption_letter_spacing,
						'italic'         => $caption_italic,
						'line_height'    => $caption_line_height,
						'color'          => $caption_color,
						'padding_bottom' => $caption_bottom_padding,
					), false )
				);

				if ( $rfont_size = vcex_get_responsive_font_size_data( $caption_font_size ) ) {
					$attrs['data-wpex-rcss'] = "data-wpex-rcss='" . htmlspecialchars( wp_json_encode( array( 'font-size' => $rfont_size ) ) ) . "'";
				}

				$output .= '<div' . vcex_parse_html_attributes( $attrs ) . '>' . wp_kses_post( do_shortcode( $caption ) ) . '</div>';

			}

			// Button
			if ( $has_button ) {

				if ( $button_font_family ) {
					vcex_enqueue_google_font( $button_font_family );
				}

				$attrs = array(
					'class' => vcex_get_button_classes( $button_style, $button_color ),
					'style' => vcex_inline_style( array(
						'font_family'    => $button_font_family,
						'font_weight'    => $button_font_weight,
						'font_size'      => $button_font_size,
						'letter_spacing' => $button_letter_spacing,
						'italic'         => $button_italic,
						'color'          => $button_custom_color,
						'background'     => $button_custom_background,
						'width'          => $button_width,
						'padding'        => $button_padding,
						'border_radius'  => $button_border_radius,
					), false )
				);

				if ( $rfont_size = vcex_get_responsive_font_size_data( $button_font_size ) ) {
					$attrs['data-wpex-rcss'] = "data-wpex-rcss='" . htmlspecialchars( wp_json_encode( array( 'font-size' => $rfont_size ) ) ) . "'";
				}

				$hover_data = array();
				if ( $button_custom_hover_color ) {
					$hover_data['color'] = esc_attr( $button_custom_hover_color );
				}
				if ( $button_custom_hover_background ) {
					$hover_data['background'] = esc_attr( $button_custom_hover_background );
				}
				if ( $hover_data ) {
					$attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( $hover_data ) );
				}

				$output .= '<div class="vcex-ib-button"><span' . vcex_parse_html_attributes( $attrs ) . '>' . do_shortcode( wp_kses_post( $button_text ) ) . '</span></div>';

			}

		$output .= '</div>';

	$output .= '</div>';

	if ( $has_link ) {
		$output .= '</a>';
	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
