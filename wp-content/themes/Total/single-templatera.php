<?php
/**
 * The template for editing templatera templates via the front-end editor.
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// This file is only used for the front-end editor.
if ( ! wpex_vc_is_inline() ) {
	exit;
}

get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content clr">

				<?php wpex_hook_content_top(); ?>

				<div class="single-page-content entry clr">

					<?php if ( wpex_is_footer_builder_page() || wpex_is_header_builder_page() ) : ?>

						<div class="wpex-single-elementor-notice"><span class="ticon ticon-info-circle"></span><?php esc_html_e( 'You are currently editing a section of your site via a page builder template. Hover over the section of the site you are editing to make your changes. This is where you site content will go.', 'total' ); ?></div>

					<?php else : ?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php the_content(); ?>

						<?php endwhile; ?>

					<?php endif; ?>

				</div>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->

<?php get_footer(); ?>