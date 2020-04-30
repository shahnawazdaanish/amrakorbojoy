<?php
/**
 * Categories + Title Bottom Visible
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
if ( 'outside_link' != $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Get post title
$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title(); ?>

<div class="overlay-cats-title-btm-v theme-overlay">
	<?php if ( $taxonomy ) {
		wpex_list_post_terms( array(
			'taxonomy' => $taxonomy,
			'before'   => '<div class="overlay-cats-title-btm-v-cats clr">',
			'after'    => '</div>',
			'instance' => 'overlay_categories_title-bottom_visible',
		) );
	} ?>
	<a href="<?php the_permalink(); ?>" class="overlay-cats-title-btm-v-title entry-title"><?php echo esc_html( $title ); ?></a>
</div>