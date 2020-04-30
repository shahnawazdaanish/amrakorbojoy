<?php
/**
 * Loop Blog
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add to counter
global $wpex_count;
$wpex_count++;

// Get layout
wpex_get_template_part( 'blog_entry' );

// Reset counter to clear floats
if ( wpex_get_array_first_value( wpex_blog_entry_columns() ) == $wpex_count ) {
    $wpex_count=0;
}