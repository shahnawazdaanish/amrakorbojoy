<?php
/**
 * Outputs the testimonial entry rating
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo wpex_get_star_rating( '', get_the_ID(), '<div class="testimonial-entry-rating clr">', '</div>' ); ?>