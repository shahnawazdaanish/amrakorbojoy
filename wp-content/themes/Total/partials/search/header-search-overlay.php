<?php
/**
 * Site header search dropdown HTML
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="wpex-searchform-overlay" class="header-searchform-wrap wpex-fs-overlay" data-placeholder="<?php echo esc_attr( wpex_get_header_menu_search_form_placeholder() ); ?>" data-disable-autocomplete="true">
	<div class="wpex-close">&times;<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'total' ); ?></span></div>
	<div class="wpex-inner wpex-scale">
		<div class="wpex-title"><?php esc_html_e( 'Search', 'total' ); ?></div>
		<?php echo wpex_get_header_menu_search_form(); ?>
		<span class="ticon ticon-search" aria-hidden="true"></span>
	</div>
</div>