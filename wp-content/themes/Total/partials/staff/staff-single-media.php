<?php
/**
 * Staff single media template part
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
$attachments = wpex_get_gallery_ids( get_the_ID() );

// Check if has thumbnail
$has_thumb = has_post_thumbnail();

if ( $attachments || $has_thumb ) : ?>

	<div id="staff-single-media" class="clr">

		<?php
		// Display image gallery
		if ( $attachments ) :

			get_template_part( 'partials/staff/staff-single-gallery' );

		// Display single featured image
		elseif( $has_thumb ) :

			wpex_enqueue_lightbox_scripts();

			// Display thumbnail
			// Note: use the wpex_get_staff_post_thumbnail_args filter to override the thumbnail output.
			echo wpex_get_staff_post_thumbnail( array(
				'before' => '<a href="' .  wpex_get_lightbox_image() . '" class="wpex-lightbox">',
				'after'  => '</a>',
			) );

		endif; ?>

	</div><!-- .staff-entry-media -->

<?php endif; ?>