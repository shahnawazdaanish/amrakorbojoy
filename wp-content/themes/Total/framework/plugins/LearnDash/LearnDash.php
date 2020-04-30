<?php
/**
 * LearnDash Config
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9.7
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class LearnDash {

	/**
	 * Start things up.
	 */
	public function __construct() {
		add_filter( 'wpex_customizer_panels', array( $this, 'customizer_settings' ) );
		add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ) );
		add_filter( 'wpex_main_metaboxes_post_types', array( $this, 'page_settings_meta' ) );
		add_filter( 'wpex_has_breadcrumbs', array( $this, 'wpex_has_breadcrumbs' ) );
	}

	/**
	 * Adds Customizer settings for LearnDash.
	 */
	public function customizer_settings( $panels ) {
		$panels['learndash'] = array(
			'title'      => esc_html__( 'LearnDash', 'total' ) . ' (' . WPEX_THEME_BRANDING . ')',
			'settings'   => WPEX_FRAMEWORK_DIR . 'plugins/LearnDash/customizer.php'
		);
		return $panels;
	}

	/**
	 * Alter default layout.
	 */
	public function layouts( $layout ) {
		$types = $this->get_learndash_types();

		foreach( $types as $type ) {


			// Archives
			if ( is_post_type_archive( $type ) ) {
				return wpex_get_mod( $type . '_archives_layout', wpex_get_mod( 'learndash_layout' ) );
			}

			// Single posts
			if ( is_singular( $type ) ) {
				return wpex_get_mod( $type . '_single_layout', wpex_get_mod( 'learndash_layout' ) );
			}


		}

		// Return layout
		return $layout;

	}

	/**
	 * Add LearnDash post types to array of post types to use with Total page settings metabox.
	 */
	public function page_settings_meta( $types ) {
		if ( wpex_get_mod( 'learndash_wpex_metabox', true ) ) {
			$types = array_merge( $types, $this->get_learndash_types() );
		}
		return $types;
	}

	/**
	 * Disable breadcrumbs for LearnDash.
	 */
	public function wpex_has_breadcrumbs( $bool ) {

		$types = $this->get_learndash_types();

		foreach( $types as $type ) {

			if ( is_post_type_archive( $type ) || is_singular( $type ) ) {
				$bool = wpex_get_mod( 'learndash_breadcrumbs', true );
			}

		}

		return $bool;
	}

	/**
	 * Return array of learndash post types.
	 */
	public function get_learndash_types() {
		if ( function_exists( 'learndash_get_post_types' ) ) {
			return learndash_get_post_types();
		}
		return array(
			'sfwd-courses',
			'sfwd-lessons',
			'sfwd-topic',
			'sfwd-quiz',
			'sfwd-question',
			'sfwd-certificates',
			'sfwd-assignment',
			'sfwd-groups',
		);
	}

}
new LearnDash();