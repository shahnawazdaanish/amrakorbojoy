<?php
/**
 * Class for easily adding term meta settings
 *
 * @package Total Theme Core
 * @subpackage Meta
 * @version 1.1.2
 */

namespace TotalThemeCore;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class TermMeta {

	/**
	 * Main constructor.
	 */
	public function __construct() {

		// Register meta options
		// Not needed since it only is used for sanitization which we do last
		//add_action( 'init', array( $this, 'register_meta' ) );

		// Admin init
		add_action( 'admin_init', array( $this, 'meta_form_fields' ), 40 );

	}

	/**
	 * Array of meta options.
	 */
	public function meta_options() {

		// Get array of widget areas
		$widget_areas = array( esc_html__( 'Default', 'total-theme-core' ) );

		if ( function_exists( 'wpex_get_widget_areas' ) ) {
			$widget_areas = $widget_areas + wpex_get_widget_areas();
		}

		// Return meta array
		return apply_filters( 'wpex_term_meta_options', array(

			// Redirect
			'wpex_redirect' => array(
				'label'     => esc_html__( 'Redirect', 'total-theme-core' ),
				'type'      => 'wp_dropdown_pages',
				'args'      => array(
					'sanitize_callback' => 'esc_html',
				),
			),

			// Sidebar select
			'wpex_sidebar' => array(
				'label'    => esc_html__( 'Sidebar', 'total-theme-core' ),
				'type'     => 'select',
				'choices'  => $widget_areas,
				'args'      => array(
					'sanitize_callback' => 'esc_html',
				),
			),

		) );

	}

	/**
	 * Add meta form fields.
	 */
	public function meta_form_fields() {

		// Get taxonomies
		$taxonomies = apply_filters( 'wpex_term_meta_taxonomies', get_taxonomies( array(
			'public' => true,
		) ) );

		// Return if no taxes defined
		if ( ! $taxonomies ) {
			return;
		}

		// Loop through taxonomies
		foreach ( $taxonomies as $taxonomy ) {

			// Add forms
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'add_form_fields' ) );

			// Save forms
			add_action( 'edited_' . $taxonomy, array( $this, 'save_forms' ), 10, 3 );

		}

	}

	/**
	 * Register meta options.
	 */
	public function register_meta() {

		// Define meta options array on init
		$meta_options = $this->meta_options();

		// Define meta args
		$args = array();

		// Loop through meta options
		foreach( $meta_options as $key => $val ) {
			$args = isset( $val['args'] ) ? $val['args'] : array();
			register_meta( 'term', $key, $args );
		}

	}

	/**
	 * Adds new category fields.
	 */
	public function add_form_fields( $tag ) {

		// Nonce
		wp_nonce_field( 'wpex_term_meta_nonce', 'wpex_term_meta_nonce' );

		// Get options
		$meta_options = $this->meta_options();

		// Loop through options
		foreach ( $meta_options as $key => $val ) {
			$this->meta_form_field( $key, $val, $tag );
		}

	}

	/**
	 * Saves meta fields.
	 */
	public function save_forms( $term_id ) {

		// Make sure everything is secure
		if ( empty( $_POST['wpex_term_meta_nonce'] )
			|| ! wp_verify_nonce( $_POST['wpex_term_meta_nonce'], 'wpex_term_meta_nonce' )
		) {
			return;
		}

		// Get options
		$meta_options = $this->meta_options();

		// Loop through options
		foreach ( $meta_options as $key => $val ) {

			// Check option value
			$value = isset( $_POST[$key] ) ? $_POST[$key] : '';

			// Save setting
			if ( $value ) {
				update_term_meta( $term_id, $key, wp_strip_all_tags( $value ) );
			}

			// Delete setting
			else {
				delete_term_meta( $term_id, $key );
			}

		}

	}

	/**
	 * Outputs the form field.
	 */
	public function meta_form_field( $key, $val, $tag ) {

		// Get data
		$label    = isset( $val['label'] ) ? $val['label'] : '';
		$type     = isset( $val['type'] ) ? $val['type'] : 'text';
		$term_id  = $tag->term_id;
		$value    = get_term_meta( $term_id, $key, true );

		// Text
		if ( 'text' === $type ) { ?>

			<tr class="form-field">
				<th scope="row" valign="top"><label for="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td><input type="text" name="<?php echo esc_html( $key ); ?>" value="<?php echo esc_html( $value ); ?>" /></td>
			</tr>

		<?php }

		// Select
		if ( 'select' === $type ) {

			$choices = isset( $val['choices'] ) ? $val['choices'] : '';

			if ( $choices ) { ?>

				<tr class="form-field">
					<th scope="row" valign="top"><label for="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td>
						<select name="<?php echo esc_html( $key ); ?>">
							<?php foreach ( $choices as $key => $val ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ) ?>><?php echo esc_html( $val ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

			<?php }

		}

		// Select
		if ( 'wp_dropdown_pages' === $type ) {

			$args = array(
				'name'             => $key,
				'selected'         => $value,
				'show_option_none' => esc_html__( 'None', 'total-theme-core' )
			); ?>

				<tr class="form-field">
					<th scope="row" valign="top"><label for="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td><?php wp_dropdown_pages( $args ); ?></td>
				</tr>

		<?php }

	}

}
new TermMeta();