<?php
/**
 * Category Tag
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for outside position
if ( 'outside_link' != $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Get terms
if ( $taxonomy ) {

	// Get terms
	$terms = wp_get_post_terms( get_the_ID(), $taxonomy );

	// Display if we have terms
	if ( $terms ) { ?>

		<div class="overlay-category-tag theme-overlay wpex-clr">

			<?php
			$count = 0;
			foreach ( $terms as $term ) {
				$count++; ?>
				<a href="<?php echo esc_url( get_term_link( $term->term_id, $taxonomy ) ); ?>" class="term-<?php echo esc_attr( $term->slug ); ?> count-<?php echo absint( $count ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php } ?>

		</div>

	<?php }

}