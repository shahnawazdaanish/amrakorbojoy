<?php
/*
Plugin Name: Total - Sticky Header Two, Three, Four
Description: Removes the sticky menu and adds sticky header support for header styles 2, 3 and 4.
Version: 1.2
Author: Aj Clarke
Author URI: http://www.wpexplorer.com
License: GPLv2
Prefix: total_ext_shttf
*/

class Total_Sticky_Header_Two_Three_Four {

	public function __construct() {
		add_filter( 'wpex_header_menu_wrap_classes', array( $this, 'menu_classes' ), 50 );
		add_filter( 'wpex_localize_array', array( $this, 'localize_array' ), 50 );
		add_filter( 'wpex_has_fixed_header', array( $this, 'has_fixed_header' ), 50 );
		add_filter( 'wpex_customizer_sections', array( $this, 'customizer_sections' ), 50 );
		add_filter( 'wpex_has_shrink_sticky_header', array( $this, 'has_shrink' ), 50 );
	}

	public function menu_classes( $classes ) {
		unset( $classes['fixed-nav'] );
		return $classes;
	}

	public function customizer_sections( $sections ) {
		if ( is_array( $sections ) ) {
			unset( $sections['wpex_fixed_menu'] );
			if ( ! empty( $sections['wpex_header_fixed']['settings'] ) ) {
				$new_settings = $sections['wpex_header_fixed']['settings'];
				foreach ( $new_settings as $key => $val ) {
					unset( $new_settings[$key]['control']['active_callback'] );
				}
				$sections['wpex_header_fixed']['settings'] = $new_settings;
			}
		}
		return $sections;
	}

	public function has_fixed_header( $bool ) {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$bool = false; // Disable in VC editor
		} elseif ( 'disabled' != wpex_sticky_header_style() && 'six' != wpex_sticky_header_style() ) {
			$bool = true;
		}
		return $bool;
	}

	public function localize_array( $array ) {
		$array['hasStickyNavbar'] = false;
		unset( $array['hasStickyNavbarMobile'] );
		unset( $array['stickyNavbarBreakPoint'] );
		return $array;
	}

	public function has_shrink( $bool ) {
		$sticky_style = wpex_sticky_header_style();
		if ( 'shrink' == $sticky_style || 'shrink_animated' == $sticky_style ) {
			$header_style = wpex_header_style();
			if ( 'one' == $header_style
				|| 'two'  == $header_style
				|| 'three'  == $header_style
				|| 'four'  == $header_style
				|| 'five' == $header_style
			) {
				$bool = true;
			}
		}
		return $bool;
	}

}
new Total_Sticky_Header_Two_Three_Four;