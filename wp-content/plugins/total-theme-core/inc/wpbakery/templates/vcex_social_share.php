<?php
/**
 * Visual Composer Social Share
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Total only module
if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
	return;
}

$atts = vcex_vc_map_get_attributes( 'vcex_social_share', $atts, $this );

$sites = ! empty( $atts['sites'] ) ? (array) vcex_vc_param_group_parse_atts( $atts['sites'] ) : '';

if ( ! $sites ) {
	return;
}

$wrap_attrs = array(
	'class' => 'wpex-social-share position-horizontal',
);

$wrap_attrs['class'] .= ' style-' . esc_attr( $atts['style'] );
if ( isset( $atts['visibility'] ) ) {
	$wrap_attrs['class'] .= ' ' .  $atts['visibility'];
}

$sites_array = array();
foreach ( $sites as $k => $v ) {
	$sites_array[] = $k;
}
$social_share_data = wpex_get_social_share_data( vcex_get_the_ID(), $sites_array );

foreach ( $social_share_data as $datak => $datav ) {
	$wrap_attrs['data-' . $datak ] = $datav;
}

wp_enqueue_script( 'wpex-social-share' );

$output = '';

$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	$output .= '<ul class="clr">';

		// Get array of social share items
		$items = vcex_get_social_items();

		// Loop through sites and save new array with filters for output
		foreach ( $sites as $k => $v ) {

			$site = isset( $v['site'] ) ? $v['site'] : '';

			if ( isset( $items[$site] ) ) {

				$item = $items[$site];

				$output .= '<li class="' . esc_attr( $item['li_class'] ) . '">';

					if ( isset( $item['href'] ) ) {

						$output .= '<a href="' . esc_url( $item['href'] ) . '" role="button">';

					} else {

						$output .= '<a href="#" role="button">';

					}

						$output .= '<span class="' . esc_attr( $item['icon_class'] ) . '" aria-hidden="true"></span>';

						$output .= '<span class="wpex-label">' . esc_html( $item['label'] ) . '</span>';

					$output .= '</a>';

				$output .= '</li>';

			}

		}

	$output .= '</ul>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
