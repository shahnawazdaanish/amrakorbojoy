<?php
/**
 * Plugin Name: Total Theme Core
 * Plugin URI:  https://wpexplorer-themes.com/total/docs/total-theme-core-plugin/
 * Description: Adds core functionality to the Total WordPress theme including post types, shortcodes, builder modules meta options and more. This is a required plugin for the Total theme and can only be disabled via a child theme or by switching themes.
 * Version:     1.1.2
 * Author:      WPExplorer
 * Author URI:  https://www.wpexplorer.com/
 * License:     Custom license
 * License URI: http://themeforest.net/licenses/terms/regular
 * Text Domain: total-theme-core
 * Domain Path: /languages
 *
 * @author  WPExplorer
 * @package TotalThemeCore
 * @version 1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Total_Theme_Core Class.
 *
 * @since 1.0
 */
if ( ! class_exists( 'Total_Theme_Core' ) ) {

	final class Total_Theme_Core {

		/**
		 * Total_Theme_Core constructor.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function __construct() {

			// Define plugin constants.
			define( 'TTC_VERSION', '1.1.2' );
			define( 'TTC_MAIN_FILE_PATH', __FILE__ );
			define( 'TTC_PLUGIN_DIR_PATH', plugin_dir_path( TTC_MAIN_FILE_PATH ) );
			define( 'TTC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

			// Add text domain.
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			// Plugin helper functions.
			require_once TTC_PLUGIN_DIR_PATH . 'inc/helper-functions.php';

			// Flush Rewrites when de-activating the plugin.
			register_deactivation_hook( TTC_MAIN_FILE_PATH, 'flush_rewrite_rules' );

			// Do stuff when we activate the plugin for the first time.
			register_activation_hook( TTC_MAIN_FILE_PATH, array( $this, 'on_activation' ) );

			// Load all plugin features.
			add_action( 'after_setup_theme', array( $this, 'load' ) );

		}

		/**
		 * Flush Re-write rules.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function on_activation() {

			if ( get_theme_mod( 'portfolio_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Portfolio.php';
				$class = new TotalThemeCore\Portfolio;
				$class->on_activation();
			}

			if ( get_theme_mod( 'staff_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Staff.php';
				$class = new TotalThemeCore\Staff;
				$class->on_activation();
			}

			if ( get_theme_mod( 'testimonials_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Testimonials.php';
				$class = new TotalThemeCore\Testimonials;
				$class->on_activation();
			}

			flush_rewrite_rules();

		}

		/**
		 * Start things up.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function load() {

			// Don't load on older versions of Total to prevent issues with customers potentially downgrading
			if ( defined( 'WPEX_THEME_VERSION' ) && version_compare( '4.9', WPEX_THEME_VERSION, '>' ) ) {
				return;
			}

			// Custom shortcodes
			require_once TTC_PLUGIN_DIR_PATH . 'inc/shortcodes.php';

			// Demo importer
			if ( get_theme_mod( 'demo_importer_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/demo-importer/demo-importer.php';
			}

			// Widget Areas
			if ( get_theme_mod( 'widget_areas_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/widgets/WidgetAreas.php';
			}

			// WPBakery functions
			if ( get_theme_mod( 'extend_visual_composer', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/wpbakery/WPBakeryShortcodes.php';
			}

			// Meta classes
			$this->meta();

			// Register Custom Post Types
			$this->post_types();

			// Addons
			$this->addons();

			// Custom Widgets
			if ( get_theme_mod( 'custom_widgets_enable', true ) ) {
				add_action( 'widgets_init', array( $this, 'widgets' ) );
				add_action( 'admin_print_scripts-widgets.php', array( $this, 'widget_scripts' ) );
			}

		}

		/**
		 * Load Text Domain.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'total-theme-core', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Meta classes.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function meta() {

			if ( is_admin() ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/meta/MetaBoxes.php';
				require_once TTC_PLUGIN_DIR_PATH . 'inc/meta/TermMeta.php';
			}

			if ( get_theme_mod( 'gallery_metabox_enable', true ) && is_admin() ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/meta/GalleryMetabox.php';
			}

			if ( get_theme_mod( 'term_thumbnails_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/meta/TermThumbnails.php';
			}

			if ( get_theme_mod( 'category_settings_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/meta/CategorySettings.php';
			}

		}

		/**
		 * Custom Post Types.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function post_types() {

			if ( get_theme_mod( 'portfolio_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Portfolio.php';
			}

			if ( get_theme_mod( 'staff_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Staff.php';
			}

			if ( get_theme_mod( 'testimonials_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/post-types/Testimonials.php';
			}

		}

		/**
		 * Load Addons.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function addons() {

			if ( get_theme_mod( 'post_series_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/addons/PostSeries.php';
			}

			if ( get_theme_mod( 'custom_css_enable', true ) ) {
				require_once TTC_PLUGIN_DIR_PATH . 'inc/addons/CustomCSSPanel.php';
			}

		}

		/**
		 * Custom Widgets.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function widgets() {

			// Include Widget Builder Class
			require_once TTC_PLUGIN_DIR_PATH . 'inc/widgets/WidgetBuilder.php';

			// Define widgets
			$widgets = array(
				'about'               => 'AboutWidget',
				'advertisement'       => 'Advertisement',
				'newsletter'          => 'NewsletterWidget',
				'simple-newsletter'   => 'SimpleNewsletterWidget',
				'info'                => 'BusinessInfoWidget',
				'social-fontawesome'  => 'SocialProfilesWidget',
				'social',
				'simple-menu'         => 'SimpleMenuWidget',
				'modern-menu'         => 'ModernMenuWidget',
				'facebook-page'       => 'FacebookWidget',
				'google-map'          => 'GoogleMapWidget',
				'flickr'              => 'FlickrWidget',
				'video'               => 'VideoWidget',
				'posts-thumbnails'    => 'PostsThumbnailsWidget',
				'posts-grid'          => 'PostsGridWidget',
				'posts-icons'         => 'PostsWithFormatIcons',
				'instagram-grid'      => 'InstagramGridWidget',
				'users-grid'          => 'UsersGridWidget',
				'comments-avatar'     => 'CommentsWidget',
			);

			// Add templatera widget
			if ( function_exists( 'templatera_init' ) ) {
				$widgets['templatera'] = 'TemplateraWidget';
			}

			// bbPress Widgets
			if ( class_exists( 'bbPress' ) ) {
				$widgets['bbpress-forum-info'] = 'bbPressForumInfo';
				$widgets['bbpress-topic-info'] = 'bbPressTopicInfoWidget';
			}

			// Apply filters and return widgets array
			$widgets = apply_filters( 'wpex_custom_widgets', $widgets );

			// Loop through array and register the custom widgets
			if ( $widgets && is_array( $widgets ) ) {
				foreach ( $widgets as $widget ) {
					$file = TTC_PLUGIN_DIR_PATH . 'inc/widgets/' . $widget . '.php';
					if ( file_exists ( $file ) ) {
						require_once $file;
					}
				}
			}

		}

		/**
		 * Custom Widgets scripts
		 *
		 * @since  1.0
		 * @access public
		 */
		public function widget_scripts() {

			wp_enqueue_style(
				'wpex-custom-widgets-admin',
				TTC_PLUGIN_DIR_URL . 'assets/css/custom-widgets-admin.css',
				false,
				'1.0'
			);

			wp_enqueue_script(
				'wpex-custom-widgets-admin',
				TTC_PLUGIN_DIR_URL . 'assets/js/custom-widgets-admin.min.js',
				array( 'jquery' ),
				'1.0',
				true
			);

			wp_localize_script( 'wpex-custom-widgets-admin', 'wpexCustomWidgets', array(
				'confirm' => esc_html__( 'Do you really want to delete this item?', 'total-theme-core' ),
			) );

		}

	}

	new Total_Theme_Core;

}