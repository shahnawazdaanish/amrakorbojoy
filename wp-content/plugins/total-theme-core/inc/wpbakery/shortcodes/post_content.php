<?php
/**
 * Post Content Shortcode
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VCEX_Post_Content_Shortcode' ) ) {

	class VCEX_Post_Content_Shortcode {

		/**
		 * Define shortcode name.
		 */
		public $shortcode = 'vcex_post_content';

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
		 * Map shortcode to VC
		 *
		 * @since 4.3
		 */
		public function map() {
			return array(
				'name' => esc_html__( 'Post Content', 'total-theme-core' ),
				'description' => esc_html__( 'Display your post content.', 'total-theme-core' ),
				'base' => $this->shortcode,
				'icon' => 'vcex-post-content vcex-icon ticon ticon-pencil',
				'category' => vcex_shortcodes_branding(),
				'params' => array(
					array(
						'type' => 'vcex_notice',
						'param_name' => 'main_notice',
						'text' => esc_html__( 'The Post Content module should be used only when creating a custom template via templatera that will override the default output of a post/page.', 'total-theme-core' ),
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Sidebar', 'total-theme-core' ),
						'param_name' => 'sidebar',
						'std' => 'false',
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Post Series', 'total-theme-core' ),
						'param_name' => 'post_series',
						'std' => 'false',
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Social Share', 'total-theme-core' ),
						'param_name' => 'social_share',
						'std' => 'false',
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Author Box', 'total-theme-core' ),
						'param_name' => 'author_bio',
						'std' => 'false',
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Related Posts', 'total-theme-core' ),
						'param_name' => 'related',
						'std' => 'false',
					),
					array(
						'type' => 'vcex_ofswitch',
						'heading' => esc_html__( 'Comments', 'total-theme-core' ),
						'param_name' => 'comments',
						'std' => 'false',
					),
					// Typography
					array(
						'type' => 'vcex_font_family_select',
						'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
						'param_name' => 'font_family',
						'group' => esc_html__( 'Typography', 'total-theme-core' ),
					),
					array(
						'type' => 'vcex_responsive_sizes',
						'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
						'param_name' => 'font_size',
						'target' => 'font-size',
						'group' => esc_html__( 'Typography', 'total-theme-core' ),
					),
					// CSS
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
						'param_name' => 'css',
						'group' => esc_html__( 'Design Options', 'total-theme-core' ),
					),
				)
			);
		}
	}
}
new VCEX_Post_Content_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_post_content' ) ) {
	class WPBakeryShortCode_vcex_post_content extends WPBakeryShortCode {}
}
