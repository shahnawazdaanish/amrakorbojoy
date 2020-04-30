<?php
/**
 * Video widget
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
class VideoWidget extends WidgetBuilder {
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_video',
			'name'    => $this->branding() . esc_html__( 'Video', 'total-theme-core' ),
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
					'id'          => 'video_url',
					'label'       => esc_html__( 'Video URL', 'total-theme-core' ),
					'type'        => 'url',
					'description' => esc_html__( 'Enter in a video URL that is compatible with WordPress\'s built-in oEmbed feature.', 'total-theme-core' ) . '<a href="http://codex.wordpress.org/Embeds" target="_blank">' . esc_html__( 'Learn More', 'total-theme-core' ) . '</a></span>'
				),
				array(
					'id'    => 'video_description',
					'label' => esc_html__( 'Description', 'total-theme-core' ),
					'type'  => 'textarea',
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

		// Show video
		if ( $video_url )  {

			$output .= '<div class="responsive-video-wrap clr">';

				$output .= wp_oembed_get( $video_url, array(
					'width' => 270
				) );

			$output .= '</div>';

		} else {

			$output .= esc_html__( 'You forgot to enter a video URL.', 'total-theme-core' );

		}

		// Show video description if field isn't empty
		if ( $video_description ) {

			$output .= '<div class="wpex-video-widget-description">' . wp_kses_post( $video_description ) . '</div>';

		}

		// Echo output
		echo $output;

		// After widget hook
		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\VideoWidget' );