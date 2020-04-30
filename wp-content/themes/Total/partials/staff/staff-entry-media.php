<?php
/**
 * Staff entry media template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! has_post_thumbnail() ) {
	return;
}

$wpex_loop = isset( $wpex_loop ) ? $wpex_loop : 'archive';

// Classes
$classes = array( 'staff-entry-media', 'clr' );
if ( $overlay = wpex_overlay_classes() ) {
	$classes[] = $overlay;
}
if ( $img_hover = wpex_get_entry_image_animation_classes() ) {
	$classes[] = $img_hover;
}

// Check if links are enabled
$has_links = wpex_get_mod( 'staff_links_enable', true ); ?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

	<?php
	// Open link around staff members if enabled
	if ( $has_links ) : ?>

		<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="staff-entry-media-link">

	<?php endif; ?>

		<?php echo wpex_get_staff_entry_thumbnail( $wpex_loop ); ?>

		<?php wpex_entry_media_after( 'staff' ); ?>

		<?php wpex_overlay( 'inside_link' ); ?>

	<?php if ( $has_links ) echo '</a>'; ?>

	<?php wpex_overlay( 'outside_link' ); ?>

</div><!-- .staff-entry-media -->