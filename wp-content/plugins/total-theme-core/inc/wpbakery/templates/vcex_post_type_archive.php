<?php
/**
 * Visual Composer Post Type Archive
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Store orginal atts value for use in non-builder params
$og_atts = $atts;

// Define entry counter
global $wpex_count;
$wpex_count = ! empty( $og_atts['entry_count'] ) ? $og_atts['entry_count'] : 0;

// Get and extract shortcode attributes
$atts = vcex_vc_map_get_attributes( 'vcex_post_type_archive', $atts, $this );

// Add paged attribute for load more button (used for WP_Query)
if ( ! empty( $og_atts['paged'] ) ) {
	$atts['paged'] = $og_atts['paged'];
}

// Build the WordPress query
$vcex_query = vcex_build_wp_query( $atts );

// Set post to blog
$post_type = $atts['post_type'];
$post_type = ( 'post' == $post_type ) ? 'blog' : $post_type;

// Output posts
if ( $vcex_query->have_posts() ) :

	// Wrapper classes
	$wrap_classes = array( 'vcex-module', 'vcex-post-type-archive', 'clr' );
	if ( $atts['css_animation'] && 'none' != $atts['css_animation'] ) {
   		$wrap_classes[] = vcex_get_css_animation( $atts['css_animation'] );
	}
	if ( $atts['classes'] ) {
	    $wrap_classes[] = vcex_get_extra_class( $atts['classes'] );
	}
	if ( $atts['visibility'] ) {
	    $wrap_classes[] = $atts['visibility'];
	}
	$wrap_classes = implode( ' ', $wrap_classes );
	$wrap_classes = vcex_parse_shortcode_classes( $wrap_classes, 'vcex_post_type_archive', $atts ); ?>

	<div class="<?php echo esc_attr( $wrap_classes ); ?>"<?php vcex_unique_id( $atts['unique_id'] ); ?>>

		<?php
		//Heading
		if ( ! empty( $atts[ 'heading' ] ) ) {
			echo vcex_get_module_header( array(
				'style'   => isset( $atts[ 'header_style' ] ) ? $atts[ 'header_style' ] : '',
				'content' => $atts[ 'heading' ],
				'classes' => array( 'vcex-module-heading vcex_post_type_archive-heading' ),
			) );
		}

		// Get loop top
		get_template_part( 'partials/loop/loop-top', $post_type );

			// Loop through posts
			while ( $vcex_query->have_posts() ) :

				// Get post from query
				$vcex_query->the_post();

				// Create new post object.
				$post = new stdClass();

					// Get content template part
					get_template_part( 'partials/loop/loop', $post_type );

			// End loop
			endwhile;

		// Get loop bottom
		get_template_part( 'partials/loop/loop-bottom', $post_type );

		// Display pagination if enabled
		if ( ( 'true' == $atts['pagination'] || ( 'true' == $atts['custom_query'] && ! empty( $vcex_query->query['pagination'] ) ) )
			&& 'true' != $atts['pagination_loadmore']
		) {

			echo vcex_pagination( $vcex_query, false );

		}

		// Load more button
		if ( 'true' == $atts['pagination_loadmore'] && ! empty( $vcex_query->max_num_pages ) ) {
			vcex_loadmore_scripts();
			$og_atts['entry_count'] = $wpex_count; // Update counter
			echo vcex_get_loadmore_button( 'vcex_post_type_archive', $og_atts, $vcex_query );
		} ?>

	</div>

	<?php
	// Reset the post data to prevent conflicts with WP globals
	wp_reset_postdata(); ?>

<?php
// If no posts are found display message
else : ?>

	<?php
	// Display no posts found error if function exists
	echo vcex_no_posts_found_message( $atts ); ?>

<?php
// End post check
endif; ?>