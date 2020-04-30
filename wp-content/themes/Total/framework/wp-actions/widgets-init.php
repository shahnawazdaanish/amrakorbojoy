<?php
/**
 * Functions that run on widgets init
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register sidebar widget areas
 *
 * @since 4.0
 */
function wpex_register_sidebar_widget_areas() {

	// Define sidebars array
	$sidebars = array(
		'sidebar' => esc_html__( 'Main Sidebar', 'total' ),
	);

	// Pages Sidebar
	if ( wpex_get_mod( 'pages_custom_sidebar', true ) ) {
		$sidebars['pages_sidebar'] = esc_html__( 'Pages Sidebar', 'total' );
	}

	// Blog Sidebar
	if ( wpex_get_mod( 'blog_custom_sidebar', false ) ) {
		$sidebars['blog_sidebar'] = esc_html__( 'Blog Sidebar', 'total' );
	}

	// Search Results Sidebar
	if ( wpex_get_mod( 'search_custom_sidebar', true ) ) {
		$sidebars['search_sidebar'] = esc_html__( 'Search Results Sidebar', 'total' );
	}

	// WooCommerce
	if ( WPEX_WOOCOMMERCE_ACTIVE && wpex_get_mod( 'woo_custom_sidebar', true ) ) {
		$sidebars['woo_sidebar'] = esc_html__( 'WooCommerce Sidebar', 'total' );
	}

	// Apply filters - makes it easier to register new sidebars
	$sidebars = apply_filters( 'wpex_register_sidebars_array', $sidebars );

	// If there are no sidebars then return
	if ( ! $sidebars ) {
		return;
	}

	// Sidebar tags
	$tag         = wpex_get_mod( 'sidebar_headings' );
	$tag_escaped = $tag ? esc_attr( $tag ) : 'div';

	// Loop through sidebars and register them
	foreach ( $sidebars as $k => $v ) {

		$args = array(
			'id'            => sanitize_key( $k ),
			'before_widget' => '<div id="%1$s" class="sidebar-box widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
			'after_title'   => '</' . $tag_escaped . '>',
		);

		if ( is_array( $v ) ) {
			$args = wp_parse_args( $v, $args );
		} else {
			$args['name'] = esc_html( $v );
		}

		register_sidebar( $args );

	}

}
add_action( 'widgets_init', 'wpex_register_sidebar_widget_areas' );

/**
 * Register footer widget areas
 *
 * @since 4.0
 */
function wpex_register_footer_widget_areas() {

	if ( wpex_has_custom_footer() ) {
		$has_footer_widgets = wpex_get_mod( 'footer_builder_footer_widgets', false );
	} else {
		$has_footer_widgets = wpex_get_mod( 'footer_widgets', true );
	}

	// Check if footer widgets are enabled
	// @todo rename this filter? Doesn't really make sense...should be "wpex_register_footer_widget_areas"
	$has_footer_widgets = apply_filters( 'wpex_register_footer_sidebars', $has_footer_widgets );

	// Return if disabled
	if ( ! $has_footer_widgets ) {
		return;
	}

	// Footer tag
	$tag = wpex_get_mod( 'footer_headings' );
	$tag_escaped = $tag ? wp_strip_all_tags( $tag ) : 'div';

	// Footer widget columns
	$footer_columns = wpex_get_mod( 'footer_widgets_columns', '4' );

	// Footer 1
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 1', 'total' ),
		'id'            => 'footer_one',
		'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s clr">',
		'after_widget'  => '</div>',
		'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
		'after_title'   => '</' . $tag_escaped . '>',
	) );

	// Footer 2
	if ( $footer_columns > '1' ) {

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 2', 'total' ),
			'id'            => 'footer_two',
			'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
			'after_title'   => '</' . $tag_escaped . '>'
		) );

	}

	// Footer 3
	if ( $footer_columns > '2' ) {

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 3', 'total' ),
			'id'            => 'footer_three',
			'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
			'after_title'   => '</' . $tag_escaped . '>',
		) );

	}

	// Footer 4
	if ( $footer_columns > '3' ) {

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 4', 'total' ),
			'id'            => 'footer_four',
			'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
			'after_title'   => '</' . $tag_escaped . '>',
		) );

	}

	// Footer 5
	if ( $footer_columns > '4' ) {

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 5', 'total' ),
			'id'            => 'footer_five',
			'before_widget' => '<div id="%1$s" class="footer-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag_escaped . ' class="widget-title">',
			'after_title'   => '</' . $tag_escaped . '>',
		) );

	}

}
add_action( 'widgets_init', 'wpex_register_footer_widget_areas', 40 );