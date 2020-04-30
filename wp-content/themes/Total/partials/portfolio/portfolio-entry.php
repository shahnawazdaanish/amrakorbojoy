<?php
/**
 * Main portfolio entry template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get loop type (archive or related)
$loop = isset( $wpex_loop ) ? $wpex_loop : 'archive'; ?>

<article id="#post-<?php the_ID(); ?>" <?php post_class( wpex_portfolio_entry_classes( $loop ) ); ?>>
	<div class="portfolio-entry-inner wpex-clr">
		<?php
		// Include entry media, include is required to pass along $wpex_loop var
		if ( $template = locate_template( 'partials/portfolio/portfolio-entry-media.php' ) ) {
			include( $template );
		}
		// Get entry content
		get_template_part( 'partials/portfolio/portfolio-entry-content' ); ?>
	</div>
</article>