<?php
/**
 * About widget
 *
 * @package Total Theme Core
 * @subpackage Widgets
 * @version 1.0.3
 */

namespace TotalThemeCore;
use WP_Query;

// Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// Start class
class PostsGridWidget extends WidgetBuilder {
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_recent_posts_thumb_grid',
			'name'    => $this->branding() . esc_html__( 'Posts Thumbnail Grid', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields' => array(
				array(
					'id'    => 'title',
					'label' => esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'number',
					'label'   => esc_html__( 'Number', 'total-theme-core' ),
					'type'    => 'number',
					'default' => 6,
				),
				array(
					'id'      => 'columns',
					'label'   => esc_html__( 'Columns', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_columns',
					'default' => '3',
				),
				array(
					'id'      => 'gap',
					'label'   => esc_html__( 'Column Gap', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_gaps',
				),
				array(
					'id'       => 'post_type',
					'label'    => esc_html__( 'Post Type', 'total-theme-core' ),
					'type'     => 'select',
					'choices'  => 'post_types',
					'default'  => 'post',
				),
				array(
					'id'      => 'taxonomy',
					'label'   => esc_html__( 'Query By Taxonomy', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'taxonomies',
				),
				array(
					'id'          => 'terms',
					'label'       => esc_html__( 'Include Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
				array(
					'id'          => 'terms_exclude',
					'label'       => esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter a comma seperated list of terms.', 'total-theme-core' ),
				),
				array(
					'id'      => 'order',
					'label'   => esc_html__( 'Order', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_order',
					'default' => 'DESC',
				),
				array(
					'id'      => 'orderby',
					'label'   => esc_html__( 'Order by', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_orderby',
					'default' => 'date',
				),
				array(
					'id'      => 'img_hover',
					'label'   => esc_html__( 'Image Hover', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_hovers',
				),
				array(
					'id'      => 'img_size',
					'label'   => esc_html__( 'Image Size', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'wpex-custom',
					'choices' => 'intermediate_image_sizes',
				),
				array(
					'id'    => 'img_width',
					'label' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'img_height',
					'label' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'      => 'img_crop_location',
					'label'   => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_crop_locations',
				),
			),
		);

		$this->create_widget( $this->args );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 * @since 1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		// Parse and extract widget settings
		extract( $this->parse_instance( $instance ) );

		// Before widget hook
		echo wp_kses_post( $args['before_widget'] );

		// Display widget title
		$this->widget_title( $args, $instance );

		// Define widget output
		$output = '';

		// Start output
		$gap = $gap ? $gap : '5';
		$output .= '<ul class="wpex-recent-posts-thumb-grid wpex-row clr gap-' . esc_attr( $gap ) . '">';

			// Query args
			$query_args = array(
				'post_type'      => $post_type,
				'posts_per_page' => $number,
				'no_found_rows'  => true,
				'tax_query'      => array(
					'relation' => 'AND',
				),
				'meta_query'     => array(
					array( 'key' => '_thumbnail_id' )
				),
			);

			// Order params - needs FALLBACK don't ever edit!
			if ( ! empty( $orderby ) ) {
				$query_args['order']   = $order;
				$query_args['orderby'] = $orderby;
			} else {
				$query_args['orderby'] = $order; // THIS IS THE FALLBACK
			}

			// Tax Query
			if ( ! empty( $taxonomy ) ) {

				// Include Terms
				if (  ! empty( $terms ) ) {

					// Sanitize terms and convert to array
					$terms = str_replace( ', ', ',', $terms );
					$terms = explode( ',', $terms );

					// Add to query arg
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms,
						'operator' => 'IN',
					);

				}

				// Exclude Terms
				if ( ! empty( $terms_exclude ) ) {

					// Sanitize terms and convert to array
					$terms_exclude = str_replace( ', ', ',', $terms_exclude );
					$terms_exclude = explode( ',', $terms_exclude );

					// Add to query arg
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms_exclude,
						'operator' => 'NOT IN',
					);

				}

			}

			// Exclude current post
			if ( is_singular() ) {
				$query_args['post__not_in'] = array( get_the_ID() );
			}

			// Query posts
			$wpex_query = new WP_Query( $query_args );

			// Set post counter variable
			$count=0;

			// Hover classes
			$img_hover_classes = '';
			if ( function_exists( 'wpex_image_hover_classes' ) ) {
				$img_hover_classes = wpex_image_hover_classes( $img_hover );
				$img_hover_classes = $img_hover_classes ? ' class="' . esc_attr( $img_hover_classes ) .'"' : '';
			}

			// Column classes
			$columns_class = function_exists( 'wpex_grid_class' ) ? wpex_grid_class( $columns ) : '';

			// Loop through posts
			while ( post_type_exists( $post_type ) && $wpex_query->have_posts() ) : $wpex_query->the_post();

				$count++;

				$post_link = function_exists( 'wpex_get_permalink' ) ? wpex_get_permalink() : get_the_permalink();

				$output .= '<li class="' . $columns_class .' nr-col col-' . esc_attr( $count ) . '">';

					$output .= '<a href="' . esc_url( $post_link ) . '"' . esc_attr( $img_hover_classes ) . '>';

						if ( function_exists( 'wpex_get_post_thumbnail' ) ) {
							$output .= wpex_get_post_thumbnail( array(
								'size'   => $img_size,
								'width'  => $img_width,
								'height' => $img_height,
								'crop'   => $img_crop_location,
							) );
						} else {
							$output .= get_the_post_thumbnail( get_the_ID(), $img_size );
						}

					$output .= '</a>';

				$output .= '</li>';

				// Reset counter to clear floats
				if ( $count == $columns ) {

					$count = '0';

				}

			// End loop
			endwhile;

			// Reset global query post data
			wp_reset_postdata();

		$output .= '</ul>';

		// Echo output
		echo $output;

		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\PostsGridWidget' );