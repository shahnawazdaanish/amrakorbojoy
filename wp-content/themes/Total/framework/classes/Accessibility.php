<?php
/**
 * Adds custom CSS to the site to tweak the main accent colors
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.6.1
 */

namespace TotalTheme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'wpex_accessibility_panel', true ) ) {
	return;
}

class Accessibility {

	/**
	 * Start things up
	 *
	 * @since 4.6.5
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 50 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 4.6.5
	 */
	public function add_page() {
		add_submenu_page(
			WPEX_THEME_PANEL_SLUG,
			esc_attr__( 'Accessibility', 'total' ),
			esc_attr__( 'Accessibility', 'total' ),
			'manage_options',
			WPEX_THEME_PANEL_SLUG . 'wpex-accessibility',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 *
	 * @since 4.6.5
	 */
	public function register_settings() {
		register_setting(
			'wpex_accessibility_settings',
			'wpex_accessibility_settings',
			array( $this, 'save_options' )
		);
	}

	/**
	 * Sanitization callback
	 *
	 * @since 4.6.5
	 */
	public function save_options( $options ) {

		if ( ! wp_verify_nonce( $_POST['wpex_accessibility_settings_nonce'], 'wpex_accessibility_settings' )  ) {
			return;
		}

		foreach ( $this->get_settings() as $k => $v ) {

			$type    = isset( $v['type'] ) ? $v['type'] : 'input';
			$default = isset( $v['default'] ) ? $v['default'] : null;

			if ( 'checkbox' == $type ) {

				if ( isset( $options[$k] ) ) {
					if ( ! $default ) {
						set_theme_mod( $k, true );
					} else {
						remove_theme_mod( $k );
					}
				} else {
					if ( $default ) {
						set_theme_mod( $k, false );
					} else {
						remove_theme_mod( $k );
					}
				}

			} else {

				if ( ! empty( $options[$k] ) && $default != $options[$k] ) {
					set_theme_mod( $k, wp_strip_all_tags( $options[$k] ) );
				} else {
					remove_theme_mod( $k );
				}

			}

		}

		$options = ''; // don't store in options, only in theme mod

		return;

	}

	/**
	 * Return array of settings
	 *
	 * @since 4.6.5
	 */
	public function get_settings() {
		return array(
			'skip_to_content' => array(
				'name'        => esc_html__( 'Skip to content link', 'total' ),
				'default'     => true,
				'type'        => 'checkbox',
				'description' => esc_html__( 'Enables the skip to content link when clicking tab as soon as your site loads.', 'total' ),
			),
			'skip_to_content_id' => array(
				'name'    => esc_html__( 'Skip to content ID', 'total' ),
				'default' => '#content',
				'type'    => 'text',
			),
			'remove_menu_ids' => array(
				'name'        => esc_html__( 'Remove Menu ID attributes', 'total' ),
				'default'     => false,
				'type'        => 'checkbox',
				'description' => esc_html__( 'Removes the ID attributes added by default in WordPress to each item in your menu.', 'total' ),
			),
			'aria_landmarks_enable' => array(
				'name'        => esc_html__( 'Aria Landmarks', 'total' ),
				'default'     => false,
				'type'        => 'checkbox',
				'description' => esc_html__( 'Enables the aria landmark tags in the theme which are disabled by default as they generate errors in the W3C checker.', 'total' ),
			),
			'main_menu_aria_label' => array(
				'name'    => esc_html__( 'Main Menu Aria Label', 'total' ),
				'type'    => 'text',
				'default' => esc_attr_x( 'Main menu', 'aria-label', 'total' ),
			),
			'mobile_menu_toggle_aria_label' => array(
				'name'    => esc_html__( 'Mobile Menu Toggle Aria Label', 'total' ),
				'type'    => 'text',
				'default' => esc_attr_x( 'Toggle mobile menu', 'aria-label', 'total' ),
			),
			'mobile_menu_aria_label' => array(
				'name'    => esc_html__( 'Mobile Menu Aria Label', 'total' ),
				'type'    => 'text',
				'default' => esc_attr_x( 'Mobile menu', 'aria-label', 'total' ),
			),
			'footer_callout_aria_label' => array(
				'name' => esc_html__( 'Footer Callout Aria Label', 'total' ),
				'type' => 'text',
			),
			'footer_menu_aria_label' => array(
				'name' => esc_html__( 'Footer Menu Aria Label', 'total' ),
				'type' => 'text',
				'default' => esc_attr_x( 'Footer menu', 'aria-label', 'total' ),
			),
			'disable_mobile_menu_focus_styles' => array(
				'name'        => esc_html__( 'Disable Mobile Menu Focus Styles', 'total' ),
				'default'     => true,
				'type'        => 'checkbox',
				'description' => esc_html__( 'Disables the focus styles for mobile devices so you don\'t see the outline when using a phone or tablet and opening and closing your mobile menu.', 'total' ),
			),
		);
	}

	/**
	 * Settings page output
	 *
	 * @since 4.6.5
	 */
	public function create_admin_page() { ?>

		<div class="wrap">

			<h1><?php esc_html_e( 'Accessibility', 'total' ); ?></h1>

			<form method="post" action="options.php">

				<?php settings_fields( 'wpex_accessibility_settings' ); ?>

				<table class="form-table">

					<?php foreach ( $this->get_settings() as $k => $v ) {

						$type        = isset( $v[ 'type' ] ) ? $v[ 'type' ] : 'input';
						$default     = isset( $v[ 'default' ] ) ? $v[ 'default' ] : null;
						$description = ! empty( $v[ 'description' ] ) ? $v[ 'description' ] : null;
						$theme_mod   = get_theme_mod( $k, $default ); ?>

						<tr valign="top">

							<th scope="row"><?php echo esc_html( $v['name'] ); ?></th>
							<td>

								<?php
								// Checkboxes
								if ( 'checkbox' == $type ) { ?>

									<?php if ( $description ) { ?>
										<label for="wpex_accessibility_settings[<?php echo esc_attr( $k ); ?>]">
									<?php } ?>

									<input id="wpex_accessibility_settings[<?php echo esc_attr( $k ); ?>]" type="checkbox" name="wpex_accessibility_settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $theme_mod ); ?>" <?php checked( $theme_mod, true ); ?> class="wpex-checkbox">

									<?php if ( $description ) { ?>
										<?php echo esc_html( $description ); ?>
										</label>
									<?php } ?>

								<?php }
								// Standard inputs
								else { ?>

									<input type="text" name="wpex_accessibility_settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $theme_mod ); ?>" <?php checked( $theme_mod, true ); ?> class="wpex-checkbox" />

									<?php if ( $description ) { ?>
										<p class="description"><?php echo esc_html( $description ); ?></p>
									<?php } ?>

								<?php } ?>
							</td>

						</tr>

					<?php } ?>

				</table>

				 <?php wp_nonce_field( 'wpex_accessibility_settings', 'wpex_accessibility_settings_nonce' ); ?>

				<?php submit_button(); ?>

			</form>

		</div>

	<?php }

	/**
	 * Run functions on init
	 *
	 * @since 4.6.5
	 */
	public function init() {

		// Remove menu id's
		if ( wpex_get_mod( 'remove_menu_ids', false ) ) {
			add_filter( 'nav_menu_item_id', '__return_false' );
		}

		// Remove focus styles
		if ( ! wpex_get_mod( 'focus_styles', true ) ) {
			add_filter( 'wpex_head_css', array( $this, 'remove_focus_styles' ) );
		}

	}

	/**
	 * Remove focus styles
	 *
	 * @since 4.6.5
	 */
	public function remove_focus_styles( $css ) {
		$css .= 'a:focus{outline:0;border:0;}';
		return $css;
	}

}
new Accessibility();