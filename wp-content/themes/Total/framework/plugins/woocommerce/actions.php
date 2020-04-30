<?php
/**
 * WooCommerce Actions
 *
 * @package Total WordPress Theme
 * @subpackage WooCommerce
 * @version 4.8
 */

// Remove demo store notice from wp_footer place top of site
remove_action( 'wp_footer', 'woocommerce_demo_store' );
add_action( 'wpex_hook_wrap_top', 'woocommerce_demo_store', 0 );

/**
 * Move ratings and price on product page
 *
 * @version 4.4.1
 */
function wpex_woo_move_product_rating_price() {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
}
add_action( 'init', 'wpex_woo_move_product_rating_price' );

/**
 * Display WooCommerce archive description on paginated shop page
 *
 * @version 4.5
 */
function wpex_woo_paginated_shop_description() {
	if ( ! wpex_is_woo_shop() || ! is_paged() ) {
		return;
	}
	$shop_id = wpex_parse_obj_id( wc_get_page_id( 'shop' ), 'page' );
	$shop_page = get_post( $shop_id );
	if ( $shop_page ) {
		$description = wc_format_content( $shop_page->post_content );
		if ( $description ) {
			echo '<div class="page-description">' . $description . '</div>';
		}
	}
}
add_action( 'woocommerce_archive_description', 'wpex_woo_paginated_shop_description' );