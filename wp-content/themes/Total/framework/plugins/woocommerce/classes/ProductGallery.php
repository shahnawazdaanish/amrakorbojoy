<?php
/**
 * Theme tweaks for WooCommerce images
 *
 * @package Total WordPress Theme
 * @subpackage WooCommerce
 * @version 4.9.3
 *
 */

namespace TotalTheme\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProductGallery {

	/**
	 * Main Class Constructor
	 *
	 * @since 4.1
	 */
	public function __construct() {

		// Enable product gallery
		if ( wpex_get_mod( 'woo_product_gallery_slider', true ) ) {
			add_theme_support( 'wc-product-gallery-slider' );
		}

		// Enable product gallery zoom support
		if ( wpex_get_mod( 'woo_product_gallery_zoom', true ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}

		// Enqueue lightbox scripts
		if ( 'total' == wpex_get_mod( 'woo_product_gallery_lightbox', 'total' ) ) {
			add_action( 'wp_footer', array( $this, 'lightbox_scripts' ) );
		} elseif ( 'woo' == wpex_get_mod( 'woo_product_gallery_lightbox', 'total' ) ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}

		// Custom product gallery flexslider options
		add_filter( 'woocommerce_single_product_carousel_options', array( $this, 'flexslider_options' ) );

		// Gallery columns
		add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'columns' ) );

		// Custom gallery CSS
		add_filter( 'wpex_head_css', array( $this, 'custom_css' ) );

	}

	/**
	 * Add Scripts
	 *
	 * @since 4.1
	 */
	public function lightbox_scripts() {
		if ( ! is_product() ) {
			return;
		}

		wpex_enqueue_lightbox_scripts();

		$file = 'js/dynamic/woocommerce/wpex-lightbox-gallery.js';

		if ( wpex_get_mod( 'minify_js_enable', true ) ) {
			$file = 'js/dynamic/woocommerce/wpex-lightbox-gallery.min.js';
		}

		if ( class_exists( 'WC_Additional_Variation_Images' )
			&& apply_filters( 'wpex_woo_additional_variation_images_custom_lightbox', true )
		) {
			$file = 'js/dynamic/woocommerce/wpex-lightbox-additional-variation-images.min.js'; // this plugin changes the gallery HTML :(
		}

		wp_enqueue_script(
			'wpex-wc-product-lightbox',
			wpex_asset_url( $file ),
			array( 'jquery', WPEX_THEME_JS_HANDLE ),
			WPEX_THEME_VERSION,
			true
		);


	}

	/**
	 * Custom product gallery flexslider options
	 *
	 * Not used at the moment due to WooCommerce bugs
	 *
	 * @since 4.1
	 */
	public function flexslider_options( $options ) {
		$options['directionNav'] = true; // Not sure if I like it
		$speed = wpex_get_mod( 'woo_product_gallery_slider_animation_speed', '600' );
		$options['animationSpeed'] = intval( $speed );
		return $options;
	}

	/**
	 * Define columns for gallery
	 *
	 * @since 4.3
	 */
	public function columns() {
		$cols = absint( wpex_get_mod( 'woocommerce_gallery_thumbnails_count' ) );
		$cols = $cols ? $cols : 5;
		return $cols;
	}

	/**
	 * Custom CSS for gallery
	 *
	 * @since 4.1
	 */
	public function custom_css( $css ) {
		if ( is_singular( 'product' ) ) {
			$thumb_cols = self::columns();
			if ( $thumb_cols && 5 !== $thumb_cols ) {
				$css .= '.woocommerce div.product div.images .flex-control-thumbs li:nth-child(4n+1) {clear: none;}';
				$css .= '.product-variation-thumbs a, .woocommerce div.product div.images .flex-control-thumbs li { width:' . 100 / $thumb_cols . '%;}';
				$css .= '.woocommerce div.product div.images .flex-control-thumbs li:nth-child(' . $thumb_cols . 'n+1) {clear: both;}';
			}
		}
		return $css;
	}

}
new ProductGallery;