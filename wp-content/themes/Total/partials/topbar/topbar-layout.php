<?php
/**
 * Topbar layout
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php wpex_hook_topbar_before(); ?>

	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'topbar' ) ) : ?>

		<div id="top-bar-wrap" class="<?php echo esc_attr( wpex_topbar_classes() ); ?>">
			<div id="top-bar" class="clr container">
				<?php wpex_hook_topbar_inner(); ?>
			</div><!-- #top-bar -->
		</div><!-- #top-bar-wrap -->

	<?php endif; ?>

<?php wpex_hook_topbar_after(); ?>