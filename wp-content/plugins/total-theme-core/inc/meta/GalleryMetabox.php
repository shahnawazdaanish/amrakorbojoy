<?php
/**
 * Creates a gallery metabox for WordPress
 *
 * Credits:
 * http://wordpress.org/plugins/easy-image-gallery/
 * https://github.com/woothemes/woocommerce
 *
 * @package Total Theme Core
 * @subpackage Meta
 * @version 1.1.2
 */

namespace TotalThemeCore;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class GalleryMetabox {
	private $post_types;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin Actions.
	 */
	public function admin_init() {

		// Post types to add the metabox to
		$this->post_types = array( 'post', 'page' );
		$this->post_types = apply_filters( 'wpex_gallery_metabox_post_types', array_combine( $this->post_types, $this->post_types ) );

		// return if no post types
		if ( ! $this->post_types ) {
			return;
		}

		// Add metabox to corresponding post types
		foreach( $this->post_types as $key => $val ) {
			add_action( 'add_meta_boxes_' . $val, array( $this, 'add_meta' ), 20 );
		}

		// Save metabox
		add_action( 'save_post', array( $this, 'save_meta' ) );

		// Load needed scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

	}

	/**
	 * Adds the gallery metabox.
	 */
	public function add_meta( $post ) {
		add_meta_box(
			'wpex-gallery-metabox',
			esc_html__( 'Image Gallery', 'total-theme-core' ),
			array( $this, 'render' ),
			$post->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Render the gallery metabox.
	 */
	public function render() {
		global $post; ?>
		<div id="wpex_gallery_images_container">
			<ul class="wpex_gallery_images">
				<?php
				$image_gallery = get_post_meta( $post->ID, '_easy_image_gallery', true );
				$attachments = array_filter( explode( ',', $image_gallery ) );
				if ( $attachments ) {
					foreach ( $attachments as $attachment_id ) {
						if ( wp_attachment_is_image ( $attachment_id  ) ) {
							echo '<li class="image" data-attachment_id="' . absint( $attachment_id ) . '"><div class="attachment-preview"><div class="thumbnail">
										' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '</div>
										<a href="#" class="wpex-gmb-remove" title="' . esc_html__( 'Remove image', 'total-theme-core' ) . '">&times;</a>
									</div></li>';
						}
					}
				} ?>
			</ul>
			<input type="hidden" id="image_gallery" name="image_gallery" value="<?php echo esc_attr( $image_gallery ); ?>" />
			<?php wp_nonce_field( 'easy_image_gallery', 'easy_image_gallery' ); ?>
		</div>
		<p class="add_wpex_gallery_images hide-if-no-js">
			<a href="#" class="button-primary"><?php esc_html_e( 'Add/Edit Images', 'total-theme-core' ); ?></a>
		</p>
		<p>
			<label for="easy_image_gallery_link_images">
				<input type="checkbox" id="easy_image_gallery_link_images" value="on" name="easy_image_gallery_link_images"<?php echo checked( get_post_meta( get_the_ID(), '_easy_image_gallery_link_images', true ), 'on', false ); ?> /> <?php esc_html_e( 'Enable Lightbox for this gallery?', 'total-theme-core' )?>
			</label>
		</p>
	<?php
	}

	/**
	 * Render the gallery metabox.
	 */
	public function save_meta( $post_id ) {

		// Check nonce
		if ( ! isset( $_POST[ 'easy_image_gallery' ] )
			|| ! wp_verify_nonce( $_POST[ 'easy_image_gallery' ], 'easy_image_gallery' )
		) {
			return;
		}

		// Check auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

		}

		if ( isset( $_POST[ 'image_gallery' ] ) && ! empty( $_POST[ 'image_gallery' ] ) ) {
			$attachment_ids = sanitize_text_field( $_POST['image_gallery'] );
			// Turn comma separated values into array
			$attachment_ids = explode( ',', $attachment_ids );
			// Clean the array
			$attachment_ids = array_filter( $attachment_ids );
			// Return back to comma separated list with no trailing comma. This is common when deleting the images
			$attachment_ids =  implode( ',', $attachment_ids );
			update_post_meta( $post_id, '_easy_image_gallery', wp_strip_all_tags( $attachment_ids ) );
		} else {
			// Delete gallery
			delete_post_meta( $post_id, '_easy_image_gallery' );
		}

		// link to larger images
		if ( isset( $_POST[ 'easy_image_gallery_link_images' ] ) ) {
			update_post_meta( $post_id, '_easy_image_gallery_link_images', wp_strip_all_tags( $_POST[ 'easy_image_gallery_link_images' ] ) );
		} else {
			update_post_meta( $post_id, '_easy_image_gallery_link_images', 'off' );
		}

		// Add action
		do_action( 'wpex_save_gallery_metabox', $post_id );

	}

	/**
	 * Load needed scripts.
	 */
	public function load_scripts( $hook ) {

		// Only needed on these admin screens
		if ( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' ) {
			return;
		}

		// Get global post
		global $post;

		// Return if post is not object
		if ( ! is_object( $post ) ) {
			return;
		}

		// Return if wrong type or is VC live editor
		if ( ! in_array( $post->post_type, $this->post_types ) ) {
			return;
		}

		// Disable in WPBakery front-end editor
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'ttc-gmb-css',
			TTC_PLUGIN_DIR_URL . 'assets/css/gallery-metabox.css',
			false,
			'1.0'
		);

		// Load jquery sortable
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Load metabox script
		wp_enqueue_script(
			'ttc-gmb-js',
			TTC_PLUGIN_DIR_URL . '/assets/js/gallery-metabox.min.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			'1.0',
			true
		);

		// Localize metabox script
		wp_localize_script( 'ttc-gmb-js', 'wpexGmb', array(
			'title'  => esc_html__( 'Add Images to Gallery', 'total-theme-core' ),
			'button' => esc_html__( 'Add to gallery', 'total-theme-core' ),
			'remove' => esc_html__( 'Remove image', 'total-theme-core' ),
		) );

	}

}

// Class needed only in the admin
new GalleryMetabox;