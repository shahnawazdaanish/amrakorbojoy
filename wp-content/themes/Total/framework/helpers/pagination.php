<?php
/**
 * Custom pagination functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns correct loop pagination.
 *
 * @since 4.8
 */
function wpex_loop_pagination( $loop_type = '', $count = '' ) {

	// Display pagination
	if ( 'blog' == $loop_type ) {
		global $wp_query;
		$query = $wp_query->query;
		$query['posts_per_page'] = $wp_query->query_vars['posts_per_page']; // pass posts_per_page to the query.
		wpex_blog_pagination( array(
			'query'    => $query,
			'is_home'  => is_home(),
			'grid'     => '#blog-entries',
			'count'    => $count,
			'maxPages' => $wp_query->max_num_pages,
			'columns'  => wpex_blog_entry_columns(),
			'category' => is_category() ? get_query_var( 'cat' ) : false,
		) );
	} else {
		wpex_pagination();
	}

}

/**
 * Numbered Pagination for archives
 *
 * @since 4.8
 * @todo replace for blog and main archives
 */
function wpex_get_pagination() {

	// Arrow style
	$arrow_icon = wpex_get_mod( 'pagination_arrow' );
	$arrow_icon = $arrow_icon ? esc_attr( $arrow_icon ) : 'angle';

	// Arrows with RTL support
	$prev_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-right' : 'ticon ticon-' . $arrow_icon . '-left';
	$next_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-left' : 'ticon ticon-' . $arrow_icon . '-right';

	return get_the_posts_pagination( array(
		'type'               => 'list',
		'prev_text'          => '<span class="' . $prev_arrow . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Previous', 'total' ) . '</span>',
		'next_text'          => '<span class="' . $next_arrow . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Next', 'total' ) . '</span>',
		'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'total' ) . ' </span>',
	) );
}


/**
 * Numbered Pagination
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_pagination' ) ) { // MUST KEEP CHECK SO USERS CAN OVERRIDE

	function wpex_pagination( $query = '', $echo = true ) {

		// Arrow style
		$arrow_icon = wpex_get_mod( 'pagination_arrow' );
		$arrow_icon = $arrow_icon ? esc_attr( $arrow_icon ) : 'angle';

		// Arrows with RTL support
		$prev_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-right' : 'ticon ticon-' . $arrow_icon . '-left';
		$next_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-left' : 'ticon ticon-' . $arrow_icon . '-right';

		// Get global $query
		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		// Set vars
		$total  = $query->max_num_pages;
		$big    = 999999999;

		// Display pagination
		if ( $total > 1 ) {

			// Get current page
			if ( $current_page = get_query_var( 'paged' ) ) {
				$current_page = $current_page;
			} elseif ( $current_page = get_query_var( 'page' ) ) {
				$current_page = $current_page;
			} else {
				$current_page = 1;
			}

			// Get permalink structure
			if ( get_option( 'permalink_structure' ) ) {
				if ( is_page() ) {
					$format = 'page/%#%/';
				} else {
					$format = '/%#%/';
				}
			} else {
				$format = '&paged=%#%';
			}

			// Previous text
			$prev_text = '<span class="' . esc_attr( $prev_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Previous', 'total' ) . '</span>';

			// Next text
			$next_text = '<span class="' . esc_attr( $next_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Next', 'total' ) . '</span>';

			// Define and add filter to pagination args
			$args = apply_filters( 'wpex_pagination_args', array(
				'base'               => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
				'format'             => $format,
				'current'            => max( 1, $current_page ),
				'total'              => $total,
				'mid_size'           => 3,
				'type'               => 'list',
				'prev_text'          => $prev_text,
				'next_text'          => $next_text,
				'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'total' ) . ' </span>',
			) );

			// Alignment classes based on Customizer settings
			$align = wpex_get_mod( 'pagination_align' );
			$align_escaped = $align ? ' text' . esc_attr( $align ) : '';

			// Output pagination
			if ( $echo ) {
				echo '<div class="wpex-pagination wpex-clr' . $align_escaped . '">' . paginate_links( $args ) . '</div>';
			} else {
				return '<div class="wpex-pagination wpex-clr' . $align_escaped . '">' . paginate_links( $args ) . '</div>';
			}

		}

	}

}

/**
 * Echo Next/Prev Pagination.
 *
 * @since 4.9
 */
function wpex_archive_next_prev_links( $query = '' ) {
	echo wpex_get_archive_next_prev_links( $query );
}

/**
 * Return Next/Prev Pagination
 *
 * @since 4.9
 */
function wpex_get_archive_next_prev_links( $query = '' ) {

	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	if ( $query->max_num_pages > 1 ) {

		$output = '<div class="page-jump wpex-clr">';

			$output .= '<div class="alignleft newer-posts">';

				$output .= get_previous_posts_link( '&larr; ' . esc_html__( 'Newer Posts', 'total' ) );

			$output .= '</div>';

			$output .= '<div class="alignright older-posts">';

				$output .= get_next_posts_link( esc_html__( 'Older Posts', 'total' ) . ' &rarr;' );

			$output .= '</div>';

		$output .= '</div>';

		return $output;
	}
}

/**
 * Infinite Scroll Pagination
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_infinite_scroll' ) ) {

	function wpex_infinite_scroll( $type = 'standard' ) {

		// Make sure lightbox scripts are loaded incase they are needed
		// @todo we can probably add extra checks here.
		wpex_enqueue_lightbox_scripts();

		// Images loaded is needed
		wp_enqueue_script( 'imagesloaded' );

		// Load infinite scroll script
		wp_enqueue_script(
			'wpex-infinite-scroll',
			wpex_asset_url( 'js/dynamic/wpex-infinite-scroll.min.js' ),
			array( 'jquery', 'imagesloaded' ),
			WPEX_THEME_VERSION,
			true
		);

		// Loading text
		$loading_text = wpex_get_mod( 'loadmore_loading_text', esc_html__( 'Loading&hellip;', 'total' ) );

		// Loading img
		$gif = apply_filters( 'wpex_loadmore_gif', includes_url( 'images/spinner-2x.gif' ) );

		// Localize loading text
		$is_params = apply_filters( 'wpex_infinite_scroll_args', array(
			'loading' => array(
				'msgText'      => '<div class="wpex-infscr-spinner"><img src="' . esc_url( $gif ) . '" class="wpex-spinner" alt="' . esc_attr( $loading_text ) . '" /><span class="ticon ticon-spinner"></span></div>',
				'msg'          => null,
				'finishedMsg'  => null,
			),
			'blankImg'     => esc_url( wpex_asset_url( 'images/blank.gif' ) ),
			'navSelector'  => 'div.infinite-scroll-nav',
			'nextSelector' => 'div.infinite-scroll-nav div.older-posts a',
			'itemSelector' => '.blog-entry',
		), 'blog' );

		wp_localize_script( 'wpex-infinite-scroll', 'wpexInfiniteScroll', $is_params );

		if ( wpex_get_mod( 'blog_entry_audio_output', false ) || apply_filters( 'wpex_infinite_scroll_enqueue_mediaelement', false ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		} ?>

		<div class="infinite-scroll-nav clr">
			<div class="alignleft newer-posts"><?php echo get_previous_posts_link( '&larr; ' . esc_html__( 'Newer Posts', 'total' ) ); ?></div>
			<div class="alignright older-posts"><?php echo get_next_posts_link( esc_html__( 'Older Posts', 'total' ) . ' &rarr;' ); ?></div>
		</div>

	<?php }

}

/**
 * Ajax load more
 *
 * @since 4.4.1
 */
function wpex_loadmore( $args = array() ) {

	wp_enqueue_script( 'imagesloaded' );
	wp_enqueue_script( 'wpex-loadmore' );

	// @todo add extra checks here to make sure the lightbox is actually needed.
	// This would require a new "wpex_archive_has_lightbox" conditional check.
	wpex_enqueue_lightbox_scripts();

	$defaults = array(
		'nonce'    => wp_create_nonce( 'wpex-load-more-nonce' ),
		'query'    => '',
		'maxPages' => '',
		'columns'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$text         = wpex_get_mod( 'loadmore_text', esc_html__( 'Load More', 'total' ), true );
	$loading_text = wpex_get_mod( 'loadmore_loading_text', esc_html__( 'Loading&hellip;', 'total' ) );
	$gif          = apply_filters( 'wpex_loadmore_gif', includes_url( 'images/spinner-2x.gif' ) );

	if ( wpex_get_mod( 'blog_entry_audio_output', false ) || apply_filters( 'wpex_loadmore_enqueue_mediaelement', false ) ) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	echo '<div class="wpex-load-more-wrap"><a href="#" class="wpex-load-more theme-button expanded" data-loadmore="' . htmlentities( wp_json_encode( $args ) ) . '"><span class="theme-button-inner">' . esc_html( $text ) . '</span></a><img src="' . esc_url( $gif ) . '" class="wpex-spinner" alt="' . esc_html( $loading_text ) . '" /><span class="ticon ticon-spinner"></span></div>';

}

/**
 * Ajax load more
 *
 * @since 4.4.1
 */
function wpex_ajax_load_more() {

	check_ajax_referer( 'wpex-load-more-nonce', 'nonce' );

	$loadmore = isset( $_POST['loadmore'] ) ? $_POST['loadmore'] : '';

	$query_args = isset( $loadmore['query'] ) ? $loadmore['query'] : array();

	if ( ! empty( $query_args['s'] ) ) {
		$post_type = 'post'; // search results when set to blog style
	} else {
		$query_args['post_type'] = ! empty( $query_args['post_type'] ) ? $query_args['post_type'] : 'post';
		$post_type = $query_args['post_type'];
	}

	$query_args['post_status'] = 'publish';
	$query_args['paged']       = isset( $_POST['page'] ) ? $_POST['page'] : 2;

	//$context = isset( $_POST['context'] ) ? $_POST['context'] : 'archive';
	global $wpex_count;
	$wpex_count = isset( $loadmore['count'] ) ? $loadmore['count'] : 0;
	$columns    = isset( $loadmore['columns'] ) ? $loadmore['columns'] : 0;

	if ( ! empty( $loadmore['is_home'] ) && $cats = wpex_blog_exclude_categories() ) {
		$query_args['category__not_in'] = $cats;
	}

	ob_start();

	$loop = new WP_Query( $query_args );

	if ( $loop->have_posts() ) :

		while ( $loop->have_posts() ): $loop->the_post();

			$wpex_count++;

			if ( 'post' == $post_type ) {

				wpex_get_template_part( 'blog_entry' );

			}

			if ( $columns == $wpex_count ) {
				$wpex_count=0;
			}

		endwhile;

	endif;

	wp_reset_postdata();

	$data = ob_get_clean();

	wp_send_json_success( $data );

	wp_die();

}
add_action( 'wp_ajax_wpex_ajax_load_more', 'wpex_ajax_load_more' );
add_action( 'wp_ajax_nopriv_wpex_ajax_load_more', 'wpex_ajax_load_more' );

/**
 * Blog Pagination
 * Execute the correct pagination function based on the theme settings
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_blog_pagination' ) ) {

	function wpex_blog_pagination( $args = array() ) {

		$pagination_style = wpex_get_mod( 'blog_pagination_style', 'standard' );

		if ( is_category() ) {

			$term       = get_query_var( 'cat' );
			$term_data  = get_option( 'category_' . $term );

			if ( ! empty( $term_data['wpex_term_pagination'] ) ) {
				$pagination_style = $term_data['wpex_term_pagination'];
			}

		}

		if ( 'infinite_scroll' == $pagination_style ) {

			if ( 'grid-entry-style' == wpex_blog_entry_style() ) {
				$infinite_type = 'standard-grid';
			} else {
				$infinite_type = 'standard';
			}

			wpex_infinite_scroll( $infinite_type );

		} elseif ( 'load_more' == $pagination_style ) {

			wpex_loadmore( $args );

		} elseif ( 'next_prev' == $pagination_style ) {

			wpex_archive_next_prev_links();

		} else {

			wpex_pagination();

		}

	}

}