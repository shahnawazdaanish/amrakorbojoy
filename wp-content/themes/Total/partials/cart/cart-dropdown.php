<?php
/**
 * Header cart dropdown
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 4.9.9
 */ ?>

<div id="current-shop-items-dropdown">
	<div id="current-shop-items-inner">
		<?php the_widget(
			'WC_Widget_Cart',
			array(),
			array(
				'before_title' => '<span class="widgettitle screen-reader-text">',
				'after_title' => '</span>'
			)
		); ?>
	</div>
</div>