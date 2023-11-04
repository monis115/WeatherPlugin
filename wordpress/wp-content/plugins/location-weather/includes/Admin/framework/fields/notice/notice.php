<?php
/**
 * Framework notice field file.
 *
 * @link       https://shapedplugin.com/
 * @since      2.1.1
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/Includes/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_notice' ) ) {
	/**
	 * SPLWT_Field_notice
	 */
	class SPLWT_Field_notice extends SPLWT_Fields {
		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 * @return void
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render
		 *
		 * @return void
		 */
		public function render() {

			$style = ( ! empty( $this->field['style'] ) ) ? $this->field['style'] : 'normal';

			echo ( ! empty( $this->field['content'] ) ) ? '<div class="splwt-lite-notice splwt-lite-notice-' . esc_attr( $style ) . '">' . wp_kses_post( $this->field['content'] ) . '</div>' : '';

		}

	}
}
