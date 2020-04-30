<?php
/**
 * Alter posts query
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpex_pre_get_posts( $query ) {

	// Only alter the front-end main query
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Search functions
	if ( $query->is_search() ) {

		$post_type = ! empty( $_GET['post_type'] ) ? wp_strip_all_tags( $_GET['post_type'] ) : '';

		// Prevent issues with Woo Search
		if ( WPEX_WOOCOMMERCE_ACTIVE && 'product' == $post_type ) {
			return;
		}

		// Search posts per page
		$query->set( 'posts_per_page', wpex_get_mod( 'search_posts_per_page', '10' ) );

		// Alter search post types unless the post_type arg is in the URL
		if ( ! $post_type ) {

			// Display standard posts only
			if ( wpex_get_mod( 'search_standard_posts_only', false ) ) {
				$query->set( 'post_type', 'post' );
				return;
			}

			// Exclude post types from search results
			$searchable_types = get_post_types( array(
				'public'              => true,
				'exclude_from_search' => false
			) );

			if ( is_array( $searchable_types ) ) {
				foreach ( $searchable_types as $type ) {
					if ( in_array( $type, array( 'staff', 'portfolio', 'testimonials' ) ) && ! wpex_get_mod( $type . '_search', true ) ) {
						unset( $searchable_types[$type] );
					}
				}
				$searchable_type[] = 'user'; // fix for relevanssi plugin
				$query->set( 'post_type', $searchable_types );
			}

		}

		return;

	}

	// Exclude categories from the main blog
	if ( is_home() || is_page_template( 'templates/blog.php' ) || is_page_template( 'templates/blog-content-above.php' ) ) {
		if ( $cats = wpex_blog_exclude_categories() ) {
			$query->set( 'category__not_in', $cats );
		}
		return;
	}

	// Category pagination
	if ( $query->is_category() ) {
		$obj = get_queried_object();
		if ( ! empty( $obj ) ) {
			$terms = get_terms( 'category' );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $query->is_category( $term->slug ) ) {
						$term_id    = $term->term_id;
						$term_data  = get_option( "category_$term_id" );
						if ( $term_data ) {
							if ( ! empty( $term_data['wpex_term_posts_per_page'] ) ) {
								$query->set( 'posts_per_page', $term_data['wpex_term_posts_per_page'] );
								return;
							}
						}
					}
				}
			}
		}
	}
}
add_action( 'pre_get_posts', 'wpex_pre_get_posts' );