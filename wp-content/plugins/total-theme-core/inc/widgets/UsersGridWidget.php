<?php
/**
 * About widget
 *
 * @package Total Theme Core
 * @subpackage Widgets
 * @version 1.0
 */

namespace TotalThemeCore;

// Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// Start class
class UsersGridWiget extends WidgetBuilder {
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_users_grid',
			'name' => $this->branding() . esc_html__( 'Users', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields'  => array(
				array(
					'id'    => 'title',
					'label' => esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'          => 'class',
					'label'       => esc_html__( 'Custom Class', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Optional classname for styling purposes.', 'total-theme-core' ),
				),
				array(
					'id'      => 'order',
					'label'   => esc_html__( 'Order', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'query_order',
					'default' => 'ASC',
				),
				array(
					'id'      => 'orderby',
					'label'   => esc_html__( 'Orderby', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => array(
						'ID'           => esc_html__( 'ID', 'total-theme-core' ),
						'login'        => esc_html__( 'Login', 'total-theme-core' ),
						'nicename'     => esc_html__( 'Nicename', 'total-theme-core' ),
						'email'        => esc_html__( 'Email', 'total-theme-core' ),
						'url'          => esc_html__( 'URL', 'total-theme-core' ),
						'registered'   => esc_html__( 'Registered', 'total-theme-core' ),
						'display_name' => esc_html__( 'Display Name', 'total-theme-core' ),
						'post_count'   => esc_html__( 'Post Count', 'total-theme-core' ),
					),
					'default' => 'login',
				),
				array(
					'id'      => 'columns',
					'label'   => esc_html__( 'Columns', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_columns',
					'default' => '4',
				),
				array(
					'id'      => 'columns_gap',
					'label'   => esc_html__( 'Column Gap', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'grid_gaps',
					'default' => '10',
				),
				array(
					'id'      => 'img_size',
					'label'   => esc_html__( 'Image Size', 'total-theme-core' ),
					'type'    => 'text',
					'default' => '70',
				),
				array(
					'id'      => 'img_hover',
					'label'   => esc_html__( 'Image Hover', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'image_hovers',
				),
				array(
					'id'    => 'admins',
					'label' => esc_html__( 'Include Administrators?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'   => 'on',
				),
				array(
					'id'    => 'editors',
					'label' => esc_html__( 'Include Editors?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'   => 'on',
				),
				array(
					'id'    => 'authors',
					'label' => esc_html__( 'Include Authors?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'  => 'on',
				),
				array(
					'id'    => 'contributors',
					'label' => esc_html__( 'Include Contributors?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'   => 'on',
				),
				array(
					'id'    => 'subscribers',
					'label' => esc_html__( 'Include Subscribers?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'link_to_posts',
					'label' => esc_html__( 'Link to user posts page?', 'total-theme-core' ),
					'type'  => 'checkbox',
					'std'   => 'on',
				),
				array(
					'id'    => 'show_name',
					'label' => esc_html__( 'Display Name?', 'total-theme-core' ),
					'type'  => 'checkbox',
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

		// Query users
		$query_args = array(
			'orderby' => $orderby,
			'order'   => $order,
		);
		$role_in = array();
		if ( $admins ) {
			$role_in[] = 'administrator';
		}
		if ( $authors ) {
			$role_in[] = 'author';
		}
		if ( $contributors ) {
			$role_in[] = 'contributor';
		}
		if ( $subscribers ) {
			$role_in[] = 'subscriber';
		}
		if ( $role_in ) {
			$query_args['role__in'] = $role_in;
		}

		$get_users = get_users( $query_args );

		if ( $get_users ) {

			$columns_gap = function_exists( 'wpex_gap_class' ) ? ' ' . wpex_gap_class( $columns_gap ) : '';

			$output .= '<ul class="wpex-users-widget wpex-row clr' . esc_attr( $columns_gap ) . '">';

				$count=0;

				$img_hover_class = function_exists( 'wpex_image_hover_classes' ) ? ' ' . wpex_image_hover_classes( $img_hover ) : '';

				foreach ( $get_users as $user ) :

					$count++;
					$classes = '';
					if ( function_exists( 'wpex_grid_class' ) ) {
						$classes = 'nr-col clr';
						$classes .= ' ' . wpex_grid_class( $columns );
						$classes .= ' col-' . $count;
					}

					$output .= '<li class="' . esc_attr( $classes ) . '">';

						// Open link tag
						if ( $link_to_posts ) {

							$output .= '<a href="' . esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ) . '" title="' . esc_attr( $user->display_name ) . ' ' . esc_html__( 'Archive', 'total-theme-core' ) . '">';

						}

						// Display avatar
						$output .= '<div class="wpex-users-widget-avatar' . esc_attr( $img_hover_class ) . '">';

							$output .= get_avatar( $user->ID, $img_size, '', $user->display_name );

						$output .= '</div>';

						// Display name
						if ( $show_name ) {

							$output .= '<div class="wpex-users-widget-name entry-title">';

								$output .= esc_html( $user->display_name );

							$output .= '</div>';

						}

						// Close link
						if ( $link_to_posts ) {
							$output .= '</a>';
						}

					$output .= '</li>';

				// Clear columns
				if ( $columns == $count ) {
					$count = 0;
				}

				// End loop
				endforeach;

			// Close ul wrap
			$output .= '</ul>';

		}

		// Echo output
		echo $output;

		// After widget hook
		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\UsersGridWiget' );