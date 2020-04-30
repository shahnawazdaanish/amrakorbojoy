<?php
/**
 * The Scroll-Top Button
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get arrow
$arrow = wpex_get_mod( 'scroll_top_arrow' );
$arrow = $arrow ? $arrow : 'chevron-up'; ?>

<a href="#outer-wrap" id="site-scroll-top"<?php wpex_aria_landmark( 'scroll_top' ); ?>><span class="ticon ticon-<?php echo esc_attr( $arrow ); ?>" aria-hidden="true"></span><span class="screen-reader-text"><?php esc_html_e( 'Back To Top', 'total' ); ?></span></a>