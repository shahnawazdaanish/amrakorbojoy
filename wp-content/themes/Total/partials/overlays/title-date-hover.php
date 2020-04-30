<?php
/**
 * Title Date Hover Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
}

// Get post data
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title();
$date  = isset( $args['post_date'] ) ? $args['post_date'] : get_the_date(); ?>

<div class="overlay-title-date-hover overlay-hide theme-overlay textcenter">
	<div class="overlay-table clr">
		<div class="overlay-table-cell clr">
			<div class="overlay-title"><?php echo esc_html( $title ); ?></div>
			<div class="overlay-date"><?php echo esc_html( $date ); ?></div>
		</div>
	</div>
</div>