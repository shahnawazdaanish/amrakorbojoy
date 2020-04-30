<?php
/**
 * Useful global functions for the staff
 *
 * @package Total WordPress Theme
 * @subpackage Staff Functions
 * @version 5.0
 */

/**
 * Returns staff entry blocks
 *
 * @since 2.1.0
 */
function wpex_staff_entry_blocks() {

	// Defaults
	$defaults = array( 'media', 'title', 'content', 'read_more' );

	// Get layout blocks
	$blocks = wpex_get_mod( 'staff_entry_composer' ); // @todo rename this to staff_entry_blocks

	// If blocks are 100% empty return defaults
	$blocks = $blocks ? $blocks : $defaults;

	// Convert blocks to array so we can loop through them
	if ( ! is_array( $blocks ) ) {
		$blocks = explode( ',', $blocks );
	}

	// Apply filters to entry layout blocks
	$blocks = apply_filters( 'wpex_staff_entry_blocks', $blocks, 'front-end' );

	// Return blocks
	return $blocks;

}

/**
 * Returns staff post blocks
 *
 * @since 2.1.0
 */
function wpex_staff_single_blocks() {

	// Defaults
	$defaults = array( 'content', 'related' );

	// Get layout blocks
	$blocks = wpex_get_mod( 'staff_post_composer' );

	// If blocks are 100% empty return defaults
	$blocks = $blocks ? $blocks : $defaults;

	// Convert blocks to array so we can loop through them
	if ( ! is_array( $blocks ) ) {
		$blocks = explode( ',', $blocks );
	}

	// Apply filters to entry layout blocks
	$blocks = apply_filters( 'wpex_staff_single_blocks', $blocks, 'front-end' );

	// Return blocks
	return $blocks;

}

/**
 * Returns staff single meta sections
 *
 * @since 3.5.0
 */
function wpex_staff_single_meta_sections() {

	// Default sections
	$sections = array( 'date', 'categories' );

	// Apply filters for easy modification
	$sections = apply_filters( 'wpex_staff_single_meta_sections', $sections );

	// Turn into array if string
	if ( $sections && ! is_array( $sections ) ) {
		$sections = explode( ',', $sections );
	}

	// Return sections
	return $sections;

}

/**
 * Returns correct thumbnail HTML for the staff entries
 *
 * @since 2.0.0
 */
function wpex_get_staff_entry_thumbnail( $loop = 'archive', $args = array() ) {
	$size = 'archive' === $loop ? 'staff_entry' : 'staff_related';
	$defaults = array(
		'size'  => $size,
		'class' => 'staff-entry-img',
	);
	$args = wp_parse_args( $args, $defaults );
	return wpex_get_post_thumbnail( apply_filters( 'wpex_get_staff_entry_thumbnail_args', $args ) );
}

/**
 * Returns correct thumbnail HTML for the staff posts
 *
 * @since 2.0.0
 */
function wpex_get_staff_post_thumbnail( $args = '' ) {

	// Define thumbnail args
	$defaults = array(
		'size'          => 'staff_post',
		'class'         => 'staff-single-media-img',
		'schema_markup' => true,
	);

	// Parse arguments
	$args = wp_parse_args( $args, $defaults );

	// Apply filters
	$args = apply_filters( 'wpex_get_staff_post_thumbnail_args', $args );

	// Return thumbanil
	return wpex_get_post_thumbnail( $args );

}

/**
 * Returns correct classes for the staff wrap
 *
 * @since 1.5.3
 */
function wpex_get_staff_wrap_classes() {

	// Define main classes
	$classes = array( 'wpex-row', 'clr' );

	// Get grid style
	$grid_style = wpex_get_mod( 'staff_archive_grid_style' );
	$grid_style = $grid_style ? $grid_style : 'fit-rows';

	// Add grid style
	$classes[] = 'staff-'. $grid_style;

	// Add gap
	if ( $gap = wpex_get_mod( 'staff_archive_grid_gap' ) ) {
		$classes[] = 'gap-' . $gap;
	}

	// Sanitize
	$classes = array_map( 'esc_attr', $classes );

	// Apply filters
	$classes = apply_filters( 'wpex_staff_wrap_classes', $classes );

	// Turn into space seperated string
	$classes = implode( ' ', $classes );

	// Return
	return $classes;

}

/**
 * Returns staff archive columns
 *
 * @since 2.0.0
 */
function wpex_staff_archive_columns() {
	return wpex_get_mod( 'staff_entry_columns', '3' );
}

/**
 * Returns correct classes for the staff grid
 *
 * @since Total 1.5.2
 */
if ( ! function_exists( 'wpex_staff_column_class' ) ) {
	function wpex_staff_column_class( $query ) {
		if ( 'related' == $query ) {
			return wpex_grid_class( wpex_get_mod( 'staff_related_columns', '3' ) );
		} else {
			return wpex_grid_class( wpex_get_mod( 'staff_entry_columns', '3' ) );
		}
	}
}

/**
 * Checks if match heights are enabled for the staff
 *
 * @since 1.5.3
 */
if ( ! function_exists( 'wpex_staff_match_height' ) ) {
	function wpex_staff_match_height() {
		$grid_style = wpex_get_mod( 'staff_archive_grid_style', 'fit-rows' ) ? wpex_get_mod( 'staff_archive_grid_style', 'fit-rows' ) : 'fit-rows';
		$columns    = wpex_get_mod( 'staff_entry_columns', '4' ) ? wpex_get_mod( 'staff_entry_columns', '4' ) : '4';
		if ( 'fit-rows' == $grid_style && wpex_get_mod( 'staff_archive_grid_equal_heights' ) && $columns > '1' ) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * Staff Overlay
 *
 * Function is deprecated and no longer used => Keep for fallback
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_get_staff_overlay' ) ) {
	function wpex_get_staff_overlay( $id = NULL ) {
		$post_id  = $id ? $id : get_the_ID();
		$position = get_post_meta( get_the_ID(), 'wpex_staff_position', true );
		if ( ! $position ) {
			return;
		} ?>
		<div class="staff-entry-position"><span><?php echo esc_html( $position ); ?></span></div>
		<?php
	}
}

/**
 * Outputs the staff social options
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_get_staff_social' ) ) {
	function wpex_get_staff_social( $atts = NULL ) {

		// Extract staff social args
		extract( shortcode_atts( array(
			'before'        => '',
			'after'         => '',
			'post_id'       => '',
			'font_size'     => '',
			'css_animation' => '',
			'css'           => '',
			'inline_style'  => '',
			'icon_margin'   => '',
			'show_icons'    => wp_validate_boolean( wpex_get_mod( 'staff_social_show_icons', true ) ),
			'style'         => wpex_get_mod( 'staff_social_default_style', 'minimal-round' ),
			'link_target'   => wpex_get_mod( 'staff_social_link_target', 'blank' ),
		), $atts, 'staff_social' ) );

		// Define output
		$output = '';

		// Get social profiles array
		$profiles = wpex_staff_social_array();

		// Define post_id
		$post_id = $post_id ? $post_id : get_the_ID();

		// Convert icon margin to style
		if ( $icon_margin && function_exists( 'vcex_inline_style' ) ) {
			$icon_margin = vcex_inline_style( array(
				'margin' => $icon_margin,
			), false );
		}

		// Parse style to return correct classname
		$style = wpex_get_social_button_class( $style );
		$style = $style ? ' '. $style : '';

		// Wrap classes
		$wrap_classes = 'staff-social wpex-social-btns clr';
		$wrap_style   = '';
		if ( $css ) {
			$wrap_classes .= ' ' . vc_shortcode_custom_css_class( $css );
		}
		if ( $css_animation && 'none' != $css_animation && function_exists( 'vcex_get_css_animation' ) ) {
			$wrap_classes .= ' ' . vcex_get_css_animation( $css_animation );
		}

		// Font size
		$font_size  = $font_size ? wpex_sanitize_data( $font_size, 'font_size' ) : '';
		$wrap_style .= $font_size ? 'font-size:' . esc_attr( $font_size ) . ';' : '';

		if ( $inline_style ) {
			$wrap_style .= $inline_style;
		}

		// Wrap attributes
		$wrap_attrs = array(
			'class' => esc_attr( $wrap_classes ),
			'style' => $wrap_style,
		);

		// Before output
		if ( $before ) {
			$output .= $before;
		}

		// Start output
		$output .= '<div '. wpex_parse_attrs( $wrap_attrs ) .'>';

			// Loop through social options
			foreach ( $profiles as $profile ) :

				// Get meta
				$meta = $profile['meta'];

				// Display link if one exists
				if ( $meta_value = get_post_meta( $post_id, $meta, true ) ) :

					// Add "mailto" for emails
					if ( 'wpex_staff_email' == $meta && is_email( $meta_value ) ) {
						$url = 'mailto:' . $meta_value;
					}

					// Add "callto" to skype
					elseif ( 'wpex_staff_skype' == $meta ) {
						if ( strpos( $meta_value, 'skype' ) === false ) {
							$url = str_replace( 'callto:', '', $meta_value );
							$url = 'callto:' . $url;
						}
					}

					// Add "tel" for phones
					elseif ( 'wpex_staff_phone_number' === $meta ) {
						if ( strpos( $meta_value, 'callto' ) === false ) {
							$url = str_replace( 'tel:', '', $meta_value );
							$url = 'tel:' . $url;
						}
					} else {
						$url = esc_url( $meta_value );
					}

					if ( ! $show_icons ) {
						$style = '';
					}

					// Link attributes
					$link_attrs  = array(
						'href'   => $url,
						'class'  => 'wpex-' . esc_attr( str_replace( '_', '-', $profile['key'] ) ) . $style,
						'target' => ( 'wpex_staff_email' == $meta ) ? '' : $link_target,
						'style'  => $icon_margin,
					);

					$output .= '<a '. wpex_parse_attrs( $link_attrs ) .'>';

						if ( $show_icons ) {

							$output .= '<span class="'. $profile['icon_class'] .'" aria-hidden="true"></span>';

							$output .= '<span class="screen-reader-text">' . esc_attr( $profile['label'] ) . '</span>';

						} else {

							if ( in_array( $profile['key'], array( 'telephone', 'fax', 'phone_number', 'phone', 'email', 'website' ) ) ) {

								$output .= esc_attr( $meta_value );

							} else {

								$output .= esc_attr( $profile['label'] );

							}

						}


					$output .= '</a>';

				endif; // URL check

			endforeach; // End profiles loop

		// End output
		$output .= '</div>';

		// After output
		if ( $after ) {
			$output .= $after;
		}

		// Return output
		return $output;

	}

}