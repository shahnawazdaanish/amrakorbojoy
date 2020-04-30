<?php
/**
 * Visual Composer Staff Carousel
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

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_staff_carousel', $atts, $this );

// Extract shortcode atts
extract( $atts );

// Inline check
$is_inline = vcex_vc_is_inline();

// Build the WordPress query
$atts['post_type'] = 'staff';
$atts['taxonomy']  = 'staff_category';
$atts['tax_query'] = '';
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$title   = ( ! $title ) ? 'true' : $title;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Prevent auto play in visual composer
	if ( $is_inline ) {
		$atts['auto_play'] = false;
	}

	// Items to scroll fallback for old setting
	if ( 'page' == $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'wpex-carousel-staff', 'clr', 'owl-carousel' );

	// Main carousel style & arrow position
	if ( $style && 'default' != $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Content alignment
	if ( $content_alignment ) {
		$wrap_classes[] = 'text'. $content_alignment;
	}

	// Arrow style
	$arrows_style = $arrows_style ? $arrows_style : 'default';
	$wrap_classes[] = 'arrwstyle-'. $arrows_style;

	// Arrow position
	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_classes[] = 'arrwpos-'. $arrows_position;
	}

	// Visibility
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}

	// CSS animation
	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Extra classes
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Entry media classes
	if ( 'true' == $media ) {
		$media_classes = array( 'wpex-carousel-entry-media', 'clr' );
		if ( $img_hover_style ) {
			$media_classes[] = vcex_image_hover_classes( $img_hover_style );
		}
		if ( $img_filter ) {
			$media_classes[] = vcex_image_filter_class( $img_filter );
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

	// Position design
	if ( 'true' == $position ) {
		$position_style = vcex_inline_style( array(
			'font_size'   => $position_size,
			'font_weight' => $position_weight,
			'margin'      => $position_margin,
			'color'       => $position_color,
		) );
	}

	// New content design settings
	if ( $content_css ) {
		$content_css = ' '. vcex_vc_shortcode_custom_css_class( $content_css );
	}
	// Old content design settings
	else {
		$content_style = array(
			'background' => $content_background,
			'padding'    => $content_padding,
			'margin'     => $content_margin,
			'border'     => $content_border,
		);
	}
	$content_style['opacity'] = $content_opacity;
	$content_style = vcex_inline_style( $content_style );

	// Social links style
	if ( 'true' == $social_links ) {
		$social_links_inline_css = vcex_inline_style( array(
			'margin' => $social_links_margin,
		), false );
	}

	// Title design
	if ( 'true' == $title ) {

		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'text_transform' => $content_heading_transform,
			'font_weight'    => $content_heading_weight,
			'font_size'      => $content_heading_size,
			'line_height'    => $content_heading_line_height,
		) );

		$heading_link_style = vcex_inline_style( array(
			'color' => $content_heading_color,
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

	// Disable autoplay
	if ( $is_inline || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Turn array to strings
	$wrap_classes = implode( ' ', $wrap_classes );

	// VC filter
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_staff_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_staff_carousel-heading' ),
		) );

	}

	// Begin carousel output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_staff_carousel' ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Loop through posts
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']        = get_the_ID();
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_esc_title'] = esc_attr( $atts['post_title'] );

			$output .= '<div class="wpex-carousel-slide wpex-clr">';

				// Media Wrap
				$media_output = '';
				if ( 'true' == $media && has_post_thumbnail() ) {

					// Generate featured image
					$thumbnail = vcex_get_post_thumbnail( array(
						'size'          => $img_size,
						'crop'          => $img_crop,
						'width'         => $img_width,
						'height'        => $img_height,
						'attributes'    => array( 'data-no-lazy' => 1 ),
						'apply_filters' => 'vcex_staff_carousel_thumbnail_args',
					) );

					$media_output .= '<div class="' . $media_classes . '">';

						// No links
						if ( in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) {

							$media_output .= $thumbnail;

							$media_output .= vcex_get_entry_media_after( 'vcex_staff_carousel' );

						}
						// Lightbox
						elseif ( 'lightbox' == $thumbnail_link ) {

							$lcount ++;

							$link_attrs = array(
								'href'       => vcex_get_lightbox_image(),
								'class'      => 'wpex-carousel-entry-img',
								'title'      => $atts['post_esc_title'],
								'data-title' => $atts['post_esc_title'],
								'data-count' => $lcount,
							);

							if ( 'true' == $lightbox_gallery ) {
								$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
							} else {
								$link_attrs['class'] .= ' wpex-lightbox';
							}

							$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

								$media_output .= $thumbnail;

						}
						// Link to post
						else {

							$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . $atts['post_esc_title'] . '" class="wpex-carousel-entry-img">';

								$media_output .= $thumbnail;

						}

						// Inner Overlay
						if ( 'none' != $overlay_style ) {
							ob_start();
							vcex_image_overlay( 'inside_link', $overlay_style, $atts );
							$media_output .= ob_get_clean();
						}

						// Close link
						if ( ! in_array( $thumbnail_link, array( 'none', 'nowhere' ) ) ) {

							$media_output .= vcex_get_entry_media_after( 'vcex_staff_carousel' );

							$media_output .= '</a>';

						}

						// Outside Overlay
						if ( 'none' != $overlay_style ) {
							ob_start();
							vcex_image_overlay( 'outside_link', $overlay_style, $atts );
							$media_output .= ob_get_clean();
						}

					$media_output .= '</div>';

				} // End media

				$output .= apply_filters( 'vcex_staff_carousel_media', $media_output, $atts );

				// Title, Postion, Excerpt and Social links
				if ( 'true' == $title
					|| 'true' == $show_categories
					|| 'true' == $position
					|| 'true' == $excerpt
					|| 'true' == $social_links
					|| 'true' == $read_more
				) {

					$output .= '<div class="wpex-carousel-entry-details clr' . $content_css . '"' . $content_style . '>';

						// Title
						$title_output = '';
						if ( 'true' == $title ) {

							$title_output .= '<div class="wpex-carousel-entry-title entry-title"' . $heading_style . '>';

								if ( 'nowhere' == $title_link ) {

									$title_output .= wp_kses_post( $atts['post_title'] );

								} else {

									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '"' . $heading_link_style . '>';

										$title_output .= wp_kses_post( $atts['post_title'] );

									$title_output .= '</a>';

								}

							$title_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_staff_carousel_title', $title_output, $atts );

						// Display position
						$position_output = '';
						if ( 'true' == $position ) {

							if ( $get_position = get_post_meta( $atts['post_id'], 'wpex_staff_position', true ) ) {

								$position_output .= '<div class="staff-entry-position"' . $position_style . '>';

									$position_output .= apply_filters( 'wpex_staff_entry_position', wp_kses_post( $get_position ) );

								$position_output .= '</div>';

							}

						}
						$output .= apply_filters( 'vcex_staff_carousel_position', $position_output, $atts );

						// Categories
						$categories_output = '';
						if ( 'true' == $show_categories ) {

							if ( $first_run ) {
								$categories_style = vcex_inline_style( array(
									'padding'   => $categories_margin,
									'font_size' => $categories_font_size,
									'color'     => $categories_color,
								) );
								$categories_classes = 'staff-entry-categories wpex-clr';
								if ( $categories_color ) {
									$categories_classes .= ' wpex-child-inherit-color';
								}
							}

							$categories_output .= '<div class="' . $categories_classes . '"' . $categories_style . '>';

								if ( 'true' == $show_first_category_only ) {

									$categories_output .= vcex_get_first_term_link( $atts['post_id'], 'staff_category' );

								} else {

									$categories_output .= vcex_get_list_post_terms( 'staff_category', true );

								}

							$categories_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_staff_carousel_categories', $categories_output, $atts );

						// Check if the excerpt is enabled
						$excerpt_output = '';
						if ( 'true' == $excerpt ) {

							// Generate excerpt
							$atts['post_excerpt'] = vcex_get_excerpt( array(
								'length'  => intval( $excerpt_length ),
								'context' => 'vcex_staff_carousel',
							) );

							// Display excerpt if there is one
							if ( $atts['post_excerpt'] ) {

								$excerpt_output .= '<div class="wpex-carousel-entry-excerpt clr">';

									$excerpt_output .= $atts['post_excerpt'];

								$excerpt_output .= '</div>';

							}

							$output .= apply_filters( 'vcex_staff_carousel_excerpt', $excerpt_output, $atts );

						} // End excerpt

						// Check if social is enabled
						$social_output = '';
						if ( 'true' == $social_links ) {

							if ( function_exists( 'wpex_get_staff_social' ) ) {

								$social_output .= wpex_get_staff_social( array(
									'style'        => $social_links_style,
									'font_size'    => $social_links_size,
									'inline_style' => $social_links_inline_css,
								) );

							}

							$output .= apply_filters( 'vcex_staff_carousel_social', $social_output, $atts );

						} // End social check

						// Display read more button if $read_more is true
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

							$output .= apply_filters( 'vcex_staff_carousel_readmore', $readmore_output, $atts );

						} // End readmore check

					$output .= '</div>';

				} // End content

			$output .= '</div>';

		// End loop
		$first_run = false;
		endwhile;

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