<?php
/**
 * WooCommerce menu cart functions.
 *
 * @package Total WordPress Theme
 * @subpackage WooCommerce
 * @version 4.9.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add WooCommerce framents
 *
 * @since 4.0
 */
function wpex_menu_cart_icon_fragments( $fragments ) {
	$fragments['.wcmenucart']                   = wpex_wcmenucart_menu_item();
	$fragments['#mobile-menu .wpex-cart-count'] = wpex_mobile_menu_cart_count();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wpex_menu_cart_icon_fragments' );

/**
 * Get correct style for WooCommerce menu cart style
 *
 * @since 4.0
 */
function wpex_header_menu_cart_style() {

	// Return if disabled completely in Customizer
	if ( 'disabled' == wpex_get_mod( 'woo_menu_icon_display', 'icon_count' ) ) {
		return false;
	}

	// If not disabled get style from settings
	else {

		// Get Menu Icon Style
		$style = wpex_get_mod( 'woo_menu_icon_style', 'drop_down' );

		// Overlay header and header style six should use popup
		if ( 'six' == wpex_header_style() ) {
			$style = 'overlay';
		}

		// Return click style for these pages
		if ( is_cart() || is_checkout() ) {
			$style = 'custom-link';
		}

	}

	// Apply filters for child theme mods
	$style = apply_filters( 'wpex_menu_cart_style', $style );

	// Sanitize output so it's not empty and check for deprecated 'drop-down' style
	if ( 'drop-down' == $style || ! $style ) {
		$style = 'drop_down';
	}

	// Return style
	return $style;

}

/**
 * Returns header menu cart item
 *
 * @since 4.4
 */
function wpex_get_header_menu_cart_item( $style = '' ) {

	if ( ! $style ) {
		return;
	}

	// Get header style
	$header_style = wpex_header_style();

	// Define classes to add to li element
	$classes = 'woo-menu-icon wpex-menu-extra menu-item';

	// Add style class
	$classes .= ' wcmenucart-toggle-' . $style;

	// Prevent clicking on cart and checkout
	if ( 'custom-link' != $style && ( is_cart() || is_checkout() ) ) {
		$classes .= ' nav-no-click';
	}

	// Add toggle class
	else {
		$classes .= ' toggle-cart-widget';
	}

	// Add ubermenu classes
	if ( class_exists( 'UberMenu' ) && apply_filters( 'wpex_add_search_toggle_ubermenu_classes', true ) ) {
		$classes .= ' ubermenu-item-level-0 ubermenu-item';
	}

	// Add cart dropdown and replace ul/li for divs to prevent conflicts with menu
	$cart_dropdown = '';
	if ( in_array( $header_style, array( 'two', 'three', 'four', 'five', 'dev' ) ) ) {
		ob_start();
		get_template_part( 'partials/cart/cart-dropdown' );
		$cart_dropdown .= ob_get_clean();
	}

	// Add cart link to menu items
	return '<li class="' . $classes . '">' . wpex_wcmenucart_menu_item() . $cart_dropdown . '</li>';

}

/**
 * Add cart item to the header menu
 *
 * @since 4.0
 */
function wpex_add_header_menu_cart_item( $items, $args ) {

	// Only used for the main menu
	if ( 'main_menu' != $args->theme_location ) {
		return $items;
	}

	// Get style
	$style = wpex_header_menu_cart_style();

	// Return items if no style
	if ( ! $style ) {
		return $items;
	}

	// Add cart item to menu
	$items .= wpex_get_header_menu_cart_item( $style );

	// Add mobile menu link
	if ( apply_filters( 'woo_mobile_menu_cart_link', true )
		&& wpex_header_has_mobile_menu()
		&& $cart_id = wpex_parse_obj_id( wc_get_page_id( 'cart' ), 'page' )
	) {
		$items .= '<li class="menu-item wpex-mm-menu-item"><a href="' . esc_url( get_permalink( $cart_id ) ) . '"><span class="link-inner">' . esc_html( wpex_get_mod( 'woo_menu_cart_text', esc_html__( 'Cart', 'total' ) ) ) . '</span></a></li>';
	}

	// Return menu items
	return $items;

}
add_filter( 'wp_nav_menu_items', 'wpex_add_header_menu_cart_item', 10, 2 );

/**
 * Creates the WooCommerce link for the navbar
 * Must check if function exists for easier child theme edits.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_wcmenucart_menu_item' ) ) {
	function wpex_wcmenucart_menu_item() {

		// Vars
		global $woocommerce;
		$icon_style   = wpex_get_mod( 'woo_menu_icon_style', 'drop-down' );
		$custom_link  = wpex_get_mod( 'woo_menu_icon_custom_link' );
		$count        = WC()->cart->cart_contents_count;

		// Link classes
		$a_classes = 'wcmenucart';
		$count     = $count ? $count : '0';
		$a_classes .= ' wcmenucart-items-' . intval( $count );
		if ( $count && '0' !== $count ) {
			$a_classes .= ' wpex-has-items';
		}
		if ( class_exists( 'UberMenu' ) && apply_filters( 'wpex_add_search_toggle_ubermenu_classes', true ) ) {
			$a_classes .= ' ubermenu-target ubermenu-item-layout-default ubermenu-item-layout-text_only';
		}

		// Define cart icon link URL
		$url = '';
		if ( 'custom-link' == $icon_style && $custom_link ) {
			$url = esc_url( $custom_link );
		} elseif ( $cart_id = wpex_parse_obj_id( wc_get_page_id( 'cart' ), 'page' ) ) {
			$url = get_permalink( $cart_id );
		}

		// Cart total
		$display = wpex_get_mod( 'woo_menu_icon_display', 'icon_count' );
		$count_txt = absint( $count );
		if ( 'icon_total' == $display ) {
			$cart_extra = WC()->cart->get_cart_total();
			$cart_extra = str_replace( 'amount', 'amount wcmenucart-details', $cart_extra );
		} elseif ( 'icon_count' == $display ) {
			$extra_class = 'wcmenucart-details count';
			if ( $count && '0' != $count ) {
				$extra_class .= ' wpex-has-items';
			}
			if ( 'six' == wpex_header_style() ) {
				$count_txt = '(' . $count_txt . ')';
			} elseif ( wpex_get_mod( 'wpex_woo_menu_icon_bubble', true ) ) {
				$extra_class .= ' t-bubble';
			}
			$cart_extra = '<span class="' . $extra_class . '">' . $count_txt . '</span>';
		} else {
			$cart_extra = '';
		}

		// Cart Icon
		$icon_class = wpex_get_mod( 'woo_menu_icon_class' );
		$icon_class = $icon_class ? esc_attr( $icon_class ) : 'shopping-cart';
		$cart_text  = wpex_get_mod( 'woo_menu_cart_text', esc_html__( 'Cart', 'total' ) );
		$cart_icon  = '<span class="wcmenucart-icon ticon ticon-' . $icon_class . '"></span><span class="wcmenucart-text">' . esc_html( $cart_text ) . '</span>';
		$cart_icon = apply_filters( 'wpex_menu_cart_icon_html', $cart_icon, $icon_class );

		// Output
		$output = '<a href="' . esc_url( $url ) . '" class="' . $a_classes . '">';

			$output .= '<span class="link-inner">';

				$output .= '<span class="wcmenucart-count">' . $cart_icon . $cart_extra . '</span>';

			$output .= '</span>';

		$output .= '</a>';

		return $output;

	}
}

/**
 * Add cart overlay html to site
 *
 * @since 4.0
 */
function wpex_cart_overlay_html() {
	if ( 'overlay' == wpex_header_menu_cart_style() ) {
		get_template_part( 'partials/cart/cart-overlay' );
	}
}
add_action( 'wpex_outer_wrap_after', 'wpex_cart_overlay_html' );

/**
 * Add cart dropdown html
 *
 * @since 4.0
 */
function wpex_add_cart_dropdown_html() {

	// Return if style not set to dropdown
	if ( 'drop_down' != wpex_header_menu_cart_style() ) {
		return;
	}

	// Should we get the template part?
	$get = false;

	// Get current header style
	$header_style = wpex_header_style();

	// Header Inner Hook
	if ( 'wpex_hook_header_inner' == current_filter() ) {
		if ( 'one' == $header_style ) {
			$get = true;
		}
	}

	// Get template file
	if ( $get ) {
		get_template_part( 'partials/cart/cart-dropdown' );
	}

}
add_action( 'wpex_hook_header_inner', 'wpex_add_cart_dropdown_html', 40 );
add_action( 'wpex_hook_main_menu_bottom', 'wpex_add_cart_dropdown_html' );

/**
 * Menu Cart counter span
 *
 * @since 4.0
 */
function wpex_mobile_menu_cart_count() {
	$count = absint( WC()->cart->cart_contents_count );
	$count = $count ? $count : '0';
	$classes = 'wpex-cart-count';
	if ( $count && '0' != $count ) {
		$classes .= ' wpex-has-items';
	}
	return '<span class="' . $classes . '">' . $count . '</span>';
}