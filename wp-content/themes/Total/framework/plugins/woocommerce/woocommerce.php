<?php
/**
 * Perform all main WooCommerce configurations for this theme
 *
 * @package Total WordPress Theme
 * @subpackage WooCommerce
 * @version 4.9.9
 *
 * @todo Move certain functions outside of the class into their own functions so they are easier for customers to remove/override?
 *
 */

if ( ! class_exists( 'WPEX_WooCommerce_Config' ) ) {

	class WPEX_WooCommerce_Config {

		/**
		 * Main Class Constructor
		 *
		 * @since 2.0.0
		 */
		public function __construct() {

			// Add theme support
			add_theme_support( 'woocommerce' );

			// Define directory for Woo functions and classes
			define( 'WPEX_WOO_CONFIG_DIR', WPEX_FRAMEWORK_DIR . 'plugins/woocommerce/' );

			// General tweaks/actions
			require_once WPEX_WOO_CONFIG_DIR . 'actions.php';

			// Function overrides
			require_once WPEX_WOO_CONFIG_DIR . 'function-overrides.php';

			// Menu cart
			require_once WPEX_WOO_CONFIG_DIR . 'menu-cart.php';

			// Include custom Classes
			require_once WPEX_WOO_CONFIG_DIR . 'classes/AccentColors.php';
			require_once WPEX_WOO_CONFIG_DIR . 'classes/ProductEntry.php';
			require_once WPEX_WOO_CONFIG_DIR . 'classes/ProductGallery.php';

			if ( ! wpex_get_mod( 'woo_dynamic_image_resizing', false ) ) {
				require_once WPEX_WOO_CONFIG_DIR . 'classes/Thumbnails.php';
			}

			// Add Customizer settings
			add_filter( 'wpex_customizer_panels', array( 'WPEX_WooCommerce_Config', 'customizer_settings' ) );

			// These filters/actions must run on init
			add_action( 'init', array( 'WPEX_WooCommerce_Config', 'init' ) );

			// Set correct post layouts
			add_filter( 'wpex_post_layout_class', array( 'WPEX_WooCommerce_Config', 'layouts' ) );

			// Disable WooCommerce main page title
			add_filter( 'woocommerce_show_page_title', '__return_false' );

			// Alter page header title
			add_filter( 'wpex_title', array( 'WPEX_WooCommerce_Config', 'title_config' ) );

			// Show/hide next/prev on products
			add_filter( 'wpex_has_next_prev', array( 'WPEX_WooCommerce_Config', 'next_prev' ) );

			// Disable category page header image by default
			add_filter( 'wpex_term_page_header_image_enabled', array( 'WPEX_WooCommerce_Config', 'term_page_header_image_enabled' ) );

			// Remove Woo Styles
			if ( apply_filters( 'wpex_custom_woo_stylesheets', true ) ) {
				add_action( 'woocommerce_enqueue_styles', '__return_empty_array', PHP_INT_MAX );
			}

			// Load customs scripts
			add_action( 'wp_enqueue_scripts', array( 'WPEX_WooCommerce_Config', 'add_custom_scripts' ) );

			// Add social share
			if ( 'woocommerce_share' == wpex_get_mod( 'woo_product_social_share_location', 'after_summary' ) ) {
				add_action( 'woocommerce_share', 'wpex_social_share' );
			} else {
				add_action( 'woocommerce_after_single_product_summary', 'wpex_social_share', 11 );
			}

			// Product post
			add_action( 'woocommerce_after_single_product_summary', array( 'WPEX_WooCommerce_Config', 'clear_summary_floats' ), 1 );

			// Alter the sale tag
			add_filter( 'woocommerce_sale_flash', array( 'WPEX_WooCommerce_Config', 'woocommerce_sale_flash' ), 10, 3 );

			// Alter shop posts per page
			add_filter( 'loop_shop_per_page', array( 'WPEX_WooCommerce_Config', 'loop_shop_per_page' ), 20 );

			// Alter shop columns
			add_filter( 'loop_shop_columns', array( 'WPEX_WooCommerce_Config', 'loop_shop_columns' ) );

			// Alter related product args
			add_filter( 'woocommerce_output_related_products_args', array( 'WPEX_WooCommerce_Config', 'related_product_args' ) );

			// Tweak Woo pagination args
			add_filter( 'woocommerce_pagination_args', array( 'WPEX_WooCommerce_Config', 'pagination_args' ) );

			// Alter shop page redirect
			add_filter( 'woocommerce_continue_shopping_redirect', array( 'WPEX_WooCommerce_Config', 'continue_shopping_redirect' ) );

			// Alter product category entry classes
			add_filter( 'product_cat_class', array( 'WPEX_WooCommerce_Config', 'product_cat_class' ) );

			// Alter product tag cloud widget args
			add_filter( 'woocommerce_product_tag_cloud_widget_args', array( 'WPEX_WooCommerce_Config', 'tag_cloud_widget_args' ) );

			// Add new typography settings
			add_filter( 'wpex_typography_settings', array( 'WPEX_WooCommerce_Config', 'typography_settings' ), 60 );

			// Alter the comment form args
			add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'comment_form_args' ) );

			// More reviews
			remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
			add_action( 'woocommerce_review_before_comment_text', 'woocommerce_review_display_rating', 0 );

			// Alter orders per-page on account page
			add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'woocommerce_my_account_my_orders_query' ) );

		} // End __construct

		/*-------------------------------------------------------------------------------*/
		/* -  Start Class Functions
		/*-------------------------------------------------------------------------------*/

		/**
		 * Adds Customizer settings for WooCommerce
		 *
		 * @since 4.0
		 */
		public static function customizer_settings( $panels ) {
			$branding = wpex_get_theme_branding();
			$branding = $branding ? ' (' . $branding . ')' : '';
			$panels['woocommerce'] = array(
				'title'    => esc_html__( 'WooCommerce', 'total' ) . $branding,
				'settings' => WPEX_WOO_CONFIG_DIR . 'customizer.php'
			);
			return $panels;
		}

		/**
		 * Runs on Init.
		 * You can't remove certain actions in the constructor because it's too early.
		 *
		 * @since 2.0.0
		 */
		public static function init() {

			// Remove category descriptions, these are added already by the theme
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

			// Remove coupon from checkout
			//remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

			// Remove single meta
			if ( ! wpex_get_mod( 'woo_product_meta', true ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			}

			// Alter upsells
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			if ( '0' != wpex_get_mod( 'woocommerce_upsells_count', '4' ) ) {
				add_action( 'woocommerce_after_single_product_summary', array( 'WPEX_WooCommerce_Config', 'upsell_display' ), 15 );
			}

			// Remove related products if count is set to 0
			if ( '0' == wpex_get_mod( 'woocommerce_related_count', '4' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			}

			// Alter crossells
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
			if ( '0' != wpex_get_mod( 'woocommerce_cross_sells_count', '4' ) ) {
				add_action( 'woocommerce_cart_collaterals', array( 'WPEX_WooCommerce_Config', 'cross_sell_display' ) );
			}

			// Remove result count if disabled
			if ( ! wpex_get_mod( 'woo_shop_result_count', true ) ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			}

			// Remove orderby if disabled
			if ( ! wpex_get_mod( 'woo_shop_sort', true ) ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}

			// Move tabs
			// Add right after meta which is set to 40
			if ( 'right' == wpex_get_mod( 'woo_product_tabs_position' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 41 );
			}

		}

		/**
		 * Returns correct title for WooCommerce pages.
		 *
		 * @since 2.0.0
		 */
		public static function title_config( $title ) {

			// Shop title
			if ( wpex_is_woo_shop() ) {

				if ( ! empty( $_GET['s'] ) ) {
					return esc_html__( 'Shop results for:', 'total' ) . ' <span>&quot;' . wp_strip_all_tags( $_GET['s'] ) . '&quot;</span>';
				} else {
					$shop_id = wpex_parse_obj_id( wc_get_page_id( 'shop' ), 'page' );
					$title   = $shop_id ? get_the_title( $shop_id ) : '';
					$title   = $title ? $title : $title = esc_html__( 'Shop', 'total' );
				}

			}

			// Product title
			elseif ( is_product() ) {
				$title = wpex_get_translated_theme_mod( 'woo_shop_single_title' );
				$title = $title ? $title : esc_html__( 'Shop', 'total' );
			}

			// Checkout
			elseif ( is_order_received_page() ) {
				$title = esc_html__( 'Order Received', 'total' );
			}

			// Return title
			return $title;

		}

		/**
		 * Tweaks the post layouts for WooCommerce archives and single product posts.
		 *
		 * @since 2.0.0
		 */
		public static function layouts( $class ) {
			if ( wpex_is_woo_shop() ) {
				$class = wpex_get_mod( 'woo_shop_layout', 'full-width' );
			} elseif ( wpex_is_woo_tax() ) {
				$class = wpex_get_mod( 'woo_shop_layout', 'full-width' );
			} elseif ( wpex_is_woo_single() ) {
				$class = wpex_get_mod( 'woo_product_layout', 'full-width' );
			} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
				$class = 'full-width';

			}
			return $class;
		}

		/**
		 * Add Custom scripts
		 *
		 * @since 2.0.0
		 */
		public static function add_custom_scripts() {

			if ( apply_filters( 'wpex_custom_woo_stylesheets', true ) ) {
				wp_enqueue_style( 'wpex-woocommerce', wpex_asset_url( 'css/wpex-woocommerce.css' ), array(), WPEX_THEME_VERSION );
				wp_style_add_data( 'wpex-woocommerce', 'rtl', 'replace' );
			}

			if ( apply_filters( 'wpex_custom_woo_scripts', true ) ) {

				wp_enqueue_script(
					'wpex-wc-functions',
					wpex_asset_url( 'js/dynamic/woocommerce/wpex-wc-functions.min.js' ),
					array( 'jquery' ),
					WPEX_THEME_VERSION,
					true
				);

				$script_data = array(
					'quantityButtons' => 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)',
				);

				if ( wpex_get_mod( 'woo_show_entry_add_to_cart', true )
					&& wpex_get_mod( 'woo_added_to_cart_notice', true )
				) {
					$script_data['addedToCartNotice'] = apply_filters( 'wpex_woocommerce_added_to_cart_notice', esc_html__( 'was added to your shopping cart.', 'total' ) );
				}

				$script_data['lightboxTitles'] = wpex_get_mod( 'woo_product_gallery_lightbox_titles', false );

				wp_localize_script(
					'wpex-wc-functions',
					'wpexWC',
					$script_data
				);

			}

		}

		/**
		 * Change onsale text.
		 *
		 * @since 2.0.0
		 */
		public static function woocommerce_sale_flash( $text, $post, $_product ) {
			return '<span class="onsale">' . esc_html__( 'Sale', 'total' ) . '</span>';
		}

		/**
		 * Returns correct posts per page for the shop
		 *
		 * @since 3.0.0
		 */
		public static function loop_shop_per_page() {
			$posts_per_page = wpex_get_mod( 'woo_shop_posts_per_page' );
			$posts_per_page = $posts_per_page ? $posts_per_page : '12';
			return $posts_per_page;
		}

		/**
		 * Change products per row for the main shop.
		 *
		 * @since 2.0.0
		 */
		public static function loop_shop_columns() {
			$columns = wpex_get_array_first_value( wpex_get_mod( 'woocommerce_shop_columns', '4' ) );
			$columns = $columns ? $columns : '4'; // always needs a fallback
			return $columns;
		}

		/**
		 * Change products per row for upsells.
		 *
		 * @since 2.0.0
		 */
		public static function upsell_display() {

			// Get count
			$count = wpex_get_mod( 'woocommerce_upsells_count' );
			$count = $count ? $count : '4';

			// Get columns
			$columns = wpex_get_array_first_value( wpex_get_mod( 'woocommerce_upsells_columns' ) );
			$columns = $columns ? $columns : '4';

			// Alter upsell display
			woocommerce_upsell_display( $count, $columns );

		}

		/**
		 * Change products per row for crossells.
		 *
		 * @since 2.0.0
		 */
		public static function cross_sell_display() {

			// Get count
			$count = wpex_get_mod( 'woocommerce_cross_sells_count' );
			$count = $count ? $count : '2';

			// Get columns
			$columns = wpex_get_array_first_value( wpex_get_mod( 'woocommerce_cross_sells_columns' ) );
			$columns = $columns ? $columns : '2';

			// Alter cross-sell display
			woocommerce_cross_sell_display( $count, $columns );

		}

		/**
		 * Alter the related product arguments.
		 *
		 * @since 2.0.0
		 */
		public static function related_product_args() {

			// Get global vars
			global $product, $orderby, $related;

			// Get posts per page
			$posts_per_page = wpex_get_mod( 'woocommerce_related_count' );
			$posts_per_page = $posts_per_page ? $posts_per_page : '4';

			// Get columns
			$columns = wpex_get_array_first_value( wpex_get_mod( 'woocommerce_related_columns' ) );
			$columns = $columns ? $columns : '4';

			// Return array
			return array(
				'posts_per_page' => $posts_per_page,
				'columns'        => $columns,
			);

		}

		/**
		 * Clear floats after single product summary.
		 *
		 * @since 2.0.0
		 */
		public static function clear_summary_floats() {
			echo '<div class="wpex-clear-after-summary wpex-clear"></div>';
		}

		/**
		 * Tweaks pagination arguments.
		 *
		 * @since 2.0.0
		 */
		public static function pagination_args( $args ) {
			$arrow_style = wpex_get_mod( 'pagination_arrow' );
			$arrow_style = $arrow_style ? esc_attr( $arrow_style ) : 'angle';
			$args['prev_text'] = '<i class="ticon ticon-' . $arrow_style . '-left"></i>';
			$args['next_text'] = '<i class="ticon ticon-' . $arrow_style . '-right"></i>';
			return $args;
		}

		/**
		 * Alter continue shoping URL.
		 *
		 * @since 2.0.0
		 */
		public static function continue_shopping_redirect( $return_to ) {
			if ( $shop_id  = wc_get_page_id( 'shop' ) ) {
				$shop_id   = wpex_parse_obj_id( $shop_id, 'page' );
				$return_to = get_permalink( $shop_id );
			}
			return $return_to;
		}

		/**
		 * Disables the next/previous links if disabled via the customizer.
		 *
		 * @since 2.0.0
		 */
		public static function next_prev( $return ) {
			if ( ! wpex_get_mod( 'woo_next_prev', true ) && is_singular( 'product' ) && is_woocommerce() ) {
				$return = false;
			}
			return $return;
		}

		/**
		 * Disable term page header image by default on Woo cats
		 *
		 * @since 3.6.0
		 */
		public static function term_page_header_image_enabled( $bool ) {
			if ( is_tax( 'product_cat' ) ) {
				$bool = false;
			}
			return $bool;
		}

		/**
		 * Alter WooCommerce category classes
		 *
		 * @since 3.0.0
		 */
		public static function product_cat_class( $classes ) {
			global $woocommerce_loop;
			$classes[] = 'col';
			$classes[] = wpex_grid_class( $woocommerce_loop['columns'] );
			return $classes;
		}

		/**
		 * Alter product tag cloud widget args
		 *
		 * @since 4.2
		 */
		public static function tag_cloud_widget_args( $args ) {
			$args['largest']  = '0.923';
			$args['smallest'] = '0.923';
			$args['unit']     = 'em';
			return $args;
		}

		/**
		 * Add typography options for the WooCommerce product title
		 *
		 * @since 3.0.0
		 */
		public static function typography_settings( $settings ) {
			$settings['woo_entry_title'] = array(
				'label' => esc_html__( 'WooCommerce Entry Title', 'total' ),
				'target' => '.woocommerce ul.products li.product .woocommerce-loop-product__title,.woocommerce ul.products li.product .woocommerce-loop-category__title',
				'margin' => true,
			);
			$settings['woo_product_title'] = array(
				'label' => esc_html__( 'WooCommerce Product Title', 'total' ),
				'target' => '.woocommerce div.product .product_title',
				'margin' => true,
			);
			$settings['woo_post_tabs_title'] = array(
				'label' => esc_html__( 'WooCommerce Tabs Title', 'total' ),
				'target' => '.woocommerce-tabs h2',
				'margin' => true,
			);
			$settings['woo_upsells_related_title'] = array(
				'label' => esc_html__( 'WooCommerce Up-Sells & Related Title', 'total' ),
				'target' => '.woocommerce .upsells.products h2, .woocommerce .related.products h2',
				'margin' => true,
			);
			return $settings;
		}

		/**
		 * Tweak comment form args
		 *
		 * @since 4.0
		 */
		public function comment_form_args( $args ) {
			$args['title_reply'] = esc_html__( 'Leave a customer review', 'total' );
			return $args;
		}

		/**
		 * Alter orders per-page on account page
		 *
		 * @since 4.0
		 */
		public function woocommerce_my_account_my_orders_query( $args ) {
			$args['limit'] = 20;
			return $args;
		}

	}

}
new WPEX_WooCommerce_Config();