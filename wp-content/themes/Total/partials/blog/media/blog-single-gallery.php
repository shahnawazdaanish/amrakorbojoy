<?php
/**
 * Blog single post gallery format media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get attachments
$attachments = wpex_get_gallery_ids( get_the_ID() );

// Return standard entry style if password protected or there aren't any attachments
if ( post_password_required() || empty( $attachments ) ) {
	get_template_part( 'partials/blog/media/blog-entry' );
	return;
}

if ( wpex_gallery_is_lightbox_enabled() || wpex_get_mod( 'blog_post_image_lightbox' ) ) {
	$lightbox_enabled = true;
} else {
	$lightbox_enabled = false;
}

$args = array(
	'before'         => '<div id="post-media" class="clr">',
	'after'          => '</div>',
	'lightbox'       => $lightbox_enabled,
	'lightbox_title' => apply_filters( 'wpex_blog_gallery_lightbox_title', false ),
	'slider_data'    => wpex_get_post_slider_settings( array(
		'filter_tag' => 'wpex_blog_slider_data_atrributes',
	) ),
	'thumbnail_args' => wpex_get_blog_post_thumbnail_args(),
);

echo wpex_get_post_media_gallery_slider( get_the_ID(), $args );