<?php
/**
 * Mobile Menu alternative.
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.6.5
 */ ?>

<div id="mobile-menu-alternative" class="wpex-hidden"<?php wpex_aria_landmark( 'mobile_menu_alt' ); ?> aria-label="<?php echo wpex_get_mod( 'mobile_menu_aria_label', esc_attr_x( 'Mobile menu', 'aria-label', 'total' ) ); ?>"><?php
		wp_nav_menu( array(
			'theme_location' => 'mobile_menu_alt',
			'menu_class'     => 'dropdown-menu',
			'fallback_cb'    => false,
		) );
?></div>