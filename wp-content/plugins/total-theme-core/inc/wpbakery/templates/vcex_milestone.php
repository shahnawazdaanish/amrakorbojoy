<?php
/**
 * Visual Composer Milestone
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Define vars
$output = '';

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_milestone', $atts, $this );
extract( $atts );

// Milestone default args
extract( apply_filters( 'vcex_milestone_settings', array(
	'separator' => $separator,
	'decimal'   => $decimal_separator,
) ) );

// Sanitize data
if ( is_callable( $number ) ) {
	$number = intval( call_user_func( $number ) );
} else {
	$number = isset( $number ) ? do_shortcode( $number ) : '45';
}
$number = str_replace( ',', '', $number );
//$number = str_replace( '.', '', $number );

// Enqueue scripts
$this->enqueue_scripts();

// Turn duration into seconds
$speed = $speed/1000;

// Wrapper Classes
$wrap_classes = array( 'vcex-module', 'vcex-milestone', 'clr' );
if ( 'true' == $animated || 'yes' == $animated ) {
	$wrap_classes[] = 'vcex-animated-milestone';
}
if ( $classes ) {
	$wrap_classes[] = vcex_get_extra_class( $classes );
}
if ( $visibility ) {
	$wrap_classes[] = $visibility;
}
if ( $css_animation && 'none' != $css_animation ) {
	$wrap_classes[] = vcex_get_css_animation( $css_animation );
}
if ( $hover_animation ) {
	$wrap_classes[] = vcex_hover_animation_class( $hover_animation );
	vcex_enque_style( 'hover-animations' );
}
if ( $css ) {
	$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $css );
}

// Wrap style
$wrap_style = vcex_inline_style( array(
	'width'         => $width,
	'border_radius' => $border_radius,
) );

// Generate Icon if enabled
if ( 'true' == $enable_icon ) {

	$wrap_classes[] = 'vcex-ip-' . $icon_position;

	$icon_attrs = array(
		'class' => 'vcex-milestone-icon',
	);

	$icon_attrs['style'] = vcex_inline_style( array(
		'color'     => $icon_color,
		'font_size' => $icon_size,
	), false );

	$icon_html = '';

	$icon_html .= '<span' . vcex_parse_html_attributes( $icon_attrs ) . '>';

		if ( $icon_alternative_classes ) {

			$icon_html .= '<span class="' . esc_attr( do_shortcode( $icon_alternative_classes ) ) . '"></span>';

		} elseif ( $icon = vcex_get_icon_class( $atts, 'icon' ) ) {

			vcex_enqueue_icon_font( $icon_type, $icon );

			$icon_html .= '<span class="' . esc_attr( $icon ) . '" aria-hidden="true"></span>';

		}

	$icon_html .= '</span>';

}

// Parse wrap classes
$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_milestone', $atts );

// Begin Output
if ( 'true' == $url_wrap && $url ) :

	$output .= '<a href="' . esc_url( do_shortcode( $url ) ) . '" class="' . $wrap_classes . '"'
			. vcex_get_unique_id( $unique_id )
			. $wrap_style
			. vcex_html( 'rel_attr', $url_rel )
			. vcex_html( 'target_attr', $url_target );
	$output .= '>';

else :

	$output .= '<div class="'. $wrap_classes .'"'
			. vcex_get_unique_id( $unique_id )
			. $wrap_style;
	$output .= '>';

endif;

	$output .= '<div class="vcex-milestone-inner">';

		// Add icon for top/left/right positions
		if ( in_array( $icon_position, array( 'top', 'left', 'right' ) ) && isset( $icon_html ) ) {
			$output .= $icon_html;
		}

		// Open description element
		$output .= '<div class="vcex-milestone-desc">';

			// Load custom font
			if ( $number_font_family ) {
				vcex_enqueue_google_font( $number_font_family );
			}

			// Number Style
			$number_style = vcex_inline_style( array(
				'color'         => $number_color,
				'font_size'     => $number_size,
				'margin_bottom' => $number_bottom_margin,
				'font_weight'   => $number_weight,
				'font_family'   => $number_font_family,
			) );

			// Display number
			$output .= '<div class="vcex-milestone-number"'. $number_style .'>';

				if ( $before || 'true' == $enable_icon ) {

					$output .= '<span class="vcex-milestone-before">';

						if ( 'inline' == $icon_position && isset( $icon_html ) ) {
							$output .= $icon_html;
						}

						$output .= esc_html( do_shortcode( $before ) );

					$output .= '</span>';

				}

				// Get milestone js options

				$startval = floatval( do_shortcode( $startval ) );
				$startval = $startval ?  $startval : 0;

				$settings = array(
					'startVal'  => $startval,
					'endVal'    => floatval( do_shortcode( $number ) ),
					'duration'  => intval( $speed ),
					'decimals'  => intval( $decimals ),
					'separator' => wp_strip_all_tags( $separator ),
					'decimal'   => wp_strip_all_tags( $decimal ),
				);

				// Output milestone number
				$output .= '<span class="vcex-milestone-time vcex-countup" data-options="' . htmlspecialchars( wp_json_encode( $settings ) ) . '">' . $startval . '</span>';

				// Display after text if defined
				if ( $after ) {

					$output .= '<span class="vcex-milestone-after">' . esc_html( do_shortcode( $after ) ) . '</span>';

				}

			// Close number/after container
			$output .= '</div>';

			// Display caption
			if ( ! empty( $caption ) ) {

				// Load custom font
				if ( $caption_font_family ) {
					vcex_enqueue_google_font( $caption_font_family );
				}

				// Caption Style
				$caption_style = vcex_inline_style( array(
					'font_family' => $caption_font_family,
					'color'       => $caption_color,
					'font_size'   => $caption_size,
					'font_weight' => $caption_font,
				) );

				// Display caction with URL
				if ( $url && 'false' == $url_wrap ) {

					$output .= '<a href="' . esc_url( do_shortcode( $url ) ) . '" class="vcex-milestone-caption"' . vcex_html( 'rel_attr', $url_rel ) . '' . vcex_html( 'target_attr', $url_target ) . '' . $caption_style . '>' . do_shortcode( wp_kses_post( $caption ) ) . '</a>';

				}

				// Display caption without URL
				else {

					$output .= '<div class="vcex-milestone-caption"' . $caption_style . '>' . do_shortcode( wp_kses_post( $caption ) ) . '</div>';

				}

			}

		$output .= '</div>';

	$output .= '</div>';

// Close wrap
if ( 'true' == $url_wrap && $url ) :

	$output .= '</a>';

else :

	$output .= '</div>';

endif;

// @codingStandardsIgnoreLine
echo $output;
