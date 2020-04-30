<?php
/**
 * Site Header Helper Functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Logo
	# Overlay
	# Sticky
	# Header Aside
	# Header Builder

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if site header is enabled
 *
 * @since 4.0
 */
function wpex_has_header( $post_id = '' ) {

	// Check if enabled by default
	if ( wpex_has_custom_header() || wpex_elementor_location_exists( 'header' ) ) {
		$bool = true;
	} else {
		$bool = get_theme_mod( 'enable_header', true );
	}

	// Get current post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check meta
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_header', true ) ) {
		if ( 'on' == $meta ) {
			$bool = false;
		} elseif ( 'enable' == $meta ) {
			$bool = true;
		}
	}

	// Apply filters and bool value
	return apply_filters( 'wpex_display_header', $bool ); // @todo rename to wpex_has_header for consistency

}

/**
 * Get header style
 *
 * @since 4.0
 */
function wpex_header_style( $post_id = '' ) {

	// Check URL
	if ( ! empty( $_GET['header_style'] ) ) {
		return wp_strip_all_tags( $_GET['header_style'] );
	}

	// Return if header is disabled
	if ( ! wpex_has_header() ) {
		return 'disabled';
	}

	// Check if builder is enabled
	if ( wpex_header_builder_id() ) {
		return 'builder';
	}

	// Get header style from customizer setting
	$style = wpex_get_mod( 'header_style', 'one' );

	// Overlay header supports certain header styles only
	if ( ! in_array( $style, array( 'one', 'five', 'dev' ) ) && wpex_has_overlay_header() ) {
		$style = 'one';
	}

	// Get current post ID
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check for custom header style defined in meta options => Overrides all
	if ( 'dev' !== $style
		&& $post_id
		&& $meta = get_post_meta( $post_id, 'wpex_header_style', true ) ) {
		$style = $meta;
	}

	// Sanitize style to make sure it isn't empty
	$style = $style ? $style : 'one';

	// Apply filters and return
	return apply_filters( 'wpex_header_style', $style );

}

/**
 * Check if the header style is in dev mode.
 *
 * @since 4.9.4
 */
function wpex_has_dev_style_header() {
	return ( 'dev' == wpex_header_style() ) ? true : false;
}

/**
 * Check if the header style is not in dev mode.
 *
 * @since 4.9.4
 */
function wpex_hasnt_dev_style_header() {
	return ( wpex_has_dev_style_header() ) ? false : true;
}

/**
 * Check if the header is set to vertical
 *
 * @since 4.0
 */
function wpex_has_vertical_header() {
	return in_array( wpex_header_style(), array( 'six' ) );
}

/**
 * Add classes to the header wrap
 *
 * @since 1.5.3
 */
function wpex_header_classes() {

	// Vars
	$post_id      = wpex_get_current_post_id();
	$header_style = wpex_header_style( $post_id );

	// Setup classes array
	$classes = array();

	// Main header style
	$classes['header_style'] = 'header-' . $header_style;

	// Non-Builder classes
	if ( 'builder' != $header_style ) {

		// Full width header
		if ( 'full-width' == wpex_site_layout() && wpex_get_mod( 'full_width_header' ) ) {
			$classes[] = 'wpex-full-width';
		}

		// Non-dev classes
		if ( 'dev' != $header_style ) {

			// Flex header style two
			if ( 'two' == $header_style && wpex_get_mod( 'header_flex_items', false ) ) {
				$classes[] = 'wpex-header-two-flex-v';
			}

			// Dropdown style (must be added here so we can target shop/search dropdowns)
			$dropdown_style = wpex_get_mod( 'menu_dropdown_style' );
			if ( $dropdown_style && 'default' != $dropdown_style ) {
				$classes['wpex-dropdown-style-' . $dropdown_style] = 'wpex-dropdown-style-' . $dropdown_style;
			}

			// Dropdown shadows
			if ( $shadow = wpex_get_mod( 'menu_dropdown_dropshadow' ) ) {
				$classes[] = 'wpex-dropdowns-shadow-' . $shadow;
			}

		}

	}

	// Sticky Header
	if ( wpex_has_sticky_header() ) {

		// Fixed header style
		$fixed_header_style = wpex_sticky_header_style();

		// Main fixed class
		$classes['fixed_scroll'] = 'fixed-scroll'; // @todo rename this at some point?
		if ( wpex_has_shrink_sticky_header() ) {
			$classes['shrink-sticky-header'] = 'shrink-sticky-header';
			if ( 'shrink_animated' == $fixed_header_style ) {
				$classes['anim-shrink-header'] = 'anim-shrink-header';
			}
		}

	}

	// Header Overlay Style
	if ( wpex_has_overlay_header() ) {

		// Get header style
		$overlay_style = wpex_overlay_header_style();
		$overlay_style = $overlay_style ? $overlay_style : 'light';

		// Dark dropdowns for overlay header
		if ( 'core' != $overlay_style ) {
			if ( $post_id && $dropdown_style_meta = get_post_meta( $post_id, 'wpex_overlay_header_dropdown_style', true ) ) {
				if ( 'default' != $dropdown_style_meta ) {
					$classes[] = 'wpex-dropdown-style-' . $dropdown_style_meta;
				}
			} else {
				unset( $classes['wpex-dropdown-style-' . $dropdown_style] );
				$classes[] = 'wpex-dropdown-style-black';
			}
		}

		// Add overlay header class
		$classes[] = 'overlay-header';

		// Add overlay header style class
		$classes[] = $overlay_style . '-style';

	}

	// Custom bg
	if ( wpex_get_mod( 'header_background' ) ) {
		$classes[] = 'custom-bg';
	}

	// Background style
	if ( wpex_header_background_image() ) {
		$bg_style = get_theme_mod( 'header_background_image_style' );
		$bg_style = $bg_style ? $bg_style : '';
		$bg_style = apply_filters( 'wpex_header_background_image_style', $bg_style );
		if ( $bg_style ) {
			$classes[] = 'bg-' . $bg_style;
		}
	}

	// Dynamic style class
	$classes[] = 'dyn-styles';

	// Clearfix class
	$classes[] = 'clr';

	// Sanitize classes
	$classes = array_map( 'esc_attr', $classes );

	// Set keys equal to vals
	$classes = array_combine( $classes, $classes );

	// Apply filters for child theming
	$classes = apply_filters( 'wpex_header_classes', $classes );

	// Turn classes into space seperated string
	$classes = implode( ' ', $classes );

	// return classes
	return $classes;

}

/**
 * Get site header background image
 *
 * @since 4.5.5.1
 */
function wpex_header_background_image() {

	// Get default Customizer value
	$image = wpex_get_mod( 'header_background_image' );

	// Apply filters before meta checks => meta should always override
	$image = apply_filters( 'wpex_header_background_image', $image );

	// Check meta for bg image
	$post_id = wpex_get_current_post_id();
	if ( $post_id && $meta_image = get_post_meta( $post_id, 'wpex_header_background_image', true ) ) {
		$image = $meta_image;
	}

	// Return image
	return wpex_get_image_url( $image );
}

/**
 * Returns header logo image
 *
 * @since 4.0
 */
function wpex_header_logo_img() {
	return wpex_get_image_url( apply_filters( 'wpex_header_logo_img_url', wpex_get_translated_theme_mod( 'custom_logo' ) ) );
}

/**
 * Check if the site is using a text logo
 *
 * @since 4.3
 */
function wpex_header_has_text_logo() {
	return wpex_header_logo_img() ? false : true;
}

/**
 * Returns header logo icon
 *
 * @since 2.0.0
 */
function wpex_header_logo_icon() {

	// Get logo img from admin panel
	$icon = esc_html( wpex_get_mod( 'logo_icon' ) );

	// Apply filter for child theming
	$icon = apply_filters( 'wpex_header_logo_icon', $icon );

	// Apply an empty/hidden icon in the customizer for postMessage support
	if ( 'none' == $icon && is_customize_preview() ) {
		$icon = 'wpex-hidden';
	}

	// Return icon
	if ( $icon && 'none' != $icon ) {
		return '<span id="site-logo-fa-icon" class="ticon ticon-' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
	}

}

/**
 * Returns header logo title
 *
 * @since 2.0.0
 */
function wpex_header_logo_title() {
	return apply_filters( 'wpex_logo_title', get_bloginfo( 'name' ) ); // @todo rename to wpex_header_logo_title
}

/**
 * Check if the header logo should scroll up on click
 *
 * @since 4.5.3
 */
function wpex_header_logo_scroll_top() {
	$bool = apply_filters( 'wpex_header_logo_scroll_top', false );
	if ( $post_id = wpex_get_current_post_id() ) {
		$meta = get_post_meta( $post_id, 'wpex_logo_scroll_top', true );
		if ( 'enable' == $meta ) {
			$bool = true;
		} elseif ( 'disable' == $meta ) {
			$bool = false;
		}
	}
	return $bool;
}

/**
 * Returns header logo URL
 *
 * @since 2.0.0
 */
function wpex_header_logo_url() {
	$url = '';
	if ( wpex_header_logo_scroll_top() ) {
		$url = '#';
	} elseif ( wpex_vc_is_inline() ) {
		$url = get_permalink();
	}
	$url = $url ? $url : home_url( '/' );
	return apply_filters( 'wpex_logo_url', $url ); // @todo rename to wpex_header_logo_url
}

/**
 * Header logo classes
 *
 * @since 2.0.0
 */
function wpex_header_logo_classes() {

	// Define classes array
	$classes = array( 'site-branding', 'clr' );

	// Default class
	$classes[] = 'header-' . sanitize_html_class( wpex_header_style() ) . '-logo';

	// Get custom overlay logo
	if ( wpex_has_overlay_header() && wpex_overlay_header_logo_img() ) {
		$classes[] = 'has-overlay-logo';
	}

	// Scroll top
	if ( wpex_header_logo_scroll_top() ) {
		$classes[] = 'wpex-scroll-top';
	}

	// Sanitize classes
	$classes = array_map( 'esc_attr', $classes );

	// Apply filters for child theming
	$classes = apply_filters( 'wpex_header_logo_classes', $classes );

	// Turn classes into space seperated string
	$classes = implode( ' ', $classes );

	// Return classes
	return $classes;

}

/*-------------------------------------------------------------------------------*/
/* [ Logo ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns correct header logo height
 *
 * @since 4.0
 */
function wpex_header_logo_img_height() {
	$height = apply_filters( 'logo_height', wpex_get_mod( 'logo_height' ) ); // @todo rename to wpex_header_logo_height unless this is also a setting coming from WordPress
	return $height ? $height : '';  // can't be empty or 0
}

/**
 * Returns correct header logo width
 *
 * @since 4.0
 */
function wpex_header_logo_img_width() {
	$width = apply_filters( 'logo_width', wpex_get_mod( 'logo_width' ) );
	return $width ? $width : ''; // can't be empty or 0
}

/**
 * Returns correct heeader logo retina img
 *
 * @since 4.0
 */
function wpex_header_logo_img_retina() {

	// Overlay header custom logo retina version
	if ( wpex_has_overlay_header() && wpex_overlay_header_logo_img() ) {
		$logo = wpex_overlay_header_logo_img_retina();
	}

	// Default retina logo
	else {
		$logo = wpex_get_translated_theme_mod( 'retina_logo' );
	}

	// Apply filters
	$logo = apply_filters( 'wpex_retina_logo_url', $logo ); // // @todo deprecate using apply_filters_deprecated
	$logo = apply_filters( 'wpex_header_logo_img_retina_url', $logo );

	// Set correct scheme and return
	return wpex_get_image_url( $logo );

}

/**
 * Returns correct heeader logo retina img height
 *
 * @since 4.0
 */
function wpex_header_logo_img_retina_height() {

	// Get default height from customizer setting
	$height = wpex_get_translated_theme_mod( 'logo_height' );

	// Get post id
	$post_id = wpex_get_current_post_id();

	// Check overlay header
	if ( wpex_has_overlay_header() && $overlay_logo_height = wpex_overlay_header_logo_img_retina_height() ) {
		$height = $overlay_logo_height;
	}

	// Apply filters and sanitize
	$height = absint( apply_filters( 'wpex_retina_logo_height', $height ) );

	// Return height value
	return $height ? $height : false;

}

/**
 * Adds js for the retina logo
 *
 * @since 1.1.0
 */
function wpex_header_logo_img_retina_js() {

	// Not needed in admin or if there is a custom header
	if ( is_admin() || wpex_has_custom_header() ) {
		return;
	}

	// Get retina logo url
	$logo_url = wpex_header_logo_img_retina();

	// Logo url is required
	if ( ! $logo_url ) {
		return;
	}

	// Get logo height
	$logo_height = wpex_header_logo_img_retina_height();

	// Logo height is required
	if ( ! $logo_height ) {
		return;
	} ?>

	<!-- Retina Logo -->
	<script>
		jQuery( function( $ ){
			if ( window.devicePixelRatio >= 2 ) {
				$( "#site-logo img.logo-img" ).attr( "src", "<?php echo esc_url( $logo_url ); ?>" ).css( "max-height","<?php echo absint( $logo_height ); ?>px" );
			}
		} );
	</script>

<?php }
add_action( 'wp_head', 'wpex_header_logo_img_retina_js' );

/*-------------------------------------------------------------------------------*/
/* [ Header Overlay Style ]
/*-------------------------------------------------------------------------------*/

/**
 * Checks if the overlay header is enabled
 *
 * @since 4.0
 */
function wpex_has_overlay_header() {

	// Return false if header is disabled
	// @todo is this check really needed?
	if ( ! wpex_has_header() ) {
		return false;
	}

	// False by default
	$return = false;

	// Get current post id
	$post_id = wpex_get_current_post_id();

	// Return true if enabled via the post meta
	if ( $post_id && 'on' == get_post_meta( $post_id, 'wpex_overlay_header', true ) ) {
		$return = true;
	}

	// Return false if page is password protected and the page header is disabled
	if ( post_password_required() && ! wpex_has_page_header() ) {
		$return = false;
	}

	// Apply filters and return
	return apply_filters( 'wpex_has_overlay_header', $return );

}

/**
 * Returns overlay header style
 *
 * @since 4.0
 */
function wpex_overlay_header_style() {

	// Default style is empty
	$style = '';

	// Get post id
	$post_id = wpex_get_current_post_id();

	// If overlay header is enabled
	if ( $post_id ) {
		$style = get_post_meta( $post_id, 'wpex_overlay_header_style', true );
		$style = $style ? $style : 'light'; // Fallback for when light setting used to be empty, must keep
	}

	// Apply filters and return
	return apply_filters( 'wpex_header_overlay_style', $style );
}

/**
 * Returns correct logo image for the overlay header image
 *
 * @since 4.0
 */
function wpex_overlay_header_logo_img() {

	// Check custom field for logo
	$logo = get_post_meta( wpex_get_current_post_id(), 'wpex_overlay_header_logo', true );

	// Check old REDUX methods
	if ( is_array( $logo ) ) {
		if ( ! empty( $logo['url'] ) ) {
			$logo = $logo['url'];
		} else {
			$logo = false;
		}
	}

	// Apply filters for child theming and return image URL
	return wpex_get_image_url( apply_filters( 'wpex_header_overlay_logo', $logo ) );

}

/**
 * Returns correct retina logo image for the overlay header image
 *
 * @since 4.0
 */
function wpex_overlay_header_logo_img_retina() {

	// Get meta value
	$logo = get_post_meta( wpex_get_current_post_id(), 'wpex_overlay_header_logo_retina', true );

	// Apply filters for child theming and return image URL
	return wpex_get_image_url( apply_filters( 'wpex_header_overlay_logo_retina', $logo ) );

}

/**
 * Returns correct retina logo image height for the overlay header image
 *
 * @since 4.0
 */
function wpex_overlay_header_logo_img_retina_height() {
	return absint( get_post_meta( wpex_get_current_post_id(), 'wpex_overlay_header_logo_retina_height', true ) );
}

/*-------------------------------------------------------------------------------*/
/* [ Sticky Header ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if sticky header is enabled
 *
 * @since 4.0
 */
function wpex_has_sticky_header() {

	// Disable in live editor
	if ( wpex_vc_is_inline() ) {
		return;
	}

	// Disabled by default
	$return = false;

	// Get current post id
	$post_id = wpex_get_current_post_id();

	// Check meta first it should override any filter!
	if ( $post_id && 'disable' == get_post_meta( $post_id, 'wpex_sticky_header', true ) ) {
		return false;
	}

	// Get header style
	$header_style = wpex_header_style( $post_id );

	// Sticky header for builder
	if ( 'builder' == $header_style ) {
		$return = wpex_get_mod( 'header_builder_sticky', false );
	}

	// Standard sticky header
	else {

		// Return false if sticky header style is set to disabled
		if ( 'disabled' == wpex_sticky_header_style() ) {
			$return = false;
		}

		// Otherwise check if the current header style supports sticky.
		elseif ( in_array( $header_style, wpex_get_header_styles_with_sticky_support() ) ) {
			$return = true;
		}

	}

	// Apply filters and return
	return apply_filters( 'wpex_has_fixed_header', $return ); // @todo rename to wpex_has_sticky_header

}

/**
 * Get sticky header style
 *
 * @since 4.0
 */
function wpex_sticky_header_style() {

	if ( 'builder' == wpex_header_style() ) {
		return 'standard'; // Header builder only supports standard
	}

	// Get default style from customizer
	$style = wpex_get_mod( 'fixed_header_style', 'standard' );

	// If disabled in Customizer but enabled in meta set to "standard" style
	if ( 'disabled' == $style && 'enable' == get_post_meta( wpex_get_current_post_id(), 'wpex_sticky_header', true ) ) {
		$style = 'standard';
	}

	// Sanitize
	$style = $style ? $style : 'standard';

	// Return style
	return apply_filters( 'wpex_sticky_header_style', $style );

}

/**
 * Returns correct sticky header logo img
 *
 * @since 4.0
 */
function wpex_sticky_header_logo_img() {

	if ( 'builder' == wpex_header_style() ) {
		return ''; // Not needed for the sticky header builder
	}

	// Get fixed header logo from the Customizer
	$logo = wpex_get_mod( 'fixed_header_logo' );

	// Set sticky logo to header logo for overlay header when custom overlay logo is set
	// This way you can have a white logo on overlay but the default on sticky.
	if ( ! $logo
		&& wpex_has_overlay_header()
		&& 'light' != wpex_overlay_header_style() // this is a fallback to the original style @todo remove?
		&& wpex_overlay_header_logo_img()
	) {
		$header_logo = wpex_header_logo_img();
		$logo        = $header_logo ? $header_logo : $logo;
	}

	// Apply filters and return image URL
	return wpex_get_image_url( apply_filters( 'wpex_fixed_header_logo', $logo ) );

}

/**
 * Returns correct sticky header logo img retina version
 *
 * @since 4.0
 */
function wpex_sticky_header_logo_img_retina() {
	return wpex_get_image_url( apply_filters( 'wpex_fixed_header_logo_retina', wpex_get_translated_theme_mod( 'fixed_header_logo_retina' ) ) );
}

/**
 * Returns correct sticky header logo img retina version
 *
 * @since 4.0
 * @todo  Check if this function is used still...
 */
function wpex_sticky_header_logo_img_retina_height() {

	// Get height and apply filters
	$height = apply_filters( 'wpex_fixed_header_logo_retina_height', wpex_get_mod( 'fixed_header_logo_retina_height' ) );

	// Sanitize
	$height = $height ? intval( $height ) : null;

	// Return height
	return $height;

}

/**
 * Check if shrink sticky header is enabled
 *
 * @since 4.0
 */
function wpex_has_shrink_sticky_header() {
	$bool = false;
	if ( wpex_has_sticky_header()
		&& in_array( wpex_header_style(), wpex_get_header_styles_with_sticky_support() )
		&& in_array( wpex_sticky_header_style(), array( 'shrink', 'shrink_animated' ) ) ) {
		$bool = true;
	}
	return apply_filters( 'wpex_has_shrink_sticky_header', $bool );
}


/**
 * Return correct starting position for the sticky header
 *
 * @since 4.6.5
 */
function wpex_sticky_header_start_position() {
	$position = wpex_get_mod( 'fixed_header_start_position' );
	if ( is_singular() ) {
		$meta_position = get_post_meta( 'fixed_header_start_position', true );
		$position      = $meta_position ? $meta_position : $position;
	}
	return apply_filters( 'wpex_sticky_header_start_position', $position );
}

/*-------------------------------------------------------------------------------*/
/* [ Header Aside ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if the current header supports aside content
 *
 * @since 3.0.0
 */
function wpex_header_supports_aside( $header_style = '' ) {
	$bool = false;
	$header_style = $header_style ? $header_style : wpex_header_style();
	if ( in_array( $header_style, wpex_get_header_styles_with_aside_support() ) ) {
		$bool = true;
	}
	return apply_filters( 'wpex_header_supports_aside', $bool );
}

/**
 * Get Header Aside content
 *
 * @since 4.0
 */
function wpex_header_aside_content() {

	// Get header aside content
	$content = wpex_get_translated_theme_mod( 'header_aside' );

	// Check if content is a page ID and get page content
	if ( is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, 'page' );
		$post = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = $post->post_content;
		}
	}

	// Apply filters and return content
	return apply_filters( 'wpex_header_aside_content', $content );

}

/*-------------------------------------------------------------------------------*/
/* [ Header Builder ]
/*-------------------------------------------------------------------------------*/

/**
 * Get header builder ID
 *
 * @since 4.0
 */
function wpex_header_builder_id() {
	if ( ! wpex_get_mod( 'header_builder_enable', true ) ) {
		return;
	}
	$id = intval( apply_filters( 'wpex_header_builder_page_id', wpex_get_mod( 'header_builder_page_id' ) ) );
	if ( $id ) {
		$translated_id = wpex_parse_obj_id( $id, 'page' ); // translate
		$id = $translated_id ? $translated_id : $id; // if not translated return original ID
		if ( 'publish' == get_post_status( $id ) ) {
			return $id;
		}
	}
}

/**
 * Check if the theme is using the header buidler
 *
 * @since 4.1
 */
function wpex_has_custom_header() {
	return ! empty( wpex_header_builder_id() ) ? true : false;
}