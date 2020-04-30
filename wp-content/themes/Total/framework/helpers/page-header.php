<?php
/**
 * All page header functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Background
	# Subheading
	# Inline CSS

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if page header is enabled
 *
 * @since 4.0
 */
function wpex_has_page_header() {

	// Get default value
	$return = get_theme_mod( 'enable_page_header', true );

	// Get page header style
	$style = wpex_page_header_style();

	// Hide by default if style is set to hidden
	if ( 'hidden' == $style ) {
		$return = false;
	}

	// Check meta options => MUST COME LAST
	if ( $post_id = wpex_get_current_post_id() ) {

		// Check Customizer setting only if not disabled globally
		if ( is_singular() && 'hidden' != wpex_get_mod( 'page_header_style' ) ) {
			$return = get_theme_mod( get_post_type() . '_singular_page_title', $return );
		}

		// Get page meta setting
		$meta = get_post_meta( $post_id, 'wpex_disable_title', true );

		// Return true if enabled via page settings
		if ( 'enable' == $meta ) {
			$return = true;
		}

		// Return false if page header is disabled and there isn't a page header background defined
		elseif ( 'on' == $meta ) {
			$return = false;
		}

	}

	// Re enable for background image style
	// This must run last of course
	if ( 'background-image' == $style ) {
		$return = true;
	}

	// Woo Check
	if ( wpex_is_woo_shop() && ! wpex_get_mod( 'woo_shop_title', true ) ) {
		$return = false;
	}

	// Apply filters and return
	return apply_filters( 'wpex_display_page_header', $return );

}

/**
 * Check if page header title is enabled
 *
 * @since 4.0
 */
function wpex_has_page_header_title() {

	// Get current post ID
	$post_id = wpex_get_current_post_id();

	// Disable title if the page header is disabled via meta (ignore filter)
	if ( $post_id && 'on' == get_post_meta( $post_id, 'wpex_disable_title', true ) ) {
		return false;
	}

	// Apply filters and return
	return apply_filters( 'wpex_has_page_header_title', true );

}

/**
 * Returns correct page header style
 *
 * @since 4.0
 */
function wpex_page_header_style() {

	// Get default page header style defined in Customizer
	$style = wpex_get_mod( 'page_header_style' );

	// Get current post id
	$post_id = wpex_get_current_post_id();

	// Get for header style defined in page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
		$style = $meta;
	}

	// Sanitize data
	$style = ( 'default' == $style ) ? '' : $style;

	// Apply filters and return
	return apply_filters( 'wpex_page_header_style', $style );

}

/**
 * Adds correct classes to the page header
 *
 * @since 2.0.0
 */
function wpex_page_header_classes() {

	// Define main class
	$classes = array( 'page-header' );

	// Get header style
	$style = wpex_page_header_style();

	// Add classes for title style
	if ( $style ) {
		$classes[$style . '-page-header'] = $style . '-page-header';

		if ( 'background-image' == $style && wpex_get_mod( 'breadcrumbs_disable_in_page_title_with_bg', true ) ) {
			$classes[ 'hide-breadcrumbs' ] = 'hide-breadcrumbs';
		}

	}

	// Check if current page title supports mods
	if ( ! in_array( $style, array( 'background-image', 'solid-color' ) ) ) {
		$classes['wpex-supports-mods'] = 'wpex-supports-mods';
	}

	// Customizer background setting
	// Do not confuse with the page settings background-image style
	if ( 'background-image' != $style
		&& wpex_page_header_background_image()
		&& 'background-image' != wpex_get_mod( 'page_header_style' )
	) {
		$classes['has-bg-image'] = 'has-bg-image';
		$bg_style = get_theme_mod( 'page_header_background_img_style' );
		$bg_style = $bg_style ? $bg_style : 'fixed';
		$bg_style = apply_filters( 'wpex_page_header_background_img_style', $bg_style );
		$classes['bg-' . $bg_style] = 'bg-' . $bg_style;
	}

	// Apply filters
	$classes = apply_filters( 'wpex_page_header_classes', $classes );

	// Turn into comma seperated list
	$classes = implode( ' ', $classes );

	// Return classes
	return $classes;

}

/*-------------------------------------------------------------------------------*/
/* [ Background ]
/*-------------------------------------------------------------------------------*/

/**
 * Get page header background image URL
 *
 * @since 1.5.4
 */
function wpex_page_header_background_image() {

	// Get current post ID
	$post_id = wpex_get_current_post_id();

	// Get default Customizer value
	$image = wpex_get_mod( 'page_header_background_img' );

	// Fetch from featured image
	if ( $image
		&& $post_id
		&& $fetch_thumbnail_types = wpex_get_mod( 'page_header_background_fetch_thumbnail', null )
	) {
		if ( ! is_array( $fetch_thumbnail_types ) ) {
			$fetch_thumbnail_types = explode( ',', $fetch_thumbnail_types );
		}
		if ( in_array( get_post_type( $post_id ), $fetch_thumbnail_types ) ) {
			$thumbnail = get_post_thumbnail_id( $post_id );
			if ( $thumbnail ) {
				$image = $thumbnail;
			}
		}
	}

	// Apply filters before meta checks => meta should always override
	$image = apply_filters( 'wpex_page_header_background_img', $image ); // @todo remove this deprecated filter
	$image = apply_filters( 'wpex_page_header_background_image', $image, $post_id );

	// Check meta for bg image
	if ( $post_id ) {

		// Get page header background from meta
		if ( $post_id && 'background-image' == get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {

			if ( $new_meta = get_post_meta( $post_id, 'wpex_post_title_background_redux', true ) ) {
				if ( is_array( $new_meta ) && ! empty( $new_meta['url'] ) ) {
					$image = isset( $new_meta['url'] ) ? $new_meta['url'] : $image;
				} else {
					$image = $new_meta ? $new_meta : $image ;
				}
			} else {
				$meta  = get_post_meta( $post_id, 'wpex_post_title_background', true ); // Fallback
				$image = $meta ? $meta : $image;
			}

		}

	}

	// Return image
	return wpex_get_image_url( $image );
}

/**
 * Get correct page header overlay style
 *
 * @since 3.6.0
 */
function wpex_get_page_header_overlay_style() {
	$post_id = wpex_get_current_post_id();
	if ( $post_id && 'background-image' == get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
		$style = get_post_meta( $post_id, 'wpex_post_title_background_overlay', true );
	} else {
		$style = 'dark'; // Default style for categories
	}
	$style = $style == 'none' ? '' : $style; // Backwards compatibility
	return apply_filters( 'wpex_page_header_overlay_style', $style );
}

/**
 * Get correct page header overlay opacity
 *
 * @since 3.6.0
 */
function wpex_get_page_header_overlay_opacity() {
	$post_id = wpex_get_current_post_id();
	$opacity = '';
	if ( $post_id
		&& 'background-image' == get_post_meta( $post_id, 'wpex_post_title_style', true )
		&& $meta = get_post_meta( $post_id, 'wpex_post_title_background_overlay_opacity', true )
	) {
		$opacity = $meta;
	}
	return apply_filters( 'wpex_page_header_overlay_opacity', $opacity );
}

/**
 * Outputs html for the page header overlay
 *
 * @since 1.5.3
 */
function wpex_page_header_overlay( ) {

	// Only needed for the background-image style so return otherwise
	if ( 'background-image' != wpex_page_header_style() ) {
		return;
	}

	// Define vars
	$return  = '';

	// Get settings
	$overlay_style = wpex_get_page_header_overlay_style();

	// Check that overlay style isn't set to none
	if ( $overlay_style ) {

		// Return overlay element
		$return = '<span class="background-image-page-header-overlay style-'. $overlay_style .'"></span>';

	}

	// Apply filters and echo
	echo apply_filters( 'wpex_page_header_overlay', $return );
}

/*-------------------------------------------------------------------------------*/
/* [ Subheading ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if page has header subheading
 *
 * @since 4.0
 */
function wpex_page_header_has_subheading() {
	$bool = wpex_page_header_subheading_content() ? true : false;
	return apply_filters( 'wpex_page_header_has_subheading', $bool );
}

/**
 * Returns page header subheading content
 *
 * @since 4.0
 */
function wpex_page_header_subheading_content() {

	// Subheading is empty by default
	$subheading = '';
	$instance   = '';

	// Get post ID
	$post_id = wpex_get_current_post_id();

	// Posts & Pages
	if ( $post_id ) {
		if ( $meta = get_post_meta( $post_id, 'wpex_post_subheading', true ) ) {
			$subheading = $meta;
		}
		$instance = 'singular_' . get_post_type( $post_id );
	}

	// Categories
	elseif ( is_category() || is_tag() ) {
		$position = wpex_get_mod( 'category_description_position' );
		$position = $position ? $position : 'under_title';
		if ( 'under_title' == $position ) {
			$subheading = term_description();
		}
		$instance = 'category';
	}

	// Author
	elseif ( is_author() ) {
		$subheading = esc_html__( 'This author has written', 'total' ) . ' ' . get_the_author_posts() . ' ' . esc_html__( 'articles', 'total' );
		$instance = 'author';
	}

	// All other Taxonomies
	elseif ( $tax = is_tax() ) {
		if ( ! wpex_has_term_description_above_loop() ) {
			$subheading = term_description(); // note: get_the_archive_description makes extra check to is_author() which isn't needed
		}
		$instance = 'tax';
	}

	// Apply filters and return
	return apply_filters( 'wpex_post_subheading', $subheading, $instance );

}


/*-------------------------------------------------------------------------------*/
/* [ Inline CSS ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs Custom CSS for the page title
 *
 * @since 1.5.3
 */
function wpex_page_header_css( $output ) {

	// If page header is disabled we don't have to add any inline CSS to the site
	if ( ! wpex_has_page_header() ) {
		return $output;
	}

	// Get post ID
	$post_id = wpex_get_current_post_id();

	// Get header style
	$page_header_style = wpex_page_header_style();

	// Define var
	$css = $bg_img = $bg_color = $page_header_css = '';

	// Check if a header style is defined and make header style dependent tweaks
	if ( $page_header_style ) {

		// Customize background color
		if ( 'solid-color' == $page_header_style || 'background-image' == $page_header_style ) {
			$bg_color = get_post_meta( $post_id, 'wpex_post_title_background_color', true );
			if ( $bg_color && '#' != $bg_color ) {
				$page_header_css .='background-color: '. $bg_color .' !important;';
			}
		}

		// Background image Style
		if ( 'background-image' == $page_header_style ) {

			// Get background image
			$bg_img = wpex_page_header_background_image();

			// Add CSS for background image
			if ( $bg_img ) {
				$page_header_css .= 'background-image:url('. $bg_img .' )!important;';
			}

			// Background style
			$title_bg_style = apply_filters( 'wpex_page_header_background_image_style', get_post_meta( $post_id, 'wpex_post_title_background_image_style', true ) );

			if ( $title_bg_style ) {
				$page_header_css .= wpex_sanitize_data( $title_bg_style, 'background_style_css' );
			} else {
				$page_header_css .= '-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;';
			}

			// Background position
			$title_bg_position = apply_filters( 'wpex_page_header_background_position', get_post_meta( $post_id, 'wpex_post_title_background_position', true ) );

			if ( $title_bg_position ) {
				$page_header_css .= 'background-position:' . esc_attr( $title_bg_position ) .';';
			} else {
				$page_header_css .= 'background-position:50% 0;';
			}

			// Custom height => Added to inner table NOT page header
			$title_height = get_post_meta( $post_id, 'wpex_post_title_height', true );
			$title_height = $title_height ? $title_height : wpex_get_mod( 'page_header_table_height' );
			$title_height = apply_filters( 'wpex_post_title_height', $title_height ); // @todo rename filter to something more appropriate

			if ( $title_height ) {
				$css .= '.page-header-table { height:'. wpex_sanitize_data( $title_height, 'px' ) .'; }';
			}

		}

		// Apply all css to the page-header class
		if ( ! empty( $page_header_css ) ) {
			$css .= '.page-header { '. $page_header_css .' }';
		}

		// Overlay Styles
		if ( $bg_img && 'background-image' == $page_header_style ) {

			$overlay_css = '';

			// Use bg_color for overlay background
			if ( $bg_color && 'bg_color' == wpex_get_page_header_overlay_style() ) {
				$overlay_css .= 'background-color: '. $bg_color .' !important;';
			}

			// Overlay opacity
			if ( $opacity = wpex_get_page_header_overlay_opacity() ) {
				$overlay_css .= 'opacity:'. $opacity .';';

			}

			// Add overlay CSS
			if ( $overlay_css ) {
				$css .= '.background-image-page-header-overlay{'. $overlay_css .'}';
			}

		}

		// If css var isn't empty add to custom css output
		if ( ! empty( $css ) ) {
			$output .= $css;
		}

	}

	// Return output
	return $output;

}
add_filter( 'wpex_head_css', 'wpex_page_header_css' );