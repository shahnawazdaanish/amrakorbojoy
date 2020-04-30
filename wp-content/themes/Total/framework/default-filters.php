<?php
/**
 * Default Theme filters
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter output of custom wpex_the_content function.
 */
if ( function_exists( 'do_blocks' ) ) {
	add_filter( 'wpex_the_content', 'do_blocks', 9 );
}
add_filter( 'wpex_the_content', 'wptexturize' );
add_filter( 'wpex_the_content', 'convert_chars' );
add_filter( 'wpex_the_content', 'wpautop' );
add_filter( 'wpex_the_content', 'shortcode_unautop' );
add_filter( 'wpex_the_content', 'wpex_clean_up_shortcodes' );
add_filter( 'wpex_the_content', 'wp_make_content_images_responsive' );
add_filter( 'wpex_the_content', 'do_shortcode', 11 );
add_filter( 'wpex_the_content', 'convert_smilies', 20 );