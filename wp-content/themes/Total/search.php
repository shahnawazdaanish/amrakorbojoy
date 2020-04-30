<?php
/**
 * The template for displaying Search Results pages
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content">

				<?php wpex_hook_content_top(); ?>

				<?php
				// Display default theme layout if elementor template not defined
				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :

					// Display posts if there are in fact posts to display
					if ( have_posts() ) :

						// Get index loop type
						$loop_type = wpex_get_index_loop_type();

						// Get loop top
						get_template_part( 'partials/loop/loop-top', $loop_type );

							// Define counter for clearing floats
							$wpex_count = 0;

							// Loop through posts
							while ( have_posts() ) : the_post();

								// Before entry hook
								wpex_hook_archive_loop_before_entry();

								// Get content template part (entry content)
								get_template_part( 'partials/loop/loop', $loop_type );

								// After entry hook
								wpex_hook_archive_loop_after_entry();

							// End loop
							endwhile;

						// Get loop bottom
						get_template_part( 'partials/loop/loop-bottom', $loop_type );

						// Display pagination
						wpex_loop_pagination( $loop_type, $wpex_count ); ?>

					<?php
					// Show message because there aren't any posts
					else : ?>

						<div class="wpex-no-posts-found"><?php esc_html_e( 'No Posts found.', 'total' ); ?></div>

					<?php endif;

				endif; ?>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

		<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->

<?php get_footer(); ?>