<?php
/**
 * Single Custom Post Type Media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current post data
$post_id   = get_the_ID();
$post_type = get_post_type();

// Array of supported media
$supported_media = apply_filters( 'wpex_cpt_single_supported_media', array(
	'video',
	'audio',
	'gallery',
	'thumbnail',
), $post_type );

// Video
if ( in_array( 'video', $supported_media ) && $video = wpex_get_post_video( $post_id ) ) : ?>

	<div id="post-media" class="clr"><?php wpex_post_video_html( $video ); ?></div>

<?php
// Audio
elseif ( in_array( 'audio', $supported_media ) && $audio = wpex_get_post_audio( $post_id ) ) : ?>

	<div id="post-media" class="clr"><?php wpex_post_audio_html( $audio ); ?></div>

<?php
// Gallery images
elseif ( in_array( 'gallery', $supported_media ) && wpex_post_has_gallery( $post_id ) ) :

	get_template_part( 'partials/cpt/cpt-single-gallery' );

// Thumbnail
elseif ( in_array( 'thumbnail', $supported_media ) ) :

	wpex_post_thumbnail( apply_filters( 'wpex_' . $post_type . '_single_thumbnail_args', array(
		'before'        => '<div id="post-media" class="clr">',
		'after'         => '</div>',
		'size'          => $post_type . '_single',
		'alt'           => wpex_get_esc_title(),
		'schema_markup' => true
	), $post_type ) ); ?>

<?php endif; ?>