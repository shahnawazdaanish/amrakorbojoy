<?php
/**
 * Blog entry avatar
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.5.4.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<header class="blog-entry-header wpex-clr">
	<h2 class="blog-entry-title entry-title"><a href="<?php wpex_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<?php if ( wpex_get_mod( 'blog_entry_author_avatar' ) ) : ?>
		<?php get_template_part( 'partials/blog/blog-entry-avatar' ); ?>
	<?php endif; ?>
</header>