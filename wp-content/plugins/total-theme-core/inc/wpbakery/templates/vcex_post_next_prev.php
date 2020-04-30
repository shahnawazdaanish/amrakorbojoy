<?php
/**
 * Visual Composer Post Next&Previous Links
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.9
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
$atts = vcex_vc_map_get_attributes( 'vcex_post_next_prev', $atts, $this );

$prev = $next = $icon_left = $icon_right = $prev_format = $next_format = '';

$reverse       = $atts['reverse_order'];
$in_same_term  = ( 'true' == $atts['in_same_term'] ) ? true : false;
$same_term_tax = $in_same_term ? $atts['same_term_tax'] : 'category';

$classes = 'vcex-post-next-prev clr';

if ( $atts['align'] ) {
	$classes .= ' text' . $atts['align'];
}

if ( 'icon' == $atts['link_format'] ) {
	$classes .= ' vcex-icon-only';
}

// Extra classname
if ( $atts['el_class'] ) {
	$classes .= ' ' . vcex_get_extra_class( $atts['el_class'] );
}

$inline_css = vcex_inline_style( array(
	'font_size' => $atts['font_size'],
) );

$output = '<div class="'. $classes . '"' . $inline_css . '>';

	if ( $atts['icon_style'] ) {
		$icon_left = '<span class="theme-button-icon-left ticon ticon-' . $atts['icon_style'] . '-left"></span>';
		$icon_right = '<span class="theme-button-icon-right ticon ticon-' . $atts['icon_style'] . '-right"></span>';
	}

	$button_class = vcex_get_button_classes( $atts['button_style'], $atts['button_color'] );

	if ( 'true' == $atts['previous_link'] ) {

		$get_prev = get_previous_post( $in_same_term, '', $same_term_tax );

		if ( $get_prev ) {

			if ( 'icon' == $atts['link_format'] ) {
				$prev_format = ( 'true' == $reverse ) ? $icon_right : $icon_left;
			} elseif ( 'title' == $atts['link_format'] ) {
				$title = get_the_title( $get_prev->ID );
				$prev_format = ( 'true' == $reverse ) ? $title . $icon_right : $icon_left . $title;
			} elseif ( 'custom' == $atts['link_format'] ) {
				$prev_format = ( 'true' == $reverse ) ? esc_html( $atts['previous_link_custom_text'] ) . $icon_right : $icon_left . esc_html( $atts['previous_link_custom_text'] ) ;
			}

			$prev = '<a href="' . esc_url( get_permalink( $get_prev->ID ) ) . '" class="' . $button_class . '">' . $prev_format . '</a>';

		}

	}

	if ( 'true' == $atts['next_link'] ) {

		$get_next = get_next_post( $in_same_term, '', $same_term_tax );

		if ( $get_next ) {

			if ( 'icon' == $atts['link_format'] ) {
				$next_format = ( 'true' == $reverse ) ? $icon_left : $icon_right;
			} elseif ( 'title' == $atts['link_format'] ) {
				$title = get_the_title( $get_next->ID );
				$next_format = ( 'true' == $reverse ) ? $icon_left . $title : $title . $icon_right;
			} elseif ( 'custom' == $atts['link_format'] ) {
				$next_format = ( 'true' == $reverse ) ? $icon_left . esc_html( $atts['next_link_custom_text'] ) : esc_html( $atts['next_link_custom_text'] ) . $icon_right;
			}

			$next = '<a href="' . esc_url( get_permalink( $get_next->ID ) ) . '" class="' . $button_class . '">' . $next_format . '</a>';

		}

	}

	if ( 'true' == $reverse ) {
		$output .= '<div class="vcex-col">' . $next .'</div>';
		$output .= '<div class="vcex-col">' . $prev .'</div>';
	} else {
		$output .= '<div class="vcex-col">' . $prev .'</div>';
		$output .= '<div class="vcex-col">' . $next .'</div>';
	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
