<?php
/**
 * Staff post title
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Display if position is defined
if ( $position = get_post_meta( get_the_ID(), 'wpex_staff_position', true ) ) : ?>
	<div id="staff-single-position" class="single-staff-position wpex-clr"><?php echo wp_kses_post( $position ); ?></div>
<?php endif; ?>