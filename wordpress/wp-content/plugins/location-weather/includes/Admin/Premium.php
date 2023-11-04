<?php
/**
 * This is to plugin premium page.
 *
 * @package location-weather
 */

namespace ShapedPlugin\Weather\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * The premium page handler class.
 */
class Premium {

	/**
	 * The instance of the class.
	 *
	 * @var object
	 */
	private static $_instance;

	/**
	 * The Constructor of the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'upgrade_admin_menu' ), 100 );
	}

	/**
	 * The instance function of the class.
	 *
	 * @return object
	 */
	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new Premium();
		}

		return self::$_instance;
	}

	/**
	 * Add admin menu.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrade_admin_menu() {
		$landing_page = 'https://shapedplugin.com/plugin/location-weather-pro/';
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather', 'location-weather' ),
			'<span class="sp-go-pro-icon"></span>Go Pro',
			'manage_options',
			$landing_page
		);
	}

}
