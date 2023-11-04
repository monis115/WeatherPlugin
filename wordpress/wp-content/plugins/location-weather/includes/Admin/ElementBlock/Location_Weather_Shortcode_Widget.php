<?php
/**
 * The plugin Elementor addons Initializer.
 *
 * @link       https://shapedplugin.com/
 * @since      3.0.0
 *
 * @package    location_weather
 * @subpackage location_weather/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

namespace ShapedPlugin\Weather\Admin\ElementBlock;

use ShapedPlugin\Weather\Frontend\Shortcode;
use ShapedPlugin\Weather\Frontend\Scripts;

/**
 * Elementor Location Weather shortcode Widget.
 *
 * @since 2.2.5
 */
class Location_Weather_Shortcode_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 2.2.5
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'location_weather_shortcode';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.2.5
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Location Weather', 'location-weather' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.2.5
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'splw-icon-lw-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 2.2.5
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'basic' );
	}

	/**
	 * Get all post list.
	 *
	 * @since 2.2.5
	 * @return array
	 */
	public function sptp_post_list() {
		$post_list  = array();
		$splw_posts = new \WP_Query(
			array(
				'post_type'      => 'location_weather',
				'post_status'    => 'publish',
				'posts_per_page' => 10000,
			)
		);
		$posts      = $splw_posts->posts;
		foreach ( $posts as $post ) {
			$post_list[ $post->ID ] = $post->post_title;
		}
		krsort( $post_list );
		return $post_list;
	}

	/**
	 * Controls register.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'location-weather' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'sp_location_weather_pro_shortcode',
			array(
				'label'       => __( 'Location Weather Shortcode(s)', 'location-weather' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => '',
				'options'     => $this->sptp_post_list(),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render team pro shortcode widget output on the frontend.
	 *
	 * @since 2.2.5
	 * @access protected
	 */
	protected function render() {

		$settings       = $this->get_settings_for_display();
		$splw_shortcode = $settings['sp_location_weather_pro_shortcode'];

		if ( '' === $splw_shortcode ) {
			echo '<div style="text-align: center; margin-top: 0; padding: 10px" class="elementor-add-section-drag-title">Select a shortcode</div>';
			return;
		}

		$shortcode_id = intval( $splw_shortcode );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$splw_option = get_option( 'location_weather_settings', true );
			$splw_meta   = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );
			// Stylesheet loading problem solving here. Shortcode id to push page id option for getting how many shortcode in the page.
			$dynamic_style = Scripts::load_dynamic_style( $shortcode_id, $splw_meta );
			echo '<style id="sp_lw_dynamic_css' . esc_attr( $shortcode_id ) . '">' . wp_strip_all_tags( $dynamic_style['dynamic_css'] ) . '</style>';//phpcs:ignore
			Shortcode::splw_html_show( $shortcode_id, $splw_option, $splw_meta );
			?>
			<?php
		} else {
			echo do_shortcode( '[location-weather id="' . $shortcode_id . '"]' );
		}

	}

}
