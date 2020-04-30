<?php
/**
 * Testimonials Customizer Options
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.9.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Archives
$this->sections['wpex_testimonials_archives'] = array(
	'title' => esc_html__( 'Archives & Entries', 'total' ),
	'panel' => 'wpex_testimonials',
	'desc' => esc_html__( 'The following options are for the post type category and tag archives.', 'total' ),
	'settings' => array(
		array(
			'id' => 'testimonials_archive_layout',
			'default' => 'full-width',
			'control' => array(
				'label' => esc_html__( 'Page Layout', 'total' ),
				'type' => 'select',
				'choices' => $post_layouts,
			),
		),
		array(
			'id' => 'testimonials_entry_columns',
			'default' => '4',
			'control' => array(
				'label' => esc_html__( 'Columns', 'total' ),
				'type' => 'wpex-columns',
			),
		),
		array(
			'id' => 'testimonials_archive_grid_style',
			'default' => 'fit-rows',
			'control' => array(
				'label' => esc_html__( 'Grid Style', 'total' ),
				'type' => 'select',
				'choices'   => array(
					'fit-rows' => esc_html__( 'Fit Rows','total' ),
					'masonry' => esc_html__( 'Masonry','total' ),
				),
			),
		),
		array(
			'id' => 'testimonials_archive_grid_gap',
			'control' => array(
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => wpex_column_gaps(),
			),
		),
		array(
			'id' => 'testimonials_archive_posts_per_page',
			'default' => '12',
			'control' => array(
				'label' => esc_html__( 'Posts Per Page', 'total' ),
				'type' => 'number',
			),
		),
		array(
			'id' => 'testimonial_entry_title',
			'control' => array(
				'label' => esc_html__( 'Display Entry Title?', 'total' ),
				'type' => 'checkbox',
			),
		),
		array(
			'id' => 'testimonials_entry_img_size',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => esc_html__( 'Entry Image Size', 'total' ),
				'desc' => esc_html__( 'Default size is 45px.', 'total' ),
			),
			'inline_css' => array(
				'target' => '.testimonial-entry-thumb.default-dims img',
				'alter' => array( 'width', 'height' ),
				'sanitize' => 'px',
			),
		),
		array(
			'id' => 'testimonial_entry_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Entry Background', 'total' ),
			),
			'inline_css' => array(
				'target' => '.testimonial-entry-content',
				'alter' => 'background',
			),
		),
		array(
			'id' => 'testimonial_entry_pointer_bg',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Entry Pointer Background', 'total' ),
			),
			'inline_css' => array(
				'target' => '.testimonial-caret',
				'alter' => 'border-top-color',
			),
		),
		array(
			'id' => 'testimonial_entry_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => esc_html__( 'Entry Color', 'total' ),
			),
			'inline_css' => array(
				'target' => array(
					'.testimonial-entry-content',
					'.testimonial-entry-content a',
				),
				'alter' => 'color',
			),
		),
	)
);

// Single
$this->sections['wpex_testimonials_single'] = array(
	'title' => esc_html__( 'Single Post', 'total' ),
	'panel' => 'wpex_testimonials',
	'settings' => array(
		array(
			'id' => 'testimonials_singular_page_title',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Display Page Header Title?', 'total' ),
				'type' => 'checkbox',
				'active_callback' => 'wpex_cac_has_page_header',
			),
		),
		array(
			'id' => 'testimonials_single_layout',
			'control' => array(
				'label' => esc_html__( 'Single Layout', 'total' ),
				'type' => 'select',
				'choices' => $post_layouts,
			),
		),
		array(
			'id' => 'testimonials_singular_template',
			'default' => '',
			'control' => array(
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'wpex-dropdown-templates',
				'desc' => $template_desc,
			),
		),
		array(
			'id' => 'testimonial_post_style',
			'default' => 'blockquote',
			'control' => array(
				'label' => esc_html__( 'Single Style', 'total' ),
				'type' => 'select',
				'choices' => array(
					'blockquote' => esc_html__( 'Testimonial', 'total' ),
					'standard' => esc_html__( 'Standard', 'total' ),
				),
				'active_callback' => 'wpex_cac_testimonials_single_hasnt_custom_template',
			),
		),
		array(
			'id' => 'testimonials_comments',
			'control' => array(
				'label' => esc_html__( 'Enable Comments?', 'total' ),
				'type' => 'checkbox',
				'active_callback' => 'wpex_cac_testimonials_single_hasnt_custom_template',
			),
		),
		array(
			'id' => 'testimonials_next_prev',
			'default' => 1,
			'control' => array(
				'label' => esc_html__( 'Display Next/Previous Links?', 'total' ),
				'type' => 'checkbox',
			),
		),
	)
);