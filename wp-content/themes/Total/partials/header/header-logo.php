<?php
/**
 * Header Logo
 *
 * The default elements and hooks for the header logo
 * @see partials/header/header-logo-inner.php for the actual logo output.
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.5.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="site-logo" class="<?php echo esc_attr( wpex_header_logo_classes() ); ?>">
	<div id="site-logo-inner" class="clr"><?php wpex_hook_site_logo_inner(); ?></div>
</div>