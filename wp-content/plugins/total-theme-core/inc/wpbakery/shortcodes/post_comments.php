<?php
/**
 * Post Comments Shortcode
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VCEX_Comments_Shortcode' ) ) {

	class VCEX_Comments_Shortcode {

		/**
		 * Define shortcode name.
		 */
		public $shortcode = 'vcex_post_comments';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'output' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				add_action( 'vc_after_mapping', array( $this, 'vc_after_mapping' ) );
			}

		}

		/**
		 * Shortcode output => Get template file and display shortcode.
		 */
		public function output( $atts, $content = null ) {
			ob_start();
			include( vcex_get_shortcode_template( $this->shortcode ) );
			return ob_get_clean();
		}

		/**
		 * VC functions.
		 */
		public function vc_after_mapping() {
			vc_lean_map( $this->shortcode, array( $this, 'map' ) );
		}

		/**
		 * Map shortcode to VC.
		 */
		public function map() {
			return array(
				'name' => esc_html__( 'Post Comments', 'total-theme-core' ),
				'description' => esc_html__( 'Display your post comments.', 'total-theme-core' ),
				'base' => $this->shortcode,
				'icon' => 'vcex-post-comments vcex-icon ticon ticon-comments',
				'category' => vcex_shortcodes_branding(),
				'params' => array(
					array(
						'type' => 'vcex_ofswitch',
						'std' => 'false',
						'heading' => esc_html__( 'Display Heading?', 'total-theme-core' ),
						'param_name' => 'show_heading',
					),
					array(
						'type' => 'vcex_visibility',
						'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
						'param_name' => 'visibility',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
						'param_name' => 'el_class',
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'total-theme-core' ),
					),
				)
			);
		}
	}
}
new VCEX_Comments_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_post_comments' ) ) {
	class WPBakeryShortCode_vcex_post_comments extends WPBakeryShortCode {}
}
