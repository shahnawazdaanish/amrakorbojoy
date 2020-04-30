<?php
/**
 * VC Select Buttons Parameter
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vcex_select_buttons( $settings, $value ) {

	$wrap_classes = 'vcex-select-buttons-param vcex-custom-select vcex-noselect clr';

	$choices = $settings['choices'] ? $settings['choices'] : array();

	if ( 'button_size' == $choices ) {
		$choices = array(
			''       => esc_html__( 'Default', 'total-theme-core' ),
			'small'  => esc_html__( 'Small', 'total-theme-core' ),
			'medium' => esc_html__( 'Medium', 'total-theme-core' ),
			'large'  => esc_html__( 'Large', 'total-theme-core' ),
		);
	} elseif ( 'button_layout' == $choices ) {
		$choices = array(
			'inline' => esc_html__( 'Inline', 'total-theme-core' ),
			'block' => esc_html__( 'Block', 'total-theme-core' ),
			'expanded' => esc_html__( 'Expanded', 'total-theme-core' ),
		);
	} elseif ( 'link_target' == $choices ) {
		$choices = array(
			'self' => esc_html__( 'Same tab', 'total-theme-core' ),
			'_blank' => esc_html__( 'New tab', 'total-theme-core' )
		);
	} elseif( 'html_tag' == $choices ) {
		$choices = array(
			''     => esc_html__( 'Default', 'total-theme-core' ),
			'h1'   => 'h1',
			'h2'   => 'h2',
			'h3'   => 'h3',
			'h4'   => 'h4',
			'h5'   => 'h5',
			'div'  => 'div',
			'span' => 'span',
		);
	} elseif ( 'masonry_layout_mode' == $choices ) {
		$choices = array(
			'masonry' => esc_html__( 'Masonry', 'total-theme-core' ),
			'fitRows' => esc_html__( 'Fit Rows', 'total-theme-core' ),
		);
	} elseif ( 'filter_layout_mode' == $choices ) {
		$choices = array(
			'masonry' => esc_html__( 'Masonry', 'total-theme-core' ),
			'fitRows' => esc_html__( 'Fit Rows', 'total-theme-core' ),
		);
	} elseif ( 'grid_style' == $choices ) {
		$choices = array(
			'fit_columns' => esc_html__( 'Fit Columns', 'total-theme-core' ),
			'masonry' => esc_html__( 'Masonry', 'total-theme-core' ),
		);
	} elseif ( 'slider_animation' == $choices ) {
		$choices = array(
			'fade_slides' => esc_html__( 'Fade', 'total-theme-core' ),
			'slide' => esc_html__( 'Slide', 'total-theme-core' ),
		);
	} elseif( 'text_decoration' == $choices ) {
		$choices = vcex_text_decorations();
	} elseif( 'font_style' == $choices ) {
		$choices = vcex_font_styles();
	} elseif( 'bullet_styles' == $choices ) {
		if ( function_exists( 'wpex_asset_url' ) ) {
			$choices = array(
				'check'  => '<img src=" ' . wpex_asset_url( 'images/check.png' ) . '" />',
				'blue'   => '<img src=" ' . wpex_asset_url( 'images/bullets-blue.png' ) . '" />',
				'gray'   => '<img src=" ' . wpex_asset_url( 'images/bullets-gray.png' ) . '" />',
				'purple' => '<img src=" ' . wpex_asset_url( 'images/bullets-purple.png' ) . '" />',
				'red'    => '<img src=" ' . wpex_asset_url( 'images/bullets-red.png' ) . '" />',
			);
			$wrap_classes .= ' vcex-no-active-bg';
		}
	} elseif ( is_callable( $choices ) ) {
		$choices = call_user_func( $choices );
	}

	if ( ! $choices ) {
		$output .= '<input type="text" class="wpb_vc_param_value '
				. esc_attr( $settings['param_name'] ) . ' '
				. esc_attr( $settings['type'] ) . '" name="' . esc_attr( $settings['param_name'] ) . '" value="' . esc_attr( $value ) . '"/>';
	}

	$output = '<div class="' . $wrap_classes . '">';

	if ( ! $value ) {
		if ( isset( $settings['std'] ) ) {
			$value = $settings['std'];
		} else {
			$temp_choices = $choices;
			reset( $temp_choices );
			$value = key( $temp_choices );
		}
	}

	foreach ( $choices as $id => $label ) {

		$class = 'vcex-opt';
		if ( $id == $value ) {
			$class .= ' vcex-active';
		}
		if ( $id ) {
			$class .= ' vcex-opt-' . $id;
		}

		if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
			$label = str_replace( 'ticon', 'fa', $label );
		}

		$output .= '<button class="' . esc_attr( $class ) . '" data-value="'. esc_attr( $id )  .'">' . wp_kses_post( $label ) . '</button>';

	}

	$output .= '<input name="' . esc_attr( $settings['param_name'] ) . '" class="vcex-hidden-input wpb-input wpb_vc_param_value  ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . esc_attr( $value ) . '" />';

	$output .= '</div>';

	return $output;

}
vc_add_shortcode_param(
	'vcex_select_buttons',
	'vcex_select_buttons',
	vcex_asset_url( 'js/backend/vcex-params.min.js?v=' . TTC_VERSION )
);