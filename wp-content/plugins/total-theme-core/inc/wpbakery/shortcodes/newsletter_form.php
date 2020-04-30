<?php
/**
 * Newsletter Shortcode
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VCEX_Newsletter_Shortcode' ) ) {

	class VCEX_Newsletter_Shortcode {

		/**
		 * Define shortcode name.
		 */
		public $shortcode = 'vcex_newsletter_form';

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
				'name'        => esc_html__( 'Mailchimp Form', 'total-theme-core' ),
				'description' => esc_html__( 'Newsletter subscription form', 'total-theme-core' ),
				'base'        => $this->shortcode,
				'category'    => vcex_shortcodes_branding(),
				'icon'        => 'vcex-newsletter vcex-icon ticon ticon-envelope',
				'params'      => array(
					// General
					array(
						'type'       => 'vcex_notice',
						'param_name' => 'mailchimp_form_action_notice',
						'text'       => esc_html__( 'While the module is optimized for Mailchimp you could potentially use the form action URL from another newsletter service.', 'total-theme-core'),
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Mailchimp Form Action', 'total-theme-core' ),
						'param_name'  => 'mailchimp_form_action',
						'admin_label' => true,
						'value'       => '//domain.us1.list-manage.com/subscribe/post?u=numbers_go_here',
						'description' => esc_html__( 'Enter the MailChimp form action URL.', 'total-theme-core' ) .' <a href="https://wpexplorer-themes.com/total/docs/mailchimp-form-action-url/" target="_blank">'. esc_html__( 'Learn More', 'total-theme-core' ) .' &rarr;</a>',
					),
					array(
						'type'        => 'textfield',
						'admin_label' => true,
						'heading'     => esc_html__( 'Element ID', 'total-theme-core' ),
						'param_name'  => 'unique_id',
						'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'total-theme-core' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
					),
					array(
						'type'        => 'textfield',
						'admin_label' => true,
						'heading'     => esc_html__( 'Extra class name', 'total-theme-core' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'total-theme-core' ),
						'param_name'  => 'classes',
					),
					array(
						'type'       => 'vcex_visibility',
						'heading'    => esc_html__( 'Visibility', 'total-theme-core' ),
						'param_name' => 'visibility',
					),
					vcex_vc_map_add_css_animation(),
					array(
						'type'       => 'vcex_ofswitch',
						'std'        => 'false',
						'heading'    => esc_html__( 'Full-Width on Mobile', 'total-theme-core'),
						'param_name' => 'fullwidth_mobile',
					),
					// Input
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Text', 'total-theme-core' ),
						'param_name' => 'placeholder_text',
						'std'        => esc_html__( 'Enter your email address', 'total-theme-core' ),
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Background', 'total-theme-core' ),
						'param_name' => 'input_bg',
						'dependency' => array(
							'element'   => 'mailchimp_form_action',
							'not_empty' => true
						),
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Color', 'total-theme-core' ),
						'param_name' => 'input_color',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Border Color', 'total-theme-core' ),
						'param_name' => 'input_border_color',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Width', 'total-theme-core' ),
						'param_name' => 'input_width',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'vcex_text_alignments',
						'heading'    => esc_html__( 'Alignment', 'total-theme-core' ),
						'param_name' => 'input_align',
						'std' => '',
						'dependency' => array( 'element' => 'input_width', 'not_empty' => true ),
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Height', 'total-theme-core' ),
						'param_name' => 'input_height',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'vcex_trbl',
						'heading'    => esc_html__( 'Padding', 'total-theme-core' ),
						'param_name' => 'input_padding',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Border', 'total-theme-core' ),
						'param_name' => 'input_border',
						'description' => esc_html__( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total-theme-core' ),
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Border Radius', 'total-theme-core' ),
						'param_name' => 'input_border_radius',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Font Size', 'total-theme-core' ),
						'param_name' => 'input_font_size',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Letter Spacing', 'total-theme-core' ),
						'param_name' => 'input_letter_spacing',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					array(
						'type'       => 'vcex_font_weight',
						'heading'    => esc_html__( 'Font Weight', 'total-theme-core' ),
						'param_name' => 'input_weight',
						'group'      => esc_html__( 'Input', 'total-theme-core' ),
					),
					// Submit
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Text', 'total-theme-core' ),
						'param_name' => 'submit_text',
						'std' => esc_html__( 'Sign Up', 'total-theme-core' ),
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Background', 'total-theme-core' ),
						'param_name' => 'submit_bg',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Background: Hover', 'total-theme-core' ),
						'param_name' => 'submit_hover_bg',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Color', 'total-theme-core' ),
						'param_name' => 'submit_color',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Color: Hover', 'total-theme-core' ),
						'param_name' => 'submit_hover_color',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Height', 'total-theme-core' ),
						'param_name' => 'submit_height',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'vcex_trbl',
						'heading'    => esc_html__( 'Padding', 'total-theme-core' ),
						'param_name' => 'submit_padding',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Border', 'total-theme-core' ),
						'param_name' => 'submit_border',
						'description' => esc_html__( 'Please use the shorthand format: width style color. Enter 0px or "none" to disable border.', 'total-theme-core' ),
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Border Radius', 'total-theme-core' ),
						'param_name' => 'submit_border_radius',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Font Size', 'total-theme-core' ),
						'param_name' => 'submit_font_size',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Letter Spacing', 'total-theme-core' ),
						'param_name' => 'submit_letter_spacing',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
					array(
						'type'       => 'vcex_font_weight',
						'heading'    => esc_html__( 'Font Weight', 'total-theme-core' ),
						'param_name' => 'submit_weight',
						'group'      => esc_html__( 'Submit', 'total-theme-core' ),
					),
				)
			);
		}

	}
}
new VCEX_Newsletter_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_newsletter_form' ) ) {
	class WPBakeryShortCode_vcex_newsletter_form extends WPBakeryShortCode {}
}
