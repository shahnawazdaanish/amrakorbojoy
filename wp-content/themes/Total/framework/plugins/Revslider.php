<?php
/**
 * RevSlider Config
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.6.5
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class RevSlider {
	public $valid;

	/**
	 * Start things up
	 *
	 * @since 4.6.5
	 */
	public function __construct() {

		$this->valid = get_option( 'revslider-valid', 'false' );

		// Admin functions
		if ( is_admin() ) {

			// Remove things when license isn't valid
			if ( 'false' == $this->valid ) {

				// Remove notices
				global $pagenow;
				if ( $pagenow == 'plugins.php' && 'false' == $this->valid ) {
					add_action( 'admin_notices', array( $this, 'remove_plugins_page_notices' ), 9999 );
				}

				// Remove update notice
				if ( 'admin.php' == $pagenow && 'false' == $this->valid ) {
					wpex_remove_class_filter( 'admin_notices', 'RevSliderAdmin', 'add_notices', 10 );
				}

				// Remove addons page
				add_action( 'admin_menu', array( $this, 'remove_addons_page' ), 999 );

			}

			// Remove activation notice
			wpex_remove_class_filter( 'admin_notices', 'RevSliderAdmin', 'addActivateNotification', 10 );

			// Remove metabox from VC grid builder
			add_action( 'do_meta_boxes', array( $this, 'remove_metabox' ) );

		}

		// Front end functions
		else {

			// Remove front-end meta generator
			add_filter( 'revslider_meta_generator', '__return_false' );

			// Remove duplicate font awesome script since it's loaded in Total by default
			// Prevents icons from showing :(
			//add_action( 'wp_footer', array( $this, 'remove_font_awesome' ), 5 );

		}

	}

	/**
	 * Remove Revolution Slider plugin notices
	 *
	 * @since 4.6.5
	 */
	public function remove_plugins_page_notices() {
		$plugin_id = 'revslider/revslider.php';

		// Remove plugin page purchase notice
		remove_action( 'after_plugin_row_'. $plugin_id, array( 'RevSliderAdmin', 'show_purchase_notice' ), 10, 3 );

		// Hide update notice if not valid
		remove_action( 'after_plugin_row_' . $plugin_id, array( 'RevSliderAdmin', 'show_update_notice' ), 10, 3 );

	}

	/**
	 * Remove metabox from VC grid builder
	 *
	 * @since 4.6.5
	 */
	public function remove_metabox() {
		remove_meta_box(
			'mymetabox_revslider_0',
			array(
				'vc_grid_item',
				'templatera',
				'wpex_sidebars',
				'ptu',
				'ptu_tax'
			),
			'normal'
		);
	}

	/**
	 * Remove Addons Page
	 *
	 * @since 4.6.5
	 */
	public function remove_addons_page() {
		remove_submenu_page( 'revslider', 'rev_addon' );
	}

	/**
	 * Remove duplicate font awesome script
	 *
	 * @since 4.6.5
	 */
	public function remove_font_awesome() {
		global $fa_icon_var;
		$fa_icon_var = null;
	}

}
new RevSlider();