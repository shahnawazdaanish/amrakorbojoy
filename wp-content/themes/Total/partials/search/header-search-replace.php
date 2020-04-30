<?php
/**
 * Site header search dropdown HTML
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="searchform-header-replace" class="clr header-searchform-wrap" data-placeholder="<?php echo esc_attr( wpex_get_header_menu_search_form_placeholder() ); ?>" data-disable-autocomplete="true">
	<?php echo wpex_get_header_menu_search_form(); ?>
	<span id="searchform-header-replace-close" class="wpex-disable-user-select">&times;<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'total' ); ?></span></span>
</div>