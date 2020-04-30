<?php
/**
 * Customizer => General Panel
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Accent Colors
$this->sections['wpex_accent_colors'] = array(
	'title' => esc_html__( 'Accent Colors', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'accent_color',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Accent Color', 'total' ),
				'type' => 'color',
			),
		),
		array(
			'id' => 'accent_color_hover',
			'transport' => 'partialRefresh',
			'control' => array(
				'label' => esc_html__( 'Accent Hover Color', 'total' ),
				'description' => esc_html__( 'Used for various hovers on accent colors such as buttons. If left empty it will inherit the custom accent color defined above', 'total' ),
				'type' => 'color',
			),
		),
		array(
			'id' => 'main_border_color',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Border Accent Color', 'total' ),
				'type' => 'color',
			),
		),
		array(
			'id' => 'highlight_bg',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Highlight Background', 'total' ),
				'type' => 'color',
			),
			'inline_css' => array(
				'target' => array( '::selection', '::-moz-selection' ),
				'alter' => 'background',
			),
		),
		array(
			'id' => 'highlight_color',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Highlight Color', 'total' ),
				'type' => 'color',
			),
			'inline_css' => array(
				'target' => array( '::selection', '::-moz-selection' ),
				'alter' => 'color',
			),
		),
	)
);

// Background
$this->sections['wpex_background'] = array(
	'title'  => esc_html__( 'Site Background', 'total' ),
	'panel'  => 'wpex_general',
	'desc' => esc_html__( 'Here you can alter the global site background. It is recommended that you first set the site layout to "Boxed" under Layout > General > Site Layout Style.', 'total' ),
	'settings' => array(
		array(
			'id' => 't_background_color',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Background Color', 'total' ),
				'type' => 'color',
			),
			'inline_css' => array(
				'target' => 'body,.footer-has-reveal #main,body.boxed-main-layout',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 't_background_image',
			'sanitize_callback' => 'absint',
			'control' => array(
				'label' => esc_html__( 'Custom Background Image', 'total' ),
				'type' => 'media',
				'mime_type' => 'image',
			),
		),
		array(
			'id' => 't_background_style',
			'default' => 'stretched',
			'control' => array(
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'choices' => $bg_styles,
			),
		),
		array(
			'id' => 't_background_pattern',
			'sanitize_callback' => 'esc_html',
			'control' => array(
				'label' => esc_html__( 'Background Pattern', 'total' ),
				'type'  => 'wpex-bg-patterns',
			),
		),
	),
);

// Social Sharing Section
$social_share_items = wpex_get_social_items();

if ( $social_share_items ) {

	$social_share_choices = array();

	foreach ( $social_share_items as $k => $v ) {
		$social_share_choices[$k] = $v['site'];
	}

	$this->sections['wpex_social_sharing'] = array(
		'title'  => esc_html__( 'Social Share Buttons', 'total' ),
		'panel'  => 'wpex_general',
		'settings' => array(
			array(
				'id' => 'social_share_shortcode',
				'transport' => 'partialRefresh',
				'control' => array(
					'label' => esc_html__( 'Alternative Shortcode', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Override the theme default social share with your custom social sharing shortcode.', 'total' ),
				),
			),
			array(
				'id'  => 'social_share_sites',
				'transport' => 'partialRefresh',
				'default' => array( 'twitter', 'facebook', 'linkedin', 'email' ),
				'control' => array(
					'label'  => esc_html__( 'Sites', 'total' ),
					'desc' => esc_html__( 'Click and drag and drop elements to re-order them.', 'total' ),
					'type' => 'wpex-sortable',
					'object' => 'WPEX_Customize_Control_Sorter',
					'choices' => $social_share_choices,
				),
			),
			array(
				'id' => 'social_share_position',
				'transport' => 'partialRefresh',
				'control' => array(
					'label' => esc_html__( 'Position', 'total' ),
					'type' => 'select',
					'choices' => array(
						'' => esc_html__( 'Default', 'total' ),
						'horizontal' => esc_html__( 'Horizontal', 'total' ),
						'vertical' => esc_html__( 'Vertical (Fixed)', 'total' ),
					),
				),
			),
			array(
				'id' => 'social_share_style',
				'transport' => 'partialRefresh',
				'default' => 'flat',
				'control' => array(
					'label' => esc_html__( 'Style', 'total' ),
					'type'  => 'select',
					'choices' => array(
						'flat' => esc_html__( 'Flat', 'total' ),
						'minimal' => esc_html__( 'Minimal', 'total' ),
						'three-d' => esc_html__( '3D', 'total' ),
						'rounded' => esc_html__( 'Rounded', 'total' ),
						'custom' => esc_html__( 'Custom', 'total' ),
					),
				),
			),
			array(
				'id' => 'social_share_label',
				'transport' => 'partialRefresh',
				'default' => true,
				'control' => array(
					'label' => esc_html__( 'Display Horizontal Style Label?', 'total' ),
					'type' => 'checkbox',
				),
			),
			array(
				'id' => 'social_share_font_size',
				'description' => esc_html__( 'Value in px or em.', 'total' ),
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'text',
					'label' => esc_html__( 'Custom Font Size', 'total' ),
				),
				'inline_css' => array(
					'target' => '.wpex-social-share li a',
					'alter' => 'font-size',
				),
			),
			array(
				'id' => 'social_share_heading',
				'transport' => 'partialRefresh',
				'default' => esc_html__( 'Share This', 'total' ),
				'control' => array(
					'label' => esc_html__( 'Horizontal Position Heading', 'total' ),
					'type'  => 'text',
					'description' => $leave_blank_desc,
				),
			),
			array(
				'id' => 'social_share_twitter_handle',
				'transport' => 'postMessage',
				'control' => array(
					'label' => esc_html__( 'Twitter Handle', 'total' ),
					'type' => 'text',
				),
			),
		)
	);

}

// Breadcrumbs
$this->sections['wpex_breadcrumbs'] = array(
	'title' => esc_html__( 'Breadcrumbs', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'breadcrumbs',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Enable Breadcrumbs?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'breadcrumbs_visibility',
			'transport' => 'postMessage',
			'default' => 'hidden-phone',
			'control' => array(
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'select',
				'choices' => $choices_visibility,
			),
		),
		array(
			'id' => 'breadcrumbs_position',
			'transport' => 'partialRefresh',
			'default' => 'absolute',
			'control' => array(
				'label' => esc_html__( 'Position', 'total' ),
				'type'  => 'select',
				'choices' => array(
					'absolute'  => esc_html__( 'Absolute Right', 'total' ),
					'under-title' => esc_html__( 'Under Title', 'total' ),
					'custom' => esc_html__( 'Custom', 'total' ),
				),
				'active_callback' => 'wpex_cac_has_breadcrumbs',
			),
		),
		array(
			'id' => 'breadcrumbs_disable_in_page_title_with_bg',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Disable Breadcrumbs on Page Titles with Backgrounds?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'breadcrumbs_home_title',
			'transport' => 'partialRefresh',
			'control' => array(
				'label' => esc_html__( 'Custom Home Title', 'total' ),
				'type'  => 'text',
			),
		),
		array(
			'id' => 'breadcrumbs_disable_taxonomies',
			'transport' => 'partialRefresh',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Remove categories and other taxonomies from breadcrumbs?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'breadcrumbs_first_cat_only',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Display First Category Only?', 'total' ),
				'type' => 'checkbox',
			),
			'control_display' => array(
				'check' => 'breadcrumbs_disable_taxonomies',
				'value' => 'false',
			),
		),
		array(
			'id' => 'breadcrumbs_title_trim',
			'transport' => 'partialRefresh',
			'control' => array(
				'label' => esc_html__( 'Title Trim Length', 'total' ),
				'type'  => 'text',
				'desc'  => esc_html__( 'Enter the max number of words to display for your breadcrumbs post title', 'total' ),
			),
		),
		array(
			'id' => 'breadcrumbs_text_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Text Color', 'total' ),
				'active_callback' => 'wpex_cac_has_breadcrumbs',
			),
			'inline_css' => array(
				'target' => '.site-breadcrumbs',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'breadcrumbs_seperator_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Separator Color', 'total' ),
				'active_callback' => 'wpex_cac_has_breadcrumbs',
			),
			'inline_css' => array(
				'target' => '.site-breadcrumbs .sep',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'breadcrumbs_link_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Link Color', 'total' ),
				'active_callback' => 'wpex_cac_has_breadcrumbs',
			),
			'inline_css' => array(
				'target' => '.site-breadcrumbs a',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'breadcrumbs_link_color_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
				'active_callback' => 'wpex_cac_has_breadcrumbs',
			),
			'inline_css' => array(
				'target' => '.site-breadcrumbs a:hover',
				'alter' => 'color',
			),
		),
	),
);

// Page Title
$this->sections['wpex_page_header'] = array(
	'title' => esc_html__( 'Page Header Title', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'page_header_style',
			//'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => $page_header_styles,
			),
		),
		array(
			'id' => 'page_header_hidden_main_top_padding',
			'transport' => 'postMessage',
			'control_display' => array(
				'check' => 'page_header_style',
				'value' => 'hidden',
			),
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Hidden Page Header Title Spacing', 'total' ),
				'desc' => esc_html__( 'When the page header title is set to hidden there will not be any space between the header and the main content. If you want to add a default space between the header and your main content you can enter the px value here.', 'total' ) .'<br /><br />'. $pixel_desc,
			),
			'inline_css' => array(
				'target' => 'body.page-header-disabled #content-wrap',
				'alter' => 'padding-top',
				'sanitize' => 'px',
			),
		),
		array(
			'id' => 'page_header_top_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Top Padding', 'total' ),
				'description' => $pixel_desc,
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'padding-top',
			),
		),
		array(
			'id' => 'page_header_bottom_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Bottom Padding', 'total' ),
				'description' => $pixel_desc,
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'padding-bottom',
			),
		),
		array(
			'id' => 'page_header_bottom_margin',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Bottom Margin', 'total' ),
				'description' => $pixel_desc,
			),
			'inline_css' => array(
				'target' => '.page-header',
				'alter' => 'margin-bottom',
			),
		),
		array(
			'id' => 'page_header_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'page_header_title_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Text Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods .page-header-title',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'page_header_top_border',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Top Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-top-color',
			),
		),
		array(
			'id' => 'page_header_bottom_border',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Bottom Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-bottom-color',
			),
		),
		array(
			'id' => 'page_header_border_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Border Width', 'total' ),
				'description' => $pixel_desc,
			),
			'inline_css' => array(
				'target' => '.page-header.wpex-supports-mods',
				'alter' => array( 'border-width' ),
			),
		),
		array(
			'id' => 'page_header_background_img',
			'transport' => 'refresh',
			'control' => array(
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Background Image', 'total' ),
			),
		),
		array(
			'id' => 'page_header_background_img_style',
			'transport' => 'postMessage',
			'default' => 'fixed',
			'control' => array(
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'active_callback' => 'wpex_cac_has_page_header_title_background',
				'choices' => $bg_styles,
			),
		),
		array(
			'id' => 'page_header_background_fetch_thumbnail',
			'control' => array(
				'type' => 'multiple-select',
				'label' => esc_html__( 'Fetch Background From Featured Image', 'total' ),
				'description' => esc_html__( 'Check the box next to any post type where you want to display the featured image as the page header title background.', 'total' ),
				'active_callback' => 'wpex_cac_has_page_header_title_background',
				'choices' => $post_types,
			),
		),
	),
);

// Pages
$blocks = apply_filters( 'wpex_page_single_blocks', array(
	'title'    => esc_html__( 'Title', 'total' ),
	'media'    => esc_html__( 'Media', 'total' ),
	'content'  => esc_html__( 'Content', 'total' ),
	'share'    => esc_html__( 'Social Share Buttons', 'total' ),
	'comments' => esc_html__( 'Comments', 'total' ),
), 'customizer' );

$this->sections['wpex_pages'] = array(
	'title'  => esc_html__( 'Pages', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'page_single_layout',
			'control' => array(
				'label' => esc_html__( 'Page Layout', 'total' ),
				'type' => 'select',
				'choices' => $post_layouts,
			),
		),
		array(
			'id' => 'page_singular_page_title',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Display Page Header Title?', 'total' ),
				'type' => 'checkbox',
				'active_callback' => 'wpex_cac_has_page_header',
			),
		),
		array(
			'id' => 'pages_custom_sidebar',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Enable Custom Sidebar?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'page_singular_template',
			'default' => '',
			'control' => array(
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'wpex-dropdown-templates',
				'desc' => $template_desc,
			),
		),
		array(
			'id' => 'page_composer',
			'default' => 'content',
			'control' => array(
				'label' => esc_html__( 'Post Layout Elements', 'total' ),
				'type' => 'wpex-sortable',
				'choices' => $blocks,
				'desc' => esc_html__( 'Click and drag and drop elements to re-order them.', 'total' ),
				'active_callback' => 'wpex_cac_page_single_hasnt_custom_template',
			),
		),
	),
);

// Search
$this->sections['wpex_search'] = array(
	'title'  => esc_html__( 'Search Results Page', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'search_standard_posts_only',
			'default' => false,
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Show Standard Posts Only?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'search_style',
			'default' => 'default',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => array(
					'default' => esc_html__( 'Left Thumbnail', 'total' ),
					'blog' => esc_html__( 'Inherit From Blog','total' ),
				),
			),
		),
		array(
			'id' => 'search_layout',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => $post_layouts,
			),
		),
		array(
			'id' => 'search_posts_per_page',
			'default' => '10',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Posts Per Page', 'total' ),
				'type' => 'text',
			),
		),
		array(
			'id' => 'search_custom_sidebar',
			'default' => true,
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Enable Custom Sidebar?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'search_results_cpt_loops',
			'default' => true,
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Enable Post Type Parameter Support?', 'total' ),
				'type' => 'checkbox',
				'description' => esc_html__( 'When displaying custom post type results (using the post_type parameter in the search URL) it will display the results using the default post type archive loop design.', 'total' ),
			),
		),
	),
);

// Pagination
$this->sections['wpex_pagination'] = array(
	'title' => esc_html__( 'Pagination', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'pagination_align',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'select',
				'default' => 'left',
				'label' => esc_html__( 'Alignment', 'total' ),
				'choices' => array(
					'' => esc_html__( 'Default', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				),
			),
		),
		array(
			'id' => 'pagination_arrow',
			'transport' => 'postMessage',
			'default' => 'angle',
			'control' => array(
				'type' => 'select',
				'label' => esc_html__( 'Arrow Style', 'total' ),
				'choices' => array(
					'angle' => esc_html__( 'Angle', 'total' ),
					'arrow' => esc_html__( 'Arrow', 'total' ),
					'caret' => esc_html__( 'Caret', 'total' ),
					'chevron' => esc_html__( 'Chevron', 'total' ),
				),
			),
		),
		array(
			'id' => 'pagination_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => 'ul.page-numbers a,span.page-numbers,.page-links span,.page-links a > span',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'pagination_font_size',
			'description' => esc_html__( 'Value in px or em.', 'total' ),
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Font Size', 'total' ),
			),
			'inline_css' => array(
				'target' => 'ul.page-numbers, .page-links',
				'alter' => 'font-size',
			),
		),
		array(
			'id' => 'pagination_border_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Border Width', 'total' ),
			),
			'inline_css' => array(
				'target' => 'ul.page-numbers, .page-links, ul.page-numbers li, .page-links li',
				'alter' => 'border-width',
			),
		),
		array(
			'id' => 'pagination_border_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'ul.page-numbers, .page-links, ul.page-numbers li, .page-links li',
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'pagination_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'ul .page-numbers a, a.page-numbers, span.page-numbers, .page-links span, .page-links a > span',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'pagination_hover_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color: Hover', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-numbers a:hover, .page-numbers.current, .page-numbers.current:hover, .page-links span, .page-links a > span:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'pagination_hover_active',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color: Active', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-numbers.current, .page-numbers.current:hover',
				'alter' => 'color',
				'important' => true,
			),
		),
		array(
			'id' => 'pagination_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
			),
			'inline_css' => array(
				'target' => 'ul.page-numbers a,span.page-numbers,.page-links span,.page-links a>span,.bbp-pagination-links span.page-numbers, .bbp-pagination-links .page-numbers',
				'alter' => 'background',
			),
		),
		array(
			'id' => 'pagination_hover_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-numbers a:hover,.page-numbers.current,.page-numbers.current:hover,.page-links span,.page-links a > span:hover, .woocommerce nav.woocommerce-pagination ul li a:focus,.woocommerce nav.woocommerce-pagination ul li a:hover',
				'alter' => 'background',
			),
		),
		array(
			'id' => 'pagination_active_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background: Active', 'total' ),
			),
			'inline_css' => array(
				'target' => '.page-numbers.current, .page-numbers.current:hover',
				'alter' => 'background',
				'important' => true,
			),
		),
		array(
			'id' => 'loadmore_text',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Load More Text', 'total' ),
			),
		),
	),
);

// Next/Prev
$this->sections['wpex_next_prev'] = array(
	'title' => esc_html__( 'Next/Previous Links', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'next_prev_in_same_term',
			'default' => true,
			'control' => array(
				'type' => 'checkbox',
				'label' => esc_html__( 'From Same Category?', 'total' ),
			),
		),
		array(
			'id' => 'next_prev_reverse_order',
			'default' => false,
			'control' => array(
				'type' => 'checkbox',
				'label' => esc_html__( 'Reverse Order?', 'total' ),
			),
		),
		array(
			'id' => 'next_prev_link_bg_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
			),
			'inline_css' => array(
				'target' => '.post-pagination-wrap',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'next_prev_link_border_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.post-pagination-wrap',
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'next_prev_link_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => '.post-pagination-wrap',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'next_prev_link_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Link Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.post-pagination a',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'next_prev_link_font_size',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Font Size', 'total' ),
				'description' => esc_html__( 'Value in px or em.', 'total' ),
			),
			'inline_css' => array(
				'target' => '.post-pagination',
				'alter' => 'font-size',
				'sanitize' => 'font-size',
			),
		),
		array(
			'id' => 'next_prev_next_text',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Custom Next Text', 'total' ),
			),
		),
		array(
			'id' => 'next_prev_prev_text',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Custom Prev Text', 'total' ),
			),
		),
	),
);

// Theme Heading
$this->sections['wpex_theme_heading'] = array(
	'title' => esc_html__( 'Theme Heading', 'total' ),
	'panel' => 'wpex_general',
	'desc' => esc_html__( 'Heading used in various places such as the related and comments heading.', 'total' ),
	'settings' => array(
		array(
			'id' => 'theme_heading_style',
			'control' => array(
				'type' => 'select',
				'default' => '',
				'label' => esc_html__( 'Style', 'total' ),
				'choices' => wpex_get_theme_heading_styles(),
			),
		),
		array(
			'id' => 'theme_heading_tag',
			'default' => 'div',
			'control' => array(
				'label' => esc_html__( 'Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				),
			),
		),
	),
);

$inputs_target = array('.site-content input[type="date"],.site-content input[type="time"],.site-content input[type="datetime-local"],.site-content input[type="week"],.site-content input[type="month"],.site-content input[type="text"],.site-content input[type="email"],.site-content input[type="url"],.site-content input[type="password"],.site-content input[type="search"],.site-content input[type="tel"],.site-content input[type="number"],.site-content textarea' );
$inputs_target_focus = array('.site-content input[type="date"]:focus,.site-content input[type="time"]:focus,.site-content input[type="datetime-local"],.site-content input[type="week"],.site-content input[type="month"]:focus,.site-content input[type="text"]:focus,.site-content input[type="email"]:focus,.site-content input[type="url"]:focus,.site-content input[type="password"]:focus,.site-content input[type="search"]:focus,.site-content input[type="tel"]:focus,.site-content input[type="number"]:focus,.site-content textarea:focus' );

// Forms
$this->sections['wpex_general_forms'] = array(
	'title' => esc_html__( 'Forms', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'label_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Label Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'label,#comments #commentform label',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'forms_inputs_heading',
			'control' => array(
				'type' => 'wpex-heading',
				'label' => esc_html__( 'Inputs', 'total' ),
			),
		),
		array(
			'id' => 'input_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'input_border_radius',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Border Radius', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => array( 'border-radius', '-webkit-border-radius' ),
			),
		),
		array(
			'id' => 'input_font_size',
			'description' => esc_html__( 'Value in px or em.', 'total' ),
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Font-Size', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'font-size',
			),
		),
		array(
			'id' => 'input_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'input_background_focus',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Focus: Background', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target_focus,
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'input_border',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'input_border_focus',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Focus: Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target_focus,
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'input_border_width',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Border Width', 'total' ),
				'description' => $pixel_desc,
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'border-width',
			),
		),
		array(
			'id' => 'input_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target,
				'alter' => 'color',
			),
		),
		array(
			'id' => 'input_color_focus',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Focus: Color', 'total' ),
			),
			'inline_css' => array(
				'target' => $inputs_target_focus,
				'alter' => 'color',
			),
		),
	),
);

// Tables
$this->sections['wpex_general_tables'] = array(
	'title' => esc_html__( 'Tables', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'thead_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Table Header Background', 'total' ),
			),
			'inline_css' => array(
				'target' => 'thead',
				'alter' => 'background',
			),
		),
		array(
			'id' => 'thead_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Table Header Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'table thead, table thead th',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'tables_th_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Th Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'table th',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'tables_border_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Cells Border Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'table th, table td',
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'tables_cell_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Cell Padding', 'total' ),
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => 'table th, table td',
				'alter' => 'padding',
			),
		),
	),
);


// Links & Buttons
$this->sections['wpex_general_links_buttons'] = array(
	'title' => esc_html__( 'Links & Buttons', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'link_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Links Color', 'total' ),
			),
			'inline_css' => array(
				'target' => 'a, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover, .entry-title a:hover,.meta a:hover',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'link_color_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Links Color: Hover', 'total' ),
			),
			'inline_css' => array(
				'target' => 'a:hover',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'theme_button_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Theme Button Padding', 'total' ),
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => '.theme-button,input[type="submit"],button,.button,.added_to_cart',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'theme_button_border_radius',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Theme Button Border Radius', 'total' ),
			),
			'inline_css' => array(
				'target' => '.theme-button,input[type="submit"],button,#site-navigation .menu-button > a > span.link-inner,.button,.added_to_cart',
				'alter' => 'border-radius',
			),
		),
		array(
			'id' => 'theme_button_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Theme Button Background', 'total' ),
			),
			'inline_css' => array(
				'target' => '.theme-button,input[type="submit"],button,#site-navigation .menu-button > a > span.link-inner,.button,.added_to_cart',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'theme_button_hover_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Theme Button Background: Hover', 'total' ),
			),
			'inline_css' => array(
				'target' => '.theme-button:hover,input[type="submit"]:hover,button:hover,#site-navigation .menu-button > a:hover > span.link-inner,.button:hover,.added_to_cart:hover',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'theme_button_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Theme Button Color', 'total' ),
			),
			'inline_css' => array(
				'target' => '.theme-button,input[type="submit"],button,#site-navigation .menu-button > a > span.link-inner,.button,.added_to_cart',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'theme_button_hover_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Theme Button Color: Hover', 'total' ),
			),
			'inline_css' => array(
				'target' => '.theme-button:hover,input[type="submit"]:hover,button:hover,#site-navigation .menu-button > a:hover > span.link-inner,.button:hover,.added_to_cart:hover',
				'alter' => 'color',
			),
		),
	),
);

// Post Sliders
$this->sections['wpex_post_slider'] = array(
	'title'  => esc_html__( 'Post Gallery Slider', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'post_slider_animation',
			'default' => 'slide',
			'control' => array(
				'label' => esc_html__( 'Animation', 'total' ),
				'type' => 'select',
				'choices' => array(
					'slide' => esc_html__( 'Slide', 'total' ),
					'fade' => esc_html__( 'Fade','total' ),
				),
			),
		),
		array(
			'id' => 'post_slider_animation_speed',
			'default' => '600',
			'control' => array(
				'label' => esc_html__( 'Custom Animation Speed', 'total' ),
				'type' => 'textfield',
				'description' => esc_html__( 'Enter a value in milliseconds.', 'total' ),
			),
		),
		array(
			'id' => 'post_slider_autoplay',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Auto Play?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'post_slider_loop',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Loop?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'post_slider_thumbnails',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Thumbnails?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'post_slider_arrows',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Arrows?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'post_slider_arrows_on_hover',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Arrows on Hover?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'post_slider_dots',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Dots?', 'total' ),
				'type' => 'checkbox',
			),
		),
	),
);

// Lightbox
$this->sections['wpex_lightbox'] = array(
	'title' => esc_html__( 'Lightbox', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		/*array(
			'id' => 'lightbox_width',
			'default' => 1500,
			'control' => array(
				'label' => esc_html__( 'Image Max Width', 'total' ),
				'type' => 'number',
			),
		),
		array(
			'id' => 'lightbox_titles',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Titles', 'total' ),
				'type' => 'checkbox',
			),
		),
		*/
		array(
			'id' => 'lightbox_load_style_globally',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Load Scripts Globally?', 'total' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'By default the lightbox scripts will only load as needed by the theme. You can enable this option to load the scripts globally on the whole site if needed or you can use the [wpex_lightbox_scripts] shortcode anywhere to load the scripts as needed.', 'total' ),
			),
		),
		array(
			'id' => 'lightbox_auto',
			'control' => array(
				'label' => esc_html__( 'Enable Auto Lightbox?', 'total' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'Automatically add Lightbox to images inserted into the post editor.', 'total' ),
			),
		),
		array(
			'id' => 'lightbox_slideshow_autostart',
			'control' => array(
				'label' => esc_html__( 'Lightbox Gallery Slideshow Auto Start?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'lightbox_slideshow_speed',
			'default' => 3000,
			'control' => array(
				'label' => esc_html__( 'Gallery Slideshow Speed', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter a value in milliseconds.', 'total' ),
			),
		),
		array(
			'id' => 'lightbox_transition_effect',
			'default' => 'fade',
			'control' => array(
				'label' => esc_html__( 'Transition Effect', 'total' ),
				'type' => 'select',
				'choices' => array(
					'fade' => esc_html__( 'Fade', 'total' ),
					'slide' => esc_html__( 'Slide', 'total' ),
					'circular' => esc_html__( 'Circular', 'total' ),
					'tube' => esc_html__( 'Tube', 'total' ),
					'zoom-in-out' => esc_html__( 'Zoom-In-Out', 'total' ),
				),
			),
		),
		array(
			'id' => 'lightbox_transition_duration',
			'default' => 366,
			'control' => array(
				'label' => esc_html__( 'Duration in ms for transition animation', 'total' ),
				'type' => 'text',
			),
		),
		array(
			'id' => 'lightbox_thumbnails',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Enable Gallery Thumbnails Panel?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'lightbox_thumbnails_auto_start',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Auto Open Gallery Thumbnails Panel?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'lightbox_loop',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Enable Gallery Loop?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'lightbox_arrows',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Display Gallery Arrows?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'lightbox_fullscreen',
			'default' => false,
			'control' => array(
				'label' => esc_html__( 'Display Fullscreen Button?', 'total' ),
				'type' => 'checkbox',
			),
		),
	),
);

// Scroll to top
$this->sections['wpex_scroll_top'] = array(
	'title' => esc_html__( 'Scroll To Top', 'total' ),
	'panel' => 'wpex_general',
	'settings' => array(
		array(
			'id' => 'scroll_top',
			'default' => true,
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Enable Scroll Up Button?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'scroll_top_arrow',
			'default' => 'chevron-up',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'Arrow', 'total' ),
				'type' => 'select',
				'choices' => array(
					'chevron-up' => 'chevron-up',
					'caret-up' => 'caret-up',
					'angle-up' => 'angle-up',
					'angle-double-up' => 'angle-double-up',
					'long-arrow-up' => 'long-arrow-up',
					'arrow-circle-o-up' => 'arrow-circle-o-up',
					'arrow-up' => 'arrow-up',
					'caret-square-o-up' => 'caret-square-o-up',
					'level-up' => 'level-up',
					'sort-up' => 'sort-up',
					'toggle-up' => 'toggle-up',
				),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
		),
		array(
			'id' => 'scroll_top_size',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Button Size', 'total' ),
				'description' => $pixel_desc,
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'sanitize' => 'px',
				'alter' => array(
					'width',
					'height',
					'line-height',
				),
			),
		),
		array(
			'id' => 'scroll_top_icon_size',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Icon Size', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'font-size',
			),
		),
		array(
			'id' => 'scroll_top_border_radius',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Border Radius', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'border-radius',
			),
		),
		array(
			'id' => 'scroll_top_right_position',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Right Position', 'total' ),
				'description' => esc_html__( 'Default:', 'total' ) .' 40px',
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'right',
			),
		),
		array(
			'id' => 'scroll_top_left_position',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Left Position (for RTL)', 'total' ),
				'description' => esc_html__( 'Default:', 'total' ) .' 40px',
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'left',
			),
		),
		array(
			'id' => 'scroll_top_bottom_position',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Bottom Position', 'total' ),
				'description' => esc_html__( 'Default:', 'total' ) .' 40px',
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'bottom',
			),
		),
		array(
			'id' => 'scroll_top_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'scroll_top_color_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Color: Hover', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top:hover',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'scroll_top_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'scroll_top_bg_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			),
			'control_display' => array(
				'check' => 'scroll_top',
				'value' => 'true',
			),
			'inline_css' => array(
				'target' => '#site-scroll-top:hover',
				'alter' => 'background-color',
			),
		),
	),
);