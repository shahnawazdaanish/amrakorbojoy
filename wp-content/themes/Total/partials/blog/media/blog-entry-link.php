<?php
/**
 * Blog entry link format media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Display media if thumbnail exists
if ( has_post_thumbnail() ) : ?>
	<div class="blog-entry-media entry-media clr<?php wpex_entry_image_animation_classes(); ?>">
		<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" rel="bookmark" class="blog-entry-media-link"><?php echo wpex_get_blog_entry_thumbnail(); ?></a>
	</div>
<?php endif; ?>