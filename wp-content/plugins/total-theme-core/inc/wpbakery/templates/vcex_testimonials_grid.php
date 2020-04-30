<?php
/**
 * Visual Composer Testimonials Grid
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

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories'] ) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Store orginal atts value for use in non-builder params
$og_atts = $atts;

// Define entry counter
$entry_count = ! empty( $og_atts['entry_count'] ) ? $og_atts['entry_count'] : 0;

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_testimonials_grid', $atts, $this );
extract( $atts );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $og_atts['paged'] ) ) {
	$atts['paged'] = $og_atts['paged'];
}

// Define user-generated attributes
$atts['post_type'] = 'testimonials';
$atts['taxonomy']  = 'testimonials_category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$entry_media = ( ! $entry_media ) ? 'true' : $entry_media;
	$title       = ( ! $title ) ? 'true' : $title;
	$excerpt     = ( ! $excerpt ) ? 'true' : $excerpt;
	$read_more   = ( ! $read_more ) ? 'true' : $read_more;

	// Declare and sanitize vars
	$wrap_classes  = array( 'vcex-module', 'vcex-testimonials-grid-wrap', 'clr' );
	$grid_classes  = array( 'wpex-row', 'vcex-testimonials-grid', 'clr' );
	$grid_data     = array();
	$is_isotope    = false;
	$css_animation = vcex_get_css_animation( $css_animation );
	$css_animation = ( 'true' == $filter ) ? false : $css_animation;
	$title_tag     = $title_tag ? $title_tag : 'div';

	// Is Isotope var
	if ( 'true' == $filter || 'masonry' == $grid_style ) {
		$is_isotope = true;
		vcex_enqueue_isotope_scripts();
	}

	// Get filter taxonomy
	if ( 'true' == $filter ) {
		$filter_taxonomy = apply_filters( 'vcex_filter_taxonomy', $atts['taxonomy'], $atts );
		$filter_taxonomy = taxonomy_exists( $filter_taxonomy ) ? $filter_taxonomy : '';
		if ( $filter_taxonomy ) {
			$atts['filter_taxonomy'] = $filter_taxonomy; // Add to array to pass on to vcex_grid_filter_args()
		}
	} else {
		$filter_taxonomy = null;
	}

	// Get filter categories
	if ( $filter_taxonomy ) {

		// Get filter terms
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $vcex_query ) );

		// Make sure we have terms before doing things
		if ( $filter_terms ) {

			// Check url for filter cat
			if ( $active_cat_query_arg = vcex_grid_filter_get_active_item( $filter_taxonomy ) ) {
				$filter_active_category = $active_cat_query_arg;
			}

			// Check if filter active cat exists on current page
			$filter_has_active_cat = in_array( $filter_active_category, wp_list_pluck( $filter_terms, 'term_id' ) ) ? true : false;

			// Add show on load animation when active filter is enabled to prevent double animation
			if ( $filter_has_active_cat ) {
				$grid_classes[] = 'wpex-show-on-load';
			}

		} else {

			$filter = false; // No terms so we can't have a filter

		}

	}

	// Image Style
	$img_style = vcex_inline_style( array(
		'border_radius' => $img_border_radius,
	), false );

	// Image thumbnail classes
	$thumb_classes = '';
	if ( $img_width || $img_height || ! in_array( $img_size, array( 'wpex_custom', 'testimonials_entry' ) ) ) {
		$thumb_classes = ' custom-dims';
	} else {
		$thumb_classes = ' default-dims';
	}

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid Classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-'. $columns_gap;
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}

	// Data
	if ( $is_isotope && 'true' == $filter ) {
		if ( 'no_margins' != $grid_style && $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="' . esc_attr( $masonry_layout_mode ) . '"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}
		if ( ! empty( $filter_has_active_cat ) ) {
			$grid_data[] = 'data-filter=".cat-' . esc_attr( $filter_active_category ) . '"';
		}
	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_testimonials_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Load Google fonts if needed
	if ( $title_font_family ) {
		vcex_enqueue_google_font( $title_font_family );
	}

	// Columns classes
	$columns_class = vcex_get_grid_column_class( $atts );

	// Title style
	$title_style = '';
	if ( 'true' == $title ) {
		$title_style = vcex_inline_style( array(
			'font_size'     => $title_font_size,
			'font_family'   => $title_font_family,
			'color'         => $title_color,
			'margin_bottom' => $title_bottom_margin,
		) );
	}

	// Excerpt style
	$content_style = vcex_inline_style( array(
		'font_size' => $content_font_size,
		'color'     => $content_color,
	) );

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_testimonials_grid_wrap_classes', $wrap_classes ); // @todo deprecate?
	$grid_classes  = apply_filters( 'vcex_testimonials_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_testimonials_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_testimonials_grid', $atts );

	// Begin shortcode output
	$output .= '<div class="'. esc_attr( $wrap_classes ) .'"'. vcex_get_unique_id( $unique_id ) .'>';

		// Display header if enabled
		if ( $header ) {

			$output .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-module-heading vcex_testimonials_grid-heading' ),
			) );

		}

		// Display filter links
		if ( 'true' == $filter && ! empty( $filter_terms ) ) {

			// Sanitize all text
			$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

			// Filter button classes
			$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );

			// Filter font size
			$filter_style = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) );

			$filter_classes = 'vcex-testimonials-filter vcex-filter-links clr';
			if ( 'yes' == $center_filter ) {
				$filter_classes .= ' center';
			}

			$output .= '<ul class="'. $filter_classes .'"'. $filter_style .'>';

				if ( 'true' == $filter_all_link ) {

					$output .= '<li';
						if ( ! $filter_has_active_cat ) {
							$output .= ' class="active"';
						}
					$output .= '>';

						$output .= '<a href="#" data-filter="*" class="'. $filter_button_classes .'"><span>'. $all_text .'</span></a>';

					$output .= '</li>';

				}

				foreach ( $filter_terms as $term ) :

					$output .= '<li class="filter-cat-'. $term->term_id;

						if ( $filter_active_category == $term->term_id ) {
							$output .= ' active';
						}

					$output .= '">';

					$output .= '<a href="#" data-filter=".cat-'. $term->term_id .'" class="'. $filter_button_classes .'">';
						$output .= $term->name;
					$output .= '</a></li>';

				endforeach;

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			$output .= '</ul>';

		}

		$output .= '<div class="'. $grid_classes .'"'. $grid_data .'>';

			// Start loop
			while ( $vcex_query->have_posts() ) :

				// Get post from query
				$vcex_query->the_post();

				// Add to the counter var
				$entry_count++;

				// Get post data
				$atts['post_id']           = get_the_ID();
				$atts['post_title']        = get_the_title();
				$atts['post_esc_title']    = vcex_esc_title();
				$atts['post_permalink']    = vcex_get_permalink();
				$atts['post_meta_author']  = get_post_meta( $atts['post_id'], 'wpex_testimonial_author', true );
				$atts['post_meta_company'] = get_post_meta( $atts['post_id'], 'wpex_testimonial_company', true );
				$atts['post_meta_url']     = get_post_meta( $atts['post_id'], 'wpex_testimonial_url', true );

				// Add classes to the entries
				$entry_classes = array( 'testimonial-entry', 'vcex-grid-item' );
				$entry_classes[] = $columns_class;
				$entry_classes[] = 'col-' . $entry_count;
				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}
				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}
				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}

				// Begin entry output
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $atts['post_id'] ) . '>';

					$output .= '<div class="testimonial-entry-content clr">';

						$output .= '<span class="testimonial-caret"></span>';

						// Display title
						$title_output = '';
						if ( 'true' == $title ) :

							$title_output .= '<' . esc_attr( $title_tag ) . ' class="testimonial-entry-title entry-title"' . $title_style . '>';

								// Title with link
								if ( 'true' == $atts['title_link'] ) {

									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';

										$title_output .= esc_html( $atts['post_title'] );

									$title_output .= '</a>';

								}

								// Title without link
								else {

									$title_output .= esc_html( $atts['post_title'] );

								}

							$title_output .= '</'. esc_attr( $title_tag ) .'>';

							$output .= apply_filters( 'vcex_testimonials_grid_title', $title_output, $atts );

						endif;

						$output .= '<div class="testimonial-entry-details clr"' . $content_style . '>';

							// Display excerpt if enabled (default dispays full content )
							$excerpt_output = '';
							if ( 'true' == $excerpt ) {

								// Custom readmore text
								if ( 'true' == $read_more ) {

									// Add arrow
									if ( 'false' != $read_more_rarr ) {

										$read_more_rarr_html = '<span>&rarr;</span>';

									} else {

										$read_more_rarr_html = '';

									}

									// Read more text
									if ( is_rtl() ) {
										$read_more_link = '...<a href="' . esc_url( $atts['post_permalink'] ) . '">' . $read_more_text . '</a>';
									} else {
										$read_more_link = '...<a href="' . esc_url( $atts['post_permalink'] ) . '">' . esc_html( $read_more_text ) . $read_more_rarr_html . '</a>';
									}

								} else {

									$read_more_link = '...';

								}

								// Custom Excerpt function
								$excerpt_output .= vcex_get_excerpt( array(
									'post_id' => $atts['post_id'],
									'length'  => $excerpt_length,
									'more'    => $read_more_link,
									'context' => 'vcex_testimonials_grid',
								) );

							// Display full post content
							} else {

								$excerpt_output .= vcex_the_content( get_the_content(), 'vcex_testimonials_grid' );

							// End excerpt check
							}

							$output .= apply_filters( 'vcex_testimonials_grid_excerpt', $excerpt_output, $atts );

						$output .= '</div>';

					$output .= '</div>';

					$bottom_output = '';
					$bottom_output .= '<div class="testimonial-entry-bottom clr">';

						// Check if post thumbnail is defined
						$media_output = '';
						if ( 'true' == $atts['entry_media'] ) {

							if ( has_post_thumbnail( $atts['post_id'] ) ) {

								$media_output .= '<div class="testimonial-entry-thumb' . $thumb_classes . '">';

									// Define thumbnail args
									$thumbnail_args = array(
										'attachment'    => get_post_thumbnail_id( $atts['post_id'] ),
										'size'          => $img_size,
										'width'         => $img_width,
										'height'        => $img_height,
										'style'         => $img_style,
										'crop'          => $img_crop,
										'apply_filters' => 'vcex_testimonials_grid_thumbnail_args',
										'filter_arg1'   => $atts,
									);

									// Add data-no-lazy to prevent conflicts with WP-Rocket
									if ( $is_isotope ) {
										$thumbnail_args['attributes'] = array( 'data-no-lazy' => 1 );
									}

									// Display post thumbnail
									$media_output .= vcex_get_post_thumbnail( $thumbnail_args );

								$media_output .= '</div>';

							}

							$bottom_output .= apply_filters( 'vcex_testimonials_grid_media', $media_output, $atts );

						}

						$bottom_output .= '<div class="testimonial-entry-meta">';

							// Display testimonial author
							$author_output = '';
							if ( 'true' == $atts['author'] ) :

								if ( $atts['post_meta_author'] ) {

									$author_output .= '<span class="testimonial-entry-author entry-title">';

										$author_output .= wp_kses_post( $atts['post_meta_author'] );

									$author_output .= '</span>';

								}

								$bottom_output .= apply_filters( 'vcex_testimonials_grid_author', $author_output, $atts );

							endif;

							// Display testimonial company
							$company_output = '';
							if ( 'true' == $atts['company'] ) {

								if ( $atts['post_meta_company'] ) {

									// Display testimonial company with URL
									if ( $atts['post_meta_url'] ) {

										$company_output .= '<a href="'. esc_url( $atts['post_meta_url'] ) .'" class="testimonial-entry-company" target="_blank">';

											$company_output .= wp_kses_post( $atts['post_meta_company'] );

										$company_output .= '</a>';

									// Display testimonial company without URL since it's not defined
									} else {

										$company_output .= '<span class="testimonial-entry-company">';

											$company_output .= wp_kses_post( $atts['post_meta_company'] );

										$company_output .= '</span>';

									}

								}

								$bottom_output .= apply_filters( 'vcex_testimonials_grid_company', $company_output, $atts );

							}

							// Display rating
							$rating_output = '';
							if ( 'true' == $rating ) {

								if ( $atts['post_rating'] = vcex_get_star_rating( '', $atts['post_id'] ) ) {

									$rating_output .= '<div class="testimonial-entry-rating clr">'. $atts['post_rating'] .'</div>';

								}

								$bottom_output .= apply_filters( 'vcex_testimonials_grid_rating', $rating_output, $atts );

							}

						$bottom_output .= '</div>';

					$bottom_output .= '</div>';

					$output .= apply_filters( 'vcex_testimonials_grid_bottom', $bottom_output, $atts );

				$output .= '</div>';

				// Reset post loop counter
				if ( $entry_count == $columns ) {
					$entry_count=0;
				}

			endwhile; // End loop

		$output .= '</div>';

		// Display pagination if enabled
		if ( ( 'true' == $atts['pagination'] || ( 'true' == $atts['custom_query'] && ! empty( $vcex_query->query['pagination'] ) ) )
			&& 'true' != $atts['pagination_loadmore']
		) {

			$output .= vcex_pagination( $vcex_query, false );

		}

		// Load more button
		if ( 'true' == $atts['pagination_loadmore'] && ! empty( $vcex_query->max_num_pages ) ) {
			vcex_loadmore_scripts();
			$og_atts['entry_count'] = $entry_count; // Update counter
			$output .= vcex_get_loadmore_button( 'vcex_testimonials_grid', $og_atts, $vcex_query );
		}

	$output .= '</div>';

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

	// @codingStandardsIgnoreLine
	echo $output;

// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;