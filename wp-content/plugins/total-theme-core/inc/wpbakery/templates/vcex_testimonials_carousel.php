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

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_testimonials_carousel', $atts, $this );

// Define attributes
$atts['post_type'] = 'testimonials';
$atts['taxonomy']  = 'testimonials_category';
$atts['tax_query'] = '';

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// Extract attributes
	extract( $atts );

	// Define wrap attributes
	$wrap_attrs = array();

	// Add unique ID to wrap attributes
	if ( $unique_id ) {
		$wrap_attrs['id'] = esc_attr( $unique_id );
	}

	// Main Wrap Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'vcex-testimonials-carousel', 'clr', 'owl-carousel' );

	$arrows_style = $arrows_style ? $arrows_style : 'default';
	$wrap_classes[] = 'arrwstyle-' . $arrows_style;

	if ( $arrows_position && 'default' != $arrows_position ) {
		$wrap_classes[] = 'arrwpos-' . $arrows_position;
	}

	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}

	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	if ( $css ) {
		$wrap_classes[] = vcex_vc_shortcode_custom_css_class( $css );
	}

	$wrap_attrs['class'] = esc_attr( vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ) , 'vcex_testimonials_carousel', $atts ) );

	// Disable autoplay
	if ( vcex_vc_is_inline() || '1' == count( $vcex_query->posts ) ) {
		$atts['auto_play'] = false;
	}

	// Image Style
	$img_style = vcex_inline_style( array(
		'border_radius' => $img_border_radius,
	), false );

	// Image classes
	$thumb_classes = '';
	if ( $img_width || $img_height || ! in_array( $img_size, array( 'wpex_custom', 'testimonials_entry' ) ) ) {
		$thumb_classes = ' custom-dims';
	} else {
		$thumb_classes = ' default-dims';
	}

	// Load Google fonts if needed
	if ( $title_font_family ) {
		vcex_enqueue_google_font( $title_font_family );
	}

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

	// Open wrapper for auto height
	if ( 'true' == $auto_height ) {
		$output .= '<div class="owl-wrapper-outer">';
	}

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_testimonials_carousel-heading' ),
		) );

	}

	// Begin output
	$output .= '<div' . vcex_parse_html_attributes( $wrap_attrs ) . ' data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_testimonials_carousel' ) . '">';

		// Start loop
		$count = 0;
		while ( $vcex_query->have_posts() ) :
			$count++;

			// Get post from query
			$vcex_query->the_post();

			// Post VARS
			$atts['post_id']           = get_the_ID();
			$atts['post_title']        = get_the_title();
			$atts['post_permalink']    = vcex_get_permalink();
			$atts['post_meta_author']  = get_post_meta( $atts['post_id'], 'wpex_testimonial_author', true );
			$atts['post_meta_company'] = get_post_meta( $atts['post_id'], 'wpex_testimonial_company', true );
			$atts['post_meta_url']     = get_post_meta( $atts['post_id'], 'wpex_testimonial_url', true );

			$output .= '<div class="wpex-carousel-slide">';

				$output .= '<div ' . vcex_get_post_class( array( 'testimonial-entry' ) ) . '>';

					$output .= '<div class="testimonial-entry-content clr">';

						$output .= '<span class="testimonial-caret"></span>';

						// Display title
						$title_output = '';
						if ( 'true' == $title ) :

							$title_output .= '<' . esc_html( $title_tag ) . ' class="testimonial-entry-title entry-title"' . $title_style . '>';

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

							$title_output .= '</' . esc_html( $title_tag ) . '>';

							$output .= apply_filters( 'vcex_testimonials_carousel_title', $title_output, $atts );

						endif;

						$output .= '<div class="testimonial-entry-details clr"'. $content_style .'>';

							// Display excerpt if enabled (default dispays full content )
							$excerpt_output = '';
							if ( 'true' == $excerpt ) :

								// Custom readmore text
								if ( 'true' == $read_more ) :

									// Add arrow
									if ( 'false' != $read_more_rarr ) {
										$read_more_rarr_html = '<span>&rarr;</span>';
									} else {
										$read_more_rarr_html = '';
									}

									// Read more text
									if ( is_rtl() ) {
										$read_more_link = '...<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . esc_attr( $read_more_text ) . '">' . wp_kses_post( $read_more_text ) .'</a>';
									} else {
										$read_more_link = '...<a href="' . esc_url( $atts['post_permalink'] ) . '" title="' . esc_attr( $read_more_text ) . '">' . wp_kses_post( $read_more_text ) . $read_more_rarr_html .'</a>';
									}

								else :

									$read_more_link = '...';

								endif;

								// Custom Excerpt function
								$excerpt_output .= vcex_get_excerpt( array(
									'post_id' => $atts['post_id'],
									'length'  => $excerpt_length,
									'more'    => $read_more_link,
									'context' => 'vcex_testimonials_carousel',
								) );

							// Display full post content
							else :

								$excerpt_output .= vcex_the_content( get_the_content(), 'vcex_testimonials_carousel' );

							// End excerpt check
							endif;

							$output .= apply_filters( 'vcex_testimonials_carousel_excerpt', $excerpt_output, $atts );

						$output .= '</div>';

					$output .= '</div>';

					$bottom_output = '';
					$bottom_output .= '<div class="testimonial-entry-bottom clr">';

						// Check if post thumbnail is defined
						$media_output = '';
						if ( has_post_thumbnail( $atts['post_id'] ) && 'true' == $entry_media ) {

							$media_output .= '<div class="testimonial-entry-thumb' . $thumb_classes . '">';

								// Display post thumbnail
								$media_output .= vcex_get_post_thumbnail( array(
									'attachment'    => get_post_thumbnail_id( $atts['post_id'] ),
									'size'          => $img_size,
									'width'         => $img_width,
									'height'        => $img_height,
									'style'         => $img_style,
									'crop'          => $img_crop,
									'attributes'    => array( 'data-no-lazy' => 1 ),
									'apply_filters' => 'vcex_testimonials_carousel_thumbnail_args',
									'filter_arg1'   => $atts,
								) );

							$media_output .= '</div>';

						}

						$bottom_output .= apply_filters( 'vcex_testimonials_carousel_media', $media_output, $atts );

						$bottom_output .= '<div class="testimonial-entry-meta">';

							// Display testimonial author
							$author_output = '';
							if ( 'true' == $author && $atts['post_meta_author'] ) :

								$author_output .= '<span class="testimonial-entry-author entry-title">';

									$author_output .= wp_kses_post( $atts['post_meta_author'] );

								$author_output .= '</span>';

								$bottom_output .= apply_filters( 'vcex_testimonials_carousel_author', $author_output, $atts );

							endif;

							// Display testimonial company
							$company_output = '';
							if ( 'true' == $company ) {

								if ( $atts['post_meta_company'] ) {

									// Display testimonial company with URL
									if ( $atts['post_meta_url'] ) {

										$company_output .= '<a href="'. esc_url( $atts['post_meta_url'] ) .'" class="testimonial-entry-company" title="'. $atts['post_meta_company'] .'" target="_blank">';

											$company_output .= wp_kses_post( $atts['post_meta_company'] );

										$company_output .= '</a>';

									// Display testimonial company without URL since it's not defined
									} else {

										$company_output .= '<span class="testimonial-entry-company">';

											$company_output .= wp_kses_post( $atts['post_meta_company'] );

										$company_output .= '</span>';

									}

								}

								$bottom_output .= apply_filters( 'vcex_testimonials_carousel_company', $company_output, $atts );

							}

							// Display rating
							$rating_output = '';
							if ( 'true' == $rating ) {

								if ( $atts['post_rating'] = vcex_get_star_rating( '', $atts['post_id'] ) ) {

									$rating_output .= '<div class="testimonial-entry-rating clr">' . $atts['post_rating'] . '</div>';

								}

								$bottom_output .= apply_filters( 'vcex_testimonials_carousel_rating', $rating_output, $atts );

							}

						$bottom_output .= '</div>'; // close meta

					$bottom_output .= '</div>'; // close bottom

					$output .= apply_filters( 'vcex_testimonials_carousel_bottom', $bottom_output, $atts );

				$output .= '</div>';

			$output .= '</div>';

		endwhile;

	$output .= '</div>';

	// Close wrap for single item auto height
	if ( 'true' == $auto_height ) {
		$output .= '</div>';
	}

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