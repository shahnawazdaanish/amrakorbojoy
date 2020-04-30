<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.9.9
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

			<main id="content" class="clr site-content" role="main">

				<?php wpex_hook_content_top(); ?>

				<?php
				// Display 404 page if custom elementor template isn't defined.
				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) : ?>

					<article class="entry clr">

						<?php
						// Check custom page content
						if ( wpex_get_mod( 'error_page_content_id' ) && $id = wpex_get_current_post_id() ) :

							$post    = get_post( $id ); // get post
							$content = wpex_parse_vc_content( $post->post_content ); // remove weird p tags and extra code
							$content = wp_kses_post( $content ); // security
							echo do_shortcode( $content ); // parse shortcodes and echo

						else :

							// Get error text
							$error_text = trim( wpex_get_translated_theme_mod( 'error_page_text' ) );

							// Display custom text
							if ( $error_text )  : ?>

								<div class="custom-error404-content clr"><?php echo wpex_the_content( $error_text, 'error404' ); ?></div>

							<?php
							// Display default text
							else : ?>

								<div class="error404-content clr">

									<h1><?php esc_html_e( 'Sorry, this page could not be found.', 'total' ); ?></h1>
									<p><?php esc_html_e( 'The page you are looking for doesn\'t exist, no longer exists or has been moved.', 'total' ); ?></p>

								</div><!-- .error404-content -->

							<?php endif; ?>

						<?php endif; ?>

					</article><!-- .entry -->

				<?php endif; ?>

				<?php wpex_hook_content_bottom(); ?>

			</main><!-- #content -->

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->

<?php get_footer(); ?>