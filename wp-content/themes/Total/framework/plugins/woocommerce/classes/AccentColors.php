<?php
/**
 * WooCommerce accent colors
 *
 * @package Total WordPress Theme
 * @subpackage WooCommerce
 * @version 5.0
 *
 */

namespace TotalTheme\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AccentColors {

	/**
	 * Main Class Constructor
	 *
	 * @since 4.1
	 */
	public function __construct() {
		add_filter( 'wpex_accent_texts', array( $this, 'accent_texts' ) );
		add_filter( 'wpex_accent_borders', array( $this, 'accent_borders' ) );
		add_filter( 'wpex_accent_backgrounds', array( $this, 'accent_backgrounds' ) );
		add_filter( 'wpex_border_color_elements', array( $this, 'border_color_elements' ) );
	}

	/**
	 * Adds border accents for WooCommerce styles.
	 *
	 * @since 4.1
	 */
	public function accent_texts( $texts ) {
		return array_merge( array(
			'.woocommerce .order-total td',
			'.price > .amount',
			'.price ins .amount',
		), $texts );
	}

	/**
	 * Adds border accents for WooCommerce styles.
	 *
	 * @since 4.1
	 */
	public function accent_borders( $borders ) {
		return array_merge( array(
			'#current-shop-items-dropdown' => array( 'top' ),
			'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a' => array( 'bottom' ),
		), $borders );
	}

	/**
	 * Adds border accents for WooCommerce styles.
	 *
	 * @since 4.1
	 */
	public function accent_backgrounds( $backgrounds ) {
		return array_merge( array(
			'.woocommerce-MyAccount-navigation li.is-active a',
			'.woocommerce .widget_price_filter .ui-slider .ui-slider-range',
			'.woocommerce .widget_price_filter .ui-slider .ui-slider-handle',
			'#mobile-menu .wpex-cart-count.wpex-has-items',
			'.wcmenucart-details.count.t-bubble',
			'.select2-container--default .select2-results__option--highlighted[aria-selected],.select2-container--default .select2-results__option--highlighted[data-selected]',
		), $backgrounds );
	}

	/**
	 * Adds border color elements for WooCommerce styles.
	 *
	 * @since 4.1
	 */
	public function border_color_elements( $elements ) {
		return array_merge( array(

			// Product
			'.product_meta',
			'.woocommerce div.product .woocommerce-tabs ul.tabs',

			// Account
			'#customer_login form.login, #customer_login form.register, p.myaccount_user',

			// Widgets
			'.woocommerce ul.product_list_widget li:first-child',
			'.woocommerce .widget_shopping_cart .cart_list li:first-child',
			'.woocommerce.widget_shopping_cart .cart_list li:first-child',
			'.woocommerce ul.product_list_widget li',
			'.woocommerce .widget_shopping_cart .cart_list li',
			'.woocommerce.widget_shopping_cart .cart_list li',

			// Cart dropdown
			'#current-shop-items-dropdown p.total',

			// Checkout
			'.woocommerce form.login',
			'.woocommerce form.register',
			'.woocommerce-checkout #payment',
			'#add_payment_method #payment ul.payment_methods',
			'.woocommerce-cart #payment ul.payment_methods',
			'.woocommerce-checkout #payment ul.payment_methods',

		), $elements );
	}

}
new AccentColors;