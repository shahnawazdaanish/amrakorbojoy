<?php
/**
 * Visual Composer Post Type Carousel
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.2
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

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_type_carousel', $atts, $this );
extract( $atts );

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// Extract attributes
	extract( $atts );

	// Disable auto play if there is only 1 post
	if ( '1' == count( $vcex_query->posts ) ) {
		$auto_play = false;
	}

	// Prevent auto play in visual composer
	if ( vcex_vc_is_inline() ) {
		$atts['auto_play'] = false;
	}

	// Items to scroll fallback for old setting
	if ( 'page' == $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'wpex-carousel-post-type', 'wpex-clr', 'owl-carousel' );

	// Carousel style
	if ( $style && 'default' != $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Arrow style
	$arrows_style = $arrows_style ? $arrows_style : 'default';
	$wrap_classes[] = 'arrwstyle-' . $arrows_style;

	// Arrow position
	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_classes[] = 'arrwpos-' . $arrows_position;
	}

	// Visibility
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}

	// CSS animation
	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Custom Classes
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Entry media classes
	if ( 'true' == $media ) {
		$media_classes = array( 'wpex-carousel-entry-media', 'wpex-clr' );
		if ( $img_hover_style ) {
			$media_classes[] = vcex_image_hover_classes( $img_hover_style );
		}
		if ( $overlay_style ) {
			$media_classes[] = vcex_image_overlay_classes( $overlay_style );
		}
		if ( 'lightbox' == $thumbnail_link ) {
			vcex_enqueue_lightbox_scripts();
			if ( 'true' == $lightbox_gallery ) {
				$wrap_classes[] = 'wpex-carousel-lightbox';
			}
		}
		$media_classes = implode( ' ', $media_classes );
	}

	// Content Design
	$content_style = vcex_inline_style( array(
		'color'      => $content_color,
		'text_align' => $content_alignment,
		'font_size'  => $content_font_size,
	) );
	$content_css = $content_css ? ' ' . vcex_vc_shortcode_custom_css_class( $content_css ) : '';

	// Title design
	if ( 'true' == $title ) {

		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'text_transform' => $content_heading_transform,
			'font_size'      => $content_heading_size,
			'font_weight'    => $content_heading_weight,
			'line_height'    => $content_heading_line_height,
		) );

		$content_heading_color = vcex_inline_style( array(
			'color' => $content_heading_color,
		) );

	}

	// Date design
	if ( 'true' == $date ) {

		$date_style = vcex_inline_style( array(
			'color'     => $date_color,
			'font_size' => $date_font_size,
			'margin'    => $date_margin,
		) );

	}

	// Readmore design and classes
	if ( 'true' == $read_more ) {

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

	// Apply filters
	$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_post_type_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_post_type_carousel-heading' ),
		) );

	}

	// Begin output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_post_type_carousel' ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Define entry classes
		$entry_classes = array( 'wpex-carousel-slide', 'wpex-clr' );
		if ( $entry_css ) {
			$entry_classes[] = vcex_vc_shortcode_custom_css_class( $entry_css );
		}

		// Start loop
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']        = get_the_ID();
			$atts['post_type']      = get_post_type( $atts['post_id'] );
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_title_esc'] = vcex_esc_title( $atts['post_id'] );

			// Only display carousel item if there is content to show
			if ( ( 'true' == $media && has_post_thumbnail() )
				|| 'true' == $title
				|| 'true' == $date
				|| 'true' == $excerpt
				|| 'true' == $read_more
			) :

				$output .= '<div ' . vcex_grid_get_post_class( $entry_classes, $atts['post_id'] ) . '>';

					// Display media
					$media_output = '';
					if ( 'true' == $media ) {

						if ( has_post_thumbnail() ) {

							// Generate image html
							$img_html = vcex_get_post_thumbnail( array(
								'size'          => $img_size,
								'crop'          => $img_crop,
								'width'         => $img_width,
								'height'        => $img_height,
								'attributes'    => array( 'data-no-lazy' => 1 ),
								'apply_filters' => 'vcex_post_type_carousel_thumbnail_args',
								'filter_arg1'   => $atts,
							) );

							$media_output .= '<div class="'. $media_classes .'">';

								// No links
								if ( 'none' == $thumbnail_link ) {

									$media_output .= $img_html;

									$media_output .= vcex_get_entry_media_after( 'vcex_post_type_carousel' );

								// Lightbox
								} elseif ( 'lightbox' == $thumbnail_link ) {

									$lcount++;

									$atts['lightbox_link'] = vcex_get_lightbox_image(); // Escaped already

									$link_attrs = array(
										'href'       => $atts['lightbox_link'],
										'title'      => $atts['post_title_esc'],
										'class'      => 'wpex-carousel-entry-img',
										'data-count' => absint( $lcount )
									);

									if ( 'true' == $lightbox_gallery ) {
										$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
									} else {
										$link_attrs['class'] .= ' wpex-lightbox';
									}

									$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

										$media_output .= $img_html;

								// Link to post
								} else {

									$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="'. $atts['post_title_esc'] .'" class="wpex-carousel-entry-img">';

										$media_output .= $img_html;

								} // End thumbnail_link check

								// Overlay & close link
								if ( 'none' != $thumbnail_link ) {

									$media_output .= vcex_get_entry_media_after( 'vcex_post_type_carousel' );

									// Inner Overlay
									if ( 'none' != $overlay_style ) {
										ob_start();
										vcex_image_overlay( 'inside_link', $overlay_style, $atts );
										$media_output .= ob_get_clean();
									}

									// Close link
									$media_output .= '</a>';

								}

								// Outside Overlay
								if ( 'none' != $overlay_style ) {
									ob_start();
									vcex_image_overlay( 'outside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();
								}

							$media_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_post_type_carousel_media', $media_output, $atts );

					}

					// Display content area
					if ( 'true' == $title || 'true' == $excerpt || 'true' == $date || 'true' == $read_more ) {

						$output .= '<div class="wpex-carousel-entry-details wpex-clr' .  $content_css . '"' . $content_style . '>';

							// Title
							$title_output = '';
							if ( 'true' == $title && $atts['post_title'] ) {

								$title_output .= '<div class="wpex-carousel-entry-title entry-title"' . $heading_style . '>';

									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '"' . $content_heading_color . '>';

										$title_output .= wp_kses_post( $atts['post_title'] );

									$title_output .= '</a>';

								$title_output .= '</div>';

								$output .= apply_filters( 'vcex_post_type_carousel_title', $title_output, $atts );

							} // End title check

							// Display publish date if $date is enabled
							$date_output = '';
							if ( 'true' == $date ) {

								$date_output .= '<div class="vcex-carousel-entry-date wpex-clr"'. $date_style .'>';

									// Events calendar date
									if ( 'tribe_events' == $atts['post_type'] && function_exists( 'tribe_get_start_date' ) ) {

										$date_output .= esc_html( tribe_get_start_date( $atts['post_id'], false, get_option( 'date_format' ) ) );

									}

									// Standard publish date
									else {

										$date_output .= get_the_date();

									}

								$date_output .= '</div>';

								$output .= apply_filters( 'vcex_post_type_carousel_date', $date_output, $atts );

							} // End date check

							// Categories
							if ( 'true' == $show_categories ) {

								$categories_output = '';

								if ( $first_run ) {

									$categories_style = vcex_inline_style( array(
										'margin'    => $categories_margin,
										'font_size' => $categories_font_size,
										'color'     => $categories_color,
									) );

									$categories_classes = 'vcex-post-type-entry-categories entry-categories wpex-clr';

								}

								$categories_output = '';

								if ( taxonomy_exists( $categories_taxonomy ) ) {

									$categories_output .= '<div class="' . esc_attr( $categories_classes ) . '"' . $categories_style . '>';

										if ( 'true' == $show_first_category_only ) {

											if ( ! vcex_validate_boolean( $atts[ 'categories_links' ] ) ) {

												$categories_output .= vcex_get_first_term( $atts['post_id'], $categories_taxonomy );

											} else {

												$categories_output .= vcex_get_first_term_link( $atts['post_id'], $categories_taxonomy );

											}

										} else {

											$categories_output .= vcex_get_list_post_terms( $categories_taxonomy, vcex_validate_boolean( $atts[ 'categories_links' ] ) );

										}

									$categories_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_post_type_carousel_categories', $categories_output, $atts );

							}  // End categories

							// Excerpt
							$excerpt_output = '';
							if ( 'true' == $excerpt ) {

								// Generate excerpt
								$atts['post_excerpt'] = vcex_get_excerpt( array(
									'length'  => $excerpt_length,
									'context' => 'vcex_post_type_carousel',
								) );

								if ( $atts['post_excerpt'] ) {

									$excerpt_output .= '<div class="wpex-carousel-entry-excerpt wpex-clr">';

										$excerpt_output .= $atts['post_excerpt']; // Sanitized already via vcex_get_excerpt

									$excerpt_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_post_type_carousel_excerpt', $excerpt_output, $atts );

							} // End excerpt check

							// Display read more button if $read_more is true and $read_more_text isn't empty
							$readmore_output = '';
							if ( 'true' == $read_more ) {

								$readmore_output .= '<div class="entry-readmore-wrap clr">';

									$attrs = array(
										'href'  => esc_url( $atts['post_permalink'] ),
										'class' => $readmore_classes,
										'rel'   => 'bookmark',
										'style' => $readmore_style,
									);

									if ( $readmore_hover_data ) {
										$attrs['data-wpex-hover'] = $readmore_hover_data;
									}

									$readmore_output .= '<a' . vcex_parse_html_attributes( $attrs ) . '>';

										$readmore_output .= $read_more_text;

										if ( 'true' == $readmore_rarr ) {
											$readmore_output .= ' <span class="vcex-readmore-rarr">' . vcex_readmore_button_arrow() . '</span>';
										}

									$readmore_output .= '</a>';

								$readmore_output .= '</div>';

								$output .= apply_filters( 'vcex_post_type_carousel_readmore', $readmore_output, $atts );

						} // End readmore check

						$output .= '</div>';

					} // End content area check

				$output .= '</div>';

			endif;

		$first_run = false; endwhile;

	$output .= '</div>';

	// @codingStandardsIgnoreLine
	echo $output;

	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata();

// If no posts are found display message
else :

	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts );

// End post check
endif;