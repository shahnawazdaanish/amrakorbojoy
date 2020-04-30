<?php
/**
 * Template Name: Blog - Content Above
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.8
 */

get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
				// Display title if enabled
				$blocks = wpex_single_blocks();
				if ( is_array( $blocks ) && in_array( 'title', $blocks ) ) :

					get_template_part( 'partials/page-single-title' );

				endif; ?>

				<?php
				// Display post thumbnail if enabled
				if ( has_post_thumbnail() && wpex_get_mod( 'page_featured_image' ) ) : ?>

					<div id="page-featured-img" class="clr"><?php the_post_thumbnail(); ?></div>

				<?php endif; ?>

				<div class="entry-content entry clr"><?php the_content(); ?></div>

			<?php endwhile; ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content clr" role="main">

				<?php wpex_hook_content_top(); ?>

				<?php
				global $post, $paged, $more;
				$more = 0;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				} else if ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				} else {
					$paged = 1;
				}
				// Query posts
				$wp_query = new WP_Query( array(
					'post_type'        => 'post',
					'paged'            => $paged,
					'category__not_in' => wpex_blog_exclude_categories( true ),
				) );
				if ( $wp_query->posts ) :

					$columns = wpex_blog_entry_columns(); ?>

					<div id="blog-entries" class="<?php wpex_blog_wrap_classes(); ?>">
						<?php
						// Define counter for clearing floats
						$wpex_count = 0;

						// Loop through posts
						while ( have_posts() ) : the_post();

							// Before entry hook
							wpex_hook_archive_loop_before_entry();

							// Get content template part (entry content)
							get_template_part( 'partials/loop/loop', 'blog' );

							// After entry hook
							wpex_hook_archive_loop_after_entry();

						// End loop
						endwhile; ?>
					</div><!-- #blog-entries -->

					<?php
					// Display post pagination
					wpex_blog_pagination( array(
						'query'    => $wp_query->query,
						'grid'     => '#blog-entries',
						'count'    => $wpex_count,
						'perPage'  => $wp_query->query_vars['posts_per_page'],
						'maxPages' => $wp_query->max_num_pages,
						'query'    => $wp_query->query,
						'paged'    => $paged,
						'columns'  => $columns,
					) ); ?>

				<?php endif; ?>

				<?php wp_reset_postdata(); wp_reset_query(); ?>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- #content-wrap -->

<?php get_footer(); ?>