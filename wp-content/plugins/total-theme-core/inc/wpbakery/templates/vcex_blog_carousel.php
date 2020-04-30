<?php
/**
 * Visual Composer Carousel
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

// Define output
$output = '';

// Deprecated Attributes
if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
	$atts['include_categories'] = $atts['term_slug'];
}

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_blog_carousel', $atts, $this );

// Define vars
$atts[ 'post_type' ] = 'post';
$atts[ 'taxonomy' ]  = 'category';
$atts[ 'tax_query' ] = '';

// Extract attributes
extract( $atts );

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Output posts
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// Sanitize & declare variables
	$overlay_style = $overlay_style ? $overlay_style : 'none';

	// IMPORTANT: Fallback required from VC update when params are defined as empty
	// AKA - set things to enabled by default
	$media   = ( ! $media ) ? 'true' : $media;
	$title   = ( ! $title ) ? 'true' : $title;
	$date    = ( ! $date ) ? 'true' : $date;
	$excerpt = ( ! $excerpt ) ? 'true' : $excerpt;

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'wpex-carousel-blog', 'owl-carousel', 'clr' );

	// Lightbox
	if ( 'lightbox' == $thumbnail_link && 'true' == $media ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_classes[] = 'wpex-carousel-lightbox';
		}
	}

	// Carousel style
	if ( $style && 'default' != $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Alignment
	if ( $content_alignment ) {
		$wrap_classes[] = sanitize_html_class( 'text' . $content_alignment );
	}

	// Arrow style
	$arrows_style = $arrows_style ? $arrows_style : 'default';
	$wrap_classes[] = sanitize_html_class( 'arrwstyle-' . $arrows_style );

	// Arrow position
	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_classes[] = sanitize_html_class( 'arrwpos-' . $arrows_position );
	}

	// Css animation
	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Extra classes
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Visibility
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}

	// Get carousel data settings
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Convert arrays to strings
	$wrap_classes = implode( ' ', $wrap_classes );
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_blog_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_blog_carousel-heading' ),
		) );

	}

	// Begin shortcode output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_blog_carousel' ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Start loop
		$lcount = 0;
		$first_run = true;
		while ( $vcex_query->have_posts() ) :

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']             = get_the_ID();
			$atts['post_permalink']      = vcex_get_permalink( $atts['post_id'] );
			$atts['post_title']          = get_the_title();
			$atts['post_esc_title']      = vcex_esc_title( $atts['post_id'] );
			$atts['post_thumbnail']      = get_post_thumbnail_id( $atts['post_id'] );
			$atts['post_thumbnail_link'] = $atts['post_permalink'];

			// Lets store the dynamic $atts['post_id'] into the shortcodes attributes
			$atts['post_id'] = $atts['post_id'];

			// Only display carousel item if there is content to show
			if ( ( 'true' == $media && $atts['post_thumbnail'] )
				|| 'true' == $title
				|| 'true' == $date
				|| 'true' == $excerpt
			) :

				// Entry classes
				$entry_classes = 'wpex-carousel-slide clr';
				if ( $atts['post_thumbnail'] ) {
					$entry_classes .= ' has-media';
				}

				$output .= '<div class="' . esc_attr( $entry_classes ) . '">';

					// Display thumbnail if enabled and defined
					if ( 'true' == $media ) {

						$media_output = '';

						if ( $first_run ) {

							$has_overlay = ( 'none' != $overlay_style ) ? true : false;

							$media_classes = array( 'wpex-carousel-entry-media entry-media', 'clr' );

							if ( $img_hover_style ) {
								$media_classes[] = vcex_image_hover_classes( $img_hover_style );
							}

							if ( $img_filter ) {
								$media_classes[] = vcex_image_filter_class( $img_filter );
							}

							if ( $overlay_style ) {
								$media_classes[] = vcex_image_overlay_classes( $overlay_style );
							}

							$media_classes = implode( ' ', $media_classes );
						}

						if ( $atts['post_thumbnail'] ) {

							$media_output .= '<div class="' . $media_classes . '">';

								// If thumbnail link doesn't equal none
								if ( 'none' != $thumbnail_link ) :

									// Lightbox thumbnail
									if ( 'lightbox' == $thumbnail_link ) {
										$atts[ 'lightbox_link' ]       = vcex_get_lightbox_image( $atts['post_thumbnail'] );
										$atts[ 'post_thumbnail_link' ] = $atts['lightbox_link'];
									}

									// Link attributes
									$link_attrs = array(
										'href'  => esc_url( $atts['post_thumbnail_link'] ),
										'title' => $atts['post_esc_title'],
										'class' => 'wpex-carousel-entry-img',
									);
									// Add lightbox link attributes
									if ( 'lightbox' == $thumbnail_link ) {
										$lcount++;
										if ( 'true' == $lightbox_gallery ) {
											$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
										} else {
											$link_attrs['class'] .= ' wpex-lightbox';
										}
										$link_attrs['data-title'] = $atts['post_esc_title'];
										$link_attrs['data-count'] = intval( $lcount );
									}

								$media_output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

								endif; // End thumbnail_link check

								// Display post thumbnail
								$media_output .= vcex_get_post_thumbnail( array(
									'attachment'    => $atts['post_thumbnail'],
									'width'         => $img_width,
									'height'        => $img_height,
									'size'          => $img_size,
									'crop'          => $img_crop,
									'attributes'    => array( 'data-no-lazy' => 1 ),
									'apply_filters' => 'vcex_blog_carousel_thumbnail_args',
									'filter_arg1'   => $atts,
								) );

								// Inner link overlay html
								if ( $has_overlay ) {
									ob_start();
									vcex_image_overlay( 'inside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();
								}

								// Entry after media hook
								$media_output .= vcex_get_entry_media_after( 'vcex_blog_carousel' );

								// Close link tag
								if ( 'none' != $thumbnail_link ) {
									$media_output .= '</a>';
								}

								// Outer link overlay HTML
								if ( $has_overlay ) {
									ob_start();
									vcex_image_overlay( 'outside_link', $overlay_style, $atts );
									$media_output .= ob_get_clean();
								}

							$media_output .= '</div>';

						}

						$output .= apply_filters( 'vcex_blog_carousel_media', $media_output, $atts );

					} // End media check

					// Open details element if the title or excerpt are true
					if ( 'true' == $title || 'true' == $date || 'true' == $excerpt || 'true' == $read_more ) {

						if ( $first_run ) {

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
							$content_style[ 'opacity' ] = $content_opacity;
							$content_style = vcex_inline_style( $content_style );

						}

						$output .= '<div class="wpex-carousel-entry-details clr' . $content_css . '"' . $content_style . '>';

							// Display title if $title is true and there is a post title
							if ( 'true' == $title ) {

								$title_output = '';

								if ( $first_run ) {
									$heading_style = vcex_inline_style( array(
										'margin'         => $content_heading_margin,
										'font_size'      => $content_heading_size,
										'font_weight'    => $content_heading_weight,
										'text_transform' => $content_heading_transform,
										'line_height'    => $content_heading_line_height,
										'color'          => $content_heading_color,

									) );
								}

								$title_output .= '<div class="wpex-carousel-entry-title entry-title"' . $heading_style . '>';

									$title_output .= '<a href="' . esc_url( $atts['post_permalink'] ) . '">';

										$title_output .= wp_kses_post( $atts['post_title'] );

									$title_output .= '</a>';

								$title_output .= '</div>';

								$output .= apply_filters( 'vcex_blog_carousel_title', $title_output, $atts );

							} // End title

							// Display publish date if $date is enabled
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

								$output .= apply_filters( 'vcex_blog_carousel_date', $date_output, $atts );

							}

							// Display excerpt if $excerpt is true
							if ( 'true' == $excerpt ) {

								$excerpt_output = '';

								if ( $first_run ) {
									$excerpt_styling = vcex_inline_style( array(
										'color'     => $content_color,
										'font_size' => $content_font_size,

									) );
								}

								$excerpt_output .= '<div class="wpex-carousel-entry-excerpt entry-excerpt vcex-clr"' . $excerpt_styling . '>';

								$excerpt_output .= vcex_get_excerpt( array(
									'length'  => $excerpt_length,
									'context' => 'vcex_blog_carousel',
								) );

								$excerpt_output .= '</div>';

								$output .= apply_filters( 'vcex_blog_carousel_excerpt', $excerpt_output, $atts );

							} // End excerpt check

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

									$readmore_attrs = array(
										'href'  => esc_url( $atts['post_permalink'] ),
										'class' => esc_attr( $readmore_classes ),
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

								$output .= apply_filters( 'vcex_blog_carousel_readmore', $readmore_output, $atts );

							}

						$output .= '</div>';

					} // End details check

				$output .= '</div>';

			endif; // End data check

		// End entry loop
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