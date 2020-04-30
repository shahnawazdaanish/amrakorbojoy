<?php
/**
 * Contains a list of all custom WPBakery modules.
 *
 * @package Total Theme Core
 * @subpackage WPBakery
 * @version 1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return array of vcex modules.
 */
function vcex_builder_modules() {
	$modules = array(
		'animated_text',
		'author_bio',
		'blog_carousel',
		'blog_grid',
		'breadcrumbs',
		'bullets',
		'button',
		'callout',
		'countdown',
		'column_side_border',
		'custom_field',
		'divider_dots',
		'divider_multicolor',
		'divider',
		'feature_box',
		'form_shortcode',
		'heading',
		'icon_box',
		'icon',
		'image',
		'image_banner',
		'image_before_after',
		'image_carousel',
		'image_flexslider',
		'image_galleryslider',
		'image_grid',
		'image_swap',
		'leader',
		'list_item',
		'login_form',
		'milestone',
		'multi_buttons',
		'navbar',
		'newsletter_form',
		'portfolio_carousel',
		'portfolio_grid',
		'post_comments',
		'post_content',
		'post_media',
		'post_meta',
		'post_next_prev',
		'post_series',
		'post_series',
		'post_terms',
		'post_type_archive',
		'post_type_carousel',
		'post_type_grid',
		'post_type_slider',
		'pricing',
		'recent_news',
		'searchbar',
		'shortcode',
		'skillbar',
		'social_links',
		'social_share',
		'spacing',
		'staff_carousel',
		'staff_grid',
		'staff_social',
		'teaser',
		'terms_carousel',
		'terms_grid',
		'testimonials_carousel',
		'testimonials_grid',
		'testimonials_slider',
		'users_grid',
		'woocommerce_carousel',
		'woocommerce_loop_carousel',
		'grid_item-post_excerpt',
		'grid_item-post_meta',
		'grid_item-post_terms',
		'grid_item-post_video',
	);
	return apply_filters( 'vcex_builder_modules', $modules );
}