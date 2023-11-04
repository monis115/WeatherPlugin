<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.3.8
 * @package    Location_Weather
 */

namespace ShapedPlugin\Weather\Admin;

class Splwi18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.3.8
	 */
	public function load_text_domain() {

		load_plugin_textdomain(
			'location-weather',
			false,
			dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/'
		);

	}
}
