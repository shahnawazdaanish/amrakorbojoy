<?php
/**
 * Perform actions after updating the theme => Runs on Init.
 *
 * @package Total WordPress Theme
 * @subpackage Updates
 * @version 4.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hook to init to prevent any possible conflicts in main theme class
function wpex_after_update() {

	// Get user theme version
	$user_v = get_option( 'total_version' );

	// For testing purposes
	//$user_v = '';

	// If already on current version we don't need to do anything at all
	if ( $user_v == WPEX_THEME_VERSION ) {
		return;
	}

	// Add initial version so we know the first time a user activated the theme
	if ( ! get_option( 'total_initial_version' ) ) {
		update_option( 'total_initial_version', WPEX_THEME_VERSION, false );
	}

	// Old version is required and was added in v2.1.3
	// Prevents functions from running for new customers
	$user_v = $user_v ? $user_v : '2.1.3';

	/*-------------------------------------------------------------------------------*/
	/* - Functions that will always run after update
	/*-------------------------------------------------------------------------------*/

	// Backup theme mods
	wpex_backup_mods();

	// Re-enable recommended plugins notice for updates
	set_theme_mod( 'recommend_plugins_enable', true );
	delete_metadata( 'user', null, 'tgmpa_dismissed_notice_wpex_theme', null, true );

	// Reset plugin updates transient
	set_site_transient( 'update_plugins', null );

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.0.0
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.0.0', $user_v, '>' ) ) {
		require_once WPEX_FRAMEWORK_DIR . 'updates/update-3_0_0.php';
	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.3.0
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.3.0', $user_v, '>' ) ) {

		// Turn retina logo height into just logo height and delete old theme mod
		if ( $mod = wpex_get_mod( 'retina_logo_height' ) ) {
			set_theme_mod( 'logo_height', $mod );
			remove_theme_mod( 'retina_logo_height' );
		}

		// WooMenu cart enable/disable
		if ( ! wpex_get_mod( 'woo_menu_icon', true ) ) {
			set_theme_mod( 'woo_menu_icon_display', 'disabled' );
			remove_theme_mod( 'woo_menu_icon' );
		}

		// Sidebar heading color => remove duplicate setting
		if ( $mod = wpex_get_mod( 'sidebar_headings_color' ) ) {
			$mod2 = wpex_get_mod( 'sidebar_widget_title_typography' );
			if ( is_array( $mod2 ) ) {
				$mod2['color'] = $mod;
			} else {
				$mod2 = array( 'color' => $mod );
			}
			set_theme_mod( 'sidebar_widget_title_typography', $mod2 );
			remove_theme_mod( 'sidebar_headings_color' );
		}

		// Remove license key
		delete_option( 'wpex_product_license' );
		remove_theme_mod( 'envato_license_key' );

		// New single product thumb image sizes | Set equal to current post thumbnail size
		if ( WPEX_WOOCOMMERCE_ACTIVE ) {
			if ( $mod = wpex_get_mod( 'woo_post_width' ) ) {
				set_theme_mod( 'woo_post_thumb_width', $mod );
			}
			if ( $mod = wpex_get_mod( 'woo_post_height' ) ) {
				set_theme_mod( 'woo_post_thumb_height', $mod );
			}
			if ( $mod = wpex_get_mod( 'woo_post_image_crop' ) ) {
				set_theme_mod( 'woo_post_thumb_crop', $mod );
			}
		}

		// Auto updates removed
		delete_option( 'wpex_product_license' );

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.3.2
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.3.2', $user_v, '>' ) ) {

		// Set correct related image sizes => Portfolio
		if ( $mod = wpex_get_mod( 'portfolio_entry_image_width' ) ) {
			set_theme_mod( 'portfolio_related_image_width', $mod );
		}
		if ( $mod = wpex_get_mod( 'portfolio_entry_image_height' ) ) {
			set_theme_mod( 'portfolio_related_image_height', $mod );
		}
		if ( $mod = wpex_get_mod( 'portfolio_entry_image_crop' ) ) {
			set_theme_mod( 'portfolio_related_image_crop', $mod );
		}

		// Set correct related image sizes => Staff
		if ( $mod = wpex_get_mod( 'staff_entry_image_width' ) ) {
			set_theme_mod( 'staff_related_image_width', $mod );
		}
		if ( $mod = wpex_get_mod( 'staff_entry_image_height' ) ) {
			set_theme_mod( 'staff_related_image_height', $mod );
		}
		if ( $mod = wpex_get_mod( 'staff_entry_image_crop' ) ) {
			set_theme_mod( 'staff_related_image_crop', $mod );
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.3.3
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.3.3', $user_v, '>' ) ) {

		// Remove useless settings
		delete_option( 'wpex_portfolio_branding' );
		delete_option( 'wpex_staff_branding' );
		delete_option( 'wpex_testimonials_branding' );

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.4.0
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.4.0', $user_v, '>' ) ) {
		if ( ! get_theme_mod( 'fixed_header', true ) ) {
			set_theme_mod( 'fixed_header_style', 'disabled' );
			remove_theme_mod( 'fixed_header' );
		}
		remove_theme_mod( 'shink_fixed_header' );
	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 3.5.0
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '3.5.0', $user_v, '>' ) ) {

		// Update page composer based on settings
		$composer = array( 'content' );
		if ( wpex_get_mod( 'page_featured_image' ) ) {
			unset( $composer[0] );
			$composer[] = 'media';
			$composer[] = 'content';
		}
		if ( wpex_get_mod( 'social_share_pages' ) ) {
			$composer[] = 'share';
		}
		if ( wpex_get_mod( 'page_comments' ) ) {
			$composer[] = 'comments';
		}
		$composer = implode( ',', $composer );
		set_theme_mod( 'page_composer', $composer );

		remove_theme_mod( 'page_featured_image' );
		remove_theme_mod( 'social_share_pages' );
		remove_theme_mod( 'page_comments' );

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 4.0
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '4.0', $user_v, '>' ) ) {

		// Port custom CSS to new WP custom CSS function if WP is up to date
		if ( function_exists( 'wp_get_custom_css' ) && $deprecated_css = wpex_get_mod( 'custom_css', null ) ) {

			$core_css = wp_get_custom_css();
			$return   = wp_update_custom_css_post( $core_css . $deprecated_css );

			if ( ! is_wp_error( $return ) ) {

				// Save backup then remove deprecated
				update_option( 'wpex_custom_css_backup', $deprecated_css, false ); // Save backup just incase

				// Remove option
				remove_theme_mod( 'custom_css' );

			}

		}

		// Update patterns bg url
		if ( $pattern = wpex_get_mod( 'background_pattern' ) ) {
			$pattern = str_replace( array( '.png', WPEX_THEME_URI . '/images/patterns/' ), '', $pattern );
			set_theme_mod( 'background_pattern', $pattern );
		}

		// Update load custom font 1 setting
		if ( $mod = wpex_get_mod( 'load_custom_font_1_typography' ) ) {
			$font_family = isset( $mod['font-family'] ) ? $mod['font-family'] : '';
			set_theme_mod( 'load_custom_google_font_1', $mod['font-family'] );
			remove_theme_mod( 'load_custom_font_1_typography' );
		}

		// Fix for removed social_share_heading_enable setting
		// which wasn't needed because you could just leave the sharing text empty instead.
		if ( ! wpex_get_mod( 'social_share_heading_enable', true ) ) {
			set_theme_mod( 'social_share_heading', '' );
			remove_theme_mod( 'social_share_heading_enable' );
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 4.3
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '4.3', $user_v, '>' ) ) {

		// Update footer widget colors
		$mods = array(
			'footer_widget_title_typography'  => 'footer_headings_color',
			'sidebar_widget_title_typography' => 'sidebar_headings_color',
		);
		foreach ( $mods as $old => $new ) {
			$mod = wpex_get_mod( $old );
			if ( isset( $mod['color'] ) ) {
				set_theme_mod( $new, $mod['color'] );
				unset( $mod['color'] );
				set_theme_mod( $old, $mod );
			}
		}

		// Convert some settings to prevent conflicts
		$mods = array(
			'background_image',
			'background_color',
			'background_style',
			'background_pattern',
		);
		foreach ( $mods as $mod ) {
			if ( $val = wpex_get_mod( $mod ) ) {
				set_theme_mod( 't_' . $mod, $val );
				remove_theme_mod( $mod );
			}
		}

		// Update Customizer image settings
		if ( function_exists( 'attachment_url_to_postid' ) ) {

			$media_settings = array(
				'custom_logo',
				'retina_logo',
				'fixed_header_logo',
				'fixed_header_logo_retina',
				'background_image',
				'page_header_background_img',
			);

			foreach ( $media_settings as $setting ) {

				if ( $mod = wpex_get_mod( $setting ) ) {

					$mod_id = attachment_url_to_postid( $mod );

					if ( $mod_id ) {

						set_theme_mod( $setting, $mod_id );

					}

				}


			}

		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 4.4
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '4.4.1', $user_v, '>' ) ) {

		// Remove old customizer setting for shop slider
		if ( $mod = wpex_get_mod( 'woo_shop_slider' ) ) {
			if ( function_exists( 'wc_get_page_id' ) && $shop_id  = wc_get_page_id( 'shop' ) ) {
				update_post_meta( $shop_id, 'wpex_post_slider_shortcode', $mod );
				update_post_meta( $shop_id, 'wpex_post_slider_bottom_margin', '30px' );
			}
			remove_theme_mod( 'woo_shop_slider' );
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 4.5.2
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '4.5.2', $user_v, '>' ) ) {

		if ( $mod = wpex_get_mod( 'wpex_ybtt_trim_title' ) ) {
			set_theme_mod( 'breadcrumbs_title_trim', $mod );
			remove_theme_mod( 'wpex_ybtt_trim_title' );
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  UPDATE: 4.8.4
	/*-------------------------------------------------------------------------------*/
	if ( version_compare( '4.8.4', $user_v, '>' ) ) {

		// Remove autoloading for old settings
		if ( $old_option = get_option( 'wpex_custom_css_backup' ) ) {
			update_option( 'wpex_custom_css_backup', $old_option, false );
		}
		if ( $old_option = get_option( 'total_import_theme_mods_backup' ) ) {
			update_option( 'total_import_theme_mods_backup', $old_option, false );
		}
		if ( $old_option = get_option( 'wpex_total_customizer_backup' ) ) {
			update_option( 'wpex_total_customizer_backup', $old_option, false );
		}

	}

	/*-------------------------------------------------------------------------------*/
	/* -  *** Update Theme Version ***
	/*-------------------------------------------------------------------------------*/
	update_option( 'total_version', WPEX_THEME_VERSION, false );

}
add_action( 'init', 'wpex_after_update' );