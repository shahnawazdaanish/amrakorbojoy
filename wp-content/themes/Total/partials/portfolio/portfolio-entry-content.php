<?php
/**
 * Portfolio entry content template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Return if disabled for standard entries
if ( ! is_singular( 'portfolio' ) && ! wpex_get_mod( 'portfolio_entry_details', true ) ) {
	return;
}

// Return if disabled for related entries
if ( is_singular( 'portfolio' ) && ! wpex_get_mod( 'portfolio_related_excerpts', true ) ) {
	return;
}

// Entry content classes
$classes = 'portfolio-entry-details clr';
if ( wpex_portfolio_match_height() ) {
	$classes .= ' match-height-content';
} ?>

<div class="<?php echo esc_attr( $classes ); ?>">
	<?php get_template_part( 'partials/portfolio/portfolio-entry-title' ); ?>
	<?php get_template_part( 'partials/portfolio/portfolio-entry-excerpt' ); ?>
</div><!-- .portfolio-entry-details -->