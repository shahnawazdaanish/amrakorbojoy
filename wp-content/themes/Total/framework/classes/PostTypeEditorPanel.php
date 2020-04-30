<?php
/**
 * Adds a Post Type Editor Panel for defined Post Types
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @since 4.9
 */

namespace TotalTheme;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class PostTypeEditorPanel {
	private $types;
	private $post_type;

	/**
	 * Main constructor
	 *
	 * @since 4.8
	 */
	public function __construct() {
		$this->types = apply_filters( 'wpex_post_type_editor_types', array( 'portfolio', 'staff', 'testimonials' ) );
		$this->post_type = ! empty( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_submenu_pages' ), 40 );
		add_action( 'admin_init', array( $this, 'register_page_options' ), 40 );
		add_action( 'admin_notices', array( $this, 'setting_notice' ), 40 );
	}

	/**
	 * Enqueue scripts for the Post Type Editor Panel
	 *
	 * @since 4.8
	 */
	public function enqueue_scripts( $hook ) {

		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		if ( ! $page && ! in_array( $page, array(
			'wpex-staff-editor',
			'wpex-portfolio-editor',
			'wpex-testimonials-editor',
		) ) ) {
			return;
		}

		wp_enqueue_style(
			'wpex-chosen-css',
			wpex_asset_url( 'lib/chosen/chosen.min.css' ),
			false,
			'1.4.1'
		);

		wp_enqueue_script(
			'wpex-chosen-js',
			wpex_asset_url( 'lib/chosen/chosen.jquery.min.js' ),
			array( 'jquery' ),
			'1.4.1'
		);

		wp_enqueue_media();

		wp_enqueue_style(
			'wpex-theme-panel',
			wpex_asset_url( 'css/wpex-theme-panel.css' ),
			array(),
			WPEX_THEME_VERSION
		);

		wp_enqueue_script(
			'wpex-theme-panel',
			wpex_asset_url( 'js/dynamic/admin/wpex-theme-panel.min.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			true
		);

	}

	/**
	 * Return array of settings
	 *
	 * @since 4.8
	 */
	public function get_settings( $type ) {
		return array(
			'page' => array(
				'label' => esc_html__( 'Main Page', 'total' ),
				'type'  => 'wp_dropdown_pages',
			),
			'admin_icon' => array(
				'label' => esc_html__( 'Admin Icon', 'total' ),
				'type'  => 'dashicon',
				'default' => array(
					'staff'        => 'groups',
					'portfolio'    => 'portfolio',
					'testimonials' => 'format-status',
				),
			),
			'has_archive' => array(
				'label' => esc_html__( 'Enable Auto Archive?', 'total' ),
				'type'  => 'checkbox',
				'description' => esc_html__( 'Disabled by default so you can create your archive page using a page builder.', 'total' ),
			),
			'custom_sidebar' => array(
				'label' => esc_html__( 'Enable Custom Sidebar?', 'total' ),
				'type'  => 'checkbox',
				'default' => 'on',
			),
			'search' => array(
				'label' => esc_html__( 'Include in Search Results?', 'total' ),
				'type'  => 'checkbox',
				'default' => 'on',
			),
			'labels' => array(
				'label' => esc_html__( 'Post Type: Name', 'total' ),
				'type'  => 'text',
			),
			'singular_name' => array(
				'label' => esc_html__( 'Post Type: Singular Name', 'total' ),
				'type'  => 'text',
			),
			'slug' => array(
				'label' => esc_html__( 'Post Type: Slug', 'total' ),
				'type'  => 'text',
			),
			'categories' => array(
				'label' => esc_html__( 'Enable Categories?', 'total' ),
				'type'  => 'checkbox',
				'default' => 'on',
			),
			'cat_labels' => array(
				'label' => esc_html__( 'Categories: Label', 'total' ),
				'type'  => 'text',
			),
			'cat_slug' => array(
				'label' => esc_html__( 'Categories: Slug', 'total' ),
				'type'  => 'text',
			),
			'tags' => array(
				'label' => esc_html__( 'Enable Tags?', 'total' ),
				'type'  => 'checkbox',
				'default' => 'on',
				'exclusive' => array( 'portfolio', 'staff' ),
			),
			'tag_labels' => array(
				'label' => esc_html__( 'Tag: Label', 'total' ),
				'type'  => 'text',
				'conditional' => 'has_tags',
				'exclusive' => array( 'portfolio', 'staff' ),
			),
			'tag_slug' => array(
				'label' => esc_html__( 'Tag: Slug', 'total' ),
				'type'  => 'text',
				'conditional' => 'has_tags',
				'exclusive' => array( 'portfolio', 'staff' )
			),
		);
	}

	/**
	 * Add sub menu page for the Staff Editor.
	 *
	 * @since 4.8
	 */
	public function get_default( $field ) {
		if ( isset( $field['default'] ) ) {
			return is_array( $field['default'] ) ? $field['default'][$this->post_type] : $field['default'];
		}
	}

	/**
	 * Add sub menu page for the Staff Editor.
	 *
	 * @since 4.8
	 */
	public function add_submenu_pages() {

		foreach ( $this->types as $type ) {

			$post_type_obj = get_post_type_object( $type );

			if ( ! is_object( $post_type_obj ) ) {
				continue;
			}

			$submenu_page = add_submenu_page(
				'edit.php?post_type=' . $type,
				$post_type_obj->labels->name . ' ' . esc_html__( 'Settings', 'total' ),
				__( 'Settings', 'total' ),
				'administrator',
				'wpex-' . $type . '-editor',
				array( $this, 'create_admin_page' )
			);

			add_action( 'load-' . $submenu_page, array( $this, 'flush_rewrite_rules' ) );

		}

	}

	/**
	 * Flush re-write rules
	 *
	 * @since 4.8
	 */
	public function flush_rewrite_rules() {
		if ( in_array( $this->post_type, $this->types ) ) {
			flush_rewrite_rules();
		}
	}

	/**
	 * Function that will register the staff editor admin page.
	 *
	 * @since 4.8
	 */
	public function register_page_options() {
		foreach ( $this->types as $type ) {
			register_setting( 'wpex_' . $type . '_editor_options', 'wpex_' . $type . '_editor', array( $this, 'sanitize' ) );
		}
	}

	/**
	 * Displays saved message after settings are successfully saved.
	 *
	 * @since 4.8
	 */
	public function setting_notice() {
		foreach ( $this->types as $type ) {
			settings_errors( 'wpex_' . $type . '_editor_options' );
		}
	}

	/**
	 * Sanitizes input and saves theme_mods.
	 *
	 * @since 4.8
	 */
	public function sanitize( $options ) {

		$post_type = ! empty( $options[ 'post_type'] ) ? $options[ 'post_type'] : '';

		// Save values to theme mod
		if ( ! empty ( $options ) && $post_type && in_array( $post_type, $this->types ) ) {

			$settings = $this->get_settings( $post_type );

			foreach ( $settings as $k => $v ) {

				if ( isset( $v['exclusive'] ) && ! in_array( $post_type, $v['exclusive'] ) ) {
					continue;
				}

				$mod_name = $post_type . '_' . $k;
				$type     = $v[ 'type' ];
				$default  = $this->get_default( $v );
				$value    = isset( $options[$mod_name] ) ? $options[$mod_name] : '';

				if ( 'checkbox' == $type ) {

					if ( $default ) {

						if ( $value ) {
							remove_theme_mod( $mod_name );
						} else {
							set_theme_mod( $mod_name, false );
						}

					} else {

						if ( $value ) {
							set_theme_mod( $mod_name, true );
						} else {
							remove_theme_mod( $mod_name );
						}

					}

				} else {

					if ( $value ) {
						set_theme_mod( $mod_name, $value );
					} else {
						remove_theme_mod( $mod_name );
					}

				}

			}

			// Add notice
			add_settings_error(
				'wpex_' . $post_type . '_editor_options',
				esc_attr( 'settings_updated' ),
				__( 'Settings saved and rewrite rules flushed.', 'total' ),
				'updated'
			);

		}

		// Lets delete the options as we are saving them into theme mods
		$options = '';
		return $options;

	}

	/**
	 * Output for the actual Staff Editor admin page.
	 *
	 * @since 4.8
	 */
	public function create_admin_page() {

		if ( ! in_array( $this->post_type, $this->types ) ) {
			wp_die();
		}

		$post_type_obj = get_post_type_object( $this->post_type ); ?>

		<div class="wrap">

			<h2><?php echo ucfirst( esc_html( $post_type_obj->labels->name ) ); ?> <?php esc_html_e( 'Settings', 'total' ); ?></h2>

			<form method="post" action="options.php">

				<table class="form-table">

					<?php

					settings_fields( 'wpex_' . $this->post_type . '_editor_options' );

					$settings = $this->get_settings( $this->post_type );

					foreach ( $settings as $k => $v ) {

						if ( isset( $v['exclusive'] ) && ! in_array( $this->post_type, $v['exclusive'] ) ) {
							continue;
						}

						$method = 'field_' . $v['type'];

						echo '<tr valign="top">';

						echo '<th scope="row">' . $v['label'] . '</th>';

						if ( method_exists( $this, $method ) ) {

							$mod_name     = $this->post_type . '_' . $k;
							$v['default'] = $this->get_default( $v );
							$v['id']      = 'wpex_' . $this->post_type . '_editor[' . $mod_name . ']';
							$mod_v        = wpex_get_mod( $mod_name, $v['default'] );

							if ( 'checkbox' == $v['type'] ) {
								$v['value'] = ( $mod_v && 'off' !== $mod_v ) ? 'on' : 'off';
							} else {
								$v['value'] = $mod_v;
							}

							$description = isset( $v['description'] ) ? '<span class="description" style="padding-left:5px;">' . esc_html( $v['description'] ) . '</small>' : '';

							echo '<td>' . $this->$method( $v ) . $description . '</td>';


						}

						echo '</tr>';

					} ?>

				</table>

				<input type="hidden" name="wpex_<?php echo esc_attr( $this->post_type ); ?>_editor[post_type]" value="<?php echo esc_attr( $this->post_type ); ?>" />

				<?php submit_button(); ?>

			</form>

		</div>

	<?php }

	/**
	 * Return wp_dropdown_pages field
	 *
	 * @since 4.8
	 *
	 * @access private
	 * @return string
	 */
	private function field_wp_dropdown_pages( $field ) {

		return wp_dropdown_pages( array(
			'echo'             => false,
			'selected'         => $field['value'],
			'name'             => $field['id'],
			'id'               => $field['id'],
			'class'            => 'wpex-chosen',
			'show_option_none' => esc_html__( 'None', 'total' ),
		) );

	}

	/**
	 * Return text field
	 *
	 * @since 4.8
	 *
	 * @access private
	 * @return string
	 */
	private function field_text( $field ) {

		$output = '';

		$output .= '<input type="text"';

		$output .= ' id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '"';

		if ( isset( $field['size'] ) ) {
			$output .= ' size="' . esc_attr( $field['size'] ) . '" ';
		}

		$output .= ' />';

		return $output;

	}

	/**
	 * Return checkbox field
	 *
	 * @since 4.8
	 *
	 * @access private
	 * @return string
	 */
	private function field_checkbox( $field ) {

		$output = '';

		$output .= '<input type="checkbox"';

			if ( isset( $field['class'] ) ) {
				$output .= ' class="' . esc_attr( $field['class'] ) . '"';
			}

			$output .= ' id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '"';

			$output .= ' ' . checked( $field['value'], 'on', false );

		$output .= ' />';

		return $output;

	}

	/**
	 * Return dashicon field
	 *
	 * @since 4.8
	 *
	 * @access private
	 * @return string
	 */
	private function field_dashicon( $field ) {

		$output = '';

		$dashicons = wpex_get_dashicons_array();

		$output .= '<div id="wpex-dashicon-select" class="wpex-clr">';

			foreach ( $dashicons as $k => $v ) {

				$class = $field['value'] == $k ? 'button-primary' : 'button-secondary';

				$output .= '<a href="#" data-value="' . esc_attr( $k ) . '" class="' . $class . '"><span class="dashicons dashicons-' . esc_attr( $k ) .'"></span></a>';

			}

		$output .= '</div>';

		$output .= '<input type="hidden" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) .'"></td>';

		return $output;

	}

}
new PostTypeEditorPanel();