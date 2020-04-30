<?php
/**
 * Visual Composer Post Content
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

// Front-end editor placeholder
if ( vcex_vc_is_inline() ) {
	echo '<div class="vcex-frontend-inline-placeholder">' . __( 'Post Content Module Placeholder', 'total-theme-code' ) . '</div>';
	return;
}

// Get post ID
$post_ID = vcex_get_the_ID();

// Get post content
$post_content = get_the_content( $post_ID );

// Return if the current post has this shortcode inside it to prevent infinite loop
if ( strpos( $post_content, 'vcex_post_content' ) !== false ) {
	return;
}

// Get shortcode attributes based on vc_lean_map => This makes sure no attributes are empty
$atts = vcex_vc_map_get_attributes( 'vcex_post_content', $atts, $this );

// Wrap inline style
$wrap_inline_style = array(
	'font_size'   => $atts['font_size'],
	'font_family' => $atts['font_family'],
);

// Load custom Google font if needed
vcex_enqueue_google_font( $atts['font_family'] );

// Define wrap attributes
$wrap_attrs = array(
	'class' => 'vcex-post-content vcex-clr',
);

// Add CSS class
if ( $atts['css'] ) {
	$wrap_attrs['class'] .= ' ' . vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

// Get responsive data
if ( $responsive_data = vcex_get_module_responsive_data( $atts ) ) {
	$wrap_attrs['data-wpex-rcss'] = $responsive_data;
}

// Sidebar check
$has_sidebar = false;
if ( defined( 'TOTAL_THEME_ACTIVE' ) ) {
	$has_sidebar = 'true' == $atts['sidebar'] && apply_filters( 'vcex_post_content_has_sidebar', true ) ? true : false;
} ?>

<div <?php echo vcex_parse_html_attributes( $wrap_attrs ); ?>>

	<?php if ( $has_sidebar ) { ?>

		<div class="wpex-content-w clr">

	<?php } ?>

	<?php if ( function_exists( 'wpex_get_template_part' ) ) { ?>

		<?php
		// Add post series
		if ( 'true' == $atts['post_series'] ) {
			wpex_get_template_part( 'post_series' );
		} ?>

		<div class="vcex-post-content-c clr"<?php echo vcex_inline_style( $wrap_inline_style ); ?>><?php echo apply_filters( 'the_content', $post_content ); ?></div>

		<?php
		// Add social share
		if ( 'true' == $atts['social_share'] ) {
			wpex_get_template_part( 'social_share' );
		} ?>

		<?php
		// Add author box
		if ( 'true' == $atts['author_bio'] ) {
			wpex_get_template_part( 'author_bio' );
		} ?>

		<?php
		// Add related
		if ( 'true' == $atts['related'] ) {
			if ( 'post' == get_post_type( $post_ID ) ) {
				wpex_get_template_part( 'blog_single_related' );
			} else {
				wpex_get_template_part( 'cpt_single_related' );
			}
		} ?>

	<?php } ?>

	<?php
	// Add comments
	if ( 'true' == $atts['comments'] ) {
		comments_template();
	} ?>

	<?php if ( $has_sidebar ) { ?></div><?php } ?>

	<?php if ( $has_sidebar ) { ?>

		<aside id="sidebar" class="sidebar-container sidebar-primary"<?php wpex_schema_markup( 'sidebar' ); ?><?php wpex_aria_landmark( 'sidebar' ); ?>>

			<?php wpex_hook_sidebar_top(); ?>

				<div id="sidebar-inner" class="clr">

					<?php dynamic_sidebar( wpex_get_sidebar() ); ?>

				</div>

			<?php wpex_hook_sidebar_bottom(); ?>

		</aside>

	<?php } ?>

</div>