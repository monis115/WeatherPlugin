<?php
/**
 *  Framework color field file.
 *
 * @package    Location_weather
 * @subpackage Location_weather/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_color' ) ) {
	/**
	 *
	 * Field: color
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SPLWT_Field_color extends SPLWT_Fields {

		/**
		 * Column field constructor.
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

			$default_attr = ( ! empty( $this->field['default'] ) ) ? ' data-default-color="' . esc_attr( $this->field['default'] ) . '"' : '';

			echo $this->field_before(); // phpcs:ignore
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '" class="splwt-lite-color"' . $default_attr . $this->field_attributes() . '/>'; // phpcs:ignore
			echo $this->field_after(); // phpcs:ignore

		}
	}
}
