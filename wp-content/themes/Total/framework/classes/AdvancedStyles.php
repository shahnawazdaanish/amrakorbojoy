<?php
/**
 * Advanced inline CSS output - requires advanced checks
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.8
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class AdvancedStyles {

	/**
	 * Main constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'wpex_head_css', array( $this, 'generate' ), 999 );
	}

	/**
	 * Generates the CSS output
	 *
	 * @since 2.0.0
	 */
	public function generate( $output ) {

		if ( ! apply_filters( 'wpex_generate_advanced_styles', true ) ) {
			return;
		}

		// Define main variables
		$css          = '';
		$post_id      = wpex_get_current_post_id();
		$has_header   = wpex_has_header( $post_id );
		$vc_is_inline = wpex_vc_is_inline();

		// Sticky Header shrink height
		// Must add CSS in Visual Composer live editor to keep logo height consistancy
		if ( wpex_has_shrink_sticky_header() || $vc_is_inline ) {
			$shrink_header_style = wpex_sticky_header_style();
			if ( 'shrink' == $shrink_header_style || 'shrink_animated' == $shrink_header_style ) {
				$start_height = intval( wpex_get_mod( 'fixed_header_shrink_start_height' ) );
				$start_height = $start_height ? $start_height : 60;
				$output .= '/*Shrink Fixed header*/';
				if ( $vc_is_inline ) {
					$output .= '#site-header #site-logo img{max-height:' . $start_height .'px !important}';
				} else {
					$output .= '.shrink-sticky-header #site-logo img{max-height:' . $start_height .'px !important}';
					$end_height = intval( wpex_get_mod( 'fixed_header_shrink_end_height' ) );
					$end_height = $end_height ? $end_height : 50;
					$header_height = $end_height + 20;
					$output .= '.sticky-header-shrunk #site-header-inner{height:'. $header_height .'px;}';
					$output .= '.shrink-sticky-header.sticky-header-shrunk .navbar-style-five .dropdown-menu > li > a{height:'. $end_height .'px;}';
					$output .= '.shrink-sticky-header.sticky-header-shrunk #site-logo img{max-height:'. $end_height .'px !important;}';
				}
			}
		}

		/*** Mobile Menu CSS ***/
		if ( wpex_header_has_mobile_menu() ) {

			$mm_breakpoint = absint( wpex_header_menu_mobile_breakpoint() );

			/* Logo side offset */
			$mm_side_offset = wpex_get_mod( 'logo_mobile_side_offset' );

			if ( $mm_side_offset ) {

				$mm_side_offset_breakpoint = $mm_breakpoint ? $mm_breakpoint : '959';
				$mm_side_offset_direction  = is_rtl() ? 'left' : 'right';

				$output .= '/*Mobile Menu Side Offset*/';

				$output .= '@media only screen and (max-width: '. $mm_side_offset_breakpoint .'px) {';

					$output .= 'body.has-mobile-menu #site-logo{margin-' . $mm_side_offset_direction .':' . absint( $mm_side_offset ) . 'px}';

				$output .= '}';

			}


			/* Custom mobile menu breakpoint */
			$mm_toggle_style = wpex_header_menu_mobile_toggle_style();

			if ( $mm_breakpoint ) {

				$output .= '/*Mobile Menu Breakpoint*/';

				// Show main nav always and hide mobile
				$output .= 'body.has-mobile-menu .hide-at-mm-breakpoint{display:block;}
							body.has-mobile-menu .show-at-mm-breakpoint{display: none;}';

				// New breakpoint
				$output .= '@media only screen and (max-width: '. $mm_breakpoint .'px) {';

					// Show/Hide items
					$output .= 'body.has-mobile-menu .hide-at-mm-breakpoint{display:none;}';
					$output .= 'body.has-mobile-menu .show-at-mm-breakpoint{display:block;}';

					// Additional tweaks for mobile menu toggle styles
					if ( 'icon_buttons' == $mm_toggle_style ) {

						$output .= 'body.wpex-mobile-toggle-menu-icon_buttons #site-logo{height:100%;float:left;text-align:left;}';

						$output .= 'body.wpex-mobile-toggle-menu-icon_buttons.rtl #site-logo{float:right;text-align:right;}';

						$output .= '#site-header.wpex-header-two-flex-v #site-header-inner{display:block;}';

						$output .= '.header-two-aside{float:none;clear:both;padding-top:20px;text-align:left;}';

					} elseif ( 'fixed_top' == $mm_toggle_style ) {

						$output .= 'body.has-mobile-menu.wpex-mobile-toggle-menu-fixed_top{padding-top:50px;}';

					} elseif ( 'icon_buttons_under_logo' == $mm_toggle_style ) {

						$output .= 'body.wpex-mobile-toggle-menu-icon_buttons_under_logo #site-logo {float:none;width:100%;text-align:center;height:auto;}';

						$output .= 'body.wpex-mobile-toggle-menu-icon_buttons.rtl #site-logo{float:right;text-align:right;}';

						$output .= '#site-header.wpex-header-two-flex-v #site-header-inner{display:block;}';

						$output .= '.header-two-aside{float:none;clear:both;padding-top:20px;text-align:center;}';

					}

				$output .= '}';
			}
		}

		// Logo height
		if ( $has_header
			&& wpex_get_mod( 'apply_logo_height', false )
			&& $height = intval( wpex_get_mod( 'logo_height' ) )
		) {
			$output .= '/*Logo Height*/';
			$output .= '#site-logo img{max-height:'. $height .'px;}';

		}

		// Fix for Fonts In the Visual Composer
		if ( $vc_is_inline ) {
			$css .= '.wpb_row .fa:before { box-sizing:content-box!important; -moz-box-sizing:content-box!important; -webkit-box-sizing:content-box!important; }';
		}

		// Remove header border if custom color is set
		if ( $has_header && wpex_get_mod( 'header_background' ) ) {
			$css .= '.is-sticky #site-header{border-color:transparent;}';
		}

		// Header background image
		if ( $bg = wpex_header_background_image() ) {
			$css .= '#site-header{background-image:url('. esc_url( $bg ) .');}';
		}

		// Overlay Header tweaks
		if ( wpex_has_overlay_header() ) {
			if ( $font_size = get_post_meta( $post_id, 'wpex_overlay_header_font_size', true ) ) {
				$css .= '#site-navigation, #site-navigation .dropdown-menu a{font-size:'. intval( $font_size ) .'px;}';
			}
			if ( $bg = get_post_meta( $post_id, 'wpex_overlay_header_background', true ) ) {
				$css .= '#site-header.overlay-header.dyn-styles{background:' . esc_attr( $bg ) .'; }';
			}
		}

		// Page Header title bg
		if ( $bg = wpex_page_header_background_image() ) {
			$css .= '.page-header.wpex-supports-mods{background-image:url('. esc_url( $bg ) .');}';
		}

		// Mobile menu toggle color
		if ( $color = wpex_get_mod( 'mobile_menu_icon_color' ) ) {
			$css .= '#mobile-menu .wpex-bars>span, #mobile-menu .wpex-bars>span::before, #mobile-menu .wpex-bars>span::after{background-color:'. $color .';}';
		}
		if ( $color = wpex_get_mod( 'mobile_menu_icon_color_hover' ) ) {
			$css .= '#mobile-menu a:hover .wpex-bars>span, #mobile-menu a:hover .wpex-bars>span::before, #mobile-menu a:hover .wpex-bars>span::after{background-color:'. $color .';}';
		}

		// Vertical header style width
		$width = intval( wpex_get_mod( 'vertical_header_width' ) );

		if ( $width && wpex_has_vertical_header() ) {

			$width = $width . 'px';

			$css .= '@media only screen and ( min-width: 960px ) {';

				$css .= 'body.wpex-has-vertical-header.full-width-main-layout #wrap {';
					$css .= 'padding-left:' . $width  . ';';
				$css .= '}';

				$css .= 'body.wpex-has-vertical-header.rtl.full-width-main-layout #wrap {';
					$css .= 'padding-left: 0;';
					$css .= 'padding-right:' . $width  . ';';
				$css .= '}';

				$css .= 'body.wpex-has-vertical-header #site-header {';
					$css .= 'width:' . $width  . ';';
				$css .= '}';

				$css .= 'body.wpex-has-vertical-header.boxed-main-layout #wrap {';
					$css .= 'padding-left:' . $width  . ';';
				$css .= '}';

				$css .= 'body.wpex-has-vertical-header.boxed-main-layout.rtl #wrap {';
					$css .= 'padding-left:0;';
					$css .= 'padding-right:' . $width  . ';';
				$css .= '}';

			$css .= '}';

		}

		// Footer Widgets background image
		if ( $bg = wpex_get_image_url( wpex_get_mod( 'footer_bg_img' ) ) ) {
			$css .= '#footer{background-image:url(' . esc_url( $bg ) . ');}';
		}

		// Callout background image
		if ( $bg = wpex_get_image_url( wpex_get_mod( 'footer_callout_bg_img' ) ) ) {
			$css .= '#footer-callout-wrap{background-image:url(' . esc_url( $bg ) . ');}';
		}

		/*-----------------------------------------------------------------------------------*/
		/*  - Return CSS
		/*-----------------------------------------------------------------------------------*/
		if ( ! empty( $css ) ) {
			$output .= '/*ADVANCED STYLING CSS*/'. $css;
		}

		// Return output css
		return $output;

	}

}
new AdvancedStyles();