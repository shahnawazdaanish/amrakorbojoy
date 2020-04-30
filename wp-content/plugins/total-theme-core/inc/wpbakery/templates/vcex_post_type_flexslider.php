<?php
/**
 * Visual Composer Post Type Slider
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

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_type_flexslider', $atts, $this );
extract( $atts );

// Query posts with thumbnails_only
if ( 'over-image' == $caption_location ) {
	$atts['thumbnail_query'] = 'true';
}

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

//Output posts
if ( $vcex_query->have_posts() ) :

	// Load slider scripts
	if ( vcex_vc_is_inline() ) {
		vcex_enqueue_slider_scripts();
		vcex_enqueue_slider_scripts( true ); // needs both in builder incase user switches settings.
	} else {
		$noCarouselThumbnails = ( 'true' == $control_thumbs_carousel ) ? false : true;
		vcex_enqueue_slider_scripts( $noCarouselThumbnails );
	}

	$output = '';

	// Sanitize data, declate main vars & fallbacks
	$wrap_data  = array();
	$slideshow  = vcex_vc_is_inline() ? 'false' : $slideshow;
	$caption    = $caption ? $caption : 'true';
	$title      = $title ? $title : 'true';

	// Slider attributes
	if ( in_array( $animation, array( 'fade', 'fade_slides' ) ) ) {
		$wrap_data[] = 'data-fade="true"';
	}
	if ( apply_filters( 'vcex_sliders_disable_desktop_swipe', true, 'vcex_post_type_flexslider' ) ) {
		$wrap_data[] = 'data-touch-swipe-desktop="false"';
	}
	if ( 'true' == $randomize ) {
		$wrap_data[] = 'data-shuffle="true"';
	}
	if ( 'true' == $loop ) {
		$wrap_data[] = ' data-loop="true"';
	}
	if ( 'false' == $slideshow ) {
		$wrap_data[] = 'data-auto-play="false"';
	}
	if ( $slideshow && $slideshow_speed ) {
		$wrap_data[] = 'data-auto-play-delay="' . $slideshow_speed . '"';
	}
	if ( 'false' == $direction_nav ) {
		$wrap_data[] = 'data-arrows="false"';
	}
	if ( 'false' == $control_nav ) {
		$wrap_data[] = 'data-buttons="false"';
	}
	if ( 'false' == $direction_nav_hover ) {
		$wrap_data[] = 'data-fade-arrows="false"';
	}
	if ( 'true' == $control_thumbs ) {
		$wrap_data[] = 'data-thumbnails="true"';
	}
	if ( 'true' == $control_thumbs && 'true' == $control_thumbs_pointer ) {
		$wrap_data[] = 'data-thumbnail-pointer="true"';
	}
	if ( $animation_speed ) {
		$wrap_data[] = 'data-animation-speed="' . intval( $animation_speed ) . '"';
	}
	if ( $height_animation ) {
		$height_animation = intval( $height_animation );
		$height_animation = 0 == $height_animation ? '0.0' : $height_animation;
		$wrap_data[] = 'data-height-animation-duration="'. $height_animation . '"';
	}
	if ( 'true' == $control_thumbs && $control_thumbs_height ) {
		$wrap_data[] = 'data-thumbnail-height="' . intval( $control_thumbs_height ) . '"';
	}
	if ( 'true' == $control_thumbs && $control_thumbs_width ) {
		$wrap_data[] = 'data-thumbnail-width="' . intval( $control_thumbs_width ) . '"';
	}

	// Caption attributes and classes
	$caption_data = '';
	$caption_classes = array( 'wpex-slider-caption', 'clr' );
	if ( 'over-image' == $caption_location ) {
		$caption_classes[] = 'sp-static sp-layer sp-black';
		$caption_data      = ' data-width="100%" data-position="bottomLeft"';
	}
	$caption_classes[] = $caption_location;
	if ( $caption_visibility ) {
		$caption_classes[] = $caption_visibility;
	}
	$caption_classes = implode( ' ', $caption_classes );

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'vcex-posttypes-slider', 'wpex-slider', 'slider-pro', 'vcex-image-slider', 'clr' );
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}
	if ( 'under-image' == $caption_location ) {
		$wrap_classes[] = 'arrows-topright';
	}
	if ( $visibility ) {
		$wrap_classes[] = $visibility;
	}
	if ( 'true' == $excerpt && $excerpt_length ) {
		$wrap_classes[] = 'vcex-posttypes-slider-w-excerpt';
	}
	if ( 'true' == $control_thumbs ) {
		$wrap_classes[] = 'vcex-posttypes-slider-w-thumbnails';
	}

	// Convert arrays into strings
	$wrap_classes = implode( ' ', $wrap_classes );
	$wrap_data    = ' ' . implode( ' ', $wrap_data );

	// Apply filters
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_flexslider', $atts );

	// Open css wrapper
	if ( $css ) {

		$output .= '<div class="vcex-posttype-slider-css-wrap ' . vcex_vc_shortcode_custom_css_class( $css ) . '">';

	}

	// Display the first image of the slider as a "preloader"
	if ( $first_post = $vcex_query->posts[0]->ID ) {

		$output .= '<div class="wpex-slider-preloaderimg">';

			$output .= vcex_get_post_thumbnail( array(
				'attachment'    => get_post_thumbnail_id( $first_post ),
				'size'          => $img_size,
				'crop'          => $img_crop,
				'width'         => $img_width,
				'height'        => $img_height,
				'attributes'    => array( 'data-no-lazy' => 1 ),
				'apply_filters' => 'vcex_post_type_flexslider_thumbnail_args',
			) );

		$output .= '</div>';

	}

	$output .= '<div class="' . esc_attr( $wrap_classes ) . '"' . vcex_unique_id( $unique_id ) . $wrap_data . '>';

		$output .= '<div class="wpex-slider-slides sp-slides">';

				// Store posts in an array for use with the thumbnails later
				$posts_cache = array();

				// Loop through posts
				while ( $vcex_query->have_posts() ) :

					// Get post from query
					$vcex_query->the_post();

					if ( ! has_post_thumbnail() ) {
						continue;
					}

					// Get post data
					$post_id   = get_the_ID();
					$post_type = get_post_type();
					$permalink = vcex_get_permalink();
					$esc_title = vcex_esc_title();

					// Store post ids
					$posts_cache[] = $post_id;

					$output .= '<div class="wpex-slider-slide sp-slide">';

						$output .= '<div class="wpex-slider-media">';

							$output .= '<a ' . vcex_parse_html_attributes( array(
								'href'   => esc_url( $permalink ),
								'title'  => $esc_title,
								'target' => $link_target,
								'class'  => 'wpex-slider-media-link',
							) ) . '>';

								$output .= vcex_get_post_thumbnail( array(
									'size'          => $img_size,
									'crop'          => $img_crop,
									'width'         => $img_width,
									'height'        => $img_height,
									'attributes'    => array( 'data-no-lazy' => 1 ),
									'apply_filters' => 'vcex_post_type_flexslider_thumbnail_args',
								) );

							$output .= '</a>';

							// WooComerce Price
							if ( 'product' == $post_type ) {

								$output .= '<div class="slider-woocommerce-price">';

									$output .= vcex_get_woo_product_price();

								$output .= '</div>';

							}

							if ( 'true' == $caption ) {

								$output .= '<div class="' . esc_attr( $caption_classes ) . '"' . $caption_data . '>';

									if ( 'true' == $title || 'true' == $meta ) {

										$output .= '<header class="vcex-posttype-slider-header clr">';

											// Display title
											if ( 'true' == $title ) {

												$output .= '<div class="vcex-posttype-slider-title entry-title wpex-em-18px">';

													$output .= '<a ' . vcex_parse_html_attributes( array(
														'href'   => esc_url( $permalink ),
														'title'  => $esc_title,
														'target' => $link_target,
														'class'  => 'title',
													) ) . '>' . wp_kses_post( get_the_title() ) . '</a>';

												$output .= '</div>';

											}

											// Meta
											if ( 'true' == $meta ) {

												$output .= '<ul class="vcex-posttypes-slider-meta meta clr">';

													if ( 'staff' == $post_type && $position = get_post_meta( $post_id, 'wpex_staff_position', true ) ) {

														$output .= '<div class="staff-position">';

															$output .= do_shortcode( wp_kses_post( $position ) );

														$output .= '</div>';

													}

													if ( 'staff' != $post_type ) {

														$output .= '<li class="meta-date"><span class="ticon ticon-clock-o"></span><span class="updated">' . esc_html( get_the_date() ) . '</span></li>';

														$output .= '<li class="meta-author"><span class="ticon ticon-user-o"></span><span class="vcard author">' . get_the_author_posts_link() . '</span></li>';

														// Display category
														if ( 'yes' != $tax_query
															&& $category = vcex_get_post_type_cat_tax( $post_type )
														) {

															$output .= '<li class="meta-category"><span class="ticon ticon-folder-open-o"></span>' . vcex_get_list_post_terms( $category ) . '</li>';

														}

													}

												$output .= '</ul>';

											}

										$output .= '</header>';

									}

									// Display excerpt
									if ( 'true' == $excerpt && $excerpt_length ) {

										$output .= '<div class="excerpt clr">';

											$output .= vcex_get_excerpt( array(
												'length' => $excerpt_length,
											) );

										$output .= '</div>';

									}

								$output .= '</div>';

						}

					$output .= '</div>';

				$output .= '</div>';

			endwhile;

		$output .= '</div>';

		// Thumbnails
		if ( 'true' == $control_thumbs ) {

			$container_classes = 'wpex-slider-thumbnails sp-thumbnails';
			if ( 'true' == $control_thumbs_carousel ) {
				$container_classes .= ' sp-thumbnails';
			} else {
				$container_classes .= ' sp-nc-thumbnails';
			}

			$output .= '<div class="' . $container_classes . '">';

				$args = array(
					'size'          => $img_size,
					'crop'          => $img_crop,
					'width'         => $img_width,
					'height'        => $img_height,
					'attributes'    => array( 'data-no-lazy' => 1 ),
					'apply_filters' => 'vcex_post_type_flexslider_nav_thumbnail_args',
				);

				$entry_classes = '';

				if ( 'true' == $control_thumbs_carousel ) {
					$args['class'] = 'wpex-slider-thumbnail sp-thumbnail';
				} else {
					$args['class'] = 'wpex-slider-thumbnail sp-nc-thumbnail';
					if ( $control_thumbs_height || $control_thumbs_width ) {
						$args['size']   = null;
						$args['width']  = $control_thumbs_width ? $control_thumbs_width : null;
						$args['height'] = $control_thumbs_height ? $control_thumbs_height : null;
					}
				}

				foreach ( $posts_cache as $post_id ) {

					$args['attachment'] = get_post_thumbnail_id( $post_id );

					$output .= vcex_get_post_thumbnail( $args );

				}

			$output .= '</div>';

		}

	$output .= '</div>';

	// Close css wrapper
	if ( $css ) {
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
endif; ?>