<?php
/**
 * Footer bottom content
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get footer widgets columns
$columns    = wpex_get_mod( 'footer_widgets_columns', '4' );
$grid_class = apply_filters( 'wpex_footer_widget_col_classes', array( wpex_grid_class( $columns ) ) );
$grid_class = is_array( $grid_class ) ? implode( ' ', $grid_class ) : $grid_class; ?>

<div id="footer-widgets" class="<?php echo esc_attr( wpex_footer_widgets_class() ); ?>">

	<?php do_action( 'wpex_hook_footer_widgets_top' ); ?>

	<?php
	// Footer box 1 ?>
	<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-1">
		<?php dynamic_sidebar( 'footer_one' ); ?>
	</div><!-- .footer-1-box -->

	<?php
	// Footer box 2
	if ( $columns > '1' ) : ?>
		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-2">
			<?php dynamic_sidebar( 'footer_two' ); ?>
		</div><!-- .footer-2-box -->
	<?php endif; ?>

	<?php
	// Footer box 3
	if ( $columns > '2' ) : ?>
		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-3">
			<?php dynamic_sidebar( 'footer_three' ); ?>
		</div><!-- .footer-3-box -->
	<?php endif; ?>

	<?php
	// Footer box 4
	if ( $columns > '3' ) : ?>
		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-4">
			<?php dynamic_sidebar( 'footer_four' ); ?>
		</div><!-- .footer-4-box -->
	<?php endif; ?>

	<?php
	// Footer box 5
	if ( $columns > '4' ) : ?>
		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-5">
			<?php dynamic_sidebar( 'footer_five' ); ?>
		</div><!-- .footer-5-box -->
	<?php endif; ?>

	<?php do_action( 'wpex_hook_footer_widgets_bottom' ); ?>

</div><!-- #footer-widgets -->