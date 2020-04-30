<?php
/**
 * Used to insert content to the top/bottom of the mobile menu.
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wpex-mobile-menu-top wpex-hidden"><?php do_action( 'wpex_mobile_menu_top' ); ?></div>
<div class="wpex-mobile-menu-bottom wpex-hidden"><?php do_action( 'wpex_mobile_menu_bottom' ); ?></div>