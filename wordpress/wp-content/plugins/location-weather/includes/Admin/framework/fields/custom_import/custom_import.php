<?php
/**
 * Framework Custom_import field.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    easy-accordion-free
 * @subpackage easy-accordion-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_custom_import' ) ) {
	/**
	 *
	 * Field: Custom_import
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SPLWT_Field_custom_import extends SPLWT_Fields {

		/**
		 * Custom import field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}
		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->field_before();
			$lw_shortcode_link = admin_url( 'edit.php?post_type=location_weather' );
				echo '<p><input type="file" id="import" accept=".json"></p>';
				echo '<p><button type="button" class="import">Import</button></p>';
				echo '<a id="splw_shortcode_link_redirect" href="' . esc_url( $lw_shortcode_link ) . '"></a>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->field_after();
		}
	}
}
