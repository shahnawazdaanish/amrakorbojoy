<?php
/**
 * These functions are used to load template parts (partials) when used within action hooks,
 * and they probably should never be updated or modified.
 *
 * @package Total WordPress Theme
 * @subpackage Hooks
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*-------------------------------------------------------------------------------*/
/* -  Accessibility
/*-------------------------------------------------------------------------------*/

/**
 * Get skip to content link
 *
 * @since 4.2
 */
function wpex_skip_to_content_link() {
	if ( wpex_get_mod( 'skip_to_content', true ) ) {
		get_template_part( 'partials/accessibility/skip-to-content' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Toggle Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get togglebar layout template part if enabled.
 *
 * @since 1.0.0
 */
function wpex_toggle_bar() {
	if ( wpex_has_togglebar() ) {
		wpex_get_template_part( 'togglebar' );
	}
}

/**
 * Get togglebar button template part.
 *
 * @since 1.0.0
 */
function wpex_toggle_bar_button() {
	if ( wpex_has_togglebar() ) {
		wpex_get_template_part( 'togglebar_button' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Top Bar
/*-------------------------------------------------------------------------------*/

/**
 * Get Top Bar layout template part if enabled
 *
 * @since 1.0.0
 */
function wpex_top_bar() {
	if ( wpex_has_topbar() ) {
		wpex_get_template_part( 'topbar' );
	}
}

/**
 * Get topbar content
 *
 * @since 3.6.0
 */
function wpex_tobar_content() {
	wpex_get_template_part( 'topbar_content' );
}

/**
 * Get topbar social
 *
 * @since 3.6.0
 */
function wpex_topbar_social() {
	wpex_get_template_part( 'topbar_social' );
}

/*-------------------------------------------------------------------------------*/
/* -  Header
/*-------------------------------------------------------------------------------*/

/**
 * Get the header template part if enabled.
 *
 * @since 1.5.3
 */
function wpex_header() {
	if ( wpex_has_header() ) {
		wpex_get_template_part( 'header' );
	}
}

/**
 * Get the header logo template part.
 *
 * @since 1.0.0
 */
function wpex_header_logo() {
	wpex_get_template_part( 'header_logo' );
}

/**
 * Get the header logo inner content
 *
 * @since 4.5.5
 */
function wpex_header_logo_inner() {
	wpex_get_template_part( 'header_logo_inner' );
}

/**
 * Get the header aside content template part.
 *
 * @since 1.5.3
 */
function wpex_header_aside() {
	if ( wpex_header_supports_aside() ) {
		wpex_get_template_part( 'header_aside' );
	}
}

/**
 * Add search dropdown to header inner
 *
 * @since 4.5.4
 */
function wpex_header_inner_search_dropdown() {

	// Make sure site is set to dropdown style
	if ( 'drop_down' != wpex_header_menu_search_style() ) {
		return;
	}

	// Only added in the header for certain styles
	if ( in_array( wpex_header_style(), array( 'two', 'three', 'four', 'five', 'six', 'dev' ) ) ) {
		return;
	}

	// Get template part
	wpex_get_template_part( 'header_search_dropdown' );

}

/**
 * Get header search dropdown template part.
 *
 * @since 1.0.0
 * @deprecated 4.5.4
 */
function wpex_search_dropdown() {
	wpex_get_template_part( 'header_search_dropdown' );
}

/**
 * Get header search replace template part.
 *
 * @since 1.0.0
 */
function wpex_search_header_replace() {
	if ( 'header_replace' == wpex_header_menu_search_style() ) {
		wpex_get_template_part( 'header_search_replace' );
	}
}

/**
 * Gets header search overlay template part.
 *
 * @since 1.0.0
 */
function wpex_search_overlay() {
	if ( 'overlay' == wpex_header_menu_search_style() ) {
		wpex_get_template_part( 'header_search_overlay' );
	}
}

/**
 * Overlay Header Wrap Open
 *
 * @since 3.2.0
 */
function wpex_overlay_header_wrap_open() {
	if ( wpex_has_overlay_header() ) {
		echo '<div id="overlay-header-wrap" class="clr">';
	}
}

/**
 * Overlay Header Wrap Close
 *
 * @since 3.2.0
 */
function wpex_overlay_header_wrap_close() {
	if ( wpex_has_overlay_header() ) {
		echo '</div><!-- .overlay-header-wrap -->';
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Menu
/*-------------------------------------------------------------------------------*/

/**
 * Outputs the main header menu
 *
 * @since 1.0.0
 */
function wpex_header_menu() {
	$get          = false;
	$header_style = wpex_header_style();
	$filter       = current_filter();

	// Header Inner Hook
	if ( 'wpex_hook_header_inner' == $filter ) {
		if ( in_array( $header_style, array( 'one', 'five', 'six', 'dev' ) ) ) {
			$get = true;
		}
	}

	// Header Top Hook
	elseif ( 'wpex_hook_header_top' == $filter ) {
		if (  'four' == $header_style ) {
			$get = true;
		}
	}

	// Header bottom hook
	elseif ( 'wpex_hook_header_bottom' == $filter ) {
		if ( in_array( $header_style, array( 'two', 'three' ) ) ) {
			$get = true;
		}
	}

	// Get menu template part
	if ( $get ) {
		wpex_get_template_part( 'header_menu' );
	}

}

/*-------------------------------------------------------------------------------*/
/* -  Menu > Mobile
/*-------------------------------------------------------------------------------*/

/**
 * Gets the template part for the fixed top mobile menu style
 *
 * @since 3.0.0
 */
function wpex_mobile_menu_fixed_top() {
	if ( wpex_header_has_mobile_menu() && 'fixed_top' == wpex_header_menu_mobile_toggle_style() ) {
		wpex_get_template_part( 'header_mobile_menu_fixed_top' );
	}
}

/**
 * Gets the template part for the navbar mobile menu_style
 *
 * @since 3.0.0
 */
function wpex_mobile_menu_navbar() {

	// Get var
	$get = false;

	// Current filter
	$filter = current_filter();

	// Check where to place menu
	$before_wrap = ( 'outer_wrap_before' == wpex_get_mod( 'mobile_menu_navbar_position' ) ) ? true : false;

	// Check if overlay header is enabled
	if ( ! $before_wrap ) {
		$before_wrap = wpex_has_overlay_header();
	}

	// Overlay header should display above and others below
	if ( $filter == 'wpex_outer_wrap_before' && $before_wrap ) {
		$get = true;
	} elseif ( $filter == 'wpex_hook_header_bottom' && ! $before_wrap ) {
		$get = true;
	}

	// Get mobile menu navbar
	if ( $get && wpex_header_has_mobile_menu() && 'navbar' == wpex_header_menu_mobile_toggle_style() ) {
		wpex_get_template_part( 'header_mobile_menu_navbar' );
	}

}

/**
 * Gets the template part for the "icons" style mobile menu.
 *
 * @since 1.0.0
 */
function wpex_mobile_menu_icons() {
	$style = wpex_header_menu_mobile_toggle_style();
	if ( wpex_header_has_mobile_menu()
		&& ( 'icon_buttons' == $style || 'icon_buttons_under_logo' == $style )
	) {
		wpex_get_template_part( 'header_mobile_menu_icons' );
	}
}

/**
 * Get mobile menu alternative if enabled.
 *
 * @since 1.3.0
 */
function wpex_mobile_menu_alt() {
	if ( wpex_has_mobile_menu_alt() ) {
		wpex_get_template_part( 'header_mobile_menu_alt' );
	}
}

/**
 * Mobile Menu Extras.
 *
 * @since 4.9.8
 */
function wpex_mobile_menu_extras() {
	if ( wpex_header_has_mobile_menu() ) {
		wpex_get_template_part( 'header_mobile_menu_extras' );
	}
}

/**
 * Sidr Close button
 *
 * @since 3.2.0
 */
function wpex_sidr_close() {
	if ( 'sidr' != wpex_header_menu_mobile_style() ) {
		return;
	}
	$aria_label = wpex_get_mod( 'mobile_menu_toggle_aria_label', esc_attr_x( 'Toggle mobile menu', 'aria-label', 'total' ) );
	echo '<div id="sidr-close"><div class="wpex-close"><a href="#" aria-expanded="false" role="button" aria-label="' . esc_attr( $aria_label ) .'">&times;</a></div></div>';
}

/*-------------------------------------------------------------------------------*/
/* -  Page Header
/*-------------------------------------------------------------------------------*/

/**
 * Get page header template part if enabled.
 *
 * @since 1.5.2
 */
function wpex_page_header() {
	if ( wpex_has_page_header() ) {
		wpex_get_template_part( 'page_header' );
	}
}

/**
 * Get page header title template part if enabled.
 *
 * @since 1.0.0
 */
function wpex_page_header_title() {
	if ( wpex_has_page_header_title() ) {
		wpex_get_template_part( 'page_header_title' );
	}
}

/**
 * Get post heading template part.
 *
 * @since 1.0.0
 */
function wpex_page_header_subheading() {
	if ( wpex_page_header_has_subheading() ) {
		wpex_get_template_part( 'page_header_subheading' );
	}
}

/**
 * Open wrapper around page header content to vertical align things
 *
 * @since 3.3.3
 */
function wpex_page_header_title_table_wrap_open() {
	if ( 'background-image' == wpex_page_header_style() ) {
		echo '<div class="page-header-table clr"><div class="page-header-table-cell">';
	}
}

/**
 * Close wrapper around page header content to vertical align things
 *
 * @since 3.3.3
 */
function wpex_page_header_title_table_wrap_close() {
	if ( 'background-image' == wpex_page_header_style() ) {
		echo '</div></div>';
	}
}

/**
 * Echo breadcrumbs
 *
 * @since 1.0.0
 */
function wpex_display_breadcrumbs() {
	if ( 'custom' != wpex_get_mod( 'breadcrumbs_position', 'absolute' ) ) {
		wpex_get_template_part( 'breadcrumbs' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Content
/*-------------------------------------------------------------------------------*/

/**
 * Gets sidebar template
 *
 * @since 2.1.0
 */
function wpex_get_sidebar_template() {
	if ( ! in_array( wpex_content_area_layout(), array( 'full-screen', 'full-width' ) ) ) {
		get_sidebar( apply_filters( 'wpex_get_sidebar_template', null ) );
	}
}

/**
 * Displays correct sidebar
 *
 * @since 1.6.5
 */
function wpex_display_sidebar() {
	if ( wpex_has_sidebar() && $sidebar = wpex_get_sidebar() ) {
		dynamic_sidebar( $sidebar );
	}
}

/**
 * Get term description.
 *
 * @since 1.0.0
 */
function wpex_term_description() {
	if ( wpex_has_term_description_above_loop() ) {
		wpex_get_template_part( 'term_description' );
	}
}

/**
 * Get next/previous links.
 *
 * @since 1.0.0
 */
function wpex_next_prev() {
	if ( wpex_has_next_prev() ) {
		wpex_get_template_part( 'next_prev' );
	}
}

/**
 * Get next/previous links.
 *
 * @since 1.0.0
 */
function wpex_post_edit() {
	if ( wpex_has_post_edit() ) {
		wpex_get_template_part( 'post_edit' );
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Blog
/*-------------------------------------------------------------------------------*/

/**
 * Blog single media above content
 *
 * @since 1.0.0
 */
function wpex_blog_single_media_above() {

	// Only needed for blog posts
	if ( ! is_singular() ) {
		return;
	}

	// Blog media position
	$position = apply_filters( 'wpex_blog_single_media_position', wpex_get_custom_post_media_position() );

	// Display the post media above the post (this is a meta option)
	if ( 'above' == $position && ! post_password_required() ) {

		// Standard posts
		if ( 'post' == get_post_type() ) {

			// Get correct media template part
			wpex_get_template_part( 'blog_single_media', get_post_format() );

		}

		// Other post types
		else {

			wpex_get_template_part( 'cpt_single_media' );

		}

	}

}

/*-------------------------------------------------------------------------------*/
/* -  Footer
/*-------------------------------------------------------------------------------*/

/**
 * Gets the footer callout template part.
 *
 * @since 1.0.0
 */
function wpex_footer_callout() {
	if ( wpex_has_callout() ) {
		if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer_callout' ) ) {
			wpex_get_template_part( 'footer_callout' );
		}
	}
}

/**
 * Gets the footer layout template part.
 *
 * @since 2.0.0
 */
function wpex_footer() {
	if ( wpex_has_footer() ) {
		wpex_get_template_part( 'footer' );
	}
}

/**
 * Get the footer widgets template part.
 *
 * @since 1.0.0
 */
function wpex_footer_widgets() {
	wpex_get_template_part( 'footer_widgets' );
}

/**
 * Gets the footer bottom template part.
 *
 * @since 1.0.0
 */
function wpex_footer_bottom() {
	if ( wpex_has_footer_bottom() ) {
		wpex_get_template_part( 'footer_bottom' );
	}
}

/**
 * Gets the scroll to top button template part.
 *
 * @since 1.0.0
 */
function wpex_scroll_top() {
	if ( wpex_get_mod( 'scroll_top', true ) ) {
		wpex_get_template_part( 'scroll_top' );
	}
}

/**
 * Footer reaveal open code
 *
 * @since 2.0.0
 */
function wpex_footer_reveal_open() {
	if ( wpex_footer_has_reveal() ) {
		wpex_get_template_part( 'footer_reveal_open' );
	}
}

/**
 * Footer reaveal close code
 *
 * @since 2.0.0
 */
function wpex_footer_reveal_close() {
	if ( wpex_footer_has_reveal() ) {
		wpex_get_template_part( 'footer_reveal_close' );
	}
}

/**
 * Site Frame Border
 *
 * @since 2.0.0
 */
function wpex_site_frame_border() {
	if ( wpex_has_site_frame_border() || is_customize_preview() ) {
		echo '<div id="wpex-sfb-l"></div><div id="wpex-sfb-r"></div><div id="wpex-sfb-t"></div><div id="wpex-sfb-b"></div>';
	}
}

/*-------------------------------------------------------------------------------*/
/* -  Footer Bottom
/*-------------------------------------------------------------------------------*/

/**
 * Footer bottom flex box open
 *
 * @since 4.9.3
 */
function wpex_footer_bottom_flex_open() {
	echo '<div class="footer-bottom-flex clr">';
}


/**
 * Footer bottom flex box close
 *
 * @since 4.9.3
 */
function wpex_footer_bottom_flex_close() {
	echo '</div><!-- .footer-bottom-flex -->';
}

/**
 * Footer bottom copyright
 *
 * @since 2.0.0
 */
function wpex_footer_bottom_copyright() {
	wpex_get_template_part( 'footer_bottom_copyright' );
}

/**
 * Footer bottom menu
 *
 * @since 2.0.0
 */
function wpex_footer_bottom_menu() {
	wpex_get_template_part( 'footer_bottom_menu' );
}

/*-------------------------------------------------------------------------------*/
/* -  Other
/*-------------------------------------------------------------------------------*/

/**
 * Site Overlay
 *
 * @since 3.4.0
 */
function wpex_site_overlay() {
	echo '<div class="wpex-site-overlay"></div>';
}

/**
 * Site Top div
 *
 * @since 3.4.0
 */
function wpex_ls_top() {
	echo '<span data-ls_id="#site_top"></span>';
}

/**
 * Returns social sharing template part
 *
 * @since 2.0.0
 */
function wpex_social_share() {
	wpex_get_template_part( 'social_share' );
}

/**
 * Adds a hidden searchbox in the footer for use with the mobile menu
 *
 * @since 1.5.1
 */
function wpex_mobile_searchform() {
	if ( wpex_get_mod( 'mobile_menu_search', true ) ) {
		$mm_style = wpex_header_menu_mobile_style();
		if ( $mm_style && 'custom' != $mm_style ) {
			wpex_get_template_part( 'mobile_searchform' );
		}
	}
}

/**
 * Outputs page/post slider based on the wpex_post_slider_shortcode custom field
 *
 * @since 1.0.0
 */
function wpex_post_slider( $post_id = '', $postion = '' ) {

	// Get post id
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Return if there isn't a slider defined
	if ( ! wpex_post_has_slider( $post_id ) ) {
		return;
	}

	// Get current filter
	$filter = current_filter();

	// Define get variable
	$get = false;

	// Get slider position
	$position = wpex_post_slider_position( $post_id );

	// Get current filter against slider position
	if ( 'above_topbar' == $position && 'wpex_hook_topbar_before' == $filter ) {
		$get = true;
	} elseif ( 'above_header' == $position && 'wpex_hook_header_before' == $filter ) {
		$get = true;
	} elseif ( 'above_menu' == $position && 'wpex_hook_header_bottom' == $filter ) {
		$get = true;
	} elseif ( 'above_title' == $position && 'wpex_hook_page_header_before' == $filter ) {
		$get = true;
	} elseif ( 'below_title' == $position && 'wpex_hook_main_top' == $filter ) {
		$get = true;
	}

	// Return if $get is still false after checking filters
	if ( $get ) {
		wpex_get_template_part( 'post_slider' );
	}

}