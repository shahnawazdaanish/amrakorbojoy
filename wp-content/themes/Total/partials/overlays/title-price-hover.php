<?php
/**
 * Title Price Hover Overlay
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
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title(); ?>

<div class="overlay-title-price-hover overlay-hide theme-overlay textcenter">
	<div class="overlay-table clr">
		<div class="overlay-table-cell clr">
			<div class="overlay-title"><?php echo esc_html( $title ); ?></div>
			<?php if ( function_exists( 'wpex_get_woo_product_price' ) ) { ?>
				<?php echo wpex_get_woo_product_price( get_the_ID(), '<div class="overlay-price">', '</div>' ); ?>
			<?php } ?>
		</div>
	</div>
</div>