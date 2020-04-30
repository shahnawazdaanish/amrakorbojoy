<?php
/**
 * Visual Composer Blog Grid
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

// Define output var
$output = '';

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Store orginal atts value for use in non-builder params
$og_atts = $atts;

// Define entry counter
$entry_count = ! empty( $og_atts['entry_count'] ) ? $og_atts['entry_count'] : 0;

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_blog_grid', $atts, $this );
extract( $atts );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $og_atts['paged'] ) ) {
	$atts['paged'] = $og_atts['paged'];
}

// Define user-generated attributes
$atts['post_type'] = 'post';
$atts['taxonomy']  = 'category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$entry_media = ( ! $entry_media ) ? 'true' : $entry_media;
	$title       = ( ! $title ) ? 'true' : $title;
	$date        = ( ! $date ) ? 'true' : $date;
	$excerpt     = ( ! $excerpt ) ? 'true' : $excerpt;
	$read_more   = ( ! $read_more ) ? 'true' : $read_more;

	// Sanitize & declare variables
	$wrap_classes       = array( 'vcex-module', 'vcex-blog-grid-wrap', 'vcex-clr' );
	$grid_classes       = array( 'wpex-row', 'vcex-blog-grid', 'wpex-clr', 'entries' );
	$grid_data          = array();
	$is_isotope         = false;
	$css_animation      = vcex_get_css_animation( $css_animation );
	$css_animation      = ( 'true' == $filter ) ? false : $css_animation;
	$equal_heights_grid = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;
	$overlay_style      = $overlay_style ? $overlay_style : 'none';
	$url_target         = vcex_html( 'target_attr', $url_target );

	// Load lightbox script
	if ( 'lightbox' == $thumb_link ) {
		vcex_enqueue_lightbox_scripts();
	}

	// Enable Isotope?
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

	// Get filter terms
	if ( $filter_taxonomy ) {

		// Get filter terms
		$filter_terms = get_terms( $filter_taxonomy, vcex_grid_filter_args( $atts, $vcex_query ) );

		// If terms are found
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

			$filter = false; // No terms

		}

	}

	// Entry CSS class
	if ( $entry_css ) {
		$entry_css = vcex_vc_shortcode_custom_css_class( $entry_css );
	}

	// Columns class
	$columns_class = vcex_get_grid_column_class( $atts );

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-'. $columns_gap;
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}
	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Grid data attributes
	if ( 'true' == $filter ) {
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}
		if ( $filter_has_active_cat ) {
			$grid_data[] = 'data-filter=".cat-' . esc_attr( $filter_active_category ) . '"';
		}
	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_blog_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_blog_grid_wrap_classes', $wrap_classes ); // @todo deprecated
	$grid_classes  = apply_filters( 'vcex_blog_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_blog_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' '. implode( ' ', $grid_data ) : '';

	// VC core filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_blog_grid', $atts );

	// Begin output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Display header if enabled
		if ( $header ) {

			$output .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-module-heading vcex_blog_grid-heading' ),
			) );

		}

		// Display filter links
		if ( $filter_taxonomy && ! empty( $filter_terms ) ) :

			// Sanitize all text
			$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

			// Filter button classes
			$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );

			// Filter font size
			$filter_style = vcex_inline_style( array(
				'font_size' => $filter_font_size,
			) );

			$filter_classes = 'vcex-blog-filter vcex-filter-links clr';
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

				foreach ( $filter_terms as $term ) {

					$output .= '<li class="filter-cat-'. $term->term_id;

						if ( $filter_active_category == $term->term_id ) {
							$output .= ' active';
						}

					$output .= '">';

					$output .= '<a href="#" data-filter=".cat-'. $term->term_id .'" class="'. $filter_button_classes .'">';
						$output .= $term->name;
					$output .= '</a></li>';

				}

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			$output .= '</ul>';

		endif; // End filter links check

		$output .= '<div class="'. esc_attr( $grid_classes ) .'"'. $grid_data .'>';

			// Start loop
			$first_run = true;
			while ( $vcex_query->have_posts() ) :

				// Get post from query
				$vcex_query->the_post();

				// Post Data
				$atts['post_id']           = get_the_ID();
				$atts['post_title']        = get_the_title();
				$atts['post_esc_title']    = vcex_esc_title();
				$atts['post_permalink']    = vcex_get_permalink( $atts['post_id'] );
				$atts['post_format']       = get_post_format( $atts['post_id'] );
				$atts['post_excerpt']      = '';
				$atts['post_video']        = '';
				$atts['post_video_oembed'] = '';

				// Post Excerpt (check needs to be early)
				if ( 'true' == $excerpt ) {
					$atts['post_excerpt'] = vcex_get_excerpt( array(
						'length'  => $excerpt_length,
						'context' => 'vcex_blog_grid',
					) );
				}

				// Counter
				$entry_count++;

				// Get video
				if ( 'video' == $atts['post_format'] ) {
					$atts['post_video']        = vcex_get_post_video( $atts['post_id'] );
					$atts['post_video_oembed'] = $atts['post_video'] ? vcex_get_post_video_html( $atts['post_video'] ) : '';
				}

				// Apply filters to attributes
				$latts = apply_filters( 'vcex_shortcode_loop_atts', $atts, 'vcex_blog_grid' );

				// Does entry have details?
				if ( 'true' == $latts['title']
					|| ( 'true' == $latts['excerpt'] && $latts['post_excerpt'] )
					|| 'true' == $latts['read_more']
				) {
					$entry_has_details = true;
				} else {
					$entry_has_details = false;
				}

				// Entry Classes
				$entry_classes = array( 'vcex-blog-entry', 'vcex-grid-item' );
				if ( $entry_has_details ) {
					$entry_classes[] = 'entry-has-details';
				}
				$entry_classes[] = $columns_class;
				$entry_classes[] = 'col-' . $entry_count;
				if ( 'false' == $columns_responsive ) {
					$entry_classes[] = 'nr-col';
				} else {
					$entry_classes[] = 'col';
				}
				if ( $is_isotope ) {
					$entry_classes[] = 'vcex-isotope-entry';
				}
				if ( $css_animation ) {
					$entry_classes[] = $css_animation;
				}
				if ( $filter_taxonomy ) {
					if ( $post_terms = get_the_terms( $latts['post_id'], $filter_taxonomy ) ) {
						foreach ( $post_terms as $post_term ) {
							$entry_classes[] = sanitize_html_class( 'cat-' . $post_term->term_id );
						}
					}
				}
				if ( $content_alignment ) {
					$entry_classes[] = sanitize_html_class( 'text' . $content_alignment );
				}

				// Begin entry output
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $latts['post_id'] ) . '>';

					$output .= '<div class="vcex-blog-entry-inner entry-inner wpex-clr';

						if ( $entry_css ) {
							$output .= ' '. $entry_css;
						}

					$output .= '">';

						// If media is enabled
						if ( 'true' == $entry_media ) {

							$media_output = '';

							if ( $first_run ) {

								$media_classes = array( 'vcex-blog-entry-media', 'entry-media' );
								if ( $img_filter ) {
									$media_classes[] = vcex_image_filter_class( $img_filter );
								}
								if ( $img_hover_style ) {
									$media_classes[] = vcex_image_hover_classes( $img_hover_style );
								}
								if ( 'none' != $overlay_style ) {
									$media_classes[] = vcex_image_overlay_classes( $overlay_style );
								}
								$media_classes = implode( ' ', $media_classes );

								$has_overlay = ( 'none' != $overlay_style ) ? true : false;

							}

							// Display post video if defined and is video format
							if ( 'true' == $featured_video && ! empty( $latts['post_video_oembed'] ) ) :

								$media_output .= '<div class="vcex-blog-entry-media entry-media">';

									$media_output .= $latts['post_video_oembed'];

								$media_output .= '</div>';

							// Otherwise if post thumbnail is defined
							elseif ( has_post_thumbnail( $latts['post_id'] ) ) :

								$media_output .= '<div class="' . esc_attr( $media_classes ) . '">';

									// Open link tag if thumblink does not equal nowhere
									if ( 'nowhere' != $thumb_link ) :

										// Lightbox Links
										if ( $thumb_link == 'lightbox' ) :

											// Video lightbox link
											if ( 'video' == $latts['post_format'] ) :

												// Try and convert video URL into embed URL
												$embed_url = vcex_get_video_embed_url( $latts['post_video'] );
												$latts['lightbox_link'] = $embed_url ? $embed_url : $latts['post_video'];

												$media_output .= '<a href="' . $latts["lightbox_link"] . '" title="' . $latts['post_esc_title'] . '" class="wpex-lightbox">';

											// Image lightbox link
											else :

												// Add lightbox attributes
												$latts['lightbox_link'] = vcex_get_lightbox_image();

												$media_output .= '<a href="' . $latts["lightbox_link"] . '" title="' . $latts['post_esc_title'] . '" class="wpex-lightbox">';

											endif;

										else :

											 $media_output .= '<a href="' . esc_url( $latts['post_permalink'] ) . '" title="' . $latts['post_esc_title'] . '"' . $url_target . '>';

										endif;

									endif;

									// Define thumbnail args
									$thumbnail_args = array(
										'size'          => $img_size,
										'width'         => $img_width,
										'height'        => $img_height,
										'crop'          => $img_crop,
										'class'         => 'vcex-blog-entry-img',
										'apply_filters' => 'vcex_blog_grid_thumbnail_args',
										'filter_arg1'   => $latts,
									);

									// Add data-no-lazy to prevent conflicts with WP-Rocket
									if ( $is_isotope ) {
										$thumbnail_args['attributes'] = array( 'data-no-lazy' => 1 );
									}

									// Display thumbnail
									$media_output .= vcex_get_post_thumbnail( $thumbnail_args );

									// Inner link overlay HTML
									if ( $has_overlay ) {
										ob_start();
										vcex_image_overlay( 'inside_link', $overlay_style, $latts );
										$media_output .= ob_get_clean();
									}

									// Entry after media hook
									$media_output .= vcex_get_entry_media_after( 'vcex_blog_grid' );

									// Close link tag
									if ( 'nowhere' != $thumb_link ) {
										$media_output .= '</a>';
									}

									// Outer link overlay HTML
									if ( $has_overlay ) {
										ob_start();
										vcex_image_overlay( 'outside_link', $overlay_style, $latts );
										$media_output .= ob_get_clean();
									}

								$media_output .= '</div>';

							endif; // Video/thumbnail checks

							$output .= apply_filters( 'vcex_blog_grid_media', $media_output, $latts );

						}

						// Open entry details div if the $title, $excerpt or $read_more vars are true
						if ( $entry_has_details ) {

							if ( $first_run ) {

								$content_style = array(
									'color'   => $content_color,
									'opacity' => $content_opacity,
								);

								if ( ! $content_css ) {
									if ( isset( $content_background ) ) {
										$content_style['background'] = $content_background;
									}
									if ( isset( $content_padding ) ) {
										$content_style['padding'] = $content_padding;
									}
									if ( isset( $content_margin ) ) {
										$content_style['margin'] = $content_margin;
									}
									if ( isset( $content_border ) ) {
										$content_style['border'] = $content_border;
									}
								} else {
									$content_css = vcex_vc_shortcode_custom_css_class( $content_css );
								}

								$content_style = vcex_inline_style( $content_style );

							}

							$output .= '<div class="vcex-blog-entry-details entry-details wpex-clr';

								if ( $content_css ) {
									$output .= ' ' . $content_css;
								}

								$output .= '"';

								$output .= $content_style;

							$output .= '>';

								// Open equal heights div if equal heights is enabled
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								// Display title if $title is true
								if ( 'true' == $title ) {

									$title_output = '';

									if ( $first_run ) {

										$title_tag         = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $latts );
										$title_tag_escaped = $title_tag ? esc_attr( $title_tag ) : 'h2';

										$heading_style = vcex_inline_style( array(
											'margin'            => $content_heading_margin,
											'color'             => $content_heading_color,
											'font_size'         => $content_heading_size,
											'font_weight'       => $content_heading_weight,
											'line_height'       => $content_heading_line_height,
											'text_transform'    => $content_heading_transform,
										) );

										$heading_link_style = vcex_inline_style( array(
											'color' => $content_heading_color,
										) );

									}

									$title_output .= '<' . $title_tag_escaped . ' class="vcex-blog-entry-title entry-title"' . $heading_style . '><a href="' . esc_url( $latts['post_permalink'] ) . '"' . $url_target . $heading_link_style . '>' . wp_kses_post( $latts['post_title'] ) . '</a></' . $title_tag_escaped . '>';

									$output .= apply_filters( 'vcex_blog_grid_title', $title_output, $latts );

								}

								// Display date if $date is true
								if ( 'true' == $date ) {

									$date_output = '';

									if ( $first_run ) {
										$date_style = vcex_inline_style( array(
											'color'     => $date_color,
											'font_size' => $date_font_size,
										) );
									}

									$date_output .= '<div class="vcex-blog-entry-date entry-date"' . $date_style . '>';

										$date_output .= get_the_date();

									$date_output .= '</div>';

									$output .= apply_filters( 'vcex_blog_grid_date', $date_output, $latts );

								}

								// Display excerpt
								if ( 'true' == $excerpt ) {

									$excerpt_output = '';

									if ( $first_run ) {
										$excerpt_style = vcex_inline_style( array(
											'font_size' => $content_font_size,
										) );
									}

									if ( '-1' == $excerpt_length
										&& $shortcodes_custom_css = get_post_meta( $latts['post_id'], '_wpb_shortcodes_custom_css', true )
									) {

											$excerpt_output .= '<style data-type="vc_shortcodes-custom-css">';
											$excerpt_output .= strip_tags( $shortcodes_custom_css );
											$excerpt_output .= '</style>';

									}

									$excerpt_output .= '<div class="vcex-blog-entry-excerpt entry-excerpt entry clr"' . $excerpt_style . '>';

										if ( $excerpt && $latts['post_excerpt'] ) {
											$excerpt_output .= $latts['post_excerpt'];
										}

									$excerpt_output .= '</div>';

									$output .= apply_filters( 'vcex_blog_grid_excerpt', $excerpt_output, $latts );

								} // End excerpt check

								// Display read more button if $read_more is true and $read_more_text isn't empty
								if ( 'true' == $read_more ) {

									$readmore_output = '';

									if ( $first_run ) {

										// Readmore text
										$read_more_text = $read_more_text ? $read_more_text : esc_html__( 'read more', 'total' );

										// Readmore classes
										$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );

										// Readmore style
										$readmore_style = vcex_inline_style( array(
											'background'    => $readmore_background,
											'color'         => $readmore_color,
											'font_size'     => $readmore_size,
											'padding'       => $readmore_padding,
											'border_radius' => $readmore_border_radius,
											'margin'        => $readmore_margin,
										), false );

										// Readmore data
										$readmore_hover_data = array();
										if ( $readmore_hover_background ) {
											$readmore_hover_data['background'] = esc_attr( $readmore_hover_background );
										}
										if ( $readmore_hover_color ) {
											$readmore_hover_data['color'] = esc_attr( $readmore_hover_color );
										}
										if ( $readmore_hover_data ) {
											$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
										}

									}

									$readmore_output .= '<div class="vcex-blog-entry-readmore-wrap entry-readmore-wrap clr">';

										$readmore_attrs = array(
											'href'  => esc_url( $latts['post_permalink'] ),
											'class' => $readmore_classes,
											'rel'   => 'bookmark',
											'style' => $readmore_style,
										);

										if ( $readmore_hover_data ) {
											$readmore_attrs['data-wpex-hover'] = $readmore_hover_data;
										}

										$readmore_output .= '<a' . vcex_parse_html_attributes( $readmore_attrs ) . '>';

											$readmore_output .= $read_more_text;

											if ( 'true' == $readmore_rarr ) {
												$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
											}

										$readmore_output .= '</a>';

									$readmore_output .= '</div>';

									$output .= apply_filters( 'vcex_blog_grid_readmore', $readmore_output, $latts );

								}

								// Close equal heights div if equal heights is enabled
								if ( $equal_heights_grid ) {
									$output .= '</div>';
								}

							$output .= '</div>';

						} // End details check

					$output .= '</div>'; // Close entry inner

				$output .= '</div>'; // Close entry

			// Reset entry counter
			if ( $entry_count == $columns ) {
				$entry_count = 0;
			}

			$first_run = false; endwhile; // End main loop

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
			$output .= vcex_get_loadmore_button( 'vcex_blog_grid', $og_atts, $vcex_query );
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