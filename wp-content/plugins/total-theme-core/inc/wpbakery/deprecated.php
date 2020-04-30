<?php
/**
 * Deprecated functions.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vcex_ilightbox_skins() {
	return array();
}

function vcex_dummy_image_url() {
	return;
}

function vcex_dummy_image() {
	return;
}

function vcex_sanitize_data() {
	_deprecated_function( 'vcex_sanitize_data', '3.0.0', 'wpex_sanitize_data' );
}

function vcex_image_rendering() {
	return;
}

function vcex_inline_js() {
	return; // deprecated in 3.6.0 removed completely in 4.0
}

function vcex_parse_old_design_js() {
	return;
}

