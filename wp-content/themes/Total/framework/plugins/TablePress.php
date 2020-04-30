<?php
/**
 * TablePress Support
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.8
 */

namespace TotalTheme\Vendor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TablePress {

	/**
	 * Start things up
	 *
	 * @since 4.8
	 */
	public function __construct() {
		add_action( 'vc_after_mapping', array( $this, 'vc_lean_map' ), 0 );
	}

	/**
	 * Registers table module for WPBakery plugin
	 *
	 * @since 4.8
	 */
	public function vc_lean_map() {
		vc_lean_map( 'table', array( $this, 'vc_settings' ) );
		add_filter( 'vc_autocomplete_table_id_callback', array( $this, 'tables_autocomplete_callback' ), 10, 1 );
		add_filter( 'vc_autocomplete_table_id_render', array( $this, 'tables_autocomplete_render' ), 10, 1 );
	}

	/**
	 * Table vc module settings
	 *
	 * @since 4.8
	 */
	public function vc_settings() {
		return array(
			'name' => esc_html__( 'Table', 'total' ),
			'description' => esc_html__( 'Insert a TablePress table', 'total' ),
			'base' => 'table',
			'icon' => 'vcex-tablepress vcex-icon ticon ticon-table',
			'params' => array(
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Search tables by name and select your table of choice.', 'total' ),
					'param_name' => 'id',
					'settings' => array(
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
				),

			)
		);

	}

	/**
	 * Return a list of tables to choose from in the WPBakery module
	 *
	 * @since 4.8
	 */
	public function tables_autocomplete_callback( $search_string ) {

		$tables = array();

		$tablepress_tables = get_option( 'tablepress_tables' );

		if ( empty( $tablepress_tables ) ) {
			return $tables;
		}

		$tablepress_tables = json_decode( $tablepress_tables, true );
		$tablepress_tables = array_flip( $tablepress_tables['table_post'] );

		$tables_ids = get_posts( array(
			'posts_per_page' => -1,
			'post_type'      => 'tablepress_table',
			's'              => $search_string,
			'fields'         => 'ids',
		) );

		if ( ! empty( $tables_ids ) ) {
			foreach ( $tables_ids as $id ) {
				if ( isset( $tablepress_tables[ $id ] ) ) {
					$tables[] = array(
						'label' => get_the_title( $id ),
						'value' => $tablepress_tables[$id],
					);
				}
			}
		}

		return $tables;

	}

	/**
	 * Render tables for WPBakery autocomplete
	 *
	 * @since 4.8
	 */
	function tables_autocomplete_render( $data ) {

		$tablepress_tables = get_option( 'tablepress_tables' );

		if ( empty( $tablepress_tables ) ) {
			return array( 'label' => $data['value'], 'value' => $data['value'] );
		}

		$tablepress_tables = json_decode( $tablepress_tables, true );
		$tablepress_tables = $tablepress_tables['table_post'];

		return array(
			'label' => get_the_title( $tablepress_tables[ $data['value'] ] ),
			'value' => $data['value'],
		);


	}

}
new TablePress();