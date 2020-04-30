<?php
/**
 * Portfolio Post Type
 *
 * @package Total Theme Core
 * @subpackage Post Types
 * @version 1.0
 */

namespace TotalThemeCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Testimonials {

	/**
	 * Main Constructor.
	 */
	public function __construct() {

		// Adds the testimonials post type
		add_action( 'init', array( $this, 'register_post_type' ), 0 );

		// Adds the testimonials taxonomies
		if ( is_mod_enabled( get_theme_mod( 'testimonials_categories', true ) ) ) {
			add_action( 'init', array( $this, 'register_categories' ), 0 );
		}

		// Register testimonials sidebar
		if ( get_theme_mod( 'testimonials_custom_sidebar', true ) ) {
			add_filter( 'wpex_register_sidebars_array', array( $this, 'register_sidebar' ), 10 );
		}

		// Add image sizes
		add_filter( 'wpex_image_sizes', array( $this, 'add_image_sizes' ) );

		// Add testimonial VC modules
		add_filter( 'vcex_builder_modules', array( $this, 'vc_modules' ) );

		/*-------------------------------------------------------------------------------*/
		/* -  Admin only actions/filters
		/*-------------------------------------------------------------------------------*/
		if ( is_admin() ) {

			// Adds columns in the admin view for taxonomies
			add_filter( 'manage_edit-testimonials_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_testimonials_posts_custom_column', array( $this, 'column_display' ), 10, 2 );

			// Allows filtering of posts by taxonomy in the admin view
			add_action( 'restrict_manage_posts', array( $this, 'tax_filters' ) );

			// Add new image sizes tab
			add_filter( 'wpex_image_sizes_tabs', array( $this, 'image_sizes_tabs' ), 10 );

			// Add meta settings
			add_filter( 'wpex_metabox_array', array( $this, 'add_meta' ), 5, 2 );

		}

		/*-------------------------------------------------------------------------------*/
		/* -  Front-End only actions/filters
		/*-------------------------------------------------------------------------------*/
		else {

			// Display testimonials sidebar for testimonials
			if ( get_theme_mod( 'testimonials_custom_sidebar', true ) ) {
				add_filter( 'wpex_get_sidebar', array( $this, 'display_sidebar' ), 10 );
			}

			// Alter the default page title
			add_action( 'wpex_page_header_title_args', array( $this, 'alter_title' ), 10 );

			// Alter the post layouts for testimonials posts and archives
			add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ), 10 );

			// Posts per page
			add_action( 'pre_get_posts', array( $this, 'posts_per_page' ) );

			// Single next/prev visibility
			add_filter( 'wpex_has_next_prev', array( $this, 'next_prev' ), 10, 2 );

			// Alter previous post link title
			add_filter( 'wpex_prev_post_link_title', array( $this, 'prev_post_link_title' ), 10, 2 );

			// Alter next post link title
			add_filter( 'wpex_next_post_link_title', array( $this, 'next_post_link_title' ), 10, 2 );

		}

	} // End construct

	/*-------------------------------------------------------------------------------*/
	/* -  Start Class Functions
	/*-------------------------------------------------------------------------------*/

	/**
	 * Run on activation
	 */
	public function on_activation() {
		$this->register_post_type();
		if ( is_mod_enabled( get_theme_mod( 'testimonials_categories', true ) ) ) {
			$this->register_categories();
		}
	}

	/**
	 * Return correct testimonials name.
	 */
	public function testimonials_name() {
		if ( function_exists( 'wpex_get_testimonials_name' ) ) {
			return wpex_get_testimonials_name();
		} else {
			return get_theme_mod( 'testimonials_labels', esc_html__( 'Testimonials', 'total-theme-core' ) );
		}
	}

	/**
	 * Return correct testimonials singular name.
	 */
	public function testimonials_singular_name() {
		if ( function_exists( 'wpex_get_testimonials_singular_name' ) ) {
			return wpex_get_testimonials_singular_name();
		} else {
			return get_theme_mod( 'testimonials_singular_name', esc_html__( 'Testimonials', 'total-theme-core' ) );
		}
	}

	/**
	 * Return correct testimonials icon.
	 */
	public function testimonials_menu_icon() {
		if ( function_exists( 'wpex_get_testimonials_menu_icon' ) ) {
			return wpex_get_testimonials_menu_icon();
		} else {
			return get_theme_mod( 'testimonials_admin_icon', 'groups' );
		}
	}

	/**
	 * Register post type
	 */
	public function register_post_type() {

		// Get values and sanitize
		$name          = $this->testimonials_name();
		$singular_name = $this->testimonials_singular_name();
		$has_archive   = get_theme_mod( 'testimonials_has_archive', false );

		$default_slug  = $has_archive ? 'testimonials' : 'testimonial';
		$slug          = get_theme_mod( 'testimonials_slug' );
		$slug          = $slug ? esc_html( $slug ) : $default_slug;

		$menu_icon     = $this->testimonials_menu_icon();

		// Register the post type
		register_post_type( 'testimonials', apply_filters( 'wpex_testimonials_args', array(
			'labels' => array(
				'name'               => $name,
				'singular_name'      => $singular_name,
				'add_new'            => esc_html__( 'Add New', 'total-theme-core' ),
				'add_new_item'       => esc_html__( 'Add New Item', 'total-theme-core' ),
				'edit_item'          => esc_html__( 'Edit Item', 'total-theme-core' ),
				'new_item'           => esc_html__( 'Add New Testimonials Item', 'total-theme-core' ),
				'view_item'          => esc_html__( 'View Item', 'total-theme-core' ),
				'search_items'       => esc_html__( 'Search Items', 'total-theme-core' ),
				'not_found'          => esc_html__( 'No Items Found', 'total-theme-core' ),
				'not_found_in_trash' => esc_html__( 'No Items Found In Trash', 'total-theme-core' )
			),
			'public'          => true,
			'capability_type' => 'post',
			'has_archive'     => $has_archive ? true : false,
			'menu_icon'       => 'dashicons-'. $menu_icon,
			'menu_position'   => 20,
			'rewrite'         => array(
				'slug'        => $slug,
				'with_front'  => false
			),
			'supports'        => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'comments',
				'custom-fields',
				'revisions',
				'author',
				'page-attributes',
			),
		) ) );

	}

	/**
	 * Register Testimonials category
	 */
	public function register_categories() {

		// Define and sanitize options
		$name = get_theme_mod( 'testimonials_cat_labels');
		$name = $name ? esc_html( $name ) : esc_html__( 'Testimonials Categories', 'total-theme-core' );
		$slug = get_theme_mod( 'testimonials_cat_slug' );
		$slug = $slug ? esc_html( $slug ) : 'testimonials-category';

		// Define args and apply filters
		$args = apply_filters( 'wpex_taxonomy_testimonials_category_args', array(
			'labels' => array(
				'name'                       => $name,
				'singular_name'              => $name,
				'menu_name'                  => $name,
				'search_items'               => esc_html__( 'Search', 'total-theme-core' ),
				'popular_items'              => esc_html__( 'Popular', 'total-theme-core' ),
				'all_items'                  => esc_html__( 'All', 'total-theme-core' ),
				'parent_item'                => esc_html__( 'Parent', 'total-theme-core' ),
				'parent_item_colon'          => esc_html__( 'Parent', 'total-theme-core' ),
				'edit_item'                  => esc_html__( 'Edit', 'total-theme-core' ),
				'update_item'                => esc_html__( 'Update', 'total-theme-core' ),
				'add_new_item'               => esc_html__( 'Add New', 'total-theme-core' ),
				'new_item_name'              => esc_html__( 'New', 'total-theme-core' ),
				'separate_items_with_commas' => esc_html__( 'Separate with commas', 'total-theme-core' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove', 'total-theme-core' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'total-theme-core' ),
			),
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'          => $slug,
				'with_front'    => false
			),
		) );

		// Register the testimonials category taxonomy
		register_taxonomy( 'testimonials_category', array( 'testimonials' ), $args );

	}

	/**
	 * Adds columns to the WP dashboard edit screen
	 */
	public function edit_columns( $columns ) {
		if ( taxonomy_exists( 'testimonials_category' ) ) {
			$columns['testimonials_category'] = esc_html__( 'Category', 'total-theme-core' );
		}
		return $columns;
	}

	/**
	 * Adds columns to the WP dashboard edit screen
	 */
	public function column_display( $column, $post_id ) {
		switch ( $column ) :
			case 'testimonials_category':
				if ( $category_list = get_the_term_list( $post_id, 'testimonials_category', '', ', ', '' ) ) {
					echo $category_list;
				} else {
					echo '&mdash;';
				}
			break;
		endswitch;
	}

	/**
	 * Adds taxonomy filters to the testimonials admin page
	 */
	public function tax_filters() {
		global $typenow;
		if ( 'testimonials' == $typenow && taxonomy_exists( 'testimonials_category' ) ) {
			$current_tax_slug   = isset( $_GET['testimonials_category'] ) ? esc_html( $_GET['testimonials_category'] ) : false;
			$tax_obj            = get_taxonomy( 'testimonials_category' );
			$tax_name           = $tax_obj->labels->name;
			$terms              = get_terms( 'testimonials_category' );
			if ( count( $terms ) > 0 ) {
				echo '<select name="testimonials_category" id="testimonials_category" class="postform">';
				echo '<option value="">'. $tax_name . '</option>';
				foreach ( $terms as $term ) {
					echo '<option value=' . $term->slug, $current_tax_slug == $term->slug ? ' selected="selected"' : '', '>' . $term->name .' (' . $term->count .')</option>';
				}
				echo "</select>";
			}
		}
	}

	/**
	 * Registers a new custom testimonials sidebar
	 */
	public function register_sidebar( $sidebars ) {
		$obj            = get_post_type_object( 'testimonials' );
		$post_type_name = $obj->labels->name;
		$sidebars['testimonials_sidebar'] = $post_type_name . ' ' . esc_html__( 'Sidebar', 'total-theme-core' );
		return $sidebars;
	}

	/**
	 * Alter main sidebar to display testimonials sidebar
	 */
	public function display_sidebar( $sidebar ) {
		if ( is_singular( 'testimonials') || wpex_is_testimonials_tax() || is_post_type_archive( 'testimonials' ) ) {
			$sidebar = 'testimonials_sidebar';
		}
		return $sidebar;
	}

	/**
	 * Alters the default page title
	 */
	public function alter_title( $args ) {
		if ( is_singular( 'testimonials' ) ) {
			if ( ! wpex_get_mod( 'testimonials_labels' )
				&& $author = get_post_meta( get_the_ID(), 'wpex_testimonial_author', true )
			) {
				$title = sprintf( esc_html__( 'Testimonial by: %s', 'total-theme-core' ), $author );
			} else {
				$title = single_post_title( '', false );
			}
			$args['string']   = $title;
			$args['html_tag'] = 'h1';
		}
		return $args;
	}

	/**
	 * Alter the post layouts for testimonials posts and archives
	 */
	public function layouts( $class ) {
		if ( is_singular( 'testimonials' ) ) {
			$class = wpex_get_mod( 'testimonials_single_layout' );
		} elseif ( wpex_is_testimonials_tax() || is_post_type_archive( 'testimonials' ) ) {
			$class = wpex_get_mod( 'testimonials_archive_layout', 'full-width' );
		}
		return $class;
	}

	/**
	 * Archive posts per page
	 */
	public function posts_per_page( $query ) {
		if ( ! defined( 'TOTAL_THEME_ACTIVE' ) || is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( wpex_is_testimonials_tax() || is_post_type_archive( 'testimonials' ) ) {
			$query->set( 'posts_per_page', wpex_get_mod( 'testimonials_archive_posts_per_page', '12' ) );
		}
	}

	/**
	 * Adds a "testimonials" tab to the image sizes admin panel
	 */
	public function image_sizes_tabs( $array ) {
		$array['testimonials'] = wpex_get_testimonials_name();
		return $array;
	}

	/**
	 * Adds image sizes for the testimonials to the image sizes panel
	 */
	public function add_image_sizes( $sizes ) {
		$obj            = get_post_type_object( 'testimonials' );
		$post_type_name = $obj->labels->singular_name;
		$sizes['testimonials_entry'] = array(
			'label'   => sprintf( esc_html__( '%s Entry', 'total-theme-core' ), $post_type_name ),
			'width'   => 'testimonials_entry_image_width',
			'height'  => 'testimonials_entry_image_height',
			'crop'    => 'testimonials_entry_image_crop',
			'section' => 'testimonials',
		);
		return $sizes;
	}

	/**
	 * Disables the next/previous links if disabled via the customizer.
	 */
	public function next_prev( $display, $post_type ) {
		if ( 'testimonials' == $post_type ) {
			$display = wpex_get_mod( 'testimonials_next_prev', true ) ? true : false;
		}
		return $display;
	}

	/**
	 * Alter previous post link title
	 */
	public function prev_post_link_title( $title, $post_type ) {
		if ( 'testimonials' == $post_type ) {
			$title = '<span class="ticon ticon-angle-double-left"></span>' . esc_html__( 'Previous', 'total-theme-core' );
		}
		return $title;
	}

	/**
	 * Alter next post link title
	 */
	public function next_post_link_title( $title, $post_type ) {
		if ( 'testimonials' == $post_type ) {
			$title = esc_html__( 'Next', 'total-theme-core' ) . '<span class="ticon ticon-angle-double-right"></span>';
		}
		return $title;
	}

	/**
	 * Adds testimonials meta options
	 */
	public function add_meta( $array, $post ) {
		$obj = get_post_type_object( 'testimonials' );
		$array['testimonials'] = array(
			'title'                   => $obj->labels->singular_name,
			'post_type'               => array( 'testimonials' ),
			'settings'                => array(
				'testimonial_author'  => array(
					'title'           => esc_html__( 'Author', 'total-theme-core' ),
					'description'     => esc_html__( 'Enter the name of the author for this testimonial.', 'total-theme-core' ),
					'id'              => 'wpex_testimonial_author',
					'type'            => 'text',
				),
				'testimonial_company' => array(
					'title'           => esc_html__( 'Company', 'total-theme-core' ),
					'description'     => esc_html__( 'Enter the name of the company for this testimonial.', 'total-theme-core' ),
					'id'              => 'wpex_testimonial_company',
					'type'            => 'text',
				),
				'testimonial_url'     => array(
					'title'           => esc_html__( 'Company URL', 'total-theme-core' ),
					'description'     => esc_html__( 'Enter the URL for the company for this testimonial.', 'total-theme-core' ),
					'id'              => 'wpex_testimonial_url',
					'type'            => 'text',
				),
				'post_rating'         => array(
					'title'           => esc_html__( 'Rating', 'total-theme-core' ),
					'description'     => esc_html__( 'Enter a rating for this testimonial.', 'total-theme-core' ),
					'id'              => 'wpex_post_rating',
					'type'            => 'number',
					'max'             => '10',
					'min'             => '1',
					'step'            => '0.1',
				),
			),
		);
		return $array;
	}

	/**
	 * Add custom VC modules
	 */
	public function vc_modules( $modules ) {
		$modules[] = 'testimonials_grid';
		$modules[] = 'testimonials_carousel';
		$modules[] = 'testimonials_slider';
		return $modules;
	}

}
new Testimonials;