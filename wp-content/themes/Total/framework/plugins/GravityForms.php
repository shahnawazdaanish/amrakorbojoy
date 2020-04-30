<?php
/**
 * Gravity Forms Configuration Class
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9.6.1
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GravityForms {

	/**
	 * Main constructor
	 *
	 * @version 4.6.5
	 */
	public function __construct() {

		if ( ! is_admin() && apply_filters( 'wpex_gravity_forms_css', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'gravity_forms_css' ), 40 );
		}

	}

	/**
	 * Loads Gravity Forms stylesheet
	 *
	 * @since 4.6.5
	 */
	public function gravity_forms_css() {

		global $post;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'gravityform' ) ) {

			wp_enqueue_style(
				'wpex-gravity-forms',
				wpex_asset_url( 'css/wpex-gravity-forms.css' ),
				array(),
				WPEX_THEME_VERSION
			);

		}

	}

}

new GravityForms();