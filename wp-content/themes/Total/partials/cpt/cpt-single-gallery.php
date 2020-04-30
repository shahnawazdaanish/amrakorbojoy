<?php
/**
 * Used to display the custom post type slider
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();

$type = get_post_type( $post_id );

$args = array(
	'before'         => '<div id="post-media" class="wpex-clr"><div class="gallery-format-post-slider wpex-clr">',
	'after'          => '</div></div>',
	'lightbox'       => wpex_gallery_is_lightbox_enabled( $post_id ) ? true : false,
	'lightbox_title' => apply_filters( 'wpex_cpt_gallery_lightbox_title', false ),
	'thumbnails'     => apply_filters( 'wpex_' . $type . '_gallery_slider_has_thumbnails', true ),
	'thumbnail_args' => array(
		'size'          => $type . '_single',
		'apply_filters' => 'wpex_' . $type . '_single_thumbnail_args',
	),
);

echo wpex_get_post_media_gallery_slider( $post_id, $args );