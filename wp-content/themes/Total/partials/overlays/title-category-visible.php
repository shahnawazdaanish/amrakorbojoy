<?php
/**
 * Title Category Visible Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for inside position
if ( 'inside_link' != $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Get post title
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title(); ?>

<div class="overlay-title-category-visible theme-overlay textcenter">
	<div class="overlay-table clr">
		<div class="overlay-table-cell clr">
			<div class="overlay-title"><?php echo esc_html( $title ); ?></div>
			<?php if ( $taxonomy ) {
				wpex_list_post_terms( array(
					'taxonomy'   => $taxonomy,
					'before'     => '<div class="overlay-terms clr">',
					'after'      => '</div>',
					'show_links' => false,
					'instance'   => 'overlay_title-category-hover',
				) );
			} ?>
		</div>
	</div>
</div>