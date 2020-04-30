<?php
/**
 * Contat Form 7 Configuration
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.6.5
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactForm7 {

	/**
	 * Start things up
	 *
	 * @version 4.6.5
	 */
	public function __construct() {

		if ( ! defined( 'CF7MSM_PLUGIN' )
			&& function_exists( 'wpcf7_enqueue_scripts' )
			&& apply_filters( 'wpex_conditional_wpcf7_scripts', true )
		) {

			// Remove CSS Completely - theme adds styles
			add_filter( 'wpcf7_load_css', '__return_false' );

			// Remove JS
			add_filter( 'wpcf7_load_js', '__return_false' );

			// Conditionally load JS
			add_action( 'wpcf7_contact_form', array( $this, 'enqueue_scripts' ), 1 );

		}

	}

	/**
	 * Load JS conditionally
	 *
	 * @version 4.6.5
	 */
	public function enqueue_scripts() {
		wpcf7_enqueue_scripts();
		wpcf7_enqueue_styles();
	}

}
new ContactForm7();