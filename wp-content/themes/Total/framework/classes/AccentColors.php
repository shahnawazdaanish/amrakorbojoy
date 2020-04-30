<?php
/**
 * Adds custom CSS to the site to tweak the main accent colors
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.8
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class AccentColors {

	/**
	 * Main constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'customizer_css' ), 10 );
		} else {
			add_filter( 'wpex_head_css', array( $this, 'live_css' ), 1 );
		}
	}

	/**
	 * Generates arrays of elements to target
	 *
	 * @since 2.0.0
	 */
	public static function arrays( $return = '' ) {

		// Texts
		$texts = apply_filters( 'wpex_accent_texts', array(
			'a',
			'.wpex-accent-color',
			'#site-navigation .dropdown-menu > li.menu-item > a:hover',
			'#site-navigation .dropdown-menu > li.menu-item.current-menu-item > a',
			'#site-navigation .dropdown-menu > li.menu-item.current-menu-parent > a',
			'h1 a:hover',
			'h2 a:hover',
			'a:hover h2',
			'h3 a:hover',
			'h4 a:hover',
			'h5 a:hover',
			'h6 a:hover',
			'.entry-title a:hover',
			'.modern-menu-widget a:hover',
			'.theme-button.outline',
			'.theme-button.clean',
			'.meta a:hover',
		) );

		// Backgrounds
		$backgrounds = apply_filters( 'wpex_accent_backgrounds', array(

			'.wpex-accent-bg',
			'.post-edit a',
			'.background-highlight',
			'input[type="submit"]',
			'.theme-button',
			'button',
			'.button',
			'.theme-button.outline:hover',
			'.active > .theme-button',
			'.theme-button.active',
			'.tagcloud a:hover',
			'.post-tags a:hover',
			'.wpex-carousel .owl-dot.active',
			'.wpex-carousel .owl-prev',
			'.wpex-carousel .owl-next',
			'body #header-two-search #header-two-search-submit',
			'#site-navigation .menu-button > a > span.link-inner',
			'.modern-menu-widget li.menu-item.current-menu-item a',
			'#sidebar .widget_nav_menu .current-menu-item > a',
			'.widget_nav_menu_accordion .widget_nav_menu li.menu-item.current-menu-item > a',
			'#site-navigation-wrap.has-menu-underline .main-navigation-ul>li>a>.link-inner::after',
			'#wp-calendar caption',
			'#wp-calendar tbody td:hover a',
			'.navbar-style-six .dropdown-menu > li.menu-item.current-menu-item > a',
			'.navbar-style-six .dropdown-menu > li.menu-item.current-menu-parent > a',
			'#wpex-sfb-l,#wpex-sfb-r,#wpex-sfb-t,#wpex-sfb-b',
			'#site-scroll-top:hover',

		) );

		// Backgrounds Hover ( #3b86b0 )
		$backgrounds_hover = apply_filters( 'wpex_accent_hover_backgrounds', array(

			'.post-edit a:hover',
			'.theme-button:hover',
			'input[type="submit"]:hover',
			'button:hover',
			'.button:hover',
			'.wpex-carousel .owl-prev:hover',
			'.wpex-carousel .owl-next:hover',
			'#site-navigation .menu-button > a > span.link-inner:hover',

		) );

		// Borders
		$borders = apply_filters( 'wpex_accent_borders', array(
			'.theme-button.outline',
			'#searchform-dropdown',
			'body #site-navigation-wrap.nav-dropdown-top-border .dropdown-menu > li > ul' => array( 'top' ),
			'.theme-heading.border-w-color span.text' => array( 'bottom' ),
		) );

		// Return array
		if ( 'texts' == $return ) {
			return $texts;
		} elseif ( 'backgrounds' == $return ) {
			return $backgrounds;
		} elseif ( 'backgrounds_hover' == $return ) {
			return $backgrounds_hover;
		} elseif ( 'borders' == $return ) {
			return $borders;
		} else {
			return array(
				'texts'             => $texts,
				'backgrounds'       => $backgrounds,
				'backgrounds_hover' => $backgrounds_hover,
				'borders'           => $borders,
			);
		}

	}

	/**
	 * Generates the Accent css
	 *
	 * @since 4.5
	 */
	public function accent_css() {

		// Get custom accent
		$custom_accent = esc_attr( wpex_get_custom_accent_color() );

		// Return if accent color is empty or equal to default
		if ( ! $custom_accent ) {
			return;
		}

		// Define empty css var
		$css = '';

		// Get arrays
		$texts       = self::arrays( 'texts' );
		$backgrounds = self::arrays( 'backgrounds' );
		$borders     = self::arrays( 'borders' );

		// Texts
		if ( ! empty( $texts ) ) {
			$css .= implode( ',', $texts ) . '{color:' . $custom_accent . ';}';
		}

		// Backgrounds
		if ( ! empty( $backgrounds ) ) {
			$css .= implode( ',', $backgrounds ) . '{background-color:' . $custom_accent . ';}';
		}

		// Borders
		if ( ! empty( $borders ) ) {
			foreach ( $borders as $key => $val ) {
				if ( is_array( $val ) ) {
					$css .= $key . '{';
					foreach ( $val as $key => $val ) {
						$css .= 'border-' . $val . '-color:' . $custom_accent . ';';
					}
					$css .= '}';
				} else {
					$css .= $val . '{border-color:' . $custom_accent . ';}';
				}
			}
		}

		// Return CSS
		if ( $css ) {
			return $css;
		}

	}

	/**
	 * Generates the Accent hover css
	 *
	 * @since 4.5
	 */
	public function accent_hover_css() {

		$accent = esc_attr( wpex_get_custom_accent_color_hover() );

		if ( ! $accent ) {
			return;
		}

		$css = '';

		$backgrounds_hover = self::arrays( 'backgrounds_hover' );

		if ( ! empty( $backgrounds_hover ) ) {
			$css .= implode( ',', $backgrounds_hover ) . '{background-color:' . $accent . ';}';
		}

		return $css;

	}

	/**
	 * Customizer Output
	 *
	 * @since 4.0
	 */
	public function customizer_css() {
		echo '<style id="wpex-accent-css">' . $this->accent_css() . '</style>';
		echo '<style id="wpex-accent-hover-css">' . $this->accent_hover_css() . '</style>';
	}

	/**
	 * Live site output
	 *
	 * @since 4.0
	 */
	public function live_css( $output ) {
		$accent_css = $this->accent_css();
		if ( $accent_css ) {
			$output .= '/*ACCENT COLOR*/' . $accent_css;
		}
		$accent_hover_css = $this->accent_hover_css();
		if ( $accent_hover_css ) {
			$output .= '/*ACCENT HOVER COLOR*/' . $accent_hover_css;
		}
		return $output;
	}

}
new AccentColors();