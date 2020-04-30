<?php
/**
 * Customizer Templates Select Control
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.9
 */

if ( ! class_exists( 'WPEX_Customizer_Dropdown_Templates' ) ) {

	class WPEX_Customizer_Dropdown_Templates extends WP_Customize_Control {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'wpex-dropdown-templates';

		/**
		 * Render the content
		 *
		 * @access public
		 */
		public function render_content() {
			$value = $this->value(); ?>

			<label class="customize-control-select">

			<?php if ( ! empty( $this->label ) ) : ?>

				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<?php endif;

			// Description
			if ( ! empty( $this->description ) ) { ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php } ?>

			<div class="wpex-customizer-chosen-select">

				<select <?php $this->link(); ?>>

					<option value="" <?php if ( empty( $val ) ) echo 'selected="selected"'; ?>><?php esc_html_e( 'Select', 'total' ); ?></option>

					<?php
					$templates  = new WP_Query( array(
						'posts_per_page' => -1,
						'post_type'      => apply_filters( 'wpex_singular_template_supported_post_types', array( 'templatera', 'elementor_library' ) ),
						'fields'         => 'ids',
					) );

					$templates = $templates->posts;

					if ( $templates ) {

						foreach ( $templates as $template ) {

							echo '<option value="' . esc_attr( $template ) . '"' . selected( $value, $template, false ) . '>' . wp_strip_all_tags( get_the_title( $template ) ) . '</option>';

						}

					} ?>

				</select>

			</div>

		<?php }
	}

}