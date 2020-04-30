<?php
/**
 * Enqueue front end theme scripts [CSS & JS]
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core theme CSS.
 */
function wpex_enqueue_front_end_main_css() {

	// Register hover-css for use with shortcodes
	wp_register_style(
		'wpex-hover-animations',
		wpex_asset_url( 'lib/hover-css/hover-css.min.css' ),
		array(),
		'2.0.1'
	);

	wp_register_style(
		'fancybox',
		wpex_asset_url( 'lib/fancybox/jquery.fancybox.min.css' ),
		array(),
		'3.5.7'
	);

	// Main style.css File
	wp_enqueue_style( WPEX_THEME_STYLE_HANDLE, get_stylesheet_uri(), array(), WPEX_THEME_VERSION );

	// Override main style.css with style-rtl.css
	if ( is_child_theme() ) {
		wp_style_add_data( 'parent-style', 'rtl', 'replace' );
	} else {
		wp_style_add_data( WPEX_THEME_STYLE_HANDLE, 'rtl', 'replace' );
	}

	// Total modules
	if ( wpex_get_mod( 'extend_visual_composer', true ) ) {
		$deps = array( WPEX_THEME_STYLE_HANDLE );
		if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
			$deps[] = 'js_composer_front';
		}
		wp_enqueue_style( 'wpex-visual-composer', wpex_asset_url( 'css/wpex-visual-composer.css' ), $deps, WPEX_THEME_VERSION );
		wp_style_add_data( 'wpex-visual-composer', 'rtl', 'replace' );
	}

}
add_action( 'wp_enqueue_scripts', 'wpex_enqueue_front_end_main_css' );

/**
 * Browser dependent CSS.
 */
function wpex_enqueue_front_end_browser_dependent_css() {

	// IE8 Stylesheet
	wp_enqueue_style( 'wpex-ie8',
		apply_filters( 'wpex_ie8_stylesheet', wpex_asset_url( 'css/wpex-ie8.css' ) ),
		false,
		WPEX_THEME_VERSION
	);
	wp_style_add_data( 'wpex-ie8', 'conditional', 'IE 8' );

	// IE9 Stylesheet
	wp_enqueue_style( 'wpex-ie9',
		apply_filters( 'wpex_ie9_stylesheet', wpex_asset_url( 'css/wpex-ie9.css' ) ),
		false,
		WPEX_THEME_VERSION
	);
	wp_style_add_data( 'wpex-ie9', 'conditional', 'IE 9' );

}
add_action( 'wp_enqueue_scripts', 'wpex_enqueue_front_end_browser_dependent_css', 40 );

/**
 * Load theme js.
 */
function wpex_enqueue_front_end_js() {

	// First lets make sure html5 shiv is on the site
	wp_enqueue_script(
		'wpex-html5shiv',
		wpex_asset_url( 'js/dynamic/html5.js' ),
		array(),
		WPEX_THEME_VERSION,
		false
	);
	wp_script_add_data( 'wpex-html5shiv', 'conditional', 'lt IE 9' );

	// Comment reply
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Register scripts
	wp_register_script(
		'wpex-loadmore',
		wpex_asset_url( 'js/dynamic/wpex-loadmore.min.js' ),
		array( 'jquery', 'imagesloaded', WPEX_THEME_JS_HANDLE ),
		WPEX_THEME_VERSION,
		true
	);

	wp_register_script(
		'sliderPro',
		wpex_asset_url( 'js/dynamic/jquery.sliderPro.min.js' ),
		array( 'jquery' ),
		'1.3',
		true
	);

	wp_localize_script(
		'sliderPro',
		'wpexSliderPro',
		array(
			'i18n' => array(
				'NEXT' => esc_html__( 'next slide', 'total' ),
				'PREV' => esc_html__( 'previous slide', 'total' ),
				'GOTO' => esc_html__( 'go to slide', 'total' ),
			),
		)
	);

	wp_register_script(
		'sliderProCustomThumbnails',
		wpex_asset_url( 'js/dynamic/jquery.sliderProCustomThumbnails.min.js' ),
		array( 'jquery', 'sliderPro' ),
		WPEX_THEME_VERSION,
		true
	);

	wp_register_script(
		'fancybox',
		wpex_asset_url( 'lib/fancybox/jquery.fancybox.min.js' ),
		array( 'jquery', WPEX_THEME_JS_HANDLE ),
		'3.5.7',
		true
	);
	wp_localize_script(
		'fancybox',
		'wpexLightboxSettings',
		wpex_get_lightbox_settings()
	);

	// Load Lightbox Globally
	if ( wpex_get_mod( 'lightbox_auto', false )
		|| apply_filters( 'wpex_load_ilightbox_globally', wpex_get_mod( 'lightbox_load_style_globally', false ) )
		) {
		wpex_enqueue_lightbox_scripts();
	}

	// Load minified js
	if ( wpex_get_mod( 'minify_js_enable', true ) ) {

		wp_enqueue_script(
			WPEX_THEME_JS_HANDLE,
			wpex_asset_url( 'js/total.min.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

	}

	// Load all non-minified js
	else {

		wp_enqueue_script(
			'wpex-easing',
			wpex_asset_url( 'js/core/jquery.easing.js' ), // @todo maybe remove in the future since we are barely using it.
			array( 'jquery' ),
			'1.3.2',
			true
		);

		wp_enqueue_script(
			'wpex-superfish',
			wpex_asset_url( 'js/core/superfish.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-supersubs',
			wpex_asset_url( 'js/core/supersubs.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-hoverintent',
			wpex_asset_url( 'js/core/hoverIntent.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-sidr',
			wpex_asset_url( 'js/core/sidr.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-equal-heights',
			wpex_asset_url( 'js/core/jquery.wpexEqualHeights.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-mousewheel',
			wpex_asset_url( 'js/core/jquery.mousewheel.js' ), // @REMOVE WHEN WE REMOVE LIGHTBOX
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'wpex-scrolly',
			wpex_asset_url( 'js/core/scrolly.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

		// Core global functions
		wp_enqueue_script(
			WPEX_THEME_JS_HANDLE,
			wpex_asset_url( 'js/total.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

	}

	// Localize core js
	wp_localize_script( WPEX_THEME_JS_HANDLE, 'wpexLocalize', wpex_js_localize_data() );

	// Retina.js
	if ( wpex_is_retina_enabled() ) {

		wp_enqueue_script(
			'wpex-retina',
			wpex_asset_url( 'js/dynamic/retina.js' ),
			array( 'jquery' ),
			'1.3',
			true
		);

	}

	// Register social share script
	wp_register_script(
		'wpex-social-share',
		wpex_asset_url( 'js/dynamic/wpex-social-share.min.js' ),
		array( WPEX_THEME_JS_HANDLE ),
		WPEX_THEME_VERSION,
		true
	);

}
add_action( 'wp_enqueue_scripts', 'wpex_enqueue_front_end_js' );

/**
 * Load archive scripts.
 */
function wpex_enqueue_archive_scripts() {

	// Standard post archive
	if ( wpex_is_blog_query() ) {
		$style = wpex_blog_entry_style();
		if ( 'masonry' == wpex_blog_grid_style() ) {
			wpex_enqueue_isotope_scripts();
		}
		return;
	}

	// Portfolio
	if ( is_post_type_archive( 'portfolio' ) || wpex_is_portfolio_tax() ) {
		if ( in_array( wpex_get_mod( 'portfolio_archive_grid_style', 'fit-rows' ), array( 'masonry', 'no-margins' ) ) ) {
			wpex_enqueue_isotope_scripts();
		}
		return;
	}

	// Staff
	if ( is_post_type_archive( 'staff' ) || wpex_is_staff_tax() ) {
		if ( in_array( wpex_get_mod( 'staff_archive_grid_style', 'fit-rows' ), array( 'masonry', 'no-margins' ) ) ) {
			wpex_enqueue_isotope_scripts();
		}
		return;
	}

	// Testimonials
	if ( is_post_type_archive( 'testimonials' ) || wpex_is_testimonials_tax() ) {
		if ( in_array( wpex_get_mod( 'testimonials_archive_grid_style', 'fit-rows' ), array( 'masonry' ) ) ) {
			wpex_enqueue_isotope_scripts();
		}
		return;
	}

}
add_action( 'wp_enqueue_scripts', 'wpex_enqueue_archive_scripts' );

/**
 * Remove block library CSS if Gutenberg is disabled via WPBakery or if the Classic Editor plugin is active
 */
function wpex_remove_block_library_css() {
	if ( ! apply_filters( 'wpex_remove_block_library_css', true ) ) {
		return;
	}
	if ( class_exists( 'Classic_Editor' ) || ( WPEX_VC_ACTIVE && get_option( 'wpb_js_gutenberg_disable' ) ) ) {
		wp_dequeue_style( 'wp-block-library' );
		if ( WPEX_WOOCOMMERCE_ACTIVE ) {
			wp_dequeue_style( 'wc-block-style' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'wpex_remove_block_library_css', 100 );