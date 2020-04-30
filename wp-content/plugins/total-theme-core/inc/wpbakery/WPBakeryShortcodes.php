<?php
/**
 * Custom WPBakery Shortcodes
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1
 */

namespace TotalThemeCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPBakeryShortcodes {
	public $is_admin;
	public $wpbakery_is_active;
	public $vc_is_inline;

	/**
	 * WPBakery Class Constructor.
	 */
	public function __construct() {

		$this->is_admin           = is_admin();
		$this->wpbakery_is_active = function_exists( 'vc_lean_map' );
		$this->vc_is_inline       = function_exists( 'vc_is_inline' ) && vc_is_inline();

		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/shortcodes-list.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/helpers.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/arrays.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/deprecated.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/scripts.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/loadmore.php';

		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/classes/VCEX_Query_Builder.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/classes/VCEX_Inline_Style.php';

		// Allow front-end editor support for templatera
		if ( $this->wpbakery_is_active && ( $this->is_admin || $this->vc_is_inline ) ) {
			require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/templatera-frontend.php';
		}

		// Include shortcodes
		$this->require_shortcode_classes();

		// VC only functions
		if ( $this->wpbakery_is_active ) {
			add_action( 'vc_before_mapping', array( $this, 'vc_before_mapping' ) );
			add_action( 'vc_after_mapping', array( $this, 'vc_after_mapping' ) );
		}

		// Add shortcodes to tinymce
		add_filter( 'wpex_shortcodes_tinymce_json', array( $this, 'shortcodes_tinymce' ) );

		// Backend WPBakery editor scripts
		if ( $this->wpbakery_is_active && ( $this->is_admin || $this->vc_is_inline ) ) {
			add_action( 'vc_inline_editor_page_view', array( $this, 'editor_scripts' ), PHP_INT_MAX );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		// Fronend scripts used for element design
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), PHP_INT_MAX );

	}

	/**
	 * Run functions before/needed for VC mapping.
	 */
	public function vc_before_mapping() {
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/edit-form-fields.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/iconpicker.php';
		require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/autocomplete.php';
		if ( function_exists( 'vc_add_shortcode_param' ) ) {
			require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/shortcode-params/load.php';
		}
	}

	/**
	 * Run functions after VC mapping.
	 */
	public function vc_after_mapping() {
		add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ), 999 );
	}

	/**
	 * Load shortcode classes.
	 */
	public function require_shortcode_classes() {

		$modules = vcex_builder_modules();

		if ( ! empty( $modules ) ) {

			foreach ( $modules as $key => $val ) {

				if ( is_array( $val ) ) {

					$condition = isset( $val['condition'] ) ? $val['condition'] : true;

					if ( $condition ) {

						$file = isset( $val['file'] ) ? $val['file'] : TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/shortcodes/' . wp_strip_all_tags( $key ) . '.php';

						require_once $file;

					}

				} else {

					require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/shortcodes/' . wp_strip_all_tags( $val ) . '.php';

				}

			}

		}

	}

	/**
	 * Add shortcodes to tinymce.
	 */
	public function shortcodes_tinymce( $data ) {

		if ( ! apply_filters( 'vcex_wpex_shortcodes_tinymce', true ) ) {
			return $data;
		}

		$data['shortcodes']['vcex_button'] = array(
			'text' => esc_html__( 'Button', 'total-theme-core' ),
			'insert' => '[vcex_button url="#" title="Visit Site" style="flat" align="left" color="black" size="small" target="self" rel="none"]Button Text[/vcex_button]',
		);

		$data['shortcodes']['vcex_divider'] = array(
			'text' => esc_html__( 'Divider', 'total-theme-core' ),
			'insert' => '[vcex_divider color="#dddddd" width="100%" height="1px" margin_top="20" margin_bottom="20"]',
		);

		$data['shortcodes']['vcex_divider_dots'] = array(
			'text' => esc_html__( 'Divider Dots', 'total-theme-core' ),
			'insert' => '[vcex_divider_dots color="#dd3333" margin_top="10" margin_bottom="10"]',
		);

		$data['shortcodes']['vcex_spacing'] = array(
			'text' => esc_html__( 'Spacing', 'total-theme-core' ),
			'insert' => '[vcex_spacing size="20px"]',
		);

		$data['shortcodes']['vcex_spacing'] = array(
			'text' => esc_html__( 'Spacing', 'total-theme-core' ),
			'insert' => '[vcex_spacing size="30px"]',
		);

		return $data;

	}

	/**
	 * Editor Scripts.
	 */
	public function editor_scripts() {
		wp_enqueue_script(
			'vcex-vc_reload',
			vcex_asset_url( 'js/backend/vcex-vc_reload.min.js' ),
			array( 'jquery' ),
			TTC_VERSION,
			true
		);
	}

	/**
	 * Adds scripts for custom module view.
	 */
	public function admin_print_scripts() {
		if ( 'post' != get_current_screen()->base ) {
			return false;
		}
		wp_enqueue_script(
			'vcex-js-view',
			vcex_asset_url( 'js/backend/vcex-js-view.min.js' ),
			array( 'jquery' ),
			'1.0',
			true
		);
	}

	/**
	 * Admin Scripts.
	 */
	public function admin_scripts( $hook ) {

		$hooks = array(
			'edit.php',
			'post.php',
			'post-new.php',
			'widgets.php',
			'toolset_page_ct-editor', // Support VC widget plugin
		);

		if ( ! in_array( $hook, $hooks ) ) {
			return;
		}

		wp_enqueue_style(
			'vcex-shortcodes-params',
			vcex_asset_url( 'css/vcex-shortcodes-params.css' ),
			array(),
			TTC_VERSION
		);

	}

	/**
	 * Frontend Scripts.
	 */
	public function frontend_scripts() {

		if ( ! apply_filters( 'vcex_enqueue_frontend_js', true ) ) {
			return;
		}

		$deps = array( 'jquery' );

		if ( defined( 'WPEX_THEME_JS_HANDLE' ) ) {
			$deps[] = WPEX_THEME_JS_HANDLE;
		}

		wp_enqueue_script(
			'vcex-front',
			vcex_asset_url( 'js/vcex-front.min.js' ),
			$deps,
			TTC_VERSION,
			true
		);

	}


}
new WPBakeryShortcodes();