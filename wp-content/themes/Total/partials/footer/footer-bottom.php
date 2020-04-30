<?php
/**
 * Footer bottom content
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Classes
$classes = 'clr';
if ( $align = wpex_get_mod( 'bottom_footer_text_align' ) ) {
	$classes .= ' text' . $align;
} ?>

<?php wpex_hook_footer_bottom_before(); ?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer_bottom' ) ) : ?>

	<div id="footer-bottom" class="<?php echo esc_attr( $classes ); ?>"<?php wpex_schema_markup( 'footer_bottom' ); ?>>

		<?php wpex_hook_footer_bottom_top(); ?>

		<div id="footer-bottom-inner" class="container clr">
			<?php wpex_hook_footer_bottom_inner(); ?>
		</div><!-- #footer-bottom-inner -->

		<?php wpex_hook_footer_bottom_bottom(); ?>

	</div><!-- #footer-bottom -->

<?php endif; ?>

<?php wpex_hook_footer_bottom_after(); ?>