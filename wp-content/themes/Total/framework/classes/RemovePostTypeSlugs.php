<?php
/**
 * Removes slugs from core custom post types
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.6.1
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class RemovePostTypeSlugs {

	/**
	 * Post types to remove the slugs from
	 *
	 * @since 4.6.5
	 */
	public $types;

	/**
	 * Main constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->types = array( 'portfolio', 'staff', 'testimonials' );

		if ( $this->types && is_array( $this->types ) ) {

			add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 3 );

			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

		}

	}

	/**
	 * Remove slugs from the post types
	 *
	 * @since 2.0.0
	 */
	public function post_type_link( $post_link, $post, $leavename ) {

		// If not part of the theme post types return default post link
		if ( ! in_array( $post->post_type, $this->types ) || 'publish' != $post->post_status ) {
			return $post_link;
		}

		// Get the post type slug
		if ( 'portfolio' == $post->post_type ) {
			$slug = wpex_get_mod( 'portfolio_slug', 'portfolio-item' );
			$slug = $slug ? $slug : 'portfolio-item';
		} elseif ( 'staff' == $post->post_type ) {
			$slug = wpex_get_mod( 'staff_slug' );
			$slug = $slug ? $slug : 'staff-member';
		} elseif ( 'testimonials' == $post->post_type ) {
			$slug = wpex_get_mod( 'testimonials_slug', 'testimonial' );
			$slug = $slug ? $slug : 'testimonial';
		} else {
			$slug = '';
		}

		// Remove current slug
		if ( $slug ) {
			$post_link = str_replace( '/' . wp_strip_all_tags( $slug ) . '/', '/', $post_link );
		}

		// Return new post link without slug
		return $post_link;

	}

	/**
	 * WordPress will no longer recognize the custom post type as a custom post type
	 * this function tricks WordPress into thinking it actually is still a custom post type.
	 *
	 * @since 2.0.0
	 */
	public function pre_get_posts( $query ) {

		// Only noop the main query
		if ( ! $query->is_main_query() ) {
			return;
		}

		// Only noop our very specific rewrite rule match
		if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}

		// 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
		if ( ! empty( $query->query['name'] ) ) {
			$array = array( 'post', 'page' );
			$array = array_merge( $array, $this->types );
			$query->set( 'post_type', $array );
		}
	}

}
new RemovePostTypeSlugs();