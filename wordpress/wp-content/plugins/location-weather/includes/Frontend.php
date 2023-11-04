<?php
/**
 * Frontend file
 *
 * @package Location_Weather.
 */

namespace ShapedPlugin\Weather;

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

/**
 * Frontend class
 */
class Frontend {

	/**
	 * The constructor of the class.
	 */
	public function __construct() {
		new Frontend\Shortcode();
		new Frontend\Scripts();
	}
	/**
	 * Minify output
	 *
	 * @param  string $html output.
	 * @return statement
	 */
	public static function minify_output( $html ) {
		$html = preg_replace( '/<!--(?!s*(?:[if [^]]+]|!|>))(?:(?!-->).)*-->/s', '', $html );
		$html = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $html );
		while ( stristr( $html, '  ' ) ) {
			$html = str_replace( '  ', ' ', $html );
		}
		return $html;
	}
}
