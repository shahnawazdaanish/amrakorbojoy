<?php
/**
 * Helper Functions
 *
 * @package Total Theme Core
 * @subpackage inc
 * @version 1.0
 */

namespace TotalThemeCore;

/**
 * Check if a specific theme mod is enabled.
 *
 * @since 1.0
 */
function is_mod_enabled( $mod ) {
	return ( $mod && 'off' !== $mod ) ? true : false;
}

/**
 * Sanitize data through Total theme Sanitization class.
 *
 * @since 1.0
 */
function sanitize_data( $data = '', $type = '' ) {
	if ( function_exists( 'wpex_sanitize_data' ) ) {
		return wpex_sanitize_data( $data, $type );
	}
	return wp_strip_all_tags( $data );
}
