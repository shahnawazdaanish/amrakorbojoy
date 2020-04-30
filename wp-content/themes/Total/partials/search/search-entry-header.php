<?php
/**
 * Search entry header
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.4.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<header class="search-entry-header clr"><h2 class="search-entry-header-title entry-title"><a href="<?php wpex_permalink(); ?>"><?php the_title(); ?></a></h2></header>