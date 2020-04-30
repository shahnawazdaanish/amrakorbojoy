<?php
/**
 * Togglebar output
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="toggle-bar-wrap" class="<?php echo esc_attr( wpex_togglebar_classes() ); ?>">
	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'togglebar' ) ) : ?>
		<div id="toggle-bar" class="container wpex-clr">
			<?php wpex_get_template_part( 'togglebar_content' ); ?>
		</div><!-- #toggle-bar -->
	<?php endif;?>
</div><!-- #toggle-bar-wrap -->