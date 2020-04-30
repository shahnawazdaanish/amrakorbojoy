<?php
/**
 * Visual Composer Post Type Grid
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

// Store orginal atts value for use in non-builder params
$og_atts = $atts;

// Define entry counter
$entry_count = ! empty( $og_atts['entry_count'] ) ? $og_atts['entry_count'] : 0;

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_type_grid', $atts, $this );

// Extract attributes
extract( $atts );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $og_atts['paged'] ) ) {
	$atts['paged'] = $og_atts['paged'];
}

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// Define entry blocks output
	$entry_blocks = apply_filters( 'vcex_post_type_grid_entry_blocks', vcex_filter_grid_blocks_array( array(
		'media'      => $entry_media,
		'title'      => $title,
		'date'       => $date,
		'categories' => $show_categories,
		'excerpt'    => $excerpt,
		'read_more'  => $read_more,
	) ), $atts );

	// Declare and sanitize useful variables
	$wrap_classes        = array( 'vcex-module', 'vcex-post-type-grid-wrap', 'wpex-clr' );
	$grid_classes        = array( 'wpex-row', 'vcex-post-type-grid', 'entries', 'wpex-clr' );
	$grid_data           = array();
	$is_isotope          = false;
	$filter_taxonomy     = ( $filter_taxonomy && taxonomy_exists( $filter_taxonomy ) ) ? $filter_taxonomy : '';
	$equal_heights_grid  = ( 'true' == $equal_heights_grid && $columns > '1' ) ? true : false;
	$css_animation       = vcex_get_css_animation( $css_animation );
	$css_animation       = 'true' == $filter ? false : $css_animation;
	$title_tag           = apply_filters( 'vcex_grid_default_title_tag', $title_tag, $atts );
	$title_tag_escaped   = $title_tag ? esc_attr( $title_tag ) : 'h2';

	// Set correct category taxonomy
	if ( ! $categories_taxonomy ) {
		$categories_taxonomy = strpos( $post_types, ',' ) === false ? vcex_get_post_type_cat_tax( $post_types ) : 'category';
	}

	// Advanced sanitization
	if ( 'true' == $filter || 'masonry' == $grid_style || 'no_margins' == $grid_style ) {
		$is_isotope = true;
		vcex_enqueue_isotope_scripts();
	}

	// Check url for filter cat
	$filter_active_category = vcex_grid_filter_get_active_item( $filter_taxonomy );
	if ( $filter_active_category ) {
		$grid_classes[] = 'wpex-show-on-load';
		if ( 'post_types' == $filter_type ) {
			$filter_active_category = 'type-' . $filter_active_category;
		}
	}

	// Load lightbox scripts
	if ( 'lightbox' == $thumb_link || 'lightbox_gallery' == $thumb_link ) {
		if ( 'true' == $atts['thumb_lightbox_gallery'] ) {
			$grid_classes[] = 'wpex-lightbox-group';
			$lightbox_single_class = 'wpex-lightbox-group-item';
		} else {
			$lightbox_single_class = 'wpex-lightbox';
		}
		if ( 'true' != $atts['thumb_lightbox_title'] ) {
			$grid_data[] = 'data-show_title="false"';
		}
		vcex_enqueue_lightbox_scripts();
	}

	// Turn post types into array
	$post_types = $post_types ? $post_types : 'post';
	$post_types = explode( ',', $post_types );

	// Wrap classes
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Grid classes
	if ( $columns_gap ) {
		$grid_classes[] = 'gap-' . $columns_gap;
	}
	if ( 'left_thumbs' == $single_column_style ) {
		$grid_classes[] = 'left-thumbs';
	}
	if ( $is_isotope ) {
		$grid_classes[] = 'vcex-isotope-grid';
	}
	if ( 'no_margins' == $grid_style ) {
		$grid_classes[] = 'vcex-no-margin-grid';
	}
	if ( $equal_heights_grid ) {
		$grid_classes[] = 'match-height-grid';
	}

	// Data
	if ( 'true' == $filter ) {

		// Filter settings
		if ( 'fitRows' == $masonry_layout_mode ) {
			$grid_data[] = 'data-layout-mode="fitRows"';
		}
		if ( $filter_speed ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $filter_speed ) . '"';
		}

		// Define filter prefix
		if ( $filter_taxonomy ) {

			// Get filter args
			$atts['filter_taxonomy'] = $filter_taxonomy;
			$args  = vcex_grid_filter_args( $atts, $vcex_query );
			$terms = get_terms( $filter_taxonomy, $args );

			// Set correct filter class prefix
			$filter_prefix = $atts['filter_taxonomy'];
			if ( 'post_tag' == $filter_prefix ) {
				$filter_prefix = $filter_prefix;
			} elseif ( 'category' == $filter_prefix ) {
				$filter_prefix = str_replace( 'category', 'cat', $filter_prefix );
			} else {
				$parse_types   = vcex_theme_post_types();
				$parse_types[] = 'post';
				foreach ( $parse_types as $type ) {
					if ( strpos( $filter_prefix, $type ) !== false ) {
						$search  = array( $type . '_category', 'category', $type .'_tag' );
						$replace = array( 'cat', 'cat', 'tag' );
						$filter_prefix = str_replace( $search, $replace, $filter_prefix );
					}
				}
			}

		}

		// Add active filter data
		if ( $filter_active_category ) {
			if ( $filter_taxonomy ) {
				$grid_data[] = 'data-filter=".' . esc_attr( $filter_prefix . '-' . $filter_active_category ) . '"';
			} else {
				$grid_data[] = 'data-filter=".' . esc_attr( $filter_active_category ) . '"';
			}
		}

	} else {

		$isotope_transition_duration = apply_filters( 'vcex_isotope_transition_duration', null, 'vcex_post_type_grid' );
		if ( $isotope_transition_duration ) {
			$grid_data[] = 'data-transition-duration="' . esc_attr( $isotope_transition ) . '"';
		}

	}

	// Entry CSS class
	if ( $entry_css ) {
		$entry_css = vcex_vc_shortcode_custom_css_class( $entry_css );
	}

	// Apply filters
	$wrap_classes  = apply_filters( 'vcex_post_type_grid_wrap_classes', $wrap_classes ); // @todo deprecate?
	$grid_classes  = apply_filters( 'vcex_post_type_grid_classes', $grid_classes );
	$grid_data     = apply_filters( 'vcex_post_type_grid_data_attr', $grid_data );

	// Convert arrays into strings
	$wrap_classes  = implode( ' ', $wrap_classes );
	$grid_classes  = implode( ' ', $grid_classes );
	$grid_data     = $grid_data ? ' ' . implode( ' ', $grid_data ) : '';

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_grid', $atts );

	// Start output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		//Heading
		if ( ! empty( $atts[ 'heading' ] ) ) {

			$output .= vcex_get_module_header( array(
				'style'   => ! empty( $atts[ 'header_style' ] ) ? $atts[ 'header_style' ] : '',
				'content' => $atts[ 'heading' ],
				'classes' => array( 'vcex-module-heading vcex_post_type_grid-heading' ),
			) );

		}

		// Display filter links
		if ( 'true' == $filter ) :

			// Make sure the filter should display
			if ( count( $post_types ) > 1 || 'taxonomy' == $filter_type ) {

				// Filter button classes
				$filter_button_classes = vcex_get_button_classes( $filter_button_style, $filter_button_color );

				// Filter font size
				$filter_style = vcex_inline_style( array(
					'font_size' => $filter_font_size,
				) );

				$filter_classes = 'vcex-post-type-filter vcex-filter-links clr';

				if ( 'yes' == $center_filter ) {
					$filter_classes .= ' center';
				}

				$output .= '<ul class="'. $filter_classes .'"'. $filter_style .'>';

					// Sanitize all text
					$all_text = $all_text ? $all_text : esc_html__( 'All', 'total' );

					$output .= '<li';

						if ( ! $filter_active_category ) {
							$output .= ' class="active"';
						}

					$output .= '>';

						$output .= '<a href="#" data-filter="*" class="' . esc_attr( $filter_button_classes ) . '"><span>' . esc_html( $all_text ) . '</span></a>';

					$output .= '</li>';

					// Taxonomy style filter
					if ( 'taxonomy' == $filter_type ) {

						// If taxonony exists get terms
						if ( $filter_taxonomy ) {

							// Get filter args
							$atts['filter_taxonomy'] = $filter_taxonomy;
							$args  = vcex_grid_filter_args( $atts, $vcex_query );
							$terms = get_terms( $filter_taxonomy, $args );

							// Display filter
							if ( ! empty( $terms ) ) {

								foreach ( $terms as $term ) :

									$output .= '<li class="filter-cat-'. $term->term_id;

										if ( $filter_active_category == $term->term_id ) {
											$output .= ' active';
										}

									$output .= '">';

										$output .= '<a href="#" data-filter=".' . esc_attr( $filter_prefix ) . '-' . esc_attr( $term->term_id ) . '" class="' . esc_attr( $filter_button_classes ) . '">';

											$output .= $term->name;

										$output .= '</a>';

									$output .= '</li>';

								endforeach;

							} // Terms check

						} // Taxonomy exists check

					// Post types filter
					} else {

						// Get array of post types in loop so we don't display empty results
						if ( 'true' == $atts['pagination_loadmore'] ) {
							$active_types = $post_types;
						} else {
							$active_types = array();
							$post_ids = wp_list_pluck( $vcex_query->posts, 'ID' );
							foreach ( $post_ids as $post_id ) {
								$type = get_post_type( $post_id );
								$active_types[$type] = $type;
							}
						}

						// Loop through active types
						foreach ( $active_types as $type ) :

							// Get type object
							$obj = get_post_type_object( $type );

							$output .= '<li class="vcex-filter-link-' . $type;

								if ( $filter_active_category == 'type-' . $type ) {
									$output .= ' active';
								}

							$output .= '">';

							$output .= '<a href="#" data-filter=".type-' . esc_attr( $type ) . '" class="' . esc_attr( $filter_button_classes ) . '">';

								$output .= $obj->labels->name;

							$output .= '</a></li>';

						endforeach;

					}

				$output .= '</ul>';

				if ( $vcex_after_grid_filter = apply_filters( 'vcex_after_grid_filter', '', $atts ) ) {
					$output .= $vcex_after_grid_filter;
				}

			}

		endif; // End filter

		$output .= '<div class="' . esc_attr( $grid_classes ) . '"' . $grid_data . '>';

			// Static entry classes
			$static_entry_classes = array( 'vcex-post-type-entry', 'vcex-grid-item', 'clr' );
			if ( 'false' == $columns_responsive ) {
				$static_entry_classes[] = 'nr-col';
			} else {
				$static_entry_classes[] = 'col';
			}
			$static_entry_classes[] = vcex_get_grid_column_class( $atts );
			if ( $is_isotope ) {
				$static_entry_classes[] = 'vcex-isotope-entry';
			}
			if ( 'no_margins' == $grid_style ) {
				$static_entry_classes[] = 'vcex-no-margin-entry';
			}
			if ( $css_animation ) {
				$static_entry_classes[] = $css_animation;
			}
			if ( $content_alignment ) {
				$static_entry_classes[] = 'text'. $content_alignment;
			}

			// Entry media classes
			$media_classes = array( 'vcex-post-type-entry-media', 'entry-media', 'wpex-clr' );
			if ( isset( $entry_blocks['media'] ) ) {
				if ( $img_filter ) {
					$media_classes[] = vcex_image_filter_class( $img_filter );
				}
				if ( $img_hover_style ) {
					$media_classes[] = vcex_image_hover_classes( $img_hover_style );
				}
				if ( $overlay_style ) {
					$media_classes[] = vcex_image_overlay_classes( $overlay_style );
				}
			} else {
				$static_entry_classes[] = 'vcex-post-type-no-media-entry';
			}
			$media_classes = implode( ' ', $media_classes );

			/**** Loop Start ***/
			$first_run = true;
			while ( $vcex_query->have_posts() ) :

				// Get post from query
				$vcex_query->the_post();

				// Add to counter var
				$entry_count++;

				// Set post ID
				$atts['post_id'] = get_the_ID();
				$post_id = $atts['post_id'];

				// Get post data
				$atts['post_type']         = get_post_type( $post_id );
				$atts['post_title']        = get_the_title();
				$atts['post_esc_title']    = vcex_esc_title();
				$atts['post_permalink']    = vcex_get_permalink( $post_id );
				$atts['post_format' ]      = get_post_format( $post_id );
				$atts['post_excerpt']      = '';
				$atts['post_thumbnail_id'] = get_post_thumbnail_id( $post_id );
				$atts['post_video_html']   = ( 'true' == $featured_video ) ? vcex_get_post_video_html() : '';
				$atts['lightbox_data']     = array();

				// Entry Classes
				$entry_classes   = array();
				$entry_classes[] = 'col-' . $entry_count;
				$entry_classes   = array_merge( $static_entry_classes, $entry_classes );

				// Entry image output HTML
				$entry_image = '';
				if ( $atts['post_thumbnail_id'] ) {

					// Define thumbnail args
					$thumbnail_args = array(
						'attachment'    => $atts['post_thumbnail_id'],
						'size'          => $img_size,
						'crop'          => $img_crop,
						'width'         => $img_width,
						'height'        => $img_height,
						'apply_filters' => 'vcex_post_type_grid_thumbnail_args',
						'filter_arg1'   => $atts,
					);

					// Add data-no-lazy to prevent conflicts with WP-Rocket
					if ( $is_isotope ) {
						$thumbnail_args['attributes'] = array( 'data-no-lazy' => 1 );
					}

					// Set entry image var
					$entry_image = vcex_get_post_thumbnail( $thumbnail_args );

				}
				$entry_image = apply_filters( 'vcex_post_type_grid_entry_image', $entry_image, $atts );

				// Get and save Lightbox data for use with Overlays, media, title, etc
				$oembed_url     = vcex_get_post_video_oembed_url( $post_id );
				$embed_url      = vcex_get_video_embed_url( $oembed_url ); // returns embed url and adds custom params filter
				$lightbox_image = vcex_get_lightbox_image();
				if ( $embed_url ) {
					$atts['lightbox_link'] = $embed_url;
					if ( $lightbox_image ) {
						$atts['lightbox_data']['data-thumb'] = 'data-thumb="' . $lightbox_image . '"';
					}
				} else {
					$atts['lightbox_link'] = $lightbox_image;
				}

				// Apply filters to attributes
				$latts = apply_filters( 'vcex_shortcode_loop_atts', $atts, 'vcex_post_type_grid' );

				// Begin entry output
				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $post_id ) . '>';

					// Inner entry classes
					$classes = 'vcex-post-type-entry-inner entry-inner clr';
					if ( $entry_css ) {
						$classes .= ' ' . $entry_css;
					}

					// Inner entry output
					$output .= '<div class="' . $classes . '">';

						// Display media
						if ( isset( $entry_blocks['media'] ) ) {

							$media_output = '';

							// Custom output
							if ( is_callable( $entry_blocks['media'] ) ) {
								$media_output .= call_user_func( $entry_blocks['media'] );
							}

							// Default module output
							else {

								// Display video
								if ( $latts['post_video_html'] ) {

									$media_output .= '<div class="vcex-post-type-entry-media entry-media clr">';

										$media_output .= '<div class="vcex-video-wrap">';

											$media_output .= $latts['post_video_html'];

										$media_output .= '</div>';

									$media_output .= '</div>';

								// Display featured image
								} elseif ( $entry_image ) {

									$media_link_attrs = array(
										'href'   => esc_url( $latts['post_permalink'] ),
										'title'  => $latts['post_esc_title'],
										'target' => $latts['url_target'],
										'class'  => '',
									);

									$media_output .= '<div class="' . esc_attr( $media_classes ) . '">';

										// Image with link
										if ( $thumb_link == 'post'
											|| $thumb_link == 'lightbox'
											|| $thumb_link == 'lightbox_gallery'
										) {

											// Lightbox
											if ( $thumb_link == 'lightbox' || 'lightbox_gallery' == $latts['thumb_link'] ) {

												// Lightbox post gallery
												if ( 'lightbox_gallery' == $latts['thumb_link']
													&& $lightbox_gallery_imgs = wpex_get_gallery_images( $latts['post_id'], 'lightbox' )
												) {
													$media_link_attrs['class'] .= ' wpex-lightbox-gallery';
													$media_link_attrs['data']   = 'data-gallery="' . vcex_parse_inline_lightbox_gallery( $lightbox_gallery_imgs ) . '"';
												}

												// Singular lightbox
												elseif ( ! empty( $latts['lightbox_link'] ) ) {
													$media_link_attrs['class']  = $media_link_attrs['class'] ? ' ' . $lightbox_single_class : $lightbox_single_class;
													$media_link_attrs['href']   = $latts['lightbox_link'];
													$media_link_attrs['data']   = $latts['lightbox_data'];
													$media_link_attrs['target'] = '';
												}

											} else {

												// Lightbox disabled
												$latts['lightbox_link'] = null; // prevents issues w/ overlay button hover

											}

											$media_output .= '<a' . vcex_parse_html_attributes( $media_link_attrs ) . '>';

												$media_output .= $entry_image;

												if ( $overlay_style && 'none' != $overlay_style ) {
													ob_start();
													vcex_image_overlay( 'inside_link', $overlay_style, $latts );
													$media_output .= ob_get_clean();
												}

												$media_output .= vcex_get_entry_media_after( 'vcex_post_type_grid' );

											$media_output .= '</a>';

										// Just the image
										} else {

											// Display image
											$media_output .= $entry_image;

											// After image filter
											$media_output .= vcex_get_entry_media_after( 'vcex_post_type_grid' );

											// Inside overlay
											if ( $overlay_style && 'none' != $overlay_style ) {
												ob_start();
												vcex_image_overlay( 'inside_link', $overlay_style, $latts );
												$media_output .= ob_get_clean();
											}

										}

										// Outside link overlay
										if ( $overlay_style && 'none' != $overlay_style ) {
											ob_start();
											vcex_image_overlay( 'outside_link', $overlay_style, $latts );
											$media_output .= ob_get_clean();
										}

									$media_output .= '</div>';

								}

							}

							$output .= apply_filters( 'vcex_post_type_grid_media', $media_output, $atts );

						} // End media check

						// Display entry details (title, date, categories, excerpt, button )
						if ( isset( $entry_blocks['title'] )
							|| isset( $entry_blocks['date'] )
							|| isset( $entry_blocks['categories'] )
							|| isset( $entry_blocks['excerpt'] )
							|| isset( $entry_blocks['read_more'] )
						) {

							if ( $first_run ) {

								// Content classes
								$detail_classes = 'vcex-post-type-entry-details entry-details wpex-clr';

								// Content Design
								$content_style = array(
									'color'   => $content_color,
									'opacity' => $content_opacity,
								);

								if ( $content_css ) {
									$content_css = vcex_vc_shortcode_custom_css_class( $content_css );
									$detail_classes .= ' ' . $content_css;
								} else {
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

								}

								$content_style = vcex_inline_style( $content_style );

							}

							$output .= '<div class="' . esc_attr( $detail_classes ) . '"' . $content_style . '>';

								// Open equal heights wrapper
								if ( $equal_heights_grid ) {
									$output .= '<div class="match-height-content">';
								}

								// Entry blocks (except media since it's inside it's own wrapper)
								foreach ( $entry_blocks as $k => $v ) :

									// Media shouldn't be here
									if ( 'media' == $k ) {
										continue;
									}

									// Custom output
									elseif ( $v && is_callable( $v ) ) {
										$output .= call_user_func( $v );
									}

									// Entry title
									elseif ( 'title' == $k ) {

										if ( $first_run ) {

											$heading_style = vcex_inline_style( array(
												'margin'         => $content_heading_margin,
												'font_size'      => $content_heading_size,
												'color'          => $content_heading_color,
												'line_height'    => $content_heading_line_height,
												'text_transform' => $content_heading_transform,
												'font_weight'    => $content_heading_weight,
											) );

											$heading_link_style = vcex_inline_style( array(
												'color' => $content_heading_color,
											) );

										}

										$title_output = '';

										$title_output .= '<' . $title_tag_escaped . ' class="vcex-post-type-entry-title entry-title" ' . $heading_style . '>';

										if ( 'post' == $title_link ) {

											$link_attrs = array(
												'href'   => esc_url( $latts['post_permalink'] ),
												'target' => $latts['url_target'],
												'style'  => $heading_link_style,
											);

											$title_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

												$title_output .= wp_kses_post( $latts['post_title'] );

											$title_output .= '</a>';

										} else {

											$title_output .= $latts['post_title'];

										}

										$title_output .= '</' . $title_tag_escaped . ' >';

										$output .= apply_filters( 'vcex_post_type_grid_title', $title_output, $atts );

									}


									// Entry date
									elseif ( 'date' == $k ) {

										if ( $first_run ) {

											$date_style = vcex_inline_style( array(
												'color'     => $date_color,
												'font_size' => $date_font_size,
											) );

										}

										$date_output = '';

										$date_output .= '<div class="vcex-post-type-entry-date"' . $date_style . '>';

											// Get Tribe Events date
											if ( 'tribe_events' == $latts['post_type']
												&& class_exists( 'Tribe__Events__Main' )
												&& function_exists( 'wpex_get_tribe_event_date' )
											) {
												$instance = $unique_id ? $unique_id : 'vcex_post_type_grid';
												$latts['post_date'] = wpex_get_tribe_event_date( $instance );

											// Get standard date
											} else {
												$latts['post_date'] = get_the_date();
											}

											// Output date
											$date_output .= apply_filters( 'vcex_post_type_grid_date_inner', $latts['post_date'], $atts );

										$date_output .= '</div>';

										$output .= apply_filters( 'vcex_post_type_grid_date', $date_output, $atts );

									}

									// Display categories
									elseif ( 'categories' == $k ) {

										$categories_output = '';
										$get_categories = '';

										if ( taxonomy_exists( $categories_taxonomy ) ) {

												// Generate inline CSS for categories but we only need to do this 1x
												if ( $first_run ) {
													$categories_style = vcex_inline_style( array(
														'margin'    => $categories_margin,
														'font_size' => $categories_font_size,
														'color'     => $categories_color,
													) );
												}

												if ( 'true' == $show_first_category_only ) {

													if ( ! vcex_validate_boolean( $latts[ 'categories_links' ] ) ) {

														$get_categories = vcex_get_first_term( $latts['post_id'], $categories_taxonomy );

													} else {

														$get_categories = vcex_get_first_term_link( $latts['post_id'], $categories_taxonomy );

													}

												} else {

													$get_categories = vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $latts[ 'categories_links' ] ) );

												}

												$get_categories = apply_filters( 'vcex_post_type_grid_get_categories', $get_categories, $atts );

												if ( $get_categories ) {

													$categories_output .= '<div class="vcex-post-type-entry-categories entry-categories wpex-clr"' . $categories_style . '>';

														$categories_output .= $get_categories;

													$categories_output .= '</div>';

												}

										}

										$output .= apply_filters( 'vcex_post_type_grid_categories', $categories_output, $atts );

									}

									// Display excerpt
									elseif ( 'excerpt' == $k ) {

										if ( $first_run ) {

											$excerpt_style = vcex_inline_style( array(
												'font_size' => $content_font_size,
												'color'     => $content_color,
											) );

										}

										$excerpt_output = '';

										$excerpt_output .= '<div class="vcex-post-type-entry-excerpt entry-excerpt clr"' . $excerpt_style . '>';

											// Display Excerpt
											$excerpt_output .= vcex_get_excerpt( array(
												'length'  => $excerpt_length,
												'context' => 'vcex_post_type_grid',
											) );

										$excerpt_output .= '</div>';

										$output .= apply_filters( 'vcex_post_type_grid_excerpt', $excerpt_output, $atts );

									}

									// Display read more button
									elseif ( 'read_more' == $k ) {

										if ( $first_run ) {

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
											) );

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

										$readmore_output = '';

										$readmore_output .= '<div class="vcex-post-type-entry-readmore-wrap entry-readmore-wrap clr">';

											$attrs = array(
												'href'   => esc_url( $latts['post_permalink'] ),
												'class'  => $readmore_classes,
												'rel'    => 'bookmark',
												'target' => $latts['url_target'],
												'style'  => $readmore_style,
											);

											if ( $readmore_hover_data ) {
												$attrs['data-wpex-hover'] = $readmore_hover_data;
											}

											$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

												// Read more text
												$read_more_text = ! empty( $latts['read_more_text'] ) ? $latts['read_more_text'] : esc_html__( 'read more', 'total' );

												$readmore_output .= $read_more_text;

												if ( 'true' == $readmore_rarr ) {
													$readmore_output .= '<span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
												}

											$readmore_output .= '</a>';

										$readmore_output .= '</div>';

										$output .= apply_filters( 'vcex_post_type_grid_readmore', $readmore_output, $atts );

									}

								// End entry blocks
								endforeach;

								// Close equal heights wrap
								if ( $equal_heights_grid ) {
									$output .= '</div>';
								}

							$output .= '</div>';

						}

					$output .= '</div>';

				$output .= '</div>';

			// Reset count clear floats
			if ( $entry_count == $columns ) {
				$entry_count = 0;
			}

			$first_run = false; endwhile;

		$output .= '</div>'; // End grid classes

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
			$output .= vcex_get_loadmore_button( 'vcex_post_type_grid', $og_atts, $vcex_query );
		}

	$output .= '</div>'; // End module classes

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