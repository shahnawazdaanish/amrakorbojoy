<?php
/**
 * Blog entry gallery format media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! wpex_blog_entry_slider_enabled() ) {
	get_template_part( 'partials/blog/media/blog-entry' );
	return;
}

$entry_style = wpex_blog_entry_style();

if ( wpex_get_mod( 'blog_entry_image_lightbox' ) || wpex_gallery_is_lightbox_enabled() ) {
	$lightbox_enabled = true;
} else {
	$lightbox_enabled = false;
}

$slider_data = wpex_get_post_slider_settings( array(
	'filter_tag' => 'wpex_blog_slider_data_atrributes',
) );

if ( 'grid-entry-style' === $entry_style ) {
	$slider_data['auto-height'] = 'false';
}

$args = array(
	'before'         => '<div class="blog-entry-media entry-media wpex-clr">',
	'after'          => '</div>',
	'lightbox'       => $lightbox_enabled,
	'lightbox_title' => apply_filters( 'wpex_blog_gallery_lightbox_title', false ),
	'slider_data'    => $slider_data,
	'thumbnail_args' => wpex_get_blog_entry_thumbnail_args(),
);

if ( 'large-image-entry-style' !== $entry_style ) {
	$args['thumbnails'] = false;
}

echo wpex_get_post_media_gallery_slider( get_the_ID(), $args );
