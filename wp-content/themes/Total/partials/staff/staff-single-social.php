<?php
/**
 * Displays social links on singular staff posts
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo wpex_get_staff_social( apply_filters( 'wpex_staff_single_social_settings', array(
	'before' => '<div id="staff-single-social">',
	'after'  => '</div>',
) ) );