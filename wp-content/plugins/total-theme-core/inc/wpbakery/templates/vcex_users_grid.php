<?php
/**
 * Visual Composer Users Grid
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.0.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Define output var
$output = '';

// Get shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_users_grid', $atts, $this );

// Get user roles to query
$role__in = array();
if ( ! empty( $atts['role__in'] ) ) {
	$role__in = preg_split( '/\,[\s]*/', $atts['role__in'] );
}

// Query arguments
$args = apply_filters( 'vcex_users_grid_query_args', array(
	'order'    => $atts['order'],
	'orderby'  => $atts['orderby'],
	'role__in' => $role__in,
) );

// Get users
$users = get_users( $args );

// No users, lets bail
if ( ! $users ) {
	return;
}

// Get onclick action (with fallback for old link_to_author_page param)
if ( isset( $atts['link_to_author_page'] ) ) {
	if ( 'true' == $atts['link_to_author_page'] ) {
		$atts['onclick'] = 'author_page';
	} elseif ( 'false' == $atts['link_to_author_page'] ) {
		$atts['onclick'] = 'disable';
	}
}

// Wrap classes
$wrap_classes = array( 'vcex-module', 'vcex-users-grid', 'wpex-row', 'clr' );
if ( 'masonry' == $atts['grid_style'] ) {
	$wrap_classes[] = 'vcex-isotope-grid';
	vcex_enqueue_isotope_scripts();
}
if ( $atts['columns_gap'] ) {
	$wrap_classes[] = 'gap-'. $atts['columns_gap'];
}
if ( $atts['visibility'] ) {
	$wrap_classes[] = $atts['visibility'];
}
if ( $atts['classes'] ) {
	$wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
}
$wrap_classes = implode( ' ', $wrap_classes );
$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_users_grid', $atts );

// Entry classes
$entry_classes = array( 'vcex-users-grid-entry', 'clr' );
if ( 'masonry' == $atts['grid_style'] ) {
	$entry_classes[] = 'vcex-isotope-entry';
}
$entry_classes[] = vcex_get_grid_column_class( $atts );
if ( 'false' == $atts['columns_responsive'] ) {
	$entry_classes[] = 'nr-col';
} else {
	$entry_classes[] = 'col';
}
if ( $atts['css_animation'] && 'none' != $atts['css_animation'] ) {
	$entry_classes[] = vcex_get_css_animation( $atts['css_animation'] );
}
$entry_classes = implode( ' ', $entry_classes );

// Entry CSS wrapper
$entry_css_class = $atts['entry_css'] ? vcex_vc_shortcode_custom_css_class( $atts['entry_css'] ) : '';

// Avatar classes
if ( 'true' == $atts['avatar'] ) {

	$avatar_wrap_classes = 'entry-media clr';

	if ( $atts['avatar_hover_style'] ) {
		$avatar_wrap_classes .= ' '. vcex_image_hover_classes( $atts['avatar_hover_style'] );
	}

}

// Name classes & css
if ( 'true' == $atts['name'] ) {

	$name_classes = 'entry-title clr';

	if ( $atts['name_color'] && 'disable' != $atts['onclick'] ) {
		$name_classes .= ' wpex-hover-inherit-color'; // So when name has custom color the hover is the same
	}

	$name_css = vcex_inline_style( array(
		'color'          => $atts['name_color'],
		'font_size'      => $atts['name_font_size'],
		'font_weight'    => $atts['name_font_weight'],
		'font_family'    => $atts['name_font_family'],
		'margin_bottom'  => $atts['name_margin_bottom'],
		'text_transform' => $atts['name_text_transform'],
	) );

}

// Description CSS
if ( 'true' == $atts['description'] ) {

	$description_css = vcex_inline_style( array(
		'color'          => $atts['description_color'],
		'font_size'      => $atts['description_font_size'],
		'font_weight'    => $atts['description_font_weight'],
		'font_family'    => $atts['description_font_family'],
	) );

}

// Social settings
if ( 'true' == $atts['social_links'] ) {

	$social_links_inline_css = vcex_inline_style( array(
		'padding'   => $atts['social_links_padding'],
		'font_size' => $atts['social_links_size'],
	) );

	$social_links_style = vcex_get_social_button_class( $atts['social_links_style'] );

}

// Display header if enabled
if ( $atts['header'] ) {

	$output .= vcex_get_module_header( array(
		'style'   => $atts['header_style'],
		'content' => $atts['header'],
		'classes' => array( 'vcex-module-heading vcex_users_grid-heading' ),
	) );

}

// Begin output
$output .= '<div class="'. esc_attr( $wrap_classes ) .'"'. vcex_get_unique_id( $atts['unique_id'] ) .'>';

	$counter = 0;

	// Loop through users
	foreach ( $users as $user ) :

		$counter++;

		$author_link = ''; // Reset after each user

		$output .= '<div class="'. esc_attr( $entry_classes ) .' col-'. $counter .'">';

			if ( $entry_css_class ) {

				$output .= '<div class="entry-css-wrap clr '. $entry_css_class .'">';

			}

			// Avatar
			if ( 'true' == $atts['avatar'] ) {

				$output .= '<div class="'. esc_attr( $avatar_wrap_classes ) .'">';

					if ( 'disable' != $atts['onclick'] ) {

						if ( 'author_page' == $atts['onclick'] ) {
							$author_link = get_author_posts_url( $user->ID );
						} else if ( 'user_website' == $atts['onclick'] ) {
							$author_link = $user->user_url;
						}

						if ( $author_link ) {

							$author_link = '<a href="'. esc_url( $author_link ) .'" title="'. esc_attr( $user->display_name ) .'">';

							$output .= $author_link;

						}

					}

					if ( $atts['avatar_meta_field'] ) {

						if ( $avatar = get_user_meta( $user->ID, $atts['avatar_meta_field'], true ) ) {

							if ( is_numeric( $avatar ) && $get_avatar = wp_get_attachment_image( $avatar ) ) {

								$output .= $get_avatar;

							} else {

								$output .= '<img src="'. esc_url( $avatar ) .'" alt="'. esc_attr( $user->display_name ) .'" />';

							}

						}

					} else {

						$output .= get_avatar( $user->ID, $atts['avatar_size'], '', $user->display_name );

					}

					if ( $author_link ) {

						$output .= '</a>';

					}

				$output .= '</div>';

			}


			// Display name
			if ( 'true' == $atts['name'] ) {

				$name_heading_tag = $atts['name_heading_tag'] ? $atts['name_heading_tag'] : 'div';

				$output .= '<'. $name_heading_tag .' class="'. esc_attr( $name_classes ) .'"'. $name_css .'>';

					if ( $author_link ) {

						$output .= $author_link;

					}

					$output .= $user->display_name;

					if ( $author_link ) {

						$output .= '</a>';

					}

				$output .= '</'. $name_heading_tag .'>';

			}


			// Description
			if ( 'true' == $atts['description'] && $description = get_the_author_meta( 'description', $user->ID ) ) {

				$output .= '<div class="entry-excerpt clr" '. $description_css .'>';

					$output .= wpautop( wp_kses_post( $description ) );

				$output .= '</div>';

			}

			// Display social
			if ( 'true' == $atts['social_links'] ) {

				$output .= '<div class="entry-social-links clr"'. $social_links_inline_css .'>';

					$output .= vcex_get_user_social_links( $user->ID, 'icons', array(
						'class' => $social_links_style
					) );

				$output .= '</div>';

			}

			if ( $entry_css_class ) {

				$output .= '</div>';

			}

		$output .= '</div>';

		// Clear counter
		if ( $counter == $atts['columns'] ) {
			$counter = 0;
		}

	// End loop
	endforeach;

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
