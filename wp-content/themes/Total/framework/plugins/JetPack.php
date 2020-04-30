<?php
/**
 * JetPack Configuration Class
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9
 */

namespace TotalTheme\Vendor;

use Jetpack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JetPackConfig extends Jetpack {

	/**
	 * Start things up
	 *
	 * @version 3.3.5
	 */
	public function __construct() {

		// Social share
		if ( $this->is_module_active( 'sharedaddy' ) ) {

			// Remove default filters
			add_action( 'loop_start', array( $this, 'remove_share' ) );

			// Social share should always be enabled & disabled via blocks/theme filter
			add_filter( 'sharing_show', '__return_true' );

			// Enqueue scripts if social share is enabled
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			// Replace social share
			add_filter( 'wpex_custom_social_share', array( $this, 'alter_share' ) );

			// Remove Customizer settings
			add_filter( 'wpex_customizer_sections', array( $this, 'remove_customizer_settings' ), 40 );

		}

		// Carousel
		if ( $this->is_module_active( 'carousel' ) || $this->is_module_active( 'tiled-gallery' ) ) {
			add_filter( 'wpex_custom_wp_gallery', '__return_false' );
		}

	}

	/**
	 * Removes jetpack default loop filters
	 *
	 * @version 3.3.5
	 */
	public function remove_share() {
		/* Used to move JetPack sharing */ remove_filter( 'the_content', 'sharing_display', 19 );
		/* Used to move JetPack sharing */ remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}

	/**
	 * Enqueue scripts if social share is enabled
	 *
	 * @version 3.3.5
	 */
	public function load_scripts() {
		if ( wpex_has_social_share() ) {
			add_filter( 'sharing_enqueue_scripts', '__return_true' );
		}
	}

	/**
	 * Replace Total social share with sharedaddy
	 *
	 * @version 3.3.5
	 */
	public function alter_share() {
		if ( function_exists( 'sharing_display' ) ) {
			$text = '';
			$echo = false;
			$classes = 'wpex-social-share position-horizontal clr';
			if ( 'full-screen' == wpex_content_area_layout() ) {
				$classes .= ' container';
			}
			return '<div class="' . esc_attr( $classes ) . '">' . sharing_display( $text, $echo ) . '</div>';
		}
	}

	/**
	 * Remove Customizer settings
	 *
	 * @version 3.3.5
	 */
	public function remove_customizer_settings( $array ) {
		unset( $array['wpex_social_sharing'] );
		return $array;
	}

}
new JetPackConfig();