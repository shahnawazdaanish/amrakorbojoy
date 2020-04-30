<?php
/**
 * Site header search dropdown HTML
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="searchform-dropdown" class="header-searchform-wrap clr" data-placeholder="<?php echo esc_attr( wpex_get_header_menu_search_form_placeholder() ); ?>" data-disable-autocomplete="true">
	<?php echo wpex_get_header_menu_search_form(); ?>
</div>