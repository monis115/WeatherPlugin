<?php
/**
 * Script class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend;

use ShapedPlugin\Weather\Frontend;

/**
 * Script class used to hold the style and script for frontend.
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
			$this->scripts_handler();
	}

	/**
	 * Frontend script handler.
	 *
	 * @return void
	 */
	public function scripts_handler() {
		add_action( 'wp_enqueue_scripts', array( $this, 'lw_styles' ) );
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function lw_styles() {
		// Stylesheet loading problem solving here. Shortcode id to push page id option for getting how many shortcode in the page.
		$get_page_data      = self::get_page_data();
		$found_generator_id = $get_page_data['generator_id'];
		/**
		 * This function is provided for demonstration purposes only.
		 */
		if ( $found_generator_id ) {
			wp_enqueue_style( 'splw-styles' );
			wp_enqueue_style( 'splw-old-styles' );
			/* Load dynamic style in the header based on found shortcode on the page. */
			$dynamic_style = self::load_dynamic_style( $found_generator_id );
			wp_add_inline_style( 'splw-styles', wp_strip_all_tags( $dynamic_style['dynamic_css'] ) );
		}
	}

	/**
	 * Gets the existing shortcode-id, page-id and option-key from the current page.
	 *
	 * @return array
	 */
	public static function get_page_data() {
		$current_page_id    = get_queried_object_id();
		$option_key         = 'sp_lw_page_id' . $current_page_id;
		$found_generator_id = get_option( $option_key );
		if ( is_multisite() ) {
			$option_key         = 'sp_lw_page_id' . get_current_blog_id() . $current_page_id;
			$found_generator_id = get_site_option( $option_key );
		}
		$get_page_data = array(
			'page_id'      => $current_page_id,
			'generator_id' => $found_generator_id,
			'option_key'   => $option_key,
		);
		return $get_page_data;
	}

	/**
	 * Load dynamic style of the existing shortcode id.
	 *
	 * @param  mixed $found_generator_id to push id option for getting how many shortcode in the page.
	 * @param  mixed $splw_meta to push all options.
	 * @return array dynamic style and typography use in the specific shortcode.
	 */
	public static function load_dynamic_style( $found_generator_id, $splw_meta = '' ) {
		$lw_custom_css = trim( html_entity_decode( get_option( 'location_weather_settings' )['splw_custom_css'] ) );
		$custom_css    = '';
		// If multiple shortcode found in the page.
		if ( is_array( $found_generator_id ) ) {
			foreach ( $found_generator_id as $splw_id ) {
				if ( $splw_id && is_numeric( $splw_id ) && get_post_status( $splw_id ) !== 'trash' ) {
					$splw_meta   = get_post_meta( $splw_id, 'sp_location_weather_generator', true );
					include LOCATION_WEATHER_PATH . '/includes/Frontend/dynamic-style.php';
				}
			}
		} else {
			// If single shortcode found in the page.
			$splw_id = $found_generator_id;
			include LOCATION_WEATHER_PATH . '/includes/Frontend/dynamic-style.php';
		}
		// Custom css merge with dynamic style.
		if ( ! empty( $lw_custom_css ) ) {
			$custom_css .= $lw_custom_css;
		}
		$dynamic_style = array(
			'dynamic_css' => Frontend::minify_output( $custom_css ),
		);
		return $dynamic_style;
	}

	/**
	 * If the option does not exist, it will be created.
	 *
	 * It will be serialized before it is inserted into the database.
	 *
	 * @param  string $post_id existing shortcode id.
	 * @param  array  $get_page_data get current page-id, shortcode-id and option-key from the page.
	 * @return void
	 */
	public static function lw_db_options_update( $post_id, $get_page_data ) {
		$found_generator_id = $get_page_data['generator_id'];
		$option_key         = $get_page_data['option_key'];
		$current_page_id    = $get_page_data['page_id'];
		if ( $found_generator_id ) {
			$found_generator_id = is_array( $found_generator_id ) ? $found_generator_id : array( $found_generator_id );
			if ( ! in_array( $post_id, $found_generator_id ) || empty( $found_generator_id ) ) {
				// If not found the shortcode id in the page options.
				array_push( $found_generator_id, $post_id );
				if ( is_multisite() ) {
					update_site_option( $option_key, $found_generator_id );
				} else {
					update_option( $option_key, $found_generator_id );
				}
			}
		} else {
			// If option not set in current page add option.
			if ( $current_page_id ) {
				if ( is_multisite() ) {
					add_site_option( $option_key, array( $post_id ) );
				} else {
					add_option( $option_key, array( $post_id ) );
				}
			}
		}
	}
}
