<?php
/**
 * Togglebar button output
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get default state
$default_state = wpex_get_mod( 'toggle_bar_default_state', 'hidden' );

// Link attributes
$attrs = array(
	'href'        => '#',
	'class'       => 'toggle-bar-btn fade-toggle open-togglebar',
	'aria-hidden' => 'true',
);

// Visibility
if ( $visibility = wpex_get_mod( 'toggle_bar_visibility', 'always-visible' ) ) {
	$attrs['class'] .= ' ' . $visibility;
}

// Add active class if set to display by default
if ( 'visible' == $default_state ) {
	$attrs['class'] .= ' active-bar';
}

// Closed icon classes
$closed_icon = wpex_get_mod( 'toggle_bar_button_icon', 'plus' );
$closed_icon = esc_attr( apply_filters( 'wpex_togglebar_icon_class', 'ticon ticon-'. $closed_icon ) );

// Active icon classes
$active_icon = wpex_get_mod( 'toggle_bar_button_icon_active', 'minus' );
$active_icon = esc_attr( apply_filters( 'wpex_togglebar_icon_active_class', 'ticon ticon-'. $active_icon ) );

// Default icon
$default_icon = ( 'visible' == $default_state ) ? $active_icon : $closed_icon;

// Closed icon
$attrs['data-icon'] = $closed_icon;

// Active icon
$attrs['data-icon-hover'] = $active_icon;

// Icon
$icon = '<span class="' . $default_icon . '"></span>';

// Display button
echo wpex_parse_html( 'a', $attrs, $icon ); ?>