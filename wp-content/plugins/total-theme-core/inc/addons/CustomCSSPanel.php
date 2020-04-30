<?php
/**
 * Custom CSS Admin Panel.
 *
 * @package Total Theme Core
 * @subpackage Post Types
 * @version 1.0.7
 */

namespace TotalThemeCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomCSSPanel {

	/**
	 * Start things up.
	 */
	public function __construct() {

		if ( ! defined( 'TOTAL_THEME_ACTIVE' ) ) {
			return; // Total only function
		}

		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );
		add_action( 'admin_bar_menu', array( $this, 'adminbar_menu' ), 999 );
		add_action( 'admin_init', array( $this,'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );

	}

	/**
	 * Add sub menu page for the custom CSS input.
	 */
	public function add_page() {
		$slug = defined( 'WPEX_THEME_PANEL_SLUG' ) ? WPEX_THEME_PANEL_SLUG : 'themes.php';
		add_submenu_page(
			$slug,
			__( 'Custom CSS', 'total' ),
			__( 'Custom CSS', 'total' ),
			'administrator',
			$slug . '-custom-css',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Add custom CSS to the adminbar since it will be used frequently.
	 */
	public function adminbar_menu( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$slug = defined( 'WPEX_THEME_PANEL_SLUG' ) ? WPEX_THEME_PANEL_SLUG : 'themes.php';
		$wp_admin_bar->add_node( array(
			'id'    => 'wpex_custom_css',
			'title' => esc_html__( 'Custom CSS', 'total' ),
			'href'  => esc_url( admin_url( 'admin.php?page=' . $slug . '-custom-css' ) ),
			'meta'  => array(
			'class' => 'wpex-custom-css',
			)
		) );
	}

	/**
	 * Load scripts.
	 */
	public function scripts( $hook ) {

		$prefix = defined( 'WPEX_ADMIN_PANEL_HOOK_PREFIX' ) ? WPEX_ADMIN_PANEL_HOOK_PREFIX : 'appearance_page_themes';

		// Only load script when needed
		if ( $prefix . '-custom-css' != $hook ) {
			return;
		}

		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return; // Added in 4.9+
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array(
			'type' => 'text/css'
		) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "wpex_custom_css", %s ); } );',
				wp_json_encode( $settings )
			)
		);

	}

	/**
	 * Register a setting and its sanitization callback.
	 */
	public function register_settings() {
		register_setting( 'wpex_custom_css', 'wpex_custom_css', array( $this, 'sanitize' ) );
	}

	/**
	 * Displays all messages registered to 'wpex-custom_css-notices'.
	 */
	public function notices() {
		settings_errors( 'wpex_custom_css_notices' );
	}

	/**
	 * Sanitization callback.
	 */
	public function sanitize( $option ) {
		$deprecated_custom_css = get_theme_mod( 'custom_css', null );

		// Sanitize and save theme mod
		if ( ! empty( $option ) ) {

			if ( function_exists( 'wp_get_custom_css' ) && ! $deprecated_custom_css ) {

				wp_update_custom_css_post( $option );

			} else {

				set_theme_mod( 'custom_css', wp_strip_all_tags( $option ) );

			}

		} else {

			if ( function_exists( 'wp_get_custom_css' ) && ! $deprecated_custom_css ) {

				wp_update_custom_css_post( '' );

			} else {

				remove_theme_mod( 'custom_css' );

			}

		}

		// Return notice
		add_settings_error(
			'wpex_custom_css_notices',
			esc_attr( 'settings_updated' ),
			__( 'Settings saved.', 'total' ),
			'updated'
		);

		// Lets save the custom CSS into a standard option as well for backup
		return $option;
	}

	/**
	 * Settings page output.
	 */
	public function create_admin_page() {

		if ( function_exists( 'wp_get_custom_css' ) && ! get_theme_mod( 'custom_css', null ) ) {
			$custom_css = wp_get_custom_css();
		} else {
			$custom_css = get_theme_mod( 'custom_css', null );
		} ?>

		<div class="wrap wpex-custom-css-panel-wrap">

			<h1><?php esc_html_e( 'Custom CSS', 'total' ); ?></h1>

			<div class="wpex-remember-to-save">
				<p><?php echo wp_kses_post( __( 'Don\'t forget to <a href="#">save your changes</a>', 'total' ) ); ?></p>
			</div>

			<div>
				<form method="post" action="options.php">
					<?php settings_fields( 'wpex_custom_css' ); ?>
					<table class="form-table">
						<tr valign="top">
							<td style="padding:0;">
								<textarea cols="70" rows="30" id="wpex_custom_css" name="wpex_custom_css"><?php echo wp_strip_all_tags( $custom_css ); ?></textarea>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>

		</div><!-- .wrap -->

	<?php }

}
new CustomCSSPanel();