<?php
/**
 * Custom Post Type Entry Readmore
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get post type
$post_type = get_post_type();

// Readmore button classes
$button_args = apply_filters( 'wpex_' . $post_type . '_entry_button_args', array(
	'style' => '',
	'color' => '',
) );

// Readmore text
$text = wpex_get_mod( $post_type . '_readmore_text' );
$text = apply_filters( 'wpex_' . $post_type . '_readmore_link_text', $text );
$text = $text ? $text : esc_html__( 'Read more', 'total' ); ?>

<div class="cpt-entry-readmore-wrap entry-readmore-wrap wpex-clr">
	<a href="<?php wpex_permalink(); ?>" class="<?php echo wpex_get_button_classes( $button_args ); ?>"><?php echo esc_html( $text ); ?></a>
</div>