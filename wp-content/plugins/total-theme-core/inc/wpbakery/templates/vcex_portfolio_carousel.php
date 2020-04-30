<?php
/**
 * Visual Composer Portfolio Carousel
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

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Define output
$output = '';

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_portfolio_carousel', $atts, $this );

// Define attributes
$atts['post_type'] = 'portfolio';
$atts['taxonomy']  = 'portfolio_category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// Extract attributes
	extract( $atts );

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$media   = ( ! $media ) ? 'true' : $media;
	$title   = ( ! $title ) ? 'true' : $title;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Items to scroll fallback for old setting
	if ( 'page' == $items_scroll ) {
		$items_scroll = $items;
	}

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'wpex-carousel-portfolio', 'clr', 'owl-carousel' );

	// Carousel style
	if ( $style && 'default' != $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Alignment
	if ( $content_alignment ) {
		$wrap_classes[] = 'text' . $content_alignment;
	}

	// Arrow style
	$arrows_style = $arrows_style ? $arrows_style : 'default';
	$wrap_classes[] = 'arrwstyle-' . $arrows_style;

	// Arrow position
	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_classes[] = 'arrwpos-' . $arrows_position;
	}

	// Visiblity
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}

	// CSS animations
	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Lightbox classes & scripts
	if ( 'lightbox' == $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_classes[] = 'wpex-carousel-lightbox';
		}
	}

	// Custom Classes
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Disable autoplay
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Turn arrays into strings
	$wrap_classes = implode( ' ', $wrap_classes );

	// Apply filters
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_portfolio_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_portfolio_carousel-heading' ),
		) );

	}

	// Begin output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_portfolio_carousel' ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Start loop
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']        = get_the_ID();
			$atts['post_permalink'] = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']     = get_the_title( $atts['post_id'] );
			$atts['post_esc_title'] = vcex_esc_title();
			$atts['post_format']    = get_post_format();

			$output .= '<div class="wpex-carousel-slide">';

				// Display media
				if ( 'true' == $media ) :

					$media_output = '';

					if ( $first_run ) {

						$media_classes = array( 'wpex-carousel-entry-media', 'clr' );
						if ( $img_hover_style ) {
							$media_classes[] = vcex_image_hover_classes( $img_hover_style );
						}
						if ( $overlay_style ) {
							$media_classes[] = vcex_image_overlay_classes( $overlay_style );
						}
						$media_classes = implode( ' ', $media_classes );

					}

					if ( has_post_thumbnail() ) {

						// Image html
						$img_html = vcex_get_post_thumbnail( array(
							'size'          => $img_size,
							'crop'          => $img_crop,
							'width'         => $img_width,
							'height'        => $img_height,
							'attributes'    => array( 'data-no-lazy' => 1 ),
							'apply_filters' => 'vcex_portfolio_carousel_thumbnail_args',
							'filter_arg1'   => $atts,
						) );

						$media_output .= '<div class="' . esc_attr( $media_classes ) . '">';

							// No links
							if ( 'none' == $thumbnail_link ) :

								$media_output .= $img_html;
								$media_output .= vcex_get_entry_media_after( 'vcex_portfolio_carousel' );

							// Lightbox
							elseif ( 'lightbox' == $thumbnail_link ) :

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

								$media_output .= '<a'. vcex_parse_html_attributes( $link_attrs ) . '>';

								$media_output .= $img_html;

							// Link to post
							else :

								$media_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . $atts['post_esc_title'] . '" class="wpex-carousel-entry-img">';

									$media_output .= $img_html;

							endif;

							// Overlay & close link
							if ( 'none' != $thumbnail_link ) {

								// Inner Overlay
								if ( $overlay_style && 'none' != $overlay_style ) {

									ob_start();
									vcex_image_overlay( 'inside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();

								}

								// Entry after media hook
								$media_output .= vcex_get_entry_media_after( 'vcex_portfolio_carousel' );

								// Close link
								$media_output .= '</a>';

								// Outside Overlay
								if ( $overlay_style && 'none' != $overlay_style ) {
									ob_start();
									vcex_image_overlay( 'outside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();
								}

							}

						$media_output .= '</div>';

					}

					$output .= apply_filters( 'vcex_portfolio_carousel_media', $media_output, $atts );

				endif;

				// Details
				if ( 'true' == $title || 'true' == $excerpt || 'true' == $read_more ) :

					if ( $first_run ) {

						// New content design settings
						if ( $content_css ) {
							$content_css = ' ' . vcex_vc_shortcode_custom_css_class( $content_css );
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

					}

					$output .= '<div class="wpex-carousel-entry-details clr' . $content_css . '"' . $content_style . '>';

						// Open equal heights
						// @todo support equal height for carousels
						//$output .= '<div class="match-height-content clr">';

							// Title
							if ( 'true' == $title && $atts['post_title'] ) {

								$title_output = '';

								if ( $first_run ) {

									// Title design
									$heading_style = vcex_inline_style( array(
										'margin'         => $content_heading_margin,
										'text_transform' => $content_heading_transform,
										'font_size'      => $content_heading_size,
										'font_weight'    => $content_heading_weight,
										'line_height'    => $content_heading_line_height,
									) );

									// Heading color
									$content_heading_color = vcex_inline_style( array(
										'color' => $content_heading_color,
									) );

								}

								$title_output .= '<div class="wpex-carousel-entry-title entry-title"'. $heading_style .'>';

									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '"' . $content_heading_color . '>';

										$title_output .= esc_html( $atts['post_title'] );

									$title_output .= '</a>';

								$title_output .= '</div>';

								$output .= apply_filters( 'vcex_portfolio_carousel_title', $title_output, $atts );

							}

							// Excerpt
							if ( 'true' == $excerpt ) {

								$excerpt_output = '';

								if ( $first_run ) {
									$excerpt_styling = vcex_inline_style( array(
										'color'     => $content_color,
										'font_size' => $content_font_size,
									) );
								}

								// Generate excerpt
								$atts['post_excerpt'] = vcex_get_excerpt( array(
									'length'  => $excerpt_length,
									'context' => 'vcex_portfolio_carousel',
								) );

								if ( $atts['post_excerpt'] ) {

									$excerpt_output .= '<div class="wpex-carousel-entry-excerpt clr"' . $excerpt_styling . '>';

										$excerpt_output .= $atts['post_excerpt']; // Escaped via wp_trim_words

									$excerpt_output .= '</div>';

								}

								$output .= apply_filters( 'vcex_portfolio_carousel_excerpt', $excerpt_output, $atts );

							}

							// Display read more button if $read_more is true
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
										$readmore_hover_data['background'] = $readmore_hover_background;
									}
									if ( $readmore_hover_color ) {
										$readmore_hover_data['color'] = $readmore_hover_color;
									}
									if ( $readmore_hover_data ) {
										$readmore_hover_data = htmlspecialchars( wp_json_encode( $readmore_hover_data ) );
									}

								}

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

								$output .= apply_filters( 'vcex_portfolio_carousel_readmore', $readmore_output, $atts );

							}

						//$output .= '</div>'; // End match height

					$output .= '</div>';

				endif;

			$output .= '</div>';

		$first_run = false; endwhile;

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