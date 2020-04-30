<?php
/**
 * Gets and stores all theme mods for use with the theme.
 *
 * IMPORTANT: DO NOT EVER EDIT THESE CORE FUNCTIONS !!!
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.8.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define global $wpex_theme_mods
global $wpex_theme_mods;

// Gets all theme mods and stores them in the global $wpex_theme_mods variable to limit DB requests & filter checks
$wpex_theme_mods = get_theme_mods();

/**
 * Returns global mods
 *
 * @since 2.1.0
 */
function wpex_get_mods() {
	global $wpex_theme_mods;
	return $wpex_theme_mods;
}

/**
 * Returns theme mod from global var
 *
 * @since 2.1.0
 */
function wpex_get_mod( $id, $default = '', $not_empty = false ) {

	// Return get_theme_mod on customize_preview => IMPORTANT !!!
	if ( is_customize_preview() ) {
		$value = get_theme_mod( $id, $default );
		$value = ( $not_empty && ! $value ) ? $default : $value;
		return $value;
	}

	// Get global array of all theme mods
	global $wpex_theme_mods;

	// Return data from global array
	if ( ! empty( $wpex_theme_mods ) ) {

		// Check if mod is in array
		if ( isset( $wpex_theme_mods[$id] ) ) {
			$value = $wpex_theme_mods[$id];
		}

		// Set value to default if mod not in array
		else {
			$value = $default;
		}

		// Check if value can be empty if not set value to default
		$value = ( $not_empty && ! $value ) ? $default : $value;

		// Return value
		return $value;

	}

	// Global arary not found return using get_theme_mod
	else {

		$value = get_theme_mod( $id, $default );
		$value = ( $not_empty && ! $value ) ? $default : $value;
		return $value;

	}

}

/**
 * Check if a specific theme mod is disabled (for fallback conditionals)
 *
 * @since 3.3.3
 */
function wpex_is_mod_enabled( $mod ) {
	return ( $mod && 'off' !== $mod ) ? true : false;
}

/**
 * Creates a backup of your theme mods
 *
 * @since 3.0.0
 */
function wpex_backup_mods() {
	update_option( 'wpex_total_customizer_backup', wpex_get_mods(), false );
}