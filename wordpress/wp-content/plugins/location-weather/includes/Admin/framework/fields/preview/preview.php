<?php
/**
 * Preview field file.
 *
 * @link http://shapedplugin.com
 * @since 1.3.0
 *
 * @package Location_Weather.
 * @subpackage Location_Weather/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_preview' ) ) {
	/**
	 *
	 * Field: shortcode
	 *
	 * @since 1.3.0
	 * @version 1.3.0
	 */
	class SPLWT_Field_preview extends SPLWT_Fields {

		/**
		 * Shortcode field constructor.
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
		 * Render
		 *
		 * @return void
		 */
		public function render() {
			echo '<div class="sp_location_weather-preview-box"><div id="sp_location_weather-preview-box"></div></div>';
		}

	}
}
