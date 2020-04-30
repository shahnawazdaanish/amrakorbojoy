<?php
/**
 * Returns the post title
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define title args
$args = array();

// Check if singular
$is_singular = is_singular();
$post_type   = $is_singular ? get_post_type() : '';
$instance    = $post_type ? 'singular_' . $post_type : '';

// Single post markup
if ( 'post' == $post_type ) {
	$blog_single_header = wpex_get_mod( 'blog_single_header', 'custom_text' );
	if ( 'custom_text' == $blog_single_header || 'first_category' == $blog_single_header ) {
		$args['html_tag'] = 'span';
		$args['schema_markup'] = '';
	}
}

// Singular CPT
elseif ( $is_singular && ! in_array( $post_type, array( 'page', 'attachment' ) ) ) {
	$args['html_tag'] = 'span';
	$args['schema_markup'] = '';
}

// Apply filters
$args = apply_filters( 'wpex_page_header_title_args', $args, $instance );

// Parse args to prevent empty attributes and extract
extract( wp_parse_args( $args, array(
	'html_tag'      => 'h1',
	'string'        => wpex_title(),
	'schema_markup' => wpex_get_schema_markup( 'headline' )
) ) );

// If string is empty return
if ( empty( $string ) ) {
	return;
}

// Sanitize html tag
$html_tag_escaped = wp_strip_all_tags( $html_tag );

// Output title
echo '<' . $html_tag_escaped . ' class="page-header-title wpex-clr"' . $schema_markup . '>';

	echo '<span>' . do_shortcode( wp_kses_post( $string ) ) . '</span>';

echo '</' . $html_tag_escaped . '>';