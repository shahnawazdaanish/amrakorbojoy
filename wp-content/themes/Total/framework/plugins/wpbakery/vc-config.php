<?php
/**
 * Visual Composer configuration file
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start class
class WPEX_Visual_Composer_Config {

	/**
	 * Start things up.
	 */
	public function __construct() {

		// Define useful Paths
		define( 'WPEX_VCEX_DIR', WPEX_FRAMEWORK_DIR . 'plugins/wpbakery/' );
		define( 'WPEX_VCEX_DIR_URI', WPEX_FRAMEWORK_DIR_URI . 'plugins/wpbakery/' );

		// Helper functions
		require_once WPEX_VCEX_DIR . 'vc-helpers.php';

		// Global post CSS
		require_once WPEX_VCEX_DIR . 'vc-global-post-css.php';

		// Disable Welcome message
		require_once WPEX_VCEX_DIR . 'vc-disable-welcome.php';

		// Remove core elements
		require_once WPEX_VCEX_DIR . 'vc-remove-elements.php';

		// Register accent colors
		require_once WPEX_VCEX_DIR . 'vc-accent-color.php';

		// Deprecate old vc row design options
		if ( apply_filters( 'wpex_vc_parse_deprecated_css_options', true ) ) {
			require_once WPEX_VCEX_DIR . 'vc-parse-deprecated-row-css.php';
		}

		// Alter core vc modules
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_section.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_row.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_column.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_single_image.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_column_text.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_tabs_tour.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc_toggle.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc-add-params.php';
		require_once WPEX_VCEX_DIR . 'shortcode-mods/vc-modify-params.php';

		// Parse attributes
		require_once WPEX_VCEX_DIR . 'parse-atts/row-atts.php';

		// Alter the font container tags
		require_once WPEX_VCEX_DIR . 'vc-font-container.php';

		// Disable functions for non active VC licenses
		if ( wpex_vc_theme_mode_check() ) {
			require_once WPEX_VCEX_DIR . 'vc-disable-design-options.php';
			require_once WPEX_VCEX_DIR . 'vc-disable-updater.php';
			require_once WPEX_VCEX_DIR . 'vc-disable-template-library.php';
		}

		// Templatera tweaks
		if ( WPEX_TEMPLATERA_ACTIVE ) {
			require_once WPEX_VCEX_DIR . 'templatera.php';
		}

		// Run on init
		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'admin_init', array( $this, 'admin_init' ), 20 );

		// Tweak scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_composer_front_css' ), 0 );
		add_action( 'wp_footer', array( $this, 'remove_footer_scripts' ) );

		// Admin/iFrame scrips
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'vc_load_iframe_jscss', array( $this, 'iframe_scripts' ) );
		add_action( 'vc_inline_editor_page_view', array( $this, 'editor_scripts' ), PHP_INT_MAX );

		// Popup scripts
		add_action( 'vc_frontend_editor_enqueue_js_css', array( $this, 'popup_scripts' ) );
		add_action( 'vc_backend_editor_enqueue_js_css', array( $this, 'popup_scripts' ) );

		// Add Customizer settings
		add_filter( 'wpex_customizer_panels', array( $this, 'customizer_settings' ) );

		// Remove default templates => Do not edit due to extension plugin and snippets
		add_filter( 'vc_load_default_templates', '__return_empty_array' );

		// Add noscript tag for stretched rows
		if ( apply_filters( 'wpex_noscript_tags', true ) ) {
			add_action( 'wp_head', array( $this, 'noscript' ), 60 );
		}

		// Add new background styles
		add_filter( 'vc_css_editor_background_style_options_data', array( $this, 'background_styles' ) );

		// Disable builder completely on admin post types
		add_filter( 'vc_is_valid_post_type_be', array( $this, 'disable_editor' ), 10, 2 );

		// Add custom params to vc iframe URL
		add_filter( 'vc_frontend_editor_iframe_url', array( $this, 'vc_frontend_editor_iframe_url' ) );

		// Add typography settings
		add_filter( 'wpex_typography_settings', array( $this, 'typography_settings' ) );

	}

	/**
	 * Functions that run on init.
	 */
	public function init() {

		if ( function_exists( 'visual_composer' ) ) {
			remove_action( 'wp_head', array( visual_composer(), 'addMetaData' ) );
		}

		if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
			vc_set_default_editor_post_types( array( 'page', 'portfolio', 'staff' ) );
		}

	}

	/**
	 * Functions that run on admin_init.
	 */
	public function admin_init() {

		// Tweak VC logo - remove's their link
		add_filter( 'vc_nav_front_logo', array( $this, 'editor_nav_logo' ) );

		// Remove purchase notice
		wpex_remove_class_filter( 'admin_notices', 'Vc_License', 'adminNoticeLicenseActivation', 10 );

	}

	/**
	 * Override editor logo.
	 */
	public function editor_nav_logo() {
		return '<div id="vc_logo" class="vc_navbar-brand" aria-hidden="true"></div>';
	}

	/**
	 * Load js_composer_front CSS eaerly on for easier modification.
	 */
	public function load_composer_front_css() {
		wp_enqueue_style( 'js_composer_front' );
	}

	/**
	 * Remove scripts hooked in too late for me to remove on wp_enqueue_scripts.
	 */
	public function remove_footer_scripts() {

		// JS (deprecated in 4.8)
		//wp_dequeue_script( 'vc_pageable_owl-carousel' );
		//wp_dequeue_script( 'vc_grid-js-imagesloaded' );

		// Styles conflict with Total owl carousel styles
		wp_deregister_style( 'vc_pageable_owl-carousel-css-theme' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css-theme' );
		wp_deregister_style( 'vc_pageable_owl-carousel-css' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css' );

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
			'vcex-admin',
			wpex_asset_url( 'css/wpex-visual-composer-admin.css' ),
			array(),
			WPEX_THEME_VERSION
		);

		if ( is_rtl() ) {
			wp_enqueue_style(
				'vcex-admin-rtl',
				wpex_asset_url( 'css/wpex-visual-composer-admin-rtl.css' ),
				array(),
				WPEX_THEME_VERSION
			);
		}

	}

	/**
	 * iFrame Scripts.
	 */
	public function iframe_scripts() {
		wp_enqueue_style(
			'vcex-iframe-css',
			wpex_asset_url( 'css/wpex-visual-composer-iframe.css' ),
			array(),
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Editor Scripts.
	 */
	public function editor_scripts() {
		wp_enqueue_script(
			'wpex-vc_reload',
			wpex_asset_url( 'js/dynamic/wpbakery/wpex-vc_reload.min.js' ),
			array( 'jquery' ),
			'1.0',
			true
		);
	}

	/**
	 * Popup Window Scripts.
	 */
	public function popup_scripts() {

		wp_enqueue_script(
			'wpex-chosen-js',
			wpex_asset_url( 'lib/chosen/chosen.jquery.min.js' ),
			array( 'jquery' ),
			'1.4.1',
			true
		);

		wp_enqueue_style(
			'wpex-chosen-css',
			wpex_asset_url( 'lib/chosen/chosen.min.css' ),
			false,
			'1.4.1'
		);

		wp_enqueue_style(
			'vcex-admin',
			wpex_asset_url( 'css/wpex-visual-composer-admin.css' ),
			array(),
			WPEX_THEME_VERSION
		);

	}

	/**
	 * Adds Customizer settings for VC.
	 */
	public function customizer_settings( $panels ) {
		$panels['visual_composer'] = array(
			'title'      => esc_html__( 'WPBakery Builder', 'total' ),
			'settings'   => WPEX_VCEX_DIR . 'vc-customizer-settings.php',
			'is_section' => true,
		);
		return $panels;
	}

	/**
	 * Add noscript tag for stretched rows.
	 */
	public function noscript() {
		echo '<noscript><style>body .wpex-vc-row-stretched, body .vc_row-o-full-height { visibility: visible; }</style></noscript>';
	}

	/**
	 * Add noscript tag for stretched rows.
	 */
	public function background_styles( $styles ) {
		$styles[ esc_html__( 'Repeat-x', 'total' ) ] = 'repeat-x';
		$styles[ esc_html__( 'Repeat-y', 'total' ) ] = 'repeat-y';
		return $styles;
	}

	/**
	 * Disable builder completely on admin post types.
	 */
	public function disable_editor( $check, $type ) {
		$excluded_types = array( 'attachment', 'acf', 'wpex_sidebars', 'acf-field-group', 'elementor_library' );
		if ( in_array( $type, $excluded_types) ) {
			return false;
		}
		return $check;
	}

	/**
	 * Disable builder completely on admin post types.
	 */
	public function vc_frontend_editor_iframe_url( $url ) {
		if ( $url ) {
			if ( isset( $_GET[ 'wpex_inline_header_template_editor' ] ) ) {
				$url = $url . '&wpex_inline_header_template_editor=' . wp_strip_all_tags( $_GET[ 'wpex_inline_header_template_editor' ] );
			}
			if ( isset( $_GET[ 'wpex_inline_footer_template_editor' ] ) ) {
				$url = $url . '&wpex_inline_footer_template_editor=' . wp_strip_all_tags( $_GET[ 'wpex_inline_footer_template_editor' ] );
			}
		}
		return $url;
	}

	/**
	 * Add typography options for WPBakery
	 */
	public static function typography_settings( $settings ) {
		$settings['vcex_heading'] = array(
			'label' => esc_html__( 'Total Heading Module', 'total' ),
			'target' => '.vcex-heading',
			'margin' => true,
		);
		return $settings;
	}

}
new WPEX_Visual_Composer_Config();

/**
 * Fallback fix to prevent JS errors in the editor
 *
 * @todo move in to it's own file?
 */
if ( ! function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
	function vc_icon_element_fonts_enqueue( $font ) {
		switch ( $font ) {
			case 'openiconic':
				wp_enqueue_style( 'vc_openiconic' );
				break;
			case 'typicons':
				wp_enqueue_style( 'vc_typicons' );
				break;
			case 'entypo':
				wp_enqueue_style( 'vc_entypo' );
				break;
			case 'linecons':
				wp_enqueue_style( 'vc_linecons' );
				break;
			case 'monosocial':
				wp_enqueue_style( 'vc_monosocialiconsfont' );
				break;
			default:
				do_action( 'vc_enqueue_font_icon_element', $font ); // hook to custom do enqueue style
		}
	}
}