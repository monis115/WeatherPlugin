<?php
/**
 * The plugin elementor addons Initializer.
 *
 * @link       https://shapedplugin.com/
 * @since      1.2.11
 *
 * @package    location_weather
 * @subpackage location_weather/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

namespace ShapedPlugin\Weather\Admin;

/**
 * Elementor shortcode block.
 *
 * @since      1.2.11
 * @package   Location_Weather
 */
class Location_Weather_Shortcode_Block {
	/**
	 * Instance
	 *
	 * @since 1.2.11
	 *
	 * @access private
	 * @static
	 *
	 * @var Location_Weather_Shortcode_Block The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Script and style suffix
	 *
	 * @since 1.2.11
	 * @access protected
	 * @var string
	 */
	protected $suffix;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.11
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Test_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.2.11
	 *
	 * @access public
	 */
	public function __construct() {
		$this->on_plugins_loaded();
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'splw_block_enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'splw_block_enqueue_style' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'splw_element_block_icon' ) );
	}

	/**
	 * Elementor block icon.
	 *
	 * @since    1.2.11
	 * @return void
	 */
	public function splw_element_block_icon() {
		wp_enqueue_style( 'splw_element_block_icon', LOCATION_WEATHER_ASSETS . '/css/fontello.css', array(), LOCATION_WEATHER_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the elementor block area.
	 *
	 * @since    1.2.11
	 */
	public function splw_block_enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Team_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Team_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'splw-old-script' );
	}

	/**
	 * Register the JavaScript for the elementor block area.
	 *
	 * @since    1.2.11
	 */
	public function splw_block_enqueue_style() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Team_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Team_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'splw-styles' );
		wp_enqueue_style( 'splw-old-styles' );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.11
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.11
	 *
	 * @access public
	 */
	public function init() {
		// Add Plugin actions.
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.2.11
	 *
	 * @access public
	 */
	public function init_widgets() {
		// Register widget.
		\Elementor\Plugin::instance()->widgets_manager->register( new ElementBlock\Location_Weather_Shortcode_Widget() );

	}

}

Location_Weather_Shortcode_Block::instance();
