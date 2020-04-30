<?php
/**
 * Visual Composer Image Carousel
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.1
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
$atts = vcex_vc_map_get_attributes( 'vcex_image_carousel', $atts, $this );
extract( $atts );

// Output var
$output = '';

// Get images from custom field
if ( ! empty( $custom_field_gallery ) ) {

	$image_ids = get_post_meta( vcex_get_the_ID(), trim( $custom_field_gallery ), true );

// Get images from post gallery
} elseif ( 'true' == $post_gallery ) {
	$image_ids = vcex_get_post_gallery_ids();
}

// Get images based on Real Media folder
elseif ( defined( 'RML_VERSION' ) && $rml_folder ) {
	$rml_query = new WP_Query( array(
		'post_status'    => 'inherit',
		'posts_per_page' => $posts_per_page,
		'post_type'      => 'attachment',
		'orderby'        => 'rml', // Order by custom order of RML
		'rml_folder'     => $rml_folder,
		'fields'         => 'ids',
	) );
	if ( $rml_query->have_posts() ) {
		$image_ids = $rml_query->posts;
	}
}

// If there aren't any images lets display a notice
if ( empty( $image_ids ) ) {
	return;
}

// Otherwise if there are images lets turn it into an array
else {

	// Get image ID's
	if ( ! is_array( $image_ids ) ) {
		$attachment_ids = explode( ',', $image_ids );
	} else {
		$attachment_ids = $image_ids;
	}

}

// Remove duplicate images
$attachment_ids = array_unique( $attachment_ids );

// Sanitize attachments to make sure they exist
$attachment_ids = array_filter( $attachment_ids, 'vcex_validate_attachment' );

if ( ! $attachment_ids ) {
	return;
}

// Turn links into array
if ( $custom_links ) {
	$custom_links = explode( ',', $custom_links );
} else {
	$custom_links = array();
}

// Count items
$attachment_ids_count = count( $attachment_ids );
$custom_links_count   = count( $custom_links );

// Add empty values to custom_links array for images without links
if ( $attachment_ids_count > $custom_links_count ) {
	$count = 0;
	foreach( $attachment_ids as $val ) {
		$count++;
		if ( ! isset( $custom_links[$count] ) ) {
			$custom_links[$count] = '#';
		}
	}
}

// New custom links count
$custom_links_count = count( $custom_links );

// Remove extra custom links
if ( $custom_links_count > $attachment_ids_count ) {
	$count = 0;
	foreach( $custom_links as $key => $val ) {
		$count ++;
		if ( $count > $attachment_ids_count ) {
			unset( $custom_links[$key] );
		}
	}
}

// Set links as the keys for the images
$images_links_array = array_combine( $attachment_ids, $custom_links );

// Return if no images
if ( ! $images_links_array ) {
	return;
}

// Randomize images
if ( 'true' == $randomize_images ) {
	$orderby = 'rand';
} else {
	$orderby = 'post__in';
}

// Lets create a new Query for the image carousel
$vcex_query = new WP_Query( array(
	'post_type'      => 'attachment',
	//'post_mime_type'    => 'image/jpeg,image/gif,image/jpg,image/png',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'paged'          => NULL,
	'no_found_rows'  => true,
	'post__in'       => $attachment_ids,
	'orderby'        => $orderby,
) );

// Display carousel if there are images
if ( $vcex_query->have_posts() ) :

	// Enqueue scripts
	vcex_enqueue_carousel_scripts();

	// Main Classes
	$wrap_classes = array( 'vcex-module', 'wpex-carousel', 'wpex-carousel-images', 'clr', 'owl-carousel' );

	// Carousel style
	if ( $style && 'default' != $style ) {
		$wrap_classes[] = $style;
		$arrows_position = ( 'no-margins' == $style && 'default' == $arrows_position ) ? 'abs' : $arrows_position;
	}

	// Arrow classes
	if ( 'true' == $arrows ) {
		$arrows_style = $arrows_style ? $arrows_style : 'default';
		$wrap_classes[] = 'arrwstyle-'. $arrows_style;
		if ( $arrows_position && 'default' != $arrows_position ) {
			$wrap_classes[] = 'arrwpos-'. $arrows_position;
		}
	}

	// Rounded
	if ( 'yes' == $rounded_image ) {
		$wrap_classes[] = 'wpex-rounded-images';
	}

	// CSS animation class
	if ( $css_animation && 'none' != $css_animation ) {
		$wrap_classes[] = vcex_get_css_animation( $css_animation );
	}

	// Custom classes
	if ( $classes ) {
		$wrap_classes[] = vcex_get_extra_class( $classes );
	}

	// Entry classes
	$entry_classes = 'wpex-carousel-slide';
	if ( $entry_css ) {
		$entry_classes .= ' ' . vcex_vc_shortcode_custom_css_class( $entry_css );
	}

	// Image Classes
	$img_classes = array( 'wpex-carousel-entry-media', 'clr' );
	if ( $overlay_style ) {
		$img_classes[] = vcex_image_overlay_classes( $overlay_style );
	}
	if ( $img_filter ) {
		$img_classes[] = vcex_image_filter_class( $img_filter );
	}
	if ( $img_hover_style ) {
		$img_classes[] = vcex_image_hover_classes( $img_hover_style );
	}
	$img_classes = implode( ' ', $img_classes );

	// Lightbox css/js/classes
	if ( 'lightbox' == $thumbnail_link ) {
		vcex_enqueue_lightbox_scripts();
		if ( 'true' == $lightbox_gallery ) {
			$wrap_classes[] = 'wpex-carousel-lightbox';
		}
	}

	// Items to scroll fallback for old setting
	if ( 'page' == $items_scroll ) {
		$items_scroll = $items;
	}

	// Title design
	if ( 'yes' == $title ) {
		$heading_style = vcex_inline_style( array(
			'margin'         => $content_heading_margin,
			'text_transform' => $content_heading_transform,
			'font_weight'    => $content_heading_weight,
			'font_size'      => $content_heading_size,
			'color'          => $content_heading_color,
		) );
	}

	// Content Design
	if ( 'yes' == $title || 'yes' == $caption ) {

		// Defined var
		$content_style = '';

		// Non css_editor fields
		$content_style = array(
			'text_align' => $content_alignment,
			'color'      => $content_color,
			'font_size'  => $content_font_size,
		);

		// Deprecated fields
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
			$content_css = vcex_vc_shortcode_custom_css_class( $content_css ); // Custom CSS class
		}

		// Generate inline style
		if ( $content_style ) {
			$content_style = vcex_inline_style( $content_style );
		}

	}

	// Prevent auto play in visual composer
	if ( vcex_vc_is_inline() ) {
		$atts['auto_play'] = false;
	}

	// Apply filters
	$wrap_classes = vcex_parse_shortcode_classes( implode( ' ', $wrap_classes ), 'vcex_image_carousel', $atts );

	// Display header if enabled
	if ( $header ) {

		$output .= vcex_get_module_header( array(
			'style'   => $header_style,
			'content' => $header,
			'classes' => array( 'vcex-module-heading vcex_image_carousel-heading' ),
		) );

	}

	// Open wrapper for auto height
	if ( 'true' == $auto_height ) {

		$output .= '<div class="owl-wrapper-outer">';

	}

	// Begin output
	$output .= '<div class="' . esc_attr( $wrap_classes ) . '" data-wpex-carousel="' . vcex_get_carousel_settings( $atts, 'vcex_image_carousel' ) . '"' . vcex_get_unique_id( $unique_id ) . '>';

		// Start counter used for lightbox
		$count=0;

		// Loop through images
		while ( $vcex_query->have_posts() ) :

			// Add to counter
			$count++;

			// Get post from query
			$vcex_query->the_post();

			// Store entry data in $atts array so we can apply a filter later
			$atts['post_id']      = get_the_ID();
			$atts['post_data']    = vcex_get_attachment_data( $atts['post_id'] );
			$atts['post_link']    = $atts['post_data']['url'];
			$atts['post_alt']     = esc_attr( $atts['post_data']['alt'] );
			$atts['post_caption'] = $atts['post_data']['caption'];
			$atts['post_video']   = apply_filters( 'vcex_image_carousel_video_support', false ) ? $atts['post_data']['video'] : null;
			$atts['link_target']  = $custom_links_target; // save target for overlay styles

			// Get original attachment ID - fix for WPML
			$post_id = $atts['post_id'];
			if ( $custom_links_count && class_exists( 'SitePress' ) ) {
				global $sitepress;
				if ( $sitepress ) {
					$default_lang = $sitepress->get_default_language();
					$post_id = icl_object_id( $post_id, 'attachment', false, $default_lang );
				}
			}

			// Pluck array to see if item has custom link
			$atts['post_url'] = $images_links_array[$post_id];

			// Check for custom meta links
			if ( 'custom_link' == $thumbnail_link && $link_meta_key ) {
				$meta_custom_link = get_post_meta( $atts['post_id'], wp_strip_all_tags( $link_meta_key ), true );
				if ( ! empty( $meta_custom_link ) ) {
					$atts['post_url'] = $meta_custom_link;
				}
			}

			// Sanitize URLs
			$atts['post_url'] = ( '#' !== $atts['post_url'] ) ? esc_url( $atts['post_url'] ) : '';

			// Get correct title
			if ( 'title' == $title_type || ! $title_type ) {
				$attachment_title = get_the_title();
			} elseif ( 'alt' == $title_type ) {
				$attachment_title = esc_attr( $atts['post_data']['alt'] );
			}

			// Image output
			if ( ! $atts['post_video'] ) {
				$image_output = vcex_get_post_thumbnail( array(
					'attachment'    => $atts['post_id'],
					'crop'          => $img_crop,
					'size'          => $img_size,
					'width'         => $img_width,
					'height'        => $img_height,
					'alt'           => $atts['post_alt'],
					'attributes'    => array( 'data-no-lazy' => 1 ),
					'apply_filters' => 'vcex_image_carousel_thumbnail_args',
					'filter_arg1'   => $atts,
				) );
			}

			// Set classes var so we can add new ones if needed on a per-item basis
			$classes = $entry_classes;

			// Add video class
			if ( $atts['post_video'] ) {
				$classes .= ' owl-item-video';
			}

			// Begin item output
			$output .= '<div class="' . esc_attr( $classes ) . '">';

				$output .= '<figure class="' . esc_attr( $img_classes ) . '">';

					// Video
					if ( $atts['post_video'] ) {

						$output .= '<a href="' . esc_url( set_url_scheme( $atts['post_video'] ) ) . '" class="owl-video"></a>';

						//$output .= '<div class="wpex-carousel-video responsive-video-wrap">' . vcex_video_oembed( $atts['post_video'] ) . '</div>';

					}

					// Image thumbnail
					else {

						// Add custom links to attributes for use with the overlay styles
						if ( 'custom_link' == $thumbnail_link && $atts['post_url'] ) {
							$atts['overlay_link'] = $atts['post_url'];
						}

						// Lightbox
						if ( 'lightbox' == $thumbnail_link ) {

							// Main link attributes
							$link_attrs = array(
								'href'  => vcex_get_lightbox_image( $atts['post_id'] ),
								'title' => $atts['post_alt'],
								'class' => 'wpex-carousel-entry-img',
							);

							// Main link lightbox attributes
							if ( 'lightbox' == $thumbnail_link ) {
								if ( 'true' == $lightbox_gallery ) {
									$link_attrs['class'] .= ' wpex-carousel-lightbox-item';
								} else {
									$link_attrs['class'] .= ' wpex-lightbox';
								}
								$link_attrs['data-title'] = wp_strip_all_tags( $atts['post_alt'] );
								$link_attrs['data-count'] = $count;
								if ( $lightbox_path ) {
									if ( 'disabled' == $lightbox_path ) {
										$link_attrs['data-thumbnails'] = 'false';
									} else {
										$link_attrs['data-path'] = $lightbox_path;
									}
								}

								if ( ! in_array( $lightbox_title, array( 'false', 'none' ) ) ) {
									if ( 'title' == $lightbox_title ) {
										$link_attrs['data-title'] = wp_strip_all_tags( get_the_title( $atts['post_id'] ) );
									} elseif ( 'alt' == $lightbox_title ) {
										$link_attrs['data-title'] = wp_strip_all_tags( $atts['post_alt'] );
									}
								} else {
									$link_attrs['data-show_title']  = 'false';
								}

								// Caption data
								if ( 'false' != $lightbox_caption && $attachment_caption = get_post_field( 'post_excerpt', $atts['post_id'] ) ) {
									$link_attrs['data-caption'] = str_replace( '"',"'", $attachment_caption );
								}

							}

							$output .= '<a' . vcex_parse_html_attributes( $link_attrs ) . '>';

								$output .= $image_output;

								ob_start();
								vcex_image_overlay( 'inside_link', $overlay_style, $atts );
								$output .= ob_get_clean();

							$output .= '</a>';


						}

						// Attachment page
						elseif ( 'attachment_page' == $thumbnail_link || 'full_image' == $thumbnail_link ) {

							// Get URL
							if ( 'attachment_page' == $thumbnail_link ) {
								$url = get_permalink();
							} else {
								$url = wp_get_attachment_url( $post_id );
							}

							// Open link tag
							$output .= '<a href="' . esc_url( $url ) . '" class="wpex-carousel-entry-img"' . vcex_html( 'target_attr', $custom_links_target ) . '>';

								$output .= $image_output;

								ob_start();
								vcex_image_overlay( 'inside_link', $overlay_style, $atts );
								$output .= ob_get_clean();

							$output .= '</a>';

						}

						// Custom Link
						elseif ( 'custom_link' == $thumbnail_link && $atts['post_url'] ) {

							$output .= '<a href="' . esc_url( $atts['post_url'] ) . '" class="wpex-carousel-entry-img"' . vcex_html( 'target_attr', $custom_links_target ) . '>';

								$output .= $image_output;

								ob_start();
								vcex_image_overlay( 'inside_link', $overlay_style, $atts );
								$output .= ob_get_clean();

							$output .= '</a>';

						}

						// No link
						else {

							$output .= $image_output;

							ob_start();
							vcex_image_overlay( 'inside_link', $overlay_style, $atts );
							$output .= ob_get_clean();

						}

						// Outside link overlay html
						ob_start();
						vcex_image_overlay( 'outside_link', $overlay_style, $atts );
						$output .= ob_get_clean();

					} // end video/image check

				$output .= '</figure>';

				// Display details
				if ( ( 'yes' == $title && $attachment_title ) || (  'yes' == $caption && $atts['post_caption'] ) ) :

					$classes = 'wpex-carousel-entry-details clr';

					if ( $content_css ) {
						$classes .= ' ' . $content_css;
					}

					$output .= '<div class="' . esc_attr( $classes ) . '"' . $content_style . '>';

						// Display title
						if ( 'yes' == $title && $attachment_title ) :

							$output .= '<div class="wpex-carousel-entry-title entry-title"' . $heading_style . '>';

								$output .= wp_kses_post( $attachment_title );

							$output .= '</div>';

						endif;

						// Display caption
						if ( 'yes' == $caption && $atts['post_caption'] ) :

							$output .= '<div class="wpex-carousel-entry-excerpt clr">';

								$output .= do_shortcode( wp_kses_post( $atts['post_caption'] ) );

							$output .= '</div>';

						endif;

					$output .= '</div>';

				endif;

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


// End Query
endif;