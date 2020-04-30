<?php
/**
 * Scripts.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue lightbox scripts.
 * This is a Total exclusive script.
 */
function vcex_enqueue_lightbox_scripts() {
	if ( function_exists( 'wpex_enqueue_lightbox_scripts' ) ) {
		wpex_enqueue_lightbox_scripts();
	} elseif ( function_exists( 'wpex_enqueue_ilightbox_scripts' ) ) {
		wpex_enqueue_ilightbox_scripts();
	}
}

/**
 * Enqueue slider scripts.
 */
function vcex_enqueue_slider_scripts( $noCarouselThumbnails = false ) {
	if ( function_exists( 'wpex_enqueue_slider_pro_scripts' ) ) {
		wpex_enqueue_slider_pro_scripts( $noCarouselThumbnails );
	}
}

/**
 * Enqueue carousel scripts.
 */
function vcex_enqueue_carousel_scripts() {
	if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
		wp_enqueue_style(
			'owl-carousel',
			vcex_asset_url( 'css/owl-carousel.css' ),
			array(),
			'1.0'
		);
	}
	wp_enqueue_script(
		'wpex-carousel',
		vcex_asset_url( 'js/lib/wpex.owl.carousel.min.js' ),
		array( 'jquery' ),
		'1.0',
		true
	);
	wp_localize_script(
		'wpex-carousel',
		'wpexCarousel',
		array(
			'i18n' => array(
				'NEXT' => esc_html__( 'next slide', 'total-theme-core' ),
				'PREV' => esc_html__( 'previous slide', 'total-theme-core' ),
			),
		)
	);
	wp_enqueue_script( 'imagesloaded' );
}

/**
 * Enqueue isotope scripts.
 */
function vcex_enqueue_isotope_scripts() {
	wp_enqueue_script( 'imagesloaded' );
	wp_enqueue_script(
		'isotope',
		vcex_asset_url( 'js/lib/isotope.pkgd.min.js' ),
		array( 'jquery', 'imagesloaded' ),
		'3.0.6',
		true
	);
}

/**
 * Enqueue navbar filter scripts.
 */
function vcex_enqueue_navbar_filter_scripts() {
	vcex_enqueue_isotope_scripts();
}

/**
 * Enqueue Google Fonts.
 */
function vcex_enqueue_google_font( $font_family = '' ) {
	if ( function_exists( 'wpex_enqueue_google_font' ) ) {
		wpex_enqueue_google_font( $font_family );
	}
}