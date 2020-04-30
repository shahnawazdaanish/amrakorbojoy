<?php
/**
 * Adds custom CSS to alter all main theme border colors
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.5
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class BorderColors {

	/**
	 * Main constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'customizer_css' ), 99 );
			add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		} else {
			add_filter( 'wpex_head_css', array( $this, 'live_css' ), 1 );
		}
	}

	/**
	 * Array of elements
	 *
	 * @since 2.0.0
	 */
	public function elements() {
		return apply_filters( 'wpex_border_color_elements', array(

			// General
			'.theme-heading.border-side span.text:after',
			'.theme-heading.border-w-color',
			'#comments .comment-body',
			'.centered-minimal-page-header',

			// Top bar
			'#top-bar-wrap',

			// Blog
			'.blog-entry.large-image-entry-style',
			'.blog-entry.grid-entry-style .blog-entry-inner',
			'.entries.left-thumbs .blog-entry.thumbnail-entry-style',

			// CPT
			'.cpt-entry.span_1_of_1',

			// Pagination
			'ul .page-numbers a,
			 a.page-numbers,
			 span.page-numbers',

			'.post-pagination-wrap',

			// Widgets
			'#main .wpex-widget-recent-posts-li:first-child,
			 #main .widget_categories li:first-child,
			 #main .widget_recent_entries li:first-child,
			 #main .widget_archive li:first-child,
			 #main .widget_recent_comments li:first-child,
			 #main .widget_product_categories li:first-child,
			 #main .widget_layered_nav li:first-child,
			 #main .widget-recent-posts-icons li:first-child,
			 #main .site-footer .widget_nav_menu li:first-child',

			'#main .wpex-widget-recent-posts-li,
			 #main .widget_categories li,
			 #main .widget_recent_entries li,
			 #main .widget_archive li,
			 #main .widget_recent_comments li,
			 #main .widget_product_categories li,
			 #main .widget_layered_nav li,
			 #main .widget-recent-posts-icons li,
			 #main .site-footer .widget_nav_menu li',

			'.modern-menu-widget',
			'.modern-menu-widget li',
			'.modern-menu-widget li ul',

			'#sidebar .widget_nav_menu a',
			'#sidebar .widget_nav_menu ul > li:first-child > a',
			'.widget_nav_menu_accordion .widget_nav_menu a',
			'.widget_nav_menu_accordion .widget_nav_menu ul > li:first-child > a',


			// Modules
			'.vcex-divider-solid',
			'.vcex-blog-entry-details',
			'.theme-button.minimal-border',
			'.vcex-login-form',
			'.vcex-recent-news-entry',

			// Tables
			//'table th, table td', // removed in 4.8 because we have new section just for tables now

		) );
	}

	/**
	 * Generates the CSS output
	 *
	 * @since 2.0.0
	 */
	public function generate() {

		// Get array to loop through
		$elements = $this->elements();

		// Return if array is empty
		if ( empty( $elements ) ) {
			return;
		}

		// Get border color
		$color = wpex_get_mod( 'main_border_color', '#eee' );

		// Check for theme mod and make sure it's not the same as the theme's default color
		if ( $color && '#eee' != $color && '#eeeeee' != $color ) {

			// Define css var
			$css = '';

			// Borders
			$elements = implode( ',', $elements );
			$css .= $elements . '{border-color:' . $color . ';}';

			// Return CSS
			if ( $css ) {
				return '/*BORDER COLOR*/' . $css;
			}

		}

	}

	/**
	 * Live site output
	 *
	 * @since 4.0
	 */
	public function live_css( $output ) {
		if ( $css = $this->generate() ) {
			$output .= $css;
		}
		return $output;
	}

	/**
	 * Customizer Output
	 *
	 * @since 4.0
	 */
	public function customizer_css() {
		echo '<style id="wpex-borders-css">' . $this->generate() . '</style>';
	}

	/**
	 * Customizer Live JS
	 *
	 * @since 4.0
	 */
	public function customize_preview_init() {

		$elements = $this->elements();

		if ( empty( $elements ) ) {
			return;
		}

		wp_enqueue_script( 'wpex-customizer-border-colors',
			wpex_asset_url( 'js/dynamic/customizer/wpex-border-colors.min.js' ),
			array( 'customize-preview' ),
			WPEX_THEME_VERSION,
			true
		);

		wp_localize_script(
			'wpex-customizer-border-colors',
			'wpexBorderColorElements',
			$elements
		);

	}

}
new BorderColors();