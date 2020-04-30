<?php
/**
 * Video Icon Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
} ?>

<div class="overlay-icon overlay-icon-video"><span>&#9658;</span></div>