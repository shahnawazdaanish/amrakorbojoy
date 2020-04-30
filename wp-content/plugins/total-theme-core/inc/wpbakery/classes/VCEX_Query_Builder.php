<?php
/**
 * Used to build WP Queries for Visual Composer elements.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1.2
 *
 * @todo change main filter to vcex_query_args_
 */

class VCEX_Query_Builder {
	public $args = array();
	public $atts = array();

	/**
	 * Class Constructor.
	 */
	public function __construct( $atts ) {

		// Get shortcode atts
		$this->atts = $atts;

		// Check for custom query
		if ( isset( $this->atts['custom_query'] ) && 'true' === $this->atts['custom_query'] ) {
			return $this->custom_query( $this->atts['custom_query_args'] );
		}

		// Loop through shortcode atts and run class methods
		foreach ( $atts as $key => $value ) {
			$method = 'parse_' . $key;
			if ( method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}

	}

	/**
	 * Custom Query.
	 */
	private function custom_query( $query ) {

		$args = array();

		// Check if it's a callable function
		if ( is_callable( $query ) ) {
			$query = call_user_func( $query );
			if ( $query && is_array( $query ) ) {
				$args = $query;
			}
		}

		// Not callable
		else {

			// Fix for threaded arrays. Ex: &orderby[meta_value_num]=ASC&orderby[menu_order]=ASC&orderby[date]=DESC
			// VC saves the [] as {} to prevent conflicts since shortcodes use []
			$query = str_replace( '`{`', '[', $query );
			$query = str_replace( '`}`', ']', $query );

			if ( ! empty( $this->atts['custom_query_args'] ) ) {
				$query = html_entity_decode( vc_value_from_safe( $query ), ENT_QUOTES, 'utf-8' );
				$query = parse_str( $query, $args ); // create args array
			}

			// Parse dynamic args
			if ( $args ) {
				$args = $this->parse_dynamic_values( $args );
			}

		}

		// Set class args to the custom query args and parse dynamic values
		$this->args = $args;

		// Add empty values that should be added
		if ( empty( $args['post_type'] ) ) {
			$this->args['post_type'] = ! empty( $this->atts['post_type'] ) ? $this->atts['post_type'] : '';
		}
		if ( empty( $args['posts_per_page'] ) ) {
			$this->args['posts_per_page'] = 4;
		}

		// Turn args into arrays
		if ( ! empty( $args['post__in'] ) ) {
			$this->args['post__in'] = $this->string_to_array( $args['post__in'] );
		}
		if ( ! empty( $args['post__not_in'] ) ) {
			$this->args['post__not_in'] = $this->string_to_array( $args['post__not_in'] );
		}

		// Add related args if enabled
		if ( ! empty( $args['related'] ) ) {
			$this->add_related_args(); // Add related last
		}

		// Enable pagination by default
		$this->parse_pagination( 'true' );

	}

	/**
	 * Posts In (wih single underscore)
	 */
	private function parse_posts_in( $value ) {
		if ( ! $value ) return;
		$this->args['post__in'] = $this->string_to_array( $value );
		$this->args['ignore_sticky_posts'] = true;
	}

	/**
	 * Post In (shis should already be an array).
	 */
	private function parse_post__in( array $value ) {
		if ( ! $value ) return;
		$this->args['post__in'] = $value;
		$this->args['ignore_sticky_posts'] = true;
	}

	/**
	 * Offset.
	 */
	private function parse_offset( $value ) {
		if ( ! $value ) return;
		$this->args['offset'] = $value;
	}

	/**
	 * Limit by Author.
	 */
	private function parse_author_in( $value ) {
		if ( ! $value ) return;
		$this->args['author__in'] = $this->string_to_array( $value );
		$this->args['ignore_sticky_posts'] = true;
	}

	/**
	 * Show only items with thumbnails.
	 */
	private function parse_thumbnail_query( $value ) {
		if ( 'true' === $value ) {
			$this->args['meta_query'] = array( array ( 'key' => '_thumbnail_id' ) );
		}
	}

	/**
	 * Count.
	 */
	private function parse_count( $value ) {
		$value = $value ? $value : '-1';
		$this->args['posts_per_page'] = (int) $value;
	}

	/**
	 * Posts Per Page.
	 */
	private function parse_posts_per_page( $value ) {
		$value = $value ? $value : '-1';
		$this->args['posts_per_page'] = (int) $value;
	}

	/**
	 * Pagination.
	 */
	private function parse_pagination( $value ) {
		if ( ! empty( $this->atts['pagination_loadmore'] ) && 'true' === $this->atts['pagination_loadmore'] ) {
			$value = 'true';
		}
		if ( ! empty( $this->atts['paged'] ) ) {
			$this->args['paged'] = $this->atts['paged'];
			return;
		}
		if ( 'true' === $value ) {
			if ( get_query_var( 'page' ) ) {
				$paged = get_query_var( 'page' );
			} elseif ( get_query_var( 'paged' ) ) {
				$paged = get_query_var( 'paged' );
			} else {
				$paged = 1;
			}
			$this->args['paged'] = $paged;
		} else {
			$this->args['no_found_rows'] = true;
		}
	}

	/**
	 * Ignore sticky posts.
	 */
	private function parse_ignore_sticky_posts( $value ) {
		if ( 'true' === $value ) {
			$this->args['ignore_sticky_posts'] = true;
		}
	}

	/**
	 * Orderby.
	 */
	private function parse_orderby( $value ) {
		if ( $value && 'menu_order' != $value ) {
			$this->args['ignore_custom_sort'] = true; // Fix for post types order plugin
		}
		if ( 'woo_best_selling' === $value ) {
			$this->args['meta_key'] = 'total_sales';
			$this->args['orderby']  = 'meta_value_num';
		} elseif ( 'woo_top_rated' === $value ) {
			$this->args['orderby']  = ''; // This is done via order_by_rating_post_clauses
		} elseif ( ! empty( $this->atts['posts_in'] ) && ! $value ) {
			$this->args['orderby'] = 'post__in';
		} else {
			$this->args['orderby'] = $value;
		}
	}

	/**
	 * Orderby meta key.
	 */
	private function parse_orderby_meta_key( $value ) {
		if ( ! $value ) return;
		if ( ! empty( $this->args['orderby'] ) && in_array( $this->args['orderby'], array( 'meta_value', 'meta_value_num' ) ) ) {
			$this->args['meta_key'] = $value;
		}
	}

	/**
	 * Order.
	 */
	private function parse_order( $value ) {
		$this->args['order'] = $value;
	}

	/**
	 * Post Types.
	 */
	private function parse_post_type( $value ) {
		$value = $value ? $value : 'post';
		$this->args['post_type'] = $this->string_to_array( $value );
	}

	/**
	 * Post Types.
	 */
	private function parse_post_types( $value ) {
		$value = $value ? $value : 'post';
		$this->args['post_type'] = $this->string_to_array( $value );
	}

	/**
	 * Author.
	 */
	private function parse_authors( $value ) {
		if ( ! $value ) return;
		$this->args['author'] = $value;
	}

	/**
	 * Products out of stock.
	 */
	private function parse_exclude_products_out_of_stock( $value ) {
		if ( ! $value ) return;
		$this->args['meta_query'] = array(
			array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN'
			),
		);
	}

	/**
	 * Tax Query.
	 */
	private function parse_tax_query( $value ) {

		// Return if set to false
		if ( 'false' === $value ) {
			return;
		}

		// Get defined tax query terms
		if ( 'true' === $value ) {

			$tax_query_taxonomy = isset ( $this->atts['tax_query_taxonomy'] ) ? $this->atts['tax_query_taxonomy'] : '';

			if ( $tax_query_taxonomy && taxonomy_exists( $tax_query_taxonomy ) ) {

				$tax_query_terms = isset ( $this->atts['tax_query_terms'] ) ? $this->string_to_array( $this->atts['tax_query_terms'] ) : '';

				if ( $tax_query_terms ) {

					if ( 'post_format' === $tax_query_taxonomy && in_array( 'post-format-standard', $tax_query_terms ) ) {

						$all_formats = array(
							'post-format-aside',
							'post-format-gallery',
							'post-format-link',
							'post-format-image',
							'post-format-quote',
							'post-format-status',
							'post-format-audio',
							'post-format-chat',
							'post-format-video'
						);

						foreach ( $tax_query_terms as $k => $v ) {
							if ( in_array( $v, $all_formats ) ) {
								unset( $all_formats[$k] );
							}
						}

						$this->args['tax_query'] = array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'post_format',
								'field'    => 'slug',
								'terms'    => $all_formats,
								'operator' => 'NOT IN',
							),
						);

					} else {

						$this->args['tax_query'] = array(
							'relation' => 'AND',
							array(
								'taxonomy' => $tax_query_taxonomy,
								'field'    => 'slug',
								'terms'    => $tax_query_terms,
							),
						);

					}

				}

			}

		}

		// Generate tax query based on Include/Exclude categories
		elseif ( isset( $this->atts['include_categories'] ) || isset( $this->atts['exclude_categories'] ) ) {

			// Get terms to include/excude
			$terms = $this->get_terms();

			// Return if no terms
			if ( empty( $terms ) ) {
				$this->args['tax_query'] = NULL;
			}

			// The tax query relation
			$this->args['tax_query'] = array(
				'relation' => 'AND',
			);

			// Get taxonomies
			$taxonomies = $this->get_taxonomies();

			// If Single taxonomy
			if ( '1' == count( $taxonomies ) ) {

				// Includes
				if ( ! empty( $terms['include'] ) ) {
					$this->args['tax_query'][] = array(
						'taxonomy' => $taxonomies[0],
						'field'    => 'id',
						'terms'    => $terms['include'],
						'operator' => 'IN',
					);
				}

				// Excludes
				if ( ! empty( $terms['exclude'] ) ) {
					$this->args['tax_query'][] = array(
						'taxonomy' => $taxonomies[0],
						'field'    => 'id',
						'terms'    => $terms['exclude'],
						'operator' => 'NOT IN',
					);
				}

			}

			// More then 1 taxonomy
			elseif ( $taxonomies ) {

				// Merge terms
				$merge_terms = array_merge( $terms['include'], $terms['exclude'] );

				// Loop through terms to build tax_query
				$get_terms = get_terms( $taxonomies, array(
					'include' => $merge_terms,
				) );
				foreach ( $get_terms as $term ) {
					$operator = in_array( $term->term_id, $terms['exclude'] ) ? 'NOT IN' : 'IN';
					$this->args['tax_query'][] = array(
						'field'    => 'id',
						'taxonomy' => $term->taxonomy,
						'terms'    => $term->term_id,
						'operator' => $operator,
					);
				}

			}

		}

	}

	/**
	 * Include Categories.
	 */
	private function include_categories() {
		if ( empty( $this->atts['include_categories'] ) ) {
			return;
		}
		$taxonomies = $this->get_taxonomies();
		$taxonomy   = $taxonomies[0];
		return $this->sanitize_autocomplete( $this->atts['include_categories'], $taxonomy );
	}

	/**
	 * Exclude Categories.
	 */
	private function exclude_categories() {
		if ( empty( $this->atts['exclude_categories'] ) ) {
			return;
		}
		$taxonomies = $this->get_taxonomies();
		$taxonomy   = $taxonomies[0];
		return $this->sanitize_autocomplete( $this->atts['exclude_categories'], $taxonomy );
	}

	/**
	 * Get taxonomies.
	 */
	private function get_taxonomies() {
		if ( ! empty( $this->atts['taxonomy'] ) ) {
			return array( $this->atts['taxonomy'] );
		} elseif ( ! empty( $this->atts['post_type'] ) ) {
			$tax = vcex_get_post_type_cat_tax( $this->atts['post_type'] );
			if ( $tax ) {
				return $this->string_to_array( $tax );
			}
		} elseif( ! empty( $this->atts['taxonomies'] ) ) {
			return $this->string_to_array( $this->atts['taxonomies'] );
		}
	}

	/**
	 * Get the terms to include in the Query.
	 */
	private function get_terms() {

		$terms = array(
			'include' => array(),
			'exclude' => array(),
		);

		// Include categories
		$include_categories = $this->include_categories();
		if ( ! empty( $include_categories ) ) {
			foreach ( $include_categories as $cat ) {
				$terms['include'][] = $cat;
			}
		}

		// Exclude categories
		$exclude_categories = $this->exclude_categories();
		if ( ! empty( $exclude_categories ) ) {
			foreach ( $exclude_categories as $cat ) {
				$terms['exclude'][] = $cat;
			}
		}

		// Return terms
		return $terms;

	}

	/**
	 * Featured products only.
	 */
	private function parse_featured_products_only( $value ) {
		if ( ! $value ) {
			return;
		}
		//$this->args['meta_key']   =  '_featured';
		//$this->args['meta_value'] = 'yes';

		// New Woo 3.0 + method
		if ( empty( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = array();
		}
		$this->args['tax_query']['relation'] = 'AND';
		$this->args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured'
		);
	}

	/**
	 * Converts a string to an Array.
	 */
	private function string_to_array( $value ) {

		// Return if value is empty
		if ( ! $value ) {
			return;
		}

		// Return if already an array
		if ( is_array( $value ) ) {
			return $value;
		}

		// Define output array
		$array  = array();

		// Clean up value
		$items  = preg_split( '/\,[\s]*/', $value );

		// Create array
		foreach ( $items as $item ) {
			if ( strlen( $item ) > 0 ) {
				$array[] = $item;
			}
		}

		// Return array
		return $array;

	}

	/**
	 * Sanitizes autocomplete data and returns ID's of terms to include or exclude.
	 */
	private function sanitize_autocomplete( $terms, $taxonomy ) {

		// Turn into array
		$terms  = preg_split( '/\,[\s]*/', $terms );
		$return = array();

		// Loop through data and turn slugs into ID's
		foreach( $terms as $term ) {

			// Check if is integer or slug
			$field = ( is_numeric( $term ) ) ? 'id' : 'slug';

			// Get taxonomy ID from slug
			$term_data = get_term_by( $field, $term, $taxonomy );

			// Add to new array if it's a valid term
			if ( $term_data ) {
				$return[] = $term_data->term_id;
			}

		}

		// Return array
		return $return;

	}

	/**
	 * Returns related tax query.
	 */
	private function add_related_args() {
		$type = isset( $this->args['post_type'] ) ? $this->args['post_type'] : get_post_type();
		if ( function_exists( 'wpex_get_post_type_cat_tax' ) ) {
			$tax = isset( $this->args['taxonomy'] ) ? $this->args['taxonomy'] : wpex_get_post_type_cat_tax( $type );
			if ( $tax ) {
				$term = wpex_get_first_term_id( get_the_ID(), $tax );
				if ( $term ) {
					if ( isset( $this->args['post__not_in'] ) && is_array( $this->args['post__not_in'] ) ) {
						$this->args['post__not_in'][] = get_the_ID();
					} else {
						$this->args['post__not_in'] = array( get_the_ID() );
					}
					$this->args['tax_query'] = array(
						array(
							'taxonomy' => $tax,
							'field'    => 'term_id',
							'terms'    => array( $term ),
						)
					);
				} elseif ( ! apply_filters( 'vcex_query_builder_related_fallback_items', true ) ) {
					$this->args['tax_query'] = array(
						array(
							'taxonomy' => $tax,
							'field'    => 'term_id',
							'terms'    => array(),
						)
					);
				}
			}
		}
	}

	/**
	 * This function allows for dynamic values when building queries.
	 */
	public function parse_dynamic_values( $args ) {

		if ( ! is_array( $args ) ) {
			return $args;
		}

		$dynamic_values = array(
			'current_post'   => get_the_ID(),
			'current_term'   => is_tax() ? get_queried_object()->term_id : '',
			'current_author' => get_the_author_meta( 'ID' ),
			'current_user'   => get_current_user_id(),
		);

		$dynamic_values = apply_filters( 'vcex_grid_advanced_query_dynamic_values', $dynamic_values );

		foreach( $args as $key => $value ) {

			if ( is_string( $value ) && array_key_exists( $value, $dynamic_values ) ) {
				$args[$key] = $dynamic_values[$value];
			}

		}

		return $args;

	}

	/**
	 * This function actually builds and returns the Query.
	 */
	public function build() {
		if ( wp_doing_ajax() ) {
			$this->args['post_status'] = array( 'publish' );
		}
		//print_r( $this->args );
		$this->args = apply_filters( 'vcex_grid_query', $this->args, $this->atts ); //@todo rename vcex_query_args_
		return new WP_Query( $this->args );
	}

}

// Helper function runs the VCEX_Query_Builder class and returns the Query array
function vcex_build_wp_query( $atts ) {
	$query_builder = new VCEX_Query_Builder( $atts );
	return $query_builder->build();
}