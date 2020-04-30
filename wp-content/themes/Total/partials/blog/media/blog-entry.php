<?php
/**
 * Blog entry standard format media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Return if no thumbnail is defined
if ( ! has_post_thumbnail() ) {
	return;
}

$classes = 'blog-entry-media entry-media wpex-clr';

$overlay = wpex_get_mod( 'blog_entry_overlay' );
$overlay = $overlay ? $overlay : 'none';

if ( $overlay_classes = wpex_overlay_classes( $overlay ) ) {
	$classes .= ' ' . $overlay_classes;
}

if ( $animation_classes = wpex_get_entry_image_animation_classes() ) {
	$classes .= ' ' . $animation_classes;
} ?>

<div class="<?php echo esc_attr( $classes ); ?>">

	<?php
	// Lightbox style entry
	if ( wpex_get_mod( 'blog_entry_image_lightbox' ) ) :

		wpex_enqueue_lightbox_scripts();

		// Get lightbox image
		$lightbox_image = wpex_get_lightbox_image( get_post_thumbnail_id() ) ?>

		<a href="<?php echo esc_url( $lightbox_image ); ?>" title="<?php wpex_esc_title(); ?>" rel="bookmark" class="blog-entry-media-link wpex-lightbox">
			<?php echo wpex_get_blog_entry_thumbnail(); ?>
			<?php wpex_overlay( 'inside_link', $overlay, array(
				'lightbox_link' => $lightbox_image,
			) ); ?>
			<?php wpex_entry_media_after( 'blog' ); ?>
		</a><!-- .blog-entry-media-link -->

		<?php wpex_overlay( 'outside_link', $overlay, array(
			'lightbox_link' => $lightbox_image,
		) ); ?>

	<?php
	// Standard link to post
	else : ?>

		<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" rel="bookmark" class="blog-entry-media-link">
			<?php echo wpex_get_blog_entry_thumbnail(); ?>
			<?php wpex_entry_media_after( 'blog' ); ?>
			<?php wpex_overlay( 'inside_link', $overlay ); ?>
		</a><!-- .blog-entry-media-link -->

		<?php wpex_overlay( 'outside_link', $overlay ); ?>

	<?php endif; ?>

</div><!-- .blog-entry-media -->
