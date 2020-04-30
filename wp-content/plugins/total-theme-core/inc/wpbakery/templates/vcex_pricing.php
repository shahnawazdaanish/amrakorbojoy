<?php
/**
 * Visual Composer Pricing
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
$atts = vcex_vc_map_get_attributes( 'vcex_pricing', $atts, $this );
extract( $atts );

// Define output var
$output = '';

// Wrapper attributes
$wrap_attrs = array(
	'class' => 'vcex-module vcex-pricing',
	'id'    => vcex_get_unique_id( $unique_id ),
);

// Wrapper Classes
if ( 'yes' == $featured ) {
	$wrap_attrs['class'] .= ' featured';
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_css_animation( $css_animation );
}
if ( $el_class ) {
	$wrap_attrs['class'] .= ' ' . vcex_get_extra_class( $el_class );
}
if ( $visibility ) {
	$wrap_attrs['class'] .= ' ' . $visibility;
}
if ( $hover_animation ) {
	$wrap_attrs['class'] .= ' ' . vcex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
if ( $css ) {
	$wrap_attrs['class'] .= ' ' . vcex_vc_shortcode_custom_css_class( $css );
}

// Apply filters to wrap class
$wrap_attrs['class'] = vcex_parse_shortcode_classes( $wrap_attrs['class'], 'vcex_pricing', $atts );

/*
 * Start Output
 */
$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . '>';

	/*
	 * Plan
	 */
	if ( $plan ) {

		$plan_attrs = array(
			'class' => 'vcex-pricing-header clr'
		);

		$plan_attrs['style'] = vcex_inline_style( array(
			'margin'         => $plan_margin,
			'padding'        => $plan_padding,
			'background'     => $plan_background,
			'color'          => $plan_color,
			'font_size'      => $plan_size,
			'font_weight'    => $plan_weight,
			'letter_spacing' => $plan_letter_spacing,
			'border'         => $plan_border,
			'text_transform' => $plan_text_transform,
			'font_family'    => $plan_font_family,
		), false );

		vcex_enqueue_google_font( $plan_font_family );

		if ( $responsive_data = vcex_get_module_responsive_data( $plan_size, 'font_size' ) ) {
			$plan_attrs['data-wpex-rcss'] = $responsive_data;
		}

		$output .= '<div' . vcex_parse_html_attributes( $plan_attrs ) . '>';

			$output .= wp_kses_post( do_shortcode( $plan ) );

		$output .= '</div>';

	}

	/*
	 * Cost
	 */
	if ( $cost ) {

		$cost_attrs = array(
			'class' => 'vcex-pricing-ammount'
		);

		$cost_attrs['style'] = vcex_inline_style( array(
			'color'       => $cost_color,
			'font_size'   => $cost_size,
			'font_weight' => $cost_weight,
		), false );

		if ( $responsive_data = vcex_get_module_responsive_data( $cost_size, 'font_size' ) ) {
			$cost_attrs['data-wpex-rcss'] = $responsive_data;
		}

		$cost_wrap_style = vcex_inline_style( array(
			'background'  => $cost_background,
			'padding'     => $cost_padding,
			'border'      => $cost_border,
			'font_family' => $cost_font_family,
		) );

		vcex_enqueue_google_font( $cost_font_family );

		$output .= '<div class="vcex-pricing-cost clr"' . $cost_wrap_style . '>';

			$output .= '<div' . vcex_parse_html_attributes( $cost_attrs ) . '>';

				$output .= do_shortcode( wp_kses_post( $cost ) );

			$output .= '</div>';

			// Per section
			if ( $per ) {

				$per_attrs = array(
					'class' => 'vcex-pricing-per'
				);

				$per_attrs['style'] = vcex_inline_style( array(
					'display'        => $per_display,
					'font_size'      => $per_size,
					'color'          => $per_color,
					'font_weight'    => $per_weight,
					'text_transform' => $per_transform,
					'font_family'    => $per_font_family
				), false );

				vcex_enqueue_google_font( $per_font_family );

				if ( $responsive_data = vcex_get_module_responsive_data( $per_size, 'font_size' ) ) {
					$per_attrs['data-wpex-rcss'] = $responsive_data;
				}

				$output .= '<div' . vcex_parse_html_attributes( $per_attrs ) . '>';

					$output .= do_shortcode( wp_kses_post( $per ) );

				$output .= '</div>';
			}

		$output .= '</div>';

	}

	/*
	 * Content
	 */
	if ( $content ) {

		$content_attrs = array(
			'class' => 'vcex-pricing-content clr',
		);

		$content_attrs['style'] = vcex_inline_style( array(
			'padding'     => $features_padding,
			'background'  => $features_bg,
			'border'      => $features_border,
			'color'       => $font_color,
			'font_size'   => $font_size,
			'font_family' => $font_family
		), false );

		vcex_enqueue_google_font( $font_family );

		if ( $responsive_data = vcex_get_module_responsive_data( $font_size, 'font_size' ) ) {
			$content_attrs['data-wpex-rcss'] = $responsive_data;
		}

		$output .= '<div' . vcex_parse_html_attributes( $content_attrs ) . '>';

			$output .= do_shortcode( wp_kses_post( $content ) );

		$output .= '</div>';

	}

	/*
	 * Button
	 */

	if ( $button_url && ! $custom_button ) {
		$button_url_temp = $button_url; // fallback for old option
		$button_url      = vcex_get_link_data( 'url', $button_url_temp );
	}

	if ( $button_url || $custom_button ) {

		// Set button url to false if custom_button isn't empty
		$button_url = $custom_button ? false : $button_url;

		// Button Wrap Style
		$button_wrap_style = vcex_inline_style( array(
			'padding'     => $button_wrap_padding,
			'border'      => $button_wrap_border,
			'background'  => $button_wrap_bg,
			'font_family' => $button_font_family,
		) );

		vcex_enqueue_google_font( $button_font_family );

		// Extra checks needed due to button_url sanitization
		if ( $button_url || $custom_button ) {

			$output .= '<div class="vcex-pricing-button"' . $button_wrap_style . '>';

				if ( $custom_button = vcex_parse_textarea_html( $custom_button ) ) {

					$output .= do_shortcode( $custom_button );

				} elseif ( $button_url ) {

					$button_title  = vcex_get_link_data( 'title', $button_url_temp );
					$button_target = vcex_get_link_data( 'target', $button_url_temp );
					$button_rel    = vcex_get_link_data( 'rel', $button_url_temp );

					// Define button attributes
					$button_attrs = array(
						'href'   => esc_url( do_shortcode( $button_url ) ),
						'title'  => esc_attr( do_shortcode( $button_title ) ),
						'target' => $button_target,
						'rel'    => $button_rel,
						'class'  => array( vcex_get_button_classes( $button_style, $button_style_color ) )
					);

					// Custom Button Classes
					if ( 'true' == $button_local_scroll ) {
						$button_attrs['class'][] = 'local-scroll-link';
					}
					if ( $button_transform ) {
						$button_attrs['class'][] = 'text-transform-' . esc_attr( $button_transform );
					}

					// Button Data attributes
					$hover_data = array();
					if ( $button_hover_bg_color ) {
						$hover_data['background'] = esc_attr( $button_hover_bg_color );
					}
					if ( $button_hover_color ) {
						$hover_data['color'] = esc_attr( $button_hover_color );
					}
					if ( $hover_data ) {
						$button_attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( $hover_data ) );
					}

					if ( $button_size && $responsive_data = vcex_get_module_responsive_data( $button_size, 'font_size' ) ) {
						$button_attrs['data-wpex-rcss'] = $responsive_data;
					}

					// Button Style
					$border_color = ( 'outline' == $button_style ) ? $button_color : '';
					$button_style = vcex_inline_style( array(
						'background'     => $button_bg_color,
						'color'          => $button_color,
						'letter_spacing' => $button_letter_spacing,
						'font_size'      => $button_size,
						'padding'        => $button_padding,
						'border_radius'  => $button_border_radius,
						'font_weight'    => $button_weight,
						'border_color'   => $border_color,
						'text_transform' => $button_transform,
					), false );

					// Add parsed button attributes to array
					$button_attrs['style']  = $button_style;

					$output .= '<a' . vcex_parse_html_attributes( $button_attrs ) . '>';

						// Get correct icon classes
						$button_icon_left  = vcex_get_icon_class( $atts, 'button_icon_left' );
						$button_icon_right = vcex_get_icon_class( $atts, 'button_icon_right' );

						if ( $button_icon_left || $button_icon_right ) {
							vcex_enqueue_icon_font( $icon_type, $button_icon_left );
							vcex_enqueue_icon_font( $icon_type, $button_icon_right );
						}

						/*
						 * Button Icon Left
						 */
						if ( $button_icon_left ) {

							$attrs = array(
								'class' => 'vcex-icon-wrap theme-button-icon-left',
							);

							if ( $button_icon_left_transform ) {

								$attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( array(
									'parent'    => '.theme-button',
									'transform' => 'translateX(' . vcex_validate_font_size( $button_icon_left_transform ) . ')',
								) ) );

							}

							$output .= '<span' . vcex_parse_html_attributes( $attrs ) . '>';

								$output .= '<span class="' . esc_attr( $button_icon_left ) . '"></span>';

							$output .= '</span>';

						}

						$output .= do_shortcode( $button_text );

						/*
						 * Button Icon Right
						 */
						if ( $button_icon_right ) {

							$attrs = array(
								'class' => 'vcex-icon-wrap theme-button-icon-right',
							);

							if ( $button_icon_right_transform ) {
								$attrs['data-wpex-hover'] = htmlspecialchars( wp_json_encode( array(
									'parent'    => '.theme-button',
									'transform' => 'translateX(' . vcex_validate_font_size( $button_icon_right_transform ) . ')',
								) ) );
							}

							$output .= '<span' . vcex_parse_html_attributes( $attrs ) . '>';

								$output .= '<span class="' . esc_attr( $button_icon_right ) . '"></span>';

							$output .= '</span>';

						}

					$output .= '</a>';

				}

			$output .= '</div>';

		}

	} // End button checks

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
