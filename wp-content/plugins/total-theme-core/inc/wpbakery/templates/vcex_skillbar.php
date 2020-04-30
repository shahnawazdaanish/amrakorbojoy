<?php
/**
 * Visual Composer Skillbar
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.8
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
$atts = vcex_vc_map_get_attributes( 'vcex_skillbar', $atts, $this );
extract( $atts );

// Load scripts
$this->enqueue_scripts();

// Define output var
$output = '';

// Allow shortcodes for percentage
$percentage = do_shortcode( $percentage );

// Wrap attributes
$wrap_attrs = array(
	'id'           => vcex_get_unique_id( $unique_id ),
	'data-percent' => intval( $percentage ) . '&#37;',
);

// Classes
$wrap_classes = array( 'vcex-module', 'vcex-skillbar clr' );
if ( 'false' == $box_shadow ) {
   $wrap_classes[] = ' disable-box-shadow';
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

// Style
$wrap_style = vcex_inline_style( array(
	'background' => $background,
	'font_size' => $font_size,
	'height_px' => $container_height,
	'line_height_px' => $container_height,
), false );

// Add parsed data to wrap attributes
$wrap_attrs['class'] = esc_attr( vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_skillbar', $atts ) );
$wrap_attrs['style'] = $wrap_style;

// Bar style
$bar_style = vcex_inline_style( array(
	'background' => $color,
) );

// Start shortcode output
$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	/*
	 * Title
	 */
	$title_style = vcex_inline_style( array(
		'background'   => $color,
		'padding_left' => $container_padding_left,
	) );

	$output .= '<div class="vcex-skillbar-title"' . $title_style . '>';

		$output .= '<div class="vcex-skillbar-title-inner">';

			// Icon
			if ( 'true' == $show_icon && $icon = vcex_get_icon_class( $atts, 'icon' ) ) {
				vcex_enqueue_icon_font( $icon_type, $icon );
				$output .= '<span class="vcex-icon-wrap"><span class="' . esc_attr( $icon ) . '" aria-hidden="true"></span></span>';
			}

			// Title
			$output .= wp_kses_post( do_shortcode( $title ) );

		$output .= '</div>';

	$output .= '</div>';

	/*
	 * Percentage
	 */
	if ( $percentage ) {

		$output .= '<div class="vcex-skillbar-bar"' . $bar_style . '>';

			if ( 'true' == $show_percent ) {

				$output .= '<div class="vcex-skill-bar-percent">' . intval( $percentage ) . '&#37;</div>';

			}

		$output .= '</div>';

	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
