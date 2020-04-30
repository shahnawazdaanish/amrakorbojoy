<?php
/**
 * Used to display the custom post type entry gallery slider
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 *
 * @todo update to use new wpex_get_post_media_gallery_slider() function
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type = get_post_type();

if ( apply_filters( 'wpex_' . $type . '_entry_media_lightbox', true ) && wpex_gallery_is_lightbox_enabled() ) {
	$lightbox_enabled = true;
} else {
	$lightbox_enabled = false;
}

$args = array(
	'lightbox'       => $lightbox_enabled,
	'lightbox_title' => apply_filters( 'wpex_cpt_gallery_lightbox_title', false ),
	'slider_data'    => array(
		'filter_tag' => 'wpex_' . $type . '_entry_gallery',
	),
	'thumbnail_args' => array(
		'size'          => $type . '_archive',
		'apply_filters' => 'wpex_' . $type . '_entry_thumbnail_args',
	),
);

echo wpex_get_post_media_gallery_slider( get_the_ID(), $args );