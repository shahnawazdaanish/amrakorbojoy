<?php
/**
 * Elementor Configuration Class
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.9.5
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elmentor_Config {

	/**
	 * Main constructor
	 *
	 * @version 4.9.5
	 */
	public function __construct() {
		add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
	}

	/**
	 * Loads Gravity Forms stylesheet
	 *
	 * @since 4.9.5
	 */
	public function register_locations( $elementor_theme_manager ) {

		if ( apply_filters( 'total_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}

		$elementor_theme_manager->register_location( 'togglebar', array(
			'label'           => __( 'Togglebar', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		) );

		$elementor_theme_manager->register_location( 'topbar', array(
			'label'           => __( 'Top Bar', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		) );

		$elementor_theme_manager->register_location( 'footer_callout', array(
			'label'           => __( 'Footer Callout', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		) );

		$elementor_theme_manager->register_location( 'footer_bottom', array(
			'label'           => __( 'Footer Bottom', 'total' ),
			'multiple'        => true,
			'edit_in_content' => false,
		) );

	}

}

new Elmentor_Config();