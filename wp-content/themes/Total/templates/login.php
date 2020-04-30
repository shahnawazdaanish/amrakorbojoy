<?php
/**
 * Template Name: Login
 *
 * @package Total WordPress Theme
 * @subpackage Templates
 * @version 4.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?><!DOCTYPE html>
<html <?php language_attributes(); ?><?php wpex_schema_markup( 'html' ); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	} ?>

	<?php do_action( 'wpex_hook_after_body_tag' ); // reserved specicially for child theme edits and custom actions panel ?>

	<div id="login-page-wrap" class="clr">

		<div id="login-page" class="clr container">

			<div id="login-page-logo" class="clr">

				<?php
				// Display post thumbnail
				if ( has_post_thumbnail() ) : ?>

					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php the_post_thumbnail(); ?></a>

				<?php
				// If no thumbnail is set display text logo
				else : ?>

					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>

				<?php endif; ?>

			</div><!-- #login-page-logo -->

			<div id="login-page-content" class="clr">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; ?>
			</div><!-- #login-page-content -->

			<?php
			if ( is_user_logged_in() ) {

				$logged_in_text = esc_html__( 'You are currently logged in.', 'total' ) . ' ' . '<a href="' . esc_url( wp_logout_url( wpex_get_current_url() ) ) . '">' . esc_html__( 'Logout?', 'total' ); ?>

				<p><?php echo wp_kses_post( apply_filters( 'wpex_login_page_template_logged_in_text', $logged_in_text ) ); ?></p>

			<?php } else {
				$wpex_login_args = apply_filters( 'wpex_login_page_template_args', array(
					'echo'           => true,
					'redirect'       => esc_url( home_url( '/' ) ),
					'form_id'        => 'login-template-form',
					'label_username' => esc_html__( 'Username', 'total' ),
					'label_password' => esc_html__( 'Password', 'total' ),
					'label_remember' => esc_html__( 'Remember Me', 'total' ),
					'label_log_in'   => esc_html__( 'Log In', 'total' ),
					'id_username'    => 'user_login',
					'id_password'    => 'user_pass',
					'id_remember'    => 'rememberme',
					'id_submit'      => 'wp-submit',
					'remember'       => true,
					'value_username' => NULL,
					'value_remember' => false
				) );
				wp_login_form( $wpex_login_args );
			} ?>

		</div><!-- #login-page -->

	</div><!-- #login-page-wrap -->

<?php wp_footer(); ?>

</body>
</html>