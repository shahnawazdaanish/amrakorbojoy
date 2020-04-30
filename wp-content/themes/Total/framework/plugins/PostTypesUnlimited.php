<?php
/**
 * Adds custom options to the Post Types Unlimited Plugin meta options.
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 5.0
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostTypesUnlimited {

	/**
	 * Check if in admin
	 *
	 * @since 4.8.4
	 */
	public $is_admin = false;

	/**
	 * Post Types Variable
	 *
	 * @since 4.8.4
	 */
	public $types = array();

	/**
	 * Taxonomies Variable
	 *
	 * @since 4.8.4
	 */
	public $taxonomies = array();

	/**
	 * Main PostTypesUnlimited class constructor.
	 *
	 * @since 4.8.4
	 */
	public function __construct() {

		$this->is_admin = is_admin();

		if ( $this->is_admin ) {
			add_filter( 'ptu_metaboxes', array( $this, 'add_meta' ), 50 );
		}

		if ( ! $this->get_post_types() && ! $this->get_taxonomies() ) {
			return; // no need to do anything if we don't have any post types or taxonomies
		}

		/** Backend actions **/
		if ( $this->is_admin ) {

			add_filter( 'wpex_main_metaboxes_post_types', array( $this, 'wpex_main_metaboxes_post_types' ) );
			add_filter( 'wpex_image_sizes_tabs', array( $this, 'wpex_image_sizes_tabs' ) );
			add_filter( 'wpex_gallery_metabox_post_types', array( $this, 'wpex_gallery_metabox_post_types' ) );

		}

		/** Front-end actions (allowed in ajax) **/
		if ( ! $this->is_admin || wp_doing_ajax() ) {

			add_filter( 'wpex_post_layout_class', array( $this, 'wpex_post_layout_class' ) );
			add_filter( 'wpex_page_header_style', array( $this, 'wpex_page_header_style' ), 10 );
			add_filter( 'wpex_title', array( $this, 'wpex_title' ), 10, 2 );
			add_filter( 'wpex_page_header_title_args', array( $this, 'wpex_page_header_title_args' ), 10, 2 );
			add_filter( 'wpex_has_next_prev', array( $this, 'wpex_has_next_prev' ), 10, 2 );
			add_filter( 'wpex_breadcrumbs_trail', array( $this, 'wpex_breadcrumbs_trail' ) );
			add_filter( 'wpex_get_archive_grid_class', array( $this, 'wpex_get_archive_grid_class' ) );
			add_filter( 'wpex_get_grid_entry_columns', array( $this, 'wpex_get_grid_entry_columns' ) );
			add_filter( 'wpex_get_archive_grid_entry_class', array( $this, 'wpex_get_archive_grid_entry_class' ) );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_action( 'wpex_entry_blocks', array( $this, 'wpex_entry_blocks' ), 10, 2 );
			add_filter( 'wpex_meta_blocks', array( $this, 'wpex_meta_blocks' ), 10, 2 );
			add_filter( 'wpex_meta_categories_taxonomy', array( $this, 'wpex_meta_categories_taxonomy' ) );
			add_filter( 'wpex_single_blocks', array( $this, 'wpex_single_blocks' ), 10, 2 );
			add_filter( 'wpex_get_singular_template_id', array( $this, 'wpex_get_singular_template_id' ), 10, 2 );
			add_filter( 'wpex_has_term_description_above_loop', array( $this, 'wpex_has_term_description_above_loop' ) );
			add_filter( 'wpex_term_page_header_image_enabled', array( $this, 'wpex_term_page_header_image_enabled' ) );
			add_filter( 'wpex_get_post_type_cat_tax', array( $this, 'wpex_get_post_type_cat_tax' ), 10, 2 );

		}

		/** Front & Backend **/
		add_filter( 'wpex_image_sizes', array( $this, 'wpex_image_sizes' ), 100 );
		add_filter( 'wpex_register_sidebars_array', array( $this, 'wpex_register_sidebars_array' ) );
		add_filter( 'wpex_get_sidebar', array( $this, 'wpex_get_sidebar' ) );

	}

	/**
	 * Add new meta options.
	 *
	 * @since 4.8.4
	 */
	public function add_meta( $metaboxes ) {

		// Layout styles
		$layouts = wpex_get_post_layouts();

		// Pages select
		$pages_select = array(
			'' => esc_attr__( 'None', 'total' )
		);
		$get_pages = get_pages();
		if ( $get_pages ) {
			foreach ( $get_pages as $page ) {
				$pages_select[$page->ID] = $page->post_title;
			}
		}

		// Templates select
		$templates_select = array(
			'' => esc_attr__( 'None', 'total' )
		);

		if ( post_type_exists( 'templatera' ) ) {
			$templates = get_posts( array(
				'posts_per_page' => -1,
				'post_type'      => 'templatera',
			) );
			if ( $templates ) {
				foreach ( $templates as $template ) {
					$templates_select[$template->ID] = $template->post_title;
				}
			}
		}

		/*if ( post_type_exists( 'elementor_library' ) ) {
			$templates = get_posts( array(
				'posts_per_page' => -1,
				'post_type'      => 'elementor_library',
			) );
			if ( $templates ) {
				foreach ( $templates as $template ) {
					$templates_select[$template->ID] = 'Elementor - ' . $template->post_title;
				}
			}
		}*/

		// Tax select
		$tax_select = array(
			'' => esc_attr__( 'None', 'total' )
		);
		$get_taxes = get_taxonomies( array(
			'public' => true,
		), 'objects' );
		if ( $get_taxes ) {
			foreach ( $get_taxes as $k => $v ) {
				$tax_select[$k] = $k;
			}
		}

		// Header styles
		if ( function_exists( 'wpex_get_page_header_styles' ) ) {
			$page_header_styles = wpex_get_page_header_styles();
		} else {
			$page_header_styles = array();
		}

		/*** ADD meta options to the custom post type */
		$metaboxes[] = array(
			'id' => 'total_ptu',
			'title' => esc_attr__( 'Theme Settings', 'total' ),
			'screen' => array( 'ptu' ),
			'context' => 'normal',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => esc_attr__( 'Post Settings Metabox', 'total' ),
					'id' => 'total_ps_meta',
					'type' => 'checkbox',
					'desc' => esc_attr__( 'Enable the post settings metabox in the editor for this post type.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Image Sizes', 'total' ),
					'id' => 'total_image_sizes',
					'type' => 'checkbox',
					'desc' => esc_attr__( 'Enable image size settings for this post type.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Custom Sidebar', 'total' ),
					'id' => 'total_custom_sidebar',
					'type' => 'text',
					'desc' => esc_attr__( 'Enter a name to create a custom sidebar for the post type archive, single posts and attached taxonomies.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Post Gallery', 'total' ),
					'id' => 'total_post_gallery',
					'type' => 'checkbox',
					'desc' => esc_attr__( 'Enable the post gallery for this post type.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Main Page', 'total' ),
					'id' => 'total_main_page',
					'type' => 'select',
					'desc' => esc_attr__( 'Used for breadcrumbs.', 'total' ),
					'choices' => $pages_select,
				),
				array(
					'name' => esc_attr__( 'Main Taxonomy', 'total' ),
					'id' => 'total_main_taxonomy',
					'type' => 'select',
					'desc' => esc_attr__( 'Used for breadcrumbs, post meta categories and related items.', 'total' ),
					'choices' => $tax_select,
				),
				array(
					'name' => esc_attr__( 'Archive Page Header Title', 'total' ),
					'id' => 'total_archive_page_header_title',
					'type' => 'text',
					'desc' => esc_attr__( 'Custom title for the post type archive page header.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Archive Page Header Title Style', 'total' ),
					'id' => 'total_archive_page_header_title_style',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your page header title style.', 'total' ),
					'choices' => $page_header_styles,
				),
				array(
					'name' => esc_attr__( 'Archive Layout', 'total' ),
					'id' => 'total_archive_layout',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your custom layout.', 'total' ),
					'choices' => $layouts,
				),
				array(
					'name' => esc_attr__( 'Archive Grid Columns', 'total' ),
					'id' => 'total_archive_grid_columns',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred columns for your entries.', 'total' ),
					'choices' => wpex_grid_columns(),
				),
				array(
					'name' => esc_attr__( 'Archive Grid Style', 'total' ),
					'id' => 'total_archive_grid_style',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred grid style.', 'total' ),
					'choices' => array(
						'' => esc_attr__( 'Default', 'total' ),
						'masonry' => esc_attr__( 'Masonry', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Archive Grid Gap', 'total' ),
					'id' => 'total_archive_grid_gap',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred spacing between items in the grid.', 'total' ),
					'choices' => wpex_column_gaps(),
				),
				array(
					'name' => esc_attr__( 'Archive Posts Per Page', 'total' ),
					'id' => 'total_archive_posts_per_page',
					'type' => 'text',
					'desc' => esc_attr__( 'How many posts do you want to display before showing the post pagination? Enter -1 to display all of them without pagination.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Entry Blocks', 'total' ),
					'id' => 'total_entry_blocks',
					'type' => 'multi_select',
					'desc' => esc_attr__( 'Select the blocks you want to display for your post type entries.', 'total' ),
					'default' => array( 'media', 'title', 'meta', 'content', 'readmore' ),
					'choices' => array(
						'media'    => esc_attr__( 'Media (Thumbnail, Slider, Video)', 'total' ),
						'title'    => esc_attr__( 'Title', 'total' ),
						'meta'     => esc_attr__( 'Meta', 'total' ),
						'content'  => esc_attr__( 'Content', 'total' ),
						'readmore' => esc_attr__( 'Readmore', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Entry Meta', 'total' ),
					'id' => 'total_entry_meta_blocks',
					'type' => 'multi_select',
					'desc' => esc_attr__( 'Select the blocks you want to display for your post type entries.', 'total' ),
					'default' => array( 'date', 'author', 'categories', 'comments' ),
					'choices' => array(
						'date'       => esc_attr__( 'Date', 'total' ),
						'author'     => esc_attr__( 'Author', 'total' ),
						'categories' => esc_attr__( 'Categories', 'total' ),
						'comments'   => esc_attr__( 'Comments', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Single Post Dynamic Template', 'total' ),
					'id' => 'total_singular_template_id',
					'type' => 'select',
					'desc' => esc_attr__( 'Select a template to be used for your singular post design.', 'total' ),
					'choices' => $templates_select,
				),
				array(
					'name' => esc_attr__( 'Single Page Header Title Style', 'total' ),
					'id' => 'total_page_header_title_style',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your page header title style.', 'total' ),
					'choices' => $page_header_styles,
				),
				array(
					'name' => esc_attr__( 'Single Page Header Title', 'total' ),
					'id' => 'total_page_header_title',
					'type' => 'text',
					'desc' => esc_attr__( 'Use {{title}} to display the current post title.', 'total' ),
				),
				array(
					'name' => esc_attr__( 'Single Page Header Title tag', 'total' ),
					'id' => 'total_page_header_title_tag',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your html tag for the single page header title.', 'total' ),
					'choices' => array(
						''     => esc_html__( 'Default', 'total' ),
						'h1'   => 'h1',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
						'h6'   => 'h6',
						'div'  => 'div',
						'span' => 'span',
					),
				),
				array(
					'name' => esc_attr__( 'Single Layout', 'total' ),
					'id' => 'total_post_layout',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your custom layout.', 'total' ),
					'choices' => $layouts,
				),
				array(
					'name' => esc_html__( 'Single Blocks', 'total' ),
					'id' => 'total_single_blocks',
					'type' => 'multi_select',
					'default' => array( 'media', 'title', 'meta', 'post-series', 'content', 'page-links', 'share', 'author-bio', 'related', 'comments' ),
					'choices' => array(
						'media'       => esc_attr__( 'Media (Thumbnail, Slider, Video)', 'total' ),
						'title'       => esc_attr__( 'Title', 'total' ),
						'meta'        => esc_attr__( 'Meta', 'total' ),
						'post-series' => esc_attr__( 'Post Series (if enabled via child theme)', 'total' ),
						'content'     => esc_attr__( 'Content', 'total' ),
						'page-links'  => esc_attr__( 'Page Links', 'total' ),
						'share'       => esc_attr__( 'Social Share', 'total' ),
						'author-bio'  => esc_attr__( 'Author Bio', 'total' ),
						'related'     => esc_attr__( 'Related', 'total' ),
						'comments'    => esc_attr__( 'Comments', 'total' ),
					),
				),
				array(
					'name' => esc_html__( 'Single Meta', 'total' ),
					'id' => 'total_single_meta_blocks',
					'type' => 'multi_select',
					'default' => array( 'date', 'author', 'categories', 'comments' ),
					'choices' => array(
						'date'       => esc_attr__( 'Date', 'total' ),
						'author'     => esc_attr__( 'Author', 'total' ),
						'categories' => esc_attr__( 'Categories (Main Taxonomy)', 'total' ),
						'comments'   => esc_attr__( 'Comments', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Next/Prev', 'total' ),
					'id' => 'total_next_prev',
					'type' => 'checkbox',
					'default' => true,
					'desc' => esc_attr__( 'Enable the next and previous pagination on the singular post.', 'total' ),
				),
			)
		);


		/*** ADD meta options to the custom taxonomies */
		$metaboxes[] = array(
			'id' => 'total_ptu_tax',
			'title' => esc_attr__( 'Theme Settings', 'total' ),
			'screen' => array( 'ptu_tax' ),
			'context' => 'normal',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => esc_attr__( 'Layout', 'total' ),
					'id' => 'total_tax_layout',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your custom layout.', 'total' ),
					'choices' => $layouts,
				),
				array(
					'name' => esc_attr__( 'Archive Grid Columns', 'total' ),
					'id' => 'total_tax_grid_columns',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred columns for your entries.', 'total' ),
					'choices' => wpex_grid_columns(),
				),
				array(
					'name' => esc_attr__( 'Archive Grid Style', 'total' ),
					'id' => 'total_tax_grid_style',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred grid style.', 'total' ),
					'choices' => array(
						'' => esc_attr__( 'Default', 'total' ),
						'masonry' => esc_attr__( 'Masonry', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Archive Grid Gap', 'total' ),
					'id' => 'total_tax_grid_gap',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your preferred spacing between items in the grid.', 'total' ),
					'choices' => wpex_column_gaps(),
				),
				array(
					'name' => esc_attr__( 'Description Position', 'total' ),
					'id' => 'total_tax_term_description_position',
					'type' => 'select',
					'desc' => esc_attr__( 'Select your position for your term descriptions.', 'total' ),
					'choices' => array(
						'subheading' => esc_attr__( 'As Subheading', 'total' ),
						'above_loop' => esc_attr__( 'Before Your Posts', 'total' ),
					),
				),
				array(
					'name' => esc_attr__( 'Page Header Thumbnail', 'total' ),
					'id' => 'total_tax_term_page_header_image_enabled',
					'type' => 'checkbox',
					'default' => true,
					'desc' => esc_attr__( 'Display your term thumbnail as the page header background by default.', 'total' ),
				),
			)
		);

		return $metaboxes;

	}

	/**
	 * Get post types and store in class variable.
	 *
	 * @since 4.8.4
	 */
	public function get_post_types() {
		if ( $this->types ) {
			return $this->types;
		}
		$get_types = get_posts( array(
			'numberposts' 	   => -1,
			'post_type' 	   => 'ptu',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'fields'           => 'ids',
		) );
		if ( $get_types ) {
			foreach( $get_types as $id ) {
				$name = get_post_meta( $id, '_ptu_name', true );
				if ( $name ) {
					$this->types[ $name ] = $id;
				}
			}
		}
		return $this->types;
	}

	/**
	 * Get taxonomies and store in class variable.
	 *
	 * @since 4.8.4
	 */
	public function get_taxonomies() {
		if ( $this->taxonomies ) {
			return $this->taxonomies;
		}
		$get_taxes = get_posts( array(
			'numberposts' 	   => -1,
			'post_type' 	   => 'ptu_tax',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'fields'           => 'ids',
		) );
		if ( $get_taxes ) {
			foreach( $get_taxes as $id ) {
				$name = get_post_meta( $id, '_ptu_name', true );
				if ( $name ) {
					$this->taxonomies[ $name ] = $id;
				}
			}
		}
		return $this->taxonomies;
	}

	/**
	 * Return post typemeta value.
	 *
	 * @since 4.8.4
	 */
	public function get_setting_value( $post_type, $setting_id ) {
		$types = $this->get_post_types();
		if ( $types && ! empty( $types[$post_type] ) ) {
			return get_post_meta( $types[$post_type], $setting_id, true );
		}
	}

	/**
	 * Return meta value.
	 *
	 * @since 4.8.4
	 */
	public function get_tax_setting_value( $tax, $setting_id ) {
		$taxes = $this->get_taxonomies();
		if ( $taxes && ! empty( $taxes[$tax] ) ) {
			return get_post_meta( $taxes[$tax], $setting_id, true );
		}
	}

	/**
	 * Enable metabox for types.
	 *
	 * @since 4.8.4
	 */
	public function wpex_main_metaboxes_post_types( $types ) {
		$get_types = $this->get_post_types();
		if ( $get_types ) {
			foreach ( $get_types as $type => $id ) {
				if ( get_post_meta( $id, '_ptu_total_ps_meta', true ) ) {
					$types[$type] = $type;
				}
			}
		}
		return $types;
	}

	/**
	 * Enable image sizes.
	 *
	 * @since 4.8.4
	 */
	public function wpex_image_sizes_tabs( $tabs ) {
		$types = $this->get_post_types();
		if ( $types ) {
			foreach ( $types as $type => $id ) {
				if ( get_post_meta( $id, '_ptu_total_image_sizes', true ) ) {
					$postType = get_post_type_object( $type );
					if ( $postType ) {
						$tabs[$type] = $postType->labels->singular_name;
					}
				}
			}
		}
		return $tabs;
	}

	/**
	 * Add image size options.
	 *
	 * @since 4.8.4
	 */
	public function wpex_image_sizes( $sizes ) {
		$types = $this->get_post_types();
		if ( $types ) {
			foreach ( $types as $type => $id ) {
				if ( get_post_meta( $id, '_ptu_total_image_sizes', true ) ) {
					$sizes[ $type . '_archive' ] = array(
						'label'   => esc_html__( 'Archive', 'total' ),
						'width'   => $type . '_archive_image_width',
						'height'  => $type . '_archive_image_height',
						'crop'    => $type . '_archive_image_crop',
						'section' => $type,
					);
					$sizes[ $type . '_single' ] = array(
						'label'   => esc_html__( 'Post', 'total' ),
						'width'   => $type . '_post_image_width',
						'height'  => $type . '_post_image_height',
						'crop'    => $type . '_post_image_crop',
						'section' => $type,
					);
					$sizes[ $type . '_single_related' ] = array(
						'label'   => esc_html__( 'Post Related Items', 'total' ),
						'width'   => $type . '_single_related_image_width',
						'height'  => $type . '_single_related_image_height',
						'crop'    => $type . '_single_related_image_crop',
						'section' => $type,
					);
				}
			}
		}
		return $sizes;
	}

	/**
	 * Register sidebars.
	 *
	 * @since 4.8.4
	 */
	public function wpex_register_sidebars_array( $sidebars ) {
		$types = $this->get_post_types();
		if ( $types ) {
			foreach ( $types as $type => $id ) {
				$sidebar = get_post_meta( $id, '_ptu_total_custom_sidebar', true );
				if ( $sidebar ) {
					$id = wp_strip_all_tags( $sidebar );
					$id = str_replace( ' ', '_', $sidebar );
					$id = strtolower( $sidebar );
					$sidebars[$id] = $sidebar;
				}
			}
		}
		return $sidebars;
	}

	/**
	 * Display Custom Sidebar.
	 *
	 * @since 4.9.3
	 */
	public function wpex_get_sidebar( $sidebar ) {
		$types = $this->get_post_types();
		if ( $types ) {
			foreach ( $types as $type => $id ) {
				if ( is_singular( $type )
					|| is_post_type_archive( $type )
					|| ( is_tax() && $type = get_post_type() )
				) {
					$custom_sidebar = get_post_meta( $id, '_ptu_total_custom_sidebar', true );
					if ( $custom_sidebar ) {
						$sidebar = $custom_sidebar;
					}
				}
			}
		}
		return $sidebar;
	}

	/**
	 * Enable gallery metabox.
	 *
	 * @since 4.8.4
	 */
	public function wpex_gallery_metabox_post_types( $types ) {
		$get_types = $this->get_post_types();
		if ( $get_types ) {
			foreach ( $get_types as $type => $id ) {
				if ( get_post_meta( $id, '_ptu_total_post_gallery', true ) ) {
					$types[$id] = $type;
				}
			}
		}
		return $types;
	}

	/**
	 * Alter layouts.
	 *
	 * @since 4.8.4
	 */
	public function wpex_post_layout_class( $layout ) {
		if ( is_singular() ) {
			$custom_layout = $this->get_setting_value( get_post_type(), '_ptu_total_post_layout' );
			if ( $custom_layout ) {
				$layout = $custom_layout;
			}
		} elseif ( is_post_type_archive() ) {
			$custom_layout = $this->get_setting_value( get_post_type(), '_ptu_total_archive_layout' );
			if ( $custom_layout ) {
				$layout = $custom_layout;
			}
		} elseif ( is_tax() ) {
			$custom_layout = $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_layout' );
			if ( $custom_layout ) {
				$layout = $custom_layout;
			}
		}
		return $layout;
	}

	/**
	 * Filter page header style.
	 *
	 * @since 4.9.9
	 */
	public function wpex_page_header_style( $style ) {
		if ( is_singular() ) {
			if ( $custom_style = $this->get_setting_value( get_post_type(), '_ptu_total_page_header_title_style' ) ) {
				$style = $custom_style;
			}
		} elseif ( is_post_type_archive() ) {
			if ( $custom_style = $this->get_setting_value( get_post_type(), '_ptu_total_archive_page_header_title_style' ) ) {
				$style = $custom_style;
			}
		}
		return $style;
	}

	/**
	 * Filter The Page Header Title.
	 *
	 * @since 4.8.4
	 */
	public function wpex_title( $title ) {
		if ( is_singular() ) {
			if ( $custom_title = $this->get_setting_value( get_post_type(), '_ptu_total_page_header_title' ) ) {
				if ( '{{title}}' == $custom_title ) {
					$custom_title = get_the_title();
				}
				$title = $custom_title;
			}
		} elseif ( is_post_type_archive() ) {
			if ( $custom_title = $this->get_setting_value( get_post_type(), '_ptu_total_archive_page_header_title' ) ) {
				$title = $custom_title;
			}
		}
		return $title;
	}

	/**
	 *  Filter The Page Header Title Attributes.
	 *
	 * @since 4.8.4
	 */
	public function wpex_page_header_title_args( $args ) {
		if ( is_singular() ) {
			if ( $custom_tag = $this->get_setting_value( get_post_type(), '_ptu_total_page_header_title_tag' ) ) {
				$args['html_tag'] = $custom_tag;
			}
		}
		return $args;
	}

	/**
	 * Next & Previous Pagination.
	 *
	 * @since 4.8.4
	 */
	public function wpex_has_next_prev( $bool ) {
		$check = $this->get_setting_value( get_post_type(), '_ptu_total_next_prev' );
		if ( isset( $check ) ) {
			return wp_validate_boolean( $check );
		}
		return $bool;
	}

	/**
	 * Filter breadcrumbs output.
	 *
	 * @since 4.8.4
	 */
	public function wpex_breadcrumbs_trail( $trail ) {
		if ( is_singular() ) {
			$types = $this->get_post_types();
			if ( $types ) {
				$current_type = get_post_type();
				if ( $current_type && array_key_exists( $current_type, $types ) ) {
					// Add main page
					$main_page = get_post_meta( $types[$current_type], '_ptu_total_main_page', true ) ;
					if ( $main_page && get_post_status( $main_page ) ) {
						$trail['post_type_archive'] = \WPEX_Breadcrumbs::get_crumb_html( get_the_title( $main_page ), get_permalink( $main_page ) );
					}
					// Add category
					if ( empty( $trail[ 'categories' ] ) ) {
						$main_tax = get_post_meta( $types[$current_type], '_ptu_total_main_taxonomy', true ) ;
						if ( $main_tax ) {
							$terms = \WPEX_Breadcrumbs::get_post_terms( $main_tax );
							if ( $terms ) {
								$trail[ 'categories' ] = '<span class="trail-categories">' . $terms . '</span>';
							}
						}
					}
				}
			}
		}
		return $trail;
	}

	/**
	 * Filter grid archive classes.
	 *
	 * @since 4.9
	 */
	public function wpex_get_archive_grid_class( $classes ) {
		if ( is_post_type_archive() ) {
			if ( 'masonry' == $this->get_setting_value( get_post_type(), '_ptu_total_archive_grid_style' ) ) {
				$classes[] = 'wpex-masonry-grid';
				wpex_enqueue_isotope_scripts();
			}
			if ( $gap = $this->get_setting_value( get_post_type(), '_ptu_total_archive_grid_gap' ) ) {
				$classes[] = 'gap-' . absint( $gap );
			}
		} elseif ( is_tax() ) {
			if ( 'masonry' == $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_grid_style' ) ) {
				$classes[] = 'wpex-masonry-grid';
				wpex_enqueue_isotope_scripts();
			}
			if ( $gap = $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_grid_gap' ) ) {
				$classes[] = 'gap-' . absint( $gap );
			}
		}
		return $classes;
	}

	/**
	 * Filter grid entry columns.
	 *
	 * @since 4.8.4
	 */
	public function wpex_get_grid_entry_columns( $columns ) {
		if ( is_post_type_archive() ) {
			if ( $value = $this->get_setting_value( get_post_type(), '_ptu_total_archive_grid_columns' ) ) {
				$columns = $value;
			}
		} elseif ( is_tax() ) {
			if ( $value = $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_grid_columns' ) ) {
				$columns = $value;
			}
		}
		return $columns;
	}

	/**
	 * Filter grid entry classes.
	 *
	 * @since 4.9
	 */
	public function wpex_get_archive_grid_entry_class( $classes ) {
		if ( is_post_type_archive() ) {
			if ( 'masonry' == $this->get_setting_value( get_post_type(), '_ptu_total_archive_grid_style' ) ) {
				$classes[] = 'isotope-entry';
			}
		} elseif ( is_tax() ) {
			if ( 'masonry' == $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_grid_style' ) ) {
				$classes[] = 'isotope-entry';
			}
		}
		return $classes;
	}

	/**
	 * Filter current query.
	 *
	 * @since 4.8.4
	 */
	public function pre_get_posts( $query ) {

		if ( is_admin() || ! is_object( $query ) || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive() ) {
			$custom_ppp = $this->get_setting_value( $query->query['post_type'], '_ptu_total_archive_posts_per_page' );
			if ( $custom_ppp ) {
				$query->set( 'posts_per_page', $custom_ppp );
			}
		}

	}

	/**
	 * Filter the post type entry blocks.
	 *
	 * @since 4.8.4
	 */
	public function wpex_entry_blocks( $blocks, $type ) {
		if ( $custom_blocks = $this->get_setting_value( $type, '_ptu_total_entry_blocks' ) ) {
			$blocks = $custom_blocks;
		}
		return $blocks;
	}

	/**
	 * Filter the post type entry meta blocks
	 *
	 * @since 4.8.4
	 */
	public function wpex_meta_blocks( $blocks, $type ) {
		if ( is_singular() && is_main_query() ) {
			$meta_key = '_ptu_total_single_meta_blocks';
		} else {
			$meta_key = '_ptu_total_entry_meta_blocks';
		}
		if ( $custom_blocks = $this->get_setting_value( $type, $meta_key ) ) {
			$blocks = $custom_blocks;
		}
		return $blocks;
	}

	/**
	 * Filter categories meta taxonomy.
	 *
	 * @since 4.8.4
	 */
	public function wpex_meta_categories_taxonomy( $taxonomy ) {
		if ( $value = $this->get_setting_value( get_post_type(), '_ptu_total_main_taxonomy' ) ) {
			$taxonomy = $value;
		}
		return $taxonomy;
	}

	/**
	 * Filter the post type single blocks.
	 *
	 * @since 4.8.4
	 */
	public function wpex_single_blocks( $blocks, $type ) {
		if ( $custom_blocks = $this->get_setting_value( $type, '_ptu_total_single_blocks' ) ) {
			$blocks = $custom_blocks;
		}
		return $blocks;
	}

	/**
	 * Set correct singular page template.
	 *
	 * @since 4.8.4
	 */
	public function wpex_get_singular_template_id( $template, $type ) {
		if ( $custom_template = $this->get_setting_value( $type, '_ptu_total_singular_template_id' ) ) {
			$template = $custom_template;
		}
		return $template;
	}

	/**
	 * Check if term description should be above the loop.
	 *
	 * @since 4.8.4
	 */
	public function wpex_has_term_description_above_loop( $bool ) {
		if ( 'above_loop' == $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_term_description_position' ) ) {
			$bool = true;
		}
		return $bool;
	}

	/**
	 * Check if page header image is enabled/disabled by default.
	 *
	 * @since 4.8.4
	 */
	public function wpex_term_page_header_image_enabled( $bool ) {
		if ( is_tax() ) {
			$check = $this->get_tax_setting_value( get_query_var( 'taxonomy' ), '_ptu_total_tax_term_page_header_image_enabled' );
			if ( isset( $check ) ) {
				$bool = wp_validate_boolean( $check );
			}
		}
		return $bool;
	}

	/**
	 * Set correct main taxonomy for custom post type.
	 *
	 * @since 4.8.4
	 */
	public function wpex_get_post_type_cat_tax( $taxonomy, $post_type ) {
		$get_types = $this->get_post_types();
		if ( $get_types ) {
			foreach ( $get_types as $type => $id ) {
				if ( $post_type == $type ) {
					$taxonomy = get_post_meta( $id, '_ptu_total_main_taxonomy', true );
				}
			}
		}
		return $taxonomy;
	}

}
new PostTypesUnlimited();