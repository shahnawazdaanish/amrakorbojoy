<?php
/**
 * Breadcrumbs output
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.0
 */

// Return if breadcrumbs are disabled
// Check MUST be added here in case we are adding breadcrumbs via child theme
if ( ! wpex_has_breadcrumbs() ) {
	return;
}

// Echo theme breadrumbs
echo wpex_get_breadcrumbs_output();