<?php
/**
 * Lightbox Buttons Overlay
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only used for outside position
if ( 'outside_link' != $position ) {
	return;
}

wpex_enqueue_lightbox_scripts();

// Lightbox
$lightbox_link = ! empty( $args['lightbox_link'] ) ? $args['lightbox_link'] : wpex_get_lightbox_image();
$lightbox_data = '';
if ( ! empty( $args['lightbox_data'] ) ) {
	$lightbox_data = is_array( $args['lightbox_data'] ) ? ' ' . implode( ' ', $args['lightbox_data'] ) : $args['lightbox_data'];
}
$lightbox_class = 'wpex-lightbox'; // can't use galleries in this overlay style due to duplicate links

// Custom Link
$link = isset( $args['overlay_link'] ) ? $args['overlay_link'] : wpex_get_permalink();

// Define link target
$target = '';
if ( isset( $args['link_target'] ) && ( 'blank' == $args['link_target'] || '_blank' == $args['link_target'] ) ) {
    $target = 'blank';
}

// Apply filters
$link   = apply_filters( 'wpex_lightbox_buttons_button_overlay_link', $link, $args );
$target = apply_filters( 'wpex_button_overlay_target', $target, $args );

// Sanitize Data
$link          =  $link;
$target        = 'blank' == $target ? ' target="_blank"' : '';
$lightbox_link = $lightbox_link; ?>

<div class="overlay-view-lightbox-buttons overlay-hide theme-overlay textcenter">

	<div class="overlay-table clr">

		<div class="overlay-table-cell clr">

			<?php
			$button_one_attrs = array(
				'href'   => esc_url( $lightbox_link ),
				'class'  => esc_attr( $lightbox_class ),
				'data'   => $lightbox_data,
			); ?>

			<a <?php echo wpex_parse_attrs( $button_one_attrs ); ?>><span class="ticon ticon-search" aria-hidden="true"></span></a>

			<?php
			$button_two_attrs = array(
				'href'   => esc_url( $link ),
				'class'  => 'view-post',
				'target' => $target,
			); ?>

			<a <?php echo wpex_parse_attrs( $button_two_attrs ); ?>><span class="ticon ticon-arrow-right" aria-hidden="true"></span></a>

		</div>

	</div>

</div>
