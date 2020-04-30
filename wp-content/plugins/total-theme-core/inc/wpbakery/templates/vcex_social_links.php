<?php
/**
 * Visual Composer Social Links
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
extract( vcex_vc_map_get_attributes( 'vcex_social_links', $atts, $this ) );

// Get social profiles array | Used for fallback method and to grab icon styles
$social_profiles = (array) vcex_social_links_profiles();

// Social profile array can't be empty
if ( ! $social_profiles ) {
	return;
}

// Define output var
$output = '';

// Get current author social links
if ( 'true' == $author_links ) {

	$post_tmp    = get_post( vcex_get_the_ID() );
	$post_author = $post_tmp->post_author;

	if ( ! $post_author ) {
		return;
	}

	$loop = array();
	$social_settings = wpex_get_user_social_profile_settings_array();

	foreach ( $social_settings as $id => $label ) {

		if ( $url = get_the_author_meta( 'wpex_'. $id, $post_author ) ) {

			$loop[$id] = $url;

		}

	}

	$post_tmp = '';

} else {

	// Display custom social links
	// New method since 3.5.0 | must check $atts value due to fallback and default var
	if ( ! empty( $atts['social_links'] ) ) {
		$social_links = (array) vcex_vc_param_group_parse_atts( $social_links );
		$loop = array();
		foreach ( $social_links as $key => $val ) {
			$loop[$val['site']] = isset( $val['link'] ) ? do_shortcode( $val['link'] ) : '';
		}
	} else {
		$loop = $social_profiles;
	}

}

// Loop is required
if ( ! is_array( $loop ) ) {
	return;
}

// Wrap attributes
$wrap_attrs = array(
	'id' => $unique_id,
	'data' => '',
);

// Wrap classes
$wrap_classes = array( 'vcex-module' );
if ( $style ) {
	$wrap_classes[] = 'wpex-social-btns vcex-social-btns';
} else {
	$wrap_classes[] = 'vcex-social-links';
}
if ( $align ) {
	$wrap_classes[] = 'text'. $align;
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}
if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}

// Wrap style
$wrap_style = vcex_inline_style( array(
	'color'         => $color,
	'font_size'     => $size,
	'border_radius' => $border_radius,
), false );

// Link Attributes
$a_style = vcex_inline_style( array(
	'width'       => $width,
	'height'      => $height,
	'line_height' => $height ? intval( $height ) .'px' : '',
), false );

// Link Classes
$a_classes = array();
if ( $style ) {
	$a_classes[] = vcex_get_social_button_class( $style );
} else {
	$a_classes[] = 'vcex-social-link';
}
if ( $width || $height ) {
	$a_classes[] = 'no-padding';
}
if ( $hover_animation ) {
	$a_classes[] = vcex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
if ( $css ) {
	$a_classes[] = vcex_vc_shortcode_custom_css_class( $css );
}

// Hover data
$a_hover_data = array();
if ( $hover_bg ) {
	$a_hover_data['background'] = esc_attr( $hover_bg );
}
if ( $hover_color ) {
	$a_hover_data['color'] = esc_attr( $hover_color );
}
$a_hover_data = $a_hover_data ? htmlspecialchars( wp_json_encode( $a_hover_data ) ) : '';

// Responsive settings
if ( $responsive_data = vcex_get_module_responsive_data( $size, 'font_size' ) ) {
	$wrap_attrs['data-wpex-rcss'] = $responsive_data;
}

// Add attributes to array
$wrap_attrs['class'] = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_social_links', $atts );
$wrap_attrs['style'] = $wrap_style;

// Begin output
$output .= '<div' . vcex_parse_html_attributes(  $wrap_attrs ) . '>';

	// Loop through social profiles
	foreach ( $loop as $key => $val ) {

		// Google plus was shut down
		if ( 'googleplus' == $key || 'google-plus' == $key ) {
			continue;
		}

		// Sanitize classname
		$profile_class = $key;
		$profile_class = 'googleplus' == $key ? 'google-plus' : $key;

		// Get URL
		if ( 'true' != $author_links && empty( $atts['social_links'] ) ) {
			$url = isset( $atts[$key] ) ? $atts[$key] : '';
		} else {
			$url = $val;
		}

		// Link output
		if ( $url ) {

			$a_attrs = array(
				'href'   => esc_url( do_shortcode( $url ) ),
				'class'  => implode( ' ', $a_classes ) . ' wpex-' . $profile_class,
				'style'  => $a_style,
				'target' => $link_target,
			);

			if ( $a_hover_data ) {
				$a_attrs['data-wpex-hover'] = $a_hover_data;
			}

			$output .= '<a '. vcex_parse_html_attributes( $a_attrs ) .'><span class="' . esc_attr( $social_profiles[$key]['icon_class'] ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html( $key ) . '</span></a>';
		}

	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
