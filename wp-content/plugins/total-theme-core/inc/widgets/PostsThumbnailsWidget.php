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
class PostsThumbnailsWidget extends WidgetBuilder {
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_recent_posts_thumb',
			'name'    => $this->branding() . esc_html__( 'Posts With Thumbnails', 'total-theme-core' ),
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
					'default' => 3,
				),
				array(
					'id'      => 'style',
					'label'   => esc_html__( 'Style', 'total-theme-core' ),
					'type'    => 'select',
					'default' => 'default',
					'choices' => array(
						'default' => esc_html__( 'Small Image', 'total-theme-core' ),
						'fullimg' => esc_html__( 'Full Image', 'total-theme-core' )
					),
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
					'id'          => 'custom_query_args',
					'label'       => esc_html__( 'Custom Query Callback', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter a custom callback function name to return your own arguments for the query. Your callback function should return an array of arguments for the WP_Query class.', 'total-theme-core' ),
				),
				array(
					'id'          => 'excerpt_length',
					'label'       => esc_html__( 'Excerpt Length', 'total-theme-core' ),
					'type'        => 'number',
					'default'     => 0,
					'description' => esc_html__( 'Enter a value to display an excerpt with chose number of words.', 'total-theme-core' ),
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
					'id'    => 'add_img_width',
					'label' => esc_html__( 'Apply width?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'description' => esc_html__( 'By default the image width value is used for cropping only. Check this box to actually alter your image size to the defined width.', 'total-theme-core' ),
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
				array(
					'id'    => 'date',
					'label' => esc_html__( 'Disable Date?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'thumbnail_query',
					'label' => esc_html__( 'Post With Thumbnails Only?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'   => 'on',
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

		$post_type = ! empty( $post_type ) ? $post_type : 'post';

		// Custom Query args
		if ( ! empty( $custom_query_args ) && is_callable( $custom_query_args ) ) {
			$query_args = (array) call_user_func( $custom_query_args );
		}

		// Widget query args
		else {

			$query_args = array(
				'post_type'      => array( $post_type ),
				'posts_per_page' => $number,
				'no_found_rows'  => true,
				'tax_query'      => array(
					'relation' => 'AND',
				),
			);

			// Query by thumbnail meta_key
			if ( $thumbnail_query ) {
				$query_args['meta_query'] = array( array( 'key' => '_thumbnail_id' ) );
			}

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

			// Singular post arguments
			if ( is_singular() ) {

				$query_args['post__not_in'] = array( get_the_ID() );

				// Exclude expired events
				if ( 'tribe_events' == $post_type
					&& 'tribe_events' == get_post_type()
					&& class_exists( 'Tribe__Events__Main' )
					&& apply_filters( 'wpex_recent_posts_thumb_widget_exclude_past_events', true )
				) {
					$expired_events = get_posts( array(
						'post_type'     => 'tribe_events',
						'nopaging'      => true,
						'fields'        => 'ids',
						'meta_query'    => array(
							array(
								'key'       => '_EventEndDate',
								'value'     => date( 'Y-m-d H:i:s' ),
								'compare'   => '<',
								'type'      => 'DATETIME',
							),
						),
					) );
					if ( $expired_events ) {
						if ( isset( $query_args['post__not_in'] ) ) {
							$ids = array_merge( $query_args['post__not_in'], $expired_events );
						} else {
							$ids = $expired_events;
						}
						$ids = array_map( 'absint', $ids );
						$query_args['post__not_in'] = array_unique( $ids );
					}
				}

			}

		}

		// Query posts
		$wpex_query = new WP_Query( $query_args );

		// If there are posts loop through them
		if ( post_type_exists( $post_type ) && $wpex_query->have_posts() ) :

			// Begin entries output
			$output .= '<ul class="wpex-widget-recent-posts wpex-clr style-' . esc_attr( $style ) . '">';

					// Loop through posts
					while ( $wpex_query->have_posts() ) : $wpex_query->the_post();

						// Check thumb
						$has_thumb = has_post_thumbnail();

						// Li classes
						$li_classes = 'wpex-widget-recent-posts-li clr';
						if ( ! $has_thumb ) {
							$li_classes .= ' wpex-no-thumb';
						}

						// Output entry
						$output .= '<li class="' . esc_attr( $li_classes ) . '">';

							// Get post link
							$post_link = function_exists( 'wpex_get_permalink' ) ? wpex_get_permalink() : get_permalink();

							// Get post title attribute
							if ( function_exists( 'wpex_get_esc_title' ) ) {
								$esc_title = wpex_get_esc_title();
							} else {
								$esc_title = the_title_attribute( array(
									'echo' => false,
								) );
							}

							// Entry thumbnail
							if ( $has_thumb ) {

								// Inline CSS
								$inline_css = '';
								if ( $add_img_width && $img_width ) {
									$inline_css = ' style="width:' . intval( $img_width ) . 'px"';
								}

								// Thumb chasses
								$thumb_classes = 'wpex-widget-recent-posts-thumbnail';
								if ( function_exists( 'wpex_image_hover_classes' ) && $img_hover = esc_attr( $img_hover ) ) {
									$thumb_classes .= ' ' . wpex_image_hover_classes( $img_hover );
								}

								$output .= '<a href="' . esc_url( $post_link ) . '" title="' . $esc_title . '" class="' . esc_attr( $thumb_classes ) . '"' . $inline_css . '>';

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

							}

							// Entry details
							$output .= '<div class="details clr">';

								$output .= '<a href="' . esc_url( $post_link ) . '" class="wpex-widget-recent-posts-title">' . esc_html( get_the_title() ) . '</a>';

								// Display date if enabled
								if ( '1' != $date ) {

									if ( class_exists( 'Tribe__Events__Main' )
										&& 'tribe_events' == get_post_type()
										&& function_exists( 'wpex_get_tribe_event_date' )
									) {
										$the_date = wpex_get_tribe_event_date( 'wpex_recent_posts_thumb_widget' );
									} else {
										$the_date = get_the_date();
									}

									$output .= '<div class="wpex-widget-recent-posts-date">' . esc_html( $the_date ) . '</div>';

								}

								// Display excerpt
								if ( intval( $excerpt_length ) && 0 !== $excerpt_length ) {

									if ( function_exists( 'wpex_get_excerpt' ) ) {

										$excerpt = wpex_get_excerpt( array(
											'length'          => $excerpt_length,
											'context'         => 'wpex_recent_posts_thumb_widget',
											'custom_excerpts' => false,
										) );

									} else {

										$excerpt = wp_trim_words( get_the_excerpt(), absint( $excerpt_length ) );

									}

									if ( $excerpt ) {

										$output .= '<div class="wpex-widget-recent-posts-excerpt">' . $excerpt . '</div>';

									}

								}

							$output .= '</div>';

						$output .= '</li>';

					endwhile;

			$output .= '</ul>';

			// Reset post data
			wp_reset_postdata();

		endif;

		// Echo output
		echo $output;

		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\PostsThumbnailsWidget' );