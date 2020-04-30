<?php
/**
 * Product Loop Start
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 9999
 *
 */

// Define classes and apply filter for easy modification
$classes = 'products wpex-row clr';
if ( wpex_get_mod( 'woo_entry_equal_height', false ) ) {
	$classes .= ' match-height-grid';
}
if ( $gap = wpex_get_mod( 'woo_shop_columns_gap' ) ) {
	$classes .= ' gap-' . $gap;
}
$classes = apply_filters( 'wpex_woo_loop_wrap_classes', $classes ); ?>

<ul class="<?php echo esc_attr( $classes );?>">