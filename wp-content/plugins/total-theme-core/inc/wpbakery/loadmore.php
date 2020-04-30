<?php
/**
 * Load More functions for Total VC grid modules.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load More Scripts.
 */
function vcex_loadmore_scripts() {

	$dependencies = array( 'jquery' );

	if ( defined( 'WPEX_THEME_JS_HANDLE' ) ) {
		$dependencies[] = WPEX_THEME_JS_HANDLE;
	}

	wp_enqueue_script( 'imagesloaded' );

	$dependencies[] = 'imagesloaded';

	if ( apply_filters( 'vcex_loadmore_enqueue_mediaelement', false ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	wp_enqueue_script(
		'vcex-loadmore',
		vcex_asset_url( 'js/vcex-loadmore.min.js' ),
		$dependencies,
		TTC_VERSION,
		true
	);
}

/**
 * Load More Button.
 */
function vcex_get_loadmore_button( $shortcode_tag, $atts, $query ) {

	// Get current page and max_num_pages
	$page      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$max_pages = $query->max_num_pages;

	// No need for load more if we already reached the last page
	if ( $page >= $max_pages ) {
		return;
	}

	// Remove useless attributes
	unset( $atts['wrap_css'] );
	unset( $atts['show_categories_tax'] );

	if ( ! in_array( $shortcode_tag, array( 'vcex_post_type_archive', 'vcex_post_type_grid', 'vcex_recent_news' ) ) ) {
		unset( $atts['post_type'] );
		unset( $atts['taxonomy'] );
	}

	// Define load more text
	$load_more_text = '';

	if ( function_exists( 'wpex_get_translated_theme_mod' ) ) {
		$load_more_text = wpex_get_translated_theme_mod( 'loadmore_text', $load_more_text );
	}

	// Create array of load more settings to be added to the button data
	$settings = apply_filters( 'vcex_get_loadmore_button_settings', array(
		'class'        => 'vcex-loadmore-button theme-button',
		'text'         => $load_more_text ? $load_more_text : esc_html__( 'Load More', 'total-theme-core' ),
		'loading_text' => esc_html__( 'Loading...', 'total-theme-core' ),
		'failed_text'  => esc_html__( 'Failed to load posts.', 'total-theme-core' ),
		'gif'          => includes_url( 'images/spinner-2x.gif' ),
	), $shortcode_tag, $atts );

	// Build load more button
	$button = '<div class="vcex-loadmore">';

		$btn_attr = array(
			'class'                 => esc_attr( $settings['class'] ),
			'data-page'             => esc_attr( $page ),
			'data-max-pages'        => esc_attr( $max_pages ),
			'data-text'             => esc_attr( $settings['text'] ),
			'data-loading-text'     => esc_attr( $settings['loading_text'] ),
			'data-failed-text'      => esc_attr( $settings['failed_text'] ),
			'data-nonce'            => esc_attr( wp_create_nonce( 'vcex-ajax-pagination-nonce' ) ),
			'data-shortcode-tag'    => esc_attr( $shortcode_tag ),
			'data-shortcode-params' => htmlspecialchars( wp_json_encode( $atts ) ),
		);

		$button .= '<a';
			foreach ( $btn_attr as $name => $value_escaped ) {
	            $button .= ' ' . $name . '="' .  $value_escaped . '"';
	        }
		$button .= '>';

			$button_text_allowed_tags = array(
				'img'  => array(
					'src' => array(),
					'alt' => array(),
				),
				'span' => array(
					'class' => array(),
				),
			);

			$button .= '<span class="vcex-txt">' . wp_kses( $settings['text'], $button_text_allowed_tags ) . '</span>';

		$button .= '</a>';

		$button .= '<img src="' . esc_url( $settings['gif'] ) . '" class="vcex-spinner" alt="' . esc_attr( $settings['loading_text'] ) . '" />';

	$button .= '<span class="ticon ticon-spinner"></span></div>';

	// Return load more button
	return $button;

}

/**
 *  Load More AJAX.
 */
function vcex_loadmore_ajax_render() {

	check_ajax_referer( 'vcex-ajax-pagination-nonce', 'nonce' );

	if ( empty( $_POST[ 'shortcodeParams' ] ) ) {
		wp_die();
	}

	$allowed_shortcodes = array(
		'vcex_blog_grid',
		'vcex_image_grid',
		'vcex_portfolio_grid',
		'vcex_post_type_archive',
		'vcex_post_type_grid',
		'vcex_recent_news',
		'vcex_staff_grid',
		'vcex_testimonials_grid',
	);

	if ( empty( $_POST[ 'shortcodeTag' ] ) || ! in_array( $_POST[ 'shortcodeTag' ], $allowed_shortcodes ) ) {
		wp_die();
	}

	if ( class_exists( 'WPBMap' ) ) {
		WPBMap::addAllMappedShortcodes(); // fix for WPBakery not working in ajax
	}

	$tag    = wp_strip_all_tags( $_POST[ 'shortcodeTag' ] );
	$params = (array) $_POST[ 'shortcodeParams' ];

	$data = wp_send_json_success( vcex_do_shortcode_function( $tag, $params ) );

	wp_send_json_success( $data );

	wp_die();

}
add_action( 'wp_ajax_vcex_loadmore_ajax_render', 'vcex_loadmore_ajax_render' );
add_action( 'wp_ajax_nopriv_vcex_loadmore_ajax_render', 'vcex_loadmore_ajax_render' );