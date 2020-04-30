<?php
/**
 * Social Share Buttons Output
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Disabled if post is password protected or if disabled
if ( ! wpex_has_social_share() ) {
	return;
}

// Custom social share shortcode
if ( $custom_social = apply_filters( 'wpex_custom_social_share', wpex_get_mod( 'social_share_shortcode', false ) ) ) :

	$classes = 'wpex-social-share-wrap clr';

	if ( 'full-screen' == wpex_content_area_layout() ) {
		$classes .= ' container';
	} ?>

	<div class="<?php echo esc_attr( $classes ); ?>">
		<div class="wpex-social-share position-horizontal clr"><?php echo do_shortcode( wp_kses_post( $custom_social ) ); ?></div>
	</div>

<?php return; endif;

// Get sharing sites
$sites = wpex_social_share_sites();

// Return if there aren't any sites enabled
if ( empty( $sites ) ) {
	return;
}

// Load share script
wp_enqueue_script( 'wpex-social-share' );

// Declare main vars
$position = wpex_social_share_position();
$style    = wpex_social_share_style();
$heading  = wpex_social_share_heading();

// Wrap attributes
$wrap_attrs = array(
	'class' => 'wpex-social-share',
);
if ( $position ) {
	$wrap_attrs['class'] .= ' position-' . $position;
}
if ( $style ) {
	$wrap_attrs['class'] .= ' style-' . esc_attr( $style );
}
if ( 'full-screen' == wpex_content_area_layout() && 'horizontal' == $style ) {
	$wrap_attrs['class'] .= ' container';
}
if ( ! wpex_social_share_has_labels() ) {
	$wrap_attrs['class'] .= ' disable-labels';
}

// Get social share data
$social_share_data = wpex_get_social_share_data( wpex_get_current_post_id(), $sites );

foreach ( $social_share_data as $datak => $datav ) {
	$wrap_attrs[ 'data-' . $datak ] = $datav;
} ?>

<div <?php echo wpex_parse_attrs( $wrap_attrs ); ?>>

	<?php
	// Display heading if enabled and social share is set to the horizontal position
	if ( 'horizontal' == $position ) {

		$heading_options = array(
			'content'		=> $heading,
			'classes'		=> array( 'social-share-title' ),
			'apply_filters'	=> 'social_share',
		);

		if ( function_exists( 'is_product' ) && is_product() ) {
			$heading_options[ 'style' ] = 'plain';
		}

		wpex_heading( $heading_options );

	} ?>

	<ul class="clr">

		<?php
		// Get array of social share items
		// @see framework/helpers/social-share.php
		$items = wpex_get_social_items();

		// Loop through sites and save new array with filters for output
		foreach ( $sites as $site ) :

			if ( isset( $items[$site] ) ) :

				$item = isset( $items[$site] ) ? $items[$site] : '';

				if ( ! $item ) {
					continue;
				}

				$li_class = isset( $item['li_class'] ) ? $item['li_class'] : ''; ?>

				<li class="<?php echo esc_attr( $li_class ); ?>">

					<?php if ( isset( $item['href'] ) ) { ?>

						<a href="<?php echo esc_attr( $item['href'] ); ?>" role="button">

					<?php } else { ?>

						<a href="#" role="button">

					<?php } ?>

						<span class="<?php echo esc_attr( $item['icon_class'] ); ?>" aria-hidden="true"></span>

						<span class="wpex-label"><?php echo esc_html( $item['label'] ); ?></span>

					</a>

				</li>

			<?php

			endif;

		 endforeach; ?>

	</ul>

</div>
