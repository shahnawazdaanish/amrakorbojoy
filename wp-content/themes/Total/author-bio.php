<?php
/**
 * The template for displaying Author bios.
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get global post
global $post;

// Return if post is empty
if ( ! $post ) {
	return;
}

// Define author bio data
$data = array(
	'post_author' => $post->post_author,
	'avatar_size' => apply_filters( 'wpex_author_bio_avatar_size', 70 ),
	'author_name' => get_the_author(),
	'posts_url'   => get_author_posts_url( $post->post_author ),
	'description' => get_the_author_meta( 'description', $post->post_author ),
);

// Get author avatar
$data['avatar'] = get_avatar( $post->post_author, $data['avatar_size'] );

// Apply filters so we can tweak the author bio output
$data = apply_filters( 'wpex_post_author_bio_data', $data );

// Extract variables
extract( $data );

// Only display if description exists
if ( $description ) : ?>

	<section class="author-bio clr<?php if ( ! $avatar ) echo ' no-avatar'; ?>">

		<?php if ( ! empty( $avatar ) ) { ?>

			<div class="author-bio-avatar">

				<?php if ( ! empty( $posts_url ) ) { ?>

					<a href="<?php echo esc_url( $posts_url ); ?>" title="<?php esc_attr_e( 'Visit Author Page', 'total' ); ?>"><?php echo wpex_sanitize_data( $avatar, 'img' ); ?></a>

				<?php } else { ?>

					<?php echo wpex_sanitize_data( $avatar, 'img' ); ?>

				<?php } ?>

			</div><!-- .author-bio-avatar -->

		<?php } ?>

		<div class="author-bio-content clr">

			<?php if ( ! empty( $author_name ) ) { ?>

				<h4 class="author-bio-title">

					<?php if ( ! empty( $posts_url ) ) { ?>

						<a href="<?php echo esc_url( $posts_url ); ?>" title="<?php esc_attr_e( 'Visit Author Page', 'total' ); ?>"><?php echo strip_tags( $author_name ); ?></a>

					<?php } else { ?>

						<?php echo strip_tags( $author_name ); ?>

					<?php } ?>

				</h4><!-- .author-bio-title -->

			<?php } ?>

			<?php
			// Outputs the author description if one exists
			if ( ! empty( $description ) ) { ?>

				<div class="author-bio-description clr">
					<?php echo wpautop( do_shortcode( wp_kses_post( $description ) ) ); ?>
				</div><!-- author-bio-description -->

			<?php } ?>

			<?php
			// Display author social links if there are social links defined
			wpex_user_social_links( array(
				'user_id'         => $post_author,
				'display'         => 'icons',
				'before'          => '<div class="author-bio-social clr">',
				'after'           => '</div>',
				'link_attributes' => array(
					'class' => wpex_get_social_button_class( wpex_get_mod( 'author_box_social_style', 'flat-color-round' ) )
				),
			) ); ?>

		</div><!-- .author-bio-content -->

	</section><!-- .author-bio -->

<?php endif; ?>