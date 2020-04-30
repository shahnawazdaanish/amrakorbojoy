<?php
/**
 * Visual Composer Terms Grid
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Define output var
$output = '';

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_terms_grid', $atts, $this );

// Taxonomy is required
if ( ! $atts[ 'taxonomy' ] ) {
	return;
}

// Sanitize data
$title_typo = vcex_parse_typography_param( $atts[ 'title_typo' ] );
$title_font_family = $atts[ 'title_font_family' ] ? $atts[ 'title_font_family' ] : $title_typo[ 'font_family' ]; // Fallback
$atts[ 'parent_terms' ] = ( $atts[ 'parent_terms' ] && 'false' != $atts[ 'parent_terms' ] ) ? 'true' : 'false'; // Fallback

$atts[ 'title_tag' ] = ! empty( $title_typo[ 'tag' ] ) ? $title_typo[ 'tag' ] : 'h2';
$description_typo = vcex_parse_typography_param( $atts[ 'description_typo' ] );
$description_font_family = $atts[ 'description_font_family' ] ? $atts[ 'description_font_family' ] : $description_typo[ 'font_family' ]; // Fallback
$atts[ 'exclude_terms' ] = $atts[ 'exclude_terms' ] ? preg_split( '/\,[\s]*/', $atts[ 'exclude_terms' ] ) : array();


// Remove useless align
if ( isset( $title_typo[ 'text_align' ] ) && 'left' == $title_typo[ 'text_align' ] ) {
	unset( $title_typo[ 'text_align' ] );
}
if ( isset( $description_typo[ 'text_align' ] ) && 'left' == $description_typo[ 'text_align' ] ) {
	unset( $description_typo[ 'text_align' ] );
}

// Load Google Fonts if needed
if ( $atts[ 'title_font_family' ] ) {
	unset( $title_typo[ 'font_family' ] ); // Fallback
	vcex_enqueue_google_font( $atts[ 'title_font_family' ] );
}
if ( $atts[ 'description_font_family' ] ) {
	unset( $description_typo[ 'font_family' ] ); // Fallback
	vcex_enqueue_google_font( $atts[ 'description_font_family' ] );
}

// Term arguments
$query_args = array(
	'order'      => $atts[ 'order' ],
	'orderby'    => $atts[ 'orderby' ],
	'hide_empty' => ( 'false' == $atts[ 'hide_empty' ] ) ? false : true,
);
if ( 'true' == $atts[ 'parent_terms' ] ) {
	$query_args[ 'parent' ] = 0;
}
if ( $atts[ 'child_of' ] ) {
	$child_of = get_term_by( 'slug', $atts[ 'child_of' ], $atts[ 'taxonomy' ] );
	if ( $child_of && ! is_wp_error( $child_of ) ) {
		$query_args[ 'child_of' ] = $child_of->term_id;
	}
}

// Get terms for current post only
if ( 'true' == $atts[ 'get_post_terms' ] ) {
	$query_args['object_ids'] = vcex_get_the_ID();
}

// Get terms
$query_args = apply_filters( 'vcex_terms_grid_query_args', $query_args, $atts );
$terms = get_terms( $atts[ 'taxonomy' ], $query_args );

// Terms needed
if ( ! $terms || is_wp_error( $terms ) ) {
	return;
}

// Define post type based on the taxonomy
$taxonomy  = get_taxonomy( $atts[ 'taxonomy' ] );
$post_type = $taxonomy->object_type[0];

// Wrap classes
$wrap_classes = array( 'vcex-module', 'vcex-terms-grid', 'wpex-row', 'clr' );
if ( 'masonry' == $atts[ 'grid_style' ] ) {
	$wrap_classes[] = 'vcex-isotope-grid';
	vcex_enqueue_isotope_scripts();
}
if ( $atts[ 'columns_gap' ] ) {
	$wrap_classes[] = 'gap-' . $atts[ 'columns_gap' ];
}
if ( $atts[ 'visibility' ] ) {
	$wrap_classes[] = $atts[ 'visibility' ];
}
if ( $atts[ 'classes' ] ) {
	$wrap_classes[] = vcex_get_extra_class( $atts[ 'classes' ] );
}
$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_terms_grid', $atts );

// Entry classes
$entry_classes = array( 'vcex-terms-grid-entry', 'clr' );
if ( 'masonry' == $atts[ 'grid_style' ] ) {
	$entry_classes[] = 'vcex-isotope-entry';
}
$entry_classes[] = vcex_get_grid_column_class( $atts );;
if ( 'false' == $atts[ 'columns_responsive' ] ) {
	$entry_classes[] = 'nr-col';
} else {
	$entry_classes[] = 'col';
}
if ( $atts[ 'css_animation' ] && 'none' != $atts[ 'css_animation' ] ) {
	$entry_classes[] = vcex_get_css_animation( $atts[ 'css_animation' ] );
}
$entry_classes = implode( ' ', $entry_classes );

// Entry CSS wrapper
$entry_css_class = $atts[ 'entry_css' ] ? vcex_vc_shortcode_custom_css_class( $atts[ 'entry_css' ] ) : '';

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
$overlay_style = $atts['overlay_style'];
if ( $overlay_style ) {
	$media_classes[] = vcex_image_overlay_classes( $overlay_style );
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

// Display header if enabled
if ( $atts['header'] ) {

	$output .= vcex_get_module_header( array(
		'style'   => $atts['header_style'],
		'content' => $atts['header'],
		'classes' => array( 'vcex-module-heading vcex_terms_grid-heading' ),
	) );

}

// Begin output
$output .= '<div class="' . esc_attr( $wrap_classes ) . '">';

	// Start counter
	$counter = 0;

	// Loop through terms
	foreach( $terms as $term ) :

		$term_link        = get_term_link( $term, $taxonomy );
		$term_description = do_shortcode( wp_kses_post( $term->description ) );

		// Excluded
		if ( in_array( $term->slug, $atts[ 'exclude_terms' ] ) ) {
			continue;
		}

		// Add to counter
		$counter++;

		$output .= '<div class="' . esc_attr( $entry_classes ) . ' term-' . absint( $term->term_id ) . ' term-' . esc_attr( $term->slug ) . ' col-' . $counter . '">';

			if ( $entry_css_class ) {
				$output .= '<div class="' . esc_attr( $entry_css_class ) . '">';
			}

				// Display image if enabled
				if ( 'true' == $atts[ 'img' ] ) :

					// Get term thumbnail
					$img_id = vcex_get_term_thumbnail_id( $term->term_id );

					// Get woo product image
					if ( 'product' == $post_type ) {
						$img_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
					}

					// Image not defined via meta, display image from first post in term
					if ( ! $img_id ) :

						// Query first post in term
						$vcex_query = new WP_Query( array(
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
						if ( $vcex_query->have_posts() ) {

							while ( $vcex_query->have_posts() ) : $vcex_query->the_post();

								$img_id = get_post_thumbnail_id();

							endwhile;

						}

						// Reset query
						wp_reset_postdata();

					endif;

					if ( $img_id ) :

						$output .= '<div class="' . esc_attr( $media_classes ) . '">';

							$output .= '<a href="' . esc_url( $term_link ) . '" title="' . esc_attr( $term->name ) . '">';

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
								if ( 'true' == $atts[ 'title_overlay' ] && 'true' == $atts[ 'title' ] && ! empty( $term->name ) ) :

									$output .= '<div class="vcex-terms-grid-entry-overlay wpex-clr">';

										$output .= '<div class="vcex-terms-grid-entry-overlay-table wpex-clr">';

											$output .= '<div class="vcex-terms-grid-entry-overlay-cell wpex-clr">';

												$output .= '<' . esc_attr( $atts[ 'title_tag' ] ) . ' class="vcex-terms-grid-entry-title entry-title"'. $title_style .'>';

													$output .= '<span>' . esc_html( $term->name ) . '</span>';
													if ( 'true' == $atts[ 'term_count' ] ) {
														$output .= '<span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';

													}

												$output .= '</' . esc_attr( $atts[ 'title_tag' ] ) . '>';

											$output .= '</div>';

										$output .= '</div>';
									$output .= '</div>';

								endif;

								// Data for overlays
								if ( $img_id ) {
									$atts[ 'lightbox_link' ] = wpex_get_lightbox_image( $img_id );
								}
								$atts[ 'overlay_link' ]    = get_term_link( $term, $taxonomy );
								$atts[ 'post_title' ]      = $term->name;
								$atts[ 'overlay_excerpt' ] = $term_description;

								if ( $overlay_style && 'none' != $overlay_style ) {
									ob_start();
									vcex_image_overlay( 'inside_link', $overlay_style, $atts );
									$output .= ob_get_clean();
								}

							$output .= '</a>';

							if ( $overlay_style && 'none' != $overlay_style ) {
								ob_start();
								vcex_image_overlay( 'outside_link', $overlay_style, $atts );
								$output .= ob_get_clean();
							}

						$output .= '</div>';

					endif; // End img ID check

				endif; // End image check

				// Inline title and description
				if ( 'false' == $atts[ 'title_overlay' ] || 'false' == $atts[ 'img' ] ) :

					// Show title
					if ( 'false' == $atts[ 'title_overlay' ] && 'true' == $atts[ 'title' ] && ! empty( $term->name ) ) :

						$output .= '<' . esc_attr( $atts[ 'title_tag' ] ) . ' class="vcex-terms-grid-entry-title entry-title"' . $title_style . '>';

							$output .= '<a href="' . get_term_link( $term, $taxonomy ) . '">';

								$output .= esc_html( $term->name );

								if ( 'true' == $atts[ 'term_count' ] ) {
									$output .= ' <span class="vcex-terms-grid-entry-count">(' . absint( $term->count ) . ')</span>';
								}

							$output .= '</a>';
						$output .= '</'. esc_attr( $atts[ 'title_tag' ] ) .'>';

					endif;

					// Display term description
					if ( 'true' == $atts[ 'description' ] && $term_description ) :

						$output .= '<div class="vcex-terms-grid-entry-excerpt clr"' . $description_style . '>';

							$output .= $term_description;

						$output .= '</div>';

					endif;

				endif;

			$output .= '</div>';

		// Close entry
		if ( $entry_css_class ) {
			$output .= '</div>';
		}

		// Clear counter
		if ( $counter == $atts[ 'columns' ] ) {
			$counter = 0;
		}

	endforeach;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
