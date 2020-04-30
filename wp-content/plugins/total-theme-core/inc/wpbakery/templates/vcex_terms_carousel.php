<?php
/**
 * Visual Composer Testimonials Carousel
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Define output
$output = '';

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_terms_carousel', $atts, $this );

// Taxonomy is required
if ( ! $atts[ 'taxonomy' ] ) {
	return;
}

// Fix for fallback parent terms switched from bool to string
$atts[ 'parent_terms' ] = ( $atts[ 'parent_terms' ] && 'false' != $atts[ 'parent_terms' ] ) ? true : false;

// Term arguments
$query_args = array();
if ( $atts[ 'parent_terms' ] ) {
	$query_args[ 'parent' ] = 0;
}
if ( 'false' == $atts[ 'hide_empty' ] ) {
	$query_args[ 'hide_empty' ] = false;
}
if ( $atts[ 'child_of' ] ) {
	$child_of = get_term_by( 'slug', $atts[ 'child_of' ], $atts[ 'taxonomy' ] );
	if ( $child_of && ! is_wp_error( $child_of ) ) {
		$query_args[ 'child_of' ] = $child_of->term_id;
	}
}

// Get terms
$query_args = apply_filters( 'vcex_terms_carousel_query_args', $query_args, $atts );
$terms = get_terms( $atts[ 'taxonomy' ], $query_args );

// Terms needed
if ( ! $terms || is_wp_error( $terms ) ) {
	return;
}

// Enqueue scripts
vcex_enqueue_carousel_scripts();

// Get excluded terms
$atts[ 'exclude_terms' ] = $atts[ 'exclude_terms' ] ? preg_split( '/\,[\s]*/', $atts[ 'exclude_terms' ] ) : array();

// Main Classes
$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'vcex-terms-carousel', 'clr', 'owl-carousel' );

// Arrow style
$atts[ 'arrows_style' ] = $atts[ 'arrows_style' ] ? $atts[ 'arrows_style' ] : 'default';
$wrap_classes[] = 'arrwstyle-'. $atts[ 'arrows_style' ];

// Arrow position
if ( $atts[ 'arrows_position' ] && 'default' != $atts[ 'arrows_position' ] ) {
	$wrap_classes[] = 'arrwpos-'. $atts[ 'arrows_position' ];
}

// Visiblity
if ( $atts[ 'visibility' ] ) {
	$wrap_classes[] = $atts[ 'visibility' ];
}

// CSS animations
if ( $atts[ 'css_animation' ] && 'none' != $atts[ 'css_animation' ] ) {
	$wrap_classes[] = vcex_get_css_animation( $atts[ 'css_animation' ] );
}

// Custom Classes
if ( $atts[ 'classes' ] ) {
	$wrap_classes[] = vcex_get_extra_class( $atts[ 'classes' ] );
}

// Disable autoplay
if ( vcex_vc_is_inline() || '1' == count( $terms ) ) {
	$atts['auto_play'] = false;
}

// Turn arrays into strings
$wrap_classes = implode( ' ', $wrap_classes );

// Typography
$title_typo        = vcex_parse_typography_param( $atts[ 'title_typo' ] );
$title_tag_escaped = ! empty( $title_typo[ 'tag' ] ) ? esc_attr( $title_typo[ 'tag' ] ) : 'h2';
$description_typo  = vcex_parse_typography_param( $atts[ 'description_typo' ] );

// Remove useless align
if ( isset( $title_typo[ 'text_align' ] ) && 'left' == $title_typo[ 'text_align' ] ) {
	unset( $title_typo[ 'text_align' ] );
}
if ( isset( $description_typo[ 'text_align' ] ) && 'left' == $description_typo[ 'text_align' ] ) {
	unset( $description_typo[ 'text_align' ] );
}

// Load Google Fonts if needed
if ( $atts[ 'title_font_family' ] ) {
	vcex_enqueue_google_font( $atts[ 'title_font_family' ] );
}
if ( $atts[ 'description_font_family' ] ) {
	vcex_enqueue_google_font( $atts[ 'description_font_family' ] );
}

// Define post type based on the taxonomy
$taxonomy  = get_taxonomy( $atts[ 'taxonomy' ] );
$post_type = $taxonomy->object_type[0];

// Entry CSS wrapper
if ( $atts[ 'entry_css' ] ) {
	$entry_css_class = vcex_vc_shortcode_custom_css_class( $atts[ 'entry_css' ] );
}

// Image classes
$media_classes = array( 'vcex-terms-grid-entry-image', 'wpex-clr' );
if ( 'true' == $atts[ 'title_overlay' ] && 'true' == $atts[ 'img' ] ) {
	$media_classes[] = 'vcex-has-overlay';
}
if ( $atts[ 'img_filter' ] ) {
	$media_classes[] = vcex_image_filter_class( $atts[ 'img_filter' ] );
}
if ( $atts[ 'img_hover_style' ] ) {
	$media_classes[] = vcex_image_hover_classes( $atts[ 'img_hover_style' ] );
}
$media_classes = implode( ' ', $media_classes );

// Title style
$title_style = array(
	'font_family'   => $atts[ 'title_font_family' ],
	'font_weight'   => $atts[ 'title_font_weight' ],
	'margin_bottom' => $atts[ 'title_bottom_margin' ],
);
$title_style = $title_typo + $title_style;
$title_style = vcex_inline_style( $title_style );

// Description style
$description_font_family = array( 'font_family' => $atts[ 'description_font_family' ] );
$description_typo        = $description_typo + $description_font_family;
$description_style       = vcex_inline_style( $description_typo );


// Button design and classes if enabled
if ( 'true' == $atts[ 'button' ] ) {

	$button_data = array();

	$button_text = $atts[ 'button_text' ] ? $atts[ 'button_text' ] : esc_html__( 'visit category', 'total' );

	$button_align = $atts[ 'button_align' ] ? ' text' . $atts[ 'button_align' ]  : '';

	$button_classes = vcex_get_button_classes( $atts[ 'button_style' ], $atts[ 'button_style_color' ] );

	$button_style = vcex_inline_style( array(
		'background'    => $atts[ 'button_background' ],
		'color'         => $atts[ 'button_color' ],
		'font_size'     => $atts[ 'button_size' ],
		'padding'       => $atts[ 'button_padding' ],
		'border_radius' => $atts[ 'button_border_radius' ],
		'margin'        => $atts[ 'button_margin' ],
	) );

	$button_hover_data = array();
	if ( $atts[ 'button_hover_background' ] ) {
		$button_hover_data[ 'background' ] = $atts[ 'button_hover_background' ];
	}
	if ( $atts[ 'button_hover_color' ] ) {
		$button_hover_data[ 'color' ] = $atts[ 'button_hover_color' ];
	}
	if ( $button_hover_data ) {
		$button_hover_data = htmlspecialchars( wp_json_encode( $button_hover_data ) );
	}

}

// VC filter
$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_terms_carousel', $atts );

// Display header if enabled
if ( $atts['header_style'] ) {

	$output .= vcex_get_module_header( array(
		'style'   => $atts['header_style'],
		'content' => $atts['header'],
		'classes' => array( 'vcex-module-heading vcex_terms_carousel-heading' ),
	) );

}

// Begin output
$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_terms_carousel' ) . '"' . vcex_get_unique_id( $atts[ 'unique_id' ] ) . '>';

	// Loop through terms
	foreach( $terms as $term ) :

		// Excluded
		if ( in_array( $term->slug, $atts[ 'exclude_terms' ] ) ) {
			continue;
		}

		// Term data
		$term_link = get_term_link( $term, $taxonomy );

		// Begin entry output
		$output .= '<div class="vcex-terms-carousel-entry clr term-' . absint( $term->term_id ) . ' term-' . esc_attr( $term->slug ) . '">';

		// Entry css wrapper
		if ( $atts[ 'entry_css' ] && $entry_css_class ) {

			$output .= '<div class="' . esc_attr( $entry_css_class ) . '">';

		}

			// Display image if enabled
			if ( 'true' == $atts[ 'img' ] ) :

				// Check meta for featured image
				$img_id = '';

				// Get term thumbnail id
				$img_id = vcex_get_term_thumbnail_id( $term->term_id );

				// Get woo product image
				if ( 'product' == $post_type ) {
					$img_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				}

				// Image not defined via meta, display image from first post in term
				if ( ! $img_id ) :

					// Query first post in term
					$first_post = new WP_Query( array(
						'post_type'      => $post_type,
						'posts_per_page' => '1',
						'no_found_rows'  => true,
						'tax_query'      => array(
							array(
								'taxonomy' => $term->taxonomy,
								'field'    => 'id',
								'terms'    => $term->term_id,
							)
						),
					) );

					// Get featured image of first post
					if ( $first_post->have_posts() ) {

						while ( $first_post->have_posts() ) : $first_post->the_post();

							$img_id = get_post_thumbnail_id();

						endwhile;

					}

					// Reset query
					wp_reset_postdata();

				endif;

				if ( $img_id ) :

					$output .= '<div class="' . esc_attr( $media_classes ) . '">';

						$output .= '<a href="' . esc_url( $term_link ) . '">';

							// Display post thumbnail
							$output .= vcex_get_post_thumbnail( array(
								'attachment' => $img_id,
								'alt'        => $term->name,
								'width'      => $atts[ 'img_width' ],
								'height'     => $atts[ 'img_height' ],
								'crop'       => $atts[ 'img_crop' ],
								'size'       => $atts[ 'img_size' ],
							) );

							// Overlay title
							if ( 'true' == $atts[ 'title_overlay' ]
								&& 'true' == $atts[ 'title' ]
								&& ! empty( $term->name )
							) :

								$output .= '<div class="vcex-terms-grid-entry-overlay wpex-clr">';

									$output .= '<div class="vcex-terms-grid-entry-overlay-table wpex-clr">';

										$output .= '<div class="vcex-terms-grid-entry-overlay-cell wpex-clr">';

											$output .= '<' . $title_tag_escaped . ' class="vcex-terms-grid-entry-title entry-title"' . $title_style . '>';

												$output .= '<span>' . esc_html( $term->name ) . '</span>';

												if ( 'true' == $atts[ 'term_count' ] ) {

													$output .= '<span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';

												}

											$output .= '</' . $title_tag_escaped . '>';

										$output .= '</div>';

									$output .= '</div>';

								$output .= '</div>';

							endif;

						$output .= '</a>';
					$output .= '</div>';

				endif; // End img ID check

			endif; // End image check

			// Inline title and description
			if ( 'false' == $atts[ 'title_overlay' ] || 'false' == $atts[ 'img' ] ) :

				// Show title
				if ( 'false' == $atts[ 'title_overlay' ] && 'true' == $atts[ 'title' ] && ! empty( $term->name ) ) :

					$output .= '<' . $title_tag_escaped . ' class="vcex-terms-grid-entry-title entry-title"' . $title_style . '>';

						$output .= '<a href="' . esc_url( $term_link ) . '" title="' . esc_attr( $term->name ) . '">';

							$output .= esc_html( $term->name );

							if ( 'true' == $atts[ 'term_count' ] ) {

								$output .= ' <span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';

							}

						$output .= '</a>';

					$output .= '</' . $title_tag_escaped . '>';

				endif;

				// Display term description
				if ( 'true' == $atts[ 'description' ] && ! empty( $term->description ) ) :

					$output .= '<div class="vcex-terms-grid-entry-excerpt clr"' . $description_style . '>';

						$output .= do_shortcode( wp_kses_post( $term->description ) );

					$output .= '</div>';

				endif;

				// Display button if enabled
				if ( 'true' == $atts[ 'button' ] ) :

					$output .= '<div class="vcex-terms-grid-entry-button clr' . esc_attr( $button_align ) . '">';

						$button_attrs = array(
							'href'            => esc_url( $term_link ),
							'class'           => esc_attr( $button_classes ),
							'style'           => $button_style,
							'rel'             => 'bookmark',
							'data-wpex-hover' => $button_hover_data,
						);

						$output .= '<a' . vcex_parse_html_attributes( $button_attrs ) . '>';

							$output .= do_shortcode( wp_kses_post( $button_text ) );

						$output .= '</a>';

					$output .= '</div>';

				endif;

			endif;

		$output .= '</div>';

		// Close entry
		if ( $atts[ 'entry_css' ] && $entry_css_class ) {

			$output .= '</div>';

		}

	endforeach;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
