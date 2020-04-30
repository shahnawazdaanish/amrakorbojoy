<?php
/**
 * Massive Addons Tweaks
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.6.5
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MassiveAddons {

	/**
	 * Main constructor
	 *
	 * @version 4.6.5
	 */
	public function __construct() {

		// Disable Total advanced parallax since it conflicts with Massive Addons
		add_filter( 'vcex_supports_advanced_parallax', '__return_false' );

	}

}
new MassiveAddons();