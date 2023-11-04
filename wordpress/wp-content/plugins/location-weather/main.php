<?php
/**
 * Location Weather
 *
 * @package           Location_Weather
 * @author            ShapedPlugin
 * @copyright         2023 ShapedPlugin
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Location Weather
 * Description:       Location Weather allows you to display beautiful weather to your WordPress site in a minute without coding skills! The plugin is customizable and developer-friendly. The weather data is available by OpenWeatherMap.com.
 * Plugin URI:        https://shapedplugin.com/plugin/location-weather-pro/?ref=1
 * Author:            ShapedPlugin LLC
 * Author URI:        https://shapedplugin.com/
 * Version:           1.3.11
 * Requires at least: 4.7
 * Requires PHP:      5.6
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       location-weather
 * Domain Path:       /languages
 */

/**
 * Exit if entering directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! ( is_plugin_active( 'location-weather-pro/main.php' ) || is_plugin_active_for_network( 'location-weather-pro/main.php' ) ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * The Main Class the of the plugin.
 */
final class Location_Weather {

	/**
	 * Suffix for minified file.
	 *
	 * @var string
	 */
	public $suffix = '';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.3.11';

	/**
	 * The unique slug of this plugin.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	public $plugin_slug = 'location-weather';

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		$this->define_constants();

		add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links_location' ), 10, 2 );
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_action( 'widgets_init', array( $this, 'splw_widget' ) );
		add_action( 'activated_plugin', array( $this, 'redirect_after_activation' ), 10, 2 );
		$import_export = new ShapedPlugin\Weather\Admin\Location_Weather_Import_Export();
		add_action( 'wp_ajax_splw_export_shortcodes', array( $import_export, 'export_shortcodes' ) );
		add_action( 'wp_ajax_splw_import_shortcodes', array( $import_export, 'import_shortcodes' ) );
		add_action( 'wp_ajax_splw_ajax_location_weather', array( $this, 'splw_ajax_location_weather' ) );
		add_action( 'wp_ajax_nopriv_splw_ajax_location_weather', array( $this, 'splw_ajax_location_weather' ) );
		add_action( 'wp_loaded', array( $this, 'register_all_scripts' ) );
		add_action( 'save_post', array( $this, 'delete_page_lw_option_on_save' ), 10, 2 );
	}

	/**
	 *  Location Weather ajax action.
	 */
	public function splw_ajax_location_weather() {
		$nonce = isset( $_POST['splw_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['splw_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_nonce' ) ) {
			wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'location-weather' ) ), 401 );
		}
		$arguments['id'] = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : null;

		$shortcode = new ShapedPlugin\Weather\Frontend\Shortcode();
		echo $shortcode->render_shortcode( $arguments ); // phpcs:ignore
		wp_die();
	}

	/**
	 * Initializes a singleton instance.
	 *
	 * @return \Location_Weather
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
	/**
	 *  Location Weather lite Widget.
	 */
	public function splw_widget() {
		register_widget( new ShapedPlugin\Weather\Admin\LW_Widget() );
		register_widget( new ShapedPlugin\Weather\Admin\sp_location_weather_widget_content() );
	}
	/**
	 * Define plugin constants.
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'LOCATION_WEATHER_VERSION', $this->version );
		define( 'LOCATION_WEATHER_SLUG', $this->plugin_slug );
		define( 'LOCATION_WEATHER_FILE', __FILE__ );
		define( 'LOCATION_WEATHER_PATH', __DIR__ );
		define( 'LOCATION_WEATHER_URL', plugins_url( '', LOCATION_WEATHER_FILE ) );
		define( 'LOCATION_WEATHER_ASSETS', LOCATION_WEATHER_URL . '/assets' );
		define( 'LOCATION_WEATHER_STORE_URL', 'https://shapedplugin.com' );
	}

	/**
	 * Add plugin action menu
	 *
	 * @since 1.2.0
	 *
	 * @param array  $links Link to the generator.
	 * @param string $file Generator linking button.
	 *
	 * @return array
	 */
	public function add_plugin_action_links_location( $links, $file ) {

		if ( plugin_basename( __FILE__ ) === $file ) {
			$new_links       = array(
				sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=location_weather&page=lw-settings' ), __( 'Settings', 'location-weather' ) ),
			);
			$links['go_pro'] = sprintf( '<a target="_blank" href="%1$s" style="color: #35b747; font-weight: 700;">Go Pro!</a>', 'https://shapedplugin.com/plugin/location-weather-pro/?ref=1' );
			return array_merge( $new_links, $links );
		}

		return $links;
	}

	/**
	 * Redirect after activation.
	 *
	 * @since 1.2.0
	 *
	 * @param string $file Path to the plugin file, relative to the plugin.
	 *
	 * @return void
	 */
	public function redirect_after_activation( $file ) {

		if ( plugin_basename( __FILE__ ) === $file && ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) ) {
			exit( esc_url( wp_safe_redirect( admin_url( 'edit.php?post_type=location_weather&page=splw_help' ) ) ) );
		}
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init_plugin() {
		if ( is_admin() ) {
			new ShapedPlugin\Weather\Admin();
		} else {
			new ShapedPlugin\Weather\Frontend();
		}

		// Elementor shortcode block.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ( is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active_for_network( 'elementor/elementor.php' ) ) ) {
			new ShapedPlugin\Weather\Admin\Location_Weather_Shortcode_Block();
		}

		// Gutenberg block.
		if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) {
			new ShapedPlugin\Weather\Admin\Gutenberg_Block\Gutenberg_Block_Init();
		}
	}

		/**
		 * Register the all scripts of the plugin.
		 *
		 * @since    2.0
		 */
	public function register_all_scripts() {
		/**
		 * Register the stylesheets for the public and admin facing side of the plugin.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		$setting_options        = get_option( 'location_weather_settings', true );
		$skip_cache_for_weather = isset( $setting_options['splw_skipping_cache'] ) ? $setting_options['splw_skipping_cache'] : false;
		wp_register_style( 'splw-styles', LOCATION_WEATHER_ASSETS . '/css/splw-style' . $this->suffix . '.css', array(), LOCATION_WEATHER_VERSION, 'all' );
		wp_register_style( 'splw-old-styles', LOCATION_WEATHER_ASSETS . '/css/old-style' . $this->suffix . '.css', array(), LOCATION_WEATHER_VERSION, 'all' );
		wp_register_style( 'splw-admin', LOCATION_WEATHER_ASSETS . '/css/admin' . $this->suffix . '.css', array(), LOCATION_WEATHER_VERSION, 'all' );

		/**
		 * Register the JavaScript for the public and admin facing side of the plugin.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		wp_register_script( 'splw-old-script', LOCATION_WEATHER_ASSETS . '/js/Old-locationWeather' . $this->suffix . '.js', array( 'jquery' ), LOCATION_WEATHER_VERSION, true );

		wp_localize_script(
			'splw-old-script',
			'splw_ajax_object',
			array(
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'splw_nonce'      => wp_create_nonce( 'splw_nonce' ),
				'splw_skip_cache' => $skip_cache_for_weather,
			)
		);
	}
	/**
	 * Delete page shortcode ids array option on save
	 *
	 * @param  int    $post_ID current post id.
	 * @param  object $post check post type.
	 * @return void
	 */
	public function delete_page_lw_option_on_save( $post_ID, $post ) {
		if ( is_multisite() ) {
			$option_key = 'sp_lw_page_id' . get_current_blog_id() . $post_ID;
			if ( get_site_option( $option_key ) ) {
				delete_site_option( $option_key );
			}
		} elseif ( get_option( 'sp_lw_page_id' . $post_ID ) ) {
				delete_option( 'sp_lw_page_id' . $post_ID );
		}

		if ( 'location_weather' === $post->post_type ) {
			$current_cache_key = 'sp_open_weather_data' . $post_ID;
			$this->splw_delete_transient( $current_cache_key );
		}
	}
	/**
	 * Delete the existing shortcode ids cache option on save
	 *
	 * @param  mixed $cache_key Key.
	 * @return void
	 */
	public function splw_delete_transient( $cache_key ) {
		if ( is_multisite() ) {
			$cache_key = $cache_key . '_' . get_current_blog_id();
			delete_site_transient( $cache_key );
		} else {
			delete_transient( $cache_key );
		}
	}
}
// End of the class.

/**
 * Initialize the main plugin.
 *
 * @return \Location_Weather
 */
function location_weather() {
	return Location_Weather::init();
}

/**
 * Launch the plugin.
 *
 * @param object The plugin object.
 */
if ( ! ( is_plugin_active( 'location-weather-pro/main.php' ) || is_plugin_active_for_network( 'location-weather-pro/main.php' ) ) ) {
	location_weather();
}
