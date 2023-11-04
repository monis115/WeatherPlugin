<?php
/**
 * The settings configuration.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

// Set a unique slug-like ID.
$prefix = 'location_weather_tools_options';

// Create options.
SPLW::createOptions(
	$prefix,
	array(
		'menu_title'         => __( 'Tools', 'location-weather' ),
		'menu_slug'          => 'lw-tools',
		'menu_parent'        => 'edit.php?post_type=location_weather',
		'menu_type'          => 'submenu',
		'show_search'        => false,
		'show_all_options'   => false,
		'show_reset_all'     => false,
		'framework_title'    => __( 'Tools', 'location-weather' ),
		'framework_class'    => 'splw-tools-options',
		'show_buttons'       => false, // Custom show button option added for hide save button in tools page.
		'theme'              => 'light',
		'show_reset_section' => true,

	)
);
// Create a section.
SPLW::createSection(
	$prefix,
	array(
		'title'  => 'Export',
		'class'  => 'location_weather_export',
		'fields' => array(
			array(
				'id'       => 'splw_what_export',
				'type'     => 'radio',
				'class'    => 'splw_what_export',
				'title'    => __( 'Choose What To Export', 'location-weather' ),
				'multiple' => false,
				'options'  => array(
					'all_shortcodes'      => __( 'All Shortcode(s)', 'location-weather' ),
					'selected_shortcodes' => __( 'Selected Shortcode(s)', 'location-weather' ),
				),
				'default'  => 'all_shortcodes',
			),
			array(
				'id'          => 'splw_post',
				'class'       => 'splw_post_ids',
				'type'        => 'select',
				'title'       => ' ',
				'options'     => 'location_weather',
				'chosen'      => true,
				'sortable'    => false,
				'multiple'    => true,
				'placeholder' => __( 'Choose Shortcode(s)', 'location-weather' ),
				'query_args'  => array(
					'posts_per_page' => -1,
				),
				'dependency'  => array( 'splw_what_export', '==', 'selected_shortcodes', true ),

			),
			array(
				'id'      => 'lw_export',
				'class'   => 'location_weather_export',
				'type'    => 'button_set',
				'title'   => ' ',
				'options' => array(
					'' => __( 'Export', 'location-weather' ),
				),
			),
		),
	)
);

// Custom CSS Field.
SPLW::createSection(
	$prefix,
	array(
		'class'  => 'location_weather_import',
		'title'  => __( 'Import', 'location-weather' ),
		'fields' => array(
			array(
				'class' => 'lw_import',
				'type'  => 'custom_import',
				'title' => __( 'Import JSON File To Upload', 'location-weather' ),
			),
		),
	)
);
