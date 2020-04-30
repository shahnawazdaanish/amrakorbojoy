<?php
/**
 * Portfolio entry content template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get loop
$wpex_loop = isset( $wpex_loop ) ? $wpex_loop : 'archive';

// Get video and thumbnail
$get_video     = wpex_get_post_video();
$has_thumbnail = has_post_thumbnail();

// Classes
$classes = array( 'portfolio-entry-media', 'clr' );
if ( $overlay = wpex_overlay_classes() ) {
	$classes[] = $overlay;
}
$classes = implode( ' ', $classes );

// Return if there isn't a video or a thumbnail
if ( ! $get_video && ! $has_thumbnail ) {
	return;
} ?>

<div class="<?php echo esc_attr( $classes ); ?>"><?php

	// If the portfolio post has a video display it
	if ( $get_video ) :

		echo wpex_get_portfolio_post_video( $get_video );

	// Otherwise display thumbnail if one exists
	elseif ( $has_thumbnail ) : ?>
		<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link<?php wpex_entry_image_animation_classes(); ?>">
			<?php echo wpex_get_portfolio_entry_thumbnail( $wpex_loop ); ?>
			<?php wpex_entry_media_after( 'portfolio' ); ?>
			<?php wpex_overlay( 'inside_link' ); ?>
		</a>
		<?php wpex_overlay( 'outside_link' ); ?>
	<?php endif;

?></div><!-- .portfolio-entry-media -->