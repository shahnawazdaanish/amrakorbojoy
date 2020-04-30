<?php
/**
 * About widget
 *
 * @package Total Theme Core
 * @subpackage Widgets
 * @version 1.1.2
 */

namespace TotalThemeCore;

// Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// Start class
class BusinessInfoWidget extends WidgetBuilder {
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->args = array(
			'id_base' => 'wpex_info_widget',
			'name'    => $this->branding() . esc_html__( 'Business Info', 'total-theme-core' ),
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
					'id'    => 'address',
					'label' => esc_html__( 'Address', 'total-theme-core' ),
					'type'  => 'textarea',
				),
				array(
					'id'    => 'phone_number',
					'label' => esc_html__( 'Phone Number', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'phone_number_mobile',
					'label' => esc_html__( 'Mobile Phone Number', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'phone_number_tel_link',
					'label' => esc_html__( 'Add "tel" link to the phone number?', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'fax_number',
					'label' => esc_html__( 'Fax Number', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'email',
					'label' => esc_html__( 'Email', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'          => 'email_label',
					'label'       => esc_html__( 'Email Label', 'total-theme-core' ),
					'type'        => 'text',
					'description' => esc_html__( 'Will display your email by default if this field is empty.', 'total-theme-core' ),
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

		$output .= '<div class="wpex-info-widget wpex-clr">';

		// Address
		if ( $address ) {

			$output .= '<div class="wpex-info-widget-address wpex-clr">';

				$output .= '<span class="ticon ticon-map-marker"></span>';

				$output .= wpautop( wp_kses_post( $address ) );

			$output .= '</div>';

		}

		// Phone number
		if ( $phone_number ) {

			$output .= '<div class="wpex-info-widget-phone wpex-clr">';

				$output .= '<span class="ticon ticon-phone"></span>';

				if ( true == wp_validate_boolean( $phone_number_tel_link ) ) {

					$output .= '<a href="tel:' . wp_strip_all_tags( $phone_number ) . '">' . wp_strip_all_tags( $phone_number ) . '</a>';

				} else {

					$output .= wp_strip_all_tags( $phone_number );

				}

			$output .= '</div>';

		}

		// Phone number mobile
		if ( $phone_number_mobile ) {

			$output .= '<div class="wpex-info-widget-phone-mobile wpex-clr">';

				$output .= '<span class="ticon ticon-mobile"></span>';

				if ( true == wp_validate_boolean( $phone_number_tel_link ) ) {

					$output .= '<a href="tel:' . wp_strip_all_tags( $phone_number_mobile ) . '">' . wp_strip_all_tags( $phone_number_mobile ) . '</a>';

				} else {

					$output .= wp_strip_all_tags( $phone_number_mobile );

				}

			$output .= '</div>';

		}

		// Fax number
		if ( $fax_number ) {

			$output .= '<div class="wpex-info-widget-fax wpex-clr">';

				$output .= '<span class="ticon ticon-fax"></span>';

				$output .= wp_strip_all_tags( $fax_number );

			$output .= '</div>';

		}

		// Email
		if ( $email ) {

			// Sanitize email
			$sanitize_email = sanitize_email( $email );
			$is_email       = is_email( $sanitize_email );

			// Spam protect email address
			$protected_email = $is_email ? antispambot( $sanitize_email ) : $sanitize_email;

			// Sanitize & fallback for email label
			$email_label = ( ! $email_label && $is_email ) ? $protected_email : $email_label;

			// Email output
			$output .= '<div class="wpex-info-widget-email wpex-clr">';

				$output .= '<span class="ticon ticon-envelope"></span>';

				if ( $is_email ) {

					$output .= '<a href="mailto:' . $protected_email . '">' . wp_strip_all_tags( $email_label ) . '</a>';

				} else {

					$parse_email_url = parse_url( $email );

					if ( ! empty( $parse_email_url['scheme'] ) ) {
						$output .= '<a href="' . esc_url( $email ) . '">' . wp_strip_all_tags( $email_label ) . '</a>';
					} else {
						$output .= wp_strip_all_tags( $email_label );
					}

				}

			$output .= '</div>';

		}

		$output .= '</div>';

		// Echo output
		echo $output;

		// After widget hook
		echo wp_kses_post( $args['after_widget'] );

	}

}
register_widget( 'TotalThemeCore\BusinessInfoWidget' );