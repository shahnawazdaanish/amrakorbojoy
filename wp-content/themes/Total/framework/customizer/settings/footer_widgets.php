<?php
/**
 * Customizer => Footer Widgets
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// General
$this->sections['wpex_footer_general'] = array(
	'title'    => esc_html__( 'General', 'total' ),
	'panel'    => 'wpex_footer_widgets',
	'settings' => array(
		array(
			'id' => 'footer_widgets',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Enable Footer Widgets?', 'total' ),
				'type' => 'checkbox',
				'desc' => $disable_panel_desc,
			),
		),
		array(
			'id' => 'fixed_footer',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Enable Fixed Footer?', 'total' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'This setting will not "fix" your footer per-se but will add a min-height to your #main container to keep your footer always at the bottom of the page.', 'total' ),
				//'active_callback' => 'wpex_cac_has_footer_widgets', // Also affects footer bottom
			),
		),
		array(
			'id' => 'footer_reveal',
			'control' => array(
				'label' => esc_html__( 'Enable Footer Reveal?', 'total' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'Enable the footer reveal style. The footer will be placed in a fixed postion and display on scroll. This setting is for the "Full-Width" layout only and desktops only.', 'total' ),
				//'active_callback' => 'wpex_cac_supports_reveal',
			),
		),
		array(
			'id' => 'footer_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => '#footer-inner',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'footer_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background Color', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '#footer',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'footer_bg_img',
			'control' => array(
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Background Image', 'total' ),
			),
		),
		array(
			'id' => 'footer_bg_img_style',
			'default' => '',
			'control' => array(
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'choices' => $bg_styles,
			),
			'control_display' => array(
				'check' => 'footer_bg_img',
				'value' => 'true',
			),
		),
		array(
			'id' => 'footer_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => array(
					'#footer',
					'#footer p',
					'#footer li a:before',
					'#footer .widget-recent-posts-icons li .fa',
					'#footer strong'
				),
				'alter' => 'color',
			),
		),
		array(
			'id' => 'footer_borders',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Borders', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => array(
					'#footer li',
					'#footer #wp-calendar thead th',
					'#footer #wp-calendar tbody td',
					'#footer table th',
					'#footer table td'
				),
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'footer_link_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Links', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '#footer a',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'footer_link_color_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Links: Hover', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '#footer a:hover',
				'alter' => 'color',
			),
		),
	),
);

$this->sections['wpex_footer_widgets_columns'] = array(
	'title'    => esc_html__( 'Widget Columns', 'total' ),
	'panel'    => 'wpex_footer_widgets',
	'settings' => array(
		array(
			'id' => 'footer_widgets_columns',
			'default' => '4',
			'control' => array(
				'label' => esc_html__( 'Columns', 'total' ),
				'type' => 'select',
				'choices' => array(
					'5' => '5',
					'4' => '4',
					'3' => '3',
					'2' => '2',
					'1' => '1',
				),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
		),
		array(
			'id' => 'footer_widgets_gap',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => wpex_column_gaps(),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
		),
		array(
			'id' => 'footer_widgets_bottom_margin',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Bottom Margin', 'total' ),
				'type' => 'text',
				'active_callback' => 'wpex_cac_has_footer_widgets',
				'description'     => esc_html__( 'The Bottom Margin is technically applied to each widget so you have space between widgets added in the same column. If you alter this value you should probably also change your general Footer top padding so the top/bottom spacing in your footer area match.', 'total' ),
			),
			'inline_css'   => array(
				'target'   => '.footer-widget',
				'alter'    => 'padding-bottom',
				'sanitize' => 'px-pct',
			),
		),
		array(
			'id' => 'footer_widgets_col_1_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Column 1 Width', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css'   => array(
				'target'   => '.footer-box.col-1',
				'alter'    => 'width',
				'sanitize' => 'px-pct',
			),
		),
		array(
			'id' => 'footer_widgets_col_2_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Column 2 Width', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css'   => array(
				'target'   => '.footer-box.col-2',
				'alter'    => 'width',
				'sanitize' => 'px-pct',
			),
		),
		array(
			'id' => 'footer_widgets_col_3_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Column 3 Width', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css'   => array(
				'target'   => '.footer-box.col-3',
				'alter'    => 'width',
				'sanitize' => 'px-pct',
			),
		),
		array(
			'id' => 'footer_widgets_col_4_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Column 4 Width', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css'   => array(
				'target'   => '.footer-box.col-4',
				'alter'    => 'width',
				'sanitize' => 'px-pct',
			),
		),
		array(
			'id' => 'footer_widgets_col_5_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Column 5 Width', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css'   => array(
				'target'   => '.footer-box.col-5',
				'alter'    => 'width',
				'sanitize' => 'px-pct',
			),
		),
	),
);

$this->sections['wpex_footer_widgets_titles'] = array(
	'title'    => esc_html__( 'Widget Titles', 'total' ),
	'panel'    => 'wpex_footer_widgets',
	'settings' => array(
		array(
			'id' => 'footer_headings',
			'transport' => 'postMessage',
			'default' => 'div',
			'control' => array(
				'label' => esc_html__( 'Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
					'span' => 'span',
					'div' => 'div',
				),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
		),
		array(
			'id' => 'footer_headings_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '.footer-widget .widget-title',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'footer_headings_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '.footer-widget .widget-title',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'footer_headings_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
				'description' => $padding_desc,
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '.footer-widget .widget-title',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'footer_headings_align',
			'transport' => 'postMessage',
			'control' =>  array(
				'type' => 'select',
				'label' => esc_html__( 'Text Align', 'total' ),
				'choices' => array(
					'' => esc_html__( 'Default','total' ),
					'left' => esc_html__( 'Left','total' ),
					'right' => esc_html__( 'Right','total' ),
					'center' => esc_html__( 'Center','total' ),
				),
				'active_callback' => 'wpex_cac_has_footer_widgets',
			),
			'inline_css' => array(
				'target' => '.footer-widget .widget-title',
				'alter' => 'text-align',
			),
		),
	),
);