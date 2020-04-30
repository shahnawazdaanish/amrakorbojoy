<?php
/**
 * Visual Composer Breadcrumbs
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

// Return if crumbs class doesn't exist
if ( ! class_exists( 'WPEX_Breadcrumbs' ) ) {
	return;
}

// Define crumbs
$crumbs = '';

// Yoast Crumbs
if ( function_exists( 'yoast_breadcrumb' ) && current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
	$crumbs = yoast_breadcrumb( '', '', false );
}

// Custom breadcrumbs
elseif ( $custom_breadcrumbs = apply_filters( 'wpex_custom_breadcrumbs', null ) ) {
	$crumbs = wp_kses_post( $custom_breadcrumbs );
} elseif ( class_exists( 'WPEX_Breadcrumbs' ) ) {
	// Generate breadcrumbs (stores trail in $ouput var)
	$crumbs = new WPEX_Breadcrumbs();
	$crumbs = $crumbs->generate_crumbs(); // needs to generate it's own to prevent issues with theme stuff
}

// Return if no crumbs
if ( ! $crumbs ) {
	return;
}

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_breadcrumbs', $atts, $this );

// Get inline styles
$inline_style = vcex_inline_style( array(
	'color'       => $atts['color'],
	'font_size'   => $atts['font_size'],
	'font_family' => $atts['font_family'],
	'text_align'  => $atts['align'],
), false );

// Load custom font
if ( $atts['font_family'] ) {
	vcex_enqueue_google_font( $atts['font_family'] );
}

// Define wrapper attributes
$wrap_attrs = array(
	'class' => 'vcex-breadcrumbs',
	'style' => $inline_style
);

// Extra classname
if ( $atts['el_class'] ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_extra_class( $atts['el_class'] );
}

// Visibility
if ( $atts['visibility'] ) {
	$wrap_attrs['class'] .= ' ' . $atts['visibility'];
}

// Responsive settings
if ( $responsive_data = vcex_get_module_responsive_data( $atts['font_size'], 'font_size' ) ) {
	$wrap_attrs['data-wpex-rcss'] = $responsive_data;
}

// Echo breadcrumbs
echo '<nav' . vcex_parse_html_attributes( $wrap_attrs ) . '>' . $crumbs . '</nav>';