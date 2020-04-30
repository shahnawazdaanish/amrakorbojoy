<?php
/**
 * Visual Composer Terms Grid
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_terms', $atts, $this );

// Taxonomy is required
if ( ! $atts['taxonomy'] ) {
	return;
}

// Load Google Fonts if needed
if ( $atts['button_font_family'] ) {
	vcex_enqueue_google_font( $atts['button_font_family'] );
}

// Get module style
$module_style = isset( $atts['style'] ) ? $atts['style'] : 'buttons';

// Define terms
$terms = array();

// Get featured term
if ( 'true' == $atts['first_term_only'] && class_exists( 'WPSEO_Primary_Term' ) ) {
	$primary_term = new WPSEO_Primary_Term( $atts['taxonomy'], $post_id );
	$primary_term = $primary_term->get_primary_term();
	if ( $primary_term ) {
		$get_primary_term = get_term( $primary_term, $atts['taxonomy'] );
		$terms = array( $get_primary_term );
	}
}

// If terms is empty lets query them
if ( ! $terms ) {

	// Query arguments
	$query_args = array(
		'order'   => $atts['order'],
		'orderby' => $atts['orderby'],
		'fields'  => 'all',
	);

	// Apply filters to query args
	$query_args = apply_filters( 'vcex_post_terms_query_args', $query_args, $atts );

	// Get terms
	$terms = wp_get_post_terms( vcex_get_the_ID(), $atts['taxonomy'], $query_args );

	// Get first term only
	if ( 'true' == $atts['first_term_only'] ) {
		$terms = array( $terms[0] );
	}

}

// Terms needed
if ( ! $terms || is_wp_error( $terms ) ) {
	return;
}

// Wrap classes
$wrap_classes = 'vcex-post-terms clr';
if ( $atts['visibility'] ) {
	$wrap_classes .= ' '. $atts['visibility'];
}
if ( $atts['classes'] ) {
	$wrap_classes .= ' '. vcex_get_extra_class( $atts['classes'] );
}
if ( 'center' == $atts['button_align'] ) {
	$wrap_classes .= ' textcenter';
}

// Define output var
$output = '';

// Get total count
$tcount = count( $terms );

// VC filter
$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_terms', $atts );

// Wrap style
if ( 'buttons' !== $module_style ) {

	$wrap_style = vcex_inline_style( array(
		'color'          => $atts['button_color'],
		'font_size'      => $atts['button_font_size'],
		'font_weight'    => $atts['button_font_weight'],
		'text_transform' => $atts['button_text_transform'],
	) );

} else {

	$wrap_style  = '';

}

// Begin output
$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $atts['unique_id'] ) . $wrap_style . '>';

	// Define link vars
	$link_class = $link_style = '';
	$link_hover_data = array();

	// Button Style Classes and inline styles
	if ( 'buttons' == $module_style ) {

		$link_class .= vcex_get_button_classes(
			$atts['button_style'],
			$atts['button_color_style'],
			$atts['button_size'],
			$atts['button_align']
		);

		if ( $atts['css_animation'] && 'none' != $atts['css_animation'] ) {
			$link_class .= ' ' . vcex_get_css_animation( $atts['css_animation'] );
		}

		// Button Style
		$link_style = vcex_inline_style( array(
			'margin'         => $atts['button_margin'],
			'color'          => $atts['button_color'],
			'background'     => $atts['button_background'],
			'padding'        => $atts['button_padding'],
			'font_size'      => $atts['button_font_size'],
			'font_weight'    => $atts['button_font_weight'],
			'border_radius'  => $atts['button_border_radius'],
			'text_transform' => $atts['button_text_transform'],
		) );

		// Button data
		if ( $atts['button_hover_background'] ) {
			$link_hover_data['background'] = esc_attr( $atts['button_hover_background'] );
		}
		if ( $atts['button_hover_color'] ) {
			$link_hover_data['color'] = esc_attr( $atts['button_hover_color'] );
		}
		$link_hover_data = $link_hover_data ? htmlspecialchars( wp_json_encode( $link_hover_data ) ) : '';

	}

	// Get child_of value
	if ( ! empty( $atts['child_of'] ) ) {
		$get_child_of = get_term_by( 'slug', trim( $atts['child_of'] ), $atts['taxonomy'] );
		if ( $get_child_of ) {
			$child_of_id = $get_child_of->term_id;
		}
	}


	// Get excluded terms
	if ( ! empty( $atts['exclude_terms'] ) ) {
		$exclude_terms = preg_split( '/\,[\s]*/', $atts['exclude_terms'] );
	} else {
		$exclude_terms = array();
	}

	// Before Text
	if ( 'inline' == $module_style && ! empty( $atts['before_text'] ) ) {
		$output .= '<span class="vcex-label">' . do_shortcode( wp_strip_all_tags( $atts['before_text'] ) ) . '</span> ';
	}

	// Open UL list
	elseif ( 'ul' == $module_style ) {
		$output .= '<ul>';
	}

	// Open OL list
	elseif ( 'ol' == $module_style ) {
		$output .= '<ol>';
	}

	// Loop through terms
	$terms_count = 0;
	foreach ( $terms as $term ) :
		$terms_count ++;

		// Skip items that aren't a child of a specific parent.
		if ( ! empty( $child_of_id ) && $term->parent != $child_of_id ) {
			continue;
		}

		// Skip excluded terms
		if ( in_array( $term->slug, $exclude_terms ) ) {
			continue;
		}

		// Add li tags
		if ( in_array( $module_style, array( 'ul', 'ol' ) ) ) {
			$output .= '<li>';
		}

		// Open term element
		if ( 'true' == $atts['archive_link'] ) {

			$output .= '<a' . vcex_parse_html_attributes( array(
				'href'            => esc_url( get_term_link( $term, $atts['taxonomy'] ) ),
				'class'           => esc_attr( $link_class ),
				'style'           => $link_style,
				'data-wpex-hover' => $link_hover_data,
			) ) . '>';

		} else {

			$output .= '<span' . vcex_parse_html_attributes( array(
				'class' => esc_attr( $link_class ),
				'style' => $link_style,
				'data-wpex-hover' => $link_hover_data,
			) ) . '>';

		}

		// Display title
		$output .= esc_html( $term->name );

		// Close term element
		if ( 'true' == $atts['archive_link'] ) {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}

		// Add spacer for inline style
		if ( 'inline' == $module_style && $terms_count < count( $terms ) ) {
			$spacer = '&comma;';
			$custom_spacer = ! empty( $atts['spacer'] ) ? $atts['spacer'] : apply_filters( 'vcex_post_terms_default_spacer', '' );
			if ( $custom_spacer ) {
				$output .= ' ';
				$spacer = $custom_spacer;
			}
			$output .= '<span class="vcex-spacer">' . do_shortcode( wp_strip_all_tags( $spacer ) ) . '</span> ';
		}

		// Close li tags
		if ( in_array( $module_style, array( 'ul', 'ol' ) ) ) {
			$output .= '</li>';
		}

	endforeach;

	// Close UL list
	if ( 'ul' == $module_style ) {
		$output .= '</ul>';
	}

	// Open OL list
	elseif ( 'ol' == $module_style ) {
		$output .= '</ol>';
	}

// Close main wrapper
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
