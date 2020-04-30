<?php
/**
 * Outputs the portfolio entry title
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.4.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<h2 class="portfolio-entry-title entry-title"><a href="<?php wpex_permalink(); ?>"><?php the_title(); ?></a></h2>