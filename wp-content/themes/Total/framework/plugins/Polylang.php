<?php
/**
 * Polylang Functions
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Polylang {

	/**
	 * Start things up
	 *
	 * @since 4.6.5
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_strings' ) );
		add_filter( 'wpex_shortcodes_tinymce_json', array( $this, 'tinymce_shortcode' ) );
		add_filter( 'pll_get_post_types', array( $this, 'post_types' ) );
	}

	/**
	 * Registers theme_mod strings into Polylang
	 *
	 * @since 4.6.5
	 */
	public function register_strings() {
		if ( function_exists( 'pll_register_string' ) ) {
			$strings = wpex_register_theme_mod_strings();
			if ( $strings ) {
				foreach( $strings as $string => $default ) {
					pll_register_string( $string, get_theme_mod( $string, $default ), 'Theme Settings', true );
				}
			}
		}
	}

	/**
	 * Add shortcodes to the tiny MCE
	 *
	 * @since 4.6.5
	 */
	public function tinymce_shortcode( $data ) {
		if ( shortcode_exists( 'polylang_switcher' ) ) {
			$data['shortcodes']['polylang_switcher'] = array(
				'text' => esc_html__( 'PolyLang Switcher', 'total' ),
				'insert' => '[polylang_switcher dropdown="false" show_flags="true" show_names="false"]',
			);
		}
		return $data;
	}

	/**
	 * Post Types
	 *
	 * @since 4.6.5
	 */
	public function post_types( $types ) {
		if ( WPEX_TEMPLATERA_ACTIVE ) {
			$types['templatera'] = 'templatera';
		}
		return $types;
	}

}
new Polylang();