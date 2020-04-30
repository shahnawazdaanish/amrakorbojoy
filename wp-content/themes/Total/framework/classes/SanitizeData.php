<?php
/**
 * Sanitize inputted data
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

class SanitizeData {

	/**
	 * Parses data
	 *
	 * @since 2.0.0
	 */
	public function parse_data( $input, $type ) {
		$type = str_replace( '-', '_', $type );
		if ( method_exists( $this, $type ) ) {
			return $this->$type( $input );
		} else {
			return $input;
		}
	}

	/**
	 * URL
	 *
	 * @since 4.8
	 */
	public function url( $input ) {
		return esc_url( $input );
	}

	/**
	 * Text
	 *
	 * @since 4.8
	 */
	public function text( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Text Field
	 *
	 * @since 4.8
	 */
	public function text_field( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Textarea
	 *
	 * @since 4.8
	 */
	public function textarea( $input ) {
		return wp_kses_post( $input );
	}

	/**
	 * Boolean
	 *
	 * @since 2.0.0
	 */
	public function boolean( $input ) {
		if ( ! $input ) {
			return false;
		}
		if ( 'true' == $input || 'yes' == $input ) {
			return true;
		}
		if ( 'false' == $input || 'no' == $input ) {
			return false;
		}
	}

	/**
	 * Pixels
	 *
	 * @since 2.0.0
	 */
	public function px( $input ) {
		if ( 'none' == $input ) {
			return '0';
		} else {
			return floatval( $input ) . 'px'; // Not sure why we used floatval but lets leave it incase
		}
	}

	/**
	 * Font Size
	 *
	 * @since 2.0.0
	 */
	public function font_size( $input ) {
		return wpex_sanitize_font_size( $input );
	}

	/**
	 * Font Weight
	 *
	 * @since 2.0.0
	 */
	public function font_weight( $input ) {
		if ( 'normal' == $input ) {
			return '400';
		} elseif ( 'semibold' == $input ) {
			return '600';
		} elseif ( 'bold' == $input ) {
			return '700';
		} elseif ( 'bolder' == $input ) {
			return '900';
		} else {
			return esc_html( $input );
		}
	}

	/**
	 * Hex Color
	 *
	 * @since 2.0.0
	 */
	public function hex_color( $input ) {
		if ( ! $input ) {
			return null;
		} elseif ( 'none' == $input ) {
			return 'transparent';
		} elseif ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $input ) ) {
			return $input;
		} else {
			return null;
		}
	}

	/**
	 * Border Radius
	 *
	 * @since 2.0.0
	 */
	public function border_radius( $input ) {
		if ( 'none' == $input ) {
			return '0';
		} elseif ( strpos( $input, 'px' ) ) {
			return $input;
		} elseif ( strpos( $input, '%' ) ) {
			if ( '50%' == $input ) {
				return $input;
			} else {
				return str_replace( '%', 'px', $input );
			}
		} else {
			return intval( $input ) .'px';
		}
	}

	/**
	 * Pixel or Percent
	 *
	 * @since 2.0.0
	 */
	public function px_pct( $input ) {
		if ( 'none' == $input || '0px' == $input ) {
			return '0';
		} elseif ( strpos( $input, '%' ) ) {
			return wp_strip_all_tags( $input );
		} elseif ( $input = floatval( $input ) ) {
			return wp_strip_all_tags( $input ) .'px';
		}
	}

	/**
	 * Opacity
	 *
	 * @since 2.0.0
	 */
	public function opacity( $input ) {
		if ( ! is_numeric( $input ) || $input > 1 ) {
			return;
		} else {
			return wp_strip_all_tags( $input );
		}
	}

	/**
	 * HTML
	 *
	 * @since 3.3.0
	 */
	public function html( $input ) {
		return wp_kses_post( $input );
	}

	/**
	 * Image
	 *
	 * @since 2.0.0
	 */
	public function img( $input ) {
		return wp_kses( $input, array(
			'img' => array(
				'src'    => array(),
				'alt'    => array(),
				'srcset' => array(),
				'id'     => array(),
				'class'  => array(),
				'height' => array(),
				'width'  => array(),
				'data'   => array(),
			),
		) );
	}

	/**
	 * Image from setting
	 *
	 * @since 3.5.0
	 */
	public function image_src_from_mod( $input ) {
		return wpex_get_image_url( $input );
	}

	/**
	 * Background Style
	 *
	 * @since 3.5.0
	 */
	public function background_style_css( $input ) {
		if ( $input == 'stretched' ) {
			return '-webkit-background-size: cover;
					-moz-background-size: cover;
					-o-background-size: cover;
					background-size: cover;
					background-position: center center;
					background-attachment: fixed;
					background-repeat: no-repeat;';
		} elseif ( $input == 'cover' ) {
			return 'background-position: center center;
					-webkit-background-size: cover;
					-moz-background-size: cover;
					-o-background-size: cover;
					background-size: cover;';
		} elseif ( $input == 'repeat' ) {
			return 'background-repeat:repeat;';
		} elseif ( $input == 'repeat-y' ) {
			return 'background-position: center center;background-repeat:repeat-y;';
		} elseif ( $input == 'fixed' ) {
			return 'background-repeat: no-repeat; background-position: center center; background-attachment: fixed;';
		} elseif ( $input == 'fixed-top' ) {
			return 'background-repeat: no-repeat; background-position: center top; background-attachment: fixed;';
		} elseif ( $input == 'fixed-bottom' ) {
			return 'background-repeat: no-repeat; background-position: center bottom; background-attachment: fixed;';
		} else {
			return 'background-repeat:'. $input .';';
		}
	}

	/**
	 * Embed URL
	 *
	 * @since 2.0.0
	 */
	public function embed_url( $url ) {
		return wpex_get_video_embed_url( $url );
	}

	/**
	 * Google Map Embed
	 *
	 * @since 4.8
	 */
	public function google_map( $input ) {
		return wp_kses( $input, array(
			'iframe' => array(
				'src'             => array(),
				'height'          => array(),
				'width'           => array(),
				'frameborder'     => array(),
				'style'           => array(),
				'allowfullscreen' => array(),
			),
		) );
	}

} // End Class