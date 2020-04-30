<?php
/**
 * Used for custom site backgrounds
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.6.5
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class SiteBackgrounds {

	/**
	 * Main constructor
	 *
	 * @since 4.6.5
	 */
	public function __construct() {
		add_filter( 'wpex_head_css', array( $this, 'generate' ), 999 );
	}

	/**
	 * Generates the CSS output
	 *
	 * @since 4.6.5
	 */
	public function generate( $output ) {

		// Vars
		$css = $add_css = '';

		// Global vars
		$css     = '';
		$image   = wpex_get_mod( 't_background_image' ); // converted to background_img in 4.3 to prevent conflict with WP
		$style   = wpex_get_mod( 't_background_style' );
		$pattern = wpex_get_mod( 't_background_pattern' );
		$post_id = wpex_get_current_post_id();

		// Single post vars
		if ( $post_id ) {

			// Color
			$single_color = get_post_meta( $post_id, 'wpex_page_background_color', true );
			$single_color = str_replace( '#', '', $single_color );

			// Image
			$single_image = get_post_meta( $post_id, 'wpex_page_background_image_redux', true );
			if ( $single_image ) {
				if ( is_array( $single_image ) ) {
					$single_image = ( ! empty( $single_image['url'] ) ) ? $single_image['url'] : '';
				} else {
					$single_image = $single_image;
				}
			} else {
				$single_image = get_post_meta( $post_id, 'wpex_page_background_image', true );
			}

			// Background style
			$single_style = get_post_meta( $post_id, 'wpex_page_background_image_style', true );

		}

		/*-----------------------------------------------------------------------------------*/
		/*  - Sanitize Data
		/*-----------------------------------------------------------------------------------*/

		$color = ! empty( $single_color ) ? $single_color : '';
		$style = ( ! empty( $single_image ) && ! empty( $single_style ) ) ? $single_style : $style;
		$image = ! empty( $single_image ) ? $single_image : $image;

		$settings = apply_filters( 'wpex_body_background_settings', array(
			'color'   => $color,
			'image'   => $image,
			'style'   => $style,
			'pattern' => $pattern,
		) );

		if ( ! $settings ) {
			return;
		}

		extract( $settings );

		$color = str_replace( '#', '', $color ); //@todo remove to support rgba?
		
		if ( $image && is_numeric( $image ) ) {
			$image = wp_get_attachment_image_src( $image, 'full' );
			$image = isset( $image[0] ) ? $image[0] : '';
		}

		$style = $style ? $style : 'stretched';

		/*-----------------------------------------------------------------------------------*/
		/*  - Generate CSS
		/*-----------------------------------------------------------------------------------*/

		// Color
		if ( $color ) {
			
			$css .= 'background-color:#' . $color . '!important;';

			if ( wpex_footer_has_reveal() ) {
				$output .= '.footer-has-reveal #main{ background-color:#' . $color . '!important;}';
			}

		}
		
		// Image
		if ( $image && ! $pattern ) {
			$css .= 'background-image:url('. $image .') !important;';
			$css .= wpex_sanitize_data( $style, 'background_style_css' );
		}
		
		// Pattern
		if ( $pattern ) {
			$patterns = wpex_get_background_patterns();
			if ( isset( $patterns[$pattern] ) ) {
				$css .= 'background-image:url('. $patterns[$pattern]['url'] .'); background-repeat:repeat;';
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/*  - Return $css
		/*-----------------------------------------------------------------------------------*/
		if ( ! empty( $css ) ) {
			$css = '/*SITE BACKGROUND*/body{'. $css .'}';
			$output .= $css;
		}

		// Return output css
		return $output;

	}

}
new SiteBackgrounds();