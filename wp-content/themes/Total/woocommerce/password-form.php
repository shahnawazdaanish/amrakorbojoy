<?php
/**
 * Template used for the WooCommerce Category Locker plugin
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="clr site-content">

				<?php wpex_hook_content_top(); ?>

				<article class="entry-content entry clr">

					<?php
					do_action( 'wcl_before_passform' );

					if ( function_exists( 'wcl_get_the_password_form' ) ) {
						echo wcl_get_the_password_form();
					}

					do_action( 'wcl_after_passform' ); ?>

				</article><!-- #post -->

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- #content-wrap -->

<?php get_footer(); ?>