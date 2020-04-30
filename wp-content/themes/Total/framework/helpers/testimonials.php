<?php
/**
 * Helper functions for the testimonials post type
 *
 * @package Total WordPress Theme
 * @subpackage Testimonials Functions
 * @version 4.9.8
 */

/**
 * Returns correct thumbnail HTML for the testimonials entries
 *
 * @since 2.0.0
 */
function wpex_get_testimonials_entry_thumbnail( $args = array() ) {
	$defaults = array(
        'size'  => 'testimonials_entry',
        'class' => 'testimonials-entry-img',
    );
    $args = wp_parse_args( $args, $defaults );
    return wpex_get_post_thumbnail( $args );
}

/**
 * Returns testimonials archive columns
 *
 * @since 2.0.0
 */
function wpex_testimonials_archive_columns() {
	return wpex_get_mod( 'testimonials_entry_columns', '4' );
}

/**
 * Returns correct classes for the testimonials archive wrap
 *
 * @since 2.0.0
 */
function wpex_get_testimonials_wrap_classes() {

	// Define main classes
	$classes = array( 'wpex-row', 'clr' );

	// Get grid style
	if ( 'masonry' == wpex_get_mod( 'testimonials_archive_grid_style', 'fit-rows' ) ) {
		$classes[] = 'testimonials-masonry';
	}

	// Add gap
	if ( $gap = wpex_get_mod( 'testimonials_archive_grid_gap' ) ) {
		$classes[] = 'gap-' . $gap;
	}

	// Sanitize
	$classes = array_map( 'esc_attr', $classes );

	// Apply filters
	$classes = apply_filters( 'wpex_testimonials_wrap_classes', $classes );

	// Turn array into string
	$classes = implode( ' ', $classes );

	// Return
	return $classes;

}