<?php
/**
 * Page Media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check for video
if ( $video = wpex_get_post_video() ) : ?>

	<div id="post-media" class="wpex-clr"><?php wpex_post_video_html( $video ); ?></div>

<?php
// Audio
elseif ( $audio = wpex_get_post_audio() ) : ?>

	<div id="post-media" class="wpex-clr"><?php wpex_post_audio_html( $audio ); ?></div>

<?php
// Thumbnail
else : ?>

	<?php if ( has_post_thumbnail() ) {

		wpex_post_thumbnail( apply_filters( 'wpex_page_single_thumbnail_args', array(
			'before'        => '<div id="page-featured-img" class="wpex-clr">',
			'after'         => '</div>',
			'size'          => 'full',
			'schema_markup' => true
		) ) );

	} ?>

<?php endif; ?>