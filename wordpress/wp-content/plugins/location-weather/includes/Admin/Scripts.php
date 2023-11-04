<?php
/**
 * Script class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Admin;

/**
 * Script class used to hold the style and script for admin.
 */
class Scripts {

	/**
	 * Script and style suffix
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * The constructor of the class.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_handler' ) );
	}

	/**
	 * Frontend script handler.
	 *
	 * @return void
	 */
	public function scripts_handler() {
		$this->lw_styles();
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function lw_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 */
		wp_enqueue_style( 'splw-admin' );

		$wpscreen              = get_current_screen();
		$the_current_post_type = $wpscreen->post_type;
		if ( ( 'location_weather' === $the_current_post_type ) ) {
			wp_enqueue_style( 'splw-styles' );
			wp_enqueue_style( 'splw-old-styles' );
			wp_enqueue_script( 'splw-old-script' );
		}
	}
}
