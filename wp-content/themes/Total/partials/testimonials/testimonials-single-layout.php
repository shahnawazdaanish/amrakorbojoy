<?php
/**
 * Testimonials single post layout
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.8
 *
 * @todo Allow display of the title in the testimonial seperate from archive entry title setting
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Custom template design
if ( $template_content = wpex_get_singular_template_content( 'testimonials' ) ) {
	wpex_singular_template( $template_content );
	return;
} ?>

<div id="single-blocks" class="clr">

	<div class="entry-content entry wpex-clr">

		<?php

		// "Quote" style
		if ( 'blockquote' == wpex_get_mod( 'testimonial_post_style', 'blockquote' ) ) :

			get_template_part( 'partials/testimonials/testimonials-entry' );

		// Display full content
		else : ?>

			<div class="single-content clr"><?php the_content(); ?></div>

		<?php endif; ?>

	</div><!-- .entry-content -->

	<?php
	// Displays comments if enabled
	if ( wpex_get_mod( 'testimonials_comments', false ) && comments_open() ) : ?>

		<div id="testimonials-post-comments" class="clr"><?php comments_template(); ?></div>

	<?php endif; ?>

</div><!-- #single-blocks -->