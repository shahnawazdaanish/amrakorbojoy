<?php
/**
 * Divider Shortcode
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VCEX_Author_Bio_Shortcode' ) ) {

	class VCEX_Author_Bio_Shortcode {

		/**
		 * Main constructor.
		 */
		public function __construct() {
			add_shortcode( 'vcex_author_bio', array( $this, 'output' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				add_action( 'vc_after_mapping', array( $this, 'vc_after_mapping' ) );
			}

		}

		/**
		 * Shortcode output => Get template file and display shortcode.
		 */
		public function output( $atts, $content = null ) {
			ob_start();
			if ( function_exists( 'wpex_get_template_part' ) ) {
				wpex_get_template_part( 'author_bio' );
			} else {
				echo esc_html__( 'This theme is missing the author bio function.', 'total-theme-core' );
			}
			return ob_get_clean();
		}

		/**
		 * VC functions.
		 */
		public function vc_after_mapping() {
			vc_lean_map( 'vcex_author_bio', array( $this, 'map' ) );
		}

		/**
		 * Map shortcode to VC.
		 */
		public function map() {
			return array(
				'name'        => esc_html__( 'Author Bio', 'total-theme-core' ),
				'description' => esc_html__( 'Display current author bio.', 'total-theme-core' ),
				'base'        => 'vcex_author_bio',
				'icon'        => 'vcex-author-bio vcex-icon ticon ticon-user-circle',
				'category'    => vcex_shortcodes_branding(),
				'params'      => array(
					array(
						'type'       => 'vcex_notice',
						'param_name' => 'main_notice',
						'text'       => esc_html__( 'This module doesn\'t have any settings.', 'total-theme-core' ),
					),
				),
				'show_settings_on_create' => false,
			);
		}
	}
}
new VCEX_Author_Bio_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_author_bio' ) ) {
	class WPBakeryShortCode_vcex_author_bio extends WPBakeryShortCode {}
}