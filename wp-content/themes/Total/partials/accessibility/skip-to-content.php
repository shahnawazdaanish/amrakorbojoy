<?php
/**
 * Skip To Content
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.6.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get correct content ID
$id = esc_attr( wpex_get_mod( 'skip_to_content_id' ) );
$id = $id ? $id : 'content'; ?>

<a href="#<?php echo str_replace( '#', '', $id ); ?>" class="skip-to-content"<?php wpex_aria_landmark( 'skip_to_content' ); ?>><?php echo esc_html__( 'skip to Main Content', 'total' ); ?></a>