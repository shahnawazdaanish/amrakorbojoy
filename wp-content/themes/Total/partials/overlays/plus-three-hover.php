<?php
/**
 * Plus Three Hover Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
} ?>

<div class="overlay-plus-three-hover overlay-hide theme-overlay wpex-accent-color"><span class="ticon ticon-plus-circle"></span></div>