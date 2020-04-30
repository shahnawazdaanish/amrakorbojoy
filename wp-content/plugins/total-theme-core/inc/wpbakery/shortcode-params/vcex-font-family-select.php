<?php
/**
 * Adds a new custom font family select parameter to the VC
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'vcex_font_family_select_shortcode_param' ) ) {

	function vcex_font_family_select_shortcode_param( $settings, $value ) {

		if ( defined( 'TOTAL_THEME_ACTIVE' ) ) {

			$output = '<select name="'
					. $settings['param_name']
					. '" class="wpb_vc_param_value wpb-input wpb-select vcex-chosen '
					. $settings['param_name']
					. ' ' . $settings['type'] .'">'
					. '<option value="" '. selected( $value, '', false ) .'>'. esc_html__( 'Default', 'total-theme-core' ) .'</option>';

			$fonts = wpex_add_custom_fonts();
			if ( $fonts && is_array( $fonts ) ) {
				$output .= '<optgroup label="'. esc_html__( 'Custom Fonts', 'total-theme-core' ) .'">';
				foreach ( $fonts as $font ) {
					$output .= '<option value="'. esc_html( $font ) .'" '. selected( $value, $font, false ) .'>'. esc_html( $font ) .'</option>';
				}
				$output .= '</optgroup>';
			}

			if ( $std_fonts = wpex_standard_fonts() ) {
				$output .= '<optgroup label="'. esc_html__( 'Standard Fonts', 'total-theme-core' ) .'">';
					foreach ( $std_fonts as $font ) {
						$output .= '<option value="'. esc_html( $font ) .'" '. selected( $font, $value, false ) .'>'. esc_html( $font ) .'</option>';
					}
				$output .= '</optgroup>';
			}

			if ( $google_fonts = wpex_google_fonts_array() ) {
				$output .= '<optgroup label="'. esc_html__( 'Google Fonts', 'total-theme-core' ) .'">';
					foreach ( $google_fonts as $font ) {
						$output .= '<option value="'. esc_html( $font ) .'" '. selected( $font, $value ) .'>'. esc_html( $font ) .'</option>';
					}
				$output .= '</optgroup>';
			}

			$output .= '</select>';

			$output .= '<div class="vc_description vc_clearfix">' . sprintf( wp_kses( esc_html__( 'Choose between standard and Google font options. If you are unfamiliar with Google fonts you can visit <a href="%s" target="_blank">the Google Fonts website</a> and locate the font you like then type the name into the field above.', 'total-theme-core' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), 'https://fonts.google.com/' ) . '</div>';

		} else {
			$output = vcex_total_exclusive_notice();
			$output .= '<input type="hidden" class="wpb_vc_param_value '
				. esc_attr( $settings['param_name'] ) . ' '
				. esc_attr( $settings['type'] ) . '" name="' . esc_attr( $settings['param_name'] ) . '" value="' . esc_attr( $value ) . '"/>';
		}

		return $output;

	}

}

vc_add_shortcode_param(
	'vcex_font_family_select',
	'vcex_font_family_select_shortcode_param',
	vcex_asset_url( 'js/backend/vcex-params.min.js?v=' . TTC_VERSION )
);