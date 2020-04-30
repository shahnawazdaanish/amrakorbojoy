<?php
/**
 * Visual Composer Templatera tweaks
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_templatera_remove_notices() {
	remove_action( 'admin_notices', 'templatera_notice' );
}
add_action( 'init', 'wpex_templatera_remove_notices' );
