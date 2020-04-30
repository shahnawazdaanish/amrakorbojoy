<?php
/**
 * Term descriptions
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Display term description if there is one
if ( $term_description = term_description() ) : ?>

	<div class="term-description entry clr"><?php echo term_description(); ?></div>

<?php endif; ?>