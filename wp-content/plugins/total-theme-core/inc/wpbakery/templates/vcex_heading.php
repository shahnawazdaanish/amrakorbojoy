<?php
/**
 * Visual Composer Heading
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.2
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
$atts = vcex_vc_map_get_attributes( 'vcex_heading', $atts, $this );
extract( $atts );

// Get text
if ( 'post_title' === $source ) {
	$text = vcex_get_the_title();
} elseif ( 'post_date' === $source ) {
	$text = get_the_date( '', vcex_get_the_ID() );
} elseif ( 'post_modified_date' === $source ) {
	$text = get_the_modified_date( '', vcex_get_the_ID() );
} elseif ( 'post_author' === $source ) {
	$text = get_the_author();
	if ( empty( $text ) ) {
		$post_tmp = get_post( vcex_get_the_ID() );
		if ( $user = get_userdata( $post_tmp->post_author ) ) {
			$text = $user->data->display_name;
		}
	}
} elseif ( 'current_user' === $source ) {
	$text = wp_get_current_user()->display_name;
} elseif( 'custom_field' === $source ) {
	$text = $custom_field ? get_post_meta( vcex_get_the_ID(), $custom_field, true ) : '';
} elseif( 'callback_function' === $source ) {
	$text = ( $callback_function && function_exists( $callback_function ) ) ? call_user_func( $callback_function ) : '';
} else {
	$text = trim( vcex_vc_value_from_safe( $text ) );
	$text = do_shortcode( $text );
}

// Apply filters
$text = apply_filters( 'vcex_heading_text', $text );

// Return if no heading
if ( empty( $text ) ) {
	return;
}

// Define& sanitize vars
$output             = $icon_left_escaped = $icon_right_escaped = $link_wrap_tag_escaped = '';
$heading_attrs      = array( 'class' => '' );
$wrap_classes       = array( 'vcex-module', 'vcex-heading' );
$tag_escaped        = $tag ? esc_attr( $tag ) : apply_filters( 'vcex_heading_default_tag', 'div' );
$add_css_to_inner   = ( 'plain' === $style ) ? $add_css_to_inner : false;

// Add classes to wrapper
if ( $style ) {
	$wrap_classes[] = 'vcex-heading-'. $style;
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $css && 'true' != $add_css_to_inner ) {
	$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $css );
}
if ( 'true' === $italic ) {
	$wrap_classes[] = 'wpex-italic';
}

// Load custom font
if ( $font_family ) {
	vcex_enqueue_google_font( $font_family );
}

// Get link data
$link = vcex_build_link( $link );
if ( $link && isset( $link['url'] ) ) {
	$heading_attrs['href']   = do_shortcode( $link['url'] );
	$heading_attrs['title']  = isset( $link['title'] ) ? $link['title'] : '';
	$heading_attrs['target'] = isset( $link['target'] ) ? $link['target'] : '';
	$heading_attrs['rel']    = isset( $link['rel'] ) ? $link['rel'] : '';
	$link_wrap_tag_escaped   = $tag_escaped; // Add wrapper around link to keep tag (h2,h3...etc)
	$tag_escaped = 'a'; // Set tag to link
	if ( 'true' === $link_local_scroll ) {
		$wrap_classes[] = 'local-scroll-link';
	}
}

// Auto responsive Text
if ( 'true' === $responsive_text && $font_size ) {

	// Convert em font size to pixels
	if ( strpos( $font_size, 'em' ) !== false ) {
		$font_size = str_replace( 'em', '', $font_size );
		$font_size = $font_size * absint( vcex_get_body_font_size() );
	}

	// Convert em min-font size to pixels
	if ( strpos( $min_font_size, 'em' ) !== false ) {
		$min_font_size = str_replace( 'em', '', $min_font_size );
		$min_font_size = $min_font_size * absint( vcex_get_body_font_size() );
	}

	// Add wrap classes and data
	if ( $font_size && $min_font_size ) {
		$wrap_classes[] = 'wpex-responsive-txt';
		$heading_attrs['data-max-font-size'] = absint( $font_size );
		$min_font_size  = $min_font_size ? $min_font_size : '21px'; // 21px = default heading font size
		$min_font_size  = apply_filters( 'wpex_vcex_heading_min_font_size', $min_font_size );
		$heading_attrs['data-min-font-size'] = absint( $min_font_size );
	}

}

// Get responsive data
if ( $responsive_data = vcex_get_module_responsive_data( $atts ) ) {
	$heading_attrs['data-wpex-rcss'] = $responsive_data;
}

// Hover data
$hover_data = array();
if ( $color_hover ) {
	$hover_data[ 'color'] = esc_attr( $color_hover );
}
if ( $background_hover ) {
	$wrap_classes[] = 'transition-all';
	$hover_data[ 'background' ] = esc_attr( $background_hover );
}
if ( $hover_data ) {
	$heading_attrs[ 'data-wpex-hover' ] = htmlspecialchars( wp_json_encode( $hover_data ) );
}

if ( 'true' === $hover_white_text ) {
	$wrap_classes[] = 'wpex-hover-white-text';
}

if ( $align ) {
	$wrap_classes[] = 'align' . $align;
}

// Inner attributes
$inner_attrs = array(
	'class' => 'vcex-heading-inner clr',
);

// Inner style
$inner_attrs['style'] = vcex_inline_style( array(
	'border_color' => $inner_bottom_border_color,
) );

// Inner CSS
if ( 'true' === $add_css_to_inner && $css ) {
	$inner_attrs['class'] .= ' ' . vcex_vc_shortcode_custom_css_class( $css );
}

// Get icon
$icon = vcex_get_icon_class( $atts, 'icon' );

// Icon output
if ( $icon ) {

	vcex_enqueue_icon_font( $icon_type, $icon );

	$icon_attrs = array(
		'class' => 'vcex-icon-wrap vcex-icon-position-' . esc_attr( $icon_position ),
		'style' => vcex_inline_style( array(
			'color' => $icon_color,
		) )
	);

	$icon_output = '<span' . vcex_parse_html_attributes( $icon_attrs ) . '>';

		$icon_output .= '<span class="' . esc_attr( $icon ) . '" aria-hidden="true"></span>';

	$icon_output .= '</span>';

	// Add icon to heading
	if ( 'left' === $icon_position ) {
		$icon_left_escaped = $icon_output;
	} else {
		$icon_right_escaped = $icon_output;
	}

}

// Add custom classes last
if ( $el_class ) {
	$wrap_classes[] = vcex_get_extra_class( $el_class );
}

// Turn wrap classes into string and apply filter
$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_heading', $atts );

// Add classes to attributes array
$heading_attrs['class'] = $wrap_classes;

// Add inline style
$heading_attrs['style'] = vcex_inline_style( array(
	'color'               => $color,
	'font_family'         => $font_family,
	'font_size'           => $font_size,
	'letter_spacing'      => $letter_spacing,
	'font_weight'         => $font_weight,
	'text_align'          => $text_align,
	'text_transform'      => $text_transform,
	'line_height'         => $line_height,
	'border_bottom_color' => $inner_bottom_border_color_main,
	'width'               => $width,
), false );

// Heading output
if ( $link_wrap_tag_escaped ) {

	$output .= '<' . $link_wrap_tag_escaped . ' class="vcex-heading-link-wrap clr">';

}

$output .= '<' . $tag_escaped . '' . vcex_parse_html_attributes( $heading_attrs ) . '>';

	$output .= '<span' . vcex_parse_html_attributes( $inner_attrs ) . '>';

		$output .= $icon_left_escaped;

			$output .= wp_kses_post( $text );

		$output .= $icon_right_escaped;

	$output .= '</span>';

$output .= '</' . $tag_escaped . '>';

if ( $link_wrap_tag_escaped ) {

	$output .= '</' . $link_wrap_tag_escaped . '>';

}

// @codingStandardsIgnoreLine
echo $output;
