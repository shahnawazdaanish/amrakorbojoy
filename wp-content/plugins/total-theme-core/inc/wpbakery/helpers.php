<?php
/**
 * Helper functions for custom WPBakery modules.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return correct branding.
 */
function vcex_shortcodes_branding() {
	if ( function_exists( 'wpex_get_theme_branding' ) ) {
		return wpex_get_theme_branding();
	}
	return 'Total Theme';
}

/**
 * Total exclusive setting notice
 */
function vcex_total_exclusive_notice() {
	$notice = sprintf(
		__( 'This is a Total theme exclusive function. <a href="%s" target="_blank">Click here</a> to learn more.', 'total-theme-core' ),
		esc_url( 'https://total.wpexplorer.com' )
	);
	return '<div class="vcex-t-exclusive">' . wp_kses_post( $notice ) . '</div>';
}

/**
 * Locate shortcode template.
 */
function vcex_get_shortcode_template( $shortcode ) {
	$user_template = locate_template( 'vcex_templates/' . $shortcode . '.php' );
	if ( $user_template ) {
		return $user_template;
	}
	return TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/templates/' . $shortcode . '.php';
}

/**
 * Parse shortcode attributes.
 */
function vcex_vc_map_get_attributes( $shortcode = '', $atts = '', $class = '' ) {
	// Fix inline shortcodes - @see WPBakeryShortCode => prepareAtts()
	if ( is_array( $atts ) ) {
		foreach ( $atts as $key => $val ) {
			$atts[ $key ] = str_replace( array(
				'`{`',
				'`}`',
				'``',
			), array(
				'[',
				']',
				'"',
			), $val );
		}
	}
	if ( function_exists( 'vc_map_get_attributes' ) ) {
		return vc_map_get_attributes( $shortcode, $atts );
	}
	$atts = shortcode_atts( vcex_get_shortcode_class_attrs( $class ), $atts, $shortcode );
	return $atts;
}

/**
 * Returns all shortcode atts and default values.
 */
function vcex_get_shortcode_class_attrs( $class ) {
	$atts = array();
	$map = $class->map();
	$params = $map[ 'params' ];
	if ( $params ) {
		foreach( $params as $k => $v ) {
			$value = '';
			if ( isset( $v[ 'std' ] ) ) {
				$value = $v[ 'std' ];
			} elseif ( isset( $v[ 'value' ] ) ) {
				if ( is_array( $v[ 'value' ] ) ) {
					$value = reset( $v[ 'value' ] );
				} else {
					$value = $v[ 'value' ];
				}
			}
			$atts[ $v[ 'param_name' ] ] = $value;
		}
	}
	return $atts;
}

/**
 * Call any shortcode function by it's tagname
 */
function vcex_do_shortcode_function( $tag, $atts = array(), $content = null ) {
	global $shortcode_tags;

    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
        return false;
    }

    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
 * Get module header output.
 */
function vcex_get_module_header( $args = array() ) {
	if ( function_exists( 'wpex_get_heading' ) ) {
		$output = wpex_get_heading( $args );
	} else {
		$output = '<h2 class="vcex-module-heading">' . do_shortcode( wp_kses_post( $header ) ) . '</h2>';
	}
	return apply_filters( 'vcex_get_module_header', $output, $args );
}

/**
 * Return correct asset path.
 */
function vcex_asset_url( $part = '' ) {
	return TTC_PLUGIN_DIR_URL . 'inc/wpbakery/assets/' . $part;
}

/**
 * Return correct asset dir path.
 */
function vcex_asset_dir_path( $part = '' ) {
	return TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/assets/' . $part;
}

/**
 * Check if currently working in the wpbakery front-end editor.
 */
function vcex_vc_is_inline() {
	if ( function_exists( 'vc_is_inline' ) ) {
		return vc_is_inline();
	}
	return false; // prevents things from running if wpbakery is disabled
}

/**
 * Check if responsiveness is enabled.
 */
function vcex_is_layout_responsive() {
	return apply_filters( 'wpex_is_layout_responsive', get_theme_mod( 'responsive', true ) );
}

/**
 * Adds inline style for elements.
 */
function vcex_inline_style( $atts = array(), $add_style = true ) {
	if ( ! empty( $atts ) && is_array( $atts ) ) {
		$inline_style = new VCEX_Inline_Style( $atts, $add_style );
		return $inline_style->return_style();
	}
}

/**
 * Return post id.
 */
function vcex_get_the_ID() {
	if ( function_exists( 'wpex_get_dynamic_post_id' ) ) {
		return wpex_get_dynamic_post_id();
	}
	return get_the_ID();
}

/**
 * Return post title.
 */
function vcex_get_the_title() {
	if ( function_exists( 'wpex_title' ) && function_exists( 'wpex_get_dynamic_post_id' ) ) {
		return wpex_title( wpex_get_dynamic_post_id() );
	} else {
		return get_the_title();
	}
}

/**
 * Return post permalink.
 */
function vcex_get_permalink( $post_id = '' ) {
	if ( function_exists( 'wpex_get_permalink' ) ) {
		return wpex_get_permalink( $post_id );
	}
	return get_permalink();
}

/**
 * Return post class.
 */
function vcex_get_post_class( $class = '', $post_id = null ) {
	return 'class="' . esc_attr( implode( ' ', get_post_class( $class, $post_id ) ) ) . '"';
}

/**
 * Get post type cat tax
 */
function vcex_get_post_type_cat_tax( $post_type = '' ) {
	if ( function_exists( 'wpex_get_post_type_cat_tax' ) ) {
		return wpex_get_post_type_cat_tax( $post_type );
	}
	$post_type = $post_type ? $post_type : get_post_type();
	if ( 'post' == $post_type ) {
		$tax = 'category';
	} elseif ( 'portfolio' == $post_type ) {
		$tax = 'portfolio_category';
	} elseif ( 'staff' == $post_type ) {
		$tax = 'staff_category';
	} elseif ( 'testimonials' == $post_type ) {
		$tax = 'testimonials_category';
	}
	return apply_filters( 'wpex_get_post_type_cat_tax', $tax, $post_type );
}

/**
 * Return post content.
 */
function vcex_the_content( $content = '', $context = '' ) {
	if ( ! $content ) {
		return;
	}
	if ( defined( 'TOTAL_THEME_ACTIVE' ) ) {
		return apply_filters( 'wpex_the_content', $content, $context );
	} else {
		return do_shortcode( shortcode_unautop( wpautop( wp_kses_post( $content ) ) ) );
	}
}

/**
 * Return escaped post title.
 */
function vcex_esc_title( $post = '' ) {
	return the_title_attribute( array(
		'echo' => false,
		'post' => $post,
	) );
}

/**
 * Wrapper for esc_attr with fallback.
 */
function vcex_esc_attr( $val = null, $fallback = null ) {
	$val = esc_attr( $val );
	return $val ? $val : esc_attr( $fallback );
}

/**
 * Wrapper for the wpex_get_star_rating function.
 */
function vcex_get_star_rating( $rating = '', $post_id = '', $before = '', $after = '' ) {
	if ( function_exists( 'wpex_get_star_rating' ) ) {
		return wpex_get_star_rating( $rating, $post_id, $before, $after );
	}
	if ( $rating = get_post_meta( get_the_ID(), 'wpex_post_rating', true ) ) {
		echo esc_html( $trating );
	}
}

/**
 * Wrapper for the vcex_get_user_social_links function.
 */
function vcex_get_user_social_links( $user_id = '', $display = 'icons', $attr = '', $before = '', $after = '' ) {
	if ( function_exists( 'wpex_get_user_social_links' ) ) {
		return wpex_get_user_social_links( $user_id, $display, $attr, $before, $after );
	}
}

/**
 * Wrapper for the wpex_get_social_button_class function.
 */
function vcex_get_social_button_class( $style = 'default' ) {
	if ( function_exists( 'wpex_get_social_button_class' ) ) {
		return wpex_get_social_button_class( $style );
	}
}

/**
 * Get image filter class.
 */
function vcex_image_filter_class( $filter = '' ) {
	if ( function_exists( 'wpex_image_filter_class' ) ) {
		return wpex_image_filter_class( $filter );
	}
}

/**
 * Get image hover classes.
 */
function vcex_image_hover_classes( $hover = '' ) {
	if ( function_exists( 'wpex_image_hover_classes' ) ) {
		return wpex_image_hover_classes( $hover );
	}
}

/**
 * Get image overlay classes.
 */
function vcex_image_overlay_classes( $overlay = '' ) {
	if ( function_exists( 'wpex_overlay_classes' ) ) {
		return wpex_overlay_classes( $overlay );
	}
}

/**
 * Return image overlay.
 */
function vcex_image_overlay( $position = '', $style = '', $atts = '' ) {
	if ( function_exists( 'wpex_overlay' ) ) {
		return wpex_overlay( $position, $style, $atts );
	}
}

/**
 * Return button classes.
 */
function vcex_get_button_classes( $style = '', $color = '', $size = '', $align = '' ) {
	if ( function_exists( 'wpex_get_button_classes' ) ) {
		return wpex_get_button_classes( $style, $color, $size, $align );
	}
}

/**
 * Return after media content.
 */
function vcex_get_entry_media_after( $instance = '' ) {
	return apply_filters( 'wpex_get_entry_media_after', '', $instance ); // do NOT rename filter!!!
}

/**
 * Return excerpt.
 */
function vcex_get_excerpt( $args = '' ) {
	if ( function_exists( 'wpex_get_excerpt' ) ) {
		return wpex_get_excerpt( $args );
	} else {
		$excerpt_length = isset( $args['length'] ) ? $args['length'] : 40;
		return wp_trim_words( get_the_excerpt(), $excerpt_length, null );
	}
}

/**
 * Return thumbnail.
 */
function vcex_get_post_thumbnail( $args = '' ) {
	if ( function_exists( 'wpex_get_post_thumbnail' ) ) {
		return wpex_get_post_thumbnail( $args );
	}
	if ( isset( $args[ 'attachment' ] ) ) {
		$size = isset( $args[ 'size' ] ) ? $args[ 'size' ] : 'full';
		return wp_get_attachment_image( $args[ 'attachment' ], $size );
	}
}

/**
 * Return WooCommerce price
 */
function vcex_get_woo_product_price( $post_id = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( 'product' == get_post_type( $post_id ) ) {
		$product = wc_get_product( $post_id );
		$price   = $product->get_price_html();
		if ( $price ) {
			return $price;
		}
	}
}

/**
 * Wrapper for intval with fallback.
 */
function vcex_intval( $val = null, $fallback = null ) {
	if ( 0 == $val ) {
		return 0; // Some settings may need this
	}
	$val = intval( $val );
	return $val ? $val : intval( $fallback );
}

/**
 * Return button arrow.
 */
function vcex_readmore_button_arrow() {
	if ( is_rtl() ) {
		$arrow = '&larr;';
	} else {
		$arrow = '&rarr;';
	}
	return apply_filters( 'wpex_readmore_button_arrow', $arrow );
}

/**
 * WPBakery vc_param_group_parse_atts wrapper function
 */
function vcex_vc_param_group_parse_atts( $atts_string ) {
	if ( function_exists( 'vc_param_group_parse_atts' ) ) {
		return vc_param_group_parse_atts( $atts_string );
	}
	$array = json_decode( urldecode( $atts_string ), true );
	return $array;
}

/**
 * Takes array of html attributes and converts into a string.
 */
function vcex_parse_html_attributes( $attrs ) {

	if ( ! $attrs || ! is_array( $attrs ) ) {
		return $attrs;
	}

	// Define output
	$output = '';

	// Loop through attributes
	foreach ( $attrs as $key => $val ) {

		// Skip
		if ( 'content' == $key ) {
			continue;
		}

		// If the attribute is an array convert to string
		if ( is_array( $val ) ) {
			$val = array_filter( $val, 'trim' ); // Remove extra space
			$val = implode( ' ', $val );
		}

		// Sanitize rel attribute
		if ( 'rel' == $key ) {
			if ( ! in_array( $val, array( 'nofollow' ) ) ) {
				continue;
			}
		}

		// Sanitize id
		elseif ( 'id' == $key ) {
			$val = trim ( str_replace( '#', '', $val ) );
			$val = str_replace( ' ', '', $val );
		}

		// Sanitize class
		elseif ( 'class' == $key ) {
			$val = trim( $val ); // remove extra spaces
		}

		// Sanitize targets
		elseif ( 'target' == $key ) {
			$val = ( strpos( $val, 'blank' ) !== false ) ? '_blank' : '';
		}

		// Add attribute to output
		if ( $val ) {
			if ( in_array( $key, array( 'download' ) ) ) {
				$output .= ' ' . trim( $val ); // Used for example on total button download attribute
			} else {
				$needle = ( 'data' == $key ) ? 'data-' : $key . '=';
				if ( $val && strpos( $val, $needle ) !== false ) {
					$output .= ' ' . trim( $val ); // Already has tag added
				} else {
					$output .= ' ' . $key . '="' . $val . '"';
				}
			}
		}

		// Items with empty vals
		else {

			// Empty alts are allowed
			if ( 'alt' == $key ) {
				$output .= " alt='" . esc_attr( $val ) . "'";
			}

			// Data attributes
			elseif ( strpos( $key, 'data-' ) !== false ) {
				$output .= ' ' . $key;
			}

		}

	}

	// Return output
	return ' ' . trim( $output ); // Must always have empty space infront
}

/**
 * Validate Font Size.
 */
function vcex_validate_font_size( $input ) {
	if ( strpos( $input, 'px' ) || strpos( $input, 'em' ) || strpos( $input, 'vw' ) || strpos( $input, 'vmin' ) || strpos( $input, 'vmax' ) ) {
		$input = esc_html( $input );
	} else {
		$input = absint( $input ) . 'px';
	}
	if ( $input != '0px' && $input != '0em' ) {
		return esc_html( $input );
	}
	return '';
}

/**
 * Validate Boolean.
 */
function vcex_validate_boolean( $input ) {
	if ( ! $input ) {
		return false;
	}
	if ( 'true' == $input || 'yes' == $input ) {
		return true;
	}
	if ( 'false' == $input || 'no' == $input ) {
		return false;
	}
}

/**
 * Validate px.
 */
function vcex_validate_px( $input ) {
	if ( ! $input ) {
		return;
	}
	if ( 'none' == $input ) {
		return '0';
	} else {
		return floatval( $input ) . 'px';
	}
}

/**
 * Validate px or percentage value.
 */
function vcex_validate_px_pct( $input ) {
	if ( ! $input ) {
		return;
	}
	if ( 'none' == $input || '0px' == $input ) {
		return '0';
	} elseif ( strpos( $input, '%' ) ) {
		return wp_strip_all_tags( $input );
	} elseif ( $input = floatval( $input ) ) {
		return wp_strip_all_tags( $input ) . 'px';
	}
}

/**
 * Get site default font size.
 */
function vcex_get_body_font_size() {
	if ( function_exists( 'wpex_get_body_font_size' ) ) {
		return wpex_get_body_font_size();
	}
	return apply_filters( 'vcex_get_body_font_size', '13px' );
}

/**
 * Get theme term data.
 */
function vcex_get_term_data() {
	if ( function_exists( 'wpex_get_term_data' ) ) {
		return wpex_get_term_data();
	}
}

/**
 * Get term thumbnail.
 */
function vcex_get_term_thumbnail_id( $term_id = '' ) {
	if ( function_exists( 'wpex_get_term_thumbnail_id' ) ) {
		return wpex_get_term_thumbnail_id( $term_id );
	}
}

/**
 * Get post video.
 */
function vcex_get_post_video( $post_id = '' ) {
	if ( function_exists( 'wpex_get_post_video' ) ) {
		return wpex_get_post_video( $post_id );
	}
}

/**
 * Get post video html.
 */
function vcex_get_post_video_html() {
	if ( function_exists( 'wpex_get_post_video_html' ) ) {
		return wpex_get_post_video_html();
	}
}

/**
 * Get post video html.
 */
function vcex_video_oembed( $video = '', $classes = '', $params = array() ) {
	if ( function_exists( 'wpex_video_oembed' ) ) {
		return wpex_video_oembed( $video, $classes, $params );
	}
	return wp_oembed_get( $video );
}

/**
 * Get post video oembed URL.
 */
function vcex_get_post_video_oembed_url( $post_id = '' ) {
	if ( function_exists( 'wpex_get_post_video_oembed_url' ) ) {
		return wpex_get_post_video_oembed_url( $post_id );
	}
}

/**
 * Get post video oembed URL.
 */
function vcex_get_video_embed_url( $post_id = '' ) {
	if ( function_exists( 'wpex_get_video_embed_url' ) ) {
		return wpex_get_video_embed_url( $post_id );
	}
}

/**
 * Return inline gallery code.
 */
function vcex_parse_inline_lightbox_gallery( $attachements = '' ) {
	if ( function_exists( 'wpex_parse_inline_lightbox_gallery' ) ) {
		return wpex_parse_inline_lightbox_gallery( $attachements );
	}
}

/**
 * Get hover animation class
 */
function vcex_hover_animation_class( $animation = '' ) {
	if ( function_exists( 'wpex_hover_animation_class' ) ) {
		return wpex_hover_animation_class( $animation );
	}
}

/**
 * Get first post term.
 */
function vcex_get_first_term( $post_id = '', $taxonomy = 'category', $terms = '' ) {
	if ( function_exists( 'wpex_get_first_term' ) ) {
		return wpex_get_first_term( $post_id, $taxonomy, $terms );
	}
}

/**
 * Get post first term link.
 */
function vcex_get_first_term_link( $post_id = '', $taxonomy = 'category', $terms = '' ) {
	if ( function_exists( 'wpex_get_first_term_link' ) ) {
		return wpex_get_first_term_link( $post_id, $taxonomy, $terms );
	}
}

/**
 * Get post terms.
 */
function vcex_get_list_post_terms( $taxonomy = 'category', $show_links = true ) {
	if ( function_exists( 'wpex_get_list_post_terms' ) ) {
		return wpex_get_list_post_terms( $taxonomy, $show_links );
	}
}

/**
 * Get pagination.
 */
if ( ! function_exists( 'vcex_pagination' ) ) {
	function vcex_pagination( $query = '', $echo = true ) {
		if ( function_exists( 'wpex_pagination' ) ) {
			return wpex_pagination( $query, $echo );
		}
		if ( $query ) {
			global $wp_query;
			$temp_query = $wp_query;
			$wp_query = $query;
		}
		ob_start();
		posts_nav_link();
		$wp_query = $temp_query;
		return ob_get_clean();
	}
}

/**
 * Check if an attachment id exists.
 */
function vcex_validate_attachment( $attachment = '' ) {
	if ( 'attachment' == get_post_type( $attachment ) ) {
		return $attachment;
	}
}

/**
 * Get shortcode custom css class.
 */
function vcex_vc_shortcode_custom_css_class( $css = '' ) {
	if ( $css && function_exists( 'vc_shortcode_custom_css_class' ) ) {
		return trim( vc_shortcode_custom_css_class( $css ) );
	}
}

/**
 * Adds the vc custom css filter tag.
 */
function vcex_parse_shortcode_classes( $classes = '', $shortcode_base = '', $atts = '' ) {
	if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
		return apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $classes, $shortcode_base, $atts );
	}
	return $classes;
}

/**
 * Get encoded vc data.
 */
function vcex_vc_value_from_safe( $value, $encode = false ) {
	if ( function_exists( 'vc_value_from_safe' ) ) {
		return vc_value_from_safe( $value );
	}
	$value = preg_match( '/^#E\-8_/', $value ) ? rawurldecode( base64_decode( preg_replace( '/^#E\-8_/', '', $value ) ) ) : $value;
	if ( $encode ) {
		$value = htmlentities( $value, ENT_COMPAT, 'UTF-8' );
	}
	return $value;
}

/**
 * Filters module grid to return active blocks.
 */
function vcex_filter_grid_blocks_array( $blocks ) {
	$new_blocks = array();
	foreach ( $blocks as $key => $value ) {
		if ( 'true' == $value ) {
			$new_blocks[$key] = '';
		}
	}
	return $new_blocks;
}

/**
 * Displays notice when functions aren't found.
 */
function vcex_function_needed_notice() {
	return; // deprecated in 4.9
}

/**
 * Returns correct classes for grid modules
 * Does NOT use post_class to prevent conflicts.
 */
function vcex_grid_get_post_class( $classes = array(), $post_id = '', $media_check = true ) {

	// Get post ID
	$post_id = $post_id ? $post_id : get_the_ID();

	// Get post type
	$post_type = get_post_type( $post_id );

	// Add post ID class
	$classes[] = 'post-' . $post_id;

	// Add entry class
	$classes[] = 'entry';

	// Add type class
	$classes[] = 'type-' . $post_type;

	// Add has media class
	if ( $media_check && function_exists( 'wpex_post_has_media' ) ) {
		if ( wpex_post_has_media( $post_id, true ) ) {
			$classes[] = 'has-media';
		} else {
			$classes[] = 'no-media';
		}
	}

	// Add terms
	if ( $terms = vcex_get_post_term_classes( $post_id, $post_type ) ) {
		$classes[] = $terms;
	}

	// Custom link class
	if ( function_exists( 'wpex_get_post_redirect_link' ) && wpex_get_post_redirect_link() ) {
		$classes[] = 'has-redirect';
	}

	// Apply filters
	$classes = apply_filters( 'vcex_grid_get_post_class', $classes );

	// Sanitize classes
	$classes_escaped = array_map( 'esc_attr', $classes );

	// Turn into string
	$classes_escaped = implode( ' ', $classes_escaped );

	// Return class
	return 'class="' . $classes_escaped . '"';

}

/**
 * Returns entry classes for vcex module entries.
 */
function vcex_get_post_term_classes( $post_id, $post_type ) {

	if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
		return array();
	}

	// Define vars
	$classes = array();

	// Loop through tax objects and save in taxonomies var
	$taxonomies = get_object_taxonomies( $post_type, 'names' );

	// Return of there is an error
	if ( is_wp_error( $taxonomies ) || ! $taxonomies ) {
		return;
	}

	// Loop through taxomies
	foreach ( $taxonomies as $tax ) {

		// Get terms
		$terms = get_the_terms( $post_id, $tax );

		// Make sure terms aren't empty before loop
		if ( ! is_wp_error( $terms ) && $terms ) {

			// Loop through terms
			foreach ( $terms as $term ) {

				// Set prefix as taxonomy name
				$prefix = esc_html( $term->taxonomy );

				// Add class if we have a prefix
				if ( $prefix ) {

					// Get total post types to parse
					$parse_types = vcex_theme_post_types();
					if ( in_array( $post_type, $parse_types ) ) {
						$search  = array( $post_type . '_category', $post_type . '_tag' );
						$replace = array( 'cat', 'tag' );
						$prefix  = str_replace( $search, $replace, $prefix );
					}

					// Category prefix
					if ( 'category' == $prefix ) {
						$prefix = 'cat';
					}

					// Add term
					$classes[] = sanitize_html_class( $prefix . '-' . $term->term_id );

					// Add term parent
					if ( $term->parent ) {
						$classes[] = sanitize_html_class( $prefix . '-' . $term->parent );
					}

				}

			}
		}
	}

	// Sanitize classes
	$classes_escaped = array_map( 'esc_attr', $classes );

	// Return classes
	return $classes_escaped ? implode( ' ', $classes_escaped ) : '';

}

/**
 * Returns correct class for columns.
 */
function vcex_get_grid_column_class( $atts ) {
	$return_class = '';
	if ( isset( $atts['columns'] ) ) {
		$return_class .= sanitize_html_class( 'span_1_of_' . $atts['columns'] );
	}
	if ( isset( $atts['single_column_style'] ) && 'left_thumbs' == $atts['single_column_style'] ) {
		return sanitize_html_class( $return_class );
	}
	if ( ! empty( $atts['columns_responsive_settings'] ) ) {
		$rs = vcex_parse_multi_attribute( $atts['columns_responsive_settings'], array() );
		foreach ( $rs as $key => $val ) {
			if ( $val ) {
				$return_class .= ' ' . sanitize_html_class( 'span_1_of_' . $val . '_' . $key );
			}
		}
	}
	return $return_class;
}

/**
 * Returns correct CSS for custom button color based on style.
 */
function vcex_get_button_custom_color_css( $style = '', $color ='' ) {
	if ( function_exists( 'wpex_get_button_custom_color_css' ) ) {
		return wpex_get_button_custom_color_css( $style, $color );
	}
}

/**
 * REturns theme post types.
 */
function vcex_theme_post_types() {
	if ( function_exists( 'wpex_theme_post_types' ) ) {
		return wpex_theme_post_types();
	}
	return array();
}

/**
 * Returns correct class for columns.
 */
function vcex_parse_multi_attribute( $value = '', $default = array() ) {
	$result = $default;
	$params_pairs = explode( '|', $value );
	if ( ! empty( $params_pairs ) ) {
		foreach ( $params_pairs as $pair ) {
			$param = preg_split( '/\:/', $pair );
			if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
				if ( 'http' == $param[1] && isset( $param[2] ) ) {
					$param[1] = rawurlencode( 'http:' . $param[2] ); // fix for incorrect urls that are not encoded
				}
				$result[ $param[0] ] = rawurldecode( $param[1] );
			}
		}
	}
	return $result;
}

/**
 *  Get carousel data.
 */
function vcex_get_carousel_settings( $atts, $shortcode ) {

	$settings = array(
		'nav'                  => vcex_esc_attr( $atts[ 'arrows' ], true ),
		'dots'                 => vcex_esc_attr( $atts[ 'dots' ], false ),
		'autoplay'             => vcex_esc_attr( $atts[ 'auto_play' ], false ),
		'loop'                 => vcex_esc_attr( $atts[ 'infinite_loop' ], true ),
		'center'               => vcex_esc_attr( $atts[ 'center' ], 150 ),
		'smartSpeed'           => vcex_intval( $atts[ 'animation_speed' ], 250 ),
		'items'                => vcex_intval( $atts[ 'items' ], 4 ),
		'slideBy'              => vcex_intval( $atts[ 'items_scroll' ], 1 ),
		'autoplayTimeout'      => ! empty( $atts[ 'timeout_duration' ] ) ? intval( $atts[ 'timeout_duration' ] ) : 5000, // cant be 0
		'margin'               => vcex_intval( $atts[ 'items_margin' ], 15 ),
		'itemsTablet'          => vcex_intval( $atts[ 'tablet_items' ], 3 ),
		'itemsMobileLandscape' => vcex_intval( $atts[ 'mobile_landscape_items' ], 2 ),
		'itemsMobilePortrait'  => vcex_intval( $atts[ 'mobile_portrait_items' ], 1 ),
	);

	if ( isset( $atts[ 'style' ] ) && $atts[ 'style' ] == 'no-margins' ) {
		$settings[ 'margin' ] = 0;
	}

	if ( isset( $atts[ 'auto_width' ] ) ) {
		$settings[ 'autoWidth' ] = vcex_esc_attr( $atts[ 'auto_width' ], false );
	}

	if ( isset( $atts[ 'auto_height' ] ) ) {
		$settings[ 'autoHeight' ] = vcex_esc_attr( $atts[ 'auto_height' ], false );
	}

	$settings = apply_filters( 'vcex_get_carousel_settings', $settings, $atts, $shortcode );

	foreach( $settings as $k => $v ) {
		if ( 'true' == $v ) {
			$settings[ $k ] = true;
		}
		if ( 'false' == $v ) {
			$settings[ $k ] = false;
		}
	}

	return htmlspecialchars( wp_json_encode( $settings ) );
}

/**
 * Helper function enqueues icon fonts from Visual Composer.
 */
function vcex_enqueue_icon_font( $family = '', $icon = '' ) {

	// Return if there isn't an icon
	if ( ! $icon ) {
		return;
	}

	// If font family isn't defined lets get it from the icon class
	if ( ! $family ) {
		$family = vcex_get_icon_type_from_class( $icon );
	}

	// Return if we are using ticons
	if ( 'ticons' == $family || ! $family ) {
		return;
	}

	// Check for custom enqueue
	$fonts = vcex_get_icon_font_families();

	// Custom stylesheet check
	if ( ! empty( $fonts[$family]['style'] ) ) {
		wp_enqueue_style( $fonts[$family]['style'] );
		return;
	}

	// Default vc font icons
	if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
		vc_icon_element_fonts_enqueue( $family );
	}

}

/**
 * Returns animation class and loads animation js.
 */
function vcex_get_css_animation( $css_animation = '' ) {
	if ( defined( 'WPB_VC_VERSION' ) && $css_animation && 'none' != $css_animation ) {
		wp_enqueue_script( 'vc_waypoints' );
		wp_enqueue_style( 'vc_animate-css' );
		$css_animation = sanitize_html_class( $css_animation );
		return ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
	}
}

/**
 * Return unique ID for responsive class.
 */
function vcex_get_reponsive_unique_id( $unique_id = '' ) {
	return $unique_id ? '.wpex-' . $unique_id : uniqid( 'wpex-' );
}

/**
 * Return responsive font-size data.
 */
function vcex_get_responsive_font_size_data( $value ) {

	// Font size is needed
	if ( ! $value ) {
		return;
	}

	// Not needed for simple font_sizes
	if ( strpos( $value, '|' ) === false ) {
		return;
	}

	// Parse data to return array
	$data = vcex_parse_multi_attribute( $value );

	if ( ! $data && ! is_array( $data ) ) {
		return;
	}

	$sanitized_data = array();

	// Sanitize
	foreach ( $data as $key => $val ) {
		$sanitized_data[$key] = vcex_validate_font_size( $val, 'font_size' );
	}

	return $sanitized_data;

}

/**
 * Return responsive font-size data.
 */
function vcex_get_module_responsive_data( $atts, $type = '' ) {

	if ( ! $atts ) {
		return; // No need to do anything if atts is empty
	}

	$return      = array();
	$parsed_data = array();
	$settings    = array( 'font_size' );

	if ( $type && ! is_array( $atts ) ) {
		$settings = array( $type );
		$atts = array( $type => $atts );
	}

	foreach ( $settings as $setting ) {

		if ( 'font_size' == $setting ) {

			// Get value from params
			$value = isset( $atts['font_size'] ) ? $atts['font_size'] : '';

			// Value needed
			if ( ! $value ) {
				break;
			}

			// Get font size data
			$value = vcex_get_responsive_font_size_data( $value );

			// Add to new array
			if ( $value ) {
				$parsed_data['font-size'] = $value;
			}

		} // End font_size

	} // End foreach

	// Return
	if ( $parsed_data ) {
		return "data-wpex-rcss='" . htmlspecialchars( wp_json_encode( $parsed_data ) ) . "'";
	}

}

/**
 * Get Extra class.
 */
function vcex_get_extra_class( $classes = '' ) {
	$classes = trim( $classes );
	if ( $classes ) {
		return esc_attr( str_replace( '.', '', $classes ) );
	}
}

/**
 * Parses lightbox dimensions.
 */
function vcex_parse_lightbox_dims( $dims = '', $return = '' ) {

	// Return if no dims
	if ( ! $dims ) {
		return;
	}

	// Parse data
	$dims = explode( 'x', $dims );
	$w    = isset( $dims[0] ) ? absint( $dims[0] ) : null;
	$h    = isset( $dims[1] ) ? absint( $dims[1] ) : null;

	// Width and height required
	if ( ! $w || ! $h ) {
		return;
	}

	// Return width
	if ( 'width' == $return ) {
		return $w;
	}

	// Return height
	elseif ( 'height' == $return ) {
		return $h;
	}

	// Return height
	elseif ( 'array' == $return ) {
		return array(
			'width'  => $w,
			'height' => $h,
		);
	}

	// Return dimensions (deprecated in version 1.0.4)
	else {
		return 'width:' . esc_attr( $w ) . ',height:' . esc_attr( $h );
	}

}

/**
 * Parses textarea HTML.
 */
function vcex_parse_textarea_html( $html = '' ) {
	if ( $html && base64_decode( $html, true ) ) {
		return rawurldecode( base64_decode( strip_tags( $html ) ) );
	}
	return $html;
}

/**
 * Parses the font_control / typography param (used for mapper and front-end)
 */
function vcex_parse_typography_param( $value ) {
	$defaults = array(
		'tag'               => '',
		'text_align'        => '',
		'font_size'         => '',
		'line_height'       => '',
		'color'             => '',
		'font_style_italic' => '',
		'font_style_bold'   => '',
		'font_family'       => '',
		'letter_spacing'    => '',
		'font_family'       => '',
	);
	if ( ! function_exists( 'vc_parse_multi_attribute' ) ) {
		return $defaults;
	}
	$values = wp_parse_args( vc_parse_multi_attribute( $value ), $defaults );
	return $values;
}

/**
 * Url param to check for for filters.
 */
function vcex_grid_filter_url_param() {
	return apply_filters( 'vcex_grid_filter_url_param', 'filter' );
}

/**
 * Get vcex grid filter active item.
 */
function vcex_grid_filter_get_active_item( $tax = '' ) {
	$param = vcex_grid_filter_url_param();
	if ( empty( $_GET[$param] ) ) {
		return;
	}
	$paramv = esc_html( $_GET[$param] );
	if ( $tax && ! is_numeric( $paramv ) ) {
		$get_term = get_term_by( 'slug', $paramv, $tax );
		if ( ! $get_term ) {
			$get_term = get_term_by( 'name', $paramv, $tax );
		}
		if ( $get_term ) {
			$term_id = $get_term->term_id;
			if ( class_exists( 'SitePress' ) ) {
				global $sitepress;
				$term_id = apply_filters( 'wpml_object_id', $term_id, $taxonomy, true, $sitepress->get_default_language() );
			}
			return $term_id;
		}
	}
	return $paramv;
}

/**
 * Return grid filter arguments.
 */
function vcex_grid_filter_args( $atts = '', $query = '' ) {

	// Return if no attributes found
	if ( ! $atts ) {
		return;
	}

	// Define args
	$args = $include = array();

	// Don't get empty
	$args['hide_empty'] = true;

	// Taxonomy
	if ( ! empty( $atts['filter_taxonomy'] ) ) {
		$taxonomy = $atts['filter_taxonomy'];
	} elseif ( isset( $atts['taxonomy'] ) ) {
		$taxonomy = $atts['taxonomy']; // Fallback
	} else {
		$taxonomy = null;
	}

	// Define post type and taxonomy
	$post_type = ! empty( $atts['post_type'] ) ? $atts['post_type'] : '';

	// Define include/exclude category vars
	$include_cats = ! empty( $atts['include_categories'] ) ? vcex_string_to_array( $atts['include_categories'] ) : '';

	// Check if only 1 category is included
	// If so check if it's a parent item so we can display children as the filter links
	if ( $include_cats && '1' == count( $include_cats )
		&& $children = get_term_children( $include_cats[0], $taxonomy )
	) {
		$include = $children;
	}

	// Check for ajax pagination
	$ajax_pagination = ( isset( $atts['pagination_loadmore'] ) && 'true' == $atts['pagination_loadmore'] ) ? true : false;

	// Ajax pagination should include all categories or specified ones
	if ( $ajax_pagination ) {

		if ( $include_cats && is_array( $include_cats ) ) {
			$include = $include_cats;
		}

		$exclude_cats = ! empty( $atts['exclude_categories'] ) ? vcex_string_to_array( $atts['exclude_categories'] ) : '';
		$exclude = $exclude_cats;

	}

	// Include only terms from current query
	elseif ( empty( $include ) && $query ) {

		// Pluck ids from query
		$post_ids = wp_list_pluck( $query->posts, 'ID' );

		// Loop through post ids
		foreach ( $post_ids as $post_id ) {

			// Get post terms
			$terms = wp_get_post_terms( $post_id, $taxonomy );

			// Make sure there is no errors with terms and post has terms
			if ( ! is_wp_error( $terms ) && $terms ) {

				// Loop through terms
				foreach( $terms as $term ) {

					// Store term id
					$term_id = $term->term_id;

					// WPML Check
					if ( class_exists( 'SitePress' ) ) {
						global $sitepress;
						$term_id = apply_filters( 'wpml_object_id', $term_id, $taxonomy, true, $sitepress->get_default_language() );
					}

					// Include terms if include_cats variable is empty
					if ( ! $include_cats ) {

						// Include term
						$include[$term_id] = $term_id;

						/* Include parent
						if ( $term->parent ) {
							$include[$term->parent] = $term->parent;
						}*/

					}

					// Include terms if include_cats is enabled and term is in var
					elseif ( $include_cats && in_array( $term_id, $include_cats ) ) {
						$include[$term_id] = $term_id;
					}

				}

			}

		}

		// Add included terms to include param
		$args['include'] = $include;

	}

	// Add to args
	if ( ! empty( $include ) ) {
		$args['include'] = $include;
	}
	if ( ! empty( $exclude ) ) {
		$args['exclude'] = $exclude;
	}

	// Apply filters @todo deprecate?
	if ( $post_type ) {
		$args = apply_filters( 'vcex_'. $post_type .'_grid_filter_args', $args );
	}

	// Return args
	return apply_filters( 'vcex_grid_filter_args', $args, $post_type );

}

/**
 * Generates various types of HTML based on a value.
 */
function vcex_html( $type, $value, $trim = false ) {

	// Return nothing by default
	$return = '';

	// Return if value is empty
	if ( ! $value ) {
		return;
	}

	// Title attribute
	if ( 'id_attr' == $type ) {
		$value  = trim ( str_replace( '#', '', $value ) );
		$value  = str_replace( ' ', '', $value );
		if ( $value ) {
			$return = ' id="'. esc_attr( $value ) .'"';
		}
	}

	// Title attribute
	if ( 'title_attr' == $type ) {
		$return = ' title="'. esc_attr( $value ) .'"';
	}

	// Link Target
	elseif ( 'target_attr' == $type ) {
		if ( 'blank' == $value
			|| '_blank' == $value
			|| strpos( $value, 'blank' ) ) {
			$return = ' target="_blank"';
		}
	}

	// Link rel
	elseif ( 'rel_attr' == $type ) {
		if ( 'nofollow' == $value ) {
			$return = ' rel="nofollow"';
		}
	}

	// Return HTMl
	if ( $trim ) {
		return trim( $return );
	} else {
		return $return;
	}

}

/**
 * Notice when no posts are found.
 */
function vcex_no_posts_found_message( $atts ) {
	$message = null;
	if ( vcex_vc_is_inline() ) {
		$message = '<div class="vcex-no-posts-found">' . esc_html__( 'No posts found for your query.', 'total-theme-core' ) . '</div>';
	}
	return apply_filters( 'vcex_no_posts_found_message', $message, $atts );
}

/**
 * Echos unique ID html for VC modules.
 */
function vcex_unique_id( $id = '' ) {
	echo vcex_get_unique_id( $id );
}

/**
 * Returns unique ID html for VC modules.
 */
function vcex_get_unique_id( $id = '' ) {
	if ( $id ) {
		return ' id="' . esc_attr( $id ) . '"'; // do not remove empty space at front!!
	}
}

/**
 * Returns lightbox image.
 */
function vcex_get_lightbox_image( $thumbnail_id = '' ) {
	if ( function_exists( 'wpex_get_lightbox_image' ) ) {
		return wpex_get_lightbox_image( $thumbnail_id );
	} else {
		return esc_url( wp_get_attachment_url(  $thumbnail_id ) );
	}
}

/**
 * Returns attachment data
 */
function vcex_get_attachment_data( $attachment = '', $return = 'array' ) {

	// Initial checks
	if ( ! $attachment || 'none' == $return ) {
		return;
	}

	// Sanitize return value
	$return = $return ? $return : 'array';

	// Return data
	if ( 'array' == $return ) {
		return array(
			'url'         => get_post_meta( $attachment, '_wp_attachment_url', true ),
			'src'         => wp_get_attachment_url( $attachment ),
			'alt'         => get_post_meta( $attachment, '_wp_attachment_image_alt', true ),
			'title'       => get_the_title( $attachment ),
			'caption'     => get_post_field( 'post_excerpt', $attachment ),
			'description' => get_post_field( 'post_content', $attachment ),
			'video'       => esc_url( get_post_meta( $attachment, '_video_url', true ) ),
		);
	} elseif ( 'url' == $return ) {
		return get_post_meta( $attachment, '_wp_attachment_url', true );
	} elseif ( 'src' == $return ) {
		return get_post_meta( $attachment, '_wp_attachment_url', true );
	} elseif ( 'alt' == $return ) {
		return get_post_meta( $attachment, '_wp_attachment_image_alt', true );
	} elseif ( 'title' == $return ) {
		return get_the_title( $attachment );
	} elseif ( 'caption' == $return ) {
		return get_post_field( 'post_excerpt', $attachment );
	} elseif ( 'description' == $return ) {
		return get_post_field( 'post_content', $attachment );
	} elseif ( 'video' == $return ) {
		return esc_url( get_post_meta( $attachment, '_video_url', true ) );
	}

}

/**
 * Returns post gallery ID's
 */
function vcex_get_post_gallery_ids( $post_id = '' ) {
	$filter_val = apply_filters( 'vcex_pre_get_post_gallery_ids', null );
	if ( $filter_val ) {
		return $filter_val;
	}
	if ( function_exists( 'wpex_get_gallery_ids' ) ) {
		return wpex_get_gallery_ids( $post_id );
	}
	$attachment_ids = '';
	$post_id = $post_id ? $post_id : vcex_get_the_ID();
	if ( class_exists( 'WC_product' ) && 'product' == get_post_type( $post_id ) ) {
		$product = new WC_product( $post_id );
		if ( $product && method_exists( $product, 'get_gallery_image_ids' ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
		}
	}
	$attachment_ids = $attachment_ids ? $attachment_ids : get_post_meta( $post_id, '_easy_image_gallery', true );
	if ( $attachment_ids ) {
		$attachment_ids = is_array( $attachment_ids ) ? $attachment_ids : explode( ',', $attachment_ids );
		$attachment_ids = array_values( array_filter( $attachment_ids, 'wpex_sanitize_gallery_id' ) );
		return apply_filters( 'wpex_get_post_gallery_ids', $attachment_ids );
	}
}

/**
 * Used to enqueue styles for Visual Composer modules.
 */
function vcex_enque_style( $type, $value = '' ) {

	if ( 'ilightbox' == $type || 'lightbox' == $type ) {
		if ( function_exists( 'wpex_enqueue_lightbox_scripts' ) ) {
			wpex_enqueue_lightbox_scripts();
		} elseif ( function_exists( 'wpex_enqueue_ilightbox_skin' ) ) {
			wpex_enqueue_ilightbox_skin( $value );
		}
	}

	// Hover animation
	elseif ( 'hover-animations' == $type ) {
		wp_enqueue_style( 'wpex-hover-animations' );
	}

}

/**
 * Border Radius Classname.
 */
function vcex_get_border_radius_class( $val ) {
	if ( 'none' == $val || '' == $val ) {
		return;
	}
	return sanitize_html_class( 'wpex-' . $val );
}

/**
 * Helper function for building links using link param.
 */
function vcex_build_link( $link, $fallback = '' ) {

	// If empty return fallback
	if ( empty( $link ) ) {
		return $fallback;
	}

	// Return if there isn't any link
	if ( '||' == $link || '|||' == $link || '||||' == $link ) {
		return;
	}

	// Return simple link escaped (fallback for old textfield input)
	if ( false === strpos( $link, 'url:' ) ) {
		return esc_url( $link );
	}

	// Build link
	// Needs to use total function to fix issue with fallbacks
	$link = vcex_parse_multi_attribute( $link, array( 'url' => '', 'title' => '', 'target' => '', 'rel' => '' ) );

	// Sanitize
	$link = is_array( $link ) ? $link : '';

	// Return link
	return $link;

}

/**
 * Returns link data (used for fallback link settings).
 */
function vcex_get_link_data( $return, $link, $fallback = '' ) {

	$link = vcex_build_link( $link, $fallback );

	if ( 'url' == $return ) {
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			return $link['url'];
		} else {
			return is_array( $link ) ? $fallback : $link;
		}
	}

	if ( 'title' == $return ) {
		if ( is_array( $link ) && ! empty( $link['title'] ) ) {
			return $link['title'];
		} else {
			return $fallback;
		}
	}

	if ( 'target' == $return ) {
		if ( is_array( $link ) && ! empty( $link['target'] ) ) {
			return $link['target'];
		} else {
			return $fallback;
		}
	}

	if ( 'rel' == $return ) {
		if ( is_array( $link ) && ! empty( $link['rel'] ) ) {
			return $link['rel'];
		} else {
			return $fallback;
		}
	}

}

/**
 * Returns correct icon family for specific icon class
 */
function vcex_get_icon_type_from_class( $icon ) {
	if ( strpos( $icon, 'ticon' ) !== false || strpos( $icon, 'fa fa-' ) !== false ) {
		return 'ticons';
	} elseif ( strpos( $icon, 'fa-' ) !== false ) {
		return 'fontawesome';
	} elseif ( strpos( $icon, 'ticon' ) !== false ) {
		return 'ticons';
	} elseif ( strpos( $icon, 'vc-oi' ) !== false ) {
		return 'openiconic';
	} elseif ( strpos( $icon, 'typcn' ) !== false ) {
		return 'typicons';
	} elseif ( strpos( $icon, 'entypo-icon' ) !== false ) {
		return 'entypo';
	} elseif ( strpos( $icon, 'vc_li' ) !== false ) {
		return 'linecons';
	} elseif ( strpos( $icon, 'vc-material' ) !== false ) {
		return 'material';
	}
}

/**
 * Returns correct icon class based on icon type.
 */
function vcex_get_icon_class( $atts, $icon_location = 'icon' ) {

	$icon = '';
	$icon_type = ! empty( $atts['icon_type'] ) ? $atts['icon_type'] : '';

	// Custom icon set for specific library
	if ( $icon_type && ! empty( $atts[$icon_location . '_' . $icon_type] ) ) {
		$icon = $atts[$icon_location . '_' . $icon_type];
	}

	// Parse the default icon parameter which could be anything really
	elseif ( ! empty( $atts[ $icon_location ] ) ) {

		// Get icon value
		$icon = $atts[$icon_location];

		// Get icon type if not set
		if ( ! $icon_type ) {
			$icon_type = vcex_get_icon_type_from_class( $icon );
		}

		// converts old 4.7 fontawesome icons to ticons
		if ( 'ticons' == $icon_type ) {
			$icon = str_replace( 'fa fa-', 'ticon ticon-', $icon );
		}

		// Icon type is unknown so lets add prefixes
		if ( ! $icon_type ) {
			$icon = vcex_add_default_icon_prefix( $icon );
		}

	}

	// Extra checks
	if ( ! $icon || in_array( $icon, array( 'icon', 'none' ) ) ) {
		return '';
	}

	// Return icon class
	return $icon;

}

/**
 * Adds default icon prefix to a non-prefixed icon.
 */
function vcex_add_default_icon_prefix( $icon ) {
	return 'ticon ' . sanitize_html_class( 'ticon-' . trim( $icon ) );
}

/**
 * Convert to array.
 *
 * @todo deprecate - no longer in use
 */
function vcex_string_to_array( $value = array() ) {

	// Return wpex function if it exists
	if ( function_exists( 'wpex_string_to_array' ) ) {
		return wpex_string_to_array( $value );
	}

	// Create our own return
	else {

		// Return null for empty array
		if ( empty( $value ) && is_array( $value ) ) {
			return null;
		}

		// Return if already array
		if ( ! empty( $value ) && is_array( $value ) ) {
			return $value;
		}

		// Clean up value
		$items  = preg_split( '/\,[\s]*/', $value );

		// Create array
		foreach ( $items as $item ) {
			if ( strlen( $item ) > 0 ) {
				$array[] = $item;
			}
		}

		// Return array
		return $array;

	}

}

/**
 * Returns array of carousel settings
 */
function vcex_vc_map_carousel_settings() {
	return array(
		array(
			'type' => 'vcex_ofswitch',
			'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
			'param_name' => 'arrows',
			'std' => 'true',
		),
		array(
			'type' => 'vcex_carousel_arrow_styles',
			'heading' => esc_html__( 'Arrows Style', 'total-theme-core' ),
			'param_name' => 'arrows_style',
			'dependency' => array( 'element' => 'arrows', 'value' => 'true' ),
		),
		array(
			'type' => 'vcex_carousel_arrow_positions',
			'heading' => esc_html__( 'Arrows Position', 'total-theme-core' ),
			'param_name' => 'arrows_position',
			'dependency' => array( 'element' => 'arrows', 'value' => 'true' ),
			'std' => 'default',
		),
		array(
			'type' => 'vcex_ofswitch',
			'heading' => esc_html__( 'Dot Navigation', 'total-theme-core' ),
			'param_name' => 'dots',
			'std' => 'false',
		),
		array(
			'type' => 'vcex_ofswitch',
			'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
			'param_name' => 'auto_play',
			'std' => 'false',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Items To Scrollby', 'total-theme-core' ),
			'param_name' => 'items_scroll',
			'value' => '1',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Autoplay interval timeout.', 'total-theme-core' ),
			'param_name' => 'timeout_duration',
			'value' => '5000',
			'description' => esc_html__( 'Time in milliseconds between each auto slide. Default is 5000.', 'total-theme-core' ),
			'dependency' => array( 'element' => 'auto_play', 'value' => 'true' ),
		),
		array(
			'type' => 'vcex_ofswitch',
			'heading' => esc_html__( 'Infinite Loop', 'total-theme-core' ),
			'param_name' => 'infinite_loop',
			'std' => 'true',
		),
		array(
			'type' => 'vcex_ofswitch',
			'heading' => esc_html__( 'Center Item', 'total-theme-core' ),
			'param_name' => 'center',
			'std' => 'false',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
			'param_name' => 'animation_speed',
			'value' => '250',
			'description' => esc_html__( 'Default is 250 milliseconds. Enter 0.0 to disable.', 'total-theme-core' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Items To Display', 'total-theme-core' ),
			'param_name' => 'items',
			'value' => '4',
		),
		array(
			'type' => 'vcex_ofswitch',
			'std' => 'false',
			'heading' => esc_html__( 'Auto Height?', 'total-theme-core' ),
			'param_name' => 'auto_height',
			'dependency' => array( 'element' => 'items', 'value' => '1' ),
			'description' => esc_html__( 'Allows the carousel to change height based on the active item. This setting is used only when you are displaying 1 item per slide.', 'total-theme-core' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tablet: Items To Display', 'total-theme-core' ),
			'param_name' => 'tablet_items',
			'value' => '3',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Mobile Landscape: Items To Display', 'total-theme-core' ),
			'param_name' => 'mobile_landscape_items',
			'value' => '2',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Mobile Portrait: Items To Display', 'total-theme-core' ),
			'param_name' => 'mobile_portrait_items',
			'value' => '1',
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Margin Between Items', 'total-theme-core' ),
			'param_name' => 'items_margin',
			'value' => '15',
		),
		array(
			'type' => 'vcex_ofswitch',
			'std' => 'false',
			'heading' => esc_html__( 'Auto Width', 'total-theme-core' ),
			'param_name' => 'auto_width',
			'description' => esc_html__( 'If enabled the carousel will display items based on their width showing as many as possible.', 'total-theme-core' ),
		),
	);
}

/**
 * Returns array for adding CSS Animation to VC modules.
 */
function vcex_vc_map_add_css_animation( $args = array() ) {

	// Fallback pre VC 5.0
	if ( ! function_exists( 'vc_map_add_css_animation' ) ) {

		$animations = apply_filters( 'wpex_css_animations', array(
			''              => esc_html__( 'None', 'total') ,
			'top-to-bottom' => esc_html__( 'Top to bottom', 'total' ),
			'bottom-to-top' => esc_html__( 'Bottom to top', 'total' ),
			'left-to-right' => esc_html__( 'Left to right', 'total' ),
			'right-to-left' => esc_html__( 'Right to left', 'total' ),
			'appear'        => esc_html__( 'Appear from center', 'total' ),
		) );

		return array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Appear Animation', 'total-theme-core' ),
			'param_name' => 'css_animation',
			'value' => array_flip( $animations ),
			'dependency' => array( 'element' => 'filter', 'value' => 'false' ),
		);

	}

	// New since VC 5.0
	$defaults = array(
		'type' => 'animation_style',
		'heading' => esc_html__( 'CSS Animation', 'total-theme-core' ),
		'param_name' => 'css_animation',
		'value' => 'none',
		'std' => 'none',
		'settings' => array(
			'type' => 'in',
			'custom' => array(
				array(
					'label' => esc_html__( 'Default', 'total-theme-core' ),
					'values' => array(
						__( 'Top to bottom', 'total-theme-core' )      => 'top-to-bottom',
						__( 'Bottom to top', 'total-theme-core' )      => 'bottom-to-top',
						__( 'Left to right', 'total-theme-core' )      => 'left-to-right',
						__( 'Right to left', 'total-theme-core' )      => 'right-to-left',
						__( 'Appear from center', 'total-theme-core' ) => 'appear',
					),
				),
			),
		),
		'description' => esc_html__( 'Select a CSS animation for when the element "enters" the browser\'s viewport. Note: Animations will not work with grid filters as it creates a conflict with re-arranging items.', 'total-theme-core' ) ,
	);
	$args = wp_parse_args( $args, $defaults );
	return apply_filters( 'vc_map_add_css_animation', $args );
}