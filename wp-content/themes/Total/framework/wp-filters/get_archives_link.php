<?php
/**
 * Filters the get_archives_link to add span around the "after" content for styling purposes.
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpex_get_archives_link' ) ) :
	function wpex_get_archives_link( $link_html ) {
		if ( false === strpos( $link_html, 'span' ) ) {
			$link_html = str_replace( '&nbsp;(', '&nbsp;<span class="get_archives_link-span">(', $link_html );
			$link_html = str_replace( ')', ')</span>', $link_html );
		}
		return $link_html;
	}
	add_filter( 'get_archives_link', 'wpex_get_archives_link', PHP_INT_MAX );
endif;