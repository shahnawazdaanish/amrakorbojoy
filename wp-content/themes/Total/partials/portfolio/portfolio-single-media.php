<?php
/**
 * Portfolio single media template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get attachments ( gallery images )
$attachments = wpex_get_gallery_ids( get_the_ID() ); ?>

<div id="portfolio-single-media" class="wpex-clr">

	<?php
	// Display slider if there are $attachments
	if ( $attachments ) :

		get_template_part( 'partials/portfolio/portfolio-single-gallery' );

	// Display Post Video if defined
	elseif ( $video = wpex_get_post_video() ) :

		echo wpex_get_portfolio_post_video( $video );

	// Otherwise display post thumbnail
	elseif ( has_post_thumbnail() ) :

		if ( apply_filters( 'wpex_single_portfolio_media_lightbox', true ) ) :

			wpex_enqueue_lightbox_scripts();

			// Display thumbnail
			// Note: use the wpex_get_portfolio_post_thumbnail_args filter to override the thumbnail output.
			echo wpex_get_portfolio_post_thumbnail( array(
				'before' => '<a href="' .  wpex_get_lightbox_image() . '" class="wpex-lightbox">',
				'after'  => '</a>',
			) );

		else :

			echo wpex_get_portfolio_post_thumbnail();

		endif;

	endif; ?>

</div><!-- .portfolio-entry-media -->