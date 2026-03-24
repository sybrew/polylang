<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

defined( 'ABSPATH' ) || exit;

/**
 * Class that holds admin data for the language switcher.
 *
 * @since 3.9
 */
class Admin {
	/**
	 * Returns options available for the language switcher.
	 *
	 * @since 3.9
	 *
	 * @param string $key Optional. Either `string` to return option labels, `default` to return default values, `choices` to return choices, or an empty string to return everything.
	 *                    Defaults to an empty string.
	 * @return array List of switcher options strings, default values, options, or everything.
	 */
	public static function get_options( string $key = '' ): array {
		$options = array(
			'layout'                 => array(
				'label'   => __( 'Layout:', 'polylang' ),
				'default' => 'horizontal',
				'choices' => array(
					'dropdown'   => __( 'Dropdown', 'polylang' ),
					'horizontal' => __( 'Horizontal', 'polylang' ),
					'vertical'   => __( 'Vertical', 'polylang' ),
				),
			),
			'alignment'              => array(
				'label'   => __( 'Alignment:', 'polylang' ),
				'default' => is_rtl() ? 'right' : 'left',
				'choices' => array(
					'left'      => _x( 'Left', 'alignment', 'polylang' ),
					'center'    => _x( 'Center', 'alignment', 'polylang' ),
					'right'     => _x( 'Right', 'alignment', 'polylang' ),
					'stretched' => _x( 'Stretched', 'alignment', 'polylang' ),
				),
			),
			'show_flags'             => array(
				'label'   => __( 'Display flags', 'polylang' ),
				'default' => false,
			),
			'flag_aspect_ratio'      => array(
				'label'   => __( 'Flags aspect ratio:', 'polylang' ),
				'default' => '32',
				'choices' => array(
					'32' => '3:2',
					'11' => '1:1',
				),
			),
			'show_labels'            => array(
				'label'   => __( 'Display labels:', 'polylang' ),
				'default' => 'names',
				'choices' => array(
					''      => __( 'No', 'polylang' ),
					'names' => __( 'Language names', 'polylang' ),
					'codes' => __( 'Language codes', 'polylang' ),
				),
			),
			'force_home'             => array(
				'label'   => __( 'Force link to front page', 'polylang' ),
				'default' => false,
			),
			'hide_current'           => array(
				'label'   => __( 'Hide the current language', 'polylang' ),
				'default' => false,
			),
			'hide_if_no_translation' => array(
				'label'   => __( 'Hide languages with no translation', 'polylang' ),
				'default' => false,
			),
		);

		if ( '' === $key ) {
			return $options;
		}

		if ( 'choices' === $key ) {
			$return = array();
			foreach ( $options as $name => $data ) {
				if ( isset( $data['choices'] ) ) {
					$return[ $name ] = $data['choices'];
				}
			}
			return $return;
		}

		return wp_list_pluck( $options, $key );
	}
}
