<?php
/**
 * bbPress Configuration Class
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.8
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BuddyPress {

	/**
	 * Start things up
	 *
	 * @access public
	 * @since  4.0
	 */
	public function __construct() {
		define( 'WPEX_BUDDYPRESS_DIR', WPEX_FRAMEWORK_DIR . '3rd-party/buddypress/' );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );
		add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ), 11 ); // on 11 due to bbPress issues

	}

	/**
	 * Load custom CSS
	 *
	 * @since  4.0
	 */
	public function scripts() {
		wp_enqueue_style(
			'wpex-buddypress',
			wpex_asset_url( 'css/wpex-buddypress.css' ),
			array(),
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Set layouts
	 *
	 * @version 4.5
	 */
	public function layouts( $layout ) {
		if ( is_buddypress() ) {
			//$layout = wpex_get_mod( 'bp_layout', 'left-sidebar' );
			if ( bp_is_user() ) {
				$layout = wpex_get_mod( 'bp_user_layout', wpex_get_default_content_area_layout() );
			}
		}
		return $layout;
	}

}
new BuddyPress();