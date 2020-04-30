<?php
/**
 * Alter wp list categories arguments.
 * Adds a span around the counter for easier styling.
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpex_wp_list_categories_args' ) ) :
	function wpex_wp_list_categories_args( $links ) {
		if ( false === strpos( $links, 'span' ) ) {
			$links = str_replace( '</a> (', '</a> <span class="cat-count-span">(', $links );
			$links = str_replace( ')', ')</span>', $links );
		}
		return $links;
	}
	add_filter( 'wp_list_categories', 'wpex_wp_list_categories_args' );
endif;