<?php
/**
 * Blog entry audio format media
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Display media
if ( apply_filters( 'wpex_blog_entry_audio_embed', wpex_get_mod( 'blog_entry_audio_output', false ) )
	&& ! post_password_required()
	&& $audio = wpex_get_post_audio()
) : ?>

	<div class="blog-entry-media entry-media wpex-clr"><?php echo wpex_get_post_audio_html( $audio ); ?></div>

<?php
// Display media if thumbnail exists
elseif ( has_post_thumbnail() ) :

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
		<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" rel="bookmark" class="blog-entry-media-link">
			<?php echo wpex_get_blog_entry_thumbnail(); ?>
			<?php if ( $overlay && 'none' != $overlay ) { ?>
				<?php wpex_overlay( 'inside_link', $overlay ); ?>
			<?php } elseif ( ! wpex_get_mod( 'thumbnail_format_icons' ) ) { ?>
				<div class="blog-entry-music-icon-overlay"><span class="ticon ticon-music"></span></div>
			<?php } ?>
			<?php wpex_entry_media_after( 'blog' ); ?>
		</a>
		<?php wpex_overlay( 'outside_link', $overlay ); ?>
	</div>

<?php endif; ?>