<?php
/**
 * Parses inline styles
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class VCEX_Inline_Style {
	private $style;
	private $add_style;

	/**
	 * Class Constructor.
	 */
	public function __construct( $atts, $add_style ) {
		$this->style = array();
		$this->add_style = $add_style;

		// Loop through shortcode atts and run class methods
		foreach ( $atts as $key => $value ) {
			if ( ! empty( $value ) ) {
				$method = 'parse_' . $key;
				if ( method_exists( $this, $method ) ) {
					$this->$method( $value );
				}
			}
		}

	}

	/**
	 * Display.
	 */
	private function parse_display( $value ) {
		$this->style[] = 'display:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Float.
	 */
	private function parse_float( $value ) {
		if ( 'center' == $value ) {
			$this->style[] = 'margin-right:auto;margin-left:auto;float:none;';
		} else {
			$this->style[] = 'float:' . wp_strip_all_tags( $value ) . ';';
		}
	}

	/**
	 * Width.
	 */
	private function parse_width( $value ) {
		$this->style[] = 'width:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Min-Width.
	 */
	private function parse_min_width( $value ) {
		$this->style[] = 'min-width:' . $this->sanitize_font_size( $value )  . ';';
	}

	/**
	 * Background.
	 */
	private function parse_background( $value ) {
		$this->style[] = 'background:' . $value . ';';
	}

	/**
	 * Background Image.
	 */
	private function parse_background_image( $value ) {
		$this->style[] = 'background-image:url(' . esc_url( $value ) . ');';
	}

	/**
	 * Background Position.
	 */
	private function parse_background_position( $value ) {
		$this->style[] = 'background-position:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Background Color.
	 */
	private function parse_background_color( $value ) {
		$value = 'none' == $value ? 'transparent' : $value;
		$this->style[] = 'background-color:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border.
	 */
	private function parse_border( $value ) {
		$value = 'none' == $value ? '0' : $value;
		$this->style[] = 'border:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border: Color.
	 */
	private function parse_border_color( $value ) {
		$value = 'none' == $value ? 'transparent' : $value;
		$this->style[] = 'border-color:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border: Bottom Color.
	 */
	private function parse_border_bottom_color( $value ) {
		$value = 'none' == $value ? 'transparent' : $value;
		$this->style[] = 'border-bottom-color:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border Width.
	 */
	private function parse_border_width( $value ) {
		$this->style[] = 'border-width:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border Style.
	 */
	private function parse_border_style( $value ) {
		$this->style[] = 'border-style:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Border: Top Width.
	 */
	private function parse_border_top_width( $value ) {
		$this->style[] = 'border-top-width:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Border: Bottom Width.
	 */
	private function parse_border_bottom_width( $value ) {
		$this->style[] = 'border-bottom-width:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Margin.
	 */
	private function parse_margin( $value ) {

		if ( $this->parse_trbl_property( $value, 'margin' ) ) {
			return;
		}

		$value          = ( 'none' == $value ) ? '0' : $value;
		$value          = is_numeric( $value ) ? $value  . 'px' : $value;
		$this->style[]  = 'margin:' . wp_strip_all_tags( $value ) . ';';

	}

	/**
	 * Margin: Right.
	 */
	private function parse_margin_right( $value ) {
		$this->style[] = 'margin-right:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Margin: Left.
	 */
	private function parse_margin_left( $value ) {
		$this->style[] = 'margin-left:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Margin: Top.
	 */
	private function parse_margin_top( $value ) {
		$this->style[] = 'margin-top:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Margin: Bottom.
	 */
	private function parse_margin_bottom( $value ) {
		$this->style[] = 'margin-bottom:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Padding.
	 */
	private function parse_padding( $value ) {

		if ( $this->parse_trbl_property( $value, 'padding' ) ) {
			return;
		}

		$value = 'none' == $value ? '0' : $value;
		$value = is_numeric( $value ) ? $value  . 'px' : $value;
		$this->style[] = 'padding:' . wp_strip_all_tags( $value ) . ';';

	}

	/**
	 * Padding: Top.
	 */
	private function parse_padding_top( $value ) {
		$this->style[] = 'padding-top:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Padding: Bottom.
	 */
	private function parse_padding_bottom( $value ) {
		$this->style[] = 'padding-bottom:' .  $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Padding: Left.
	 */
	private function parse_padding_left( $value ) {
		$this->style[] = 'padding-left:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Padding: Right.
	 */
	private function parse_padding_right( $value ) {
		$this->style[] = 'padding-right:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Font-Size.
	 */
	private function parse_font_size( $value ) {
		if ( $value && strpos( $value, '|' ) === false ) {
			if ( $value = $this->sanitize_font_size( $value ) ) {
				$this->style[] = 'font-size:' . $value  . ';';
			}
		}
	}

	/**
	 * Font Weight.
	 */
	private function parse_font_weight( $value ) {
		if ( 'normal' == $value ) {
			$value = '400';
		} elseif ( 'semibold' == $value ) {
			$value = '600';
		} elseif ( 'bold' == $value ) {
			$value = '700';
		} elseif ( 'bolder' == $value ) {
			$value = '900';
		} else {
			$value = wp_strip_all_tags( $value );
		}
		$this->style[] = 'font-weight:' . $value  . ';';
	}

	/**
	 * Font Family (exclusive to Total theme)
	 */
	private function parse_font_family( $value ) {
		if ( function_exists( 'wpex_sanitize_font_family' ) && $value = wpex_sanitize_font_family( $value ) ) {
			$value = str_replace( '"', "'", $value );
			$this->style[] = 'font-family:' . wp_strip_all_tags( $value ) . ';';
		}
	}

	/**
	 * Color.
	 */
	private function parse_color( $value ) {
		$this->style[] = 'color:' . wp_strip_all_tags( $value )  . ';';
	}

	/**
	 * Opacity.
	 */
	private function parse_opacity( $value ) {
		if ( ! is_numeric( $value ) || $value > 1 ) {
			return;
		}
		$this->style[] = 'opacity:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Text Align.
	 */
	private function parse_text_align( $value ) {
		if ( 'textcenter' == $value ) {
			$value = 'center';
		} elseif ( 'textleft' == $value ) {
			$value = 'left';
		} elseif ( 'textright' == $value ) {
			$value = 'right';
		}
		if ( $value ) {
			$this->style[] = 'text-align:' . $value . ';';
		}
	}

	/**
	 * Text Transform.
	 */
	private function parse_text_transform( $value ) {
		$allowed_values = array(
			'none',
			'capitalize',
			'uppercase',
			'lowercase',
			'initial',
			'inherit'
		);
		if ( ! in_array( $value, $allowed_values ) ) {
			return;
		}
		$this->style[] = 'text-transform:' .  $value . ';';
	}

	/**
	 * Letter Spacing.
	 */
	private function parse_letter_spacing( $value ) {
		if ( strpos( $value, 'px' ) || strpos( $value, 'em' ) || strpos( $value, 'vmin' ) || strpos( $value, 'vmax' ) ) {
			$value = wp_strip_all_tags( $value );
		} else {
			$value = absint( $value ) . 'px';
		}
		$this->style[] = 'letter-spacing:' . $value . ';';
	}

	/**
	 * Line-Height.
	 */
	private function parse_line_height( $value ) {
		$this->style[] = 'line-height:' . wp_strip_all_tags( $value ) . ';';
	}

	/**
	 * Line-Height with px sanitize.
	 */
	private function parse_line_height_px( $value ) {
		$this->style[] = 'line-height:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Height.
	 */
	private function parse_height( $value ) {
		$this->style[] = 'height:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Height with px sanitize.
	 */
	private function parse_height_px( $value ) {
		$this->style[] = 'height:' . $this->sanitize_px( $value )  . ';';
	}

	/**
	 * Min-Height.
	 */
	private function parse_min_height( $value ) {
		$this->style[] = 'min-height:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Border Radius.
	 */
	private function parse_border_radius( $value ) {
		if ( 'none' == $value ) {
			$value = '0';
		} elseif ( strpos( $value, 'px' ) ) {
			$value = wp_strip_all_tags( $value );
		} elseif ( strpos( $value, '%' ) ) {
			if ( '50%' == $value ) {
				$value = wp_strip_all_tags( $value );
			} else {
				$value = wp_strip_all_tags( str_replace( '%', 'px', $value ) );
			}
		} else {
			$value = intval( $value ) .'px';
		}
		$this->style[] = 'border-radius:' . $value  . ';';
	}

	/**
	 * Position: Top.
	 */
	private function parse_top( $value ) {
		$this->style[] = 'top:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Position: Bottom.
	 */
	private function parse_bottom( $value ) {
		$this->style[] = 'bottom:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Position: Right.
	 */
	private function parse_right( $value ) {
		$this->style[] = 'right:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Position: Left.
	 */
	private function parse_left( $value ) {
		$this->style[] = 'left:' . $this->sanitize_px_pct( $value ) . ';';
	}

	/**
	 * Style.
	 */
	private function parse_font_style( $value ) {
		$this->style[] = 'font-style:' . wp_strip_all_tags( $value )  . ';';
	}

	/**
	 * Text Decoration.
	 */
	private function parse_text_decoration( $value ) {
		$this->style[] = 'text-decoration:' . wp_strip_all_tags( $value )  . ';';
	}

	/**
	 * Italic.
	 */
	private function parse_italic( $value ) {
		if ( 'true' ==  $value || 'yes' == $value ) {
			$this->style[] = 'font-style:italic;';
		}
	}

	/**
	 * Animation delay.
	 */
	private function parse_animation_delay( $value ) {
		$this->style[] = 'animation-delay:' . floatval( $value ) . 's;';
	}

	/**
	 * Transition Speed.
	 */
	private function parse_transition_speed( $value ) {
		$this->style[] = 'transition-duration:' . floatval( $value ) . 's;';
	}

	/**
	 * Parse top/right/bottom/left fields.
	 */
	private function parse_trbl_property( $value, $property ) {

		if ( ! function_exists( 'vcex_parse_multi_attribute' ) ) {
			return;
		}

		if ( false !== strpos( $value, ':' ) && $values = vcex_parse_multi_attribute( $value ) ) {

			// All values are the same
			if ( isset( $values['top'] )
				&& count( $values ) == 4
				&& count( array_unique( $values ) ) <= 1
			) {
				$value          = $values['top'];
				$value          = ( 'none' == $value ) ? '0' : $value;
				$value          = is_numeric( $value ) ? $value  . 'px' : $value;
				$this->style[]  = $property . ':' . $value . ';';
				return true;
			}

			// Values are different
			foreach ( $values as $k => $v ) {

				if ( 0 == $v ) {
					$v = '0px'; // 0px fix
				}

				if ( ! empty( $v ) ) {

					$method = 'parse_' . $property . '_' . $k;
					if ( method_exists( $this, $method ) ) {
						$this->$method( $v );
					}

				}

			}

			return true;

		}

	}

	/**
	 * Sanitize px-pct.
	 */
	private function sanitize_px_pct( $input ) {
		if ( 'none' == $input || '0px' == $input ) {
			return '0';
		} elseif ( strpos( $input, '%' ) ) {
			return wp_strip_all_tags( $input );
		} elseif ( $input = floatval( $input ) ) {
			return wp_strip_all_tags( $input ) . 'px';
		}
	}

	/**
	 * Sanitize font-size.
	 */
	private function sanitize_font_size( $input ) {
		if ( strpos( $input, 'px' ) || strpos( $input, 'em' ) || strpos( $input, 'vw' ) || strpos( $input, 'vmin' ) || strpos( $input, 'vmax' ) ) {
			$input = wp_strip_all_tags( $input );
		} else {
			$input = absint( $input ) . 'px';
		}
		if ( $input != '0px' && $input != '0em' ) {
			return wp_strip_all_tags( $input );
		}
		return '';
	}

	/**
	 * Sanitize px.
	 */
	private function sanitize_px( $input ) {
		if ( 'none' == $input ) {
			return '0';
		} else {
			return floatval( $input ) . 'px';
		}
	}

	/**
	 * Returns the styles.
	 */
	public function return_style() {
		if ( ! empty( $this->style ) ) {
			$this->style = implode( false, $this->style );
			if ( $this->add_style ) {
				return ' style="' . esc_attr( $this->style )  . '"';
			} else {
				return esc_attr( $this->style );
			}
		} else {
			return null;
		}
	}

} // End Class