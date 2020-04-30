<?php
/**
 * Outputs the testimonial entry avatar
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Display thumbnail
echo wpex_get_testimonials_entry_thumbnail( array(
	'before' => '<div class="testimonial-entry-thumb default-dims">',
	'after'  => '</div>',
) ); ?>