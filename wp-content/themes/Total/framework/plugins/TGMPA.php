<?php
/**
 * Recommends plugins for use with the theme via the TGMA Script
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TGMPA {

	/**
	 * Main constructor
	 *
	 * @version 4.6.5
	 */
	public function __construct() {
		require_once WPEX_FRAMEWORK_DIR . 'lib/tgmpa/class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', array( $this, 'register' ) );
	}

	/**
	 * Remove Revolution Slider plugin notices
	 *
	 * @since 4.6.5
	 */
	function register() {

		// Get array of recommended plugins
		// See framework/core-functions.php
		$plugins = wpex_recommended_plugins();

		// Dismissable is true by default (lets users dismiss the notice completely)
		$dismissable = true;

		// Prevent dismiss for Visual Composer
		// And remove VC plugin from recommended list if it has a valid license
		// active on the site to prevent update issues between TGMPA and VC plugin
		if ( WPEX_VC_ACTIVE ) {
			if ( wpex_vc_theme_mode_check() ) {
				$dismissable = wpex_vc_is_supported() ? true : false;
			} else {
				unset( $plugins['js_composer'] );
			}
		}

		// Register notice
		tgmpa( $plugins, array(
			'id'           => 'wpex_theme',
			'domain'       => 'total',
			'menu'         => 'install-required-plugins',
			'has_notices'  => true,
			'is_automatic' => true,
			'dismissable'  => $dismissable,
		) );

	}

}
new TGMPA();