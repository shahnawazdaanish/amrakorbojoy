<?php
/**
 * Custom user actions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.9.6
 */

namespace TotalTheme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomActions {

	/**
	 * Start things up
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_page' ), 40 );
			add_action( 'admin_init', array( $this,'register_settings' ) );
		} else {
			add_action( 'init', array( $this,'output' ) );
		}
	}

	/**
	 * Add sub menu page
	 *
	 * @since 3.0.0
	 */
	public function add_page() {
		$slug = WPEX_THEME_PANEL_SLUG;
		add_submenu_page(
			$slug,
			esc_html__( 'Custom Actions', 'total' ),
			esc_html__( 'Custom Actions', 'total' ),
			'administrator',
			$slug .'-user-actions',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 *
	 * @since 3.0.0
	 */
	public function register_settings() {
		register_setting( 'wpex_custom_actions', 'wpex_custom_actions', array( $this, 'admin_sanitize' ) );
	}

	/**
	 * Main Sanitization callback
	 *
	 * @since 3.0.0
	 */
	public function admin_sanitize( $options ) {

		if ( ! empty( $options ) ) {

			// Loop through options and save them
			foreach ( $options as $key => $val ) {

				// Delete action if empty
				if ( empty( $val['action'] ) ) {
					unset( $options[$key] );
				}

				// Validate settings
				else {

					// Priority must be a number
					if ( ! empty( $val['priority'] ) ) {
						$options[$key]['priority'] = intval( $val['priority'] );
					}


				}
			}

			return $options;

		}

	}

	/**
	 * Settings page output
	 *
	 * @since 3.0.0
	 */
	public function create_admin_page() { ?>

		<div class="wrap wpex-custom-actions-admin-wrap">

			<h1><?php esc_html_e( 'Custom Actions', 'total' ); ?></h1>

			<p><?php esc_html_e( 'Here you can insert HTML code into any section of the theme. PHP code is not allowed for security reasons. If you wish to insert PHP code into a theme action you will want to use a child theme.', 'total' ); ?></p>

			<hr />

			<form method="post" action="options.php">

				<?php settings_fields( 'wpex_custom_actions' ); ?>

				<?php $options = get_option( 'wpex_custom_actions' ); ?>

				<div id="poststuff" class="wpex-custom-actions">

					<div id="post-body" class="metabox-holder columns-2">

						<div id="post-body-content">

							<div id="post-body-content" class="postbox-container">

								<div class="meta-box-sortables ui-sortable">

									<?php
									// Get hooks
									$wp_hooks = array(
										'wp_hooks' => array(
											'label' => 'WordPress',
											'hooks' => array(
												'wp_head',
												'wp_body_open',
												'wp_footer',
											),
										),
										'html' => array(
											'label' => 'HTML',
											'hooks' => array( 'wpex_hook_after_body_tag' )
										)
									);

									// Theme hooks
									$theme_hooks = wpex_theme_hooks();

									// Remove header hooks if builder is enabled
									if ( wpex_header_builder_id() ) {
										unset( $theme_hooks['header'] );
										unset( $theme_hooks['header_logo'] );
										unset( $theme_hooks['main_menu'] );
									}

									// Combine hooks
									$hooks = ( $wp_hooks + $theme_hooks );

									// Loop through sections
									foreach( $hooks as $section ) { ?>

										<h2><?php echo wp_strip_all_tags( $section['label'] ); ?></h2>

										<?php
										// Loop through hooks
										$hooks = $section['hooks'];

										foreach ( $hooks as $hook ) {

											// Get data
											$action   = ! empty( $options[$hook]['action'] ) ? $options[$hook]['action'] : '';
											$priority = isset( $options[$hook]['priority'] ) ? intval( $options[$hook]['priority'] ) : 10;  ?>

											<div class="postbox closed">

												<div class="handlediv" title="Click to toggle"></div>

												<?php
												// Inline css
												$inline_style = 'padding-right:10px;';
												if ( is_rtl() ) {
													$inline_style = 'padding-left:10px;';
												} ?>

												<h3 class="hndle<?php if ( $action ) echo ' active'; ?>"><span><span class="dashicons dashicons-editor-code" style="<?php echo esc_attr( $inline_style ); ?>"></span><?php echo wp_strip_all_tags( $hook ); ?></span><span class="dashicons dashicons-arrow-down"></span></h3>

												<div class="inside">

													<p>
														<label><?php esc_html_e( 'Code', 'total' ); ?></label>
														<textarea placeholder="<?php esc_attr_e( 'Enter your custom action here&hellip;', 'total' ); ?>" name="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][action]" rows="10" cols="50" style="width:100%;" class="wpex-textarea"><?php echo esc_textarea( $action ); ?></textarea>
													</p>

													<p class="wpex-clr">
														<label><?php esc_html_e( 'Priority', 'total' ); ?></label>
														<input name="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][priority]" type="number" value="<?php echo esc_attr( $priority ); ?>" class="wpex-priority">
													</p>

												</div><!-- .inside -->

											</div><!-- .postbox -->

										<?php } ?>

									<?php } ?>

								</div><!-- .meta-box-sortables -->

							</div><!-- #post-body-content -->

							<div id="postbox-container-1" class="postbox-container">

								<div class="postbox">

									<h3 class='hndle'><span><span class="dashicons dashicons-upload" style="margin-right:7px;"></span><?php esc_html_e( 'Save Your Actions', 'total' ); ?></span></h3>

									<div class="inside">

										<p><?php esc_html_e( 'Click the button below to save your custom actions.', 'total' ); ?></p>

										<?php submit_button(); ?>

									</div><!-- .inside -->

								</div><!-- .postbox -->

							</div><!-- .postbox-container -->

						</div><!-- #post-body-content -->

					</div><!-- #post-body -->

				</div><!-- #poststuff -->

			</form>

		</div><!-- .wrap -->

	<?php }

	/**
	 * Outputs code on the front-end
	 *
	 * @since 3.0.0
	 */
	public function output() {

		// Get actions
		$actions = get_option( 'wpex_custom_actions' );

		// Return if actions are empty
		if ( empty( $actions ) ) {
			return;
		}

		// Loop through options
		foreach ( $actions as $key => $val ) {
			if ( ! empty( $val['action'] ) ) {
				$priority = isset( $val['priority'] ) ? intval( $val['priority'] ) : 10;
				add_action( $key, array( $this, 'execute_action' ), $priority );
			}
		}

	}

	/**
	 * Used to execute an action
	 *
	 * @since 3.0.0
	 */
	public function execute_action() {

		// Set main vars
		$hook    = current_filter();
		$actions = get_option( 'wpex_custom_actions' );
		$php     = ! empty( $actions[$hook]['php'] ) ?  true : false;
		$output  = $actions[$hook]['action'];

		// Output
		if ( $output ) {
			if ( $php ) {
				echo htmlspecialchars( $output );
			} else {
				echo do_shortcode( $output );
			}
		}

	}

}
new CustomActions();