<?php
/**
 * Custom Post Type Entry Title
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.4.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<header class="cpt-entry-header wpex-clr">
	<h2 class="cpt-entry-title entry-title"><a href="<?php wpex_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
</header>