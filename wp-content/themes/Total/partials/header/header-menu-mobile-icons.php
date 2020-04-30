<?php
/**
 * Mobile Icons Header Menu
 *
 * Note: By default this file only loads if wpex_header_has_mobile_menu returns true.
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="mobile-menu" class="<?php echo wpex_header_mobile_menu_classes(); ?>"><?php

	// Output user-defined mobile icons
	echo wpex_get_mobile_menu_extra_icons();

	// Output main toggle icon
	echo wpex_get_mobile_menu_toggle_icon();

?></div>