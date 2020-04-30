<?php
/**
 * Main Customizer functions
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Total Customizer class
 *
 * @since 3.0.0
 */
if ( ! class_exists( 'WPEX_Customizer' ) ) {

	class WPEX_Customizer {
		public  $sections              = array();
		protected $enable_postMessage  = true;    // Disable for testing purposes
		protected $inline_css_settings = array();
		protected $control_visibility  = array();

		/**
		 * Start things up
		 *
		 * @since 3.0.0
		 */
		public function __construct() {

			// Define directory for Customizer functions
			define( 'WPEX_CUSTOMIZER_DIR', WPEX_FRAMEWORK_DIR . 'customizer/' );

			// Include Customizer manager if enabled
			if ( wpex_get_mod( 'customizer_panel_enable', true ) && is_admin() ) {
				require_once WPEX_CUSTOMIZER_DIR . 'customizer-manager.php';
			}

			if ( ! is_admin() || is_customize_preview() ) {
				add_action( 'wp_loaded', array( $this, 'add_to_customizer' ), 1 );
			}

		}

		/**
		 * Define panels
		 *
		 * @version 4.5
		 */
		public function panels() {
			return apply_filters( 'wpex_customizer_panels', array(
				'general' => array(
					'title' => esc_html__( 'General Theme Options', 'total' ),
				),
				'layout' => array(
					'title' => esc_html__( 'Layout', 'total' ),
				),
				'typography' => array(
					'title' => esc_html__( 'Typography', 'total' ),
				),
				'togglebar' => array(
					'title' => esc_html__( 'Toggle Bar', 'total' ),
					'is_section' => true,
				),
				'topbar' => array(
					'title' => esc_html__( 'Top Bar', 'total' ),
				),
				'header' => array(
					'title' => esc_html__( 'Header', 'total' ),
				),
				'sidebar' => array(
					'title' => esc_html__( 'Sidebar', 'total' ),
					'is_section' => true,
				),
				'blog' => array(
					'title' => esc_html__( 'Blog', 'total' ),
				),
				'portfolio' => array(
					'title' => wpex_get_portfolio_name(),
					'condition' => 'wpex_is_total_portfolio_enabled',
				),
				'staff' => array(
					'title' => wpex_get_staff_name(),
					'condition' => 'wpex_is_total_staff_enabled',
				),
				'testimonials' => array(
					'title' => wpex_get_testimonials_name(),
					'condition' => 'wpex_is_total_testimonials_enabled',
				),
				'callout' => array(
					'title' => esc_html__( 'Callout', 'total' ),
				),
				'footer_widgets' => array(
					'title' => esc_html__( 'Footer Widgets', 'total' ),
				),
				'footer_bottom' => array(
					'title' => esc_html__( 'Footer Bottom', 'total' ),
					'is_section' => true,
				),
			) );
		}

		/**
		 * Returns array of enabled panels
		 *
		 * @since 4.9.5
		 * @todo make this better, instead of saving enabled panels we should save array of disabled panels.
		 */
		public function enabled_panels() {
			return get_option( 'wpex_customizer_panels', $this->panels() );
		}

		/**
		 * Check if customizer section is enabled
		 *
		 * @since 4.9.5
		 */
		public function is_section_enabled( $section, $section_id ) {
			$section = ! empty( $section[ 'panel' ] ) ? $section[ 'panel' ] : $section_id;
			if ( $section ) {
				$enabled_panels = $this->enabled_panels();
				$section_panel = str_replace( 'wpex_', '', $section );
				if ( empty( $enabled_panels[ $section_panel ] ) ) {
					return false;
				}
			}
			return true; // all enabled by default
		}

		/**
		 * Initialize customizer settings
		 *
		 * @since 3.6.0
		 */
		public function add_to_customizer() {

			// Add sections to customizer and store array in $this->sections variable
			if ( ! $this->sections ) {
				$this->add_sections();
			}

			// Add scripts for custom controls
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );

			// Inline CSS for customizer icons
			add_action( 'customize_controls_print_styles', array( $this, 'customize_controls_print_styles' ) );

			// Register custom controls
			add_action( 'customize_register', array( $this, 'register_control_types' ) );

			// Customizer conditionals
			add_action( 'customize_register', array( $this, 'active_callback_functions' ) );

			// Remove core panels and sections
			add_action( 'customize_register', array( $this, 'remove_core_sections' ), 11 );

			// Add theme customizer sections and panels
			add_action( 'customize_register', array( $this, 'add_customizer_panels_sections' ), 40 );

			// Load JS file for customizer
			add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );

			// CSS output
			if ( is_customize_preview() && $this->enable_postMessage ) {
				add_action( 'wp_head', array( $this, 'live_preview_styles' ), 99999 );
			} else {
				add_action( 'wpex_head_css', array( $this, 'head_css' ), 999 );
			}

		}

		/**
		 * Adds custom controls
		 *
		 * @since 3.0.0
		 */
		public function customize_controls_enqueue_scripts() {

			// Chosen JS
			wp_enqueue_script(
				'wpex-chosen-js',
				wpex_asset_url( 'lib/chosen/chosen.jquery.min.js' ),
				array( 'customize-controls', 'jquery' ),
				'1.4.1'
			);

			// Controls JS
			wp_enqueue_script(
				'wpex-customizer-controls',
				wpex_asset_url( 'js/dynamic/customizer/wpex-controls.min.js' ),
				array( 'customize-controls', 'wpex-chosen-js', 'jquery' ),
				WPEX_THEME_VERSION
			);

			wp_localize_script(
				'wpex-customizer-controls',
				'wpexControlVisibility',
				$this->loop_through_array( 'control_visibility' )
			);

			// Load Chosen CSS
			wp_enqueue_style(
				'wpex-chosen-css',
				wpex_asset_url( 'lib/chosen/chosen.min.css' ),
				false,
				'1.4.1'
			);

			// Load CSS to style custom customizer controls
			wp_enqueue_style(
				'wpex-customizer-css',
				wpex_asset_url( 'css/wpex-customizer.css' ),
				false,
				WPEX_THEME_VERSION
			);

		}

		/**
		 * Adds custom controls
		 *
		 * @since 3.6.0
		 */
		public function register_control_types( $wp_customize ) {

			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/hr.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/heading.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/bg-patterns.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/textarea.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/dropdown-pages.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/dropdown-templates.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/textarea.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/multi-check.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/sorter.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/font-family.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/fa-icon-select.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/responsive-field.php';
			require_once WPEX_FRAMEWORK_DIR . 'customizer/controls/columns.php';

			// Registered types that are eligible to be rendered via JS and created dynamically.
			// @link https://developer.wordpress.org/reference/classes/wp_customize_manager/register_control_type/
			$wp_customize->register_control_type( 'WPEX_Customizer_Hr_Control' );
			$wp_customize->register_control_type( 'WPEX_Customizer_Heading_Control' );

		}

		/**
		 * Adds custom controls
		 *
		 * @since 3.6.0
		 */
		public function active_callback_functions() {
			require_once WPEX_CUSTOMIZER_DIR . 'customizer-active-callback-functions.php';
		}

		/**
		 * Adds CSS for customizer custom controls
		 *
		 * MUST Be added inline to get correct post type Icons
		 *
		 * @since 3.0.0
		 */
		public function customize_controls_print_styles() {

			// Get post type icons
			$portfolio_icon    = wpex_dashicon_css_content( wpex_get_portfolio_menu_icon() );
			$staff_icon        = wpex_dashicon_css_content( wpex_get_staff_menu_icon() );
			$testimonials_icon = wpex_dashicon_css_content( wpex_get_testimonials_menu_icon() ); ?>

			 <style id="wpex-customizer-controls-css">

				/* Icons */
				#accordion-panel-wpex_general > h3:before,
				#accordion-panel-wpex_typography > h3:before,
				#accordion-panel-wpex_layout > h3:before,
				#accordion-section-wpex_togglebar > h3:before,
				#accordion-panel-wpex_topbar > h3:before,
				#accordion-panel-wpex_header > h3:before,
				#accordion-section-wpex_sidebar > h3:before,
				#accordion-panel-wpex_blog > h3:before,
				#accordion-panel-wpex_portfolio > h3:before,
				#accordion-panel-wpex_staff > h3:before,
				#accordion-panel-wpex_testimonials > h3:before,
				#accordion-panel-wpex_callout > h3:before,
				#accordion-panel-wpex_footer_widgets > h3:before,
				#accordion-section-wpex_footer_bottom > h3:before,
				#accordion-section-wpex_visual_composer > h3:before,
				#accordion-panel-wpex_woocommerce > h3:before,
				#accordion-section-wpex_tribe_events > h3:before,
				#accordion-section-wpex_bbpress > h3:before,
				#accordion-panel-wpex_learndash > h3:before { display: inline-block; width: 20px; height: 20px; font-size: 20px; line-height: 1; font-family: dashicons; text-decoration: inherit; font-weight: 400; font-style: normal; vertical-align: top; text-align: center; -webkit-transition: color .1s ease-in 0; transition: color .1s ease-in 0; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; color: #298cba; margin-right: 10px; font-family: "dashicons"; content: "\f108"; }

				<?php if ( is_rtl() ) { ?>
					.rtl #accordion-panel-wpex_general > h3:before,
					.rtl #accordion-panel-wpex_typography > h3:before,
					.rtl #accordion-panel-wpex_layout > h3:before,
					.rtl #accordion-section-wpex_togglebar > h3:before,
					.rtl #accordion-panel-wpex_topbar > h3:before,
					.rtl #accordion-panel-wpex_header > h3:before,
					.rtl #accordion-section-wpex_sidebar > h3:before,
					.rtl #accordion-panel-wpex_blog > h3:before,
					.rtl #accordion-panel-wpex_portfolio > h3:before,
					.rtl #accordion-panel-wpex_staff > h3:before,
					.rtl #accordion-section-wpex_testimonials > h3:before,
					.rtl #accordion-panel-wpex_callout > h3:before,
					.rtl #accordion-panel-wpex_footer_widgets > h3:before,
					.rtl #accordion-section-wpex_footer_bottom > h3:before,
					.rtl #accordion-section-wpex_visual_composer > h3:before,
					.rtl #accordion-panel-wpex_woocommerce > h3:before,
					.rtl #accordion-section-wpex_tribe_events > h3:before,
					.rtl #accordion-section-wpex_bbpress > h3:before,
					.rtl #accordion-panel-wpex_learndash > h3:before { margin-right: 0; margin-left: 10px; }
				<?php } ?>

				#accordion-panel-wpex_typography > h3:before { content: "\f215" }
				#accordion-panel-wpex_layout > h3:before { content: "\f535" }
				#accordion-section-wpex_togglebar > h3:before { content: "\f132" }
				#accordion-panel-wpex_topbar > h3:before { content: "\f157" }
				#accordion-panel-wpex_header > h3:before { content: "\f175" }
				#accordion-section-wpex_sidebar > h3:before { content: "\f135" }
				#accordion-panel-wpex_blog > h3:before { content: "\f109" }
				#accordion-panel-wpex_portfolio > h3:before { content: "\<?php echo esc_attr( $portfolio_icon ); ?>" }
				#accordion-panel-wpex_staff > h3:before { content: "\<?php echo esc_attr( $staff_icon ); ?>" }
				#accordion-panel-wpex_testimonials > h3:before { content: "\<?php echo esc_attr( $testimonials_icon ); ?>" }
				#accordion-panel-wpex_callout > h3:before { content: "\f488" }
				#accordion-panel-wpex_footer_widgets > h3:before { content: "\f209" }
				#accordion-section-wpex_footer_bottom > h3:before { content: "\f209"; }
				#accordion-section-wpex_visual_composer > h3:before { content: "\f540" }
				#accordion-panel-wpex_woocommerce > h3:before { content: "\f174" }
				#accordion-section-wpex_tribe_events > h3:before { content: "\f145" }
				#accordion-section-wpex_bbpress > h3:before { content: "\f449" }
				#accordion-panel-wpex_learndash > h3:before { content: "\f118" }

			 </style>

		<?php }

		/**
		 * Removes core modules
		 *
		 * @since 3.0.0
		 */
		public function remove_core_sections( $wp_customize ) {

			// Remove core sections
			$wp_customize->remove_section( 'colors' );
			$wp_customize->remove_section( 'background_image' );

			// Remove core controls
			$wp_customize->remove_control( 'blogdescription' );
			$wp_customize->remove_control( 'header_textcolor' );
			$wp_customize->remove_control( 'background_color' );
			$wp_customize->remove_control( 'background_image' );

			// Remove default settings
			$wp_customize->remove_setting( 'background_color' );
			$wp_customize->remove_setting( 'background_image' );

		}

		/**
		 * Get all sections
		 *
		 * @since 3.0.0
		 */
		public function add_sections() {

			// Get panels
			$panels = $this->panels();

			// Return if there aren't any panels
			if ( ! $panels ) {
				return;
			}

			// Useful variables to pass along to customizer settings
			$page_header_styles = wpex_get_page_header_styles();
			$post_layouts       = wpex_get_post_layouts();
			$choices_visibility = wpex_visibility();
			$bg_styles          = wpex_get_bg_img_styles();
			$post_types         = wpex_get_post_types( 'customizer_settings', array( 'attachment' ) );
			$overlay_styles     = wpex_overlay_styles_array();
			$button_styles      = wpex_button_styles();
			$button_colors      = wpex_button_colors();

			// Save re-usable descriptions
			$margin_desc          = esc_html__( 'Please use the following format: top right bottom left.', 'total' );
			$padding_desc         = esc_html__( 'Format: top right bottom left.', 'total' ) .'<br />'. esc_html__( 'Example:', 'total' ) .' 5px 10px 5px 10px';
			$pixel_desc           = esc_html__( 'Enter a value in pixels. Example: 10px.', 'total' );
			$border_desc          = esc_html__( 'Enter a value in pixels. Example: 10px. Set the value to 0px to disable.', 'total' );
			$leave_blank_desc     = esc_html__( 'Leave blank to disable.', 'total' );
			$post_id_content_desc = esc_html__( 'If you enter the ID number of a page or Templatera template it will display it\'s content instead.', 'total' );
			$template_desc        = esc_html__( 'Create a dynamic template using Templatera to override the default content layout.', 'total' );

			if ( get_theme_mod( 'customizer_panel_enable', true ) ) {
				$disable_panel_desc = sprintf( esc_html__( 'If you disable this section we recommend you go to %sTheme Panel > Customizer Manager%s and disable the Customizer panel as well.', 'total' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wpex-panel-customizer' ) ) . '" target="_blank">', '</a>' );
			} else {
				$disable_panel_desc = '';
			}

			// Social styles
			$social_styles = array(
				'' => esc_html__( 'Minimal', 'total' ),
				'colored-icons' => esc_html__( 'Colored Image Icons (Legacy)', 'total' ),
			);
			$social_styles = array_merge( wpex_social_button_styles(), $social_styles );
			unset( $social_styles[''] );

			// Loop through panels
			foreach( $panels as $id => $val ) {

				// These have their own sections outside the main class scope
				if ( 'typography' == $id ) {
					continue;
				}

				// Continue if condition isn't me
				if ( isset( $val['condition'] ) && ! call_user_func( $val['condition'] ) ) {
					continue;
				}

				// Section file location
				if ( ! empty( $val['settings'] ) ) {
					$file = $val['settings'];
				} else {
					$file = WPEX_CUSTOMIZER_DIR . 'settings/' . $id . '.php';
				}

				// Include file and update sections var
				if ( file_exists( $file ) ) {
					require_once $file;
				}

			}

			// Set keys equal to ID for child theme editing => DON'T REMOVE !!
			// @todo - maybe we should just do this manually?
			$parsed_sections = array();
			if ( $this->sections && is_array( $this->sections ) ) {
				foreach ( $this->sections as $key => $val ) {
					$new_settings = array();
					foreach( $val['settings'] as $skey => $sval ) {
						$new_settings[$sval['id']] = $sval;
					}
					$val['settings'] = $new_settings;
					$parsed_sections[$key] = $val;
				}
			}

			// Apply filters to sections
			$this->sections = apply_filters( 'wpex_customizer_sections', $parsed_sections );

			// Store inline_css settings to speed things up.
			$this->inline_css_settings = $this->get_inline_css_settings();

			//print_r( $this->sections );

		}

		/**
		 * Registers new controls
		 * Removes default customizer sections and settings
		 * Adds new customizer sections, settings & controls
		 *
		 * @since 3.0.0
		 */
		public function add_customizer_panels_sections( $wp_customize ) {

			// Get all panels
			$all_panels = $this->panels();

			// Get enabled panels
			$enabled_panels = $this->enabled_panels();

			// No panels are enabled so lets bounce
			if ( ! $enabled_panels ) {
				return;
			}

			$priority = 140;

			foreach( $all_panels as $id => $val ) {

				$priority++;

				// Continue if panel is disabled or it's the typography panel
				if ( ! isset( $enabled_panels[$id] ) || 'typography' == $id ) {
					continue;
				}

				// Continue if condition isn't met
				if ( isset( $val['condition'] ) && ! $val['condition'] ) {
					continue;
				}

				// Get title and check if panel or section
				$title      = isset( $val['title'] ) ? $val['title'] : $val;
				$is_section = isset( $val['is_section'] ) ? true : false;

				// Add section
				if ( $is_section ) {

					$wp_customize->add_section( 'wpex_' . $id, array(
						'priority' => $priority,
						'title'    => $title,
					) );

				}

				// Add Panel
				else {

					$wp_customize->add_panel( 'wpex_'. $id, array(
						'priority' => $priority,
						'title'    => $title,
					) );

				}

			}

			// Create the new customizer sections
			if ( ! empty( $this->sections ) ) {
				$this->create_sections( $wp_customize, $this->sections );
			}

		}

		/**
		 * Creates the Sections and controls for the customizer
		 *
		 * @since 3.0.0
		 */
		public function create_sections( $wp_customize ) {

			// Get array of enabled pabels
			$enabled_panels = $this->enabled_panels();

			// Loop through sections and add create the customizer sections, settings & controls
			foreach ( $this->sections as $section_id => $section ) {

				// Check if section panel is enabled
				if ( ! $this->is_section_enabled( $section, $section_id ) ) {
					continue;
				}

				// Get section settings
				$settings = ! empty( $section['settings'] ) ? $section['settings'] : null;

				// Return if no settings are found
				if ( ! $settings ) {
					return;
				}

				// Get section description
				$description = isset( $section['desc'] ) ? $section['desc'] : '';

				// Add customizer section
				if ( isset( $section['panel'] ) ) {
					$wp_customize->add_section( $section_id, array(
						'title'       => $section['title'],
						'panel'       => $section['panel'],
						'description' => $description,
					) );
				}

				// Add settings+controls
				foreach ( $settings as $setting ) {

					// Get vals
					$id                = $setting['id']; // Required no need to check
					$transport         = isset( $setting['transport'] ) ? $setting['transport'] : 'refresh';
					$default           = isset( $setting['default'] ) ? $setting['default'] : '';
					$control_type      = isset( $setting['control']['type'] ) ? $setting['control']['type'] : 'text';
					$sanitize_callback = isset( $setting['sanitize_callback'] ) ?  $setting['sanitize_callback'] : '';

					// Check partial refresh
					if ( 'partialRefresh' == $transport ) {
						$transport = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';
					}

					// Set transport to refresh if postMessage is disabled
					if ( ! $this->enable_postMessage || 'wpex_heading' == $control_type ) {
						$transport = 'refresh';
					}

					// Add sanitize callbacks if not set yet
					if ( ! $sanitize_callback ) {

						if ( 'checkbox' == $control_type ) {
							$sanitize_callback = 'wpex_sanitize_checkbox';
						} elseif ( 'select' == $control_type ) {
							$sanitize_callback = 'wpex_sanitize_customizer_select';
						} elseif ( 'color' == $control_type ) {
							$sanitize_callback = 'sanitize_hex_color';
						} elseif( 'wpex-columns' == $control_type ) {
							$sanitize_callback = 'wpex_sanitize_customizer_columns';
						}

					}

					// Add values to control
					$setting['control']['settings'] = $setting['id'];
					$setting['control']['section'] = $section_id;

					// Add description
					if ( isset( $setting['control']['desc'] ) ) {
						$setting['control']['description'] = $setting['control']['desc'];
					}

					// Control object
					if ( isset( $setting['control']['object'] ) ) {
						$control_obj = $setting['control']['object'];
					} elseif ( 'image' == $control_type ) {
						$control_obj = 'WP_Customize_Image_Control';
						$sanitize_callback = 'esc_url';
					} elseif ( 'media' == $control_type ) {
						$control_obj = 'WP_Customize_Media_Control';
						$sanitize_callback = 'absint';
					} elseif ( 'color' == $control_type ) {
						$control_obj = 'WP_Customize_Color_Control';
					} elseif ( 'wpex-heading' == $control_type ) {
						$control_obj = 'WPEX_Customizer_Heading_Control';
					} elseif ( 'wpex-sortable' == $control_type ) {
						$control_obj = 'WPEX_Customize_Control_Sorter';
					} elseif ( 'wpex-fa-icon-select' == $control_type ) {
						$control_obj = 'WPEX_Font_Awesome_Icon_Select';
					} elseif ( 'wpex-dropdown-pages' == $control_type ) {
						$control_obj = 'WPEX_Customizer_Dropdown_Pages';
						$sanitize_callback = 'absint';
					} elseif ( 'wpex-dropdown-templates' == $control_type ) {
						$control_obj = 'WPEX_Customizer_Dropdown_Templates';
						$sanitize_callback = 'absint';
					} elseif ( 'wpex-textarea' == $control_type ) {
						$control_obj = 'WPEX_Customizer_Textarea_Control';
					} elseif ( 'wpex-bg-patterns' == $control_type ) {
						$control_obj = 'WPEX_Customizer_BG_Patterns_Control';
						$sanitize_callback = 'esc_html';
					} elseif ( 'wpex-responsive-field' == $control_type ) {
						$control_obj = 'WPEX_Responsive_Field_Custom_Control';
					} elseif ( 'wpex-columns' == $control_type ) {
						$control_obj = 'WPEX_Columns_Custom_Control';
					} elseif ( 'multiple-select' == $control_type ) {
						$control_obj = 'WPEX_Customize_Multicheck_Control';
						$sanitize_callback = 'esc_html';
					} else {
						$control_obj = 'WP_Customize_Control';
					}

					// If $id defined add setting and control
					if ( $id ) {

						// Add setting
						$wp_customize->add_setting( $id, array(
							'type'              => 'theme_mod',
							'transport'         => $transport,
							'default'           => $default,
							'sanitize_callback' => $sanitize_callback,
						) );

						// Add control
						$wp_customize->add_control( new $control_obj ( $wp_customize, $id, $setting['control'] ) );

					}
				}
			}

			// Load partial refresh functions
			require_once WPEX_CUSTOMIZER_DIR . 'customizer-partial-refresh.php';

		} // END create_sections()

		/**
		 * Loads js file for customizer preview
		 *
		 * @since 3.3.0
		 */
		public function customize_preview_init() {

			if ( ! $this->enable_postMessage ) {
				return;
			}

			wp_enqueue_script( 'wpex-customizer-preview',
				wpex_asset_url( 'js/dynamic/customizer/wpex-preview.min.js' ),
				array( 'customize-preview' ),
				WPEX_THEME_VERSION,
				true
			);

			wp_localize_script(
				'wpex-customizer-preview',
				'wpexCustomizer',
				array(
					'stylingOptions' => $this->inline_css_settings,
				)
			);

			if ( class_exists( 'TotalTheme\AccentColors' ) ) {

				wp_localize_script(
					'wpex-customizer-preview',
					'wpexAccentElements',
					TotalTheme\AccentColors::arrays()
				);

			}

		}

		/**
		 * Loops through all settings and returns things
		 *
		 * @since 4.1
		 */
		public function loop_through_array( $return = 'css' ) {

			if ( 'css' == $return && $this->inline_css_settings ) {
				return $this->inline_css_settings;
			}

			if ( 'control_visibility' == $return && $this->control_visibility ) {
				return $this->control_visibility;
			}

			$css_settings       = array();
			$control_visibility = array();
			$settings           = wp_list_pluck( $this->sections, 'settings' );

			if ( ! $settings || ! is_array( $settings ) ) {
				return;
			}

			foreach ( $settings as $settings_array ) {

				foreach ( $settings_array as $setting ) {

					// CSS
					if ( isset( $setting['inline_css'] ) ) {
						$css_settings[$setting['id']] = $setting['inline_css'];
						if ( isset( $setting['default'] ) ) {
							$css_settings[$setting['id']]['default'] = $setting['default'];
						}
					}

					// Control visibility
					if ( isset( $setting['control_display'] ) ) {
						if ( isset( $setting['control_display']['value'] )
							&& is_array( $setting['control_display']['value'] )
						) {
							$setting['control_display']['multiCheck'] = true;
						}
						$control_visibility[$setting['id']] = $setting['control_display'];
					}

				}

			}

			$this->inline_css_settings = $css_settings;
			$this->control_visibility  = $control_visibility;

			if ( 'css' == $return ) {
				return $this->inline_css_settings;
			} elseif ( 'control_visibility' == $return ) {
				return $this->control_visibility;
			}

		}

		/**
		 * Loops through all settings and returns array of online inline_css settings
		 *
		 * @since 4.1
		 */
		public function get_inline_css_settings() {
			return $this->loop_through_array( 'css' );
		}

		/**
		 * Generates inline CSS for styling options
		 *
		 * @since 1.0.0
		 */
		public function loop_through_inline_css( $return = 'css' ) {

			// Define vars
			$elements_to_alter = array();
			$preview_styles    = array();
			$add_css           = '';

			// Get customizer settings
			$settings = $this->get_inline_css_settings();

			// Return if there aren't any settings
			if ( ! $settings ) {
				return;
			}

			foreach ( $settings as $key => $inline_css ) {

				// Store setting ID
				$setting_id = $key;

				// Conditional CSS check to add CSS or not
				if ( isset( $inline_css['condition'] ) && ! call_user_func( $inline_css['condition'] ) ) {
					continue;
				}

				// Get default
				$default = isset ( $inline_css['default'] ) ? $inline_css['default'] : false;

				// Get current value
				$theme_mod  = wpex_get_mod( $setting_id, $default );

				// If mod is equal to default and part of the mods let's remove it
				// This is a good place since we are looping through all settings anyway
				if ( apply_filters( 'wpex_remove_default_mods', false ) ) {
					$get_all_mods = wpex_get_mods();
					if ( $theme_mod == $default && $get_all_mods && isset( $get_all_mods[$setting_id] ) ) {
						remove_theme_mod( $setting_id );
					}
				}

				// Get css params
				$sanitize    = isset( $inline_css['sanitize'] ) ? $inline_css['sanitize'] : false;
				$alter       = isset( $inline_css['alter'] ) ? $inline_css['alter'] : '';
				$important   = isset( $inline_css['important'] ) ? '!important' : false;
				$media_query = isset( $inline_css['media_query'] ) ? $inline_css['media_query'] : false;
				$target      = isset( $inline_css['target'] ) ? $inline_css['target'] : '';

				// If alter is set to "display" and type equals 'checkbox' then set correct value
				if ( 'display' == $alter && 'checkbox' == $sanitize ) {
					$theme_mod = $theme_mod ? '' : 'none';
				}

				// These are required for outputting custom CSS
				if ( ! $theme_mod ) {
					continue;
				}

				// Add to preview_styles array
				if ( 'preview_styles' == $return ) {
					$preview_styles[$setting_id] = '';
				}

				// Target and alter vars are required, if they are empty continue onto the next setting
				if ( ! $target || ! $alter ) {
					continue;
				}

				// Sanitize output
				if ( $sanitize ) {
					$theme_mod = wpex_sanitize_data( $theme_mod, $sanitize );
				} else {
					$theme_mod = $theme_mod;
				}

				// Set to array if not
				$target = is_array( $target ) ? $target : array( $target );

				// Loop through items
				foreach( $target as $element ) {

					// Add to elements list if not already for CSS output only
					if ( 'css' == $return && ! $media_query && ! isset( $elements_to_alter[$element] ) ) {
						$elements_to_alter[$element] = array( 'css' => '' );
					}

					// Return CSS or js
					if ( is_array( $alter ) ) {

						// Loop through elements to alter
						foreach( $alter as $alter_val ) {

							// Inline CSS
							if ( 'css' == $return ) {

								// If it has a media query it's its own thing
								if ( $media_query ) {
									$add_css .= '@media only screen and '. $media_query . '{'.$element .'{ '. $alter_val .':'. $theme_mod . $important .'; }}';
								} else {
									$elements_to_alter[$element]['css'] .= $alter_val .':'. $theme_mod . $important .';';
								}
							}

							// Live preview styles
							elseif ( 'preview_styles' == $return ) {

								// If it has a media query it's its own thing
								if ( $media_query ) {
									$preview_styles[$setting_id] .= '@media only screen and '. $media_query . '{'.$element .'{ '. $alter_val .':'. $theme_mod . $important .'; }}';
								}

								// Not media query
								else {
									$preview_styles[$setting_id] .= $element .'{ '. $alter_val .':'. $theme_mod . $important .'; }';
								}
							}
						}
					}

					// Single element to alter
					else {

						// Background image tweak
						if ( 'background-image' == $alter ) {
							$theme_mod = 'url('. esc_url( $theme_mod ) .')';
						}

						// Inline CSS
						if ( 'css' == $return ) {

							// If it has a media query it's its own thing
							if ( $media_query ) {
								$add_css .= '@media only screen and '. $media_query . '{'.$element .'{ '. $alter .':'. $theme_mod . $important .'; }}';
							}

							// Not media query
							else {
								$elements_to_alter[$element]['css'] .= $alter .':'. $theme_mod . $important .';';
							}

						}

						// Live preview styles
						elseif ( 'preview_styles' == $return ) {

							// If it has a media query it's its own thing
							if ( $media_query ) {
								$preview_styles[$setting_id] .= '@media only screen and '. $media_query . '{'.$element .'{ '. $alter .':'. $theme_mod . $important .'; }}';
							}

							// Not media query
							else {
								$preview_styles[$setting_id] .= $element .'{ '. $alter .':'. $theme_mod . $important .'; }';
							}

						}

					}

				}

			} // End settings loop

			// Loop through elements for CSS only
			if ( 'css' == $return && $elements_to_alter ) {
				foreach( $elements_to_alter as $element => $array ) {
					if ( isset( $array['css'] ) ) {
						$add_css .= $element.'{'.$array['css'].'}';
					}
				}
			}

			// Return inline css
			if ( 'css' == $return ) {
				return $add_css;
			}

			// Return preview styles
			if ( 'preview_styles' == $return ) {
				return $preview_styles;
			}

		}

		/**
		 * Returns correct CSS to output to wp_head
		 *
		 * @since 1.0.0
		 */
		public function head_css( $output ) {
			$inline_css = $this->loop_through_inline_css( 'css' );
			if ( $inline_css ) {
				$output .= '/*CUSTOMIZER STYLING*/'. $inline_css;
			}
			unset( $this->sections );
			return $output;
		}

		/**
		 * Returns correct CSS to output to wp_head
		 *
		 * @since 1.0.0
		 */
		public function live_preview_styles() {
			$live_preview_styles = $this->loop_through_inline_css( 'preview_styles' );
			if ( $live_preview_styles ) {
				foreach ( $live_preview_styles as $key => $val ) {
					if ( ! empty( $val ) ) {
						echo '<style id="wpex-customizer-'. $key .'"> '. $val .'</style>';
					}
				}
			}
		}

	}

}
new WPEX_Customizer();