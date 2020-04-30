<?php
/**
 * Blog single post link format media
 * Link formats should redirect to the URL defined in the custom fields
 * This template file is used as a fallback only.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Return if there isn't a thumbnail
if ( ! has_post_thumbnail() ) {
    return;
} ?>

<div id="post-media" class="clr">

	<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="<?php wpex_img_animation_classes(); ?>"><?php echo wpex_get_blog_post_thumbnail(); ?></a>

	<?php
	// Blog entry caption
	if ( wpex_get_mod( 'blog_thumbnail_caption' ) && $caption = wpex_featured_image_caption() ) : ?>

		<div class="post-media-caption clr"><?php echo wp_kses_post( $caption ); ?></div>

	<?php endif; ?>

</div><!-- #post-media -->