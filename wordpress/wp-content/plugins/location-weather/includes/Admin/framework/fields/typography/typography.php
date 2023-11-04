<?php
/**
 * Framework typography field file.
 *
 * @link       https://shapedplugin.com/
 * @since      2.1.1
 *
 * @package    Location_Weather_Pro
 * @subpackage Location_Weather_Pro/Includes/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_typography' ) ) {
	/**
	 * SPLWT_Field_typography
	 */
	class SPLWT_Field_typography extends SPLWT_Fields {


		/**
		 * Chosen
		 *
		 * @var bool
		 */
		public $chosen = false;

		/**
		 * Value
		 *
		 * @var array
		 */
		public $value = array();

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 * @return void
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

			echo wp_kses_post( $this->field_before() );

			$args = wp_parse_args(
				$this->field,
				array(
					'font_family'        => true,
					'font_weight'        => true,
					'font_style'         => true,
					'font_size'          => true,
					'line_height'        => true,
					'letter_spacing'     => true,
					'text_align'         => true,
					'text_transform'     => true,
					'color'              => true,
					'chosen'             => true,
					'preview'            => true,
					'subset'             => false,
					'multi_subset'       => false,
					'extra_styles'       => false,
					'backup_font_family' => false,
					'font_variant'       => false,
					'word_spacing'       => false,
					'text_decoration'    => false,
					'custom_style'       => false,
					'exclude'            => '',
					'unit'               => 'px',
					'line_height_unit'   => '',
					'preview_text'       => 'The quick brown fox jumps over the lazy dog',
					'margin_top'         => true,
					'margin_bottom'      => true,
				)
			);

			$default_value = array(
				'font-family'        => '',
				'font-weight'        => '',
				'font-style'         => '',
				'font-variant'       => '',
				'font-size'          => '',
				'line-height'        => '',
				'letter-spacing'     => '',
				'word-spacing'       => '',
				'text-align'         => '',
				'text-transform'     => '',
				'text-decoration'    => '',
				'backup-font-family' => '',
				'color'              => '',
				'custom-style'       => '',
				'type'               => '',
				'subset'             => '',
				'extra-styles'       => array(),
				'margin-top'         => '',
				'margin-bottom'      => '',
			);

			$default_value    = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;
			$this->value      = wp_parse_args( $this->value, $default_value );
			$this->chosen     = $args['chosen'];
			$chosen_class     = ( $this->chosen ) ? ' splwt-lite--chosen' : '';
			$line_height_unit = ( ! empty( $args['line_height_unit'] ) ) ? $args['line_height_unit'] : $args['unit'];

			echo '<div class="splwt-lite--typography' . esc_attr( $chosen_class ) . '" data-unit="' . esc_attr( $args['unit'] ) . '" data-line-height-unit="' . esc_attr( $line_height_unit ) . '" data-exclude="' . esc_attr( $args['exclude'] ) . '">';

			echo '<div class="splwt-lite--blocks splwt-lite--blocks-selects">';

			// Font Family.
			if ( ! empty( $args['font_family'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Font Family', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select( array( $this->value['font-family'] => $this->value['font-family'] ), 'font-family', esc_html__( 'Select a font', 'location-weather' ) );
				echo '</div>';
			}

			//
			// Backup Font Family.
			if ( ! empty( $args['backup_font_family'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-backup-font-family hidden">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Backup Font Family', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					apply_filters(
						'splwt_field_typography_backup_font_family',
						array(
							'Arial, Helvetica, sans-serif',
							"'Arial Black', Gadget, sans-serif",
							"'Comic Sans MS', cursive, sans-serif",
							'Impact, Charcoal, sans-serif',
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							'Tahoma, Geneva, sans-serif',
							"'Trebuchet MS', Helvetica, sans-serif'",
							'Verdana, Geneva, sans-serif',
							"'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace",
							'Georgia, serif',
							'Palatino Linotype',
						)
					),
					'backup-font-family',
					esc_html__( 'Default', 'location-weather' )
				);
				echo '</div>';
			}

			//
			// Font Style and Extra Style Select.
			if ( ! empty( $args['font_weight'] ) || ! empty( $args['font_style'] ) ) {

				// Font Style Select.
				echo '<div class="splwt-lite--block splwt-lite--block-font-style hidden">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Font Style', 'location-weather' ) . '</div>';
				echo '<select class="splwt-lite--font-style-select" data-placeholder="Default">';
				echo '<option value="">' . ( ! $this->chosen ? esc_html__( 'Default', 'location-weather' ) : '' ) . '</option>';// phpcs:ignore
				if ( ! empty( $this->value['font-weight'] ) || ! empty( $this->value['font-style'] ) ) {
					echo '<option value="' . esc_attr( strtolower( $this->value['font-weight'] . $this->value['font-style'] ) ) . '" selected></option>';
				}
				echo '</select>';
				echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[font-weight]' ) ) . '" class="splwt-lite--font-weight" value="' . esc_attr( $this->value['font-weight'] ) . '" />';
				echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[font-style]' ) ) . '" class="splwt-lite--font-style" value="' . esc_attr( $this->value['font-style'] ) . '" />';

				// Extra Font Style Select.
				if ( ! empty( $args['extra_styles'] ) ) {
					echo '<div class="splwt-lite--block-extra-styles hidden">';
					echo ( ! $this->chosen ) ? '<div class="splwt-lite--title">' . esc_html__( 'Load Extra Styles', 'location-weather' ) . '</div>' : '';
					$placeholder = ( $this->chosen ) ? esc_html__( 'Load Extra Styles', 'location-weather' ) : esc_html__( 'Default', 'location-weather' );
					echo $this->create_select( $this->value['extra-styles'], 'extra-styles', $placeholder, true );// phpcs:ignore
					echo '</div>';
				}

				echo '</div>';

			}

			//
			// Subset.
			if ( ! empty( $args['subset'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-subset hidden">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Subset', 'location-weather' ) . '</div>';
				$subset = ( is_array( $this->value['subset'] ) ) ? $this->value['subset'] : array_filter( (array) $this->value['subset'] );
				echo $this->create_select( $subset, 'subset', esc_html__( 'Default', 'location-weather' ), $args['multi_subset'] );// phpcs:ignore
				echo '</div>';
			}

			//
			// Text Align.
			if ( ! empty( $args['text_align'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Text Align', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'inherit' => esc_html__( 'Inherit', 'location-weather' ),
						'left'    => esc_html__( 'Left', 'location-weather' ),
						'center'  => esc_html__( 'Center', 'location-weather' ),
						'right'   => esc_html__( 'Right', 'location-weather' ),
						'justify' => esc_html__( 'Justify', 'location-weather' ),
						'initial' => esc_html__( 'Initial', 'location-weather' ),
					),
					'text-align',
					esc_html__( 'Default', 'location-weather' )
				);
				echo '</div>';
			}

			//
			// Font Variant.
			if ( ! empty( $args['font_variant'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Font Variant', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'normal'         => esc_html__( 'Normal', 'location-weather' ),
						'small-caps'     => esc_html__( 'Small Caps', 'location-weather' ),
						'all-small-caps' => esc_html__( 'All Small Caps', 'location-weather' ),
					),
					'font-variant',
					esc_html__( 'Default', 'location-weather' )
				);
				echo '</div>';
			}

			//
			// Text Transform.
			if ( ! empty( $args['text_transform'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Text Transform', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'none'       => esc_html__( 'None', 'location-weather' ),
						'capitalize' => esc_html__( 'Capitalize', 'location-weather' ),
						'uppercase'  => esc_html__( 'Uppercase', 'location-weather' ),
						'lowercase'  => esc_html__( 'Lowercase', 'location-weather' ),
					),
					'text-transform',
					esc_html__( 'Default', 'location-weather' )
				);
				echo '</div>';
			}

			//
			// Text Decoration.
			if ( ! empty( $args['text_decoration'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Text Decoration', 'location-weather' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'none'               => esc_html__( 'None', 'location-weather' ),
						'underline'          => esc_html__( 'Solid', 'location-weather' ),
						'underline double'   => esc_html__( 'Double', 'location-weather' ),
						'underline dotted'   => esc_html__( 'Dotted', 'location-weather' ),
						'underline dashed'   => esc_html__( 'Dashed', 'location-weather' ),
						'underline wavy'     => esc_html__( 'Wavy', 'location-weather' ),
						'underline overline' => esc_html__( 'Overline', 'location-weather' ),
						'line-through'       => esc_html__( 'Line-through', 'location-weather' ),
					),
					'text-decoration',
					esc_html__( 'Default', 'location-weather' )
				);
				echo '</div>';
			}

			echo '</div>';

			echo '<div class="splwt-lite--blocks splwt-lite--blocks-inputs">';

			// Font Size.
			if ( ! empty( $args['font_size'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Font Size', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[font-size]' ) ) . '" class="splwt-lite--font-size splwt-lite--input splwt-lite-input-number" value="' . esc_attr( $this->value['font-size'] ) . '" />';
				echo '<span class="splwt-lite--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Line Height.
			if ( ! empty( $args['line_height'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Line Height', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[line-height]' ) ) . '" class="splwt-lite--line-height splwt-lite--input splwt-lite-input-number" value="' . esc_attr( $this->value['line-height'] ) . '" />';
				echo '<span class="splwt-lite--unit">' . esc_attr( $line_height_unit ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Letter Spacing.
			if ( ! empty( $args['letter_spacing'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Letter Spacing', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[letter-spacing]' ) ) . '" class="splwt-lite--letter-spacing splwt-lite--input splwt-lite-input-number" value="' . esc_attr( $this->value['letter-spacing'] ) . '" />';
				echo '<span class="splwt-lite--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Word Spacing.
			if ( ! empty( $args['word_spacing'] ) ) {
				echo '<div class="splwt-lite--block">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Word Spacing', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[word-spacing]' ) ) . '" class="splwt-lite--word-spacing splwt-lite--input splwt-lite-input-number" value="' . esc_attr( $this->value['word-spacing'] ) . '" />';
				echo '<span class="splwt-lite--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			echo '</div>';

			echo '<div class="splwt-lite--blocks splwt-lite--blocks-color">';
			//
			// Font Color.
			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . esc_attr( $default_value['color'] ) . '"' : '';
				echo '<div class="splwt-lite--block splwt-lite--block-font-color">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Font Color', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite-field-color">';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ) . '" class="splwt-lite-color splwt-lite--color" value="' . esc_attr( $this->value['color'] ) . '"' . wp_kses_post( $default_color_attr ) . ' />';
				echo '</div>';
				echo '</div>';
			}
			// Margin Top.
			if ( ! empty( $args['margin_top'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-margin">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Margin Top', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--blocks">';
				echo '<div class="splwt-lite--block splwt-lite--unit">' . '<i class="fa fa-long-arrow-up"></i>' . '</div>';
				echo '<div class="splwt-lite--block"><input type="number" name="' . $this->field_name( '[margin-top]' ) . '" class="splwt-lite--margin-top splwt-lite--input splwt-lite-number" value="' . $this->value['margin-top'] . '" /></div>';
				echo '<div class="splwt-lite--block splwt-lite--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}
			//
			// Margin Bottom.
			if ( ! empty( $args['margin_bottom'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-margin">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Margin Bottom', 'location-weather' ) . '</div>';
				echo '<div class="splwt-lite--blocks">';
				echo '<div class="splwt-lite--block splwt-lite--unit">' . '<i class="fa fa-long-arrow-down"></i>' . '</div>';
				echo '<div class="splwt-lite--block"><input type="number" name="' . $this->field_name( '[margin-bottom]' ) . '" class="splwt-lite--margin-bottom splwt-lite--input splwt-lite-number" value="' . $this->value['margin-bottom'] . '" /></div>';
				echo '<div class="splwt-lite--block splwt-lite--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';

			//
			// Custom style.
			if ( ! empty( $args['custom_style'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-custom-style">';
				echo '<div class="splwt-lite--title">' . esc_html__( 'Custom Style', 'location-weather' ) . '</div>';
				echo '<textarea name="' . esc_attr( $this->field_name( '[custom-style]' ) ) . '" class="splwt-lite--custom-style">' . esc_attr( $this->value['custom-style'] ) . '</textarea>';
				echo '</div>';
			}

			//
			// Preview.
			$always_preview = ( 'always' !== $args['preview'] ) ? ' hidden' : '';

			if ( ! empty( $args['preview'] ) ) {
				echo '<div class="splwt-lite--block splwt-lite--block-preview' . esc_attr( $always_preview ) . '">';
				echo '<div class="splwt-lite--toggle fas fa-toggle-off"></div>';
				echo '<div class="splwt-lite--preview">' . esc_attr( $args['preview_text'] ) . '</div>';
				echo '</div>';
			}

			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[type]' ) ) . '" class="splwt-lite--type" value="' . esc_attr( $this->value['type'] ) . '" />';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[unit]' ) ) . '" class="splwt-lite--unit-save" value="' . esc_attr( $args['unit'] ) . '" />';

			echo '</div>';

			echo wp_kses_post( $this->field_after() );

		}

		/**
		 * Create_select
		 *
		 * @param  mixed $options options.
		 * @param  mixed $name name.
		 * @param  mixed $placeholder placeholder.
		 * @param  mixed $is_multiple multiple check.
		 * @return statement
		 */
		public function create_select( $options, $name, $placeholder = '', $is_multiple = false ) {

			$multiple_name = ( $is_multiple ) ? '[]' : '';
			$multiple_attr = ( $is_multiple ) ? ' multiple data-multiple="true"' : '';
			$chosen_rtl    = ( $this->chosen && is_rtl() ) ? ' chosen-rtl' : '';

			$output  = '<select name="' . esc_attr( $this->field_name( '[' . $name . ']' . $multiple_name ) ) . '" class="splwt-lite--' . esc_attr( $name ) . esc_attr( $chosen_rtl ) . '" data-placeholder="' . esc_attr( $placeholder ) . '"' . $multiple_attr . '>';
			$output .= ( ! empty( $placeholder ) ) ? '<option value="">' . esc_attr( ( ! $this->chosen ) ? $placeholder : '' ) . '</option>' : '';

			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					if ( $is_multiple ) {
						$selected = ( in_array( $option_value, $this->value[ $name ] ) ) ? ' selected' : '';
						$output  .= '<option value="' . esc_attr( $option_value ) . '"' . esc_attr( $selected ) . '>' . esc_attr( $option_value ) . '</option>';
					} else {
						$option_key = ( is_numeric( $option_key ) ) ? $option_value : $option_key;
						$selected   = ( $option_key === $this->value[ $name ] ) ? ' selected' : '';
						$output    .= '<option value="' . esc_attr( $option_key ) . '"' . esc_attr( $selected ) . '>' . esc_attr( $option_value ) . '</option>';
					}
				}
			}

			$output .= '</select>';

			return $output;

		}

	}
}
