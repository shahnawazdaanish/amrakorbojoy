<?php
/**
 * LayerSlider Config
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.6.5
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LayerSlider {

	/**
	 * Start things up
	 *
	 * @since 4.6.5
	 */
	public function __construct() {

		// Enqueue CSS
		if ( apply_filters( 'wpex_layer_slider_css', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'css' ) );
		}

		// Remove purchase notice on plugins page
		add_action( 'admin_init', array( $this, 'remove_notices' ), PHP_INT_MAX );

	}

	/**
	 * Enqueue layerslider theme CSS
	 *
	 * @since 4.6.5
	 */
	public function css() {

		wp_enqueue_style(
			'wpex-layerslider',
			wpex_asset_url( 'css/wpex-layerslider.css' ),
			array(),
			WPEX_THEME_VERSION
		);

	}

	/**
	 * Remove notices
	 *
	 * @since 4.6.5
	 */
	public function remove_notices() {

		if ( defined( 'LS_PLUGIN_BASE' ) && ! get_option( 'layerslider-authorized-site', null ) ) {
			remove_action( 'after_plugin_row_' . LS_PLUGIN_BASE, 'layerslider_plugins_purchase_notice', 10, 3 );
		}

	}

}
new LayerSlider;