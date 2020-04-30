<?php
/**
 * Staff entry title template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( wpex_get_mod( 'staff_entry_position', true ) && $position = get_post_meta( get_the_ID(), 'wpex_staff_position', true ) ) : ?>
	<div class="staff-entry-position clr"><?php echo wp_kses_post( $position ); ?></div>
<?php endif; ?>