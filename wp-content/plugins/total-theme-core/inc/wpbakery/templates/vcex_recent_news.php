<?php
/**
 * Visual Composer Recent News
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
$term_slug = isset( $atts['term_slug'] ) ? $atts['term_slug'] : '';

// Store orginal atts value for use in non-builder params
$og_atts = $atts;

// Define entry counter
$entry_count = ! empty( $og_atts['entry_count'] ) ? $og_atts['entry_count'] : 0;

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_recent_news', $atts, $this );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $og_atts['paged'] ) ) {
	$atts['paged'] = $og_atts['paged'];
}

// Define non-vc attributes
$atts['tax_query']  = '';
$atts['taxonomies'] = 'category';

// Extract shortcode atts
extract( $atts );

// IMPORTANT: Fallback required from VC update when params are defined as empty
// AKA - set things to enabled by default
$title     = ( ! $title ) ? 'true' : $title;
$date      = ( ! $date ) ? 'true' : $date;
$excerpt   = ( ! $excerpt ) ? 'true' : $excerpt;
$read_more = ( ! $read_more ) ? 'true' : $read_more;

// Fallback for term slug
if ( ! empty( $term_slug ) && empty( $include_categories ) ) {
	$include_categories = $term_slug;
}

// Custom taxonomy only for standard posts
if ( 'custom_post_types' == $get_posts ) {
	$atts['include_categories'] = $atts['exclude_categories'] = '';
}

// Get Standard posts
if ( 'standard_post_types' == $get_posts ) {
	$atts['post_types'] = 'post';
}

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// Sanitize grid columns
	$grid_columns = $grid_columns ? $grid_columns : '1';

	// Set correct category taxonomy
	if ( ! $categories_taxonomy ) {
		$categories_taxonomy = strpos( $post_types, ',' ) === false ? vcex_get_post_type_cat_tax( $post_types ) : 'category';
	}

	// Set show_categories to false if taxonomy doesn't exist
	if ( 'true' == $show_categories && ! taxonomy_exists( $categories_taxonomy ) ) {
		$show_categories = false;
	}

	// Wrap Classes
	$wrap_classes = array( 'vcex-module', 'vcex-recent-news', 'clr' );
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( '1' != $grid_columns ) {
		$wrap_classes[] = 'wpex-row';
		if ( $columns_gap ) {
			$wrap_classes[] = 'gap-' . $columns_gap;
		}
		$atts['columns'] = $grid_columns;
		$grid_columns_class = vcex_get_grid_column_class( $atts );
	}
	if ( $css ) {
		$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $css );
	}

	// Entry Classes
	$entry_classes = array( 'vcex-recent-news-entry', 'clr' );
	if ( 'true' != $date ) {
		$entry_classes[] = 'no-left-padding';
	}
	if ( $css_animation && 'none' != $css_animation ) {
		$entry_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Entry Style
	$entry_style = vcex_inline_style( array(
		'border_color' => $entry_bottom_border_color
	) );

	// Convert arrays to strings
	$wrap_classes = implode( ' ', $wrap_classes );

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_recent_news', $atts );

	// Add wrapper (introduced in 4.8 for load more function)
	$output .= '<div class="vcex-recent-news-wrap clr">';

	// Output module
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Display header if enabled
		if ( $header ) {

			$output .= vcex_get_module_header( array(
				'style'   => $header_style,
				'content' => $header,
				'classes' => array( 'vcex-recent-news-header vcex-module-heading' ),
			) );

		}

		// Loop through posts
		$total_count = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Add to counters
			$entry_count++;

			// Create new post object.
			$post = new stdClass();

			// Post vars
			$post->ID            = get_the_ID();
			$post->permalink     = vcex_get_permalink( $post->ID );
			$post->the_title     = get_the_title( $post->ID );
			$post->the_title_esc = esc_attr( the_title_attribute( 'echo=0' ) );
			$post->type          = get_post_type( $post->ID );
			$post->video_embed   = vcex_get_post_video_html();
			$post->format        = get_post_format( $post->ID );

			$entry_wrap_classes = 'vcex-recent-news-entry-wrap vcex-grid-item';
			if ( $grid_columns > '1' ) {
				$entry_wrap_classes .= ' col ' . $grid_columns_class . ' col-' . $entry_count;
			}

			$output .= '<div class="' . esc_attr( $entry_wrap_classes ) . '">';

			$output .= '<article ' . vcex_get_post_class( $entry_classes, $post->ID ) . $entry_style . '>';

				/*
				 * Entry Date.
				 */
				$date_output = '';
				if ( 'true' == $date ) {

					if ( $first_run ) {
						$month_style = vcex_inline_style( array(
							'background_color' => $month_background,
							'color' => $month_color,
						) );
					}

					$date_output .= '<div class="vcex-recent-news-date">';

						$date_output .= '<span class="day">';

							// Standard day display
							$day = get_the_time( 'd', $post->ID );

							// Filter day display for tribe events calendar plugin
							// @todo move to events config file
							if ( 'tribe_events' == $post->type && function_exists( 'tribe_get_start_date' ) ) {
								$day = tribe_get_start_date( $post->ID, false, 'd' );
							}

							// Apply filters and return date
							$date_output .= apply_filters( 'vcex_recent_news_day_output', $day );

						// Close day
						$date_output .= '</span>';

						$date_output .= '<span class="month"' . $month_style . '>';

							// Standard month year display
							$month_year = '<span>' . get_the_time( 'M', $post->ID ) . '</span>';
							$month_year .= ' <span class="year">' . get_the_time( 'y', $post->ID ) . '</span>';

							// Filter month/year display for tribe events calendar plugin
							// @todo move to events config file
							if ( 'tribe_events' == $post->type && function_exists( 'tribe_get_start_date' ) ) {
								$month_year = '<span>' . tribe_get_start_date( $post->ID, false, 'M' ) . '</span>';
								$month_year .= ' <span class="year">' . tribe_get_start_date( $post->ID, false, 'y' ) . '</span>';
							}

							// Echo the month/year
							$date_output .= apply_filters( 'vcex_recent_news_month_year_output', $month_year );

						// Close month
						$date_output .= '</span>';

					$date_output .= '</div>';

				}

				$output .= apply_filters( 'vcex_recent_news_date', $date_output, $atts );

				$output .= '<div class="vcex-news-entry-details clr">';

					/*
					 * Media.
					 */
					$media_output = '';
					if ( 'true' == $featured_image ) {

						// Display video
						if ( 'true' == $featured_video && $post->video_embed ) {

							$media_output .= '<div class="vcex-news-entry-video clr">' . $post->video_embed . '</div>';

						// Display featured image
						} elseif ( has_post_thumbnail( $post->ID ) ) {

							$media_classes = array(
								'vcex-news-entry-thumbnail',
								'entry-media',
								'clr'
							);

							if ( $overlay_style ) {
								$media_classes[] = vcex_image_overlay_classes( $overlay_style );
							}
							if ( $img_hover_style ) {
								$media_classes[] = vcex_image_hover_classes( $img_hover_style );
							}

							$media_output .= '<div class="' . esc_attr( implode( ' ', $media_classes ) )  . '">';

								$media_output .= '<a href="' . esc_url( $post->permalink ) . '" title="' . vcex_esc_title() . '">';

									// Display thumbnail
									$media_output .= vcex_get_post_thumbnail( array(
										'size'          => $img_size,
										'crop'          => $img_crop,
										'width'         => $img_width,
										'height'        => $img_height,
										'alt'           => vcex_esc_title(),
										'apply_filters' => 'vcex_recent_news_thumbnail_args',
										'filter_arg1'   => $atts,
									) );

									$media_output .= vcex_get_entry_media_after( 'vcex_recent_news' );

									ob_start();
									vcex_image_overlay( 'inside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();

								$media_output .= '</a>';

								ob_start();
								vcex_image_overlay( 'outside_link', $overlay_style, $atts );
								$media_output .= ob_get_clean();

							$media_output .= '</div>';

						} // End thumbnail check

					} // End featured image check

					$output .= apply_filters( 'vcex_recent_news_media', $media_output, $atts );

					/* Categories.
					 *
					 */
					if ( 'true' == $show_categories ) {

						$categories_output = '';
						$get_categories    = '';

						// Generate inline CSS for categories but we only need to do this 1x
						if ( $first_run ) {
							$categories_style = vcex_inline_style( array(
								'margin'    => $categories_margin,
								'font_size' => $categories_font_size,
								'color'     => $categories_color,
							) );
						}

						if ( 'true' == $show_first_category_only ) {

							if ( ! vcex_validate_boolean( $categories_links ) ) {

								$get_categories = vcex_get_first_term( $post->ID, $categories_taxonomy );

							} else {

								$get_categories = vcex_get_first_term_link( $post->ID, $categories_taxonomy );

							}

						} else {

							$get_categories = vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $categories_links ) );

						}

						$get_categories = apply_filters( 'vcex_post_type_grid_get_categories', $get_categories, $atts );

						if ( $get_categories ) {

							$categories_output .= '<div class="vcex-recent-news-entry-categories entry-categories wpex-clr"' . $categories_style . '>';

								$categories_output .= $get_categories; // already sanitized

							$categories_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_recent_news_categories', $categories_output, $atts );

					}

					/*
					 * Title.
					 */
					if ( 'true' == $title ) {

						$title_output = '';

						if ( $first_run ) {
							$title_tag_escaped = $title_tag ? esc_attr( $title_tag ) : 'h2';
							$heading_style = vcex_inline_style( array(
								'font_size'      => $title_size,
								'font_weight'    => $title_weight,
								'text_transform' => $title_transform,
								'line_height'    => $title_line_height,
								'margin'         => $title_margin,
								'color'          => $title_color,
							) );
						}

						$title_output .= '<header class="vcex-recent-news-entry-title entry-title">';

							$title_output .= '<' . $title_tag_escaped . ' class="vcex-recent-news-entry-title-heading"' . $heading_style . '>';

								$title_output .= '<a href="' . esc_url( $post->permalink ) . '">' . wp_kses_post( $post->the_title ) . '</a>';

							$title_output .= '</' . $title_tag_escaped . '>';

						$title_output .= '</header>';

						$output .= apply_filters( 'vcex_recent_news_title', $title_output, $atts );

					} // End title check

					// Excerpt and readmore
					if ( 'true' == $excerpt || 'true' == $read_more ) {

							if ( 'true' == $excerpt ) {

								$excerpt_output   = '';

								if ( $first_run ) {
									$excerpt_style = vcex_inline_style( array(
										'font_size' => $excerpt_font_size,
										'color'     => $excerpt_color,
									) );
								}

								$excerpt_output .= '<div class="vcex-recent-news-entry-excerpt entry clr"' . $excerpt_style . '>';

									// Output excerpt
									$excerpt_output .= vcex_get_excerpt( array(
										'length'  => $excerpt_length,
										'context' => 'vcex_recent_news',
									) );

								$excerpt_output .= '</div>';

								$output .= apply_filters( 'vcex_recent_news_excerpt', $excerpt_output, $atts );

							} // End excerpt check

							// Display readmore link
							if ( 'true' == $read_more ) {

								$read_more_output = '';

								if ( $first_run ) {

									// Readmore text
									$read_more_text = $read_more_text ? $read_more_text : esc_html__( 'read more', 'total' );

									// Readmore classes
									$readmore_classes = vcex_get_button_classes( $readmore_style, $readmore_style_color );

									// Read more style
									$readmore_border_color  = ( 'outline' == $readmore_style ) ? $readmore_color : '';
									$readmore_style = vcex_inline_style( array(
										'background'    => $readmore_background,
										'color'         => $readmore_color,
										'border_color'  => $readmore_border_color,
										'font_size'     => $readmore_size,
										'padding'       => $readmore_padding,
										'border_radius' => $readmore_border_radius,
										'margin'        => $readmore_margin,
									) );

									// Readmore data
									$readmore_hover_data = array();
									if ( $readmore_hover_background ) {
										$readmore_hover_data['background'] = $readmore_hover_background;
									}
									if ( $readmore_hover_color ) {
										$readmore_hover_data['color'] = $readmore_hover_color;
									}
									if ( $readmore_hover_data ) {
										$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
									}

								}

								$attrs = array(
									'href'  => esc_url( $post->permalink ),
									'class' => esc_attr( $readmore_classes ),
									'rel'   => 'bookmark',
									'style' => $readmore_style,
								);

								if ( $readmore_hover_data ) {
									$attrs['data-wpex-hover'] = $readmore_hover_data;
								}

								$read_more_output .= '<div class="vcex-recent-news-entry-readmore-wrap entry-readmore-wrap clr">';

									$read_more_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

										$read_more_output .= do_shortcode( wp_strip_all_tags( $read_more_text ) );

										if ( 'true' == $readmore_rarr ) {

											$read_more_output .= '<span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';

										}

									$read_more_output .= '</a>';

								$read_more_output .= '</div>';

								$output .= apply_filters( 'vcex_recent_news_read_more', $read_more_output, $atts );

							} // End readmore text

					} // End excerpt + readmore

				$output .= '</div>';

			$output .= '</article>';

			$output .= '</div>'; // entry wrap close

			if ( $entry_count == $grid_columns ) {
				$entry_count=0;
			}

		$first_run = false;

	endwhile;

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
		$output .= vcex_get_loadmore_button( 'vcex_recent_news', $og_atts, $vcex_query );

	}

	// Remove post object from memory
	$post = null;

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

	// Close wrap
	$output .= '</div>';

	// @codingStandardsIgnoreLine
	echo $output;

// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;